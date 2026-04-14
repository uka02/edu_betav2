<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Lesson;
use App\Models\LessonExamAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class LessonExamService
{
    public function __construct(
        private readonly CertificationRequestService $certificationRequestService
    ) {
    }

    public function gradeAndRecord(Lesson $lesson, User $user, int $examIndex, array $answers, int $timeTaken): array
    {
        $examSegment = collect($lesson->segments ?? [])
            ->filter(fn ($segment) => ($segment['type'] ?? null) === 'exam')
            ->values()
            ->get($examIndex);

        if (! is_array($examSegment)) {
            throw new InvalidArgumentException('Exam not found.');
        }

        $questions = $examSegment['questions'] ?? [];
        $passingScore = (int) ($examSegment['exam_settings']['passing_score'] ?? 70);
        $timeLimitExceeded = $this->timeLimitExceeded($examSegment, $timeTaken);
        [$correctCount, $results] = $this->gradeQuestions($questions, $answers);

        $totalQuestions = count($questions);
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;
        $passed = ! $timeLimitExceeded && $score >= $passingScore;

        return DB::transaction(function () use (
            $lesson,
            $user,
            $examIndex,
            $answers,
            $timeTaken,
            $score,
            $passed,
            $correctCount,
            $totalQuestions,
            $results,
            $timeLimitExceeded
        ) {
            $attempt = LessonExamAttempt::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'exam_index' => $examIndex,
                'score' => $score,
                'passed' => $passed,
                'correct_count' => $correctCount,
                'total_questions' => $totalQuestions,
                'time_taken' => $timeTaken,
                'answers' => $answers,
                'results' => $results,
                'attempted_at' => now(),
            ]);

            $certificate = $this->issueCertificate($lesson, $user, $attempt);

            return [
                'attempt' => $attempt,
                'certificate' => $certificate,
                'score' => $score,
                'passed' => $passed,
                'correct_count' => $correctCount,
                'total_questions' => $totalQuestions,
                'time_taken' => $timeTaken,
                'results' => $results,
                'time_limit_exceeded' => $timeLimitExceeded,
            ];
        });
    }

    private function timeLimitExceeded(array $examSegment, int $timeTaken): bool
    {
        $timeLimitMinutes = max(0, (int) ($examSegment['exam_settings']['time_limit'] ?? 0));

        if ($timeLimitMinutes === 0) {
            return false;
        }

        return $timeTaken > ($timeLimitMinutes * 60);
    }

    private function gradeQuestions(array $questions, array $userAnswers): array
    {
        $correctCount = 0;
        $results = [];

        foreach ($questions as $index => $question) {
            $userAnswer = $userAnswers[$index] ?? null;
            $correctAnswer = $question['correct_answer'] ?? null;
            $isCorrect = false;

            if (($question['type'] ?? null) === 'multiple_choice') {
                $isCorrect = (string) $userAnswer === (string) $correctAnswer;
            } elseif (($question['type'] ?? null) === 'true_false') {
                $userAnswerStr = ($userAnswer === 0 || $userAnswer === '0') ? 'true' : 'false';
                $isCorrect = $userAnswerStr === (string) $correctAnswer;
            } elseif (($question['type'] ?? null) === 'short_answer') {
                $caseSensitive = (bool) ($question['case_sensitive'] ?? false);
                $userAnswerText = (string) $userAnswer;
                $correctAnswerText = (string) $correctAnswer;

                $isCorrect = $caseSensitive
                    ? $userAnswerText === $correctAnswerText
                    : Str::lower($userAnswerText) === Str::lower($correctAnswerText);
            }

            if ($isCorrect) {
                $correctCount++;
            }

            $results[] = [
                'question_index' => $index,
                'question' => $question['question'] ?? '',
                'type' => $question['type'] ?? 'multiple_choice',
                'user_answer' => $userAnswer,
                'correct_answer' => $correctAnswer,
                'is_correct' => $isCorrect,
            ];
        }

        return [$correctCount, $results];
    }

    private function issueCertificate(Lesson $lesson, User $user, LessonExamAttempt $attempt): ?Certificate
    {
        if (! $attempt->passed) {
            return null;
        }

        return $this->certificationRequestService->issueCertificateForAttempt($lesson, $user, $attempt);
    }
}
