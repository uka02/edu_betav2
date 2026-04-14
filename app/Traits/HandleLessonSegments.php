<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Trait HandleLessonSegments
 * 
 * Provides shared functionality for processing and managing lesson segments and blocks
 * across multiple controllers. Handles file uploads, validation, and cleanup.
 */
trait HandleLessonSegments
{
    /**
     * Process all segments from the request into a clean array for storage.
     * Handles file uploads (images/files) directly from segments[id][blocks][blockId][image|file].
     */
    private function processSegments(Request $request): array
    {
        $segments    = [];
        $segmentData = $request->input('segments', []);
        $filesData   = $request->file('segments', []);

        // Segment index 0 is basic info — skip it
        foreach ($segmentData as $segmentId => $segment) {
            if ((int)$segmentId === 0) continue;

            $processedSegment = [
                'id'          => (int)$segmentId,
                'custom_name' => $segment['custom_name'] ?? null,
                'type'        => isset($segment['exam_settings']) || isset($segment['questions']) ? 'exam' : 'content',
                'blocks'      => [],
            ];

            // --- Content blocks ---
            if (isset($segment['blocks']) && is_array($segment['blocks'])) {
                foreach ($segment['blocks'] as $blockId => $block) {
                    $processedBlock = [
                        'id'   => (int)$blockId,
                        'type' => $block['type'] ?? 'text',
                    ];

                    switch ($block['type']) {
                        case 'text':
                        case 'subheading':
                        case 'video':
                            $processedBlock['content'] = $block['content'] ?? '';
                            break;

                        case 'image':
                            // File is at segments[segmentId][blocks][blockId][image]
                            $uploadedImage = $filesData[$segmentId]['blocks'][$blockId]['image'] ?? null;
                            if ($uploadedImage && $uploadedImage->isValid()) {
                                $processedBlock['path'] = $uploadedImage->store('lesson-images', 'public');
                            } elseif (!empty($block['existing_path'])) {
                                $processedBlock['path'] = $block['existing_path'];
                            }
                            $processedBlock['caption'] = $block['content'] ?? '';
                            break;

                        case 'file':
                            // File is at segments[segmentId][blocks][blockId][file]
                            $uploadedFile = $filesData[$segmentId]['blocks'][$blockId]['file'] ?? null;
                            if ($uploadedFile && $uploadedFile->isValid()) {
                                $processedBlock['path'] = $uploadedFile->store('lesson-files', 'public');
                            } elseif (!empty($block['existing_path'])) {
                                $processedBlock['path'] = $block['existing_path'];
                            }
                            break;

                        case 'callout':
                            $processedBlock['content']      = $block['content'] ?? '';
                            $processedBlock['callout_type'] = $block['callout_type'] ?? 'info';
                            break;

                        case 'code':
                            $processedBlock['content']  = $block['content'] ?? '';
                            $processedBlock['language'] = $block['language'] ?? 'javascript';
                            break;

                        case 'divider':
                            // No additional data needed
                            break;

                        case 'quiz':
                            $processedBlock['question']       = $block['question'] ?? '';
                            $processedBlock['answers']        = array_values($block['answers'] ?? []);
                            $processedBlock['correct_answer'] = (int)($block['correct_answer'] ?? 0);
                            break;
                    }

                    $processedSegment['blocks'][] = $processedBlock;
                }
            }

            // --- Exam settings & questions ---
            if ($processedSegment['type'] === 'exam') {
                $processedSegment['exam_settings'] = [
                    'time_limit'    => (int)($segment['exam_settings']['time_limit'] ?? 0),
                    'passing_score' => (int)($segment['exam_settings']['passing_score'] ?? 60),
                ];

                $processedSegment['questions'] = [];

                if (isset($segment['questions']) && is_array($segment['questions'])) {
                    foreach ($segment['questions'] as $questionId => $question) {
                        $processedQuestion = [
                            'id'       => (int)$questionId,
                            'type'     => $question['type'] ?? 'multiple_choice',
                            'question' => $question['question'] ?? '',
                        ];

                        switch ($question['type']) {
                            case 'multiple_choice':
                                $processedQuestion['answers']        = array_values($question['answers'] ?? []);
                                $processedQuestion['correct_answer'] = (string)($question['correct_answer'] ?? '0');
                                break;

                            case 'true_false':
                                $processedQuestion['correct_answer'] = filter_var(
                                    $question['correct_answer'] ?? 'true',
                                    FILTER_VALIDATE_BOOLEAN
                                ) ? 'true' : 'false';
                                break;

                            case 'short_answer':
                                $processedQuestion['correct_answer'] = $question['correct_answer'] ?? '';
                                $processedQuestion['case_sensitive'] = filter_var(
                                    $question['case_sensitive'] ?? false,
                                    FILTER_VALIDATE_BOOLEAN
                                );
                                break;
                        }

                        $processedSegment['questions'][] = $processedQuestion;
                    }
                }
            }

            $segments[] = $processedSegment;
        }

        return $segments;
    }

    /**
     * Delete stored files that are no longer referenced after an update.
     */
    private function cleanupUnusedFiles(array $oldSegments, array $newSegmentData): void
    {
        // Collect all file paths still in use (kept via existing_path)
        $usedPaths = [];
        foreach ($newSegmentData as $segment) {
            if (isset($segment['blocks']) && is_array($segment['blocks'])) {
                foreach ($segment['blocks'] as $block) {
                    if (!empty($block['existing_path'])) {
                        $usedPaths[] = $block['existing_path'];
                    }
                }
            }
        }

        // Delete any old block files that are no longer referenced
        foreach ($oldSegments as $segment) {
            if (isset($segment['blocks']) && is_array($segment['blocks'])) {
                foreach ($segment['blocks'] as $block) {
                    if (
                        isset($block['path']) &&
                        in_array($block['type'], ['image', 'file']) &&
                        !in_array($block['path'], $usedPaths)
                    ) {
                        Storage::disk('public')->delete($block['path']);
                    }
                }
            }
        }
    }
}
