<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Lesson;
use App\Models\LessonExamAttempt;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CertificateManagementService
{
    public function examOptions(Lesson $lesson): array
    {
        return collect($lesson->segments ?? [])
            ->filter(fn ($segment) => ($segment['type'] ?? null) === 'exam')
            ->values()
            ->map(fn (array $segment, int $index) => [
                'index' => $index,
                'title' => $this->resolveExamTitle($segment, $index),
                'passing_score' => (int) ($segment['exam_settings']['passing_score'] ?? 70),
            ])
            ->all();
    }

    public function buildEligibility(User $educator, Lesson $lesson, User $learner, int $examIndex = 0): array
    {
        $examOptions = $this->examOptions($lesson);
        $selectedExamIndex = $this->normalizeExamIndex($lesson, $examIndex);
        $selectedExam = collect($examOptions)->firstWhere('index', $selectedExamIndex);

        $progress = LessonProgress::query()
            ->where('user_id', $learner->id)
            ->where('lesson_id', $lesson->id)
            ->first(['id', 'progress_percent', 'completed_at', 'last_viewed_at']);

        $passedAttempt = LessonExamAttempt::query()
            ->where('user_id', $learner->id)
            ->where('lesson_id', $lesson->id)
            ->where('exam_index', $selectedExamIndex)
            ->where('passed', true)
            ->latest('attempted_at')
            ->first(['id', 'score', 'attempted_at', 'exam_index']);

        $existingCertificate = Certificate::query()
            ->where('user_id', $learner->id)
            ->where('lesson_id', $lesson->id)
            ->where('exam_index', $selectedExamIndex)
            ->first(['id', 'certificate_code']);

        $progressPercent = (float) ($progress?->progress_percent ?? 0);
        $lessonCompleted = $progress && ($progress->completed_at !== null || $progressPercent >= 100);
        $hasExamOptions = count($examOptions) > 0;
        $assessmentPassed = $hasExamOptions ? $passedAttempt !== null : true;
        $ownsLesson = (int) $lesson->user_id === (int) $educator->id;
        $isPublished = (bool) $lesson->is_published;
        $isLearner = $learner->isLearner();
        $isDuplicate = $existingCertificate !== null;
        $selectedExamExists = ! $hasExamOptions || $selectedExam !== null;

        $checks = [
            [
                'label' => __('certificates.verify_lesson_owner'),
                'passed' => $ownsLesson,
                'detail' => $ownsLesson
                    ? __('certificates.verify_lesson_owner_pass')
                    : __('certificates.verify_lesson_owner_fail'),
            ],
            [
                'label' => __('certificates.verify_lesson_published'),
                'passed' => $isPublished,
                'detail' => $isPublished
                    ? __('certificates.verify_lesson_published_pass')
                    : __('certificates.verify_lesson_published_fail'),
            ],
            [
                'label' => __('certificates.verify_learner_account'),
                'passed' => $isLearner,
                'detail' => $isLearner
                    ? __('certificates.verify_learner_account_pass')
                    : __('certificates.verify_learner_account_fail'),
            ],
            [
                'label' => __('certificates.verify_completion'),
                'passed' => $lessonCompleted,
                'detail' => $lessonCompleted
                    ? __('certificates.verify_completion_pass', ['progress' => (int) round($progressPercent)])
                    : __('certificates.verify_completion_fail'),
            ],
            [
                'label' => __('certificates.verify_assessment'),
                'passed' => $assessmentPassed,
                'detail' => $assessmentPassed
                    ? ($hasExamOptions
                        ? __('certificates.verify_assessment_pass')
                        : __('certificates.verify_assessment_not_required'))
                    : __('certificates.verify_assessment_fail'),
            ],
            [
                'label' => __('certificates.verify_unique_certificate'),
                'passed' => ! $isDuplicate,
                'detail' => $isDuplicate
                    ? __('certificates.verify_unique_certificate_fail')
                    : __('certificates.verify_unique_certificate_pass'),
            ],
        ];

        $canIssue = collect($checks)->every(fn (array $check) => $check['passed'])
            && $selectedExamExists;

        return [
            'can_issue' => $canIssue,
            'checks' => $checks,
            'lesson' => $lesson,
            'learner' => $learner,
            'progress' => $progress,
            'progress_percent' => $progressPercent,
            'passed_attempt' => $passedAttempt,
            'existing_certificate' => $existingCertificate,
            'selected_exam_index' => $selectedExamIndex,
            'selected_exam_title' => $selectedExam['title'] ?? __('certificates.default_manual_exam_title'),
            'selected_exam_passing_score' => $selectedExam['passing_score'] ?? null,
            'has_exam_options' => $hasExamOptions,
            'selected_exam_exists' => $selectedExamExists,
        ];
    }

    public function issueForEducator(User $educator, array $payload): Certificate
    {
        $lesson = Lesson::query()
            ->whereKey($payload['lesson_id'])
            ->firstOrFail();

        $learner = User::query()
            ->where('email', $payload['learner_email'])
            ->firstOrFail();

        $examIndex = $this->normalizeExamIndex($lesson, (int) ($payload['exam_index'] ?? 0));
        $eligibility = $this->buildEligibility($educator, $lesson, $learner, $examIndex);

        if (! $eligibility['selected_exam_exists']) {
            throw ValidationException::withMessages([
                'exam_index' => __('certificates.invalid_exam_selection'),
            ]);
        }

        if (! $eligibility['can_issue']) {
            throw ValidationException::withMessages($this->eligibilityErrors($eligibility));
        }

        $issuedAt = isset($payload['issued_at']) ? Carbon::parse($payload['issued_at']) : now();
        $score = $this->resolveScore($payload['score'] ?? null, $eligibility);
        $passingScore = $this->resolvePassingScore($payload['passing_score'] ?? null, $eligibility);

        return Certificate::create([
            'user_id' => $learner->id,
            'issued_by_user_id' => $educator->id,
            'lesson_id' => $lesson->id,
            'lesson_exam_attempt_id' => $eligibility['passed_attempt']?->id,
            'exam_index' => $eligibility['selected_exam_index'],
            'certificate_code' => $this->generateCertificateCode(),
            'issued_at' => $issuedAt,
            'validated_at' => now(),
            'validation_notes' => $this->normalizeNullableString($payload['validation_notes'] ?? null),
            'snapshot' => $this->buildSnapshot(
                lesson: $lesson,
                learner: $learner,
                educator: $educator,
                examTitle: $this->normalizeNullableString($payload['exam_title'] ?? null) ?? $eligibility['selected_exam_title'],
                examIndex: $eligibility['selected_exam_index'],
                score: $score,
                passingScore: $passingScore
            ),
        ]);
    }

    public function updateForEducator(User $educator, Certificate $certificate, array $payload): Certificate
    {
        $certificate->loadMissing(['lesson', 'user', 'attempt']);

        $score = $this->resolveScore($payload['score'] ?? null, [
            'passed_attempt' => $certificate->attempt,
            'progress_percent' => data_get($certificate->snapshot, 'score'),
        ]);

        $snapshot = array_merge(
            $certificate->snapshot ?? [],
            $this->buildSnapshot(
                lesson: $certificate->lesson,
                learner: $certificate->user,
                educator: $educator,
                examTitle: $this->normalizeNullableString($payload['exam_title'] ?? null)
                    ?? data_get($certificate->snapshot, 'exam_title')
                    ?? __('certificates.default_manual_exam_title'),
                examIndex: (int) $certificate->exam_index,
                score: $score,
                passingScore: $this->resolvePassingScore(
                    $payload['passing_score'] ?? null,
                    ['selected_exam_passing_score' => data_get($certificate->snapshot, 'passing_score')]
                )
            )
        );

        $certificate->fill([
            'issued_by_user_id' => $educator->id,
            'issued_at' => Carbon::parse($payload['issued_at']),
            'validated_at' => now(),
            'validation_notes' => $this->normalizeNullableString($payload['validation_notes'] ?? null),
            'snapshot' => $snapshot,
        ])->save();

        return $certificate->fresh(['lesson.user', 'user', 'attempt', 'issuer']);
    }

    public function normalizeExamIndex(Lesson $lesson, int $examIndex): int
    {
        $examOptions = $this->examOptions($lesson);

        if ($examOptions === []) {
            return 0;
        }

        return collect($examOptions)->pluck('index')->contains($examIndex)
            ? $examIndex
            : 0;
    }

    private function buildSnapshot(
        Lesson $lesson,
        User $learner,
        User $educator,
        string $examTitle,
        int $examIndex,
        ?float $score,
        ?int $passingScore
    ): array {
        return [
            'learner_name' => $learner->name,
            'lesson_title' => $lesson->title,
            'exam_title' => $examTitle,
            'exam_index' => $examIndex,
            'lesson_slug' => $lesson->slug,
            'lesson_difficulty' => $lesson->difficulty,
            'issuer_name' => $educator->name,
            'score' => $score,
            'passing_score' => $passingScore,
        ];
    }

    private function resolveExamTitle(array $segment, int $examIndex): string
    {
        $customName = trim((string) ($segment['custom_name'] ?? ''));

        if ($customName !== '') {
            return $customName;
        }

        return __('lessons.exam_index_label') . ' ' . ($examIndex + 1);
    }

    private function resolveScore(mixed $score, array $eligibility): ?float
    {
        if ($score !== null && $score !== '') {
            return round((float) $score, 2);
        }

        if (($eligibility['passed_attempt'] ?? null) !== null) {
            return (float) $eligibility['passed_attempt']->score;
        }

        if (($eligibility['progress_percent'] ?? null) !== null) {
            return round((float) $eligibility['progress_percent'], 2);
        }

        return null;
    }

    private function resolvePassingScore(mixed $passingScore, array $eligibility): ?int
    {
        if ($passingScore !== null && $passingScore !== '') {
            return (int) $passingScore;
        }

        if (($eligibility['selected_exam_passing_score'] ?? null) !== null) {
            return (int) $eligibility['selected_exam_passing_score'];
        }

        return null;
    }

    private function eligibilityErrors(array $eligibility): array
    {
        $messages = [];

        if (! data_get($eligibility, 'checks.0.passed')) {
            $messages['lesson_id'] = __('certificates.error_lesson_owner');
        } elseif (! data_get($eligibility, 'checks.1.passed')) {
            $messages['lesson_id'] = __('certificates.error_lesson_published');
        }

        if (! data_get($eligibility, 'checks.2.passed')) {
            $messages['learner_email'] = __('certificates.error_learner_role');
        } elseif (! data_get($eligibility, 'checks.3.passed')) {
            $messages['learner_email'] = __('certificates.error_learner_completion');
        } elseif (! data_get($eligibility, 'checks.4.passed')) {
            $messages['exam_index'] = __('certificates.error_learner_assessment');
        }

        if (! data_get($eligibility, 'checks.5.passed')) {
            $messages['lesson_id'] = __('certificates.error_certificate_exists');
        }

        return $messages;
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
