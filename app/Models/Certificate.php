<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'issued_by_user_id',
        'lesson_id',
        'lesson_exam_attempt_id',
        'exam_index',
        'certificate_code',
        'issued_at',
        'validated_at',
        'validation_notes',
        'snapshot',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'validated_at' => 'datetime',
        'snapshot' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'certificate_code';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function attempt()
    {
        return $this->belongsTo(LessonExamAttempt::class, 'lesson_exam_attempt_id');
    }

    public function displayLearnerName(): string
    {
        return $this->normalizedSnapshotValue('learner_name', $this->user?->name);
    }

    public function displayLessonTitle(): string
    {
        return $this->normalizedSnapshotValue('lesson_title', $this->lesson?->title);
    }

    public function displayIssuerName(): string
    {
        return $this->normalizedSnapshotValue(
            'issuer_name',
            $this->issuer?->name ?? $this->lesson?->user?->name
        );
    }

    public function displayExamTitle(): string
    {
        $examTitle = $this->normalizedSnapshotValue('exam_title');

        if ($examTitle !== '') {
            return $examTitle;
        }

        return __('lessons.exam_index_label') . ' ' . ($this->exam_index + 1);
    }

    public function displayScore(): ?float
    {
        $score = data_get($this->snapshot ?? [], 'score', $this->attempt?->score);

        return is_numeric($score) ? (float) $score : null;
    }

    public function downloadFilename(string $extension = 'svg'): string
    {
        $segments = array_filter([
            $this->slugSegment($this->displayLearnerName()),
            $this->slugSegment($this->displayLessonTitle()),
            $this->slugSegment($this->certificate_code),
        ]);

        $basename = implode('-', $segments);

        if ($basename === '') {
            $basename = 'certificate';
        }

        return $basename . '.' . ltrim($extension, '.');
    }

    private function normalizedSnapshotValue(string $key, mixed $fallback = null): string
    {
        return trim((string) data_get($this->snapshot ?? [], $key, $fallback));
    }

    private function slugSegment(?string $value): string
    {
        return substr(Str::slug((string) $value), 0, 40);
    }
}
