function getSegmentElement(segmentId) {
    return document.querySelector(`.segment-container[data-segment="${segmentId}"]`);
}

function getContentBlocksContainerElement(segmentId) {
    return document.getElementById(`contentBlocks_${segmentId}`)
        || (segmentId === 1 ? document.getElementById('contentBlocks') : null);
}

function getSegmentIdFromElement(element) {
    return Number.parseInt(element?.closest('.segment-container')?.getAttribute('data-segment') ?? '', 10);
}

function setElementValue(element, value) {
    if (!element) {
        return;
    }

    if (element.type === 'checkbox') {
        element.checked = Boolean(value);
        return;
    }

    element.value = value ?? '';
}

function escapeDuplicateLabel(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function getDuplicateButtonHtml(kind, id) {
    const labels = {
        segment: i18n.duplicateSegment,
        block: i18n.duplicateBlock,
        question: i18n.duplicateQuestion,
    };

    const handlers = {
        segment: `duplicateSegment(${id})`,
        block: `duplicateBlock(${id})`,
        question: `duplicateQuestion(${id})`,
    };

    if (!labels[kind] || !handlers[kind]) {
        return '';
    }

    return `
        <button type="button" class="btn btn-secondary btn-sm duplicate-action-btn" title="${escapeDuplicateLabel(labels[kind])}" onclick="${handlers[kind]}">
            ${escapeDuplicateLabel(labels[kind])}
        </button>
    `;
}

function moveSegmentRecordAfter(newSegmentId, referenceSegmentId) {
    const newIndex = segments.findIndex((segment) => segment.id === newSegmentId);
    const referenceIndex = segments.findIndex((segment) => segment.id === referenceSegmentId);

    if (newIndex === -1 || referenceIndex === -1 || newIndex === referenceIndex + 1) {
        return;
    }

    const [segmentRecord] = segments.splice(newIndex, 1);
    segments.splice(referenceIndex + 1, 0, segmentRecord);
}

function insertElementAfter(element, referenceElement) {
    if (!element || !referenceElement || !referenceElement.parentNode) {
        return;
    }

    referenceElement.parentNode.insertBefore(element, referenceElement.nextSibling);
}

function updateSegmentHeader(segmentElement, fallbackTitle) {
    const header = segmentElement?.querySelector('.segment-header');
    const nameInput = segmentElement?.querySelector('.segment-name-input');

    if (!header) {
        return;
    }

    const customName = nameInput?.value?.trim() ?? '';
    header.textContent = customName || fallbackTitle;
}

function getSegmentFallbackLabel(segmentId) {
    const segment = segments.find((item) => item.id === segmentId);
    return getSegmentDefaultTitle(segment) || segment?.label || i18n.contentSegment;
}

function collectBlockData(block) {
    if (!block) {
        return null;
    }

    const typeInput = block.querySelector('input[type="hidden"][name$="[type]"]');
    const blockType = block.getAttribute('data-block-type') || typeInput?.value;

    if (!blockType) {
        return null;
    }

    switch (blockType) {
        case 'text':
        case 'subheading':
            return {
                type: blockType,
                content: block.querySelector('textarea, input[name$="[content]"]')?.value ?? '',
            };

        case 'video':
            return {
                type: blockType,
                content: block.querySelector('input[type="url"], input[name$="[content]"]')?.value ?? '',
            };

        case 'image':
            return {
                type: blockType,
                caption: block.querySelector('textarea[name$="[content]"], input[name$="[content]"]')?.value ?? '',
                path: block.querySelector('input[name$="[existing_path]"]')?.value ?? '',
            };

        case 'file':
            return {
                type: blockType,
                path: block.querySelector('input[name$="[existing_path]"]')?.value ?? '',
            };

        case 'callout':
            return {
                type: blockType,
                callout_type: block.querySelector('select[name$="[callout_type]"]')?.value ?? 'info',
                content: block.querySelector('textarea[name$="[content]"]')?.value ?? '',
            };

        case 'code':
            return {
                type: blockType,
                language: block.querySelector('select[name$="[language]"]')?.value ?? 'javascript',
                content: block.querySelector('textarea[name$="[content]"]')?.value ?? '',
            };

        case 'divider':
            return { type: blockType };

        case 'quiz': {
            const correctAnswer = block.querySelector('.quiz-answer-radio:checked')?.value ?? '0';

            return {
                type: blockType,
                question: block.querySelector('input[name$="[question]"]')?.value ?? '',
                answers: Array.from(block.querySelectorAll('.quiz-answer-input')).map((input) => input.value ?? ''),
                correct_answer: correctAnswer,
            };
        }

        default:
            return {
                type: blockType,
            };
    }
}

function collectQuestionData(question) {
    if (!question) {
        return null;
    }

    const typeInput = question.querySelector('input[type="hidden"][name$="[type]"]');
    const questionType = question.getAttribute('data-question-type') || typeInput?.value;

    if (!questionType) {
        return null;
    }

    const data = {
        type: questionType,
        question: question.querySelector('input[name$="[question]"]')?.value ?? '',
    };

    if (questionType === 'multiple_choice') {
        data.answers = Array.from(question.querySelectorAll('.quiz-answer-input')).map((input) => input.value ?? '');
        data.correct_answer = question.querySelector('.quiz-answer-radio:checked')?.value ?? '0';
        return data;
    }

    if (questionType === 'true_false') {
        data.correct_answer = question.querySelector('input[name$="[correct_answer]"]:checked')?.value ?? '';
        return data;
    }

    data.correct_answer = question.querySelector('input[name$="[correct_answer]"]')?.value ?? '';
    data.case_sensitive = question.querySelector('input[name$="[case_sensitive]"]')?.checked ?? false;

    return data;
}

function collectSegmentData(segmentId) {
    const segment = segments.find((item) => item.id === segmentId);
    const segmentElement = getSegmentElement(segmentId);

    if (!segment || !segmentElement) {
        return null;
    }

    const data = {
        type: segment.type,
        custom_name: segmentElement.querySelector('.segment-name-input')?.value?.trim() ?? segment.customName ?? '',
    };

    if (segment.type === 'exam') {
        data.exam_settings = {
            time_limit: segmentElement.querySelector('input[name$="[exam_settings][time_limit]"]')?.value ?? '0',
            passing_score: segmentElement.querySelector('input[name$="[exam_settings][passing_score]"]')?.value ?? '60',
        };

        data.questions = Array.from(segmentElement.querySelectorAll('.quiz-question'))
            .map(collectQuestionData)
            .filter(Boolean);

        return data;
    }

    data.blocks = Array.from(segmentElement.querySelectorAll('.content-block'))
        .map(collectBlockData)
        .filter(Boolean);

    return data;
}

function syncSegmentState(segmentId) {
    const segment = segments.find((item) => item.id === segmentId);
    const segmentElement = getSegmentElement(segmentId);

    if (!segment || !segmentElement) {
        return;
    }

    const customName = segmentElement.querySelector('.segment-name-input')?.value?.trim() ?? '';
    segment.customName = customName;
    updateSegmentHeader(segmentElement, getSegmentFallbackLabel(segmentId));
}

function applyQuizBlockData(blockElement, blockData) {
    const blockId = Number.parseInt(blockElement?.getAttribute('data-block-id') ?? '', 10);
    const segmentId = getSegmentIdFromElement(blockElement);
    const desiredAnswers = Math.max(blockData.answers?.length ?? 0, 2);
    const answersContainer = blockElement?.querySelector(`#quiz-answers-${blockId}`);

    if (!blockElement || !answersContainer || Number.isNaN(blockId) || Number.isNaN(segmentId)) {
        return;
    }

    while (answersContainer.querySelectorAll('.quiz-answer').length < desiredAnswers) {
        addQuizAnswer(blockId, segmentId);
    }

    while (answersContainer.querySelectorAll('.quiz-answer').length > desiredAnswers) {
        answersContainer.querySelector('.quiz-answer:last-child')?.remove();
    }

    reindexQuizAnswers(blockId);

    setElementValue(blockElement.querySelector('input[name$="[question]"]'), blockData.question ?? '');

    const answerInputs = Array.from(blockElement.querySelectorAll('.quiz-answer-input'));
    answerInputs.forEach((input, index) => {
        input.value = blockData.answers?.[index] ?? '';
    });

    const correctIndex = Number.parseInt(blockData.correct_answer ?? '0', 10) || 0;
    const correctRadio = blockElement.querySelector(`.quiz-answer-radio[value="${correctIndex}"]`)
        || blockElement.querySelector('.quiz-answer-radio');

    if (correctRadio) {
        correctRadio.checked = true;
        highlightCorrectAnswer(blockId, correctRadio.value);
    }
}

function populateBlockElement(blockElement, blockData) {
    if (!blockElement || !blockData) {
        return;
    }

    switch (blockData.type) {
        case 'text':
        case 'subheading':
            setElementValue(blockElement.querySelector('textarea, input[name$="[content]"]'), blockData.content ?? '');
            break;

        case 'video':
            setElementValue(blockElement.querySelector('input[type="url"], input[name$="[content]"]'), blockData.content ?? '');
            break;

        case 'image':
            setElementValue(blockElement.querySelector('textarea[name$="[content]"], input[name$="[content]"]'), blockData.caption ?? '');
            setElementValue(blockElement.querySelector('input[name$="[existing_path]"]'), blockData.path ?? '');
            break;

        case 'file':
            setElementValue(blockElement.querySelector('input[name$="[existing_path]"]'), blockData.path ?? '');
            break;

        case 'callout':
            setElementValue(blockElement.querySelector('select[name$="[callout_type]"]'), blockData.callout_type ?? 'info');
            setElementValue(blockElement.querySelector('textarea[name$="[content]"]'), blockData.content ?? '');
            break;

        case 'code':
            setElementValue(blockElement.querySelector('select[name$="[language]"]'), blockData.language ?? 'javascript');
            setElementValue(blockElement.querySelector('textarea[name$="[content]"]'), blockData.content ?? '');
            break;

        case 'quiz':
            applyQuizBlockData(blockElement, blockData);
            break;

        default:
            break;
    }
}

function applyQuestionData(questionElement, questionData) {
    if (!questionElement || !questionData) {
        return;
    }

    setElementValue(questionElement.querySelector('input[name$="[question]"]'), questionData.question ?? '');

    if (questionData.type === 'multiple_choice') {
        const questionId = Number.parseInt(questionElement.getAttribute('data-question-id') ?? '', 10);
        const segmentId = getSegmentIdFromElement(questionElement);
        const answersContainer = questionElement.querySelector(`#answers_${questionId}`);
        const desiredAnswers = Math.max(questionData.answers?.length ?? 0, 2);

        if (!answersContainer || Number.isNaN(questionId) || Number.isNaN(segmentId)) {
            return;
        }

        while (answersContainer.querySelectorAll('.quiz-answer').length < desiredAnswers) {
            addQuestionAnswer(questionId, segmentId);
        }

        while (answersContainer.querySelectorAll('.quiz-answer').length > desiredAnswers) {
            answersContainer.querySelector('.quiz-answer:last-child')?.remove();
        }

        reindexQuestionAnswers(answersContainer);

        const answerInputs = Array.from(questionElement.querySelectorAll('.quiz-answer-input'));
        answerInputs.forEach((input, index) => {
            input.value = questionData.answers?.[index] ?? '';
        });

        const correctIndex = Number.parseInt(questionData.correct_answer ?? '0', 10) || 0;
        const correctRadio = questionElement.querySelector(`.quiz-answer-radio[value="${correctIndex}"]`)
            || questionElement.querySelector('.quiz-answer-radio');

        if (correctRadio) {
            correctRadio.checked = true;
        }

        return;
    }

    if (questionData.type === 'true_false') {
        const selected = questionElement.querySelector(`input[name$="[correct_answer]"][value="${questionData.correct_answer}"]`);
        if (selected) {
            selected.checked = true;
        }
        return;
    }

    setElementValue(questionElement.querySelector('input[name$="[correct_answer]"]'), questionData.correct_answer ?? '');
    setElementValue(questionElement.querySelector('input[name$="[case_sensitive]"]'), questionData.case_sensitive ?? false);
}

function appendBlockFromData(segmentId, blockData) {
    if (!blockData) {
        return null;
    }

    if (typeof renderBlockFromData === 'function') {
        const newBlockId = blockCounter++;
        renderBlockFromData(segmentId, { ...blockData, id: newBlockId });
        return getContentBlocksContainerElement(segmentId)?.querySelector(`.content-block[data-block-id="${newBlockId}"]`) ?? null;
    }

    const newBlockId = blockCounter;
    const adders = {
        text: addTextBlock,
        image: addImageBlock,
        subheading: addSubheadingBlock,
        video: addVideoBlock,
        file: addFileBlock,
        callout: addCalloutBlock,
        code: addCodeBlock,
        divider: addDividerBlock,
        quiz: addQuizBlock,
    };

    adders[blockData.type]?.(segmentId);

    const blockElement = getContentBlocksContainerElement(segmentId)?.querySelector(`.content-block[data-block-id="${newBlockId}"]`) ?? null;
    populateBlockElement(blockElement, blockData);

    return blockElement;
}

function appendQuestionFromData(segmentId, questionData) {
    if (!questionData) {
        return null;
    }

    if (typeof renderQuestionFromData === 'function') {
        const newQuestionId = blockCounter++;
        renderQuestionFromData(segmentId, { ...questionData, id: newQuestionId });
        return document.querySelector(`#quizContainer_${segmentId} .quiz-question[data-question-id="${newQuestionId}"]`);
    }

    const newQuestionId = blockCounter;
    addExamQuestion(segmentId, questionData.type);

    const questionElement = document.querySelector(`#quizContainer_${segmentId} .quiz-question[data-question-id="${newQuestionId}"]`);
    applyQuestionData(questionElement, questionData);

    return questionElement;
}

function createSegmentShell(segmentData) {
    if (segmentData.type === 'exam') {
        if (typeof renderExamSegment === 'function') {
            const newSegmentId = segmentCounter++;
            segments.push({
                id: newSegmentId,
                label: i18n.examIndexLabel,
                type: 'exam',
                customName: segmentData.custom_name || '',
            });

            const segmentHtml = renderExamSegment(newSegmentId, {
                custom_name: segmentData.custom_name || '',
                exam_settings: segmentData.exam_settings || { time_limit: 0, passing_score: 60 },
                questions: [],
            });

            document.getElementById('segmentsContainer').insertAdjacentHTML('beforeend', segmentHtml);
            return newSegmentId;
        }

        addExamSegment();
        return segments[segments.length - 1]?.id ?? null;
    }

    if (typeof renderContentSegment === 'function') {
        contentSegmentIndex++;
        const newSegmentId = segmentCounter++;
        const label = `${i18n.contentSegment} ${contentSegmentIndex}`;

        segments.push({
            id: newSegmentId,
            label,
            type: 'content',
            customName: segmentData.custom_name || '',
        });

        const segmentHtml = renderContentSegment(newSegmentId, {
            custom_name: segmentData.custom_name || '',
            blocks: [],
        });

        document.getElementById('segmentsContainer').insertAdjacentHTML('beforeend', segmentHtml);
        return newSegmentId;
    }

    addContentSegment();
    return segments[segments.length - 1]?.id ?? null;
}

function populateSegmentElement(segmentId, segmentData) {
    const segmentElement = getSegmentElement(segmentId);

    if (!segmentElement) {
        return;
    }

    const nameInput = segmentElement.querySelector('.segment-name-input');
    setElementValue(nameInput, segmentData.custom_name ?? '');

    const segmentRecord = segments.find((item) => item.id === segmentId);
    if (segmentRecord) {
        segmentRecord.customName = segmentData.custom_name ?? '';
    }

    updateSegmentHeader(segmentElement, getSegmentFallbackLabel(segmentId));

    if (segmentData.type === 'exam') {
        setElementValue(segmentElement.querySelector('input[name$="[exam_settings][time_limit]"]'), segmentData.exam_settings?.time_limit ?? '0');
        setElementValue(segmentElement.querySelector('input[name$="[exam_settings][passing_score]"]'), segmentData.exam_settings?.passing_score ?? '60');
        segmentData.questions?.forEach((questionData) => appendQuestionFromData(segmentId, questionData));
        return;
    }

    segmentData.blocks?.forEach((blockData) => appendBlockFromData(segmentId, blockData));
}

function duplicateSegment(segmentId) {
    const sourceElement = getSegmentElement(segmentId);
    const segmentData = collectSegmentData(segmentId);

    if (!sourceElement || !segmentData) {
        return;
    }

    const newSegmentId = createSegmentShell(segmentData);

    if (!newSegmentId) {
        return;
    }

    populateSegmentElement(newSegmentId, segmentData);

    const newSegmentElement = getSegmentElement(newSegmentId);
    insertElementAfter(newSegmentElement, sourceElement);
    moveSegmentRecordAfter(newSegmentId, segmentId);

    syncSegmentState(newSegmentId);
    switchSegment(newSegmentId);
    updateStats();
}

function duplicateBlock(blockId) {
    const blockElement = document.querySelector(`.content-block[data-block-id="${blockId}"]`);
    if (!blockElement) {
        return;
    }

    const blockData = collectBlockData(blockElement);
    const segmentId = getSegmentIdFromElement(blockElement);

    if (!blockData || Number.isNaN(segmentId)) {
        return;
    }

    const newBlockElement = appendBlockFromData(segmentId, blockData);
    insertElementAfter(newBlockElement, blockElement);
    updateStats();
}

function duplicateQuestion(questionId) {
    const questionElement = document.querySelector(`.quiz-question[data-question-id="${questionId}"]`);
    if (!questionElement) {
        return;
    }

    const questionData = collectQuestionData(questionElement);
    const segmentId = getSegmentIdFromElement(questionElement);

    if (!questionData || Number.isNaN(segmentId)) {
        return;
    }

    const newQuestionElement = appendQuestionFromData(segmentId, questionData);
    insertElementAfter(newQuestionElement, questionElement);
    updateStats();
}

function decorateDuplicateControls() {
    document.querySelectorAll('.segment-container[data-segment]').forEach((segmentElement) => {
        const segmentId = Number.parseInt(segmentElement.getAttribute('data-segment') ?? '', 10);
        if (!segmentId) {
            return;
        }

        const actions = segmentElement.querySelector('.segment-actions');
        if (actions && !actions.querySelector('.duplicate-segment-btn')) {
            actions.insertAdjacentHTML('afterbegin', getDuplicateButtonHtml('segment', segmentId).replace('duplicate-action-btn', 'duplicate-action-btn duplicate-segment-btn'));
        }
    });

    document.querySelectorAll('.content-block[data-block-id]').forEach((blockElement) => {
        const blockId = Number.parseInt(blockElement.getAttribute('data-block-id') ?? '', 10);
        const actions = blockElement.querySelector('.block-actions');

        if (!blockId || !actions || actions.querySelector('.duplicate-block-btn')) {
            return;
        }

        actions.insertAdjacentHTML('afterbegin', getDuplicateButtonHtml('block', blockId).replace('duplicate-action-btn', 'duplicate-action-btn duplicate-block-btn'));
    });

    document.querySelectorAll('.quiz-question[data-question-id]').forEach((questionElement) => {
        const questionId = Number.parseInt(questionElement.getAttribute('data-question-id') ?? '', 10);
        const header = questionElement.querySelector('.quiz-question-header');

        if (!questionId || !header || header.querySelector('.duplicate-question-btn')) {
            return;
        }

        const removeButton = header.querySelector('button[onclick^="removeQuestion"]');
        const buttonHtml = getDuplicateButtonHtml('question', questionId).replace('duplicate-action-btn', 'duplicate-action-btn duplicate-question-btn');

        if (removeButton) {
            removeButton.insertAdjacentHTML('beforebegin', buttonHtml);
        } else {
            header.insertAdjacentHTML('beforeend', buttonHtml);
        }
    });
}

window.decorateDuplicateControls = decorateDuplicateControls;
