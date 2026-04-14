function addQuizAnswer(blockId, segmentId = 1) {
    const answersContainer = document.getElementById(`quiz-answers-${blockId}`);
    if (!answersContainer) {
        return;
    }

    const answerCount = answersContainer.querySelectorAll('.quiz-answer').length;
    const answerId = answerCount;
    const baseName = `segments[${segmentId}][blocks][${blockId}]`;

    const answerHtml = `
        <div class="quiz-answer" data-answer-id="${answerId}">
            <input type="radio" name="${baseName}[correct_answer]" value="${answerId}" class="quiz-answer-radio" onchange="highlightCorrectAnswer(${blockId}, ${answerId})" ${answerId === 0 ? 'checked' : ''}>
            <input type="text" name="${baseName}[answers][${answerId}]" class="quiz-answer-input" placeholder="${i18n.placeholderAnswer} ${answerId + 1}" required>
            <button type="button" class="quiz-answer-remove" onclick="removeQuizAnswer(this, ${blockId})" title="${i18n.removeQuestion}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    `;

    answersContainer.insertAdjacentHTML('beforeend', answerHtml);

    if (answerId === 0) {
        highlightCorrectAnswer(blockId, 0);
    }

    updateCourseOutline();
}

function removeQuizAnswer(button, blockId) {
    const answersContainer = document.getElementById(`quiz-answers-${blockId}`);
    const answers = answersContainer.querySelectorAll('.quiz-answer');

    if (answers.length <= 2) {
        alert(i18n.minAnswersValidation);
        return;
    }

    const answerElement = button.closest('.quiz-answer');
    const wasCorrect = answerElement.querySelector('.quiz-answer-radio').checked;
    answerElement.remove();

    if (wasCorrect) {
        const remainingAnswers = answersContainer.querySelectorAll('.quiz-answer');
        if (remainingAnswers.length > 0) {
            const firstRadio = remainingAnswers[0].querySelector('.quiz-answer-radio');
            firstRadio.checked = true;
            highlightCorrectAnswer(blockId, firstRadio.value);
        }
    }

    reindexQuizAnswers(blockId);
    updateCourseOutline();
}

function highlightCorrectAnswer(blockId, answerId) {
    const answersContainer = document.getElementById(`quiz-answers-${blockId}`);
    const answers = answersContainer.querySelectorAll('.quiz-answer');

    answers.forEach((answer, index) => {
        if (index == answerId) {
            answer.classList.add('correct');
        } else {
            answer.classList.remove('correct');
        }
    });
}

function reindexQuizAnswers(blockId) {
    const answersContainer = document.getElementById(`quiz-answers-${blockId}`);
    const answers = answersContainer.querySelectorAll('.quiz-answer');
    const block = answersContainer.closest('.content-block');
    const typeInput = block?.querySelector('input[type="hidden"][name$="[type]"]');

    if (!typeInput) {
        return;
    }

    const baseName = typeInput.name.replace(/\[type\]$/, '');

    answers.forEach((answer, index) => {
        answer.dataset.answerId = index;

        const radio = answer.querySelector('.quiz-answer-radio');
        const input = answer.querySelector('.quiz-answer-input');

        radio.value = index;
        radio.name = `${baseName}[correct_answer]`;
        radio.setAttribute('onchange', `highlightCorrectAnswer(${blockId}, ${index})`);

        input.name = `${baseName}[answers][${index}]`;
        input.placeholder = `${i18n.placeholderAnswer} ${index + 1}`;
    });
}
