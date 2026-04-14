<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use SoftDeletes;

    public const SUBJECT_ARCHITECTURE = 'architecture';
    public const SUBJECT_ART_CULTURE = 'art_culture';
    public const SUBJECT_ARTIFICIAL_INTELLIGENCE = 'artificial_intelligence';
    public const SUBJECT_BIOLOGY_LIFE_SCIENCES = 'biology_life_sciences';
    public const SUBJECT_BUSINESS_MANAGEMENT = 'business_management';
    public const SUBJECT_CHEMISTRY = 'chemistry';
    public const SUBJECT_COMMUNICATION = 'communication';
    public const SUBJECT_COMPUTER_SCIENCE = 'computer_science';
    public const SUBJECT_DATA_ANALYSIS_STATISTICS = 'data_analysis_statistics';
    public const SUBJECT_DESIGN = 'design';
    public const SUBJECT_ECONOMICS_FINANCE = 'economics_finance';
    public const SUBJECT_EDUCATION_TEACHER_TRAINING = 'education_teacher_training';
    public const SUBJECT_ELECTRONICS = 'electronics';
    public const SUBJECT_ENERGY_EARTH_SCIENCES = 'energy_earth_sciences';
    public const SUBJECT_ENGINEERING = 'engineering';
    public const SUBJECT_ENVIRONMENTAL_STUDIES = 'environmental_studies';
    public const SUBJECT_ETHICS = 'ethics';
    public const SUBJECT_FOOD_NUTRITION = 'food_nutrition';
    public const SUBJECT_HEALTH_SAFETY = 'health_safety';
    public const SUBJECT_HISTORY = 'history';
    public const SUBJECT_HUMANITIES = 'humanities';
    public const SUBJECT_LANGUAGE = 'language';
    public const SUBJECT_LAW = 'law';
    public const SUBJECT_LITERATURE = 'literature';
    public const SUBJECT_MATH = 'math';
    public const SUBJECT_MEDICINE = 'medicine';
    public const SUBJECT_MUSIC = 'music';
    public const SUBJECT_PHILANTHROPY = 'philanthropy';
    public const SUBJECT_PHILOSOPHY_ETHICS = 'philosophy_ethics';
    public const SUBJECT_PHYSICS = 'physics';
    public const SUBJECT_SCIENCE = 'science';
    public const SUBJECT_SOCIAL_SCIENCES = 'social_sciences';
    public const SUBJECT_SUSTAINABILITY = 'sustainability';

    // Legacy topic values kept for compatibility with existing lessons.
    public const SUBJECT_CYBERSECURITY = 'cybersecurity';
    public const SUBJECT_NETWORKING = 'networking';
    public const SUBJECT_PYTHON = 'python';
    public const SUBJECT_IT_ESSENTIALS = 'it_essentials';
    public const SUBJECT_CAREER_READINESS = 'career_readiness';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'subject',
        'type',
        'content',
        'segments',
        'content_blocks',
        'video_url',
        'document_path',
        'thumbnail',
        'duration',
        'duration_minutes',
        'difficulty',
        'is_published',
        'is_free',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_free' => 'boolean',
        'segments' => 'array',
        'content_blocks' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            $lesson->subject ??= self::defaultSubject();

            if (empty($lesson->slug)) {
                $lesson->slug = self::generateUniqueSlug($lesson->title);
            }
        });
    }

    public static function subjectOptions(): array
    {
        return [
            self::SUBJECT_ARCHITECTURE,
            self::SUBJECT_ART_CULTURE,
            self::SUBJECT_ARTIFICIAL_INTELLIGENCE,
            self::SUBJECT_BIOLOGY_LIFE_SCIENCES,
            self::SUBJECT_BUSINESS_MANAGEMENT,
            self::SUBJECT_CHEMISTRY,
            self::SUBJECT_COMMUNICATION,
            self::SUBJECT_COMPUTER_SCIENCE,
            self::SUBJECT_DATA_ANALYSIS_STATISTICS,
            self::SUBJECT_DESIGN,
            self::SUBJECT_ECONOMICS_FINANCE,
            self::SUBJECT_EDUCATION_TEACHER_TRAINING,
            self::SUBJECT_ELECTRONICS,
            self::SUBJECT_ENERGY_EARTH_SCIENCES,
            self::SUBJECT_ENGINEERING,
            self::SUBJECT_ENVIRONMENTAL_STUDIES,
            self::SUBJECT_ETHICS,
            self::SUBJECT_FOOD_NUTRITION,
            self::SUBJECT_HEALTH_SAFETY,
            self::SUBJECT_HISTORY,
            self::SUBJECT_HUMANITIES,
            self::SUBJECT_LANGUAGE,
            self::SUBJECT_LAW,
            self::SUBJECT_LITERATURE,
            self::SUBJECT_MATH,
            self::SUBJECT_MEDICINE,
            self::SUBJECT_MUSIC,
            self::SUBJECT_PHILANTHROPY,
            self::SUBJECT_PHILOSOPHY_ETHICS,
            self::SUBJECT_PHYSICS,
            self::SUBJECT_SCIENCE,
            self::SUBJECT_SOCIAL_SCIENCES,
            self::SUBJECT_SUSTAINABILITY,
        ];
    }

    public static function defaultSubject(): string
    {
        return self::SUBJECT_COMPUTER_SCIENCE;
    }

    public static function legacySubjectAliases(): array
    {
        return [
            self::SUBJECT_CYBERSECURITY => self::SUBJECT_COMPUTER_SCIENCE,
            self::SUBJECT_NETWORKING => self::SUBJECT_COMPUTER_SCIENCE,
            self::SUBJECT_PYTHON => self::SUBJECT_COMPUTER_SCIENCE,
            self::SUBJECT_IT_ESSENTIALS => self::SUBJECT_COMPUTER_SCIENCE,
            self::SUBJECT_CAREER_READINESS => self::SUBJECT_BUSINESS_MANAGEMENT,
        ];
    }

    public static function validSubjectInputs(): array
    {
        return array_values(array_unique([
            ...self::subjectOptions(),
            ...array_keys(self::legacySubjectAliases()),
        ]));
    }

    public static function normalizeSubject(?string $subject): string
    {
        $value = trim((string) $subject);

        if ($value === '') {
            return self::defaultSubject();
        }

        if (in_array($value, self::subjectOptions(), true)) {
            return $value;
        }

        return self::legacySubjectAliases()[$value] ?? self::defaultSubject();
    }

    public static function subjectFilterValues(?string $subject): array
    {
        $subject = trim((string) $subject);

        if ($subject === '' || ! in_array($subject, self::validSubjectInputs(), true)) {
            return [];
        }

        $canonicalSubject = self::normalizeSubject($subject);
        $legacySubjects = array_keys(array_filter(
            self::legacySubjectAliases(),
            static fn (string $mappedSubject): bool => $mappedSubject === $canonicalSubject
        ));

        return array_values(array_unique([
            $canonicalSubject,
            ...$legacySubjects,
        ]));
    }

    public static function generateUniqueSlug(?string $title): string
    {
        $baseSlug = Str::slug((string) $title);

        if ($baseSlug === '') {
            $baseSlug = 'lesson';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (self::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function examAttempts()
    {
        return $this->hasMany(LessonExamAttempt::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function certificateRequests()
    {
        return $this->hasMany(CertificateVerification::class);
    }

    public function progressRecords()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function feedbackEntries()
    {
        return $this->hasMany(LessonFeedback::class);
    }

    public function reports()
    {
        return $this->hasMany(LessonReport::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeBySubject($query, ?string $subject)
    {
        $subject = trim((string) $subject);

        if ($subject === '') {
            return $query;
        }

        $filterValues = self::subjectFilterValues($subject);

        if ($filterValues === []) {
            return $query;
        }

        return $query->whereIn('subject', $filterValues);
    }

    public function getNormalizedSubjectAttribute(): string
    {
        return self::normalizeSubject($this->subject);
    }

    // Soft delete scopes
    public function scopeTrashed($query)
    {
        return $query->onlyTrashed();
    }

    public function scopeWithTrashed($query)
    {
        return $query->withTrashed();
    }
}
