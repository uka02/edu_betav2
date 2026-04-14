const learnerPreviewStorageBase = '/storage/';
let learnerPreviewActiveSectionId = 'basic-info';
let learnerPreviewObjectUrls = [];

function resetLearnerPreviewObjectUrls() {
    learnerPreviewObjectUrls.forEach((url) => {
        try {
            URL.revokeObjectURL(url);
        } catch (error) {
            // Ignore preview URL cleanup errors.
        }
    });

    learnerPreviewObjectUrls = [];
}

function createLearnerPreviewObjectUrl(file) {
    if (!(file instanceof File)) {
        return '';
    }

    const url = URL.createObjectURL(file);
    learnerPreviewObjectUrls.push(url);

    return url;
}

function escapeLearnerPreviewHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function nl2brLearnerPreview(value) {
    return escapeLearnerPreviewHtml(value).replace(/\n/g, '<br>');
}

function buildLearnerPreviewStorageUrl(path) {
    const normalizedPath = String(path ?? '').trim().replace(/^\/+/, '');

    if (!normalizedPath) {
        return '';
    }

    if (/^https?:\/\//i.test(normalizedPath)) {
        return normalizedPath;
    }

    return `${learnerPreviewStorageBase}${normalizedPath}`;
}

function getLearnerPreviewAssetUrl(input, previewElement = null, existingUrl = '') {
    if (previewElement?.src) {
        return previewElement.src;
    }

    if (input?.files?.[0]) {
        return createLearnerPreviewObjectUrl(input.files[0]);
    }

    return existingUrl || '';
}

function getLearnerPreviewFileInfo(input, fileNameElement, existingUrl = '') {
    if (input?.files?.[0]) {
        return {
            name: input.files[0].name,
            url: createLearnerPreviewObjectUrl(input.files[0]),
        };
    }

    const fallbackName = fileNameElement?.textContent?.trim() ?? '';

    return {
        name: [i18n.noFileChosen, i18n.placeholderFileNotChosen].includes(fallbackName) ? '' : fallbackName,
        url: existingUrl || '',
    };
}

function getLearnerPreviewEmbedUrl(url) {
    const rawUrl = String(url ?? '').trim();

    if (!rawUrl) {
        return '';
    }

    try {
        const parsedUrl = new URL(rawUrl, window.location.origin);
        const host = parsedUrl.hostname.replace(/^www\./i, '').toLowerCase();

        if (host === 'youtu.be') {
            const videoId = parsedUrl.pathname.split('/').filter(Boolean)[0] ?? '';
            return videoId ? `https://www.youtube.com/embed/${videoId}` : '';
        }

        if (host === 'youtube.com' || host === 'm.youtube.com') {
            if (parsedUrl.pathname === '/watch') {
                const videoId = parsedUrl.searchParams.get('v');
                return videoId ? `https://www.youtube.com/embed/${videoId}` : '';
            }

            const parts = parsedUrl.pathname.split('/').filter(Boolean);
            const videoId = ['embed', 'shorts'].includes(parts[0] ?? '') ? parts[1] : '';
            return videoId ? `https://www.youtube.com/embed/${videoId}` : '';
        }

        if (host === 'vimeo.com' || host === 'player.vimeo.com') {
            const videoId = parsedUrl.pathname.split('/').filter(Boolean).find((part) => /^\d+$/.test(part)) ?? '';
            return videoId ? `https://player.vimeo.com/video/${videoId}` : '';
        }
    } catch (error) {
        return '';
    }

    return '';
}

function getLearnerPreviewDurationMinutes() {
    const hours = Number.parseInt(document.querySelector('input[name="duration_hours"]')?.value ?? '0', 10) || 0;
    const minutes = Number.parseInt(document.querySelector('input[name="duration_minutes"]')?.value ?? '0', 10) || 0;

    return (hours * 60) + minutes;
}

