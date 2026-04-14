<?php

namespace App\Http\Requests\Lessons;

use Illuminate\Foundation\Http\FormRequest;

abstract class LessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'subject' => 'nullable|in:' . implode(',', \App\Models\Lesson::validSubjectInputs()),
            'type' => 'required|in:video,text,document',
            'video_url' => 'required_if:type,video|nullable|url',
            'document' => $this->documentRule(),
            'thumbnail' => 'nullable|image|mimes:png,jpg,jpeg,gif|max:5120',
            'duration_hours' => 'nullable|integer|min:0|max:99',
            'duration_minutes' => 'nullable|integer|min:0|max:59',
            'difficulty' => 'nullable|in:beginner,intermediate,advanced',
            'is_published' => 'nullable|boolean',
            'is_free' => 'nullable|boolean',
            'segments' => 'nullable|array',
            'segments.*.custom_name' => 'nullable|string|max:255',
            'segments.*.blocks' => 'nullable|array',
            'segments.*.blocks.*.type' => 'required_with:segments.*.blocks|in:text,subheading,image,video,file,quiz,callout,code,divider',
            'segments.*.blocks.*.content' => 'nullable|string',
            'segments.*.blocks.*.image' => 'nullable|image|mimes:png,jpg,jpeg,gif|max:5120',
            'segments.*.blocks.*.file' => 'nullable|file|max:20480',
            'segments.*.blocks.*.callout_type' => 'nullable|in:info,warning,success,danger',
            'segments.*.blocks.*.language' => 'nullable|string|max:50',
            'segments.*.blocks.*.question' => 'nullable|string|max:1000',
            'segments.*.blocks.*.answers' => 'nullable|array',
            'segments.*.blocks.*.answers.*' => 'nullable|string|max:500',
            'segments.*.blocks.*.correct_answer' => 'nullable',
            'segments.*.exam_settings.time_limit' => 'nullable|integer|min:0',
            'segments.*.exam_settings.passing_score' => 'nullable|integer|min:0|max:100',
            'segments.*.questions' => 'nullable|array',
            'segments.*.questions.*.type' => 'required_with:segments.*.questions|in:multiple_choice,true_false,short_answer',
            'segments.*.questions.*.question' => 'required_with:segments.*.questions|string|max:1000',
            'segments.*.questions.*.correct_answer' => 'required_with:segments.*.questions',
            'segments.*.questions.*.answers' => 'nullable|array',
            'segments.*.questions.*.answers.*' => 'nullable|string|max:500',
            'segments.*.questions.*.case_sensitive' => 'nullable|boolean',
        ];
    }

    abstract protected function documentRule(): string;
}
