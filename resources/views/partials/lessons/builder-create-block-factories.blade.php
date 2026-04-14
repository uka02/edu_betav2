        function addTextBlock(segmentId = 1) {
            const container = document.getElementById(`contentBlocks_${segmentId}`) || document.getElementById('contentBlocks');
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();

            const blockId = blockCounter++;
            const blockHtml = `
                <div class="content-block" data-block-id="${blockId}">
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="text">
                    <div class="block-header">
                        <span class="block-type">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 7V4h16v3M9 20h6M12 4v16"/>
                            </svg>
                            ${i18n.blockText}
                        </span>
                        <div class="block-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockUp(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="18 15 12 9 6 15"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockDown(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <textarea name="segments[${segmentId}][blocks][${blockId}][content]" class="block-textarea" placeholder="${i18n.contentPlaceholderText}" required></textarea>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', blockHtml);
            updateStats();
        }

        function addImageBlock(segmentId = 1) {
            const container = document.getElementById(`contentBlocks_${segmentId}`) || document.getElementById('contentBlocks');
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();

            const blockId = blockCounter++;
            const blockHtml = `
                <div class="content-block" data-block-id="${blockId}">
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="image">
                    <div class="block-header">
                        <span class="block-type">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            ${i18n.blockImage}
                        </span>
                        <div class="block-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockUp(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="18 15 12 9 6 15"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockDown(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="file-input-wrapper">
                        <input type="file" id="block-image-${blockId}" name="segments[${segmentId}][blocks][${blockId}][image]" class="file-input" accept="image/*" onchange="previewBlockImage(this, ${blockId})" required>
                        <label for="block-image-${blockId}" class="file-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            ${i18n.blockImageSelect}
                        </label>
                        <p class="file-name" id="block-image-name-${blockId}">${i18n.placeholderFileNotChosen}</p>
                        <img id="block-image-preview-${blockId}" class="block-image-preview" style="display: none;">
                    </div>
                    <input type="text" name="segments[${segmentId}][blocks][${blockId}][content]" class="form-input" placeholder="${i18n.imageCaptionLabel}" style="margin-top: 12px;">
                </div>
            `;
            container.insertAdjacentHTML('beforeend', blockHtml);
            updateStats();
        }

        function addSubheadingBlock(segmentId = 1) {
            const container = document.getElementById(`contentBlocks_${segmentId}`) || document.getElementById('contentBlocks');
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();

            const blockId = blockCounter++;
            const blockHtml = `
                <div class="content-block" data-block-id="${blockId}">
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="subheading">
                    <div class="block-header">
                        <span class="block-type">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <text x="4" y="16" font-size="12" font-weight="700">H</text>
                            </svg>
                            ${i18n.blockSubheading}
                        </span>
                        <div class="block-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockUp(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="18 15 12 9 6 15"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockDown(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <input type="text" name="segments[${segmentId}][blocks][${blockId}][content]" class="form-input" placeholder="${i18n.contentPlaceholderSubheading}" required>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', blockHtml);
            updateStats();
        }

        function addVideoBlock(segmentId = 1) {
            const container = document.getElementById(`contentBlocks_${segmentId}`) || document.getElementById('contentBlocks');
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();

            const blockId = blockCounter++;
            const blockHtml = `
                <div class="content-block" data-block-id="${blockId}">
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="video">
                    <div class="block-header">
                        <span class="block-type">${i18n.blockVideo}</span>
                        <div class="block-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockUp(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="18 15 12 9 6 15"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockDown(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="video-url-wrapper" style="margin-top:12px;">
                        <label class="form-label" for="block-video-url-${blockId}">${i18n.videoUrlLabel}</label>
                        <input type="url" id="block-video-url-${blockId}" name="segments[${segmentId}][blocks][${blockId}][content]" class="form-input video-url-input" placeholder="{{ __('lessons.video_url_placeholder') }}" required>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', blockHtml);
            updateStats();
        }

        function addFileBlock(segmentId = 1) {
            const container = document.getElementById(`contentBlocks_${segmentId}`) || document.getElementById('contentBlocks');
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();

            const blockId = blockCounter++;
            const blockHtml = `
                <div class="content-block" data-block-id="${blockId}">
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="file">
                    <div class="block-header">
                        <span class="block-type">${i18n.blockFile}</span>
                        <div class="block-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockUp(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="18 15 12 9 6 15"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockDown(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="file-input-wrapper">
                        <input type="file" id="block-file-${blockId}" name="segments[${segmentId}][blocks][${blockId}][file]" class="file-input" onchange="updateFileName(this, ${blockId})" required>
                        <label for="block-file-${blockId}" class="file-label">${i18n.blockFileSelect}</label>
                        <p class="file-name" id="block-file-name-${blockId}">${i18n.placeholderFileNotChosen}</p>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', blockHtml);
            updateStats();
        }

        function addCalloutBlock(segmentId = 1) {
            const container = document.getElementById(`contentBlocks_${segmentId}`) || document.getElementById('contentBlocks');
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();

            const blockId = blockCounter++;
            const blockHtml = `
                <div class="content-block" data-block-id="${blockId}">
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="callout">
                    <div class="block-header">
                        <span class="block-type">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 8v4m0 4v.01M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"></path>
                            </svg>
                            ${i18n.blockCallout}
                        </span>
                        <div class="block-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockUp(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="18 15 12 9 6 15"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockDown(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="callout-type-${blockId}">${i18n.calloutType}</label>
                        <select id="callout-type-${blockId}" name="segments[${segmentId}][blocks][${blockId}][callout_type]" class="form-select">
                            <option value="info">${i18n.calloutInfo}</option>
                            <option value="warning">${i18n.calloutWarning}</option>
                            <option value="success">${i18n.calloutSuccess}</option>
                            <option value="danger">${i18n.calloutDanger}</option>
                        </select>
                    </div>
                    <textarea name="segments[${segmentId}][blocks][${blockId}][content]" class="block-textarea" placeholder="${i18n.contentPlaceholderCallout}" required></textarea>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', blockHtml);
            updateStats();
        }

        function addCodeBlock(segmentId = 1) {
            const container = document.getElementById(`contentBlocks_${segmentId}`) || document.getElementById('contentBlocks');
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();

            const blockId = blockCounter++;
            const blockHtml = `
                <div class="content-block" data-block-id="${blockId}">
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="code">
                    <div class="block-header">
                        <span class="block-type">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="16 18 22 12 16 6"></polyline>
                                <polyline points="8 6 2 12 8 18"></polyline>
                            </svg>
                            ${i18n.blockCode}
                        </span>
                        <div class="block-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockUp(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="18 15 12 9 6 15"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockDown(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="code-lang-${blockId}">${i18n.codeLanguage}</label>
                        <select id="code-lang-${blockId}" name="segments[${segmentId}][blocks][${blockId}][language]" class="form-select">
                            <option value="javascript">JavaScript</option>
                            <option value="python">Python</option>
                            <option value="php">PHP</option>
                            <option value="html">HTML</option>
                            <option value="css">CSS</option>
                            <option value="sql">SQL</option>
                            <option value="java">Java</option>
                        </select>
                    </div>
                    <textarea name="segments[${segmentId}][blocks][${blockId}][content]" class="code-block" placeholder="${i18n.codePlaceholder}" required style="font-family: monospace; min-height: 150px;"></textarea>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', blockHtml);
            updateStats();
        }

        function addDividerBlock(segmentId = 1) {
            const container = document.getElementById(`contentBlocks_${segmentId}`) || document.getElementById('contentBlocks');
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();

            const blockId = blockCounter++;
            const blockHtml = `
                <div class="content-block" data-block-id="${blockId}">
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="divider">
                    <div class="block-header">
                        <span class="block-type">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            ${i18n.blockDivider}
                        </span>
                        <div class="block-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockUp(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="18 15 12 9 6 15"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockDown(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="divider-preview"></div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', blockHtml);
            updateStats();
        }

        function addQuizBlock(segmentId = 1) {
            const container = document.getElementById(`contentBlocks_${segmentId}`) || document.getElementById('contentBlocks');
            const empty = container.querySelector('.empty-builder');
            if (empty) empty.remove();

            const blockId = blockCounter++;
            const blockHtml = `
                <div class="content-block quiz-block" data-block-id="${blockId}">
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="quiz">
                    <div class="block-header">
                        <span class="block-type">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            ${i18n.blockQuiz}
                        </span>
                        <div class="block-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockUp(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="18 15 12 9 6 15"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="moveBlockDown(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="quiz-question">
                            <label class="form-label">${i18n.question}</label>
                            <input type="text" name="segments[${segmentId}][blocks][${blockId}][question]" class="quiz-question-input" placeholder="${i18n.questionPlaceholder}" required>
                        </div>
                        <div class="quiz-answers" id="quiz-answers-${blockId}">
                            <label class="form-label">${i18n.answersInstruction}</label>
                            <p class="quiz-hint">${i18n.correctAnswerHint}</p>
                        </div>
                        <button type="button" class="quiz-add-answer" onclick="addQuizAnswer(${blockId}, ${segmentId})">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            ${i18n.addAnswer}
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', blockHtml);
            for (let i = 0; i < 4; i++) {
                addQuizAnswer(blockId, segmentId);
            }
            updateStats();
        }

        @include('partials.lessons.builder-content-quiz-helpers')

        function removeBlock(button) {
            const block = button.closest('.content-block');
            const container = block.closest('.content-blocks');
            block.remove();

            if (container && container.children.length === 0) {
                container.innerHTML = `
                    <div class="empty-builder">
                        <div class="empty-builder-icon">B</div>
                        <p>${i18n.addContentEmpty}</p>
                    </div>
                `;
            }
            
            reindexBlocks();
        }

        function moveBlockUp(button) {
            const block = button.closest('.content-block');
            const prev = block.previousElementSibling;
            if (prev && !prev.classList.contains('empty-builder')) {
                block.parentNode.insertBefore(block, prev);
                reindexBlocks();
            }
        }

        function moveBlockDown(button) {
            const block = button.closest('.content-block');
            const next = block.nextElementSibling;
            if (next) {
                block.parentNode.insertBefore(next, block);
                reindexBlocks();
            }
        }

        function reindexBlocks() {
            updateStats();
        }

        function previewBlockImage(input, blockId) {
            const fileName = input.files[0] ? input.files[0].name : i18n.placeholderFileNotChosen;
            document.getElementById(`block-image-name-${blockId}`).textContent = fileName;

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                const preview = document.getElementById(`block-image-preview-${blockId}`);
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function updateFileName(input, blockId) {
            const fileName = input.files[0] ? input.files[0].name : i18n.placeholderFileNotChosen;
            document.getElementById(`block-file-name-${blockId}`).textContent = fileName;
        }
