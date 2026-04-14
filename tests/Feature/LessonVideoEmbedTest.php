<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonVideoEmbedTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_page_embeds_supported_primary_lesson_video_urls(): void
    {
        $user = User::factory()->create();

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Video Lesson',
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/shorts/dQw4w9WgXcQ?feature=share',
            'duration_minutes' => 10,
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        $this->actingAs($user)
            ->get(route('lessons.show', $lesson))
            ->assertOk()
            ->assertSee('https://www.youtube.com/embed/dQw4w9WgXcQ', false)
            ->assertSee('https://www.youtube.com/shorts/dQw4w9WgXcQ?feature=share', false)
            ->assertSee(__('lessons.open_video'));
    }

    public function test_show_page_embeds_supported_content_block_video_urls(): void
    {
        $user = User::factory()->create();

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Segment Video Lesson',
            'type' => 'text',
            'duration_minutes' => 12,
            'is_published' => true,
            'is_free' => true,
            'segments' => [
                [
                    'type' => 'content',
                    'blocks' => [
                        [
                            'type' => 'video',
                            'content' => 'https://youtu.be/dQw4w9WgXcQ?t=43',
                        ],
                    ],
                ],
            ],
        ]);

        $this->actingAs($user)
            ->get(route('lessons.show', $lesson))
            ->assertOk()
            ->assertSee('https://www.youtube.com/embed/dQw4w9WgXcQ?enablejsapi=1&amp;rel=0&amp;start=43', false)
            ->assertSee('https://youtu.be/dQw4w9WgXcQ?t=43', false)
            ->assertSee(__('lessons.open_video'));
    }
}
