@php
    $metaLabel = $metaLabel ?? null;
    $showStatus = $showStatus ?? false;
@endphp

<a href="{{ route('lessons.show', $lesson) }}" class="lcard">
    <div class="lc-thumb">
        @if($lesson->thumbnail)
            <img src="{{ Storage::url($lesson->thumbnail) }}" alt="{{ $lesson->title }}">
        @else
            <span class="lc-ph">{{ $lesson->type }}</span>
        @endif
        <span class="lc-tag">{{ ucfirst($lesson->type) }}</span>
    </div>
    <div class="lc-body">
        <div class="lc-meta">
            @if($metaLabel)
                <span class="lc-auth">{{ $metaLabel }}</span>
            @endif
            @if($showStatus)
                <span class="lc-st {{ $lesson->is_published ? 'st-pub' : 'st-drf' }}">
                    {{ $lesson->is_published ? __('lessons.published') : __('lessons.draft') }}
                </span>
            @endif
        </div>
        <div class="lc-title">{{ Str::limit($lesson->title, 52) }}</div>
        <div class="lc-foot">
            <span class="lc-dur">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                {{ $lesson->duration_minutes ?? '0' }} {{ __('lessons.min') ?? 'min' }}
            </span>
            <span class="lc-price {{ $lesson->is_free ? 'pr-free' : 'pr-paid' }}">
                {{ $lesson->is_free ? __('lessons.free') : __('lessons.paid') }}
            </span>
        </div>
    </div>
</a>
