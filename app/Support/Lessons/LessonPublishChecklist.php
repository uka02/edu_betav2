<?php

namespace App\Support\Lessons;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class LessonPublishChecklist
{
    private const VALID_TYPES = ['video', 'text', 'document'];

    public function evaluateRequest(Request $request, ?Lesson $lesson = null): array
    {
        $type = (string) $request->input('type', $lesson?->type ?? '');
        $subject = (string) ($request->input('subject') ?? $lesson?->subject ?? Lesson::defaultSubject());
        $segments = $this->normalizeRequestSegments(
            is_array($request->input('segments', [])) ? $request->input('segments', []) : [],
            is_array($request->file('segments', [])) ? $request->file('segments', []) : [],
        );

        return $this->buildResult([
            'title' => filled(trim((string) ($request->input('title') ?? $lesson?->title ?? ''))),
            'type' => in_array($type, self::VALID_TYPES, true),
            'subject' => in_array($subject, Lesson::validSubjectInputs(), true),
            'duration' => $this->resolveDurationMinutesFromRequest($request) > 0,
            'media' => $this->hasRequiredMedia(
                $type,
                (string) ($request->input('video_url') ?? $lesson?->video_url ?? ''),
                $request->hasFile('document') || filled($lesson?->document_path),
            ),
            'segments' => count($segments) > 0,
            'completion' => count($segments) > 0 && $this->allSegmentsComplete($segments),
        ]);
    }

    public function evaluateLesson(Lesson $lesson): array
    {
        $type = (string) $lesson->type;
        $subject = (string) ($lesson->subject ?? Lesson::defaultSubject());
        $segments = $this->normalizeStoredSegments($lesson->segments ?? []);

        return $this->buildResult([
            'title' => filled(trim((string) $lesson->title)),
            'type' => in_array($type, self::VALID_TYPES, true),
            'subject' => in_array($subject, Lesson::validSubjectInputs(), true),
            'duration' => (int) $lesson->duration_minutes > 0,
            'media' => $this->hasRequiredMedia(
                $type,
                (string) ($lesson->video_url ?? ''),
                filled($lesson->document_path),
            ),
            'segments' => count($segments) > 0,
            'completion' => count($segments) > 0 && $this->allSegmentsComplete($segments),
        ]);
    }

    private function buildResult(array $items): array
    {
        $failedKeys = array_keys(array_filter($items, static fn (bool $complete) => ! $complete));

        return [
            'items' => $items,
            'ready' => $failedKeys === [],
            'failed_keys' => $failedKeys,
        ];
    }

    private function hasRequiredMedia(string $type, string $videoUrl, bool $hasDocument): bool
    {
        return match ($type) {
            'video' => filled(trim($videoUrl)),
            'document' => $hasDocument,
            'text' => true,
            default => false,
        };
    }

    private function resolveDurationMinutesFromRequest(Request $request): int
    {
        return ((int) $request->input('duration_hours', 0) * 60)
            + (int) $request->input('duration_minutes', 0);
    }

    private function normalizeRequestSegments(array $segmentData, array $fileData): array
    {
        $segments = [];

        foreach ($segmentData as $segmentId => $segment) {
            if ((int) $segmentId === 0 || ! is_array($segment)) {
                continue;
            }

            $normalizedSegment = [
                'type' => isset($segment['exam_settings']) || isset($segment['questions']) ? 'exam' : 'content',
                'blocks' => [],
                'questions' => [],
            ];

            if (is_array($segment['blocks'] ?? null)) {
                foreach ($segment['blocks'] as $blockId => $block) {
                    if (! is_array($block)) {
                        continue;
                    }

                    $normalizedSegment['blocks'][] = [
                        'type' => (string) ($block['type'] ?? ''),
                        'content' => $block['content'] ?? null,
                        'path' => $this->resolveRequestBlockPath($fileData, $segmentId, $blockId, $block),
                        'question' => $block['question'] ?? null,
                        'answers' => is_array($block['answers'] ?? null) ? $block['answers'] : [],
                        'correct_answer' => $block['correct_answer'] ?? null,
                    ];
                }
            }

            if (is_array($segment['questions'] ?? null)) {
                foreach ($segment['questions'] as $question) {
                    if (! is_array($question)) {
                        continue;
                    }

                    $normalizedSegment['questions'][] = [
                        'type' => (string) ($question['type'] ?? ''),
                        'question' => $question['question'] ?? null,
                        'answers' => is_array($question['answers'] ?? null) ? $question['answers'] : [],
                        'correct_answer' => $question['correct_answer'] ?? null,
                    ];
                }
            }

            $segments[] = $normalizedSegment;
        }

        return $segments;
    }

