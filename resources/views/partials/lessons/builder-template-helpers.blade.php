const lessonTemplateCopy = {
    generatedTitles: {
        video: @js(__('lessons.template_generated_title_video', ['subject' => '__SUBJECT__'])),
        reading: @js(__('lessons.template_generated_title_reading', ['subject' => '__SUBJECT__'])),
        quiz: @js(__('lessons.template_generated_title_quiz', ['subject' => '__SUBJECT__'])),
        mixed: @js(__('lessons.template_generated_title_mixed', ['subject' => '__SUBJECT__'])),
    },
    focusBySubject: {
        cybersecurity: @js(__('lessons.template_focus_cybersecurity')),
        networking: @js(__('lessons.template_focus_networking')),
        python: @js(__('lessons.template_focus_python')),
        it_essentials: @js(__('lessons.template_focus_it_essentials')),
        career_readiness: @js(__('lessons.template_focus_career_readiness')),
    },
    sectionLabels: {
        overview: @js(__('lessons.template_section_overview')),
        reading: @js(__('lessons.template_section_reading')),
        practice: @js(__('lessons.template_section_practice')),
        assessment: @js(__('lessons.template_section_assessment')),
    },
    strings: {
        videoOverviewHeading: @js(__('lessons.template_video_overview_heading')),
        videoOverviewText: @js(__('lessons.template_video_overview_text', ['topic' => '__TOPIC__', 'subject' => '__SUBJECT__'])),
        videoWatchCallout: @js(__('lessons.template_video_watch_callout')),
        videoReflectionHeading: @js(__('lessons.template_video_reflection_heading')),
        videoReflectionText: @js(__('lessons.template_video_reflection_text', ['topic' => '__TOPIC__', 'subject' => '__SUBJECT__'])),
        videoReflectionCallout: @js(__('lessons.template_video_reflection_callout')),
        readingCoreHeading: @js(__('lessons.template_reading_core_heading')),
        readingCoreText: @js(__('lessons.template_reading_core_text', ['topic' => '__TOPIC__', 'subject' => '__SUBJECT__'])),
        readingPathHeading: @js(__('lessons.template_reading_path_heading')),
        readingPathText: @js(__('lessons.template_reading_path_text')),
        readingTakeawayCallout: @js(__('lessons.template_reading_takeaway_callout')),
        quizIntroHeading: @js(__('lessons.template_quiz_intro_heading')),
        quizIntroText: @js(__('lessons.template_quiz_intro_text', ['topic' => '__TOPIC__'])),
        quizCallout: @js(__('lessons.template_quiz_callout')),
        mixedPracticeHeading: @js(__('lessons.template_mixed_practice_heading')),
        mixedPracticeText: @js(__('lessons.template_mixed_practice_text', ['topic' => '__TOPIC__'])),
        mixedPracticeCallout: @js(__('lessons.template_mixed_practice_callout')),
        practiceCodeHeading: @js(__('lessons.template_practice_code_heading')),
        practiceCodeText: @js(__('lessons.template_practice_code_text', ['topic' => '__TOPIC__'])),
        quickCheckQuestion: @js(__('lessons.template_generic_quick_check_question', ['topic' => '__TOPIC__'])),
        quickCheckAnswerCorrect: @js(__('lessons.template_generic_quick_check_answer_correct', ['subject' => '__SUBJECT__'])),
        quickCheckAnswerDistractor1: @js(__('lessons.template_generic_quick_check_answer_distractor_1')),
        quickCheckAnswerDistractor2: @js(__('lessons.template_generic_quick_check_answer_distractor_2')),
        quickCheckAnswerDistractor3: @js(__('lessons.template_generic_quick_check_answer_distractor_3')),
        examMcQuestion: @js(__('lessons.template_generic_exam_mc_question', ['topic' => '__TOPIC__'])),
        examMcAnswerCorrect: @js(__('lessons.template_generic_exam_mc_answer_correct', ['subject' => '__SUBJECT__'])),
        examMcAnswerDistractor1: @js(__('lessons.template_generic_exam_mc_answer_distractor_1')),
        examMcAnswerDistractor2: @js(__('lessons.template_generic_exam_mc_answer_distractor_2')),
        examMcAnswerDistractor3: @js(__('lessons.template_generic_exam_mc_answer_distractor_3')),
        examTfQuestion: @js(__('lessons.template_generic_exam_tf_question', ['topic' => '__TOPIC__'])),
        examShortQuestion: @js(__('lessons.template_generic_exam_short_question', ['topic' => '__TOPIC__'])),
        examShortAnswer: @js(__('lessons.template_generic_exam_short_answer')),
    },
};

