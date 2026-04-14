function bindLessonDraftSupport(options = {}) {
    const form = document.getElementById('lessonForm');
    if (!form) {
        return;
    }

    const initialStorageKey = options.storageKey || 'lesson-builder-draft';
    const saveDelay = options.saveDelay ?? 1200;
    const cleanupKeys = Array.isArray(options.clearStorageKeysOnLoad) ? options.clearStorageKeysOnLoad : [];
    const autosaveUrl = options.autosaveUrl || null;
    const lessonStorageKeyPrefix = options.lessonStorageKeyPrefix || null;
    const recoveryPanel = document.getElementById('draftRecoveryPanel');
    const recoveryMeta = document.getElementById('draftRecoveryMeta');
    const restoreButton = document.getElementById('restoreDraftBtn');
    const discardButton = document.getElementById('discardDraftBtn');
    const statusElement = document.getElementById('draftStatus');
    const statusLocale = document.documentElement.lang || 'en';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || form.querySelector('input[name="_token"]')?.value
        || '';
    const relativeTimeFormatter = typeof Intl !== 'undefined' && typeof Intl.RelativeTimeFormat === 'function'
        ? new Intl.RelativeTimeFormat(statusLocale, { numeric: 'auto' })
        : null;

    let activeStorageKey = initialStorageKey;
    let currentLessonId = normalizeLessonId(options.initialLessonId ?? document.querySelector('meta[name="lesson-id"]')?.content);
    let draftTimer = null;
    let statusTimer = null;
    let autosaveController = null;
    let isRestoringDraft = false;
    let isSubmittingForm = false;
    let pendingAutosaveIncludesFiles = false;
    let statusMode = 'ready';
    let statusSavedAt = null;

    const canAutosaveToServer = Boolean(autosaveUrl && (options.canAutosaveToServer ?? true));

    function normalizeLessonId(value) {
        const normalized = Number.parseInt(String(value ?? ''), 10);

        return Number.isFinite(normalized) && normalized > 0 ? normalized : null;
    }

    function formatTimestamp(value) {
        if (!value) {
            return '';
        }

        const parsed = new Date(value);
        if (Number.isNaN(parsed.getTime())) {
            return String(value);
        }

        return parsed.toLocaleString();
    }

    function clearStatusTimer() {
        if (statusTimer) {
            window.clearInterval(statusTimer);
            statusTimer = null;
        }
    }

    function formatRelativeTime(value) {
        if (!value) {
            return '';
        }

        const parsed = new Date(value);
        if (Number.isNaN(parsed.getTime())) {
            return '';
        }

        const diffSeconds = Math.round((parsed.getTime() - Date.now()) / 1000);
        const absSeconds = Math.abs(diffSeconds);

        if (absSeconds < 5) {
            return null;
        }

        let unit = 'second';
        let amount = diffSeconds;

        if (absSeconds >= 86400) {
            unit = 'day';
            amount = Math.round(diffSeconds / 86400);
        } else if (absSeconds >= 3600) {
            unit = 'hour';
            amount = Math.round(diffSeconds / 3600);
        } else if (absSeconds >= 60) {
            unit = 'minute';
            amount = Math.round(diffSeconds / 60);
        }

        if (!relativeTimeFormatter) {
            return formatTimestamp(value);
        }

        return relativeTimeFormatter.format(amount, unit);
    }

    function resolveStatusMessage() {
        if (statusMode === 'dirty') {
            return i18n.draftStatusUnsaved;
        }

        if (statusMode === 'saving') {
            return i18n.draftStatusSaving;
        }

        if (statusMode === 'discarded') {
            return i18n.draftStatusDiscarded;
        }

        if (statusMode === 'error') {
            return i18n.draftStatusError;
        }

        if (statusMode === 'saved') {
            const relative = formatRelativeTime(statusSavedAt);

            return relative
                ? i18n.draftStatusSaved.replace('__TIME__', relative)
                : i18n.draftStatusSavedJustNow;
        }

        if (statusMode === 'restored') {
            const relative = formatRelativeTime(statusSavedAt);

            return relative
                ? i18n.draftStatusRestoredSaved.replace('__TIME__', relative)
                : i18n.draftStatusRestored;
        }

        return i18n.draftStatusReady;
    }

    function renderStatus() {
        if (!statusElement) {
            return;
        }

        statusElement.dataset.state = statusMode;
        statusElement.textContent = resolveStatusMessage();
    }

    function setStatus(mode, savedAt = null) {
        statusMode = mode;
        statusSavedAt = savedAt;

        renderStatus();
        clearStatusTimer();

        if (statusSavedAt && ['saved', 'restored'].includes(statusMode)) {
            statusTimer = window.setInterval(renderStatus, 1000);
        }
    }

    function shouldTrackField(field) {
        return Boolean(
            field?.name
            && !['file', 'submit', 'button', 'reset'].includes(field.type)
            && !['_token', '_method', 'save_action', 'lesson_id'].includes(field.name)
        );
    }

    function trackedFields() {
        return Array.from(form.elements).filter(shouldTrackField);
    }

    function resolveStorageKeyForLesson(lessonId) {
        if (!lessonStorageKeyPrefix || !lessonId) {
            return activeStorageKey;
        }

        return `${lessonStorageKeyPrefix}${lessonId}`;
    }

    function readStoredDraftFromKey(key) {
        try {
            const raw = localStorage.getItem(key);
            if (!raw) {
                return null;
            }

            const parsed = JSON.parse(raw);
            if (!parsed || !Array.isArray(parsed.fields)) {
                return null;
            }

            return parsed;
        } catch {
            return null;
        }
    }

    function readStoredDraft() {
        return readStoredDraftFromKey(activeStorageKey);
    }

    function writeStoredDraftToKey(key, snapshot) {
        localStorage.setItem(key, JSON.stringify(snapshot));
    }

    function writeStoredDraft(snapshot) {
        writeStoredDraftToKey(activeStorageKey, snapshot);
    }

    function discardStoredDraft(showStatus = true) {
        localStorage.removeItem(activeStorageKey);

        if (recoveryPanel) {
            recoveryPanel.style.display = 'none';
        }

        if (showStatus) {
            setStatus('discarded');
        }
    }

    function clearStorageKeys(keys) {
        keys.forEach((key) => {
            if (typeof key === 'string' && key !== '') {
                localStorage.removeItem(key);
            }
        });
    }

    function ensureLessonMetaTag() {
        let lessonMeta = document.querySelector('meta[name="lesson-id"]');

        if (!lessonMeta) {
            lessonMeta = document.createElement('meta');
            lessonMeta.name = 'lesson-id';
            document.head.appendChild(lessonMeta);
        }

        return lessonMeta;
    }

    function ensureMethodOverride() {
        let methodInput = form.querySelector('input[name="_method"]');

        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }

        methodInput.value = 'PUT';
    }

    function syncLessonIdentity(lessonId, response = {}) {
        const normalizedLessonId = normalizeLessonId(lessonId);

        if (!normalizedLessonId) {
            return;
        }

        const previousStorageKey = activeStorageKey;
        const previousSnapshot = readStoredDraftFromKey(previousStorageKey);

        currentLessonId = normalizedLessonId;
        ensureLessonMetaTag().content = String(normalizedLessonId);

        if (response.update_url) {
            form.action = response.update_url;
        }

        ensureMethodOverride();

        if (response.edit_url) {
            window.history.replaceState({}, '', response.edit_url);
        }

        const nextStorageKey = resolveStorageKeyForLesson(normalizedLessonId);
        if (nextStorageKey !== previousStorageKey) {
            activeStorageKey = nextStorageKey;

            if (previousSnapshot) {
                writeStoredDraft(previousSnapshot);
            }

            localStorage.removeItem(previousStorageKey);
        }
    }

    function serializeDraftFields() {
        return trackedFields().map((field) => ({
            name: field.name,
            type: field.type,
            value: field.value,
            checked: field.checked,
        }));
    }

    function buildAutosavePayload(snapshot, includeFiles = false) {
        const payload = includeFiles ? new FormData(form) : new FormData();

        payload.delete('_method');
        payload.delete('save_action');
        payload.delete('lesson_id');

        if (!includeFiles) {
            snapshot.fields.forEach((field) => {
                if (!field.name) {
                    return;
                }

                if (field.type === 'checkbox' || field.type === 'radio') {
                    if (field.checked) {
                        payload.append(field.name, field.value ?? 'on');
                    }

                    return;
                }

                payload.append(field.name, field.value ?? '');
            });
        }

        payload.set('_token', csrfToken);
        payload.set('save_action', 'draft');

        if (currentLessonId) {
            payload.set('lesson_id', String(currentLessonId));
        }

        return payload;
    }

    async function persistDraftSnapshot(snapshot, options = {}) {
        const afterSuccessMode = options.afterSuccessMode || 'saved';
        const includeFiles = Boolean(options.includeFiles);

        if (!canAutosaveToServer || isSubmittingForm) {
            setStatus(afterSuccessMode, snapshot.saved_at);

            return snapshot;
        }

        if (autosaveController) {
            autosaveController.abort();
        }

        autosaveController = new AbortController();

        try {
            const response = await fetch(autosaveUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: buildAutosavePayload(snapshot, includeFiles),
                signal: autosaveController.signal,
            });

            if (!response.ok) {
                throw new Error(`Draft autosave failed with status ${response.status}`);
            }

            const data = await response.json();

            if (data.lesson_id) {
                syncLessonIdentity(data.lesson_id, data);
            }

            setStatus(afterSuccessMode, data.saved_at || snapshot.saved_at);
        } catch (error) {
            if (error?.name === 'AbortError') {
                if (includeFiles) {
                    pendingAutosaveIncludesFiles = true;
                }

                return snapshot;
            }

            if (includeFiles) {
                pendingAutosaveIncludesFiles = true;
            }

            setStatus('error', snapshot.saved_at);
        }

        return snapshot;
    }

    async function saveDraft(options = {}) {
        const afterSuccessMode = options.afterSuccessMode || 'saved';
        const includeFiles = Boolean(options.includeFiles);

        setStatus('saving');

        const snapshot = {
            version: 1,
            saved_at: new Date().toISOString(),
            fields: serializeDraftFields(),
        };

        writeStoredDraft(snapshot);

        return persistDraftSnapshot(snapshot, {
            afterSuccessMode,
            includeFiles,
        });
    }

    function scheduleLocalDraftSave(options = {}) {
        if (isRestoringDraft || isSubmittingForm) {
            return;
        }

        if (options.includeFiles) {
            pendingAutosaveIncludesFiles = true;
        }

        setStatus('dirty');
        clearTimeout(draftTimer);

        draftTimer = window.setTimeout(() => {
            const includeFiles = pendingAutosaveIncludesFiles;
            pendingAutosaveIncludesFiles = false;

            void saveDraft({ includeFiles });
        }, saveDelay);
    }

    function buildDraftStructure(fields) {
        const segmentMap = new Map();
        let maxSegmentId = 0;
        let maxBlockId = 0;

        fields.forEach((field, index) => {
            const segmentMatch = field.name.match(/^segments\[(\d+)\](.+)$/);
            if (!segmentMatch) {
                return;
            }

            const segmentId = parseInt(segmentMatch[1], 10);
            const path = segmentMatch[2];
            maxSegmentId = Math.max(maxSegmentId, segmentId);

            if (!segmentMap.has(segmentId)) {
                segmentMap.set(segmentId, {
                    id: segmentId,
                    firstIndex: index,
                    type: 'content',
                    blocks: [],
                    questions: [],
                    blockTypes: new Map(),
                    questionTypes: new Map(),
                });
            }

            const segment = segmentMap.get(segmentId);

            if (path.includes('[exam_settings]') || path.includes('[questions][')) {
                segment.type = 'exam';
            }

            const blockTypeMatch = path.match(/^\[blocks\]\[(\d+)\]\[type\]$/);
            const blockMatch = path.match(/^\[blocks\]\[(\d+)\]/);
            const questionTypeMatch = path.match(/^\[questions\]\[(\d+)\]\[type\]$/);
            const questionMatch = path.match(/^\[questions\]\[(\d+)\]/);

            if (blockMatch) {
                const blockId = parseInt(blockMatch[1], 10);
                maxBlockId = Math.max(maxBlockId, blockId);

                if (!segment.blocks.includes(blockId)) {
                    segment.blocks.push(blockId);
                }

                if (blockTypeMatch) {
                    segment.blockTypes.set(blockId, field.value || 'text');
                }
            }

            if (questionMatch) {
                const questionId = parseInt(questionMatch[1], 10);
                maxBlockId = Math.max(maxBlockId, questionId);
                segment.type = 'exam';

                if (!segment.questions.includes(questionId)) {
                    segment.questions.push(questionId);
                }

                if (questionTypeMatch) {
                    segment.questionTypes.set(questionId, field.value || 'multiple_choice');
                }
            }
        });

        const segmentsOrdered = Array.from(segmentMap.values())
            .sort((left, right) => left.firstIndex - right.firstIndex)
            .filter((segment) => segment.id !== 0)
            .map((segment) => ({
                id: segment.id,
                type: segment.type,
                blocks: segment.blocks.map((blockId) => ({
                    id: blockId,
                    type: segment.blockTypes.get(blockId) || 'text',
                })),
                questions: segment.questions.map((questionId) => ({
                    id: questionId,
                    type: segment.questionTypes.get(questionId) || 'multiple_choice',
                })),
            }));

        return {
            segments: segmentsOrdered,
            maxSegmentId,
            maxBlockId,
        };
    }

    function addBlockByType(blockType, segmentId) {
        if (blockType === 'text') {
            addTextBlock(segmentId);
        } else if (blockType === 'subheading') {
            addSubheadingBlock(segmentId);
        } else if (blockType === 'image') {
            addImageBlock(segmentId);
        } else if (blockType === 'video') {
            addVideoBlock(segmentId);
        } else if (blockType === 'file') {
            addFileBlock(segmentId);
        } else if (blockType === 'callout') {
            addCalloutBlock(segmentId);
        } else if (blockType === 'code') {
            addCodeBlock(segmentId);
        } else if (blockType === 'divider') {
            addDividerBlock(segmentId);
        } else if (blockType === 'quiz') {
            addQuizBlock(segmentId);
        }
    }

    function resetBuilderState() {
        document.querySelectorAll('.segment-container').forEach((segment) => {
            if (segment.getAttribute('data-segment') !== '0') {
                segment.remove();
            }
        });

        document.getElementById('segmentsContainer')?.replaceChildren();

        segments = segments.filter((segment) => segment.id === 0);
        segmentCounter = 1;
        blockCounter = 0;
        contentSegmentIndex = 0;

        if (typeof maxSegmentId !== 'undefined') {
            maxSegmentId = 1;
        }

        initializeSegments();
        switchSegment(0);
    }

    function rebuildDraftStructure(structure) {
        resetBuilderState();

        structure.segments.forEach((segment) => {
            segmentCounter = segment.id;

            if (segment.type === 'exam') {
                addExamSegment();
            } else {
                addContentSegment();
            }

            segment.blocks.forEach((block) => {
                blockCounter = block.id;
                addBlockByType(block.type, segment.id);
            });

            segment.questions.forEach((question) => {
                blockCounter = question.id;
                addExamQuestion(segment.id, question.type);
            });
        });

        segmentCounter = Math.max(structure.maxSegmentId + 1, segmentCounter);
        blockCounter = Math.max(structure.maxBlockId + 1, blockCounter);

        if (typeof maxSegmentId !== 'undefined') {
            maxSegmentId = structure.maxSegmentId + 1;
        }

        initializeSegments();
    }

    function applyDraftFields(fields) {
        const elements = Array.from(form.elements).filter((field) => field.name);

        fields.forEach((field) => {
            const matches = elements.filter((element) => element.name === field.name);

            if (matches.length === 0) {
                return;
            }

            if (field.type === 'checkbox') {
                matches.forEach((match) => {
                    match.checked = Boolean(field.checked);
                });

                return;
            }

            if (field.type === 'radio') {
                matches.forEach((match) => {
                    match.checked = Boolean(field.checked) && match.value === field.value;
                });

                return;
            }

            matches[0].value = field.value ?? '';
        });

        document.getElementById('type')?.dispatchEvent(new Event('change', { bubbles: true }));
        document.getElementById('title')?.dispatchEvent(new Event('input', { bubbles: true }));

        document.querySelectorAll('.segment-name-input').forEach((input) => {
            input.dispatchEvent(new Event('input', { bubbles: true }));
        });

        updateStats();
        initializeSegments();
        switchSegment(0);
    }

    function restoreStoredDraft(snapshot) {
        isRestoringDraft = true;

        const structure = buildDraftStructure(snapshot.fields);
        rebuildDraftStructure(structure);
        applyDraftFields(snapshot.fields);

        isRestoringDraft = false;

        if (recoveryPanel) {
            recoveryPanel.style.display = 'none';
        }

        void saveDraft({ afterSuccessMode: 'restored' });
    }

    function showDraftRecovery(snapshot) {
        if (!recoveryPanel || !recoveryMeta) {
            return;
        }

        recoveryPanel.style.display = 'flex';
        recoveryMeta.textContent = `${i18n.draftRecoveryMessage} ${formatTimestamp(snapshot.saved_at)}. ${i18n.localDraftFilesNotice}`;
    }

    restoreButton?.addEventListener('click', () => {
        const snapshot = readStoredDraft();
        if (!snapshot) {
            return;
        }

        restoreStoredDraft(snapshot);
    });

    discardButton?.addEventListener('click', () => {
        discardStoredDraft();
    });

    form.addEventListener('input', () => {
        scheduleLocalDraftSave();
    });

    form.addEventListener('change', (event) => {
        scheduleLocalDraftSave({
            includeFiles: event.target instanceof HTMLInputElement && event.target.type === 'file',
        });
    });

    form.addEventListener('click', (event) => {
        const button = event.target.closest('button[type="button"]');
        if (!button) {
            return;
        }

        window.setTimeout(() => {
            scheduleLocalDraftSave();
        }, 50);
    });

    form.addEventListener('submit', () => {
        isSubmittingForm = true;
        clearTimeout(draftTimer);

        if (autosaveController) {
            autosaveController.abort();
        }
    });

    clearStorageKeys(cleanupKeys);

    if (currentLessonId) {
        syncLessonIdentity(currentLessonId);
    }

    const storedDraft = readStoredDraft();
    if (storedDraft?.fields?.length) {
        showDraftRecovery(storedDraft);
        setStatus('ready');
    } else {
        setStatus('ready');
    }
}