    private function normalizeStoredSegments(mixed $segments): array
    {
        if (! is_array($segments)) {
            return [];
        }

        $normalized = [];

        foreach ($segments as $segment) {
            if (! is_array($segment)) {
                continue;
            }

            $normalized[] = [
                'type' => (string) ($segment['type'] ?? 'content'),
                'blocks' => is_array($segment['blocks'] ?? null) ? $segment['blocks'] : [],
                'questions' => is_array($segment['questions'] ?? null) ? $segment['questions'] : [],
            ];
        }

        return $normalized;
    }

    private function resolveRequestBlockPath(array $fileData, int|string $segmentId, int|string $blockId, array $block): ?string
    {
        if ($this->hasUploadedSegmentFile($fileData[$segmentId]['blocks'][$blockId]['image'] ?? null)
            || $this->hasUploadedSegmentFile($fileData[$segmentId]['blocks'][$blockId]['file'] ?? null)) {
            return '__uploaded__';
        }

        $existingPath = $block['existing_path'] ?? null;

        return filled($existingPath) ? (string) $existingPath : null;
    }

    private function hasUploadedSegmentFile(mixed $file): bool
    {
        return $file instanceof UploadedFile && $file->isValid();
    }

    private function allSegmentsComplete(array $segments): bool
    {
        foreach ($segments as $segment) {
            if (! $this->segmentIsComplete($segment)) {
                return false;
            }
        }

        return true;
    }

    private function segmentIsComplete(array $segment): bool
    {
        if (($segment['type'] ?? 'content') === 'exam') {
            $questions = array_values(array_filter(
                is_array($segment['questions'] ?? null) ? $segment['questions'] : [],
                'is_array'
            ));

            if ($questions === []) {
                return false;
            }

            foreach ($questions as $question) {
                if (! $this->questionHasMeaningfulContent($question)) {
                    return false;
                }
            }

            return true;
        }

        $blocks = array_values(array_filter(
            is_array($segment['blocks'] ?? null) ? $segment['blocks'] : [],
            'is_array'
        ));

        if ($blocks === []) {
            return false;
        }

        foreach ($blocks as $block) {
            if (! $this->blockHasMeaningfulContent($block)) {
                return false;
            }
        }

        return true;
    }

    private function blockHasMeaningfulContent(array $block): bool
    {
        return match ($block['type'] ?? null) {
            'text', 'subheading', 'callout', 'code' => filled(trim((string) ($block['content'] ?? ''))),
            'video' => filled(trim((string) ($block['content'] ?? ''))),
            'image', 'file' => filled($block['path'] ?? null),
            'divider' => true,
            'quiz' => $this->quizBlockHasMeaningfulContent($block),
            default => false,
        };
    }

    private function quizBlockHasMeaningfulContent(array $block): bool
    {
        if (blank(trim((string) ($block['question'] ?? '')))) {
            return false;
        }

        $answers = is_array($block['answers'] ?? null) ? $block['answers'] : [];
        $filledAnswers = array_filter($answers, static fn ($answer) => filled(trim((string) $answer)));
        $correctIndex = is_numeric($block['correct_answer'] ?? null) ? (int) $block['correct_answer'] : null;

        return count($filledAnswers) >= 2
            && $correctIndex !== null
            && array_key_exists($correctIndex, $answers)
            && filled(trim((string) $answers[$correctIndex]));
    }

    private function questionHasMeaningfulContent(array $question): bool
    {
        if (blank(trim((string) ($question['question'] ?? '')))) {
            return false;
        }

        return match ($question['type'] ?? null) {
            'multiple_choice' => $this->multipleChoiceQuestionHasMeaningfulContent($question),
            'true_false' => in_array((string) ($question['correct_answer'] ?? ''), ['true', 'false'], true),
            'short_answer' => filled(trim((string) ($question['correct_answer'] ?? ''))),
            default => false,
        };
    }

    private function multipleChoiceQuestionHasMeaningfulContent(array $question): bool
    {
        $answers = is_array($question['answers'] ?? null) ? $question['answers'] : [];
        $filledAnswers = array_filter($answers, static fn ($answer) => filled(trim((string) $answer)));
        $correctIndex = is_numeric($question['correct_answer'] ?? null) ? (int) $question['correct_answer'] : null;

        return count($filledAnswers) >= 2
            && $correctIndex !== null
            && array_key_exists($correctIndex, $answers)
            && filled(trim((string) $answers[$correctIndex]));
    }
}
