        function addContentSegment() {
            contentSegmentIndex++;
            const newSegmentId = segmentCounter;
            segmentCounter++;

            segments.push({
                id: newSegmentId,
                label: `${i18n.contentSegment} ${contentSegmentIndex}`,
                type: 'content',
                customName: ''
            });

            const contentArea = document.querySelector('.content-area');
            const newSegment = document.createElement('div');
            newSegment.className = 'segment-container';
            newSegment.setAttribute('data-segment', newSegmentId);
            newSegment.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h2 class="segment-header" style="margin: 0;">${i18n.contentSegment} ${contentSegmentIndex}</h2>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">${i18n.segmentName}</label>
                    <input type="text" name="segments[${newSegmentId}][custom_name]" class="form-input segment-name-input" placeholder="${i18n.segmentNamePlaceholder}" data-segment-id="${newSegmentId}">
                </div>
                
                <div class="content-builder">
                    <div class="builder-header">
                        <h3 class="builder-title">${i18n.contentSegment}</h3>
                    </div>

                    <div id="contentBlocks_${newSegmentId}" class="content-blocks">
                        <div class="empty-builder">
                            <div class="empty-builder-icon">B</div>
                            <p>${i18n.noContent}</p>
                        </div>
                    </div>

                    <div class="builder-actions">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addTextBlock(${newSegmentId})">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            ${i18n.blockText}
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addSubheadingBlock(${newSegmentId})">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <text x="4" y="16" font-size="12" font-weight="700">H</text>
                            </svg>
                            ${i18n.blockSubheading}
                        </button>
                        <div class="content-dropdown">
                            <button type="button" class="btn btn-secondary btn-sm">${i18n.addContent} v</button>
                            <div class="dropdown-menu">
                                <button type="button" onclick="addImageBlock(${newSegmentId})">${i18n.blockImage}</button>
                                <button type="button" onclick="addVideoBlock(${newSegmentId})">${i18n.blockVideo}</button>
                                <button type="button" onclick="addFileBlock(${newSegmentId})">${i18n.blockFile}</button>
                                <button type="button" onclick="addCalloutBlock(${newSegmentId})">${i18n.blockCallout}</button>
                                <button type="button" onclick="addCodeBlock(${newSegmentId})">${i18n.blockCode}</button>
                                <button type="button" onclick="addDividerBlock(${newSegmentId})">${i18n.blockDivider}</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addQuizBlock(${newSegmentId})">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            ${i18n.blockQuiz}
                        </button>
                    </div>
                </div>

                <div class="segment-actions">
                    <button type="button" class="segment-remove-btn" onclick="removeSegment(${newSegmentId})">${i18n.removeSegment}</button>
                </div>
            `;
            
            const formActions = document.querySelector('.form-actions');
            formActions.parentNode.insertBefore(newSegment, formActions);

            initializeSegments();
            switchSegment(newSegmentId);
        }

        function addExamSegment() {
            const newSegmentId = segmentCounter;
            segmentCounter++;
            const examIndex = newSegmentId - 1;

            segments.push({
                id: newSegmentId,
                label: `${i18n.examIndexLabel} ${examIndex}`,
                type: 'exam',
                customName: ''
            });

            const contentArea = document.querySelector('.content-area');
            const newSegment = document.createElement('div');
            newSegment.className = 'segment-container';
            newSegment.setAttribute('data-segment', newSegmentId);
            newSegment.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h2 class="segment-header" style="margin: 0;">${i18n.examIndexLabel} ${examIndex}</h2>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">${i18n.segmentName}</label>
                    <input type="text" name="segments[${newSegmentId}][custom_name]" class="form-input segment-name-input" placeholder="${i18n.segmentNamePlaceholder}" data-segment-id="${newSegmentId}">
                </div>
                
                <div class="exam-settings">
                    <h3 style="color: var(--text); margin-bottom: 15px; font-size: 14px; font-weight: 600;">${i18n.examSettings}</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label class="form-label">${i18n.timeLimit}</label>
                            <input type="number" name="segments[${newSegmentId}][exam_settings][time_limit]" class="form-input" placeholder="${i18n.timeLimitHelp}" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">${i18n.passingScore}</label>
                            <input type="number" name="segments[${newSegmentId}][exam_settings][passing_score]" class="form-input" placeholder="60" value="60" min="0" max="100">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">${i18n.examQuestions}</label>
                    <div id="quizContainer_${newSegmentId}" class="quiz-container">
                        <div class="empty-builder">
                            <div class="empty-builder-icon">Q</div>
                            <p>${i18n.noQuestions}</p>
                        </div>
                    </div>
                    
                    <div class="content-dropdown" style="margin-top: 16px;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="event.preventDefault(); event.stopPropagation(); this.parentElement.classList.toggle('open');">${i18n.addQuestion} v</button>
                        <div class="dropdown-menu">
                            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); this.parentElement.parentElement.classList.remove('open'); addExamQuestion(${newSegmentId}, 'multiple_choice')">${i18n.questionMultipleChoice}</button>
                            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); this.parentElement.parentElement.classList.remove('open'); addExamQuestion(${newSegmentId}, 'true_false')">${i18n.questionTrueFalse}</button>
                            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); this.parentElement.parentElement.classList.remove('open'); addExamQuestion(${newSegmentId}, 'short_answer')">${i18n.questionShortAnswer}</button>
                        </div>
                    </div>
                </div>

                <div class="segment-actions">
                    <button type="button" class="segment-remove-btn" onclick="removeSegment(${newSegmentId})">${i18n.removeSegment}</button>
                </div>
            `;
            
            const formActions = document.querySelector('.form-actions');
            formActions.parentNode.insertBefore(newSegment, formActions);

            initializeSegments();
            switchSegment(newSegmentId);
        }
