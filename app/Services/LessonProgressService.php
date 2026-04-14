<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;

class LessonProgressService
{
    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public function recordBatch(User $user, Lesson $lesson, array $items): LessonProgress
    {
        $progress = LessonProgress::firstOrNew([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $trackableItems = $this->trackableItemsForLesson($lesson);
        $state = $this->normalizeState($progress->progress_state);

        foreach ($items as $item) {
            $key = (string) ($item['key'] ?? '');

            if ($key === '' || ! isset($trackableItems[$key])) {
                continue;
            }

            $expectedKind = $trackableItems[$key]['kind'];
            $existingItemState = $state['items'][$key] ?? [];

            $state['items'][$key] = $expectedKind === 'video'
                ? $this->mergeVideoItemState($existingItemState, $item)
                : $this->mergeBlockItemState($existingItemState, $item);
        }

        $summary = $this->summarizeState($lesson, $state, $trackableItems);

        $progress->fill([
            'watched_seconds' => $summary['watched_seconds'],
            'last_position_seconds' => $summary['last_position_seconds'],
            'progress_percent' => $summary['progress_percent'],
            'progress_state' => $state,
            'last_viewed_at' => now(),
            'completed_at' => $summary['progress_percent'] >= 100 ? ($progress->completed_at ?? now()) : null,
        ]);

        $progress->save();

        return $progress;
    }

    public function syncStoredProgress(?LessonProgress $progress, Lesson $lesson): ?LessonProgress
    {
        if (! $progress) {
            return null;
        }

        $state = $this->normalizeState($progress->progress_state);
        $summary = $this->summarizeState($lesson, $state);

        $progress->forceFill([
            'watched_seconds' => $summary['watched_seconds'],
            'last_position_seconds' => $summary['last_position_seconds'],
            'progress_percent' => $summary['progress_percent'],
            'progress_state' => $state,
            'completed_at' => $summary['progress_percent'] >= 100 ? ($progress->completed_at ?? now()) : null,
        ]);

        if ($progress->isDirty([
            'watched_seconds',
            'last_position_seconds',
            'progress_percent',
            'progress_state',
            'completed_at',
        ])) {
            $progress->save();
        }

        return $progress;
    }

    /**
     * @return array<string, array{kind: string}>
     */
    public function trackableItemsForLesson(Lesson $lesson): array
    {
        $items = [];

        if ($lesson->type === 'video' && filled($lesson->video_url)) {
            $items['main-video'] = ['kind' => 'video'];
        }

        if ($lesson->type === 'document' && filled($lesson->document_path)) {
            $items['main-document'] = ['kind' => 'block'];
        }

        $segments = $lesson->segments ?? [];

        if (is_array($segments) && count($segments) > 0) {
            foreach ($segments as $segmentIndex => $segment) {
                $segmentId = (int) ($segment['id'] ?? ($segmentIndex + 1));
                $blocks = $segment['blocks'] ?? [];

                if (! is_array($blocks)) {
                    continue;
                }

                foreach ($blocks as $blockIndex => $block) {
                    $blockId = (int) ($block['id'] ?? ($blockIndex + 1));
                    $blockType = (string) ($block['type'] ?? '');

                    if (! in_array($blockType, $this->trackableBlockTypes(), true)) {
                        continue;
                    }

                    $items[$this->blockKey($segmentId, $blockId)] = [
                        'kind' => $blockType === 'video' ? 'video' : 'block',
                    ];
                }
            }
        } elseif (filled($lesson->content)) {
            $items['legacy-content'] = ['kind' => 'block'];
        }

        return $items;
    }

    public function blockKey(int $segmentId, int $blockId): string
    {
        return "segment-{$segmentId}-block-{$blockId}";
    }

    /**
     * @param  mixed  $state
     * @return array{items: array<string, array<string, mixed>>}
     */
    private function normalizeState(mixed $state): array
    {
        $items = [];

        if (is_array($state) && isset($state['items']) && is_array($state['items'])) {
            $items = $state['items'];
        }

        return ['items' => $items];
    }

    /**
     * @param  array<string, mixed>  $existingItemState
     * @param  array<string, mixed>  $incomingItemState
     * @return array<string, mixed>
     */
    private function mergeVideoItemState(array $existingItemState, array $incomingItemState): array
    {
        $existingProgressPercent = (int) ($existingItemState['progress_percent'] ?? 0);
        $incomingProgressPercent = $this->resolveVideoProgressPercent($incomingItemState, $existingItemState);
        $durationSeconds = max(
            (int) ($incomingItemState['duration_seconds'] ?? 0),
            (int) ($existingItemState['duration_seconds'] ?? 0)
        );

        $positionSeconds = max(0, (int) ($incomingItemState['position_seconds'] ?? $existingItemState['position_seconds'] ?? 0));
        $progressPercent = max($existingProgressPercent, $incomingProgressPercent);
        $completed = $this->toBool($incomingItemState['completed'] ?? false)
            || $this->toBool($existingItemState['completed'] ?? false)
            || $progressPercent >= 90;

        return [
            'kind' => 'video',
            'progress_percent' => $completed ? 100 : min(100, $progressPercent),
            'position_seconds' => $positionSeconds,
            'duration_seconds' => $durationSeconds,
            'completed' => $completed,
        ];
    }

    /**
     * @param  array<string, mixed>  $existingItemState
     * @param  array<string, mixed>  $incomingItemState
     * @return array<string, mixed>
     */
    private function mergeBlockItemState(array $existingItemState, array $incomingItemState): array
    {
        $completed = $this->toBool($incomingItemState['completed'] ?? false)
            || $this->toBool($existingItemState['completed'] ?? false);

        $progressPercent = $completed ? 100 : max(
            (int) ($existingItemState['progress_percent'] ?? 0),
            (int) ($incomingItemState['progress_percent'] ?? 0)
        );

        return [
            'kind' => 'block',
            'progress_percent' => min(100, $progressPercent),
            'completed' => $completed || $progressPercent >= 100,
        ];
    }

    /**
     * @param  array<string, mixed>  $incomingItemState
     * @param  array<string, mixed>  $existingItemState
     */
    private function resolveVideoProgressPercent(array $incomingItemState, array $existingItemState): int
    {
        if (isset($incomingItemState['progress_percent'])) {
            return max(0, min(100, (int) $incomingItemState['progress_percent']));
        }

        $positionSeconds = (int) ($incomingItemState['position_seconds'] ?? 0);
        $durationSeconds = (int) ($incomingItemState['duration_seconds'] ?? $existingItemState['duration_seconds'] ?? 0);

        if ($durationSeconds <= 0) {
            return 0;
        }

        return max(0, min(100, (int) round(($positionSeconds / $durationSeconds) * 100)));
    }

    /**
     * @param  array{items: array<string, array<string, mixed>>}  $state
     * @param  array<string, array{kind: string}>|null  $trackableItems
     * @return array{progress_percent: int, watched_seconds: int, last_position_seconds: int}
     */
    private function summarizeState(Lesson $lesson, array $state, ?array $trackableItems = null): array
    {
        $trackableItems ??= $this->trackableItemsForLesson($lesson);
        $totalTrackableItems = count($trackableItems);

        if ($totalTrackableItems === 0) {
            return [
                'progress_percent' => 0,
                'watched_seconds' => 0,
                'last_position_seconds' => 0,
            ];
        }

        $completedUnits = 0.0;
        $watchedSeconds = 0;
        $lastPositionSeconds = 0;

        foreach ($trackableItems as $key => $itemMeta) {
            $itemState = $state['items'][$key] ?? null;

            if (! is_array($itemState)) {
                continue;
            }

            if ($itemMeta['kind'] === 'video') {
                $progressPercent = max(0, min(100, (int) ($itemState['progress_percent'] ?? 0)));
                $durationSeconds = max(0, (int) ($itemState['duration_seconds'] ?? 0));
                $completedUnits += $progressPercent / 100;
                $watchedSeconds += (int) round(($progressPercent / 100) * $durationSeconds);
                $lastPositionSeconds = max($lastPositionSeconds, (int) ($itemState['position_seconds'] ?? 0));
            } else {
                $completed = $this->toBool($itemState['completed'] ?? false) || (int) ($itemState['progress_percent'] ?? 0) >= 100;
                $completedUnits += $completed ? 1 : 0;
            }
        }

        return [
            'progress_percent' => (int) round(($completedUnits / $totalTrackableItems) * 100),
            'watched_seconds' => $watchedSeconds,
            'last_position_seconds' => $lastPositionSeconds,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function trackableBlockTypes(): array
    {
        return [
            'text',
            'image',
            'video',
            'file',
            'quiz',
            'callout',
            'code',
        ];
    }

    private function toBool(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
