        const existingSegments = @json($lesson->segments ?? []);

        function initializeFromExisting() {
            if (!existingSegments || existingSegments.length === 0) {
                addDefaultContentSegment();
                return;
            }

            existingSegments.forEach(seg => {
                if (seg.type === 'exam') {
                    addExamSegmentWithData(seg);
                } else {
                    addContentSegmentWithData(seg);
                }
                if (seg.id >= maxSegmentId) {
                    maxSegmentId = seg.id + 1;
                }
            });

            segmentCounter = maxSegmentId + 1;
        }

        function addDefaultContentSegment() {
            const newSegmentId = 1;
            segments.push({
                id: newSegmentId,
                label: `${i18n.contentSegment} 1`,
                type: 'content',
                customName: ''
            });
            const segmentHtml = renderContentSegment(newSegmentId, { custom_name: '', blocks: [] });
            document.getElementById('segmentsContainer').insertAdjacentHTML('beforeend', segmentHtml);
            initializeSegments();
        }

        function addContentSegmentWithData(segmentData) {
            segments.push({
                id: segmentData.id,
                label: segmentData.custom_name || `${i18n.contentSegment} ${contentSegmentIndex++}`,
                type: 'content',
                customName: segmentData.custom_name || ''
            });
            const segmentHtml = renderContentSegment(segmentData.id, segmentData);
            document.getElementById('segmentsContainer').insertAdjacentHTML('beforeend', segmentHtml);
            if (segmentData.blocks && Array.isArray(segmentData.blocks)) {
                segmentData.blocks.forEach(block => {
                    blockCounter = Math.max(blockCounter, block.id + 1);
                    renderBlockFromData(segmentData.id, block);
                });
            }
        }

        function addExamSegmentWithData(segmentData) {
            segments.push({
                id: segmentData.id,
                label: segmentData.custom_name || `${i18n.examIndexLabel}`,
                type: 'exam',
                customName: segmentData.custom_name || ''
            });
            const segmentHtml = renderExamSegment(segmentData.id, segmentData);
            document.getElementById('segmentsContainer').insertAdjacentHTML('beforeend', segmentHtml);
            if (segmentData.questions && Array.isArray(segmentData.questions)) {
                segmentData.questions.forEach(question => {
                    blockCounter = Math.max(blockCounter, question.id + 1);
                    renderQuestionFromData(segmentData.id, question);
                });
            }
        }

        function renderContentSegment(segmentId, segmentData) {
            return `
                <div class="segment-container" data-segment="${segmentId}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h2 class="segment-header" style="margin: 0;">${segmentData.custom_name || i18n.contentSegment}</h2>
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">${i18n.segmentName}</label>
                        <input type="text" name="segments[${segmentId}][custom_name]" class="form-input segment-name-input" placeholder="${i18n.segmentNamePlaceholder}" value="${segmentData.custom_name || ''}" data-segment-id="${segmentId}">
                    </div>
                    <div class="content-builder">
                        <div class="builder-header">
                            <h3 class="builder-title">${i18n.contentSegment}</h3>
                        </div>
                        <div id="contentBlocks_${segmentId}" class="content-blocks">
                            <div class="empty-builder">
                                <div class="empty-builder-icon">B</div>
                                <p>${i18n.noContent}</p>
                            </div>
                        </div>
                        <div class="builder-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="addTextBlock(${segmentId})">+ ${i18n.blockText}</button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="addSubheadingBlock(${segmentId})">${i18n.blockSubheading}</button>
                            <div class="content-dropdown">
                                <button type="button" class="btn btn-secondary btn-sm">${i18n.addContent} v</button>
                                <div class="dropdown-menu">
                                    <button type="button" onclick="addImageBlock(${segmentId})">${i18n.blockImage}</button>
                                    <button type="button" onclick="addVideoBlock(${segmentId})">${i18n.blockVideo}</button>
                                    <button type="button" onclick="addFileBlock(${segmentId})">${i18n.blockFile}</button>
                                    <button type="button" onclick="addCalloutBlock(${segmentId})">${i18n.blockCallout}</button>
                                    <button type="button" onclick="addCodeBlock(${segmentId})">${i18n.blockCode}</button>
                                    <button type="button" onclick="addDividerBlock(${segmentId})">${i18n.blockDivider}</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="addQuizBlock(${segmentId})">${i18n.blockQuiz}</button>
                        </div>
                    </div>
                    <div class="segment-actions">
                        <button type="button" class="segment-remove-btn" onclick="removeSegment(${segmentId})">${i18n.removeSegment}</button>
                    </div>
                </div>
            `;
        }

        function renderExamSegment(segmentId, segmentData) {
            const examSettings = segmentData.exam_settings || { time_limit: 0, passing_score: 60 };
            return `
                <div class="segment-container" data-segment="${segmentId}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h2 class="segment-header" style="margin: 0;">${segmentData.custom_name || i18n.examIndexLabel}</h2>
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">${i18n.segmentName}</label>
                        <input type="text" name="segments[${segmentId}][custom_name]" class="form-input segment-name-input" placeholder="${i18n.segmentNamePlaceholder}" value="${segmentData.custom_name || ''}" data-segment-id="${segmentId}">
                    </div>
                    <div class="exam-settings">
                        <h3 style="color: var(--text); margin-bottom: 15px; font-size: 14px; font-weight: 600;">${i18n.examSettings}</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">${i18n.timeLimit}</label>
                                <input type="number" name="segments[${segmentId}][exam_settings][time_limit]" class="form-input" placeholder="${i18n.timeLimitHelp}" value="${examSettings.time_limit || 0}" min="0">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">${i18n.passingScore}</label>
                                <input type="number" name="segments[${segmentId}][exam_settings][passing_score]" class="form-input" value="${examSettings.passing_score || 60}" min="0" max="100">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">${i18n.examQuestions}</label>
                        <div id="quizContainer_${segmentId}" class="quiz-container">
                            <div class="empty-builder">
                                <div class="empty-builder-icon">B</div>
                                <p>${i18n.noQuestions}</p>
                            </div>
                        </div>
                        <div class="content-dropdown" style="margin-top: 16px;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="event.preventDefault(); event.stopPropagation(); this.parentElement.classList.toggle('open');">${i18n.addQuestion} v</button>
                            <div class="dropdown-menu">
                                <button type="button" onclick="event.preventDefault(); event.stopPropagation(); this.parentElement.parentElement.classList.remove('open'); addExamQuestion(${segmentId}, 'multiple_choice')">${i18n.questionMultipleChoice}</button>
                                <button type="button" onclick="event.preventDefault(); event.stopPropagation(); this.parentElement.parentElement.classList.remove('open'); addExamQuestion(${segmentId}, 'true_false')">${i18n.questionTrueFalse}</button>
                                <button type="button" onclick="event.preventDefault(); event.stopPropagation(); this.parentElement.parentElement.classList.remove('open'); addExamQuestion(${segmentId}, 'short_answer')">${i18n.questionShortAnswer}</button>
                            </div>
                        </div>
                    </div>
                    <div class="segment-actions">
                        <button type="button" class="segment-remove-btn" onclick="removeSegment(${segmentId})">${i18n.removeSegment}</button>
                    </div>
                </div>
            `;
        }

        function renderBlockFromData(segmentId, blockData) {
            const blockId = blockData.id;
            const container = document.getElementById(`contentBlocks_${segmentId}`);

            if (!container) {
                console.warn(`contentBlocks_${segmentId} not found`, blockData);
                return;
            }

            const empty = container.querySelector('.empty-builder');
            if (empty) empty.remove();

            let blockHtml = `
                <div class="content-block" data-block-id="${blockId}" data-block-type="${blockData.type}">
                    <div class="block-header">
                        <span class="block-type">${getBlockTypeLabel(blockData.type)}</span>
                        <div class="block-actions">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">x</button>
                        </div>
                    </div>
            `;

            switch (blockData.type) {
                case 'text':
                case 'subheading':
                    blockHtml += `
                        <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="${blockData.type}">
                        <textarea name="segments[${segmentId}][blocks][${blockId}][content]" class="block-textarea" placeholder="${i18n.contentPlaceholderText}">${blockData.content || ''}</textarea>
                    `;
                    break;

                case 'video':
                    blockHtml += `
                        <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="video">
                        <label class="form-label">${i18n.videoUrlLabel}</label>
                        <input type="url" name="segments[${segmentId}][blocks][${blockId}][content]" class="video-url-input" placeholder="https://..." value="${blockData.content || ''}">
                    `;
                    break;

                case 'image':
                    blockHtml += `
                        <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="image">
                        <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][existing_path]" value="${blockData.path || ''}">
                        <label class="form-label">${i18n.blockImageSelect}</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="block_image_${blockId}" name="segments[${segmentId}][blocks][${blockId}][image]" accept="image/*" onchange="updateBlockFileName(this, 'block_image_${blockId}_name')">
                            <label for="block_image_${blockId}" class="file-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                                ${i18n.blockImageSelect}
                            </label>
                            <p class="file-name" id="block_image_${blockId}_name">${blockData.path ? blockData.path.split('/').pop() : i18n.noFileChosen}</p>
                            ${blockData.path && blockData.path.includes('lesson-images') ? `<img src="/storage/${blockData.path}" class="block-image-preview">` : ''}
                        </div>
                        <label class="form-label" style="margin-top: 12px;">${i18n.imageCaptionLabel}</label>
                        <textarea name="segments[${segmentId}][blocks][${blockId}][content]" class="block-textarea" placeholder="${i18n.contentPlaceholderText}">${blockData.caption || ''}</textarea>
                    `;
                    break;

                case 'file':
                    blockHtml += `
                        <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="file">
                        <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][existing_path]" value="${blockData.path || ''}">
                        <label class="form-label">${i18n.blockFileSelect}</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="block_file_${blockId}" name="segments[${segmentId}][blocks][${blockId}][file]" onchange="updateBlockFileName(this, 'block_file_${blockId}_name')">
                            <label for="block_file_${blockId}" class="file-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                ${i18n.blockFileSelect}
                            </label>
                            <p class="file-name" id="block_file_${blockId}_name">${blockData.path ? blockData.path.split('/').pop() : i18n.noFileChosen}</p>
                        </div>
                    `;
                    break;

                case 'callout':
                    blockHtml += `
                        <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="callout">
                        <label class="form-label">${i18n.calloutTypeLabel}</label>
                        <select name="segments[${segmentId}][blocks][${blockId}][callout_type]" class="form-select">
                            <option value="info" ${blockData.callout_type === 'info' ? 'selected' : ''}>${i18n.calloutInfo}</option>
                            <option value="warning" ${blockData.callout_type === 'warning' ? 'selected' : ''}>${i18n.calloutWarning}</option>
                            <option value="success" ${blockData.callout_type === 'success' ? 'selected' : ''}>${i18n.calloutSuccess}</option>
                            <option value="danger" ${blockData.callout_type === 'danger' ? 'selected' : ''}>${i18n.calloutDanger}</option>
                        </select>
                        <label class="form-label" style="margin-top: 12px;">${i18n.contentPlaceholderCallout}</label>
                        <textarea name="segments[${segmentId}][blocks][${blockId}][content]" class="block-textarea" placeholder="${i18n.contentPlaceholderCallout}">${blockData.content || ''}</textarea>
                    `;
                    break;

                case 'code':
                    blockHtml += `
                        <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="code">
                        <label class="form-label">${i18n.codeLanguageLabel}</label>
                        <select name="segments[${segmentId}][blocks][${blockId}][language]" class="form-select">
                            <option value="javascript" ${blockData.language === 'javascript' ? 'selected' : ''}>JavaScript</option>
                            <option value="python" ${blockData.language === 'python' ? 'selected' : ''}>Python</option>
                            <option value="html" ${blockData.language === 'html' ? 'selected' : ''}>HTML</option>
                            <option value="css" ${blockData.language === 'css' ? 'selected' : ''}>CSS</option>
                            <option value="sql" ${blockData.language === 'sql' ? 'selected' : ''}>SQL</option>
                            <option value="php" ${blockData.language === 'php' ? 'selected' : ''}>PHP</option>
                            <option value="java" ${blockData.language === 'java' ? 'selected' : ''}>Java</option>
                        </select>
                        <textarea name="segments[${segmentId}][blocks][${blockId}][content]" class="block-textarea" placeholder="${i18n.codePlaceholder}" style="font-family: monospace;">${blockData.content || ''}</textarea>
                    `;
                    break;

                case 'divider':
                    blockHtml += `
                        <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="divider">
                        <div class="divider-preview"></div>
                    `;
                    break;

                case 'quiz':
                    blockHtml += `
                        <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="quiz">
                        <label class="form-label">${i18n.question}</label>
                        <input type="text" name="segments[${segmentId}][blocks][${blockId}][question]" class="quiz-question-input" placeholder="${i18n.questionPlaceholder}" value="${blockData.question || ''}" required>
                        <label class="form-label" style="margin-top: 12px;">${i18n.answersInstruction}</label>
                        <p class="quiz-hint">${i18n.correctAnswerHint}</p>
                        <div id="quiz-answers-${blockId}" class="quiz-answers">
                    `;
                    if (blockData.answers && Array.isArray(blockData.answers)) {
                        blockData.answers.forEach((answer, idx) => {
                            blockHtml += `
                                <div class="quiz-answer">
                                    <input type="radio" name="segments[${segmentId}][blocks][${blockId}][correct_answer]" value="${idx}" class="quiz-answer-radio" onchange="highlightCorrectAnswer(${blockId}, ${idx})" ${blockData.correct_answer == idx ? 'checked' : ''}>
                                    <input type="text" name="segments[${segmentId}][blocks][${blockId}][answers][${idx}]" class="quiz-answer-input" placeholder="${i18n.placeholderAnswer} ${idx + 1}" value="${answer || ''}" required>
                                    <button type="button" class="quiz-answer-remove" onclick="removeQuizAnswer(this, ${blockId})">x</button>
                                </div>
                            `;
                        });
                    } else {
                        blockHtml += `
                            <div class="quiz-answer">
                                <input type="radio" name="segments[${segmentId}][blocks][${blockId}][correct_answer]" value="0" class="quiz-answer-radio" onchange="highlightCorrectAnswer(${blockId}, 0)" checked>
                                <input type="text" name="segments[${segmentId}][blocks][${blockId}][answers][0]" class="quiz-answer-input" placeholder="${i18n.placeholderAnswer} 1" required>
                                <button type="button" class="quiz-answer-remove" onclick="removeQuizAnswer(this, ${blockId})">x</button>
                            </div>
                        `;
                    }
                    blockHtml += `
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addQuizAnswer(${blockId}, ${segmentId})" style="margin-top: 8px;">+ ${i18n.addAnswer}</button>
                    `;
                    break;
            }

            blockHtml += `</div>`;
            container.insertAdjacentHTML('beforeend', blockHtml);

            if (blockData.type === 'quiz') {
                highlightCorrectAnswer(blockId, blockData.correct_answer ?? 0);
            }
        }

        function renderQuestionFromData(segmentId, questionData) {
            const container = document.getElementById(`quizContainer_${segmentId}`);

            if (!container) {
                console.warn(`quizContainer_${segmentId} not found`, questionData);
                return;
            }

            const empty = container.querySelector('.empty-builder');
            if (empty) empty.remove();

            const questionId = questionData.id;
            let questionHtml = `
                <div class="quiz-question" data-question-id="${questionId}" data-question-type="${questionData.type}">
                    <div class="quiz-question-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid rgba(168, 85, 247, 0.2);">
                        <span style="font-size: 12px; color: var(--muted); font-weight: 600; text-transform: uppercase;">`;

            if (questionData.type === 'multiple_choice') questionHtml += i18n.questionMultipleChoice;
            else if (questionData.type === 'true_false') questionHtml += i18n.questionTrueFalse;
            else if (questionData.type === 'short_answer') questionHtml += i18n.questionShortAnswer;

            questionHtml += `</span>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(this)">${i18n.removeQuestion}</button>
                    </div>
                    <input type="hidden" name="segments[${segmentId}][questions][${questionId}][type]" value="${questionData.type}">
                    <div class="form-group">
                        <label class="form-label">${i18n.question}</label>
                        <input type="text" name="segments[${segmentId}][questions][${questionId}][question]" class="quiz-question-input" placeholder="${i18n.questionPlaceholder}" value="${questionData.question || ''}" required>
                    </div>`;

            if (questionData.type === 'multiple_choice') {
                questionHtml += `
                    <div class="form-group">
                        <label class="form-label">${i18n.answers}</label>
                        <div class="quiz-answers" id="answers_${questionId}">
                `;
                if (questionData.answers && Array.isArray(questionData.answers)) {
                    questionData.answers.forEach((answer, idx) => {
                        questionHtml += `
                            <div class="quiz-answer">
                                <input type="radio" name="segments[${segmentId}][questions][${questionId}][correct_answer]" value="${idx}" class="quiz-answer-radio" ${questionData.correct_answer == idx ? 'checked' : ''}>
                                <input type="text" name="segments[${segmentId}][questions][${questionId}][answers][${idx}]" class="quiz-answer-input" placeholder="${i18n.placeholderAnswer} ${idx + 1}" value="${answer || ''}" required>
                                <button type="button" class="quiz-answer-remove" onclick="removeQuestionAnswer(this)">x</button>
                            </div>
                        `;
                    });
                } else {
                    questionHtml += `
                        <div class="quiz-answer">
                            <input type="radio" name="segments[${segmentId}][questions][${questionId}][correct_answer]" value="0" class="quiz-answer-radio">
                            <input type="text" name="segments[${segmentId}][questions][${questionId}][answers][0]" class="quiz-answer-input" placeholder="${i18n.placeholderAnswer} 1" required>
                            <button type="button" class="quiz-answer-remove" onclick="removeQuestionAnswer(this)">x</button>
                        </div>
                    `;
                }
                questionHtml += `
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addQuestionAnswer(${questionId}, ${segmentId})" style="margin-top: 8px;">+ ${i18n.addAnswer}</button>
                    </div>`;
            } else if (questionData.type === 'true_false') {
                questionHtml += `
                    <div class="form-group">
                        <label class="form-label">${i18n.correctAnswer}</label>
                        <div style="display: flex; gap: 20px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" name="segments[${segmentId}][questions][${questionId}][correct_answer]" value="true" ${questionData.correct_answer === 'true' || questionData.correct_answer === true ? 'checked' : ''} required>
                                <span style="color: var(--text);">${i18n.trueAnswer}</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" name="segments[${segmentId}][questions][${questionId}][correct_answer]" value="false" ${questionData.correct_answer === 'false' || questionData.correct_answer === false ? 'checked' : ''} required>
                                <span style="color: var(--text);">${i18n.falseAnswer}</span>
                            </label>
                        </div>
                    </div>`;
            } else if (questionData.type === 'short_answer') {
                questionHtml += `
                    <div class="form-group">
                        <label class="form-label">${i18n.correctAnswer}</label>
                        <input type="text" name="segments[${segmentId}][questions][${questionId}][correct_answer]" class="quiz-question-input" placeholder="${i18n.answerCorrectPlaceholder}" value="${questionData.correct_answer || ''}" required>
                    </div>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="segments[${segmentId}][questions][${questionId}][case_sensitive]" value="1" ${questionData.case_sensitive ? 'checked' : ''}>
                            <span style="color: var(--text);">${i18n.caseSensitive}</span>
                        </label>
                    </div>`;
            }

            questionHtml += `</div>`;
            container.insertAdjacentHTML('beforeend', questionHtml);
        }

        function getBlockTypeLabel(type) {
            const labels = {
                'text': i18n.blockText,
                'subheading': i18n.blockSubheading,
                'image': i18n.blockImage,
                'video': i18n.blockVideo,
                'file': i18n.blockFile,
                'callout': i18n.blockCallout,
                'code': i18n.blockCode,
                'divider': i18n.blockDivider,
                'quiz': i18n.blockQuiz
            };
            return labels[type] || type;
        }
