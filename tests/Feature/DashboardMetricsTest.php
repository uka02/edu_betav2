<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_trackers_match_the_users_actual_lesson_metrics(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-03-20 10:00:00'));

        try {
            $user = User::factory()->learner()->create();

            $this->createLesson($user, [
                'title' => 'Learner Authored Published Lesson',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=DKpaKHUlyBY',
                'duration_minutes' => 30,
                'is_published' => true,
                'is_free' => true,
            ]);

            $this->createLesson($user, [
                'title' => 'Learner Authored Draft Lesson',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=DKpaKHUlyBY',
                'duration_minutes' => 40,
                'is_published' => false,
                'is_free' => false,
            ]);

            $this->createLesson($user, [
                'title' => 'Learner Authored Published Lesson Two',
                'duration_minutes' => 15,
                'is_published' => true,
                'is_free' => true,
            ]);

            $educator = User::factory()->educator()->create(['name' => 'Course Educator']);
            $issuer = User::factory()->educator()->create(['name' => 'Issuer User']);

            $completedLesson = $this->createLesson($educator, [
                'title' => 'Completed Lesson',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=DKpaKHUlyBY',
                'duration_minutes' => 30,
                'is_published' => true,
                'is_free' => true,
            ]);

            $inProgressLesson = $this->createLesson($educator, [
                'title' => 'In Progress Lesson',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=DKpaKHUlyBY',
                'duration_minutes' => 40,
                'is_published' => true,
                'is_free' => false,
            ]);

            $certifiedLesson = $this->createLesson($issuer, [
                'title' => 'Certified Lesson',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=DKpaKHUlyBY',
                'duration_minutes' => 60,
                'is_published' => true,
            ]);

            Certificate::create([
                'user_id' => $user->id,
                'lesson_id' => $certifiedLesson->id,
                'exam_index' => 0,
                'certificate_code' => 'CERT-DASH-0001',
                'issued_at' => now(),
                'snapshot' => [
                    'learner_name' => $user->name,
                    'lesson_title' => $certifiedLesson->title,
                    'issuer_name' => $issuer->name,
                    'score' => 92,
                ],
            ]);

            $this->createProgressRecord($user, $completedLesson, [
                'watched_seconds' => 1800,
                'progress_percent' => 100,
                'progress_state' => [
                    'items' => [
                        'main-video' => [
                            'kind' => 'video',
                            'progress_percent' => 100,
                            'position_seconds' => 1800,
                            'duration_seconds' => 1800,
                            'completed' => true,
                        ],
                    ],
                ],
                'last_viewed_at' => now(),
                'completed_at' => now(),
            ]);

            $this->createProgressRecord($user, $inProgressLesson, [
                'watched_seconds' => 1800,
                'progress_percent' => 75,
                'progress_state' => [
                    'items' => [
                        'main-video' => [
                            'kind' => 'video',
                            'progress_percent' => 75,
                            'position_seconds' => 1800,
                            'duration_seconds' => 2400,
                            'completed' => false,
                        ],
                    ],
                ],
                'last_viewed_at' => now()->subDay(),
            ]);

            $this->createProgressRecord($user, $certifiedLesson, [
                'watched_seconds' => 1800,
                'progress_percent' => 50,
                'progress_state' => [
                    'items' => [
                        'main-video' => [
                            'kind' => 'video',
                            'progress_percent' => 50,
                            'position_seconds' => 1800,
                            'duration_seconds' => 3600,
                            'completed' => false,
                        ],
                    ],
                ],
                'last_viewed_at' => now()->subDays(2),
            ]);

            $response = $this->actingAs($user)->get(route('dashboard'));

            $response->assertOk();
            $response->assertViewHas('publishedLessons', 3);
            $response->assertViewHas('totalLessonsCreated', 1);
            $response->assertViewHas('certificateCount', 1);
            $response->assertViewHas('progressPercentage', 75);
            $response->assertViewHas('dailyStreak', 3);
            $response->assertViewHas('totalLearningHours', 1.5);
            $response->assertViewHas('totalLearningHoursDisplay', '1.5');
            $response->assertSee('1.5<sup>' . __('dashboard.hours_short') . '</sup>', false);
            $response->assertSee(route('certificates.index'));
            $response->assertDontSee(__('dashboard.your_lessons'));
            $response->assertSee('name="lesson_search"', false);
        } finally {
            $this->travelBack();
        }
    }

    public function test_educator_dashboard_uses_teaching_performance_metrics(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-03-20 10:00:00'));

        try {
            $educator = User::factory()->educator()->create();
            $learnerOne = User::factory()->learner()->create();
            $learnerTwo = User::factory()->learner()->create();

            $publishedLesson = $this->createLesson($educator, [
                'title' => 'Security Foundations',
                'is_published' => true,
            ]);

            $publishedLessonTwo = $this->createLesson($educator, [
                'title' => 'Network Defense',
                'is_published' => true,
            ]);

            $this->createLesson($educator, [
                'title' => 'Private Draft',
                'is_published' => false,
            ]);

            $this->createProgressRecord($learnerOne, $publishedLesson, [
                'progress_percent' => 80,
                'last_viewed_at' => now()->subDays(2),
            ]);

            $this->createProgressRecord($learnerTwo, $publishedLessonTwo, [
                'progress_percent' => 40,
                'last_viewed_at' => now()->subDays(9),
            ]);

            Certificate::create([
                'user_id' => $learnerOne->id,
                'lesson_id' => $publishedLesson->id,
                'exam_index' => 0,
                'certificate_code' => 'CERT-EDU-0001',
                'issued_at' => now(),
                'snapshot' => [
                    'learner_name' => $learnerOne->name,
                    'lesson_title' => $publishedLesson->title,
                    'issuer_name' => $educator->name,
                    'score' => 95,
                ],
            ]);

            $response = $this->actingAs($educator)->get(route('dashboard'));

            $response->assertOk();
            $response->assertViewHas('isEducatorDashboard', true);
            $response->assertViewHas('publishedLessons', 2);
            $response->assertViewHas('totalLessonsCreated', 3);
            $response->assertViewHas('issuedCertificatesCount', 1);
            $response->assertViewHas('learnersReachedCount', 2);
            $response->assertViewHas('activeLearnersCount', 1);
            $response->assertViewHas('progressPercentage', 60);
            $response->assertSee(__('dashboard.educator_dashboard'));
            $response->assertSee(__('dashboard.your_lessons'));
            $response->assertSee(__('dashboard.recent_learner_activity'));
        } finally {
            $this->travelBack();
        }
    }

    public function test_dashboard_streak_counts_consecutive_days_from_yesterday_when_today_is_inactive(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-03-20 10:00:00'));

        try {
            $user = User::factory()->create();

            $yesterdayMorning = $this->createLesson($user, [
                'title' => 'Yesterday Morning',
                'is_published' => true,
            ]);

            $yesterdayEvening = $this->createLesson($user, [
                'title' => 'Yesterday Evening',
                'is_published' => true,
            ]);

            $twoDaysAgo = $this->createLesson($user, [
                'title' => 'Two Days Ago',
                'is_published' => true,
            ]);

            $threeDaysAgo = $this->createLesson($user, [
                'title' => 'Three Days Ago',
                'is_published' => true,
            ]);

            $this->createProgressRecord($user, $yesterdayMorning, [
                'last_viewed_at' => now()->subDay()->setTime(9, 0),
            ]);
            $this->createProgressRecord($user, $yesterdayEvening, [
                'last_viewed_at' => now()->subDay()->setTime(18, 0),
            ]);
            $this->createProgressRecord($user, $twoDaysAgo, [
                'last_viewed_at' => now()->subDays(2)->setTime(14, 0),
            ]);
            $this->createProgressRecord($user, $threeDaysAgo, [
                'last_viewed_at' => now()->subDays(3)->setTime(11, 0),
            ]);

            $response = $this->actingAs($user)->get(route('dashboard'));

            $response->assertOk();
            $response->assertViewHas('dailyStreak', 3);
        } finally {
            $this->travelBack();
        }
    }

    public function test_dashboard_lesson_search_returns_matching_published_lessons_from_all_creators(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create(['name' => 'Laravel Mentor']);

        $matchingOwnLesson = $this->createLesson($user, [
            'title' => 'Laravel Routing Mastery',
            'type' => 'video',
            'difficulty' => 'beginner',
            'is_published' => true,
        ]);

        $matchingCommunityLesson = $this->createLesson($otherUser, [
            'title' => 'Laravel Testing Patterns',
            'type' => 'text',
            'difficulty' => 'intermediate',
            'is_published' => true,
        ]);

        $this->createLesson($user, [
            'title' => 'Advanced CSS Layouts',
            'type' => 'text',
            'difficulty' => 'advanced',
            'is_published' => true,
        ]);

        $this->createLesson($otherUser, [
            'title' => 'Laravel Draft Lesson',
            'type' => 'document',
            'difficulty' => 'intermediate',
            'is_published' => false,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', [
            'lesson_search' => 'Laravel',
        ]));

        $response->assertOk();
        $response->assertViewHas('lessonSearch', 'Laravel');
        $response->assertViewHas('isGlobalLessonSearch', true);
        $response->assertViewHas('featuredLessons', function ($lessons) use ($matchingOwnLesson, $matchingCommunityLesson) {
            $lessonIds = $lessons->pluck('id')->all();

            return $lessons->count() === 2
                && in_array($matchingOwnLesson->id, $lessonIds, true)
                && in_array($matchingCommunityLesson->id, $lessonIds, true);
        });
        $response->assertDontSee(__('dashboard.your_lessons'));
        $response->assertSee(__('dashboard.search_results_for', ['term' => 'Laravel']));
        $response->assertSee('Laravel Routing Mastery');
        $response->assertSee('Laravel Testing Patterns');
        $response->assertSee('Laravel Mentor');
        $response->assertDontSee('Advanced CSS Layouts');
        $response->assertDontSee('Laravel Draft Lesson');
    }

    private function createLesson(User $user, array $attributes = []): Lesson
    {
        $createdAt = $attributes['created_at'] ?? null;
        unset($attributes['created_at']);

        $lesson = Lesson::create(array_merge([
            'user_id' => $user->id,
            'title' => 'Test Lesson ' . str()->random(8),
            'type' => 'text',
            'duration_minutes' => 0,
            'is_published' => false,
            'is_free' => false,
            'segments' => [],
        ], $attributes));

        if ($createdAt) {
            $lesson->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])->save();
        }

        return $lesson;
    }

    private function createProgressRecord(User $user, Lesson $lesson, array $attributes = []): LessonProgress
    {
        $lastViewedAt = $attributes['last_viewed_at'] ?? now();

        return LessonProgress::create(array_merge([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'watched_seconds' => 0,
            'last_position_seconds' => 0,
            'progress_percent' => 0,
            'progress_state' => ['items' => []],
            'last_viewed_at' => $lastViewedAt,
            'completed_at' => null,
        ], $attributes));
    }
}
