<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CertificateVerification;
use App\Models\User;
use App\Services\CertificationRequestService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function __construct(
        private readonly CertificationRequestService $certificationRequestService
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.certificate-verifications.index');
        }

        if ($request->user()->isEducator()) {
            return $this->educatorIndex($request);
        }

        $certificates = $request->user()
            ->certificates()
            ->with(['lesson', 'attempt'])
            ->latest('issued_at')
            ->paginate(12);

        return view('certificates.index', [
            'certificates' => $certificates,
            'showVerificationWorkflow' => false,
        ]);
    }

    public function show(Request $request, Certificate $certificate): View
    {
        $certificate->load(['lesson.user', 'attempt', 'user', 'issuer']);

        $isManaging = $this->canManageCertificate($request->user(), $certificate);
        $isOwner = (int) $certificate->user_id === (int) $request->user()->id;

        abort_unless($isOwner || $isManaging, 403);

        return view('certificates.show', [
            'certificate' => $certificate,
            'isManaging' => $isManaging,
            ...$this->certificateViewData($certificate),
        ]);
    }

    public function download(Request $request, Certificate $certificate): Response
    {
        $certificate->load(['lesson.user', 'attempt', 'user', 'issuer']);

        $isManaging = $this->canManageCertificate($request->user(), $certificate);
        $isOwner = (int) $certificate->user_id === (int) $request->user()->id;

        abort_unless($isOwner || $isManaging, 403);

        $viewData = $this->certificateViewData($certificate);
        $payload = view('certificates.download', [
            'certificate' => $certificate,
            ...$viewData,
            'learnerNameLines' => $this->wrapCertificateText($viewData['learnerName'], 22, 2),
            'lessonTitleLines' => $this->wrapCertificateText($viewData['lessonTitle'], 30, 3),
        ])->render();

        return response($payload, 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $certificate->downloadFilename('svg') . '"',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    private function educatorIndex(Request $request): View
    {
        $educator = $request->user();
        $search = trim((string) $request->query('search', ''));
        $ownedLessonIds = $educator->lessons()->select('id');
        $lessonOptions = collect($this->certificationRequestService->lessonOptionsForEducator($educator));

        $certificatesQuery = Certificate::query()
            ->whereIn('lesson_id', $ownedLessonIds)
            ->with(['lesson', 'user', 'attempt', 'issuer'])
            ->latest('issued_at');

        if ($search !== '') {
            $certificatesQuery->where(function (Builder $query) use ($search) {
                $query
                    ->where('certificate_code', 'like', "%{$search}%")
                    ->orWhereHas('lesson', function (Builder $lessonQuery) use ($search) {
                        $lessonQuery->where('title', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $certificates = $certificatesQuery->paginate(12)->withQueryString();

        $statsQuery = Certificate::query()->whereIn('lesson_id', $ownedLessonIds);
        $certificateCount = (clone $statsQuery)->count();
        $managedLearnerCount = (clone $statsQuery)->distinct('user_id')->count('user_id');
        $certificationRequests = $educator->certificateVerificationRequests()
            ->certificationRequests()
            ->with(['lesson', 'reviewer'])
            ->latest()
            ->get();

        return view('certificates.educator-index', [
            'certificates' => $certificates,
            'certificateCount' => $certificateCount,
            'managedLearnerCount' => $managedLearnerCount,
            'requestableLessonCount' => $lessonOptions->count(),
            'pendingRequestCount' => $certificationRequests->where('status', CertificateVerification::STATUS_PENDING)->count(),
            'approvedRequestCount' => $certificationRequests->where('status', CertificateVerification::STATUS_APPROVED)->count(),
            'rejectedRequestCount' => $certificationRequests->where('status', CertificateVerification::STATUS_REJECTED)->count(),
            'lessonOptions' => $lessonOptions,
            'certificationRequests' => $certificationRequests,
            'search' => $search,
        ]);
    }

    private function canManageCertificate(User $user, Certificate $certificate): bool
    {
        if (! $user->isEducator()) {
            return false;
        }

        $certificate->loadMissing('lesson');

        return (int) ($certificate->lesson?->user_id) === (int) $user->id;
    }

    private function certificateViewData(Certificate $certificate): array
    {
        return [
            'learnerName' => $certificate->displayLearnerName(),
            'lessonTitle' => $certificate->displayLessonTitle(),
            'issuerName' => $certificate->displayIssuerName(),
            'score' => $certificate->displayScore(),
            'examTitle' => $certificate->displayExamTitle(),
        ];
    }

    private function wrapCertificateText(string $value, int $maxCharacters, int $maxLines = 3): array
    {
        $normalized = trim((string) preg_replace('/\s+/u', ' ', $value));

        if ($normalized === '') {
            return [];
        }

        $words = preg_split('/\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [$normalized];
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $candidate = $currentLine === '' ? $word : $currentLine . ' ' . $word;

            if ($currentLine !== '' && mb_strlen($candidate) > $maxCharacters) {
                $lines[] = $currentLine;
                $currentLine = $word;
                continue;
            }

            $currentLine = $candidate;
        }

        if ($currentLine !== '') {
            $lines[] = $currentLine;
        }

        if (count($lines) <= $maxLines) {
            return $lines;
        }

        $visibleLines = array_slice($lines, 0, $maxLines);
        $visibleLines[$maxLines - 1] = Str::limit(
            implode(' ', array_slice($lines, $maxLines - 1)),
            $maxCharacters,
            '...'
        );

        return $visibleLines;
    }
}