function interpolateTemplateText(template, replacements = {}) {
    return Object.entries(replacements).reduce((value, [key, replacement]) => (
        String(value ?? '').split(`__${key.toUpperCase()}__`).join(String(replacement ?? ''))
    ), String(template ?? ''));
}

function getSelectedSubjectLabel() {
    const subjectSelect = document.getElementById('subject');
    return getSelectedOptionText(subjectSelect) || i18n.subject;
}

function getTemplateFocus(subjectValue) {
    return lessonTemplateCopy.focusBySubject[subjectValue] || lessonTemplateCopy.focusBySubject.cybersecurity;
}

function guessCodeLanguage(subjectValue) {
    if (subjectValue === 'python') {
        return 'python';
    }

    return 'javascript';
}

function buildPythonPracticeSnippet(context) {
    return `# Tip: replace this block with starter code for the lesson.
# Include:
# 1. the task goal
# 2. the expected output
# 3. one change learners should make`;
}

function getTemplateContext(templateKey) {
    const titleInput = document.getElementById('title');
    const subjectSelect = document.getElementById('subject');
    const difficultySelect = document.getElementById('difficulty');
    const subjectValue = subjectSelect?.value || 'cybersecurity';
    const subjectLabel = getSelectedSubjectLabel();
    const rawTitle = titleInput?.value?.trim() ?? '';
    const topic = rawTitle || getTemplateFocus(subjectValue);
    const generatedTitleTemplate = lessonTemplateCopy.generatedTitles[templateKey]
        || lessonTemplateCopy.generatedTitles.mixed;

    return {
        title: rawTitle,
        subjectValue,
        subjectLabel,
        topic,
        generatedTitle: interpolateTemplateText(generatedTitleTemplate, {
            subject: subjectLabel,
        }),
        currentDifficulty: difficultySelect?.value ?? '',
        codeLanguage: guessCodeLanguage(subjectValue),
        useCodePractice: subjectValue === 'python',
    };
}

function createQuickCheckBlock(context) {
    return {
        type: 'quiz',
        question: interpolateTemplateText(lessonTemplateCopy.strings.quickCheckQuestion, {
            topic: context.topic,
        }),
        answers: [
            interpolateTemplateText(lessonTemplateCopy.strings.quickCheckAnswerCorrect, {
                subject: context.subjectLabel,
            }),
            lessonTemplateCopy.strings.quickCheckAnswerDistractor1,
            lessonTemplateCopy.strings.quickCheckAnswerDistractor2,
            lessonTemplateCopy.strings.quickCheckAnswerDistractor3,
        ],
        correct_answer: '0',
    };
}

function createAssessmentQuestions(context) {
    return [
        {
            type: 'multiple_choice',
            question: interpolateTemplateText(lessonTemplateCopy.strings.examMcQuestion, {
                topic: context.topic,
            }),
            answers: [
                interpolateTemplateText(lessonTemplateCopy.strings.examMcAnswerCorrect, {
                    subject: context.subjectLabel,
                }),
                lessonTemplateCopy.strings.examMcAnswerDistractor1,
                lessonTemplateCopy.strings.examMcAnswerDistractor2,
                lessonTemplateCopy.strings.examMcAnswerDistractor3,
            ],
            correct_answer: '0',
        },
        {
            type: 'true_false',
            question: interpolateTemplateText(lessonTemplateCopy.strings.examTfQuestion, {
                topic: context.topic,
            }),
            correct_answer: 'true',
        },
        {
            type: 'short_answer',
            question: interpolateTemplateText(lessonTemplateCopy.strings.examShortQuestion, {
                topic: context.topic,
            }),
            correct_answer: lessonTemplateCopy.strings.examShortAnswer,
            case_sensitive: false,
        },
    ];
}

