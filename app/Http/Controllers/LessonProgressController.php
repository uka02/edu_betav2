<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    public function store(Request $request, Lesson $lesson, LessonProgressService $lessonProgressService)
    {
        if ($lesson->user_id !== $request->user()->id && ! $lesson->is_published) {
            abort(403, __('lessons.unauthorized'));
        }

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:25'],
            'items.*.key' => ['required', 'string', 'max:120'],
            'items.*.kind' => ['required', 'in:block,video'],
            'items.*.progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'items.*.position_seconds' => ['nullable', 'integer', 'min:0'],
            'items.*.duration_seconds' => ['nullable', 'integer', 'min:1'],
            'items.*.completed' => ['nullable', 'boolean'],
        ]);

        $progress = $lessonProgressService->recordBatch(
            $request->user(),
            $lesson,
            $validated['items'],
        );

        return response()->json([
            'success' => true,
            'progress_percent' => $progress->progress_percent,
            'watched_seconds' => $progress->watched_seconds,
            'last_position_seconds' => $progress->last_position_seconds,
            'completed' => $progress->completed_at !== null,
            'progress_state' => $progress->progress_state,
        ]);
    }
}
