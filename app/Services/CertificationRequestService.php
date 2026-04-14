<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateVerification;
use App\Models\Lesson;
use App\Models\LessonExamAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CertificationRequestService
{
    public function lessonOptionsForEducator(User $educator): array
    {
        return $educator->lessons()
            ->orderBy('title')
            ->get(['id', 'title', 'segments', 'is_published'])
            ->map(fn (Lesson $lesson) => $this->buildLessonOption($lesson))
            ->filter()
            ->values()
            ->all();
    }

    public function buildLessonOption(Lesson $lesson): ?array
    {
        $finalExam = $this->finalExamForLesson($lesson);

        if (! $finalExam) {
            return null;
        }

        return [
            'id' => $lesson->id,
            'title' => $lesson->title,
            'is_published' => (bool) $lesson->is_published,
            'final_exam_index' => $finalExam['index'],
            'final_exam_title' => $finalExam['title'],
            'default_passing_score' => $finalExam['passing_score'],
        ];
    }

    public function finalExamForLesson(Lesson $lesson): ?array
    {
        $examSegments = collect($lesson->segments ?? [])
            ->filter(fn ($segment) => ($segment['type'] ?? null) === 'exam')
            ->values();

        if ($examSegments->isEmpty()) {
            return null;
        }

        $examIndex = $examSegments->count() - 1;
        $segment = $examSegments->last();

        return [
            'index' => $examIndex,
            'title' => $this->resolveExamTitle((array) $segment, $examIndex),
            'passing_score' => (int) data_get($segment, 'exam_settings.passing_score', 70),
        ];
    }

    public function createRequest(User $educator, array $payload): CertificateVerification
    {
        $lesson = $educator->lessons()
            ->whereKey($payload['lesson_id'])
            ->first();

        if (! $lesson) {
            throw ValidationException::withMessages([
                'lesson_id' => __('certificates.invalid_request_lesson'),
            ]);
        }

        $finalExam = $this->finalExamForLesson($lesson);

        if (! $finalExam) {
            throw ValidationException::withMessages([
                'lesson_id' => __('certificates.lesson_requires_final_exam'),
            ]);
        }

        $hasPendingRequest = CertificateVerification::query()
            ->certificationRequests()
            ->where('user_id', $educator->id)
            ->where('lesson_id', $lesson->id)
            ->where('status', CertificateVerification::STATUS_PENDING)
            ->exists();

        if ($hasPendingRequest) {
            throw ValidationException::withMessages([
                'lesson_id' => __('certificates.pending_request_exists'),
            ]);
        }

        /** @var UploadedFile $document */
        $document = $payload['document'];

        return CertificateVerification::create([
            'user_id' => $educator->id,
            'lesson_id' => $lesson->id,
            'title' => trim((string) $payload['title']),
            'passing_score' => (int) $payload['passing_score'],
            'issuer_name' => $educator->name,
            'notes' => $this->normalizeNullableString($payload['notes'] ?? null),
            'document_path' => $document->store('certificate-verifications', 'local'),
            'original_filename' => $document->getClientOriginalName(),
            'status' => CertificateVerification::STATUS_PENDING,
        ]);
    }

    public function activeRequestForLesson(Lesson $lesson): ?CertificateVerification
    {
        return CertificateVerification::query()
            ->certificationRequests()
            ->where('lesson_id', $lesson->id)
            ->where('status', CertificateVerification::STATUS_APPROVED)
            ->latest('reviewed_at')
            ->latest('id')
            ->first();
    }

    public function issueCertificateForAttempt(Lesson $lesson, User $learner, LessonExamAttempt $attempt): ?Certificate
    {
        $lesson->loadMissing('user');

        $request = $this->activeRequestForLesson($lesson);

        if (! $request || ! $lesson->is_published || (int) $lesson->user_id === (int) $learner->id) {
            return null;
        }

        $finalExam = $this->finalExamForLesson($lesson);

        if (! $finalExam || (int) $attempt->exam_index !== (int) $finalExam['index']) {
            return null;
        }

        $requiredScore = (int) ($request->passing_score ?? $finalExam['passing_score']);

        if ((float) $attempt->score < $requiredScore) {
            return null;
        }

        return $this->firstOrCreateCertificate($lesson, $learner, $attempt, $request, $requiredScore);
    }

    public function backfillApprovedRequest(CertificateVerification $request): int
    {
        $request->loadMissing(['lesson.user']);

        $lesson = $request->lesson;

        if (! $lesson) {
            return 0;
        }

        $finalExam = $this->finalExamForLesson($lesson);

        if (! $finalExam) {
            return 0;
        }

        $requiredScore = (int) ($request->passing_score ?? $finalExam['passing_score']);

        /** @var Collection<int, LessonExamAttempt> $attempts */
        $attempts = LessonExamAttempt::query()
            ->with('user')
            ->where('lesson_id', $lesson->id)
            ->where('exam_index', $finalExam['index'])
            ->where('score', '>=', $requiredScore)
            ->orderByDesc('attempted_at')
            ->get()
            ->unique('user_id')
            ->values();

        $issuedCount = 0;

        foreach ($attempts as $attempt) {
            $learner = $attempt->user;

            if (! $learner || ! $learner->isLearner()) {
                continue;
            }

            $certificate = $this->firstOrCreateCertificate($lesson, $learner, $attempt, $request, $requiredScore);

            if ($certificate->wasRecentlyCreated) {
                $issuedCount++;
            }
        }

        return $issuedCount;
    }

    private function firstOrCreateCertificate(
        Lesson $lesson,
        User $learner,
        LessonExamAttempt $attempt,
        CertificateVerification $request,
        int $requiredScore
    ): Certificate {
        return Certificate::firstOrCreate(
            [
                'user_id' => $learner->id,
                'lesson_id' => $lesson->id,
                'exam_index' => $attempt->exam_index,
            ],
            [
                'lesson_exam_attempt_id' => $attempt->id,
                'issued_by_user_id' => $lesson->user_id,
                'certificate_code' => $this->generateCertificateCode(),
                'issued_at' => now(),
                'validated_at' => now(),
                'validation_notes' => $this->normalizeNullableString($request->notes),
                'snapshot' => [
                    'learner_name' => $learner->name,
                    'lesson_title' => $lesson->title,
                    'exam_title' => $request->title,
                    'exam_index' => $attempt->exam_index,
                    'lesson_slug' => $lesson->slug,
                    'lesson_difficulty' => $lesson->difficulty,
                    'issuer_name' => $lesson->user?->name,
                    'score' => (float) $attempt->score,
                    'passing_score' => $requiredScore,
                    'correct_count' => $attempt->correct_count,
                    'total_questions' => $attempt->total_questions,
                    'certification_request_id' => $request->id,
                ],
            ]
        );
    }

    private function resolveExamTitle(array $segment, int $examIndex): string
    {
        $customName = trim((string) ($segment['custom_name'] ?? ''));

        if ($customName !== '') {
            return $customName;
        }

        return __('lessons.exam_index_label') . ' ' . ($examIndex + 1);
    }

    private function generateCertificateCode(): string
    {
        do {
            $code = 'CERT-' . now()->format('Ymd') . '-' . Str::upper(Str::random(10));
        } while (Certificate::where('certificate_code', $code)->exists());

        return $code;
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
