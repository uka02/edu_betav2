<div
    id="examTakingContainer"
    class="exam-overlay"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="examDialogTitle"
>
    <div class="exam-wrapper">
        <div class="exam-topbar">
            <div class="exam-tb-title" id="examDialogTitle">{{ __('lessons.exam_mode') }}: {{ $lesson->title }}</div>
            <div id="examTimerDisplay" class="exam-timer" style="display:none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <span id="timerText">00:00</span>
            </div>
            <div class="exam-progress-txt">
                <span id="currentQuestion">1</span> / <span id="totalQuestions">0</span>
            </div>
            <button class="exam-exit-btn" onclick="exitExam()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                {{ __('lessons.exit') }}
            </button>
        </div>

        <div id="examQuestionView" class="exam-q-card">
            <div class="exam-q-head">
                <div class="exam-q-num" id="qNumber">1</div>
                <div class="exam-q-text" id="qText" tabindex="-1" aria-live="polite">Question text</div>
                <span class="exam-q-type" id="qType">Multiple Choice</span>
            </div>
            <div class="exam-answers-list" id="answersContainer"></div>
            <div class="exam-btn-row">
                <button class="exam-nav-btn" id="prevBtn" onclick="previousQuestion()" disabled>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    {{ __('lessons.previous') }}
                </button>
                <button class="exam-nav-btn" id="nextBtn" onclick="nextQuestion()">
                    {{ __('lessons.next') }}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
                <button class="exam-submit-btn" id="submitBtn" onclick="submitExam()" style="display:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    {{ __('lessons.submit_exam') }}
                </button>
            </div>
        </div>

        <div id="examResultsView" class="exam-results">
            <div class="results-score" id="scoreDisplay">85%</div>
            <div class="results-status" id="statusDisplay" role="status" aria-live="polite">{{ __('lessons.you_passed') }}</div>
            <div class="results-grid">
                <div class="result-stat">
                    <div class="result-stat-lbl">{{ __('lessons.result_correct_answers') }}</div>
                    <div class="result-stat-val" id="correctCount">0/0</div>
                </div>
                <div class="result-stat">
                    <div class="result-stat-lbl">{{ __('lessons.result_score_needed') }}</div>
                    <div class="result-stat-val" id="passingScore">70%</div>
                </div>
                <div class="result-stat">
                    <div class="result-stat-lbl">{{ __('lessons.result_time_taken') }}</div>
                    <div class="result-stat-val" id="timeTaken">-</div>
                </div>
            </div>
            <div class="results-breakdown">
                <h3>{{ __('lessons.result_breakdown') }}</h3>
                <div id="resultsBreakdown"></div>
            </div>
            <div class="results-btns">
                <button class="btn-cta" onclick="retakeExam()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M23 4v6h-6"/>
                        <path d="M1 20v-6h6"/>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36M20.49 15a9 9 0 0 1-14.85 3.36"/>
                    </svg>
                    {{ __('lessons.retake_exam') }}
                </button>
                <a href="{{ route('lessons.show', $lesson) }}" class="btn-sec">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    {{ __('lessons.back_to_lesson') }}
                </a>
            </div>
        </div>
    </div>
</div>
