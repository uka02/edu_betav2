<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonLearnerCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_catalog_lists_published_lessons_and_supports_subject_filters(): void
    {
        $educatorOne = User::factory()->educator()->create(['name' => 'Alice Teacher']);
        $educatorTwo = User::factory()->educator()->create(['name' => 'Batsaikhan Mentor']);

        Lesson::create([
            'user_id' => $educatorOne->id,
            'title' => 'Intro to Cybersecurity',
            'subject' => Lesson::SUBJECT_CYBERSECURITY,
            'type' => 'text',
            'content' => 'Security basics',
            'duration_minutes' => 30,
            'difficulty' => 'beginner',
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        Lesson::create([
            'user_id' => $educatorTwo->id,
            'title' => 'Python Automation',
            'subject' => Lesson::SUBJECT_PYTHON,
            'type' => 'text',
            'content' => 'Automation workflows',
            'duration_minutes' => 45,
            'difficulty' => 'intermediate',
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        Lesson::create([
            'user_id' => $educatorOne->id,
            'title' => 'Hidden Draft',
            'subject' => Lesson::SUBJECT_CYBERSECURITY,
            'type' => 'text',
            'content' => 'Draft content',
            'duration_minutes' => 20,
            'difficulty' => 'beginner',
            'is_published' => false,
            'is_free' => true,
            'segments' => [],
        ]);

        $response = $this->get(route('lessons.index', [
            'subject' => Lesson::SUBJECT_COMPUTER_SCIENCE,
            'q' => 'Alice',
        ]));

        $response
            ->assertOk()
            ->assertSee(__('lessons.explore_lessons'))
            ->assertSee(__('lessons.subject_' . Lesson::SUBJECT_COMPUTER_SCIENCE))
            ->assertSee(__('auth.sign_in'))
            ->assertSee(__('auth.create_account'))
            ->assertSee('Intro to Cybersecurity')
            ->assertSee('Alice Teacher')
            ->assertDontSee('Python Automation')
            ->assertDontSee('Hidden Draft');
    }

    public function test_learner_catalog_lists_published_lessons_and_supports_subject_filters(): void
    {
        $learner = User::factory()->learner()->create();
        $educatorOne = User::factory()->educator()->create(['name' => 'Alice Teacher']);
        $educatorTwo = User::factory()->educator()->create(['name' => 'Batsaikhan Mentor']);

        Lesson::create([
            'user_id' => $educatorOne->id,
            'title' => 'Intro to Cybersecurity',
            'subject' => Lesson::SUBJECT_CYBERSECURITY,
            'type' => 'text',
            'content' => 'Security basics',
            'duration_minutes' => 30,
            'difficulty' => 'beginner',
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        Lesson::create([
            'user_id' => $educatorTwo->id,
            'title' => 'Python Automation',
            'subject' => Lesson::SUBJECT_PYTHON,
            'type' => 'text',
            'content' => 'Automation workflows',
            'duration_minutes' => 45,
            'difficulty' => 'intermediate',
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        Lesson::create([
            'user_id' => $educatorOne->id,
            'title' => 'Hidden Draft',
            'subject' => Lesson::SUBJECT_CYBERSECURITY,
            'type' => 'text',
            'content' => 'Draft content',
            'duration_minutes' => 20,
            'difficulty' => 'beginner',
            'is_published' => false,
            'is_free' => true,
            'segments' => [],
        ]);

        $response = $this->actingAs($learner)->get(route('lessons.index', [
            'subject' => Lesson::SUBJECT_COMPUTER_SCIENCE,
            'q' => 'Alice',
        ]));

        $response
            ->assertOk()
            ->assertSee(__('lessons.explore_lessons'))
            ->assertSee(__('lessons.subject_' . Lesson::SUBJECT_COMPUTER_SCIENCE))
            ->assertSee('Intro to Cybersecurity')
            ->assertSee('Alice Teacher')
            ->assertDontSee('Python Automation')
            ->assertDontSee('Hidden Draft');
    }

    public function test_guest_can_view_published_lesson_preview_without_progress_autosave(): void
    {
        $educator = User::factory()->educator()->create(['name' => 'Preview Teacher']);

        $lesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Public Preview Lesson',
            'subject' => Lesson::SUBJECT_NETWORKING,
            'type' => 'text',
            'content' => 'Public preview content',
            'duration_minutes' => 20,
            'difficulty' => 'beginner',
            'is_published' => true,
            'is_free' => true,
            'segments' => [
                [
                    'id' => 1,
                    'type' => 'content',
                    'custom_name' => 'Overview',
                    'blocks' => [
                        [
                            'id' => 1,
                            'type' => 'text',
                            'content' => 'Segment body',
                        ],
                    ],
                ],
                [
                    'id' => 2,
                    'type' => 'exam',
                    'custom_name' => 'Quick Check',
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'question' => 'Pick the correct answer',
                            'answers' => ['A', 'B'],
                            'correct_answer' => 0,
                        ],
                    ],
                ],
            ],
        ]);

        $this->get(route('lessons.show', $lesson))
            ->assertOk()
            ->assertSee('Public Preview Lesson')
            ->assertSee(__('auth.sign_in'))
            ->assertSee(__('auth.create_account'))
            ->assertSee(__('lessons.track_progress'))
            ->assertSee(route('login'), false)
            ->assertSee('const isAuthenticatedViewer = false;', false)
            ->assertDontSee(route('lessons.progress', $lesson), false);
    }

    public function test_guest_cannot_view_unpublished_lesson(): void
    {
        $educator = User::factory()->educator()->create();

        $lesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Private Draft',
            'subject' => Lesson::SUBJECT_CYBERSECURITY,
            'type' => 'text',
            'content' => 'Private content',
            'duration_minutes' => 15,
            'difficulty' => 'beginner',
            'is_published' => false,
            'is_free' => true,
            'segments' => [],
        ]);

        $this->get(route('lessons.show', $lesson))
            ->assertForbidden();
    }

    public function test_learner_cannot_access_educator_lesson_management_routes(): void
    {
        $learner = User::factory()->learner()->create();

        $this->actingAs($learner)
            ->get(route('lessons.create'))
            ->assertForbidden();

        $this->actingAs($learner)
            ->post(route('lessons.store'), [
                'title' => 'Blocked Lesson',
                'type' => 'text',
            ])
            ->assertForbidden();
    }
}
