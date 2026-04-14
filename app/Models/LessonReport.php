<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonReport extends Model
{
    public const REASON_INACCURATE = 'inaccurate';
    public const REASON_BROKEN = 'broken';
    public const REASON_COPYRIGHT = 'copyright';
    public const REASON_INAPPROPRIATE = 'inappropriate';
    public const REASON_SPAM = 'spam';
    public const REASON_OTHER = 'other';

    protected $fillable = [
        'lesson_id',
        'user_id',
        'reason',
        'details',
    ];

    public static function reasonOptions(): array
    {
        return [
            self::REASON_INACCURATE,
            self::REASON_BROKEN,
            self::REASON_COPYRIGHT,
            self::REASON_INAPPROPRIATE,
            self::REASON_SPAM,
            self::REASON_OTHER,
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
