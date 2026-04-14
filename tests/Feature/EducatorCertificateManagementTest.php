<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Lesson;
use App\Models\LessonExamAttempt;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducatorCertificateManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_educator_certificate_index_renders_management_console(): void
    {
        $educator = User::factory()->educator()->create();
        $learner = User::factory()->learner()->create();

        $lesson = $this->createLesson($educator, [
            'title' => 'Network Automation',
            'is_published' => true,
        ]);

        Certificate::create([
            'user_id' => $learner->id,
            'issued_by_user_id' => $educator->id,
            'lesson_id' => $lesson->id,
            'exam_index' => 0,
            'certificate_code' => 'CERT-MGMT-0001',
            'issued_at' => now(),
            'validated_at' => now(),
            'validation_notes' => 'Validated against submitted lab work.',
            'snapshot' => [
                'learner_name' => $learner->name,
                'lesson_title' => $lesson->title,
                'issuer_name' => $educator->name,
                'score' => 94,
            ],
        ]);

        $response = $this->actingAs($educator)->get(route('certificates.index'));

        $response->assertOk();
        $response->assertSee(__('certificates.educator_title'));
        $response->assertSee(__('certificates.issue_certificate'));
        $response->assertSee('CERT-MGMT-0001');
        $response->assertDontSee(__('certificates.earned_certificates'));
    }

    public function test_educator_can_issue_certificate_after_validation_passes(): void
    {
        $educator = User::factory()->educator()->create(['name' => 'Educator One']);
        $learner = User::factory()->learner()->create(['email' => 'learner@example.com']);

        $lesson = $this->createLesson($educator, [
            'title' => 'Cyber Hygiene',
            'is_published' => true,
            'segments' => [
                [
                    'type' => 'exam',
                    'custom_name' => 'Final Check',
                    'exam_settings' => [
                        'passing_score' => 80,
                    ],
                    'questions' => [],
                ],
            ],
        ]);

        LessonProgress::create([
            'user_id' => $learner->id,
            'lesson_id' => $lesson->id,
            'watched_seconds' => 1800,
            'last_position_seconds' => 1800,
            'progress_percent' => 100,
            'progress_state' => ['items' => []],
            'last_viewed_at' => now(),
            'completed_at' => now(),
        ]);

        $attempt = LessonExamAttempt::create([
            'user_id' => $learner->id,
            'lesson_id' => $lesson->id,
            'exam_index' => 0,
            'score' => 96,
            'passed' => true,
            'correct_count' => 12,
            'total_questions' => 12,
            'time_taken' => 320,
            'answers' => [],
            'results' => [],
            'attempted_at' => now(),
        ]);

        $response = $this->actingAs($educator)->post(route('certificates.store'), [
            'learner_email' => $learner->email,
            'lesson_id' => $lesson->id,
            'exam_index' => 0,
            'issued_at' => now()->toDateString(),
            'validation_notes' => 'Validated after final assessment review.',
        ]);

        $certificate = Certificate::first();

        $response->assertRedirect(route('certificates.edit', $certificate));
        $this->assertNotNull($certificate);
        $this->assertSame($learner->id, $certificate->user_id);
        $this->assertSame($educator->id, $certificate->issued_by_user_id);
        $this->assertSame($attempt->id, $certificate->lesson_exam_attempt_id);
        $this->assertNotNull($certificate->validated_at);
        $this->assertSame('Validated after final assessment review.', $certificate->validation_notes);
        $this->assertSame('Final Check', data_get($certificate->snapshot, 'exam_title'));
    }

    public function test_educator_cannot_issue_certificate_before_requirements_are_met(): void
    {
        $educator = User::factory()->educator()->create();
        $learner = User::factory()->learner()->create(['email' => 'learner@example.com']);

        $lesson = $this->createLesson($educator, [
            'title' => 'Secure Passwords',
            'is_published' => true,
            'segments' => [
                [
                    'type' => 'exam',
                    'custom_name' => 'Lesson Quiz',
                    'questions' => [],
                ],
            ],
        ]);

        $response = $this->from(route('certificates.index'))
            ->actingAs($educator)
            ->post(route('certificates.store'), [
                'learner_email' => $learner->email,
                'lesson_id' => $lesson->id,
                'exam_index' => 0,
                'issued_at' => now()->toDateString(),
            ]);

        $response->assertRedirect(route('certificates.index'));
        $response->assertSessionHasErrors(['learner_email']);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_educator_can_edit_a_managed_certificate(): void
    {
        $educator = User::factory()->educator()->create(['name' => 'Educator Two']);
        $learner = User::factory()->learner()->create();

        $lesson = $this->createLesson($educator, [
            'title' => 'Python Basics',
            'is_published' => true,
        ]);

        $certificate = Certificate::create([
            'user_id' => $learner->id,
            'issued_by_user_id' => $educator->id,
            'lesson_id' => $lesson->id,
            'exam_index' => 0,
            'certificate_code' => 'CERT-MGMT-0002',
            'issued_at' => now()->subDay(),
            'validated_at' => now()->subDay(),
            'validation_notes' => 'Initial validation note.',
            'snapshot' => [
                'learner_name' => $learner->name,
                'lesson_title' => $lesson->title,
                'issuer_name' => $educator->name,
                'exam_title' => 'Educator validation',
                'score' => 80,
                'passing_score' => 70,
            ],
        ]);

        $response = $this->actingAs($educator)->put(route('certificates.update', $certificate), [
            'issued_at' => now()->toDateString(),
            'exam_title' => 'Educator reviewed final project',
            'score' => 91,
            'passing_score' => 75,
            'validation_notes' => 'Updated after reviewing the resubmitted project.',
        ]);

        $response->assertRedirect(route('certificates.edit', $certificate));

        $certificate->refresh();

        $this->assertSame('Updated after reviewing the resubmitted project.', $certificate->validation_notes);
        $this->assertSame('Educator reviewed final project', data_get($certificate->snapshot, 'exam_title'));
        $this->assertSame(91.0, (float) data_get($certificate->snapshot, 'score'));
        $this->assertSame(75, data_get($certificate->snapshot, 'passing_score'));
    }

    public function test_learners_cannot_access_educator_certificate_routes(): void
    {
        $learner = User::factory()->learner()->create();
        $educator = User::factory()->educator()->create();

        $lesson = $this->createLesson($educator, [
            'is_published' => true,
        ]);

        $certificate = Certificate::create([
            'user_id' => $learner->id,
            'issued_by_user_id' => $educator->id,
            'lesson_id' => $lesson->id,
            'exam_index' => 0,
            'certificate_code' => 'CERT-MGMT-0003',
            'issued_at' => now(),
            'validated_at' => now(),
            'snapshot' => [
                'learner_name' => $learner->name,
                'lesson_title' => $lesson->title,
                'issuer_name' => $educator->name,
            ],
        ]);

        $this->actingAs($learner)->get(route('certificates.edit', $certificate))->assertForbidden();
        $this->actingAs($learner)->put(route('certificates.update', $certificate), [
            'issued_at' => now()->toDateString(),
        ])->assertForbidden();
    }

    private function createLesson(User $user, array $attributes = []): Lesson
    {
        return Lesson::create(array_merge([
            'user_id' => $user->id,
            'title' => 'Test Lesson ' . str()->random(8),
            'type' => 'text',
            'duration_minutes' => 30,
            'is_published' => false,
            'is_free' => true,
            'segments' => [],
        ], $attributes));
    }
}
