@php
    $animationDelay = $animationDelay ?? null;
    $lessonSubject = \App\Models\Lesson::normalizeSubject($lesson->subject ?? \App\Models\Lesson::defaultSubject());
    $showLessonReports = $showLessonReports ?? false;
    $reportCount = (int) ($lesson->report_count ?? 0);
@endphp

<div class="lcard" @if($animationDelay !== null) style="animation-delay:{{ $animationDelay }}s;" @endif>
    <div class="lcard-thumb">
        @if($lesson->thumbnail)
            <img src="{{ Storage::url($lesson->thumbnail) }}" alt="{{ $lesson->title }}">
        @else
            <span class="lcard-ph">
                @if($lesson->type === 'video')
                @elseif($lesson->type === 'document')
                @else {{ strtoupper(substr($lesson->type ?? 'text', 0, 1)) }} @endif
            </span>
        @endif
        <span class="lcard-type">{{ ucfirst($lesson->type) }}</span>
        <span class="lcard-badge {{ $lesson->is_published ? 'lb-pub' : 'lb-dft' }}">
            {{ $lesson->is_published ? __('lessons.published') : __('lessons.draft') }}
        </span>
    </div>
    <div class="lcard-body">
        <div class="lcard-title">{{ $lesson->title }}</div>
        @if($showLessonOwner ?? false)
            <div style="font-size:12px;color:var(--muted);margin-bottom:10px;">
                {{ __('lessons.by') }} {{ $lesson->user?->name ?? '—' }}
            </div>
        @endif
        <div class="lcard-meta">
            <span class="lm-chip lm-diff">{{ __('lessons.subject_' . $lessonSubject) }}</span>
            <span class="lm-chip {{ $lesson->is_free ? 'lm-free' : 'lm-paid' }}">
                {{ $lesson->is_free ? __('lessons.free') : __('lessons.paid') }}
            </span>
            @if($showLessonReports)
                <span class="lm-chip" style="background:var(--red-soft);color:var(--red);">
                    {{ __('lessons.report_count') }}: {{ $reportCount }}
                </span>
            @endif
            @if($lesson->difficulty)
                <span class="lm-chip lm-diff">{{ __('lessons.' . $lesson->difficulty) }}</span>
            @endif
            @if($lesson->duration_minutes)
                <span class="lm-dur">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    {{ $lesson->duration_minutes }} {{ __('lessons.min') ?? 'min' }}
                </span>
            @endif
        </div>
        <div class="lcard-actions">
            <a href="{{ route('lessons.show', $lesson) }}" class="btn-sec">{{ __('lessons.view') }}</a>
            <a href="{{ route('lessons.edit', $lesson) }}" class="btn-sec">{{ __('lessons.edit') }}</a>
            @unless($lesson->is_published)
                <form action="{{ route('lessons.publish', $lesson) }}" method="POST" style="display:flex;flex:1;">
                    @csrf
                    <button type="submit" class="btn-cta" style="width:100%;justify-content:center;">
                        {{ __('lessons.publish') }}
                    </button>
                </form>
            @endunless
        </div>
    </div>
</div>
