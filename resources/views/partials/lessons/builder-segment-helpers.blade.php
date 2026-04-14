function initializeSegments() {
    const activeSegments = Array.from(document.querySelectorAll('.segment-container.active[data-segment]'));

    if (activeSegments.length !== 1) {
        const fallbackSegmentId = Number.parseInt(activeSegments[0]?.getAttribute('data-segment') ?? '0', 10) || 0;
        document.querySelectorAll('.segment-container').forEach((segment) => {
            segment.classList.toggle('active', segment.getAttribute('data-segment') === String(fallbackSegmentId));
        });
    }

    updateStats();
}

function moveSegment(segmentId, direction) {
    const currentIndex = segments.findIndex((segment) => segment.id === segmentId);
    if (currentIndex === -1) {
        return;
    }

    const newIndex = currentIndex + direction;
    if (newIndex < 1 || newIndex >= segments.length) {
        return;
    }

    [segments[currentIndex], segments[newIndex]] = [segments[newIndex], segments[currentIndex]];

    const contentArea = document.querySelector('.content-area');
    const containers = Array.from(contentArea.querySelectorAll('.segment-container'));

    if (containers[currentIndex] && containers[newIndex]) {
        if (direction > 0) {
            containers[newIndex].parentNode.insertBefore(containers[currentIndex], containers[newIndex].nextSibling);
        } else {
            containers[currentIndex].parentNode.insertBefore(containers[newIndex], containers[currentIndex]);
        }
    }

    initializeSegments();
}

function getOutlineBadge(index) {
    return String(index + 1);
}

function getSegmentDefaultTitle(segment) {
    if (!segment) {
        return '';
    }

    if (segment.id === 0 || segment.type === 'basic') {
        return i18n.basicInfo;
    }

    const typedIndex = segments
        .filter((item) => item.id !== 0 && item.type === segment.type)
        .findIndex((item) => item.id === segment.id) + 1;

    if (segment.type === 'exam') {
        return `${i18n.examIndexLabel} ${typedIndex}`;
    }

    return `${i18n.contentSegment} ${typedIndex}`;
}