function buildVideoTemplate(context) {
    return {
        settings: {
            title: context.generatedTitle,
            type: 'video',
            durationMinutes: 18,
            difficulty: 'beginner',
        },
        segments: [
            {
                type: 'content',
                custom_name: lessonTemplateCopy.sectionLabels.overview,
                blocks: [
                    { type: 'subheading', content: lessonTemplateCopy.strings.videoOverviewHeading },
                    {
                        type: 'text',
                        content: interpolateTemplateText(lessonTemplateCopy.strings.videoOverviewText, {
                            topic: context.topic,
                            subject: context.subjectLabel,
                        }),
                    },
                    { type: 'callout', callout_type: 'info', content: lessonTemplateCopy.strings.videoWatchCallout },
                ],
            },
            {
                type: 'content',
                custom_name: lessonTemplateCopy.sectionLabels.practice,
                blocks: [
                    { type: 'subheading', content: lessonTemplateCopy.strings.videoReflectionHeading },
                    {
                        type: 'text',
                        content: interpolateTemplateText(lessonTemplateCopy.strings.videoReflectionText, {
                            topic: context.topic,
                            subject: context.subjectLabel,
                        }),
                    },
                    { type: 'callout', callout_type: 'success', content: lessonTemplateCopy.strings.videoReflectionCallout },
                    createQuickCheckBlock(context),
                ],
            },
        ],
    };
}

function buildReadingTemplate(context) {
    return {
        settings: {
            title: context.generatedTitle,
            type: 'text',
            durationMinutes: 24,
            difficulty: 'beginner',
        },
        segments: [
            {
                type: 'content',
                custom_name: lessonTemplateCopy.sectionLabels.overview,
                blocks: [
                    { type: 'subheading', content: lessonTemplateCopy.strings.readingCoreHeading },
                    {
                        type: 'text',
                        content: interpolateTemplateText(lessonTemplateCopy.strings.readingCoreText, {
                            topic: context.topic,
                            subject: context.subjectLabel,
                        }),
                    },
                    { type: 'callout', callout_type: 'info', content: lessonTemplateCopy.strings.readingTakeawayCallout },
                ],
            },
            {
                type: 'content',
                custom_name: lessonTemplateCopy.sectionLabels.reading,
                blocks: [
                    { type: 'subheading', content: lessonTemplateCopy.strings.readingPathHeading },
                    { type: 'text', content: lessonTemplateCopy.strings.readingPathText },
                    createQuickCheckBlock(context),
                ],
            },
        ],
    };
}

function buildQuizTemplate(context) {
    return {
        settings: {
            title: context.generatedTitle,
            type: 'text',
            durationMinutes: 16,
            difficulty: 'intermediate',
        },
        segments: [
            {
                type: 'content',
                custom_name: lessonTemplateCopy.sectionLabels.overview,
                blocks: [
                    { type: 'subheading', content: lessonTemplateCopy.strings.quizIntroHeading },
                    {
                        type: 'text',
                        content: interpolateTemplateText(lessonTemplateCopy.strings.quizIntroText, {
                            topic: context.topic,
                        }),
                    },
                    { type: 'callout', callout_type: 'warning', content: lessonTemplateCopy.strings.quizCallout },
                ],
            },
            {
                type: 'exam',
                custom_name: lessonTemplateCopy.sectionLabels.assessment,
                exam_settings: {
                    time_limit: '15',
                    passing_score: '70',
                },
                questions: createAssessmentQuestions(context),
            },
        ],
    };
}

function buildMixedTemplate(context) {
    const practiceBlocks = [
        { type: 'subheading', content: lessonTemplateCopy.strings.mixedPracticeHeading },
        {
            type: 'text',
            content: interpolateTemplateText(lessonTemplateCopy.strings.mixedPracticeText, {
                topic: context.topic,
            }),
        },
    ];

    if (context.useCodePractice) {
        practiceBlocks.push(
            { type: 'subheading', content: lessonTemplateCopy.strings.practiceCodeHeading },
            {
                type: 'text',
                content: interpolateTemplateText(lessonTemplateCopy.strings.practiceCodeText, {
                    topic: context.topic,
                }),
            },
            {
                type: 'code',
                language: context.codeLanguage,
                content: buildPythonPracticeSnippet(context),
            },
        );
    }

    practiceBlocks.push(
        { type: 'callout', callout_type: 'success', content: lessonTemplateCopy.strings.mixedPracticeCallout },
        createQuickCheckBlock(context),
    );

    return {
        settings: {
            title: context.generatedTitle,
            type: 'text',
            durationMinutes: 32,
            difficulty: 'intermediate',
        },
        segments: [
            {
                type: 'content',
                custom_name: lessonTemplateCopy.sectionLabels.overview,
                blocks: [
                    { type: 'subheading', content: lessonTemplateCopy.strings.readingCoreHeading },
                    {
                        type: 'text',
                        content: interpolateTemplateText(lessonTemplateCopy.strings.readingCoreText, {
                            topic: context.topic,
                            subject: context.subjectLabel,
                        }),
                    },
                    { type: 'callout', callout_type: 'info', content: lessonTemplateCopy.strings.readingTakeawayCallout },
                ],
            },
            {
                type: 'content',
                custom_name: lessonTemplateCopy.sectionLabels.practice,
                blocks: practiceBlocks,
            },
            {
                type: 'exam',
                custom_name: lessonTemplateCopy.sectionLabels.assessment,
                exam_settings: {
                    time_limit: '20',
                    passing_score: '75',
                },
                questions: createAssessmentQuestions(context),
            },
        ],
    };
}

