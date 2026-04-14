<?php

namespace App\Http\Requests\Lessons;

class UpdateLessonRequest extends LessonRequest
{
    protected function documentRule(): string
    {
        return 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480';
    }
}
