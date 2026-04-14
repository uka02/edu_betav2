<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Lesson;
use App\Models\LessonExamAttempt;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_home_page_displays_live_platform_metrics(): void
    {
        $owner = User::factory()->create();
        $learner = User::factory()->create();
        $mentor = User::factory()->create();

        $publishedLesson = Lesson::create([
            'user_id' => $owner->id,
            'title' => 'Intro to Cybersecurity',
            'type' => 'text',
            'content' => 'Cyber basics',
            'duration_minutes' => 30,
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        $publishedLessonTwo = Lesson::create([
            'user_id' => $mentor->id,
            'title' => 'Python for Security',
            'type' => 'text',
            'content' => 'Python basics',
            'duration_minutes' => 45,
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        Lesson::create([
            'user_id' => $owner->id,
            'title' => 'Draft Networking Notes',
            'type' => 'text',
            'content' => 'Not yet published',
            'duration_minutes' => 20,
            'is_published' => false,
            'is_free' => true,
            'segments' => [],
        ]);

        LessonProgress::create([
            'user_id' => $learner->id,
            'lesson_id' => $publishedLesson->id,
            'watched_seconds' => 1800,
            'last_position_seconds' => 1800,
            'progress_percent' => 100,
            'last_viewed_at' => now(),
            'completed_at' => now(),
        ]);

        LessonProgress::create([
            'user_id' => $mentor->id,
            'lesson_id' => $publishedLessonTwo->id,
            'watched_seconds' => 600,
            'last_position_seconds' => 600,
            'progress_percent' => 35,
            'last_viewed_at' => now(),
        ]);

        LessonExamAttempt::create([
            'user_id' => $learner->id,
            'lesson_id' => $publishedLesson->id,
            'exam_index' => 0,
            'score' => 91,
            'passed' => true,
            'correct_count' => 9,
            'total_questions' => 10,
            'time_taken' => 420,
            'answers' => ['A'],
            'results' => ['status' => 'passed'],
            'attempted_at' => now(),
        ]);

        LessonExamAttempt::create([
            'user_id' => $mentor->id,
            'lesson_id' => $publishedLessonTwo->id,
            'exam_index' => 0,
            'score' => 42,
            'passed' => false,
            'correct_count' => 4,
            'total_questions' => 10,
            'time_taken' => 510,
            'answers' => ['B'],
            'results' => ['status' => 'failed'],
            'attempted_at' => now(),
        ]);

        Certificate::create([
            'user_id' => $learner->id,
            'lesson_id' => $publishedLesson->id,
            'exam_index' => 0,
            'certificate_code' => 'HOME-CERT-0001',
            'issued_at' => now(),
            'snapshot' => [
                'learner_name' => $learner->name,
                'lesson_title' => $publishedLesson->title,
                'issuer_name' => $owner->name,
                'score' => 91,
            ],
        ]);

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee(__('home.live_metrics'))
            ->assertSee('data-metric="members"', false)
            ->assertSee('data-metric="courses"', false)
            ->assertSee('data-metric="certificates"', false)
            ->assertSee('data-metric="satisfaction"', false)
            ->assertSee('>3<', false)
            ->assertSee('>2<', false)
            ->assertSee('>1<', false)
            ->assertSee('>50%<', false);
    }

    public function test_authenticated_users_are_redirected_from_home_to_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertRedirect(route('dashboard'));
    }
}
