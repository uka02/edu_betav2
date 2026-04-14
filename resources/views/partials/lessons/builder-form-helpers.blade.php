function bindLessonBuilderFormHelpers(options = {}) {
    const {
        titleCheckUrl = null,
        excludeId = null,
        validatingMessage = '',
        duplicateMessage = '',
        minLengthMessage = '',
    } = options;

    const typeSelect = document.getElementById('type');
    const videoUrlGroup = document.getElementById('video-url-group');
    const documentGroup = document.getElementById('document-group');

    if (typeSelect && videoUrlGroup && documentGroup) {
        const syncLessonTypeFields = () => {
            videoUrlGroup.style.display = 'none';
            documentGroup.style.display = 'none';

            if (typeSelect.value === 'video') {
                videoUrlGroup.style.display = 'block';
            } else if (typeSelect.value === 'document') {
                documentGroup.style.display = 'block';
            }
        };

        typeSelect.addEventListener('change', syncLessonTypeFields);
        syncLessonTypeFields();
    }

    const documentInput = document.getElementById('document');
    const documentName = document.getElementById('document-name');
    if (documentInput && documentName) {
        documentInput.addEventListener('change', function () {
            documentName.textContent = this.files[0] ? this.files[0].name : i18n.noFileChosen;
        });
    }

    const thumbnailInput = document.getElementById('thumbnail');
    const thumbnailName = document.getElementById('thumbnail-name');
    const thumbnailPreview = document.getElementById('thumbnail-preview');
    if (thumbnailInput && thumbnailName) {
        thumbnailInput.addEventListener('change', function () {
            thumbnailName.textContent = this.files[0] ? this.files[0].name : i18n.noFileChosen;

            if (thumbnailPreview && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    thumbnailPreview.src = event.target.result;
                    thumbnailPreview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    document.addEventListener('click', (event) => {
        const dropdown = event.target.closest('.content-dropdown');

        document.querySelectorAll('.content-dropdown').forEach((item) => {
            if (item !== dropdown) {
                item.classList.remove('open');
            }
        });

        if (dropdown) {
            dropdown.classList.toggle('open');
        }
    });

    bindTitleValidation({
        titleCheckUrl,
        excludeId,
        validatingMessage,
        duplicateMessage,
        minLengthMessage,
    });
}

function bindTitleValidation(options = {}) {
    const {
        titleCheckUrl = null,
        excludeId = null,
        validatingMessage = '',
        duplicateMessage = '',
        minLengthMessage = '',
    } = options;

    const titleInput = document.getElementById('title');
    const titleError = document.getElementById('titleError');

    if (!titleInput || !titleError || !titleCheckUrl) {
        return;
    }

    let titleValidationTimeout;

    titleInput.addEventListener('input', function () {
        const title = this.value.trim();
        clearTimeout(titleValidationTimeout);

        if (title.length === 0) {
            titleError.style.display = 'none';
            titleInput.classList.remove('input-error');
            titleInput.style.borderColor = '';
            return;
        }

        if (title.length < 3) {
            titleError.textContent = minLengthMessage;
            titleError.style.color = '#f59e0b';
            titleError.style.display = 'block';
            titleInput.classList.add('input-error');
            titleInput.style.borderColor = 'rgba(245, 158, 11, 0.6)';
            return;
        }

        titleError.textContent = validatingMessage;
        titleError.style.color = '#b8b8c7';
        titleError.style.display = 'block';

        titleValidationTimeout = setTimeout(() => {
            const currentLessonId = document.querySelector('meta[name="lesson-id"]')?.content || excludeId;

            fetch(titleCheckUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    title,
                    exclude_id: currentLessonId,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.exists) {
                        titleError.textContent = duplicateMessage;
                        titleError.style.color = '#ef4444';
                        titleError.style.display = 'block';
                        titleInput.classList.add('input-error');
                        titleInput.style.borderColor = 'rgba(239, 68, 68, 0.6)';
                    } else {
                        titleError.style.display = 'none';
                        titleInput.classList.remove('input-error');
                        titleInput.style.borderColor = '';
                    }
                })
                .catch(() => {
                    titleError.style.display = 'none';
                });
        }, 500);
    });
}