function formatLearnerPreviewDuration(minutes) {
    return minutes > 0 ? `${minutes} ${escapeLearnerPreviewHtml(i18n.min)}` : '-';
}

function getLearnerPreviewContentContainer(segmentId) {
    return document.getElementById(`contentBlocks_${segmentId}`)
        || (segmentId === 1 ? document.getElementById('contentBlocks') : null);
}

function collectLearnerPreviewBlock(block) {
    const type = block.getAttribute('data-block-type')
        || block.querySelector('input[type="hidden"][name$="[type]"]')?.value
        || '';

    if (!type) {
        return null;
    }

    if (type === 'divider') {
        return { type };
    }

    if (!blockHasMeaningfulContent(block)) {
        return null;
    }

    if (['text', 'subheading', 'video', 'callout', 'code'].includes(type)) {
        return {
            type,
            content: block.querySelector('[name$="[content]"]')?.value?.trim() ?? '',
            calloutType: block.querySelector('[name$="[callout_type]"]')?.value ?? 'info',
        };
    }

    if (type === 'image') {
        const imageInput = block.querySelector('input[type="file"][name$="[image]"]');
        const imagePreview = block.querySelector('.block-image-preview');
        const existingPath = block.querySelector('input[name$="[existing_path]"]')?.value ?? '';

        return {
            type,
            url: getLearnerPreviewAssetUrl(imageInput, imagePreview, buildLearnerPreviewStorageUrl(existingPath)),
            caption: block.querySelector('[name$="[content]"]')?.value?.trim() ?? '',
        };
    }

    if (type === 'file') {
        const fileInput = block.querySelector('input[type="file"][name$="[file]"]');
        const fileNameElement = block.querySelector('.file-name');
        const existingPath = block.querySelector('input[name$="[existing_path]"]')?.value ?? '';

        return {
            type,
            ...getLearnerPreviewFileInfo(fileInput, fileNameElement, buildLearnerPreviewStorageUrl(existingPath)),
        };
    }

    if (type === 'quiz') {
        return {
            type,
            question: block.querySelector('[name*="[question]"]')?.value?.trim() ?? '',
            answers: Array.from(block.querySelectorAll('.quiz-answer-input'))
                .map((input) => input.value.trim())
                .filter(Boolean),
        };
    }

    return null;
}

function collectLearnerPreviewQuestion(question) {
    if (!questionHasMeaningfulContent(question)) {
        return null;
    }

    const type = question.getAttribute('data-question-type')
        || question.querySelector('input[type="hidden"][name$="[type]"]')?.value
        || '';

    const prompt = question.querySelector('[name*="[question]"]')?.value?.trim() ?? '';

    if (type === 'multiple_choice') {
        return {
            type,
            question: prompt,
            answers: Array.from(question.querySelectorAll('.quiz-answer-input'))
                .map((input) => input.value.trim())
                .filter(Boolean),
        };
    }

    if (type === 'true_false') {
        return {
            type,
            question: prompt,
            answers: [i18n.trueAnswer, i18n.falseAnswer],
        };
    }

    return {
        type,
        question: prompt,
        answers: [],
    };
}

