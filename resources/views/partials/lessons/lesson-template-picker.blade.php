<div class="lesson-template-panel">
    <div class="lesson-template-panel-header">
        <div>
            <div class="lesson-template-panel-kicker">{{ __('lessons.lesson_templates') }}</div>
            <p class="lesson-template-panel-copy">{{ __('lessons.lesson_templates_hint') }}</p>
        </div>
    </div>

    <div class="lesson-template-grid">
        <button type="button" class="lesson-template-card" data-lesson-template="video">
            <span class="lesson-template-icon">V</span>
            <span class="lesson-template-name">{{ __('lessons.template_video_lesson') }}</span>
            <span class="lesson-template-desc">{{ __('lessons.template_video_lesson_desc') }}</span>
        </button>

        <button type="button" class="lesson-template-card" data-lesson-template="reading">
            <span class="lesson-template-icon">R</span>
            <span class="lesson-template-name">{{ __('lessons.template_reading_lesson') }}</span>
            <span class="lesson-template-desc">{{ __('lessons.template_reading_lesson_desc') }}</span>
        </button>

        <button type="button" class="lesson-template-card" data-lesson-template="quiz">
            <span class="lesson-template-icon">Q</span>
            <span class="lesson-template-name">{{ __('lessons.template_quiz_lesson') }}</span>
            <span class="lesson-template-desc">{{ __('lessons.template_quiz_lesson_desc') }}</span>
        </button>

        <button type="button" class="lesson-template-card" data-lesson-template="mixed">
            <span class="lesson-template-icon">M</span>
            <span class="lesson-template-name">{{ __('lessons.template_mixed_lesson') }}</span>
            <span class="lesson-template-desc">{{ __('lessons.template_mixed_lesson_desc') }}</span>
        </button>
    </div>
</div>
