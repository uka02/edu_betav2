function addExamQuestion(segmentId = 1, questionType = 'multiple_choice') {
    const container = document.getElementById(`quizContainer_${segmentId}`);
    const emptyState = container.querySelector('.empty-builder');
    if (emptyState) {
        emptyState.remove();
    }

    const questionId = blockCounter++;
    let questionHtml = `
        <div class="quiz-question" data-question-id="${questionId}" data-question-type="${questionType}">
            <div class="quiz-question-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid rgba(168, 85, 247, 0.2);">
                <span style="font-size: 12px; color: var(--muted); font-weight: 600; text-transform: uppercase;">`;

    if (questionType === 'multiple_choice') {
        questionHtml += i18n.questionMultipleChoice;
    } else if (questionType === 'true_false') {
        questionHtml += i18n.questionTrueFalse;
    } else if (questionType === 'short_answer') {
        questionHtml += i18n.questionShortAnswer;
    }

    questionHtml += `</span>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(this)">${i18n.removeQuestion}</button>
            </div>
            <input type="hidden" name="segments[${segmentId}][questions][${questionId}][type]" value="${questionType}">
            <div class="form-group">
                <label class="form-label">${i18n.question}</label>
                <input type="text" name="segments[${segmentId}][questions][${questionId}][question]" class="quiz-question-input" placeholder="${i18n.questionPlaceholder}" required>
            </div>`;

    if (questionType === 'multiple_choice') {
        questionHtml += `
            <div class="form-group">
                <label class="form-label">${i18n.answers}</label>
                <div class="quiz-answers" id="answers_${questionId}">
                    <div class="quiz-answer">
                        <input type="radio" name="segments[${segmentId}][questions][${questionId}][correct_answer]" value="0" class="quiz-answer-radio">
                        <input type="text" name="segments[${segmentId}][questions][${questionId}][answers][0]" class="quiz-answer-input" placeholder="${i18n.placeholderAnswer} 1" required>
                        <button type="button" class="quiz-answer-remove" onclick="removeQuestionAnswer(this)">x</button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addQuestionAnswer(${questionId}, ${segmentId})" style="margin-top: 8px;">+ ${i18n.addAnswer}</button>
            </div>`;
    } else if (questionType === 'true_false') {
        questionHtml += `
            <div class="form-group">
                <label class="form-label">${i18n.correctAnswer}</label>
                <div style="display: flex; gap: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="segments[${segmentId}][questions][${questionId}][correct_answer]" value="true" required>
                        <span style="color: var(--text);">${i18n.trueAnswer}</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="segments[${segmentId}][questions][${questionId}][correct_answer]" value="false" required>
                        <span style="color: var(--text);">${i18n.falseAnswer}</span>
                    </label>
                </div>
            </div>`;
    } else if (questionType === 'short_answer') {
        questionHtml += `
            <div class="form-group">
                <label class="form-label">${i18n.correctAnswer}</label>
                <input type="text" name="segments[${segmentId}][questions][${questionId}][correct_answer]" class="quiz-question-input" placeholder="${i18n.answerCorrectPlaceholder}" required>
            </div>
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="segments[${segmentId}][questions][${questionId}][case_sensitive]" value="1">
                    <span style="color: var(--text);">${i18n.caseSensitive}</span>
                </label>
            </div>`;
    }

    questionHtml += `</div>`;
    container.insertAdjacentHTML('beforeend', questionHtml);
    updateStats();
}

function addQuestionAnswer(questionId, segmentId) {
    const answersContainer = document.getElementById(`answers_${questionId}`);
    const answerCount = answersContainer.querySelectorAll('.quiz-answer').length;

    answersContainer.insertAdjacentHTML('beforeend', `
        <div class="quiz-answer">
            <input type="radio" name="segments[${segmentId}][questions][${questionId}][correct_answer]" value="${answerCount}" class="quiz-answer-radio">
            <input type="text" name="segments[${segmentId}][questions][${questionId}][answers][${answerCount}]" class="quiz-answer-input" placeholder="${i18n.placeholderAnswer} ${answerCount + 1}" required>
            <button type="button" class="quiz-answer-remove" onclick="removeQuestionAnswer(this)">x</button>
        </div>
    `);

    updateCourseOutline();
}

function removeQuestionAnswer(button) {
    const answersContainer = button.closest('.quiz-answers');
    if (!answersContainer) {
        return;
    }

    button.closest('.quiz-answer')?.remove();

    const firstRadio = answersContainer.querySelector('.quiz-answer-radio');
    if (firstRadio && !answersContainer.querySelector('.quiz-answer-radio:checked')) {
        firstRadio.checked = true;
    }

    reindexQuestionAnswers(answersContainer);
    updateCourseOutline();
}

function reindexQuestionAnswers(answersContainer) {
    const firstRadio = answersContainer.querySelector('.quiz-answer-radio');
    if (!firstRadio) {
        return;
    }

    const baseName = firstRadio.name.replace(/\[correct_answer\]$/, '');

    answersContainer.querySelectorAll('.quiz-answer').forEach((answer, index) => {
        const radio = answer.querySelector('.quiz-answer-radio');
        const input = answer.querySelector('.quiz-answer-input');

        radio.value = index;
        radio.name = `${baseName}[correct_answer]`;
        input.name = `${baseName}[answers][${index}]`;
        input.placeholder = `${i18n.placeholderAnswer} ${index + 1}`;
    });
}

function removeQuestion(button) {
    button.closest('.quiz-question').remove();
    updateStats();
    updateCourseOutline();
}
