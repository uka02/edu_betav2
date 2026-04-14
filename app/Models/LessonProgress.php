<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'watched_seconds',
        'last_position_seconds',
        'progress_percent',
        'progress_state',
        'last_viewed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'last_viewed_at' => 'datetime',
            'completed_at' => 'datetime',
            'progress_state' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