function getLessonTemplateDefinition(templateKey, context) {
    const definitions = {
        video: buildVideoTemplate,
        reading: buildReadingTemplate,
        quiz: buildQuizTemplate,
        mixed: buildMixedTemplate,
    };

    return (definitions[templateKey] || definitions.mixed)(context);
}

function createTemplateEmptyState(message) {
    return `
        <div class="empty-builder">
            <div class="empty-builder-icon">B</div>
            <p>${message}</p>
        </div>
    `;
}

function ensurePrimaryTemplateSegment() {
    return getSegmentElement(1);
}

function builderHasTemplateContent() {
    return segments.some((segment) => {
        if (segment.id === 0) {
            return false;
        }

        const segmentElement = getSegmentElement(segment.id);
        if (!segmentElement) {
            return false;
        }

        if ((segmentElement.querySelector('.segment-name-input')?.value ?? '').trim() !== '') {
            return true;
        }

        if (segment.type === 'exam') {
            return Array.from(segmentElement.querySelectorAll('.quiz-question')).some(questionHasMeaningfulContent);
        }

        return Array.from(segmentElement.querySelectorAll('.content-block')).some(blockHasMeaningfulContent);
    });
}

function resetCreateBuilderForTemplate() {
    const primarySegment = ensurePrimaryTemplateSegment();
    const primaryBlocks = getContentBlocksContainerElement(1);

    document.querySelectorAll('.segment-container[data-segment]').forEach((segmentElement) => {
        const segmentId = Number.parseInt(segmentElement.getAttribute('data-segment') ?? '', 10);
        if (segmentId > 1) {
            segmentElement.remove();
        }
    });

    segments = [
        { id: 0, label: `${i18n.basicInfo}`, type: 'basic' },
        { id: 1, label: `${i18n.contentSegment} 1`, type: 'content', customName: '' },
    ];

    segmentCounter = 2;
    contentSegmentIndex = 1;
    blockCounter = 0;

    if (primarySegment) {
        const primaryNameInput = primarySegment.querySelector('.segment-name-input');
        const primaryHeader = primarySegment.querySelector('.segment-header');

        setElementValue(primaryNameInput, '');

        if (primaryHeader) {
            primaryHeader.textContent = `${i18n.contentSegment} 1`;
        }
    }

    if (primaryBlocks) {
        primaryBlocks.innerHTML = createTemplateEmptyState(i18n.noContent);
    }

    updateStats();
    initializeSegments();
    switchSegment(0);
}

function dispatchTemplateFieldUpdate(element, eventName = 'input') {
    if (!element) {
        return;
    }

    element.dispatchEvent(new Event(eventName, { bubbles: true }));
}

