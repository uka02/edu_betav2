<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedViewRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_pages_render_with_shared_partials(): void
    {
        $user = User::factory()->educator()->create();
        $otherUser = User::factory()->create();

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Owner Lesson',
            'type' => 'text',
            'duration_minutes' => 30,
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        $draftLesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Draft Lesson',
            'type' => 'text',
            'content' => 'Draft lesson body',
            'duration_minutes' => 15,
            'is_published' => false,
            'is_free' => true,
            'segments' => [],
        ]);

        $segmentedLesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Segmented Lesson',
            'type' => 'text',
            'duration_minutes' => 40,
            'is_published' => true,
            'is_free' => true,
            'segments' => [
                [
                    'id' => 1,
                    'type' => 'content',
                    'custom_name' => 'Introduction',
                    'blocks' => [
                        [
                            'id' => 1,
                            'type' => 'text',
                            'content' => 'Intro content',
                        ],
                    ],
                ],
                [
                    'id' => 2,
                    'type' => 'content',
                    'custom_name' => 'Deep Dive',
                    'blocks' => [
                        [
                            'id' => 1,
                            'type' => 'text',
                            'content' => 'Deep dive content',
                        ],
                    ],
                ],
                [
                    'id' => 3,
                    'type' => 'exam',
                    'custom_name' => 'Final Check',
                    'exam_settings' => [
                        'passing_score' => 70,
                        'time_limit' => 10,
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'question' => 'Which option is correct?',
                            'answers' => ['A', 'B'],
                            'correct_answer' => 0,
                        ],
                    ],
                ],
            ],
        ]);

        Lesson::create([
            'user_id' => $otherUser->id,
            'title' => 'Trending Lesson',
            'type' => 'text',
            'duration_minutes' => 20,
            'is_published' => true,
            'is_free' => false,
            'segments' => [],
        ]);

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'exam_index' => 0,
            'certificate_code' => 'CERT-RENDER-0001',
            'issued_at' => now(),
            'snapshot' => [
                'learner_name' => $user->name,
                'lesson_title' => $lesson->title,
                'issuer_name' => $user->name,
                'score' => 95,
            ],
        ]);

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
        $this->actingAs($user)->get(route('profile.edit'))->assertOk();
        $this->actingAs($user)->get(route('certificates.index'))->assertOk();
        $this->actingAs($user)->get(route('certificates.show', $certificate))->assertOk();
        $this->actingAs($user)
            ->get(route('lessons.index'))
            ->assertOk()
            ->assertSee(route('lessons.publish', $draftLesson), false);

        $createResponse = $this->actingAs($user)->get(route('lessons.create'));
        $createResponse
            ->assertOk()
            ->assertSee('id="draftRecoveryPanel"', false)
            ->assertSee('id="draftStatus"', false)
            ->assertSee('name="save_action" value="draft"', false)
            ->assertSee('bindLessonDraftSupport({', false)
            ->assertSee('id="openLearnerPreviewBtn"', false)
            ->assertSee('id="learnerPreviewModal"', false);

        $editResponse = $this->actingAs($user)->get(route('lessons.edit', $lesson));
        $editResponse
            ->assertOk()
            ->assertSee('id="draftRecoveryPanel"', false)
            ->assertSee('id="draftStatus"', false)
            ->assertSee('name="save_action" value="draft"', false)
            ->assertSee('bindLessonDraftSupport({', false)
            ->assertSee('id="openLearnerPreviewBtn"', false)
            ->assertSee('id="learnerPreviewModal"', false);

        $this->actingAs($user)
            ->get(route('lessons.show', $draftLesson))
            ->assertOk()
            ->assertSee('data-progress-key="legacy-content"', false)
            ->assertSee('const initialLessonProgressItems =', false)
            ->assertSee('id="timeTaken"', false)
            ->assertSee(route('lessons.publish', $draftLesson), false);

        $this->actingAs($user)
            ->get(route('lessons.show', $segmentedLesson))
            ->assertOk()
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-modal="true"', false)
            ->assertSee('id="examDialogTitle"', false)
            ->assertSee('role="status"', false)
            ->assertSee("true_answer:", false)
            ->assertSee('data-segment-target="basic-info"', false)
            ->assertSee('data-segment-target="content-1"', false)
            ->assertSee('data-segment-target="content-2"', false)
            ->assertSee('data-segment-target="exam-0"', false)
            ->assertSee('data-segment="basic-info"', false)
            ->assertSee('data-segment="content-1"', false)
            ->assertSee('data-segment="content-2"', false)
            ->assertSee('data-segment="exam-0"', false)
            ->assertSee('Introduction')
            ->assertSee('Deep Dive')
            ->assertSee('Final Check');
    }
}
