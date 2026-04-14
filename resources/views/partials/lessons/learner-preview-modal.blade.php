<div class="learner-preview-modal" id="learnerPreviewModal" aria-hidden="true">
    <div
        class="learner-preview-shell"
        role="dialog"
        aria-modal="true"
        aria-labelledby="learnerPreviewTitle"
    >
        <div class="learner-preview-topbar">
            <div class="learner-preview-topbar-copy">
                <div class="learner-preview-kicker">{{ __('lessons.learner_preview') }}</div>
                <div class="learner-preview-title" id="learnerPreviewTitle">{{ __('lessons.preview_current_draft') }}</div>
                <p class="learner-preview-subtitle">{{ __('lessons.preview_uses_draft_state') }}</p>
            </div>
            <div class="learner-preview-topbar-actions">
                <div class="learner-preview-status">{{ __('lessons.learner_view') }}</div>
                <button type="button" class="learner-preview-close" id="closeLearnerPreviewBtn" aria-label="{{ __('lessons.close_preview') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <div class="learner-preview-layout">
            <aside class="learner-preview-sidebar">
                <div class="learner-preview-stats" id="learnerPreviewStats"></div>
                <div class="learner-preview-outline-card">
                    <div class="learner-preview-outline-title">{{ __('lessons.course_structure') }}</div>
                    <div class="learner-preview-outline-list" id="learnerPreviewOutline"></div>
                </div>
            </aside>

            <div class="learner-preview-content">
                <div id="learnerPreviewHero"></div>
                <div class="learner-preview-sections" id="learnerPreviewSections"></div>
            </div>
        </div>
    </div>
</div>
