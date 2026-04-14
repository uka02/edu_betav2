<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificatePageAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_cannot_view_other_users_certificate_pages(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $lesson = Lesson::create([
            'user_id' => $owner->id,
            'title' => 'Private Certificate Lesson',
            'type' => 'text',
            'duration_minutes' => 20,
            'is_published' => true,
            'is_free' => false,
            'segments' => [],
        ]);

        $certificate = Certificate::create([
            'user_id' => $owner->id,
            'lesson_id' => $lesson->id,
            'exam_index' => 0,
            'certificate_code' => 'CERT-ACCESS-0001',
            'issued_at' => now(),
            'snapshot' => [
                'learner_name' => $owner->name,
                'lesson_title' => $lesson->title,
                'issuer_name' => $owner->name,
                'score' => 88,
            ],
        ]);

        $this->actingAs($otherUser)->get(route('certificates.show', $certificate))->assertForbidden();
    }

    public function test_certificate_owner_can_download_a_named_certificate_file(): void
    {
        $owner = User::factory()->create(['name' => 'Owner Person']);

        $lesson = Lesson::create([
            'user_id' => $owner->id,
            'title' => 'Downloadable Certificate Lesson',
            'type' => 'text',
            'duration_minutes' => 20,
            'is_published' => true,
            'is_free' => false,
            'segments' => [],
        ]);

        $certificate = Certificate::create([
            'user_id' => $owner->id,
            'lesson_id' => $lesson->id,
            'exam_index' => 0,
            'certificate_code' => 'CERT-DOWNLOAD-0001',
            'issued_at' => now(),
            'snapshot' => [
                'learner_name' => $owner->name,
                'lesson_title' => $lesson->title,
                'issuer_name' => 'EduDev Academy',
                'exam_title' => 'Final Evaluation',
                'score' => 96,
            ],
        ]);

        $response = $this->actingAs($owner)->get(route('certificates.download', $certificate));

        $response->assertOk();
        $this->assertStringStartsWith('image/svg+xml', (string) $response->headers->get('content-type'));
        $this->assertSame(
            'attachment; filename="' . $certificate->downloadFilename('svg') . '"',
            $response->headers->get('content-disposition')
        );
        $response->assertSee('Owner Person', false);
        $response->assertSee('Downloadable Certificate Lesson', false);
        $response->assertSee('Final Evaluation', false);
    }

    public function test_users_cannot_download_other_users_certificates(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $lesson = Lesson::create([
            'user_id' => $owner->id,
            'title' => 'Protected Download Lesson',
            'type' => 'text',
            'duration_minutes' => 20,
            'is_published' => true,
            'is_free' => false,
            'segments' => [],
        ]);

        $certificate = Certificate::create([
            'user_id' => $owner->id,
            'lesson_id' => $lesson->id,
            'exam_index' => 0,
            'certificate_code' => 'CERT-DOWNLOAD-0002',
            'issued_at' => now(),
            'snapshot' => [
                'learner_name' => $owner->name,
                'lesson_title' => $lesson->title,
                'issuer_name' => $owner->name,
                'score' => 88,
            ],
        ]);

        $this->actingAs($otherUser)->get(route('certificates.download', $certificate))->assertForbidden();
    }
}
