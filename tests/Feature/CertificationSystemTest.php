<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Lesson;
use App\Models\LessonExamAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificationSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_passing_a_published_exam_records_attempt_and_issues_a_certificate(): void
    {
        $owner = User::factory()->create(['name' => 'Lesson Owner']);
        $learner = User::factory()->create(['name' => 'Learner User']);

        $lesson = $this->createExamLesson($owner, ['is_published' => true]);

        $response = $this
            ->actingAs($learner)
            ->postJson(route('lessons.grade-exam', $lesson), [
                'exam_index' => 0,
                'answers' => [1, 'Blue'],
                'time_taken' => 95,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('passed', true)
            ->assertJsonPath('score', 100)
            ->assertJsonPath('correct_count', 2)
            ->assertJsonPath('certificate.was_issued_now', true);

        $attempt = LessonExamAttempt::firstOrFail();
        $certificate = Certificate::firstOrFail();

        $this->assertSame($learner->id, $attempt->user_id);
        $this->assertSame($lesson->id, $attempt->lesson_id);
        $this->assertTrue($attempt->passed);
        $this->assertSame($attempt->id, $response->json('attempt_id'));
        $this->assertSame($certificate->certificate_code, $response->json('certificate.code'));
        $this->assertSame('Learner User', $certificate->snapshot['learner_name']);
        $this->assertSame($lesson->title, $certificate->snapshot['lesson_title']);
        $this->assertSame('Lesson Owner', $certificate->snapshot['issuer_name']);
    }

    public function test_failing_an_exam_records_attempt_without_issuing_a_certificate(): void
    {
        $owner = User::factory()->create();
        $learner = User::factory()->create();

        $lesson = $this->createExamLesson($owner, ['is_published' => true]);

        $response = $this
            ->actingAs($learner)
            ->postJson(route('lessons.grade-exam', $lesson), [
                'exam_index' => 0,
                'answers' => [0, 'red'],
                'time_taken' => 120,
            ]);

        $response->assertOk()
            ->assertJsonPath('passed', false)
            ->assertJsonPath('certificate', null);

        $this->assertDatabaseCount('lesson_exam_attempts', 1);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_repeated_passes_do_not_duplicate_the_certificate(): void
    {
        $owner = User::factory()->create();
        $learner = User::factory()->create();

        $lesson = $this->createExamLesson($owner, ['is_published' => true]);

        $firstResponse = $this
            ->actingAs($learner)
            ->postJson(route('lessons.grade-exam', $lesson), [
                'exam_index' => 0,
                'answers' => [1, 'blue'],
                'time_taken' => 60,
            ]);

        $secondResponse = $this
            ->actingAs($learner)
            ->postJson(route('lessons.grade-exam', $lesson), [
                'exam_index' => 0,
                'answers' => [1, 'BLUE'],
                'time_taken' => 40,
            ]);

        $firstCode = $firstResponse->json('certificate.code');

        $secondResponse->assertOk()
            ->assertJsonPath('passed', true)
            ->assertJsonPath('certificate.code', $firstCode)
            ->assertJsonPath('certificate.was_issued_now', false);

        $this->assertDatabaseCount('lesson_exam_attempts', 2);
        $this->assertDatabaseCount('certificates', 1);
    }

    public function test_passing_multiple_exam_segments_issues_distinct_certificates_per_exam(): void
    {
        $owner = User::factory()->create();
        $learner = User::factory()->create();

        $lesson = $this->createExamLesson($owner, [
            'segments' => [
                [
                    'type' => 'exam',
                    'custom_name' => 'Module 1 Exam',
                    'exam_settings' => [
                        'passing_score' => 100,
                        'time_limit' => 0,
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'question' => 'First exam question',
                            'answers' => ['A', 'B'],
                            'correct_answer' => 0,
                        ],
                    ],
                ],
                [
                    'type' => 'exam',
                    'custom_name' => 'Module 2 Exam',
                    'exam_settings' => [
                        'passing_score' => 100,
                        'time_limit' => 0,
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'question' => 'Second exam question',
                            'answers' => ['Red', 'Blue'],
                            'correct_answer' => 1,
                        ],
                    ],
                ],
            ],
        ]);

        $firstResponse = $this
            ->actingAs($learner)
            ->postJson(route('lessons.grade-exam', $lesson), [
                'exam_index' => 0,
                'answers' => [0],
                'time_taken' => 25,
            ]);

        $secondResponse = $this
            ->actingAs($learner)
            ->postJson(route('lessons.grade-exam', $lesson), [
                'exam_index' => 1,
                'answers' => [1],
                'time_taken' => 30,
            ]);

        $firstResponse->assertOk()
            ->assertJsonPath('passed', true)
            ->assertJsonPath('certificate.was_issued_now', true);

        $secondResponse->assertOk()
            ->assertJsonPath('passed', true)
            ->assertJsonPath('certificate.was_issued_now', true);

        $this->assertDatabaseCount('lesson_exam_attempts', 2);
        $this->assertDatabaseCount('certificates', 2);
        $this->assertDatabaseHas('certificates', [
            'user_id' => $learner->id,
            'lesson_id' => $lesson->id,
            'exam_index' => 0,
        ]);
        $this->assertDatabaseHas('certificates', [
            'user_id' => $learner->id,
            'lesson_id' => $lesson->id,
            'exam_index' => 1,
        ]);

        $this->assertSame('Module 1 Exam', Certificate::where('exam_index', 0)->firstOrFail()->snapshot['exam_title']);
        $this->assertSame('Module 2 Exam', Certificate::where('exam_index', 1)->firstOrFail()->snapshot['exam_title']);
    }

    public function test_submission_at_the_time_limit_can_still_pass(): void
    {
        $owner = User::factory()->create();
        $learner = User::factory()->create();

        $lesson = $this->createExamLesson($owner, [
            'segments' => [
                [
                    'type' => 'exam',
                    'exam_settings' => [
                        'passing_score' => 60,
                        'time_limit' => 1,
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'question' => 'What is 2 + 2?',
                            'answers' => ['3', '4', '5'],
                            'correct_answer' => 1,
                        ],
                        [
                            'type' => 'short_answer',
                            'question' => 'Write BLUE',
                            'correct_answer' => 'blue',
                            'case_sensitive' => false,
                        ],
                    ],
                ],
            ],
            'is_published' => true,
        ]);

        $response = $this
            ->actingAs($learner)
            ->postJson(route('lessons.grade-exam', $lesson), [
                'exam_index' => 0,
                'answers' => [1, 'Blue'],
                'time_taken' => 60,
            ]);

        $response->assertOk()
            ->assertJsonPath('passed', true)
            ->assertJsonPath('time_limit_exceeded', false)
            ->assertJsonPath('certificate.was_issued_now', true);
    }

    public function test_submission_after_the_time_limit_fails_even_with_correct_answers(): void
    {
        $owner = User::factory()->create();
        $learner = User::factory()->create();

        $lesson = $this->createExamLesson($owner, [
            'segments' => [
                [
                    'type' => 'exam',
                    'exam_settings' => [
                        'passing_score' => 60,
                        'time_limit' => 1,
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'question' => 'What is 2 + 2?',
                            'answers' => ['3', '4', '5'],
                            'correct_answer' => 1,
                        ],
                        [
                            'type' => 'short_answer',
                            'question' => 'Write BLUE',
                            'correct_answer' => 'blue',
                            'case_sensitive' => false,
                        ],
                    ],
                ],
            ],
            'is_published' => true,
        ]);

        $response = $this
            ->actingAs($learner)
            ->postJson(route('lessons.grade-exam', $lesson), [
                'exam_index' => 0,
                'answers' => [1, 'Blue'],
                'time_taken' => 61,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('score', 100)
            ->assertJsonPath('passed', false)
            ->assertJsonPath('time_limit_exceeded', true)
            ->assertJsonPath('message', __('lessons.exam_time_limit_exceeded'))
            ->assertJsonPath('certificate', null);

        $this->assertDatabaseHas('lesson_exam_attempts', [
            'lesson_id' => $lesson->id,
            'user_id' => $learner->id,
            'time_taken' => 61,
            'passed' => false,
        ]);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_users_cannot_grade_someone_elses_unpublished_lesson(): void
    {
        $owner = User::factory()->create();
        $learner = User::factory()->create();

        $lesson = $this->createExamLesson($owner, ['is_published' => false]);

        $this->actingAs($learner)
            ->postJson(route('lessons.grade-exam', $lesson), [
                'exam_index' => 0,
                'answers' => [1, 'blue'],
                'time_taken' => 30,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('lesson_exam_attempts', 0);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_a_later_exam_segment_can_be_graded_by_index(): void
    {
        $owner = User::factory()->create();
        $learner = User::factory()->create();

        $lesson = $this->createExamLesson($owner, [
            'segments' => [
                [
                    'type' => 'exam',
                    'exam_settings' => [
                        'passing_score' => 100,
                        'time_limit' => 0,
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'question' => 'First exam question',
                            'answers' => ['A', 'B'],
                            'correct_answer' => 0,
                        ],
                    ],
                ],
                [
                    'type' => 'exam',
                    'exam_settings' => [
                        'passing_score' => 50,
                        'time_limit' => 0,
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'question' => 'Second exam question',
                            'answers' => ['Red', 'Blue'],
                            'correct_answer' => 1,
                        ],
                    ],
                ],
            ],
        ]);

        $response = $this
            ->actingAs($learner)
            ->postJson(route('lessons.grade-exam', $lesson), [
                'exam_index' => 1,
                'answers' => [1],
                'time_taken' => 30,
            ]);

        $response->assertOk()
            ->assertJsonPath('passed', true)
            ->assertJsonPath('correct_count', 1)
            ->assertJsonPath('total_questions', 1);

        $this->assertDatabaseHas('lesson_exam_attempts', [
            'lesson_id' => $lesson->id,
            'user_id' => $learner->id,
            'exam_index' => 1,
            'passed' => true,
        ]);
    }

    private function createExamLesson(User $owner, array $attributes = []): Lesson
    {
        return Lesson::create(array_merge([
            'user_id' => $owner->id,
            'title' => 'Certification Lesson',
            'type' => 'text',
            'duration_minutes' => 45,
            'is_published' => true,
            'is_free' => false,
            'segments' => [
                [
                    'type' => 'exam',
                    'exam_settings' => [
                        'passing_score' => 60,
                        'time_limit' => 0,
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'question' => 'What is 2 + 2?',
                            'answers' => ['3', '4', '5'],
                            'correct_answer' => 1,
                        ],
                        [
                            'type' => 'short_answer',
                            'question' => 'Write BLUE',
                            'correct_answer' => 'blue',
                            'case_sensitive' => false,
                        ],
                    ],
                ],
            ],
        ], $attributes));
    }
}