function collectLearnerPreviewSections() {
    const sections = [{
        id: 'basic-info',
        type: 'basic',
        title: i18n.basicInfo,
        icon: 'B',
        meta: formatLearnerPreviewDuration(getLearnerPreviewDurationMinutes()),
    }];

    let contentIndex = 1;
    let examIndex = 1;

    segments
        .filter((segment) => segment.id !== 0)
        .forEach((segment) => {
            const customName = String(segment.customName || '').trim();

            if (segment.type === 'exam') {
                const questions = Array.from(document.querySelectorAll(`#quizContainer_${segment.id} .quiz-question`))
                    .map((question) => collectLearnerPreviewQuestion(question))
                    .filter(Boolean);

                sections.push({
                    id: `exam-${segment.id}`,
                    type: 'exam',
                    title: customName || `${i18n.examIndexLabel} ${examIndex++}`,
                    icon: 'E',
                    meta: `${questions.length} ${i18n.questions}`,
                    questions,
                    timeLimit: Number.parseInt(document.querySelector(`input[name="segments[${segment.id}][exam_settings][time_limit]"]`)?.value ?? '0', 10) || 0,
                    passingScore: Number.parseInt(document.querySelector(`input[name="segments[${segment.id}][exam_settings][passing_score]"]`)?.value ?? '60', 10) || 60,
                });

                return;
            }

            const blocks = Array.from(getLearnerPreviewContentContainer(segment.id)?.querySelectorAll('.content-block') ?? [])
                .map((block) => collectLearnerPreviewBlock(block))
                .filter(Boolean);

            sections.push({
                id: `content-${segment.id}`,
                type: 'content',
                title: customName || `${i18n.contentSegment} ${contentIndex++}`,
                icon: 'C',
                meta: `${blocks.length} ${i18n.blocks}`,
                blocks,
            });
        });

    return sections;
}

function getLearnerPreviewSnapshot() {
    const type = document.getElementById('type')?.value ?? '';
    const documentInput = document.getElementById('document');
    const thumbnailInput = document.getElementById('thumbnail');
    const sections = collectLearnerPreviewSections();
    const examCount = sections.filter((section) => section.type === 'exam').length;
    const questionCount = sections
        .filter((section) => section.type === 'exam')
        .reduce((total, section) => total + section.questions.length, 0);
    const blockCount = sections
        .filter((section) => section.type === 'content')
        .reduce((total, section) => total + section.blocks.length, 0);
    const videoUrl = document.getElementById('video_url')?.value?.trim() ?? '';

    return {
        title: document.getElementById('title')?.value?.trim() || i18n.untitledDraft,
        type,
        typeLabel: getSelectedOptionText(document.getElementById('type')) || '-',
        subjectLabel: getSelectedOptionText(document.getElementById('subject')) || '-',
        difficultyLabel: getSelectedOptionText(document.getElementById('difficulty')) || '-',
        durationMinutes: getLearnerPreviewDurationMinutes(),
        isFree: Boolean(document.getElementById('is_free')?.checked),
        isPublished: Boolean(document.getElementById('is_published')?.checked),
        videoUrl,
        videoEmbedUrl: getLearnerPreviewEmbedUrl(videoUrl),
        thumbnailUrl: getLearnerPreviewAssetUrl(
            thumbnailInput,
            document.getElementById('thumbnail-preview'),
            thumbnailInput?.dataset?.existingThumbnailUrl ?? '',
        ),
        documentInfo: getLearnerPreviewFileInfo(
            documentInput,
            document.getElementById('document-name'),
            documentInput?.dataset?.existingDocumentUrl ?? '',
        ),
        sections,
        counts: {
            sections: sections.length,
            blocks: Math.max(1, blockCount + questionCount),
            exams: examCount,
        },
    };
}

function renderLearnerPreviewHero(snapshot) {
    return `
        <section class="learner-preview-hero">
            <div class="learner-preview-hero-copy">
                <div class="learner-preview-kicker">${escapeLearnerPreviewHtml(i18n.learnerView)}</div>
                <h2 class="learner-preview-hero-title">${escapeLearnerPreviewHtml(snapshot.title)}</h2>
                <p class="learner-preview-hero-subtitle">${escapeLearnerPreviewHtml(i18n.previewUsesDraftState)}</p>
                <div class="learner-preview-tag-row">
                    <span class="learner-preview-tag">${escapeLearnerPreviewHtml(snapshot.subjectLabel)}</span>
                    <span class="learner-preview-tag is-blue">${escapeLearnerPreviewHtml(snapshot.typeLabel)}</span>
                    <span class="learner-preview-tag ${snapshot.isPublished ? 'is-green' : ''}">${escapeLearnerPreviewHtml(snapshot.isPublished ? i18n.published : i18n.draft)}</span>
                    <span class="learner-preview-tag ${snapshot.isFree ? 'is-green' : 'is-amber'}">${escapeLearnerPreviewHtml(snapshot.isFree ? i18n.freeBadge : i18n.paid)}</span>
                    ${snapshot.difficultyLabel !== '-' ? `<span class="learner-preview-tag">${escapeLearnerPreviewHtml(snapshot.difficultyLabel)}</span>` : ''}
                </div>
            </div>
            <div class="learner-preview-progress">
                <div class="learner-preview-progress-head">
                    <span class="learner-preview-progress-label">${escapeLearnerPreviewHtml(i18n.lessonProgress)}</span>
                    <span class="learner-preview-progress-value">0%</span>
                </div>
                <div class="learner-preview-progress-track"><div class="learner-preview-progress-fill"></div></div>
                <div class="learner-preview-progress-note">${escapeLearnerPreviewHtml(i18n.previewCurrentDraft)}</div>
            </div>
        </section>
    `;
}

