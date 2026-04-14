<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_LEARNER = 'learner';
    public const ROLE_EDUCATOR = 'educator';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'email',
        'username',
        'role',
        'password',
        'google_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function availableRoles(): array
    {
        return [
            self::ROLE_LEARNER,
            self::ROLE_EDUCATOR,
        ];
    }

    public function isLearner(): bool
    {
        return $this->role === self::ROLE_LEARNER;
    }

    public function isEducator(): bool
    {
        return $this->role === self::ROLE_EDUCATOR;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function examAttempts()
    {
        return $this->hasMany(LessonExamAttempt::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function lessonFeedbackEntries()
    {
        return $this->hasMany(LessonFeedback::class);
    }

    public function lessonReports()
    {
        return $this->hasMany(LessonReport::class);
    }

    public function certificateVerificationRequests()
    {
        return $this->hasMany(CertificateVerification::class);
    }

    public function reviewedCertificateVerificationRequests()
    {
        return $this->hasMany(CertificateVerification::class, 'reviewed_by_user_id');
    }
}
