<?php

use App\Http\Controllers\Api\LessonApiController;
use Illuminate\Support\Facades\Route;

// All API routes require authentication
Route::middleware('auth')->group(function () {
    // Lesson read endpoints (GET) - generous rate limit
    Route::middleware('throttle:lesson-read')->group(function () {
        Route::get('lessons', [LessonApiController::class, 'index'])->name('api.lessons.index');
        Route::get('lessons/{lesson}', [LessonApiController::class, 'show'])->name('api.lessons.show');
    });

    Route::middleware('educator')->group(function () {
        // Lesson creation endpoint - strict rate limit (5 per minute)
        Route::middleware('throttle:lesson-create')->post('lessons', [LessonApiController::class, 'store'])->name('api.lessons.store');

        // Lesson update endpoint - moderate rate limit (10 per minute)
        Route::middleware('throttle:lesson-update')->put('lessons/{lesson}', [LessonApiController::class, 'update'])->name('api.lessons.update');

        // Lesson delete endpoint - strict rate limit (same as creation)
        Route::middleware('throttle:lesson-create')->delete('lessons/{lesson}', [LessonApiController::class, 'destroy'])->name('api.lessons.destroy');

        // Lesson restore endpoint - restore soft-deleted lesson
        Route::middleware('throttle:lesson-create')->post('lessons/{id}/restore', [LessonApiController::class, 'restore'])->name('api.lessons.restore');

        // Lesson permanent delete endpoint - force delete with file cleanup
        Route::middleware('throttle:lesson-create')->delete('lessons/{id}/force', [LessonApiController::class, 'forceDelete'])->name('api.lessons.force-delete');
    });
});

