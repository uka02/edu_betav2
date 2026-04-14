<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonFeedback extends Model
{
    protected $fillable = [
        'lesson_id',
        'user_id',
        'rating',
        'feedback',
        'positive_feedback',
        'negative_feedback',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