function applyTemplateSettings(settings = {}, context = {}) {
    const titleInput = document.getElementById('title');
    const typeSelect = document.getElementById('type');
    const difficultySelect = document.getElementById('difficulty');
    const hoursInput = document.querySelector('input[name="duration_hours"]');
    const minutesInput = document.querySelector('input[name="duration_minutes"]');

    if (titleInput && titleInput.value.trim() === '' && settings.title) {
        setElementValue(titleInput, settings.title);
        dispatchTemplateFieldUpdate(titleInput);
    }

    if (typeSelect && settings.type) {
        setElementValue(typeSelect, settings.type);
        dispatchTemplateFieldUpdate(typeSelect, 'change');
    }

    if (difficultySelect && settings.difficulty && (difficultySelect.value ?? '') === '') {
        setElementValue(difficultySelect, context.currentDifficulty || settings.difficulty);
        dispatchTemplateFieldUpdate(difficultySelect, 'change');
    }

    if (hoursInput && minutesInput && Number.isFinite(settings.durationMinutes)) {
        const durationMinutes = Math.max(0, Number.parseInt(settings.durationMinutes, 10) || 0);
        const hours = Math.floor(durationMinutes / 60);
        const minutes = durationMinutes % 60;

        setElementValue(hoursInput, String(hours));
        setElementValue(minutesInput, String(minutes));
        dispatchTemplateFieldUpdate(hoursInput);
        dispatchTemplateFieldUpdate(minutesInput);
    }
}

function applyTemplateSegmentName(segmentId, customName) {
    const segmentElement = getSegmentElement(segmentId);
    if (!segmentElement) {
        return;
    }

    const nameInput = segmentElement.querySelector('.segment-name-input');
    setElementValue(nameInput, customName ?? '');
    dispatchTemplateFieldUpdate(nameInput);
    syncSegmentState(segmentId);
}

function applyTemplateContentSegment(segmentId, segmentDefinition) {
    applyTemplateSegmentName(segmentId, segmentDefinition.custom_name ?? '');
    segmentDefinition.blocks?.forEach((blockDefinition) => {
        appendBlockFromData(segmentId, blockDefinition);
    });
}

function applyTemplateExamSegment(segmentId, segmentDefinition) {
    applyTemplateSegmentName(segmentId, segmentDefinition.custom_name ?? '');

    const segmentElement = getSegmentElement(segmentId);
    if (!segmentElement) {
        return;
    }

    const timeLimitInput = segmentElement.querySelector('input[name$="[exam_settings][time_limit]"]');
    const passingScoreInput = segmentElement.querySelector('input[name$="[exam_settings][passing_score]"]');

    setElementValue(timeLimitInput, segmentDefinition.exam_settings?.time_limit ?? '0');
    setElementValue(passingScoreInput, segmentDefinition.exam_settings?.passing_score ?? '60');
    dispatchTemplateFieldUpdate(timeLimitInput);
    dispatchTemplateFieldUpdate(passingScoreInput);

    segmentDefinition.questions?.forEach((questionDefinition) => {
        appendQuestionFromData(segmentId, questionDefinition);
    });
}

function createTemplateSegment(segmentDefinition, usePrimarySegment = false) {
    if (usePrimarySegment) {
        return 1;
    }

    return createSegmentShell(segmentDefinition);
}

function setActiveLessonTemplate(templateKey) {
    document.querySelectorAll('.lesson-template-card[data-lesson-template]').forEach((button) => {
        button.classList.toggle('is-active', button.getAttribute('data-lesson-template') === templateKey);
    });
}

function applyLessonTemplate(templateKey) {
    if (!templateKey) {
        return;
    }

    if (builderHasTemplateContent() && !window.confirm(@js(__('lessons.template_apply_confirm')))) {
        return;
    }

    const context = getTemplateContext(templateKey);
    const definition = getLessonTemplateDefinition(templateKey, context);

    resetCreateBuilderForTemplate();
    applyTemplateSettings(definition.settings, context);

    definition.segments.forEach((segmentDefinition, index) => {
        const segmentId = createTemplateSegment(segmentDefinition, index === 0);

        if (!segmentId) {
            return;
        }

        if (segmentDefinition.type === 'exam') {
            applyTemplateExamSegment(segmentId, segmentDefinition);
            return;
        }

        applyTemplateContentSegment(segmentId, segmentDefinition);
    });

    setActiveLessonTemplate(templateKey);
    updateStats();
    switchSegment(0);
}

function bindLessonTemplatePicker() {
    document.querySelectorAll('.lesson-template-card[data-lesson-template]').forEach((button) => {
        if (button.dataset.templateBound === '1') {
            return;
        }

        button.dataset.templateBound = '1';
        button.addEventListener('click', () => {
            applyLessonTemplate(button.getAttribute('data-lesson-template'));
        });
    });
}

window.applyLessonTemplate = applyLessonTemplate;

document.addEventListener('DOMContentLoaded', bindLessonTemplatePicker);
