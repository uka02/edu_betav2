<article class="segment-container{{ $isActive ? ' active' : '' }}" data-segment="{{ $sectionId }}" id="lesson-segment-{{ $sectionId }}">
    <div class="segment-panel-head">
        <h2 class="segment-header">{{ __('lessons.basic_info') }}</h2>
        <div class="segment-meta">{{ __('lessons.' . $lesson->type) }}</div>
    </div>

@php
    $lessonSubject = \App\Models\Lesson::normalizeSubject($lesson->subject ?? \App\Models\Lesson::defaultSubject());
@endphp

<div class="basic-info-grid">
        <div>
            @if($lesson->type === 'video' && $lesson->video_url)
                @php
                    $mainVideoMetadata = \App\Support\VideoEmbed::metadata($lesson->video_url);
                @endphp
                @if($mainVideoMetadata)
                    <div class="video-wrap">
                        <iframe
                            id="lesson-main-video-{{ $lesson->id }}"
                            src="{{ $mainVideoMetadata['embed_url'] }}"
                            class="js-track-video"
                            data-progress-key="main-video"
                            data-progress-kind="video"
                            data-video-provider="{{ $mainVideoMetadata['provider'] }}"
                            data-video-id="{{ $mainVideoMetadata['video_id'] }}"
                            frameborder="0"
                            allow="autoplay; fullscreen; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </div>
                    <div class="video-actions">
                        <a href="{{ $lesson->video_url }}" class="video-source-link" target="_blank" rel="noopener noreferrer">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 3h7v7"></path>
                                <path d="M10 14 21 3"></path>
                                <path d="M21 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            </svg>
                            {{ __('lessons.open_video') }}
                        </a>
                    </div>
                @else
                    <div class="basic-info-card">
                        <a
                            href="{{ $lesson->video_url }}"
                            class="doc-dl js-track-video-link"
                            data-progress-key="main-video"
                            data-progress-kind="video"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            {{ __('lessons.open_video') }}
                        </a>
                    </div>
                @endif
            @elseif($lesson->thumbnail)
                <div class="lthumb"><img src="{{ Storage::url($lesson->thumbnail) }}" alt="{{ $lesson->title }}"></div>
            @else
                <div class="basic-info-card">
                    <div class="basic-info-tags">
                        <span class="tag tag-blue">{{ __('lessons.' . $lesson->type) }}</span>
                        <span class="tag {{ $lesson->is_published ? 'tag-green' : 'tag-muted' }}">
                            {{ $lesson->is_published ? __('lessons.published') : __('lessons.draft') }}
                        </span>
                        @if($lesson->is_free)
                            <span class="tag tag-green">{{ __('lessons.free_badge') }}</span>
                        @else
                            <span class="tag tag-amber">{{ __('lessons.paid') }}</span>
                        @endif
                    </div>
                    <div class="segment-empty-note">
                        {{ __('lessons.lesson_progress') }}: {{ $lessonProgressPercent }}%
                    </div>
                </div>
            @endif
        </div>

        <div class="basic-info-side">
            <div class="basic-info-card">
                <div class="basic-info-list">
                    <div class="basic-info-item">
                        <span class="basic-info-item-label">{{ __('lessons.by') }}</span>
                        <span class="basic-info-item-value">{{ $lesson->user?->name ?? '-' }}</span>
                    </div>
                    <div class="basic-info-item">
                        <span class="basic-info-item-label">{{ __('lessons.type') }}</span>
                        <span class="basic-info-item-value">{{ __('lessons.' . $lesson->type) }}</span>
                    </div>
                    <div class="basic-info-item">
                        <span class="basic-info-item-label">{{ __('lessons.subject') }}</span>
                        <span class="basic-info-item-value">{{ __('lessons.subject_' . $lessonSubject) }}</span>
                    </div>
                    <div class="basic-info-item">
                        <span class="basic-info-item-label">{{ __('lessons.difficulty') }}</span>
                        <span class="basic-info-item-value">{{ $lesson->difficulty ? __('lessons.' . $lesson->difficulty) : '-' }}</span>
                    </div>
                    <div class="basic-info-item">
                        <span class="basic-info-item-label">{{ __('lessons.duration') }}</span>
                        <span class="basic-info-item-value">{{ $lesson->duration_minutes ? $lesson->duration_minutes . ' ' . __('lessons.min') : '-' }}</span>
                    </div>
                    <div class="basic-info-item">
                        <span class="basic-info-item-label">{{ __('lessons.is_free') }}</span>
                        <span class="basic-info-item-value">{{ $lesson->is_free ? __('lessons.free_badge') : __('lessons.paid') }}</span>
                    </div>
                    <div class="basic-info-item">
                        <span class="basic-info-item-label">{{ __('lessons.is_published') }}</span>
                        <span class="basic-info-item-value">{{ $lesson->is_published ? __('lessons.published') : __('lessons.draft') }}</span>
                    </div>
                </div>
            </div>

            @if($lesson->type === 'document' && $lesson->document_path)
                <div class="basic-info-card">
                    <div class="doc-actions">
                        <a
                            href="{{ route('lessons.documents.preview', $lesson) }}"
                            class="doc-preview-link js-track-file-link"
                            data-progress-key="main-document"
                            data-progress-kind="block"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            {{ __('lessons.preview_document') }}
                        </a>
                        <a
                            href="{{ route('lessons.documents.stream', ['lesson' => $lesson, 'download' => 1]) }}"
                            class="doc-dl js-track-file-link"
                            data-progress-key="main-document"
                            data-progress-kind="block"
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            {{ __('lessons.download_document') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</article>