function renderLearnerPreviewStats(snapshot) {
    return `
        <div class="learner-preview-stat">
            <div class="learner-preview-stat-value">${snapshot.counts.sections}</div>
            <div class="learner-preview-stat-label">${escapeLearnerPreviewHtml(i18n.sections)}</div>
        </div>
        <div class="learner-preview-stat">
            <div class="learner-preview-stat-value">${snapshot.counts.blocks}</div>
            <div class="learner-preview-stat-label">${escapeLearnerPreviewHtml(i18n.blocks)}</div>
        </div>
        <div class="learner-preview-stat">
            <div class="learner-preview-stat-value">${snapshot.counts.exams}</div>
            <div class="learner-preview-stat-label">${escapeLearnerPreviewHtml(i18n.exams)}</div>
        </div>
        <div class="learner-preview-stat">
            <div class="learner-preview-stat-value">${snapshot.durationMinutes > 0 ? snapshot.durationMinutes : '-'}</div>
            <div class="learner-preview-stat-label">${escapeLearnerPreviewHtml(i18n.min)}</div>
        </div>
    `;
}

function renderLearnerPreviewOutline(snapshot) {
    return snapshot.sections.map((section) => `
        <button type="button" class="learner-preview-outline-item${section.id === learnerPreviewActiveSectionId ? ' is-active' : ''}" data-preview-target="${escapeLearnerPreviewHtml(section.id)}">
            <span class="learner-preview-outline-icon">${escapeLearnerPreviewHtml(section.icon)}</span>
            <span class="learner-preview-outline-copy">
                <span class="learner-preview-outline-name">${escapeLearnerPreviewHtml(section.title)}</span>
                <span class="learner-preview-outline-meta">${escapeLearnerPreviewHtml(section.meta)}</span>
            </span>
        </button>
    `).join('');
}

function renderLearnerPreviewBasicMedia(snapshot) {
    if (snapshot.type === 'video' && snapshot.videoUrl) {
        if (snapshot.videoEmbedUrl) {
            return `
                <div class="learner-preview-media">
                    <div class="learner-preview-video-frame"><iframe src="${escapeLearnerPreviewHtml(snapshot.videoEmbedUrl)}" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe></div>
                    <div class="learner-preview-media-actions"><a href="${escapeLearnerPreviewHtml(snapshot.videoUrl)}" target="_blank" rel="noopener noreferrer" class="learner-preview-link">${escapeLearnerPreviewHtml(i18n.openVideo)}</a></div>
                </div>
            `;
        }

        return `<div class="learner-preview-card"><a href="${escapeLearnerPreviewHtml(snapshot.videoUrl)}" target="_blank" rel="noopener noreferrer" class="learner-preview-link">${escapeLearnerPreviewHtml(i18n.openVideo)}</a></div>`;
    }

    if (snapshot.thumbnailUrl) {
        return `<div class="learner-preview-media"><img src="${escapeLearnerPreviewHtml(snapshot.thumbnailUrl)}" alt="${escapeLearnerPreviewHtml(snapshot.title)}" class="learner-preview-thumbnail"></div>`;
    }

    if (snapshot.type === 'document' && snapshot.documentInfo.name) {
        return `<div class="learner-preview-card"><a ${snapshot.documentInfo.url ? `href="${escapeLearnerPreviewHtml(snapshot.documentInfo.url)}" target="_blank" rel="noopener noreferrer"` : ''} class="learner-preview-link${snapshot.documentInfo.url ? '' : ' is-static'}">${escapeLearnerPreviewHtml(snapshot.documentInfo.name || i18n.downloadDocument)}</a></div>`;
    }

    return `<div class="learner-preview-empty">${escapeLearnerPreviewHtml(i18n.previewUsesDraftState)}</div>`;
}

