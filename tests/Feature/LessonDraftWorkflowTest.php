<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonDraftWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_draft_creates_an_unpublished_lesson_and_redirects_to_edit(): void
    {
        $user = User::factory()->educator()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('lessons.store'), [
                'title' => 'Draft Lesson',
                'type' => 'text',
                'is_published' => 1,
                'save_action' => 'draft',
            ]);

        $lesson = Lesson::firstOrFail();

        $response->assertRedirect(route('lessons.edit', $lesson));
        $response->assertSessionHas('success', __('lessons.draft_saved'));
        $response->assertSessionHas('clear_lesson_draft_keys', ['lesson-builder-draft:create']);

        $this->assertSame('Draft Lesson', $lesson->title);
        $this->assertFalse($lesson->is_published);
    }

    public function test_save_draft_allows_incomplete_new_lessons(): void
    {
        $user = User::factory()->educator()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('lessons.store'), [
                'save_action' => 'draft',
            ]);

        $lesson = Lesson::firstOrFail();

        $response->assertRedirect(route('lessons.edit', $lesson));
        $this->assertSame(__('lessons.untitled_draft'), $lesson->title);
        $this->assertSame('text', $lesson->type);
        $this->assertFalse($lesson->is_published);
    }

    public function test_save_draft_updates_existing_lessons_as_unpublished(): void
    {
        $user = User::factory()->educator()->create();

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Published Lesson',
            'type' => 'text',
            'duration_minutes' => 20,
            'is_published' => true,
            'is_free' => false,
            'segments' => [],
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('lessons.update', $lesson), [
                'title' => 'Published Lesson Draft',
                'type' => 'text',
                'save_action' => 'draft',
            ]);

        $response->assertRedirect(route('lessons.edit', $lesson));
        $response->assertSessionHas('success', __('lessons.draft_saved'));
        $response->assertSessionHas('clear_lesson_draft_keys', ["lesson-builder-draft:edit:{$lesson->id}"]);

        $lesson->refresh();

        $this->assertSame('Published Lesson Draft', $lesson->title);
        $this->assertFalse($lesson->is_published);
    }

    public function test_autosave_creates_an_unpublished_lesson_and_returns_edit_metadata(): void
    {
        $user = User::factory()->educator()->create();

        $response = $this
            ->actingAs($user)
            ->postJson(route('lessons.autosave'), [
                'title' => 'Autosaved Lesson Draft',
                'type' => 'text',
                'subject' => Lesson::defaultSubject(),
            ]);

        $lesson = Lesson::firstOrFail();

        $response->assertOk();
        $response->assertJsonPath('lesson_id', $lesson->id);
        $response->assertJsonPath('created', true);
        $response->assertJsonPath('edit_url', route('lessons.edit', $lesson));
        $response->assertJsonPath('update_url', route('lessons.update', $lesson));

        $this->assertSame('Autosaved Lesson Draft', $lesson->title);
        $this->assertFalse($lesson->is_published);
        $this->assertSame($user->id, $lesson->user_id);
    }

    public function test_autosave_updates_existing_unpublished_lessons(): void
    {
        $user = User::factory()->educator()->create();

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Initial Draft Title',
            'type' => 'text',
            'duration_minutes' => 10,
            'is_published' => false,
            'is_free' => true,
            'segments' => [],
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('lessons.autosave'), [
                'lesson_id' => $lesson->id,
                'title' => 'Updated Draft Title',
                'type' => 'text',
                'subject' => Lesson::defaultSubject(),
            ]);

        $response->assertOk();
        $response->assertJsonPath('lesson_id', $lesson->id);
        $response->assertJsonPath('created', false);

        $lesson->refresh();

        $this->assertSame('Updated Draft Title', $lesson->title);
        $this->assertFalse($lesson->is_published);
    }

    public function test_autosave_rejects_updates_to_published_lessons(): void
    {
        $user = User::factory()->educator()->create();

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Published Lesson',
            'type' => 'text',
            'duration_minutes' => 15,
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('lessons.autosave'), [
                'lesson_id' => $lesson->id,
                'title' => 'Should Not Change',
                'type' => 'text',
            ]);

        $response->assertStatus(422);
        $response->assertJsonPath('message', __('lessons.draft_autosave_unavailable'));

        $lesson->refresh();

        $this->assertSame('Published Lesson', $lesson->title);
        $this->assertTrue($lesson->is_published);
    }

    public function test_create_publish_requires_a_complete_publish_checklist(): void
    {
        $user = User::factory()->educator()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('lessons.create'))
            ->post(route('lessons.store'), [
                'title' => 'Checklist Incomplete',
                'type' => 'text',
                'subject' => Lesson::defaultSubject(),
                'duration_hours' => 0,
                'duration_minutes' => 30,
                'is_published' => 1,
            ]);

        $response->assertRedirect(route('lessons.create'));
        $response->assertSessionHasErrors('publish_checklist');
        $this->assertDatabaseCount('lessons', 0);
    }

    public function test_create_publish_succeeds_when_the_publish_checklist_is_complete(): void
    {
        $user = User::factory()->educator()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('lessons.store'), [
                'title' => 'Checklist Ready Lesson',
                'type' => 'text',
                'subject' => Lesson::defaultSubject(),
                'duration_hours' => 0,
                'duration_minutes' => 30,
                'is_published' => 1,
                'segments' => [
                    1 => [
                        'custom_name' => 'Getting Started',
                        'blocks' => [
                            1 => [
                                'type' => 'text',
                                'content' => 'This segment has enough content to publish.',
                            ],
                        ],
                    ],
                ],
            ]);

        $lesson = Lesson::firstOrFail();

        $response->assertRedirect(route('lessons.index'));
        $response->assertSessionHas('success', __('lessons.lesson_created'));
        $this->assertSame('Checklist Ready Lesson', $lesson->title);
        $this->assertTrue($lesson->is_published);
    }

    public function test_update_publish_requires_a_complete_publish_checklist(): void
    {
        $user = User::factory()->educator()->create();

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Needs More Content',
            'type' => 'text',
            'duration_minutes' => 20,
            'is_published' => false,
            'is_free' => true,
            'segments' => [],
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('lessons.edit', $lesson))
            ->put(route('lessons.update', $lesson), [
                'title' => 'Needs More Content',
                'type' => 'text',
                'subject' => Lesson::defaultSubject(),
                'duration_hours' => 0,
                'duration_minutes' => 20,
                'is_published' => 1,
            ]);

        $response->assertRedirect(route('lessons.edit', $lesson));
        $response->assertSessionHasErrors('publish_checklist');

        $lesson->refresh();

        $this->assertFalse($lesson->is_published);
    }

    public function test_owner_can_publish_an_unpublished_lesson(): void
    {
        $user = User::factory()->educator()->create();

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Ready To Publish',
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
                            'content' => 'This lesson is now academically complete enough to publish.',
                        ],
                    ],
                ],
            ],
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('lessons.show', $lesson))
            ->post(route('lessons.publish', $lesson));

        $response->assertRedirect(route('lessons.show', $lesson));
        $response->assertSessionHas('success', __('lessons.lesson_published'));

        $lesson->refresh();

        $this->assertTrue($lesson->is_published);
    }

    public function test_empty_text_draft_redirects_to_edit_instead_of_publishing(): void
    {
        $user = User::factory()->educator()->create();

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Empty Text Draft',
            'type' => 'text',
            'duration_minutes' => 20,
            'is_published' => false,
            'is_free' => true,
            'segments' => [],
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('lessons.publish', $lesson));

        $response->assertRedirect(route('lessons.edit', $lesson));
        $response->assertSessionHas('error', __('lessons.complete_before_publish'));

        $lesson->refresh();

        $this->assertFalse($lesson->is_published);
    }

    public function test_text_lesson_with_exam_questions_can_publish(): void
    {
        $user = User::factory()->educator()->create();

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Exam Ready Lesson',
            'type' => 'text',
            'duration_minutes' => 20,
            'is_published' => false,
            'is_free' => true,
            'segments' => [
                [
                    'id' => 2,
                    'type' => 'exam',
                    'exam_settings' => [
                        'time_limit' => 15,
                        'passing_score' => 70,
                    ],
                    'questions' => [
                        [
                            'id' => 1,
                            'type' => 'multiple_choice',
                            'question' => 'Which answer is correct?',
                            'answers' => ['First', 'Second'],
                            'correct_answer' => '1',
                        ],
                    ],
                ],
            ],
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('lessons.show', $lesson))
            ->post(route('lessons.publish', $lesson));

        $response->assertRedirect(route('lessons.show', $lesson));
        $response->assertSessionHas('success', __('lessons.lesson_published'));

        $lesson->refresh();

        $this->assertTrue($lesson->is_published);
    }

    public function test_incomplete_draft_redirects_to_edit_instead_of_publishing(): void
    {
        $user = User::factory()->educator()->create();

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Broken Video Draft',
            'type' => 'video',
            'video_url' => null,
            'duration_minutes' => 20,
            'is_published' => false,
            'is_free' => false,
            'segments' => [],
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('lessons.publish', $lesson));

        $response->assertRedirect(route('lessons.edit', $lesson));
        $response->assertSessionHas('error', __('lessons.complete_before_publish'));

        $lesson->refresh();

        $this->assertFalse($lesson->is_published);
    }
}
