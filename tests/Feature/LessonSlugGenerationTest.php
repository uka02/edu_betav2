<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonSlugGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_recreating_a_soft_deleted_lesson_generates_a_new_slug(): void
    {
        $user = User::factory()->create();

        $deletedLesson = Lesson::create([
            'user_id' => $user->id,
            'title' => '123123',
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=DKpaKHUlyBY',
            'duration_minutes' => 60,
            'difficulty' => 'beginner',
            'is_published' => true,
            'is_free' => true,
            'segments' => [
                [
                    'id' => 1,
                    'custom_name' => 'main content',
                    'type' => 'content',
                    'blocks' => [],
                ],
            ],
        ]);

        $deletedLesson->delete();

        $replacementLesson = Lesson::create([
            'user_id' => $user->id,
            'title' => '123123',
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=DKpaKHUlyBY',
            'duration_minutes' => 60,
            'difficulty' => 'beginner',
            'is_published' => true,
            'is_free' => true,
            'segments' => [
                [
                    'id' => 1,
                    'custom_name' => 'main content',
                    'type' => 'content',
                    'blocks' => [],
                ],
            ],
        ]);

        $this->assertSame('123123', $deletedLesson->slug);
        $this->assertSame('123123-1', $replacementLesson->slug);
        $this->assertDatabaseHas('lessons', [
            'id' => $replacementLesson->id,
            'slug' => '123123-1',
        ]);
    }
}