function renderLearnerPreviewBasicSection(snapshot, section) {
    return `
        <section class="learner-preview-section${section.id === learnerPreviewActiveSectionId ? ' is-active' : ''}" data-preview-section="${escapeLearnerPreviewHtml(section.id)}">
            <div class="learner-preview-section-head">
                <h3 class="learner-preview-section-title">${escapeLearnerPreviewHtml(section.title)}</h3>
                <div class="learner-preview-section-meta">${escapeLearnerPreviewHtml(snapshot.typeLabel)}</div>
            </div>
            <div class="learner-preview-basic">
                <div>${renderLearnerPreviewBasicMedia(snapshot)}</div>
                <div class="learner-preview-detail-list">
                    <div class="learner-preview-card">
                        <div class="learner-preview-detail-list">
                            <div class="learner-preview-detail"><span class="learner-preview-detail-label">${escapeLearnerPreviewHtml(i18n.type)}</span><span class="learner-preview-detail-value">${escapeLearnerPreviewHtml(snapshot.typeLabel)}</span></div>
                            <div class="learner-preview-detail"><span class="learner-preview-detail-label">${escapeLearnerPreviewHtml(i18n.subject)}</span><span class="learner-preview-detail-value">${escapeLearnerPreviewHtml(snapshot.subjectLabel)}</span></div>
                            <div class="learner-preview-detail"><span class="learner-preview-detail-label">${escapeLearnerPreviewHtml(i18n.difficulty)}</span><span class="learner-preview-detail-value">${escapeLearnerPreviewHtml(snapshot.difficultyLabel)}</span></div>
                            <div class="learner-preview-detail"><span class="learner-preview-detail-label">${escapeLearnerPreviewHtml(i18n.duration)}</span><span class="learner-preview-detail-value">${escapeLearnerPreviewHtml(formatLearnerPreviewDuration(snapshot.durationMinutes))}</span></div>
                            <div class="learner-preview-detail"><span class="learner-preview-detail-label">${escapeLearnerPreviewHtml(i18n.isFreeLabel)}</span><span class="learner-preview-detail-value">${escapeLearnerPreviewHtml(snapshot.isFree ? i18n.freeBadge : i18n.paid)}</span></div>
                            <div class="learner-preview-detail"><span class="learner-preview-detail-label">${escapeLearnerPreviewHtml(i18n.isPublishedLabel)}</span><span class="learner-preview-detail-value">${escapeLearnerPreviewHtml(snapshot.isPublished ? i18n.published : i18n.draft)}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    `;
}

