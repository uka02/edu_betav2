@php
    $lessonEngagementEnabled = $lessonEngagementEnabled ?? true;
    $structuredLessonFeedbackEnabled = $structuredLessonFeedbackEnabled ?? false;
    $canManageLesson = $canManageLesson ?? false;
    $canSubmitLessonFeedback = $lessonEngagementEnabled && $isAuthenticatedViewer && ! $canManageLesson && $lesson->is_published;
    $showGuestLessonFeedbackPrompt = $lessonEngagementEnabled && ! $isAuthenticatedViewer && $lesson->is_published;
    $reportReasonOptions = \App\Models\LessonReport::reasonOptions();
    $selectedRating = old('rating', $currentUserFeedback?->rating);
    $selectedReportReason = old('reason', $currentUserReport?->reason);
    $feedbackTextValue = old('feedback', $currentUserFeedback?->feedback);
    $positiveFeedbackTextValue = old('positive_feedback', $currentUserFeedback?->positive_feedback ?? ($structuredLessonFeedbackEnabled ? $currentUserFeedback?->feedback : null));
    $negativeFeedbackTextValue = old('negative_feedback', $currentUserFeedback?->negative_feedback);
    $reportDetailsValue = old('details', $currentUserReport?->details);
@endphp

<section class="engagement-section" id="lesson-feedback">
    <div class="engagement-head">
        <div>
            <div class="engagement-kicker">{{ __('lessons.lesson_feedback_section') }}</div>
            <h2 class="engagement-title">{{ $canManageLesson ? __('lessons.lesson_feedback_overview') : __('lessons.share_lesson_feedback') }}</h2>
            <p class="engagement-copy">
                {{ $canManageLesson ? __('lessons.lesson_feedback_owner_copy') : __('lessons.lesson_feedback_learner_copy') }}
            </p>
        </div>

        <div class="engagement-summary">
            <div class="engagement-summary-card">
                <div class="engagement-summary-value">
                    {{ $feedbackAverageRating !== null ? number_format($feedbackAverageRating, 1) . '/5' : '--' }}
                </div>
                <div class="engagement-summary-label">{{ __('lessons.average_rating') }}</div>
            </div>
            <div class="engagement-summary-card">
                <div class="engagement-summary-value">{{ $feedbackCount }}</div>
                <div class="engagement-summary-label">{{ __('lessons.feedback_count') }}</div>
            </div>
            @if($canManageLesson)
                <div class="engagement-summary-card">
                    <div class="engagement-summary-value">{{ $reportCount }}</div>
                    <div class="engagement-summary-label">{{ __('lessons.report_count') }}</div>
                </div>
            @endif
        </div>
    </div>

    @if(! $lessonEngagementEnabled)
        <div class="engagement-grid single">
            <article class="engagement-card">
                <div class="engagement-card-head">
                    <h3>{{ __('lessons.lesson_engagement_unavailable') }}</h3>
                </div>
                <p class="engagement-card-copy">{{ __('lessons.lesson_engagement_unavailable_copy') }}</p>
            </article>
        </div>
    @elseif($canSubmitLessonFeedback)
        <div class="engagement-grid">
            <article class="engagement-card">
                <div class="engagement-card-head">
                    <h3>{{ __('lessons.rate_this_lesson') }}</h3>
                    @if($currentUserFeedback)
                        <span class="engagement-badge">{{ __('lessons.feedback_last_saved') }}</span>
                    @endif
                </div>
                <p class="engagement-card-copy">{{ __('lessons.rate_this_lesson_copy') }}</p>

                <form action="{{ route('lessons.feedback.store', $lesson) }}" method="POST" class="engagement-form">
                    @csrf
                    <div>
                        <label class="engagement-label">{{ __('lessons.your_rating') }}</label>
                        <div class="rating-scale" role="radiogroup" aria-label="{{ __('lessons.your_rating') }}">
                            @for($rating = 1; $rating <= 5; $rating++)
                                <label class="rating-option">
                                    <input type="radio" name="rating" value="{{ $rating }}" {{ (string) $selectedRating === (string) $rating ? 'checked' : '' }}>
                                    <span>{{ $rating }} {{ __('lessons.star_rating_suffix') }}</span>
                                </label>
                            @endfor
                        </div>
                        @if($errors->feedback->has('rating'))
                            <div class="engagement-error">{{ $errors->feedback->first('rating') }}</div>
                        @endif
                    </div>

                    @if($structuredLessonFeedbackEnabled)
                        <div>
                            <label for="lessonPositiveFeedbackText" class="engagement-label">{{ __('lessons.positive_feedback') }}</label>
                            <textarea
                                id="lessonPositiveFeedbackText"
                                name="positive_feedback"
                                class="engagement-textarea"
                                rows="4"
                                placeholder="{{ __('lessons.positive_feedback_placeholder') }}"
                            >{{ $positiveFeedbackTextValue }}</textarea>
                            @if($errors->feedback->has('positive_feedback'))
                                <div class="engagement-error">{{ $errors->feedback->first('positive_feedback') }}</div>
                            @endif
                        </div>

                        <div>
                            <label for="lessonNegativeFeedbackText" class="engagement-label">{{ __('lessons.negative_feedback') }}</label>
                            <textarea
                                id="lessonNegativeFeedbackText"
                                name="negative_feedback"
                                class="engagement-textarea"
                                rows="4"
                                placeholder="{{ __('lessons.negative_feedback_placeholder') }}"
                            >{{ $negativeFeedbackTextValue }}</textarea>
                            @if($errors->feedback->has('negative_feedback'))
                                <div class="engagement-error">{{ $errors->feedback->first('negative_feedback') }}</div>
                            @endif
                        </div>
                    @else
                        <div>
                            <label for="lessonFeedbackText" class="engagement-label">{{ __('lessons.your_feedback') }}</label>
                            <textarea
                                id="lessonFeedbackText"
                                name="feedback"
                                class="engagement-textarea"
                                rows="5"
                                placeholder="{{ __('lessons.feedback_placeholder') }}"
                            >{{ $feedbackTextValue }}</textarea>
                            @if($errors->feedback->has('feedback'))
                                <div class="engagement-error">{{ $errors->feedback->first('feedback') }}</div>
                            @endif
                        </div>
                    @endif

                    <button type="submit" class="engagement-submit">
                        {{ $currentUserFeedback ? __('lessons.update_feedback') : __('lessons.submit_feedback') }}
                    </button>
                </form>
            </article>

            <article class="engagement-card">
                <div class="engagement-card-head">
                    <h3>{{ __('lessons.report_lesson') }}</h3>
                    @if($currentUserReport)
                        <span class="engagement-badge">{{ __('lessons.report_already_sent') }}</span>
                    @endif
                </div>
                <p class="engagement-card-copy">{{ __('lessons.report_lesson_copy') }}</p>

                <form action="{{ route('lessons.report.store', $lesson) }}" method="POST" class="engagement-form">
                    @csrf
                    <div>
                        <label for="lessonReportReason" class="engagement-label">{{ __('lessons.report_reason') }}</label>
                        <select id="lessonReportReason" name="reason" class="engagement-select">
                            <option value="">{{ __('lessons.report_reason_placeholder') }}</option>
                            @foreach($reportReasonOptions as $reasonOption)
                                <option value="{{ $reasonOption }}" {{ $selectedReportReason === $reasonOption ? 'selected' : '' }}>
                                    {{ __('lessons.report_reason_' . $reasonOption) }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->report->has('reason'))
                            <div class="engagement-error">{{ $errors->report->first('reason') }}</div>
                        @endif
                    </div>

                    <div>
                        <label for="lessonReportDetails" class="engagement-label">{{ __('lessons.report_details') }}</label>
                        <textarea
                            id="lessonReportDetails"
                            name="details"
                            class="engagement-textarea"
                            rows="5"
                            placeholder="{{ __('lessons.report_details_placeholder') }}"
                        >{{ $reportDetailsValue }}</textarea>
                        @if($errors->report->has('details'))
                            <div class="engagement-error">{{ $errors->report->first('details') }}</div>
                        @endif
                    </div>

                    <button type="submit" class="engagement-submit muted">
                        {{ $currentUserReport ? __('lessons.update_report') : __('lessons.submit_report') }}
                    </button>
                </form>
            </article>
        </div>
    @elseif($showGuestLessonFeedbackPrompt)
        <div class="engagement-grid single">
            <article class="engagement-card">
                <div class="engagement-card-head">
                    <h3>{{ __('lessons.sign_in_to_share_feedback') }}</h3>
                </div>
                <p class="engagement-card-copy">{{ __('lessons.sign_in_to_share_feedback_copy') }}</p>
                <div class="engagement-actions">
                    <a href="{{ route('login') }}" class="engagement-submit">{{ __('auth.sign_in') }}</a>
                    <a href="{{ route('signup') }}" class="engagement-ghost">{{ __('auth.create_account') }}</a>
                </div>
            </article>
        </div>
    @elseif($canManageLesson)
        @if(! $lesson->is_published)
            <div class="engagement-grid single">
                <article class="engagement-card">
                    <div class="engagement-card-head">
                        <h3>{{ __('lessons.publish_to_collect_feedback') }}</h3>
                    </div>
                    <p class="engagement-card-copy">{{ __('lessons.publish_to_collect_feedback_copy') }}</p>
                </article>
            </div>
        @else
            <div class="engagement-grid">
                <article class="engagement-card">
                    <div class="engagement-card-head">
                        <h3>{{ __('lessons.received_feedback') }}</h3>
                        <span class="engagement-badge">{{ $feedbackCount }}</span>
                    </div>
                    @if($recentLessonFeedback->isNotEmpty())
                        <div class="engagement-list">
                            @foreach($recentLessonFeedback as $feedbackEntry)
                                <div class="engagement-list-item">
                                    <div class="engagement-list-head">
                                        <strong>{{ $feedbackEntry->user?->name ?? __('lessons.learner_view') }}</strong>
                                        <span>{{ $feedbackEntry->rating }}/5</span>
                                    </div>
                                    <div class="engagement-list-meta">{{ optional($feedbackEntry->created_at)->format('Y-m-d H:i') }}</div>
                                    @php
                                        $positiveFeedbackText = $feedbackEntry->positive_feedback;
                                        $negativeFeedbackText = $feedbackEntry->negative_feedback;
                                        $legacyFeedbackText = $feedbackEntry->feedback;
                                    @endphp

                                    @if(filled($positiveFeedbackText) || filled($negativeFeedbackText))
                                        <div class="engagement-feedback-stack">
                                            @if(filled($positiveFeedbackText))
                                                <div class="engagement-feedback-note positive">
                                                    <div class="engagement-feedback-note-label">{{ __('lessons.positive_feedback') }}</div>
                                                    <div class="engagement-feedback-note-copy">{{ $positiveFeedbackText }}</div>
                                                </div>
                                            @endif

                                            @if(filled($negativeFeedbackText))
                                                <div class="engagement-feedback-note negative">
                                                    <div class="engagement-feedback-note-label">{{ __('lessons.negative_feedback') }}</div>
                                                    <div class="engagement-feedback-note-copy">{{ $negativeFeedbackText }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    @elseif(filled($legacyFeedbackText))
                                        <div class="engagement-feedback-note neutral">
                                            <div class="engagement-feedback-note-label">{{ __('lessons.general_feedback') }}</div>
                                            <div class="engagement-feedback-note-copy">{{ $legacyFeedbackText }}</div>
                                        </div>
                                    @else
                                        <p class="engagement-list-copy">
                                            {{ __('lessons.feedback_without_comment') }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="engagement-empty">{{ __('lessons.no_feedback_yet') }}</div>
                    @endif
                </article>

                <article class="engagement-card">
                    <div class="engagement-card-head">
                        <h3>{{ __('lessons.received_reports') }}</h3>
                        <span class="engagement-badge danger">{{ $reportCount }}</span>
                    </div>
                    @if($recentLessonReports->isNotEmpty())
                        <div class="engagement-list">
                            @foreach($recentLessonReports as $reportEntry)
                                <div class="engagement-list-item">
                                    <div class="engagement-list-head">
                                        <strong>{{ $reportEntry->user?->name ?? __('lessons.learner_view') }}</strong>
                                        <span class="reason-pill">{{ __('lessons.report_reason_' . $reportEntry->reason) }}</span>
                                    </div>
                                    <div class="engagement-list-meta">{{ optional($reportEntry->created_at)->format('Y-m-d H:i') }}</div>
                                    <p class="engagement-list-copy">{{ $reportEntry->details }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="engagement-empty">{{ __('lessons.no_reports_yet') }}</div>
                    @endif
                </article>
            </div>
        @endif
    @endif
</section>
