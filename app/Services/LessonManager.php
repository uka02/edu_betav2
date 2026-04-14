<?php

namespace App\Services;

use App\Models\Lesson;
use App\Traits\HandleLessonSegments;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class LessonManager
{
    use HandleLessonSegments;

    public function create(FormRequest $request, int $userId): Lesson
    {
        return Lesson::create($this->buildPayload($request, null, $userId));
    }

    public function update(FormRequest $request, Lesson $lesson): Lesson
    {
        $lesson->update($this->buildPayload($request, $lesson));

        return $lesson->refresh();
    }

    public function deleteFiles(Lesson $lesson): void
    {
        if ($lesson->thumbnail) {
            Storage::disk('public')->delete($lesson->thumbnail);
        }

        if ($lesson->document_path) {
            Storage::disk('public')->delete($lesson->document_path);
        }

        foreach ($lesson->segments ?? [] as $segment) {
            if (! isset($segment['blocks']) || ! is_array($segment['blocks'])) {
                continue;
            }

            foreach ($segment['blocks'] as $block) {
                if (
                    isset($block['path']) &&
                    in_array($block['type'], ['image', 'file'], true)
                ) {
                    Storage::disk('public')->delete($block['path']);
                }
            }
        }
    }

    private function buildPayload(FormRequest $request, ?Lesson $lesson = null, ?int $userId = null): array
    {
        $validated = $request->validated();

        unset($validated['thumbnail'], $validated['document'], $validated['duration_hours']);

        if ($userId !== null) {
            $validated['user_id'] = $userId;
        }

        $validated['subject'] = Lesson::normalizeSubject($validated['subject'] ?? $lesson?->subject ?? Lesson::defaultSubject());
        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_free'] = $request->boolean('is_free');
        $validated['duration_minutes'] = ((int) $request->input('duration_hours', 0) * 60)
            + (int) $request->input('duration_minutes', 0);

        if ($request->input('type') !== 'video') {
            $validated['video_url'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($lesson?->thumbnail) {
                Storage::disk('public')->delete($lesson->thumbnail);
            }

            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if ($request->hasFile('document')) {
            if ($lesson?->document_path) {
                Storage::disk('public')->delete($lesson->document_path);
            }

            $validated['document_path'] = $request->file('document')->store('documents', 'public');
        } elseif ($request->input('type') !== 'document') {
            if ($lesson?->document_path) {
                Storage::disk('public')->delete($lesson->document_path);
            }

            $validated['document_path'] = null;
        }

        if ($lesson) {
            $this->cleanupUnusedFiles($lesson->segments ?? [], $request->input('segments', []));
        }

        $validated['segments'] = $this->processSegments($request);

        return $validated;
    }
}
