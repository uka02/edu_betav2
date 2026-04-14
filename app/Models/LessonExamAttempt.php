<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonExamAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'lesson_id',
        'exam_index',
        'score',
        'passed',
        'correct_count',
        'total_questions',
        'time_taken',
        'answers',
        'results',
        'attempted_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'passed' => 'boolean',
        'answers' => 'array',
        'results' => 'array',
        'attempted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }
}
