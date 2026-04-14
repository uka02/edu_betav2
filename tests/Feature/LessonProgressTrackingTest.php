<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonProgressTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_progress_endpoint_records_hybrid_progress_for_video_and_block_items(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $lesson = $this->makeHybridLesson($owner);

        $response = $this
            ->actingAs($user)
            ->postJson(route('lessons.progress', $lesson), [
                'items' => [
                    [
                        'key' => 'main-video',
                        'kind' => 'video',
                        'progress_percent' => 50,
                        'position_seconds' => 120,
                        'duration_seconds' => 240,
                    ],
                    [
                        'key' => 'segment-1-block-1',
                        'kind' => 'block',
                        'completed' => true,
                    ],
                ],
            ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'progress_percent' => 50,
                'watched_seconds' => 120,
                'last_position_seconds' => 120,
                'completed' => false,
            ]);

        $progress = LessonProgress::firstWhere([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $this->assertNotNull($progress);
        $this->assertSame(50, (int) $progress->progress_percent);
        $this->assertSame(120, (int) $progress->watched_seconds);
        $this->assertSame(120, (int) $progress->last_position_seconds);
        $this->assertTrue((bool) data_get($progress->progress_state, 'items.segment-1-block-1.completed'));
        $this->assertSame(50, (int) data_get($progress->progress_state, 'items.main-video.progress_percent'));
    }

    public function test_progress_endpoint_marks_lesson_complete_when_all_trackable_items_are_completed(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $lesson = $this->makeHybridLesson($owner);

        $response = $this
            ->actingAs($user)
            ->postJson(route('lessons.progress', $lesson), [
                'items' => [
                    [
                        'key' => 'main-video',
                        'kind' => 'video',
                        'progress_percent' => 95,
                        'position_seconds' => 228,
                        'duration_seconds' => 240,
                    ],
                    [
                        'key' => 'segment-1-block-1',
                        'kind' => 'block',
                        'completed' => true,
                    ],
                    [
                        'key' => 'segment-1-block-2',
                        'kind' => 'block',
                        'completed' => true,
                    ],
                ],
            ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'progress_percent' => 100,
                'completed' => true,
            ]);

        $progress = LessonProgress::firstWhere([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $this->assertNotNull($progress);
        $this->assertSame(100, (int) $progress->progress_percent);
        $this->assertNotNull($progress->completed_at);
        $this->assertSame(100, (int) data_get($progress->progress_state, 'items.main-video.progress_percent'));
        $this->assertTrue((bool) data_get($progress->progress_state, 'items.main-video.completed'));
    }

    public function test_progress_endpoint_blocks_non_owners_from_unpublished_lessons(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $lesson = $this->makeHybridLesson($owner, false);

        $this->actingAs($user)
            ->postJson(route('lessons.progress', $lesson), [
                'items' => [
                    [
                        'key' => 'segment-1-block-1',
                        'kind' => 'block',
                        'completed' => true,
                    ],
                ],
            ])
            ->assertForbidden();
    }

    public function test_dashboard_continue_learning_uses_saved_hybrid_progress_records(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $lesson = $this->makeHybridLesson($owner);

        LessonProgress::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'watched_seconds' => 180,
            'last_position_seconds' => 180,
            'progress_percent' => 67,
            'progress_state' => [
                'items' => [
                    'main-video' => [
                        'kind' => 'video',
                        'progress_percent' => 100,
                        'position_seconds' => 240,
                        'duration_seconds' => 240,
                        'completed' => true,
                    ],
                    'segment-1-block-1' => [
                        'kind' => 'block',
                        'progress_percent' => 100,
                        'completed' => true,
                    ],
                ],
            ],
            'last_viewed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('continueLearningLessons', function ($lessons) use ($lesson) {
            return $lessons->count() === 1
                && $lessons->first()?->is($lesson)
                && (int) $lessons->first()?->progress_percent === 67;
        });
        $response->assertSee('Progress Lesson');
        $response->assertSee('width:67%;', false);
    }

    public function test_show_and_dashboard_resync_saved_progress_when_lesson_structure_changes(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $lesson = Lesson::create([
            'user_id' => $owner->id,
            'title' => 'Resynced Progress Lesson',
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=DKpaKHUlyBY',
            'duration_minutes' => 4,
            'difficulty' => 'beginner',
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        LessonProgress::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'watched_seconds' => 240,
            'last_position_seconds' => 240,
            'progress_percent' => 100,
            'progress_state' => [
                'items' => [
                    'main-video' => [
                        'kind' => 'video',
                        'progress_percent' => 100,
                        'position_seconds' => 240,
                        'duration_seconds' => 240,
                        'completed' => true,
                    ],
                ],
            ],
            'last_viewed_at' => now(),
            'completed_at' => now(),
        ]);

        $lesson->update([
            'segments' => [
                [
                    'id' => 1,
                    'type' => 'content',
                    'custom_name' => 'New Segment',
                    'blocks' => [
                        [
                            'id' => 1,
                            'type' => 'text',
                            'content' => 'Fresh block',
                        ],
                    ],
                ],
            ],
        ]);

        $this->actingAs($user)
            ->get(route('lessons.show', $lesson))
            ->assertOk()
            ->assertViewHas('lessonProgress', function ($progress) use ($lesson) {
                return $progress !== null
                    && (int) $progress->lesson_id === $lesson->id
                    && (int) $progress->progress_percent === 50;
            });

        $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));

        $dashboardResponse->assertOk();
        $dashboardResponse->assertViewHas('continueLearningLessons', function ($lessons) use ($lesson) {
            return $lessons->count() === 1
                && $lessons->first()?->is($lesson)
                && (int) $lessons->first()?->progress_percent === 50;
        });
        $dashboardResponse->assertSee('width:50%;', false);

        $progress = LessonProgress::firstWhere([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $this->assertNotNull($progress);
        $this->assertSame(50, (int) $progress->progress_percent);
        $this->assertNull($progress->completed_at);
    }

    private function makeHybridLesson(User $owner, bool $isPublished = true): Lesson
    {
        return Lesson::create([
            'user_id' => $owner->id,
            'title' => 'Progress Lesson',
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=DKpaKHUlyBY',
            'duration_minutes' => 4,
            'difficulty' => 'beginner',
            'is_published' => $isPublished,
            'is_free' => true,
            'segments' => [
                [
                    'id' => 1,
                    'type' => 'content',
                    'custom_name' => 'Main Content',
                    'blocks' => [
                        [
                            'id' => 1,
                            'type' => 'text',
                            'content' => 'Intro block',
                        ],
                        [
                            'id' => 2,
                            'type' => 'quiz',
                            'question' => 'What is 2 + 2?',
                            'answers' => ['4', '5'],
                            'correct_answer' => 0,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
