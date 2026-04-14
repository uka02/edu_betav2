<?php

namespace App\Http\Requests\Lessons;

class StoreLessonRequest extends LessonRequest
{
    protected function documentRule(): string
    {
        return 'required_if:type,document|nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480';
    }
}
