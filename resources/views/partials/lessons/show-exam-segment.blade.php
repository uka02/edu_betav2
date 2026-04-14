@php
    $examSegment = $examSegment ?? [];
    $examIndex = (int) ($examIndex ?? 0);
    $sectionId = $sectionId ?? ('exam-' . $examIndex);
    $examTitle = $examTitle ?? (trim((string) ($examSegment['custom_name'] ?? '')) ?: __('lessons.exam_index_label') . ' ' . ($examIndex + 1));
    $questionCount = count($examSegment['questions'] ?? []);
    $passingScore = $examSegment['exam_settings']['passing_score'] ?? 70;
    $timeLimit = $examSegment['exam_settings']['time_limit'] ?? null;
@endphp

<article class="segment-container{{ $isActive ? ' active' : '' }}" data-segment="{{ $sectionId }}" id="lesson-segment-{{ $sectionId }}">
    <div class="segment-panel-head">
        <h2 class="segment-header">{{ $examTitle }}</h2>
        <div class="segment-meta">{{ __('lessons.exam_mode') }}</div>
    </div>

    <div class="exam-info-box">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <div>
            <strong>{{ __('lessons.exam_instructions') }}:</strong>
            {{ __('lessons.exam_contains', ['count' => $questionCount]) }}
            {{ __('lessons.must_score', ['score' => $passingScore]) }}
            @if($timeLimit)
                {{ __('lessons.available_time', ['time' => $timeLimit]) }}
            @endif
        </div>
    </div>

    <div class="exam-stats">
        @if($timeLimit)
            <div class="exam-stat">
                <div class="exam-stat-lbl">{{ __('lessons.time_label') }}</div>
                <div class="exam-stat-val">{{ $timeLimit }}<span style="font-size:14px;">m</span></div>
            </div>
        @endif
        <div class="exam-stat">
            <div class="exam-stat-lbl">{{ __('lessons.pass_label') }}</div>
            <div class="exam-stat-val">{{ $passingScore }}%</div>
        </div>
        <div class="exam-stat">
            <div class="exam-stat-lbl">{{ __('lessons.questions_label') }}</div>
            <div class="exam-stat-val">{{ $questionCount }}</div>
        </div>
    </div>

    @if(Auth::check())
        <button class="start-exam-btn" onclick="startExam({{ $examIndex }})">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="5 3 19 12 5 21 5 3"/>
            </svg>
            {{ __('lessons.start_exam') }}
        </button>
    @else
        <div class="login-req">
            {{ __('lessons.login_required') }} <a href="{{ route('login') }}">{{ __('auth.login') }}</a> {{ __('lessons.to_start_exam') }}.
        </div>
    @endif
</article>