function renderLearnerPreviewContentBlock(block) {
    if (block.type === 'text') return `<div class="learner-preview-text">${nl2brLearnerPreview(block.content)}</div>`;
    if (block.type === 'subheading') return `<h4 class="learner-preview-subheading">${escapeLearnerPreviewHtml(block.content)}</h4>`;
    if (block.type === 'image') return `<div>${block.url ? `<img src="${escapeLearnerPreviewHtml(block.url)}" alt="${escapeLearnerPreviewHtml(block.caption || '')}" class="learner-preview-image">` : ''}${block.caption ? `<p class="learner-preview-caption">${escapeLearnerPreviewHtml(block.caption)}</p>` : ''}</div>`;
    if (block.type === 'video') {
        const embedUrl = getLearnerPreviewEmbedUrl(block.content);
        return embedUrl
            ? `<div class="learner-preview-media"><div class="learner-preview-video-frame"><iframe src="${escapeLearnerPreviewHtml(embedUrl)}" allow="autoplay; fullscreen" allowfullscreen></iframe></div><div class="learner-preview-media-actions"><a href="${escapeLearnerPreviewHtml(block.content)}" target="_blank" rel="noopener noreferrer" class="learner-preview-link">${escapeLearnerPreviewHtml(i18n.openVideo)}</a></div></div>`
            : `<div class="learner-preview-card"><a href="${escapeLearnerPreviewHtml(block.content)}" target="_blank" rel="noopener noreferrer" class="learner-preview-link">${escapeLearnerPreviewHtml(i18n.openVideo)}</a></div>`;
    }
    if (block.type === 'file') return `<div class="learner-preview-card"><a ${block.url ? `href="${escapeLearnerPreviewHtml(block.url)}" target="_blank" rel="noopener noreferrer"` : ''} class="learner-preview-link${block.url ? '' : ' is-static'}">${escapeLearnerPreviewHtml(block.name || i18n.downloadFile)}</a></div>`;
    if (block.type === 'callout') return `<div class="learner-preview-callout ${escapeLearnerPreviewHtml(block.calloutType || 'info')}">${nl2brLearnerPreview(block.content)}</div>`;
    if (block.type === 'code') return `<pre class="learner-preview-code">${escapeLearnerPreviewHtml(block.content)}</pre>`;
    if (block.type === 'divider') return `<div class="learner-preview-divider"></div>`;
    if (block.type === 'quiz') return `<div class="learner-preview-quiz"><div class="learner-preview-quiz-question">${escapeLearnerPreviewHtml(block.question)}</div><div class="learner-preview-quiz-options">${block.answers.map((answer, index) => `<div class="learner-preview-quiz-option"><span class="learner-preview-answer-letter">${String.fromCharCode(65 + index)}</span><span class="learner-preview-answer-text">${escapeLearnerPreviewHtml(answer)}</span></div>`).join('')}</div></div>`;
    return '';
}

function renderLearnerPreviewContentSection(section) {
    return `
        <section class="learner-preview-section${section.id === learnerPreviewActiveSectionId ? ' is-active' : ''}" data-preview-section="${escapeLearnerPreviewHtml(section.id)}">
            <div class="learner-preview-section-head">
                <h3 class="learner-preview-section-title">${escapeLearnerPreviewHtml(section.title)}</h3>
                <div class="learner-preview-section-meta">${escapeLearnerPreviewHtml(section.meta)}</div>
            </div>
            <div class="learner-preview-blocks">
                ${section.blocks.length ? section.blocks.map((block) => renderLearnerPreviewContentBlock(block)).join('') : `<div class="learner-preview-empty">${escapeLearnerPreviewHtml(i18n.noContent)}</div>`}
            </div>
        </section>
    `;
}

