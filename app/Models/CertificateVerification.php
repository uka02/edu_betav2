<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class CertificateVerification extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'reviewed_by_user_id',
        'title',
        'passing_score',
        'issuer_name',
        'document_path',
        'original_filename',
        'status',
        'notes',
        'review_notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'passing_score' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
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

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function scopeCertificationRequests(Builder $query)
    {
        if (! Schema::hasColumn($query->getModel()->getTable(), 'lesson_id')) {
            return $query;
        }

        return $query->whereNotNull('lesson_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
