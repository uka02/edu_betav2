<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LessonApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_store_persists_document_path_for_document_lessons(): void
    {
        Storage::fake('public');
        $user = User::factory()->educator()->create();

        $response = $this
            ->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('api.lessons.store'), [
                'title' => 'Document Lesson',
                'type' => 'document',
                'document' => UploadedFile::fake()->create('lesson.pdf', 200, 'application/pdf'),
                'duration_hours' => 1,
                'duration_minutes' => 15,
            ]);

        $response->assertCreated()->assertJson(['success' => true]);

        $lesson = Lesson::firstOrFail();

        $this->assertSame($user->id, $lesson->user_id);
        $this->assertNotNull($lesson->document_path);
        $this->assertSame(75, $lesson->duration_minutes);
        Storage::disk('public')->assertExists($lesson->document_path);
    }

    public function test_api_update_replaces_old_document_file_consistently(): void
    {
        Storage::fake('public');
        $user = User::factory()->educator()->create();

        Storage::disk('public')->put('documents/old.pdf', 'old-file');

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'title' => 'Old Lesson',
            'type' => 'document',
            'document_path' => 'documents/old.pdf',
            'duration_minutes' => 20,
            'is_published' => false,
            'is_free' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->put(route('api.lessons.update', $lesson), [
                'title' => 'Updated Lesson',
                'type' => 'document',
                'document' => UploadedFile::fake()->create('new.pdf', 200, 'application/pdf'),
                'duration_hours' => 0,
                'duration_minutes' => 45,
            ]);

        $response->assertOk()->assertJson(['success' => true]);

        $lesson->refresh();

        $this->assertNotSame('documents/old.pdf', $lesson->document_path);
        $this->assertSame(45, $lesson->duration_minutes);
        Storage::disk('public')->assertMissing('documents/old.pdf');
        Storage::disk('public')->assertExists($lesson->document_path);
    }
}
