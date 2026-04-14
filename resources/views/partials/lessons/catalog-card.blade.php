@php
    $isAuthenticatedViewer = auth()->check();
    $lessonSubject = \App\Models\Lesson::normalizeSubject($lesson->subject ?? \App\Models\Lesson::defaultSubject());
@endphp

<article class="catalog-card">
    <a href="{{ route('lessons.show', $lesson) }}" class="catalog-thumb">
        @if($lesson->thumbnail)
            <img src="{{ Storage::url($lesson->thumbnail) }}" alt="{{ $lesson->title }}">
        @else
            <span class="catalog-thumb-fallback">{{ strtoupper(substr($lessonSubject, 0, 1)) }}</span>
        @endif
        <span class="catalog-subject">{{ __('lessons.subject_' . $lessonSubject) }}</span>
        <span class="catalog-price {{ $lesson->is_free ? 'is-free' : 'is-paid' }}">
            {{ $lesson->is_free ? __('lessons.free') : __('lessons.paid') }}
        </span>
    </a>

    <div class="catalog-body">
        <div class="catalog-topline">
            <span>{{ $lesson->user?->name }}</span>
            @if($lesson->has_certificate)
                <span class="catalog-award">{{ __('lessons.earned_certificate') }}</span>
            @elseif(($lesson->exam_segment_count ?? 0) > 0)
                <span class="catalog-award">{{ __('lessons.certificate_available') }}</span>
            @endif
        </div>

        <a href="{{ route('lessons.show', $lesson) }}" class="catalog-title">{{ $lesson->title }}</a>

        <div class="catalog-meta">
            @if($lesson->difficulty)
                <span>{{ __('lessons.' . $lesson->difficulty) }}</span>
            @endif
            <span>{{ __('lessons.' . $lesson->type) }}</span>
            @if($lesson->duration_minutes)
                <span>{{ $lesson->duration_minutes }} {{ __('lessons.min') }}</span>
            @endif
        </div>

        <div class="catalog-stats">
            <div class="catalog-stat">
                <span class="catalog-stat-value">{{ $lesson->content_section_count ?? 1 }}</span>
                <span class="catalog-stat-label">{{ __('lessons.sections') }}</span>
            </div>
            <div class="catalog-stat">
                <span class="catalog-stat-value">{{ $lesson->exam_segment_count ?? 0 }}</span>
                <span class="catalog-stat-label">{{ __('lessons.exams') }}</span>
            </div>
            <div class="catalog-stat">
                <span class="catalog-stat-value">{{ $lesson->learner_count ?? 0 }}</span>
                <span class="catalog-stat-label">{{ __('lessons.tracks') }}</span>
            </div>
        </div>

        <div class="catalog-progress-shell">
            <div class="catalog-progress-head">
                <span>{{ __('lessons.lesson_progress') }}</span>
                <span>{{ (int) ($lesson->progress_percent ?? 0) }}%</span>
            </div>
            <div class="catalog-progress-track">
                <div class="catalog-progress-fill" style="width:{{ (int) ($lesson->progress_percent ?? 0) }}%;"></div>
            </div>
            <div class="catalog-progress-note">
                @if($lesson->last_opened_at)
                    {{ __('lessons.last_opened') }} {{ $lesson->last_opened_at->diffForHumans() }}
                @else
                    {{ __('lessons.track_progress') }}
                @endif
            </div>
        </div>

        <div class="catalog-actions">
            <a href="{{ route('lessons.show', $lesson) }}" class="catalog-btn-primary">
                {{ $isAuthenticatedViewer && ($lesson->has_started ?? false) ? __('lessons.continue_learning') : __('lessons.start_learning') }}
            </a>
            @if($isAuthenticatedViewer && $lesson->has_certificate)
                <a href="{{ route('certificates.index') }}" class="catalog-btn-secondary">{{ __('lessons.view_certificate') }}</a>
            @else
                <a href="{{ route('lessons.show', $lesson) }}#course-outline" class="catalog-btn-secondary">{{ __('lessons.learning_path') }}</a>
            @endif
        </div>
    </div>
</article>