function renderLearnerPreviewExamSection(section) {
    return `
        <section class="learner-preview-section${section.id === learnerPreviewActiveSectionId ? ' is-active' : ''}" data-preview-section="${escapeLearnerPreviewHtml(section.id)}">
            <div class="learner-preview-section-head">
                <h3 class="learner-preview-section-title">${escapeLearnerPreviewHtml(section.title)}</h3>
                <div class="learner-preview-section-meta">${escapeLearnerPreviewHtml(i18n.examMode)}</div>
            </div>
            <div class="learner-preview-exam-box">
                <div class="learner-preview-exam-text">${escapeLearnerPreviewHtml(i18n.examContains).replace(':count', String(section.questions.length))}</div>
                <div class="learner-preview-exam-stats">
                    ${section.timeLimit > 0 ? `<div class="learner-preview-exam-stat"><div class="learner-preview-exam-stat-label">${escapeLearnerPreviewHtml(i18n.timeLabel)}</div><div class="learner-preview-exam-stat-value">${section.timeLimit}</div></div>` : ''}
                    <div class="learner-preview-exam-stat"><div class="learner-preview-exam-stat-label">${escapeLearnerPreviewHtml(i18n.passLabel)}</div><div class="learner-preview-exam-stat-value">${section.passingScore}%</div></div>
                    <div class="learner-preview-exam-stat"><div class="learner-preview-exam-stat-label">${escapeLearnerPreviewHtml(i18n.questionsLabel)}</div><div class="learner-preview-exam-stat-value">${section.questions.length}</div></div>
                </div>
                <div class="learner-preview-exam-btn">${escapeLearnerPreviewHtml(i18n.startExam)}</div>
            </div>
        </section>
    `;
}

function renderLearnerPreviewSections(snapshot) {
    return snapshot.sections.map((section) => {
        if (section.type === 'basic') {
            return renderLearnerPreviewBasicSection(snapshot, section);
        }

        if (section.type === 'exam') {
            return renderLearnerPreviewExamSection(section);
        }

        return renderLearnerPreviewContentSection(section);
    }).join('');
}

function setLearnerPreviewActiveSection(sectionId) {
    learnerPreviewActiveSectionId = sectionId;

    document.querySelectorAll('[data-preview-target]').forEach((item) => {
        item.classList.toggle('is-active', item.getAttribute('data-preview-target') === sectionId);
    });

    document.querySelectorAll('[data-preview-section]').forEach((section) => {
        section.classList.toggle('is-active', section.getAttribute('data-preview-section') === sectionId);
    });
}

function renderLearnerPreview() {
    const modal = document.getElementById('learnerPreviewModal');

    if (!modal?.classList.contains('is-open')) {
        return;
    }

    resetLearnerPreviewObjectUrls();

    const snapshot = getLearnerPreviewSnapshot();
    const sectionIds = snapshot.sections.map((section) => section.id);

    if (!sectionIds.includes(learnerPreviewActiveSectionId)) {
        learnerPreviewActiveSectionId = sectionIds[0] ?? 'basic-info';
    }

    document.getElementById('learnerPreviewHero').innerHTML = renderLearnerPreviewHero(snapshot);
    document.getElementById('learnerPreviewStats').innerHTML = renderLearnerPreviewStats(snapshot);
    document.getElementById('learnerPreviewOutline').innerHTML = renderLearnerPreviewOutline(snapshot);
    document.getElementById('learnerPreviewSections').innerHTML = renderLearnerPreviewSections(snapshot);

    setLearnerPreviewActiveSection(learnerPreviewActiveSectionId);
}

function openLearnerPreview() {
    const modal = document.getElementById('learnerPreviewModal');

    if (!modal) {
        return;
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    renderLearnerPreview();
}

function closeLearnerPreview() {
    const modal = document.getElementById('learnerPreviewModal');

    if (!modal) {
        return;
    }

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    resetLearnerPreviewObjectUrls();
}

window.refreshLearnerPreview = renderLearnerPreview;

document.addEventListener('DOMContentLoaded', function () {
    const openButton = document.getElementById('openLearnerPreviewBtn');
    const closeButton = document.getElementById('closeLearnerPreviewBtn');
    const modal = document.getElementById('learnerPreviewModal');

    openButton?.addEventListener('click', openLearnerPreview);
    closeButton?.addEventListener('click', closeLearnerPreview);

    modal?.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeLearnerPreview();
            return;
        }

        const previewTarget = event.target.closest('[data-preview-target]');
        if (previewTarget) {
            setLearnerPreviewActiveSection(previewTarget.getAttribute('data-preview-target'));
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal?.classList.contains('is-open')) {
            closeLearnerPreview();
        }
    });
});
