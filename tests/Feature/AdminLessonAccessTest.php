<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\LessonReport;
use App\Models\User;
use App\Notifications\LessonDeletedByAdminNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLessonAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_and_manage_lessons_from_other_users(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Platform Admin']);
        $educator = User::factory()->educator()->create(['name' => 'Lesson Owner']);
        $otherEducator = User::factory()->educator()->create(['name' => 'Second Owner']);

        $draftLesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Owner Draft Lesson',
            'type' => 'text',
            'duration_minutes' => 20,
            'is_published' => false,
            'is_free' => true,
            'segments' => [
                [
                    'id' => 1,
                    'type' => 'content',
                    'blocks' => [
                        [
                            'id' => 1,
                            'type' => 'text',
                            'content' => 'Draft content that is complete enough to publish.',
                        ],
                    ],
                ],
            ],
        ]);

        $publishedLesson = Lesson::create([
            'user_id' => $otherEducator->id,
            'title' => 'Second Owner Published Lesson',
            'type' => 'text',
            'duration_minutes' => 15,
            'is_published' => true,
            'is_free' => false,
            'segments' => [],
        ]);

        $this->actingAs($admin)
            ->get(route('lessons.index'))
            ->assertOk()
            ->assertSee('Owner Draft Lesson')
            ->assertSee('Second Owner Published Lesson')
            ->assertSee('Lesson Owner')
            ->assertSee('Second Owner')
            ->assertDontSee(route('lessons.create'), false);

        $this->actingAs($admin)
            ->get(route('lessons.show', $draftLesson))
            ->assertOk()
            ->assertSee(route('lessons.edit', $draftLesson), false)
            ->assertSee(route('lessons.publish', $draftLesson), false);

        $this->actingAs($admin)
            ->get(route('lessons.edit', $draftLesson))
            ->assertOk();

        $this->actingAs($admin)
            ->put(route('lessons.update', $draftLesson), [
                'title' => 'Admin Updated Draft Lesson',
                'type' => 'text',
                'subject' => Lesson::defaultSubject(),
                'duration_hours' => 0,
                'duration_minutes' => 20,
                'segments' => [
                    1 => [
                        'custom_name' => 'Admin Reviewed Segment',
                        'blocks' => [
                            1 => [
                                'type' => 'text',
                                'content' => 'Draft content that is complete enough to publish.',
                            ],
                        ],
                    ],
                ],
                'save_action' => 'draft',
            ])
            ->assertRedirect(route('lessons.edit', $draftLesson));

        $draftLesson->refresh();
        $this->assertSame('Admin Updated Draft Lesson', $draftLesson->title);
        $this->assertSame($educator->id, $draftLesson->user_id);

        $this->actingAs($admin)
            ->from(route('lessons.show', $draftLesson))
            ->post(route('lessons.publish', $draftLesson))
            ->assertRedirect(route('lessons.show', $draftLesson));

        $draftLesson->refresh();
        $this->assertTrue($draftLesson->is_published);
    }

    public function test_admin_can_see_report_counts_and_send_deletion_reason_to_educator(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Platform Admin']);
        $educator = User::factory()->educator()->create(['name' => 'Lesson Owner']);
        $reportingLearnerOne = User::factory()->learner()->create();
        $reportingLearnerTwo = User::factory()->learner()->create();

        $lesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Reported Lesson',
            'type' => 'text',
            'duration_minutes' => 20,
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        LessonReport::create([
            'lesson_id' => $lesson->id,
            'user_id' => $reportingLearnerOne->id,
            'reason' => LessonReport::REASON_INACCURATE,
            'details' => 'Several facts are outdated.',
        ]);

        LessonReport::create([
            'lesson_id' => $lesson->id,
            'user_id' => $reportingLearnerTwo->id,
            'reason' => LessonReport::REASON_BROKEN,
            'details' => 'A document link is broken.',
        ]);

        $this->actingAs($admin)
            ->get(route('lessons.index'))
            ->assertOk()
            ->assertSee(__('lessons.report_count') . ': 2')
            ->assertSee('Reported Lesson');

        $this->actingAs($admin)
            ->from(route('lessons.show', $lesson))
            ->delete(route('lessons.destroy', $lesson), [
                'deletion_reason' => 'Removed after multiple learner reports confirmed broken and inaccurate content.',
            ])
            ->assertRedirect(route('lessons.index'))
            ->assertSessionHas('success', __('lessons.lesson_deleted_with_notice'));

        $this->assertSoftDeleted('lessons', [
            'id' => $lesson->id,
        ]);

        $notice = $educator->fresh()->notifications()->latest()->first();

        $this->assertNotNull($notice);
        $this->assertSame(LessonDeletedByAdminNotification::class, $notice->type);
        $this->assertSame('Reported Lesson', $notice->data['lesson_title']);
        $this->assertSame('Platform Admin', $notice->data['admin_name']);
        $this->assertSame(
            'Removed after multiple learner reports confirmed broken and inaccurate content.',
            $notice->data['reason']
        );

        $this->actingAs($educator)
            ->get(route('lessons.index'))
            ->assertOk()
            ->assertSee(__('lessons.recent_admin_notices'))
            ->assertSee('Reported Lesson')
            ->assertSee('Platform Admin')
            ->assertSee('Removed after multiple learner reports confirmed broken and inaccurate content.');
    }
}