function escapeOutlineText(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function getSelectedOptionText(selectElement) {
    if (!selectElement || selectElement.selectedIndex < 0) {
        return '';
    }

    return selectElement.options[selectElement.selectedIndex]?.textContent?.trim() ?? '';
}

function getContentBlocksContainer(segmentId) {
    return document.getElementById(`contentBlocks_${segmentId}`)
        || (segmentId === 1 ? document.getElementById('contentBlocks') : null);
}

function hasMeaningfulValue(element) {
    if (!element) {
        return false;
    }

    if (element.type === 'file') {
        return Boolean(element.files && element.files.length);
    }

    if (element.type === 'checkbox' || element.type === 'radio') {
        return element.checked;
    }

    return String(element.value ?? '').trim() !== '';
}

function blockHasMeaningfulContent(block) {
    const blockType = block.getAttribute('data-block-type')
        || block.querySelector('input[type="hidden"][name$="[type]"]')?.value;

    if (!blockType) {
        return false;
    }

    switch (blockType) {
        case 'text':
        case 'subheading':
        case 'callout':
        case 'code':
            return Array.from(block.querySelectorAll('textarea')).some(hasMeaningfulValue);

        case 'video':
            return hasMeaningfulValue(block.querySelector('input[type="url"]'));

        case 'image':
        case 'file':
            return hasMeaningfulValue(block.querySelector('input[type="file"]'))
                || hasMeaningfulValue(block.querySelector('input[name$="[existing_path]"]'));

        case 'divider':
            return true;

        case 'quiz': {
            const questionField = block.querySelector('input[name*="[question]"]');
            const answers = Array.from(block.querySelectorAll('.quiz-answer-input')).filter(hasMeaningfulValue);
            return hasMeaningfulValue(questionField) && answers.length >= 2;
        }

        default:
            return Array.from(block.querySelectorAll('input, textarea, select')).some(hasMeaningfulValue);
    }
}

function questionHasMeaningfulContent(question) {
    const questionPrompt = question.querySelector('input[name*="[question]"]');

    if (!hasMeaningfulValue(questionPrompt)) {
        return false;
    }

    const questionType = question.getAttribute('data-question-type');

    if (questionType === 'multiple_choice') {
        return Array.from(question.querySelectorAll('.quiz-answer-input')).filter(hasMeaningfulValue).length >= 2;
    }

    if (questionType === 'short_answer') {
        return hasMeaningfulValue(question.querySelector('input[name*="[correct_answer]"]'));
    }

    return true;
}

function getBasicOutlineState() {
    const titleValue = document.getElementById('title')?.value?.trim() ?? '';
    const typeValue = getSelectedOptionText(document.getElementById('type'));
    const subjectValue = getSelectedOptionText(document.getElementById('subject'));
    const completedFields = [titleValue, typeValue, subjectValue].filter(Boolean).length;
    const metaParts = [];

    if (titleValue) {
        metaParts.push(titleValue);
    }

    if (typeValue) {
        metaParts.push(typeValue);
    }

    if (subjectValue) {
        metaParts.push(subjectValue);
    }

    return {
        title: i18n.basicInfo,
        meta: metaParts.join(' | ') || i18n.basicInfo,
        status: `${completedFields}/3`,
        isEmpty: completedFields === 0,
        isPartial: completedFields > 0 && completedFields < 3,
    };
}

function getSegmentOutlineState(segment) {
    if (segment.id === 0) {
        return {
            ...getBasicOutlineState(),
            kicker: '',
        };
    }

    const defaultTitle = getSegmentDefaultTitle(segment);
    const customTitle = (segment.customName || '').trim();

    if (segment.type === 'exam') {
        const questions = Array.from(document.querySelectorAll(`#quizContainer_${segment.id} .quiz-question`));
        const completedQuestions = questions.filter(questionHasMeaningfulContent).length;

        return {
            title: customTitle || defaultTitle,
            kicker: customTitle ? defaultTitle : '',
            meta: questions.length === 0
                ? i18n.noQuestions
                : `${completedQuestions}/${questions.length} ${i18n.questions}`,
            status: questions.length === 0 ? '0' : `${completedQuestions}/${questions.length}`,
            isEmpty: questions.length === 0,
            isPartial: questions.length > 0 && completedQuestions < questions.length,
        };
    }

    const blocks = Array.from(getContentBlocksContainer(segment.id)?.querySelectorAll('.content-block') ?? []);
    const completedBlocks = blocks.filter(blockHasMeaningfulContent).length;

    return {
        title: customTitle || defaultTitle,
        kicker: customTitle ? defaultTitle : '',
        meta: blocks.length === 0
            ? i18n.noContent
            : `${completedBlocks}/${blocks.length} ${i18n.blocks}`,
        status: blocks.length === 0 ? '0' : `${completedBlocks}/${blocks.length}`,
        isEmpty: blocks.length === 0,
        isPartial: blocks.length > 0 && completedBlocks < blocks.length,
    };
}

function hasRequiredLessonMedia(typeValue) {
    if (typeValue === 'video') {
        return hasMeaningfulValue(document.getElementById('video_url'));
    }

    if (typeValue === 'document') {
        const documentInput = document.getElementById('document');

        return hasMeaningfulValue(documentInput)
            || documentInput?.dataset?.existingDocument === '1';
    }

    return typeValue === 'text';
}

function getPublishChecklistState() {
    const lessonType = document.getElementById('type')?.value ?? '';
    const subjectValue = document.getElementById('subject')?.value ?? '';
    const durationHours = Number.parseInt(document.querySelector('input[name="duration_hours"]')?.value ?? '0', 10) || 0;
    const durationMinutes = Number.parseInt(document.querySelector('input[name="duration_minutes"]')?.value ?? '0', 10) || 0;
    const nonBasicSegments = segments.filter((segment) => segment.id !== 0);
    const segmentStates = nonBasicSegments.map(getSegmentOutlineState);
    const allSegmentsComplete = segmentStates.length > 0
        && segmentStates.every((state) => !state.isEmpty && !state.isPartial);

    const items = [
        { key: 'title', complete: hasMeaningfulValue(document.getElementById('title')) },
        { key: 'type', complete: ['video', 'text', 'document'].includes(lessonType) },
        { key: 'subject', complete: subjectValue.trim() !== '' },
        { key: 'duration', complete: (durationHours * 60) + durationMinutes > 0 },
        { key: 'media', complete: hasRequiredLessonMedia(lessonType) },
        { key: 'segments', complete: nonBasicSegments.length > 0 },
        { key: 'completion', complete: allSegmentsComplete },
    ];

    const completeCount = items.filter((item) => item.complete).length;

    return {
        items,
        completeCount,
        totalCount: items.length,
        remainingCount: items.length - completeCount,
        ready: completeCount === items.length,
    };
}

function updatePublishChecklist() {
    const checklistCard = document.getElementById('publishChecklistCard');
    const checklistSummary = document.getElementById('publishChecklistSummary');

    if (!checklistCard || !checklistSummary) {
        return;
    }

    const state = getPublishChecklistState();

    checklistCard.classList.toggle('is-ready', state.ready);
    checklistSummary.textContent = state.ready
        ? i18n.publishChecklistReady
        : i18n.publishChecklistRemaining.replace('__COUNT__', String(state.remainingCount));

    document.querySelectorAll('[data-checklist-item]').forEach((item) => {
        const key = item.getAttribute('data-checklist-item');
        const itemState = state.items.find((entry) => entry.key === key);
        const stateLabel = item.querySelector('.publish-checklist-state');

        if (!itemState || !stateLabel) {
            return;
        }

        item.classList.toggle('is-complete', itemState.complete);
        stateLabel.textContent = itemState.complete
            ? i18n.publishChecklistDone
            : i18n.publishChecklistMissing;
    });
}

function updateCourseOutline() {
    const outline = document.getElementById('courseOutline');
    const activeSegmentId = String(document.querySelector('.segment-container.active')?.getAttribute('data-segment') ?? 0);

    if (!outline) {
        return;
    }

    outline.innerHTML = segments.map((segment, index) => {
        const state = {
            ...getSegmentOutlineState(segment),
            badge: getOutlineBadge(index),
        };
        const classes = [
            'outline-item-wrap',
            String(segment.id) === activeSegmentId ? 'active' : '',
            state.isEmpty ? 'is-empty' : '',
            state.isPartial ? 'is-partial' : '',
        ].filter(Boolean).join(' ');

        return `
            <div class="${classes}" data-segment="${segment.id}">
                <button type="button" class="outline-item-main" onclick="switchSegment(${segment.id})">
                    <span class="outline-item-icon">${state.badge}</span>
                    <span class="outline-item-copy">
                        ${state.kicker ? `<span class="outline-item-kicker">${escapeOutlineText(state.kicker)}</span>` : ''}
                        <span class="outline-item-top">
                            <span class="outline-item-name">${escapeOutlineText(state.title)}</span>
                            <span class="outline-item-status">${escapeOutlineText(state.status)}</span>
                        </span>
                        <span class="outline-item-meta">${escapeOutlineText(state.meta)}</span>
                    </span>
                </button>
                ${segment.id !== 0 ? `
                    <div class="outline-item-actions">
                        ${index > 1 ? `<button type="button" class="segment-action-btn" title="${escapeOutlineText(i18n.moveUp)}" onclick="event.stopPropagation(); moveSegment(${segment.id}, -1)">^</button>` : ''}
                        ${index < segments.length - 1 ? `<button type="button" class="segment-action-btn" title="${escapeOutlineText(i18n.moveDown)}" onclick="event.stopPropagation(); moveSegment(${segment.id}, 1)">v</button>` : ''}
                        <button type="button" class="segment-action-btn" title="${escapeOutlineText(i18n.removeSegment)}" onclick="event.stopPropagation(); removeSegment(${segment.id})">x</button>
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');

    updatePublishChecklist();
    window.refreshLearnerPreview?.();
}

function updateStats() {
    const totalBlocks = document.querySelectorAll('.content-block:not(.empty-builder)').length;
    const totalQuestions = document.querySelectorAll('.quiz-question').length;
    document.getElementById('segmentCount').textContent = segments.length;
    document.getElementById('blockCount').textContent = totalBlocks + totalQuestions;
    window.decorateDuplicateControls?.();
    updateCourseOutline();
}

function switchSegment(segmentId) {
    document.querySelectorAll('.segment-container').forEach((segment) => {
        segment.classList.remove('active');
    });

    const selectedSegment = document.querySelector(`.segment-container[data-segment="${segmentId}"]`);
    if (selectedSegment) {
        selectedSegment.classList.add('active');
    }

    updateCourseOutline();
}

function showSegmentTypeMenu() {
    document.getElementById('segmentTypeModal')?.classList.add('active');
}

function selectSegmentType(type) {
    document.getElementById('segmentTypeModal')?.classList.remove('active');

    if (type === 'content') {
        addContentSegment();
    } else if (type === 'exam') {
        addExamSegment();
    }
}

function removeSegment(segmentId) {
    if (segmentId === 0) {
        alert(i18n.cannotRemoveBasic);
        return;
    }

    if (!confirm(i18n.confirmRemoveSegment)) {
        return;
    }

    segments = segments.filter((segment) => segment.id !== segmentId);

    const segmentElement = document.querySelector(`.segment-container[data-segment="${segmentId}"]`);
    if (segmentElement) {
        segmentElement.remove();
    }

    initializeSegments();
    switchSegment(0);
}

function bindSharedBuilderEvents() {
    document.getElementById('addSegmentBtn')?.addEventListener('click', (event) => {
        event.preventDefault();
        showSegmentTypeMenu();
    });

    document.addEventListener('input', (event) => {
        if (!event.target.classList.contains('segment-name-input')) {
            return;
        }

        const segmentId = parseInt(event.target.getAttribute('data-segment-id'), 10);
        const customName = event.target.value.trim();
        const segment = segments.find((item) => item.id === segmentId);

        if (!segment) {
            return;
        }

        segment.customName = customName;
        updateCourseOutline();
    });

    document.getElementById('segmentTypeModal')?.addEventListener('click', function (event) {
        if (event.target === this) {
            this.classList.remove('active');
        }
    });

    const refreshOutlineState = (event) => {
        const target = event.target;

        if (!target) {
            return;
        }

        if (['title', 'type', 'subject', 'difficulty', 'video_url', 'document', 'thumbnail'].includes(target.id)
            || target.closest('.segment-container')) {
            updateCourseOutline();
        }
    };

    document.addEventListener('input', refreshOutlineState);
    document.addEventListener('change', refreshOutlineState);
}
