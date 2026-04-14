<?php

namespace App\Http\Requests\Certificates;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificateVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isEducator();
    }

    public function rules(): array
    {
        return [
            'lesson_id' => ['required', 'integer', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'passing_score' => ['required', 'integer', 'min:0', 'max:100'],
            'notes' => ['required', 'string', 'max:2000'],
            'document' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ];
    }
}
