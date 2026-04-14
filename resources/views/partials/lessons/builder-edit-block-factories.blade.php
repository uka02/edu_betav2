        function addTextBlock(segmentId) {
            const blockId = blockCounter++;
            const container = document.getElementById(`contentBlocks_${segmentId}`);
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();
            container.insertAdjacentHTML('beforeend', `
                <div class="content-block" data-block-id="${blockId}" data-block-type="text">
                    <div class="block-header">
                        <span class="block-type">${i18n.blockText}</span>
                        <div class="block-actions"><button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">x</button></div>
                    </div>
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="text">
                    <textarea name="segments[${segmentId}][blocks][${blockId}][content]" class="block-textarea" placeholder="${i18n.contentPlaceholderText}"></textarea>
                </div>
            `);
            updateStats();
        }

        function addSubheadingBlock(segmentId) {
            const blockId = blockCounter++;
            const container = document.getElementById(`contentBlocks_${segmentId}`);
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();
            container.insertAdjacentHTML('beforeend', `
                <div class="content-block" data-block-id="${blockId}" data-block-type="subheading">
                    <div class="block-header">
                        <span class="block-type">${i18n.blockSubheading}</span>
                        <div class="block-actions"><button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">x</button></div>
                    </div>
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="subheading">
                    <textarea name="segments[${segmentId}][blocks][${blockId}][content]" class="block-textarea" placeholder="${i18n.contentPlaceholderSubheading}"></textarea>
                </div>
            `);
            updateStats();
        }

        function addImageBlock(segmentId) {
            const blockId = blockCounter++;
            const container = document.getElementById(`contentBlocks_${segmentId}`);
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();
            container.insertAdjacentHTML('beforeend', `
                <div class="content-block" data-block-id="${blockId}" data-block-type="image">
                    <div class="block-header">
                        <span class="block-type">${i18n.blockImage}</span>
                        <div class="block-actions"><button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">x</button></div>
                    </div>
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="image">
                    <label class="form-label">${i18n.blockImageSelect}</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="block_image_${blockId}" name="segments[${segmentId}][blocks][${blockId}][image]" accept="image/*" onchange="updateBlockFileName(this, 'block_image_${blockId}_name')">
                        <label for="block_image_${blockId}" class="file-label">${i18n.blockImageSelect}</label>
                        <p class="file-name" id="block_image_${blockId}_name">${i18n.noFileChosen}</p>
                    </div>
                    <label class="form-label" style="margin-top: 12px;">${i18n.imageCaptionLabel}</label>
                    <textarea name="segments[${segmentId}][blocks][${blockId}][content]" class="block-textarea" placeholder="${i18n.contentPlaceholderText}"></textarea>
                </div>
            `);
            updateStats();
        }

        function addVideoBlock(segmentId) {
            const blockId = blockCounter++;
            const container = document.getElementById(`contentBlocks_${segmentId}`);
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();
            container.insertAdjacentHTML('beforeend', `
                <div class="content-block" data-block-id="${blockId}" data-block-type="video">
                    <div class="block-header">
                        <span class="block-type">${i18n.blockVideo}</span>
                        <div class="block-actions"><button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">x</button></div>
                    </div>
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="video">
                    <label class="form-label">${i18n.videoUrlLabel}</label>
                    <input type="url" name="segments[${segmentId}][blocks][${blockId}][content]" class="video-url-input" placeholder="https://...">
                </div>
            `);
            updateStats();
        }

        function addFileBlock(segmentId) {
            const blockId = blockCounter++;
            const container = document.getElementById(`contentBlocks_${segmentId}`);
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();
            container.insertAdjacentHTML('beforeend', `
                <div class="content-block" data-block-id="${blockId}" data-block-type="file">
                    <div class="block-header">
                        <span class="block-type">${i18n.blockFile}</span>
                        <div class="block-actions"><button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">x</button></div>
                    </div>
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="file">
                    <label class="form-label">${i18n.blockFileSelect}</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="block_file_${blockId}" name="segments[${segmentId}][blocks][${blockId}][file]" onchange="updateBlockFileName(this, 'block_file_${blockId}_name')">
                        <label for="block_file_${blockId}" class="file-label">${i18n.blockFileSelect}</label>
                        <p class="file-name" id="block_file_${blockId}_name">${i18n.noFileChosen}</p>
                    </div>
                </div>
            `);
            updateStats();
        }

        function addCalloutBlock(segmentId) {
            const blockId = blockCounter++;
            const container = document.getElementById(`contentBlocks_${segmentId}`);
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();
            container.insertAdjacentHTML('beforeend', `
                <div class="content-block" data-block-id="${blockId}" data-block-type="callout">
                    <div class="block-header">
                        <span class="block-type">${i18n.blockCallout}</span>
                        <div class="block-actions"><button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">x</button></div>
                    </div>
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="callout">
                    <label class="form-label">${i18n.calloutTypeLabel}</label>
                    <select name="segments[${segmentId}][blocks][${blockId}][callout_type]" class="form-select">
                        <option value="info">${i18n.calloutInfo}</option>
                        <option value="warning">${i18n.calloutWarning}</option>
                        <option value="success">${i18n.calloutSuccess}</option>
                        <option value="danger">${i18n.calloutDanger}</option>
                    </select>
                    <label class="form-label" style="margin-top: 12px;">${i18n.contentPlaceholderCallout}</label>
                    <textarea name="segments[${segmentId}][blocks][${blockId}][content]" class="block-textarea" placeholder="${i18n.contentPlaceholderCallout}"></textarea>
                </div>
            `);
            updateStats();
        }

        function addCodeBlock(segmentId) {
            const blockId = blockCounter++;
            const container = document.getElementById(`contentBlocks_${segmentId}`);
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();
            container.insertAdjacentHTML('beforeend', `
                <div class="content-block" data-block-id="${blockId}" data-block-type="code">
                    <div class="block-header">
                        <span class="block-type">${i18n.blockCode}</span>
                        <div class="block-actions"><button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">x</button></div>
                    </div>
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="code">
                    <label class="form-label">${i18n.codeLanguageLabel}</label>
                    <select name="segments[${segmentId}][blocks][${blockId}][language]" class="form-select">
                        <option value="javascript">JavaScript</option>
                        <option value="python">Python</option>
                        <option value="html">HTML</option>
                        <option value="css">CSS</option>
                        <option value="sql">SQL</option>
                        <option value="php">PHP</option>
                        <option value="java">Java</option>
                    </select>
                    <textarea name="segments[${segmentId}][blocks][${blockId}][content]" class="block-textarea" placeholder="${i18n.codePlaceholder}" style="font-family: monospace;"></textarea>
                </div>
            `);
            updateStats();
        }

        function addDividerBlock(segmentId) {
            const blockId = blockCounter++;
            const container = document.getElementById(`contentBlocks_${segmentId}`);
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();
            container.insertAdjacentHTML('beforeend', `
                <div class="content-block" data-block-id="${blockId}" data-block-type="divider">
                    <div class="block-header">
                        <span class="block-type">${i18n.blockDivider}</span>
                        <div class="block-actions"><button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">x</button></div>
                    </div>
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="divider">
                    <div class="divider-preview"></div>
                </div>
            `);
            updateStats();
        }

        @include('partials.lessons.builder-content-quiz-helpers')

        function addQuizBlock(segmentId) {
            const blockId = blockCounter++;
            const container = document.getElementById(`contentBlocks_${segmentId}`);
            const emptyState = container.querySelector('.empty-builder');
            if (emptyState) emptyState.remove();
            container.insertAdjacentHTML('beforeend', `
                <div class="content-block" data-block-id="${blockId}" data-block-type="quiz">
                    <div class="block-header">
                        <span class="block-type">${i18n.blockQuiz}</span>
                        <div class="block-actions"><button type="button" class="btn btn-danger btn-sm" onclick="removeBlock(this)">x</button></div>
                    </div>
                    <input type="hidden" name="segments[${segmentId}][blocks][${blockId}][type]" value="quiz">
                    <label class="form-label">${i18n.question}</label>
                    <input type="text" name="segments[${segmentId}][blocks][${blockId}][question]" class="quiz-question-input" placeholder="${i18n.questionPlaceholder}" required>
                    <label class="form-label" style="margin-top: 12px;">${i18n.answersInstruction}</label>
                    <p class="quiz-hint">${i18n.correctAnswerHint}</p>
                    <div id="quiz-answers-${blockId}" class="quiz-answers">
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addQuizAnswer(${blockId}, ${segmentId})" style="margin-top: 8px;">+ ${i18n.addAnswer}</button>
                </div>
            `);

            for (let i = 0; i < 4; i++) {
                addQuizAnswer(blockId, segmentId);
            }

            updateStats();
        }

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

            updateStats();
        }

        function updateBlockFileName(input, displayId) {
            const fileName = input.files[0] ? input.files[0].name : i18n.noFileChosen;
            document.getElementById(displayId).textContent = fileName;
        }
