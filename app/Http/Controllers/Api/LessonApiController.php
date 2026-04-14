<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lessons\StoreLessonRequest;
use App\Http\Requests\Lessons\UpdateLessonRequest;
use App\Models\Lesson;
use App\Services\LessonManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LessonApiController extends Controller
{
    public function __construct(private readonly LessonManager $lessonManager)
    {
    }

    /**
     * List all lessons for the authenticated user.
     * Supports pagination and filtering.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->isEducator()
            ? Lesson::query()->where('user_id', Auth::id())
            : Lesson::query()->published()->with('user');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('subject')) {
            $query->bySubject($request->input('subject'));
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->input('difficulty'));
        }

        // Filter by published status
        if ($request->has('published')) {
            $published = filter_var($request->input('published'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_published', $published);
        }

        // Search by title or slug
        if ($request->has('q')) {
            $searchTerm = '%' . $request->input('q') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('slug', 'like', $searchTerm)
                    ->orWhere('subject', 'like', $searchTerm);

                if ($user->isLearner()) {
                    $q->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('name', 'like', $searchTerm);
                    });
                }
            });
        }

        $lessons = $query->latest()->paginate(
            $request->input('per_page', 12)
        );

        return response()->json([
            'success' => true,
            'data' => $lessons->items(),
            'meta' => [
                'total' => $lessons->total(),
                'per_page' => $lessons->perPage(),
                'current_page' => $lessons->currentPage(),
                'last_page' => $lessons->lastPage(),
            ],
        ]);
    }

    /**
     * Get a single lesson by ID or slug.
     */
    public function show($id)
    {
        // Try to fetch by ID first, then by slug
        $lesson = Lesson::where('id', $id)
            ->orWhere('slug', $id)
            ->first();

        if (! $lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Lesson not found',
            ], Response::HTTP_NOT_FOUND);
        }

        // Authorization: user owns it or it's published
        if ($lesson->user_id !== Auth::id() && ! $lesson->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'success' => true,
            'data' => $lesson,
        ]);
    }

    /**
     * Create a new lesson.
     */
    public function store(StoreLessonRequest $request)
    {
        $lesson = $this->lessonManager->create($request, Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Lesson created successfully',
            'data' => $lesson,
        ], Response::HTTP_CREATED);
    }

    /**
     * Update an existing lesson.
     */
    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        if ($lesson->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $lesson = $this->lessonManager->update($request, $lesson);

        return response()->json([
            'success' => true,
            'message' => 'Lesson updated successfully',
            'data' => $lesson,
        ]);
    }

    /**
     * Delete a lesson (soft delete).
     */
    public function destroy(Lesson $lesson)
    {
        if ($lesson->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        // Soft delete - no file cleanup since can be restored
        $lesson->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lesson deleted successfully',
        ]);
    }

    /**
     * Permanently delete a lesson and all associated files.
     * Bypasses soft delete.
     */
    public function forceDelete($id)
    {
        $lesson = Lesson::withTrashed()->findOrFail($id);

        if ($lesson->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $this->lessonManager->deleteFiles($lesson);

        // Permanently delete from database
        $lesson->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Lesson permanently deleted successfully',
        ]);
    }

    /**
     * Restore a soft-deleted lesson.
     */
    public function restore($id)
    {
        $lesson = Lesson::withTrashed()->findOrFail($id);

        if ($lesson->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $lesson->restore();

        return response()->json([
            'success' => true,
            'message' => 'Lesson restored successfully',
            'data' => $lesson,
        ]);
    }
}
