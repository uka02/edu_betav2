<?php

namespace App\Http\Controllers;

use App\Http\Requests\Certificates\ReviewCertificateVerificationRequest;
use App\Models\CertificateVerification;
use App\Services\CertificationRequestService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCertificateVerificationController extends Controller
{
    public function __construct(
        private readonly CertificationRequestService $certificationRequestService
    ) {
    }

    public function index(Request $request): View
    {
        $status = trim((string) $request->query('status', 'pending'));
        $search = trim((string) $request->query('search', ''));

        if (! in_array($status, ['pending', 'approved', 'rejected', 'all'], true)) {
            $status = 'pending';
        }

        $query = CertificateVerification::query()
            ->certificationRequests()
            ->with(['user', 'reviewer', 'lesson'])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('issuer_name', 'like', "%{$search}%")
                    ->orWhere('original_filename', 'like', "%{$search}%")
                    ->orWhere('passing_score', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
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

        return view('admin.certificate-verifications.index', [
            'requests' => $query->paginate(12)->withQueryString(),
            'status' => $status,
            'search' => $search,
            'pendingCount' => CertificateVerification::query()->certificationRequests()->where('status', CertificateVerification::STATUS_PENDING)->count(),
            'approvedCount' => CertificateVerification::query()->certificationRequests()->where('status', CertificateVerification::STATUS_APPROVED)->count(),
            'rejectedCount' => CertificateVerification::query()->certificationRequests()->where('status', CertificateVerification::STATUS_REJECTED)->count(),
        ]);
    }

    public function approve(ReviewCertificateVerificationRequest $request, CertificateVerification $verification): RedirectResponse
    {
        $verification->update([
            'status' => CertificateVerification::STATUS_APPROVED,
            'reviewed_by_user_id' => $request->user()->id,
            'review_notes' => $request->validated('review_notes'),
            'reviewed_at' => now(),
        ]);

        $issuedCount = $this->certificationRequestService->backfillApprovedRequest($verification);

        return back()->with('success', __('certificates.verification_approved', ['count' => $issuedCount]));
    }

    public function reject(ReviewCertificateVerificationRequest $request, CertificateVerification $verification): RedirectResponse
    {
        $verification->update([
            'status' => CertificateVerification::STATUS_REJECTED,
            'reviewed_by_user_id' => $request->user()->id,
            'review_notes' => $request->validated('review_notes'),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', __('certificates.verification_rejected'));
    }
}
