@if($examSegments->count() > 0)
    <div class="exam-section">
        <div class="exam-head">
            <h2>{{ __('lessons.exam_mode') }}</h2>
        </div>

        @foreach($examSegments as $examIndex => $examSegment)
            @if(isset($examSegment['exam_settings']))
                <div class="exam-info-box">
                    <span>â„¹ï¸</span>
                    <div>
                        <strong>{{ __('lessons.exam_instructions') }}:</strong>
                        {{ __('lessons.exam_contains', ['count' => count($examSegment['questions'] ?? [])]) }}
                        {{ __('lessons.must_score', ['score' => $examSegment['exam_settings']['passing_score'] ?? 70]) }}
                        @if($examSegment['exam_settings']['time_limit'])
                            {{ __('lessons.available_time', ['time' => $examSegment['exam_settings']['time_limit']]) }}
                        @endif
                    </div>
                </div>
                <div class="exam-stats">
                    @if($examSegment['exam_settings']['time_limit'])
                        <div class="exam-stat">
                            <div class="exam-stat-lbl">{{ __('lessons.time_label') }}</div>
                            <div class="exam-stat-val">{{ $examSegment['exam_settings']['time_limit'] }}<span style="font-size:14px;">m</span></div>
                        </div>
                    @endif
                    <div class="exam-stat">
                        <div class="exam-stat-lbl">{{ __('lessons.pass_label') }}</div>
                        <div class="exam-stat-val">{{ $examSegment['exam_settings']['passing_score'] ?? 70 }}%</div>
                    </div>
                    <div class="exam-stat">
                        <div class="exam-stat-lbl">{{ __('lessons.questions_label') }}</div>
                        <div class="exam-stat-val">{{ count($examSegment['questions'] ?? []) }}</div>
                    </div>
                </div>
            @endif

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
        @endforeach
    </div>
@endif
