@php
    $quizId = $quizId ?? ($block['id'] ?? 'quiz-block');
    $progressKey = $progressKey ?? null;
    $blockType = $block['type'] ?? null;
@endphp

@if($blockType === 'text')
    <div class="cb-text js-track-block" data-progress-key="{{ $progressKey }}" data-progress-kind="block">{{ $block['content'] ?? '' }}</div>
@elseif($blockType === 'subheading')
    <h3 class="cb-subheading">{{ $block['content'] ?? '' }}</h3>
@elseif($blockType === 'image' && isset($block['path']))
    <div class="js-track-block" data-progress-key="{{ $progressKey }}" data-progress-kind="block">
        <img src="{{ Storage::url($block['path']) }}" alt="{{ $block['caption'] ?? '' }}" class="cb-image">
        @if(!empty($block['caption']))
            <p class="cb-caption">{{ $block['caption'] }}</p>
        @endif
    </div>
@elseif($blockType === 'video' && !empty($block['content']))
    @php
        $blockVideoUrl = $block['content'];
        $blockVideoMetadata = \App\Support\VideoEmbed::metadata($blockVideoUrl);
    @endphp
    @if($blockVideoMetadata)
        <div class="cb-video">
            <iframe
                id="video-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', (string) $progressKey) }}"
                src="{{ $blockVideoMetadata['embed_url'] }}"
                class="js-track-video"
                data-progress-key="{{ $progressKey }}"
                data-progress-kind="video"
                data-video-provider="{{ $blockVideoMetadata['provider'] }}"
                data-video-id="{{ $blockVideoMetadata['video_id'] }}"
                frameborder="0"
                allow="autoplay; fullscreen"
                allowfullscreen
            ></iframe>
        </div>
        <div class="cb-video-actions">
            <a href="{{ $blockVideoUrl }}" class="video-source-link" target="_blank" rel="noopener noreferrer">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 3h7v7"></path>
                    <path d="M10 14 21 3"></path>
                    <path d="M21 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                </svg>
                {{ __('lessons.open_video') }}
            </a>
        </div>
    @else
        <div>
            <a
                href="{{ $blockVideoUrl }}"
                class="doc-dl js-track-video-link"
                data-progress-key="{{ $progressKey }}"
                data-progress-kind="video"
                target="_blank"
                rel="noopener noreferrer"
            >
                {{ __('lessons.open_video') }}
            </a>
        </div>
    @endif
@elseif($blockType === 'file' && isset($block['path']))
    <div>
        <div class="doc-actions">
            <a
                href="{{ route('lessons.block-files.preview', ['lesson' => $lesson, 'segment' => $segmentId, 'block' => $blockId]) }}"
                class="doc-preview-link js-track-file-link"
                data-progress-key="{{ $progressKey }}"
                data-progress-kind="block"
                target="_blank"
                rel="noopener noreferrer"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                {{ __('lessons.preview_file') }}
            </a>
            <a
                href="{{ route('lessons.block-files.stream', ['lesson' => $lesson, 'segment' => $segmentId, 'block' => $blockId, 'download' => 1]) }}"
                class="doc-dl js-track-file-link"
                data-progress-key="{{ $progressKey }}"
                data-progress-kind="block"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                {{ __('lessons.download_file') }}
            </a>
        </div>
    </div>
@elseif($blockType === 'callout')
    <div class="cb-callout callout-{{ $block['callout_type'] ?? 'info' }} js-track-block" data-progress-key="{{ $progressKey }}" data-progress-kind="block">{{ $block['content'] ?? '' }}</div>
@elseif($blockType === 'code')
    <pre class="cb-code js-track-block" data-progress-key="{{ $progressKey }}" data-progress-kind="block">{{ $block['content'] ?? '' }}</pre>
@elseif($blockType === 'divider')
    <div class="cb-divider"></div>
@elseif($blockType === 'quiz')
    <div class="quiz-block js-track-quiz-block" data-quiz-id="{{ $quizId }}" data-progress-key="{{ $progressKey }}" data-progress-kind="block">
        <div class="quiz-q">{{ $block['question'] ?? '' }}</div>
        <div class="quiz-answers">
            @foreach($block['answers'] ?? [] as $idx => $answer)
                <label class="quiz-opt" data-answer-index="{{ $idx }}">
                    <input type="radio" name="quiz_{{ $quizId }}" value="{{ $idx }}" class="quiz-radio">
                    <span class="answer-letter">{{ chr(65 + $idx) }}</span>
                    <span class="answer-text">{{ $answer }}</span>
                </label>
            @endforeach
        </div>
        <input type="hidden" class="quiz-correct-answer" value="{{ $block['correct_answer'] ?? 0 }}">
        <button type="button" class="quiz-check-btn" onclick="checkQuizAnswer(@js($quizId))">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            {{ __('lessons.check_answer') }}
        </button>
        <div class="quiz-feedback" style="display:none;"></div>
    </div>
@endif
