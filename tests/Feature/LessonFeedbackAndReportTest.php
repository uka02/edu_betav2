<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\LessonFeedback;
use App\Models\LessonReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LessonFeedbackAndReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_learner_can_submit_and_update_feedback_and_report_for_published_lesson(): void
    {
        $educator = User::factory()->educator()->create(['name' => 'Educator Owner']);
        $learner = User::factory()->learner()->create(['name' => 'Curious Learner']);

        $lesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Published Security Lesson',
            'type' => 'text',
            'content' => 'Lesson body',
            'duration_minutes' => 25,
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        $this->actingAs($learner)
            ->post(route('lessons.feedback.store', $lesson), [
                'rating' => 5,
                'positive_feedback' => 'Very clear structure and useful examples.',
                'negative_feedback' => 'A few sections could use more real-world examples.',
            ])
            ->assertRedirect(route('lessons.show', $lesson));

        $this->actingAs($learner)
            ->post(route('lessons.report.store', $lesson), [
                'reason' => LessonReport::REASON_BROKEN,
                'details' => 'One resource link near the end looks outdated.',
            ])
            ->assertRedirect(route('lessons.show', $lesson));

        $this->assertDatabaseHas('lesson_feedback', [
            'lesson_id' => $lesson->id,
            'user_id' => $learner->id,
            'rating' => 5,
            'positive_feedback' => 'Very clear structure and useful examples.',
            'negative_feedback' => 'A few sections could use more real-world examples.',
        ]);

        $this->assertDatabaseHas('lesson_reports', [
            'lesson_id' => $lesson->id,
            'user_id' => $learner->id,
            'reason' => LessonReport::REASON_BROKEN,
            'details' => 'One resource link near the end looks outdated.',
        ]);

        $this->actingAs($learner)
            ->post(route('lessons.feedback.store', $lesson), [
                'rating' => 4,
                'positive_feedback' => 'The overall flow still feels clear and approachable.',
                'negative_feedback' => 'One section could be shorter.',
            ])
            ->assertRedirect(route('lessons.show', $lesson));

        $this->assertSame(1, LessonFeedback::count());
        $this->assertSame(4, (int) LessonFeedback::firstOrFail()->rating);

        $this->actingAs($educator)
            ->get(route('lessons.show', $lesson))
            ->assertOk()
            ->assertSee(__('lessons.received_feedback'))
            ->assertSee('Curious Learner')
            ->assertSee(__('lessons.positive_feedback'))
            ->assertSee('The overall flow still feels clear and approachable.')
            ->assertSee(__('lessons.negative_feedback'))
            ->assertSee('One section could be shorter.')
            ->assertSee(__('lessons.received_reports'))
            ->assertSee(__('lessons.report_reason_broken'))
            ->assertSee('One resource link near the end looks outdated.');
    }

    public function test_lesson_owner_and_draft_lessons_cannot_receive_feedback_or_reports(): void
    {
        $educator = User::factory()->educator()->create();
        $learner = User::factory()->learner()->create();

        $publishedLesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Owner Lesson',
            'type' => 'text',
            'content' => 'Body',
            'duration_minutes' => 15,
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        $draftLesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Draft Lesson',
            'type' => 'text',
            'content' => 'Draft body',
            'duration_minutes' => 15,
            'is_published' => false,
            'is_free' => true,
            'segments' => [],
        ]);

        $this->actingAs($educator)
            ->post(route('lessons.feedback.store', $publishedLesson), [
                'rating' => 5,
            ])
            ->assertForbidden();

        $this->actingAs($educator)
            ->post(route('lessons.report.store', $publishedLesson), [
                'reason' => LessonReport::REASON_OTHER,
                'details' => 'Owner should not be able to report this.',
            ])
            ->assertForbidden();

        $this->actingAs($learner)
            ->post(route('lessons.feedback.store', $draftLesson), [
                'rating' => 5,
            ])
            ->assertForbidden();

        $this->actingAs($learner)
            ->post(route('lessons.report.store', $draftLesson), [
                'reason' => LessonReport::REASON_OTHER,
                'details' => 'Draft lessons should not receive reports.',
            ])
            ->assertForbidden();

        $this->assertSame(0, LessonFeedback::count());
        $this->assertSame(0, LessonReport::count());
    }

    public function test_guest_viewers_see_prompt_to_sign_in_before_sharing_feedback(): void
    {
        $educator = User::factory()->educator()->create();

        $lesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Guest Feedback Prompt Lesson',
            'type' => 'text',
            'content' => 'Body',
            'duration_minutes' => 10,
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        $this->get(route('lessons.show', $lesson))
            ->assertOk()
            ->assertSee(__('lessons.sign_in_to_share_feedback'))
            ->assertSee(route('login'), false)
            ->assertSee(route('signup'), false);
    }

    public function test_lesson_show_page_handles_missing_engagement_tables_gracefully(): void
    {
        $educator = User::factory()->educator()->create();

        $lesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Lesson Without Engagement Tables',
            'type' => 'text',
            'content' => 'Body',
            'duration_minutes' => 10,
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        Schema::dropIfExists('lesson_reports');
        Schema::dropIfExists('lesson_feedback');

        $this->actingAs($educator)
            ->get(route('lessons.show', $lesson))
            ->assertOk()
            ->assertSee(__('lessons.lesson_engagement_unavailable'));
    }

    public function test_feedback_submission_redirects_with_error_when_engagement_tables_are_missing(): void
    {
        $educator = User::factory()->educator()->create();
        $learner = User::factory()->learner()->create();

        $lesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Missing Engagement Tables Lesson',
            'type' => 'text',
            'content' => 'Body',
            'duration_minutes' => 10,
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        Schema::dropIfExists('lesson_reports');
        Schema::dropIfExists('lesson_feedback');

        $this->actingAs($learner)
            ->from(route('lessons.show', $lesson))
            ->post(route('lessons.feedback.store', $lesson), [
                'rating' => 5,
                'positive_feedback' => 'Helpful lesson.',
                'negative_feedback' => 'Needs one more worked example.',
            ])
            ->assertRedirect(route('lessons.show', $lesson))
            ->assertSessionHas('error', __('lessons.lesson_engagement_unavailable'));
    }
}
