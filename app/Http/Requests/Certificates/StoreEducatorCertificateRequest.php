<?php

namespace App\Http\Requests\Certificates;

use Illuminate\Foundation\Http\FormRequest;

class StoreEducatorCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'learner_email' => ['required', 'email', 'exists:users,email'],
            'lesson_id' => ['required', 'integer', 'exists:lessons,id'],
            'exam_index' => ['nullable', 'integer', 'min:0'],
            'issued_at' => ['nullable', 'date'],
            'exam_title' => ['nullable', 'string', 'max:255'],
            'score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'validation_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
