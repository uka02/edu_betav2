<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonTitleCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_title_check_route_points_to_the_real_endpoint_and_is_scoped_to_the_current_user(): void
    {
        $user = User::factory()->educator()->create();
        $otherUser = User::factory()->educator()->create();

        Lesson::create([
            'user_id' => $user->id,
            'title' => 'My Lesson',
            'type' => 'text',
            'duration_minutes' => 10,
            'is_published' => false,
            'is_free' => false,
        ]);

        Lesson::create([
            'user_id' => $otherUser->id,
            'title' => 'Shared Title',
            'type' => 'text',
            'duration_minutes' => 10,
            'is_published' => false,
            'is_free' => false,
        ]);

        $this->assertSame('http://localhost/lessons/check-title', route('lessons.check-title'));

        $ownResponse = $this
            ->actingAs($user)
            ->postJson(route('lessons.check-title'), ['title' => 'My Lesson']);

        $otherUserResponse = $this
            ->actingAs($user)
            ->postJson(route('lessons.check-title'), ['title' => 'Shared Title']);

        $ownResponse->assertOk()->assertJson(['exists' => true]);
        $otherUserResponse->assertOk()->assertJson(['exists' => false]);
    }
}
