<?php

namespace Tests\Feature;

use App\Models\CertificateVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCertificateVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_log_in_with_username_and_is_redirected_to_verification_queue(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'admin',
            'password' => Hash::make('Admin12345!'),
        ]);

        $response = $this->post(route('login.post'), [
            'login' => 'admin',
            'password' => 'Admin12345!',
        ]);

        $response->assertRedirect(route('admin.certificate-verifications.index'));
        $this->assertAuthenticatedAs($admin->fresh());
    }

    public function test_learner_can_upload_certificate_pdf_for_verification(): void
    {
        Storage::fake('local');
        $learner = User::factory()->learner()->create();

        $response = $this->actingAs($learner)->post(route('certificate-verifications.store'), [
            'title' => 'Cisco CCNA',
            'issuer_name' => 'Cisco Networking Academy',
            'notes' => 'Completed during spring semester.',
            'document' => UploadedFile::fake()->create('ccna.pdf', 220, 'application/pdf'),
        ]);

        $response->assertRedirect(route('certificates.index'));
        $this->assertDatabaseHas('certificate_verifications', [
            'user_id' => $learner->id,
            'title' => 'Cisco CCNA',
            'status' => CertificateVerification::STATUS_PENDING,
        ]);

        $verification = CertificateVerification::first();

        Storage::disk('local')->assertExists($verification->document_path);
    }

    public function test_admin_can_approve_certificate_verification_requests(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->learner()->create();

        $verification = CertificateVerification::create([
            'user_id' => $learner->id,
            'title' => 'Python Essentials',
            'issuer_name' => 'Python Institute',
            'document_path' => 'certificate-verifications/python.pdf',
            'original_filename' => 'python.pdf',
            'status' => CertificateVerification::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.certificate-verifications.approve', $verification), [
            'review_notes' => 'Verified against the uploaded document.',
        ]);

        $response->assertRedirect();

        $verification->refresh();

        $this->assertSame(CertificateVerification::STATUS_APPROVED, $verification->status);
        $this->assertSame($admin->id, $verification->reviewed_by_user_id);
        $this->assertSame('Verified against the uploaded document.', $verification->review_notes);
        $this->assertNotNull($verification->reviewed_at);
    }

    public function test_non_admin_users_cannot_access_admin_certificate_verification_queue(): void
    {
        $learner = User::factory()->learner()->create();

        $this->actingAs($learner)
            ->get(route('admin.certificate-verifications.index'))
            ->assertForbidden();
    }

    public function test_learner_certificates_page_shows_uploaded_verification_requests(): void
    {
        $learner = User::factory()->learner()->create();

        CertificateVerification::create([
            'user_id' => $learner->id,
            'title' => 'Network Security Badge',
            'issuer_name' => 'Cyber Academy',
            'document_path' => 'certificate-verifications/network-security.pdf',
            'original_filename' => 'network-security.pdf',
            'status' => CertificateVerification::STATUS_PENDING,
        ]);

        $this->actingAs($learner)
            ->get(route('certificates.index'))
            ->assertOk()
            ->assertSee(__('certificates.verification_requests'))
            ->assertSee('Network Security Badge')
            ->assertSee(__('certificates.status_pending'));
    }
}
