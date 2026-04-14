<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LessonDocumentPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_main_document_can_be_previewed_without_downloading(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/guide.pdf', 'previewable-pdf-content');

        $educator = User::factory()->educator()->create();
        $lesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Document Preview Lesson',
            'type' => 'document',
            'document_path' => 'documents/guide.pdf',
            'duration_minutes' => 30,
            'is_published' => true,
            'is_free' => true,
            'segments' => [],
        ]);

        $this->get(route('lessons.show', $lesson))
            ->assertOk()
            ->assertSee(route('lessons.documents.preview', $lesson), false)
            ->assertSee(route('lessons.documents.stream', ['lesson' => $lesson, 'download' => 1]), false);

        $this->get(route('lessons.documents.preview', $lesson))
            ->assertOk()
            ->assertSee(__('lessons.preview_document'))
            ->assertSee('guide.pdf')
            ->assertSee(route('lessons.documents.stream', $lesson), false);

        $this->get(route('lessons.documents.stream', $lesson))
            ->assertOk()
            ->assertHeader('content-disposition', 'inline; filename="guide.pdf"');
    }

    public function test_published_file_block_can_be_previewed_without_downloading(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('lesson-files/slides.pdf', 'segment-file-content');

        $educator = User::factory()->educator()->create();
        $lesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Segment File Lesson',
            'type' => 'text',
            'duration_minutes' => 20,
            'is_published' => true,
            'is_free' => true,
            'segments' => [
                [
                    'id' => 1,
                    'type' => 'content',
                    'custom_name' => 'Downloads',
                    'blocks' => [
                        [
                            'id' => 2,
                            'type' => 'file',
                            'path' => 'lesson-files/slides.pdf',
                        ],
                    ],
                ],
            ],
        ]);

        $previewRoute = route('lessons.block-files.preview', [
            'lesson' => $lesson,
            'segment' => 1,
            'block' => 2,
        ]);
        $streamRoute = route('lessons.block-files.stream', [
            'lesson' => $lesson,
            'segment' => 1,
            'block' => 2,
        ]);

        $this->get(route('lessons.show', $lesson))
            ->assertOk()
            ->assertSee($previewRoute, false)
            ->assertSee($streamRoute . '?download=1', false);

        $this->get($previewRoute)
            ->assertOk()
            ->assertSee(__('lessons.preview_file'))
            ->assertSee('slides.pdf')
            ->assertSee($streamRoute, false);

        $this->get($streamRoute)
            ->assertOk()
            ->assertHeader('content-disposition', 'inline; filename="slides.pdf"');
    }

    public function test_unpublished_document_preview_is_forbidden_to_guests(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/private.pdf', 'private-preview-content');

        $educator = User::factory()->educator()->create();
        $lesson = Lesson::create([
            'user_id' => $educator->id,
            'title' => 'Private Document Lesson',
            'type' => 'document',
            'document_path' => 'documents/private.pdf',
            'duration_minutes' => 25,
            'is_published' => false,
            'is_free' => true,
            'segments' => [],
        ]);

        $this->get(route('lessons.documents.preview', $lesson))->assertForbidden();
        $this->get(route('lessons.documents.stream', $lesson))->assertForbidden();
    }
}
