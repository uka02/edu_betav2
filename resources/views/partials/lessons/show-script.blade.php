<script>
    const isAuthenticatedViewer = @json(Auth::check());
    const translations = {
        multiple_choice: @js(__('lessons.multiple_choice')),
        true_false: @js(__('lessons.true_false')),
        short_answer: @js(__('lessons.short_answer')),
        true_answer: @js(__('lessons.true_answer')),
        false_answer: @js(__('lessons.false_answer')),
        your_answer: @js(__('lessons.your_answer')),
        correct_answer: @js(__('lessons.correct_answer')),
        not_answered: @js(__('lessons.not_answered')),
        please_select_answer: @js(__('lessons.please_select_answer')),
        correct_label: @js(__('lessons.correct_badge')),
        progress_saved: @js(__('lessons.progress_saved')),
        progress_complete: @js(__('lessons.progress_complete')),
        progress_autosave_notice: @js(__('lessons.progress_autosave_notice')),
    };

    const initialLessonProgressItems = @json($lessonProgressItems ?? []);
    const lessonProgressState = {
        endpoint: @js(Auth::check() ? route('lessons.progress', $lesson) : null),
        itemsState: new Map(),
        pendingItems: new Map(),
        trackableItems: new Map(),
        saveInFlight: false,
        flushTimerId: null,
        statusTimerId: null,
        observer: null,
        youtubeApiPromise: null,
        vimeoApiPromise: null,
    };

    function updateLessonProgressUi(progressPercent, message = null) {
        const clampedPercent = Math.min(100, Math.max(0, progressPercent));
        const progressValue = document.getElementById('lessonProgressPercent');
        const progressFill = document.getElementById('lessonProgressFill');
        const progressStatus = document.getElementById('lessonProgressStatus');

        if (progressValue) {
            progressValue.textContent = `${clampedPercent}%`;
        }

        if (progressFill) {
            progressFill.style.width = `${clampedPercent}%`;
        }

        if (progressStatus && message) {
            progressStatus.textContent = message;
        }
    }

    function lessonProgressMessage(progressPercent) {
        if (!isAuthenticatedViewer) {
            return @js(__('lessons.track_progress'));
        }

        return progressPercent >= 100
            ? translations.progress_complete
            : translations.progress_autosave_notice;
    }

    function clampLessonProgressPercent(value) {
        return Math.min(100, Math.max(0, Number.isFinite(Number(value)) ? Math.round(Number(value)) : 0));
    }

    function normalizeProgressItem(key, kind, item = {}) {
        if (kind === 'video') {
            return {
                key,
                kind: 'video',
                progress_percent: clampLessonProgressPercent(item.progress_percent ?? 0),
                position_seconds: Math.max(0, Math.round(Number(item.position_seconds ?? 0) || 0)),
                duration_seconds: Math.max(0, Math.round(Number(item.duration_seconds ?? 0) || 0)),
                completed: Boolean(item.completed),
            };
        }

        return {
            key,
            kind: 'block',
            progress_percent: clampLessonProgressPercent(item.progress_percent ?? (item.completed ? 100 : 0)),
            completed: Boolean(item.completed) || clampLessonProgressPercent(item.progress_percent ?? 0) >= 100,
        };
    }

    function registerTrackableItem(key, kind) {
        if (!key) {
            return;
        }

        lessonProgressState.trackableItems.set(key, { key, kind });

        const existingItem = lessonProgressState.itemsState.get(key) ?? {};
        lessonProgressState.itemsState.set(key, normalizeProgressItem(key, kind, existingItem));
    }

    function resolveVideoProgressPercent(incomingItem, existingItem = {}) {
        if (incomingItem.progress_percent !== undefined && incomingItem.progress_percent !== null) {
            return clampLessonProgressPercent(incomingItem.progress_percent);
        }

        const positionSeconds = Math.max(0, Math.round(Number(incomingItem.position_seconds ?? 0) || 0));
        const durationSeconds = Math.max(
            0,
            Math.round(Number(incomingItem.duration_seconds ?? existingItem.duration_seconds ?? 0) || 0)
        );

        if (durationSeconds <= 0) {
            return 0;
        }

        return clampLessonProgressPercent((positionSeconds / durationSeconds) * 100);
    }

    function mergeVideoProgressItem(existingItem, incomingItem) {
        const normalizedExisting = normalizeProgressItem(existingItem.key ?? incomingItem.key, 'video', existingItem);
        const progressPercent = Math.max(
            normalizedExisting.progress_percent,
            resolveVideoProgressPercent(incomingItem, normalizedExisting)
        );
        const durationSeconds = Math.max(
            normalizedExisting.duration_seconds,
            Math.max(0, Math.round(Number(incomingItem.duration_seconds ?? 0) || 0))
        );
        const positionSeconds = Math.max(
            normalizedExisting.position_seconds,
            Math.max(0, Math.round(Number(incomingItem.position_seconds ?? 0) || 0))
        );
        const completed = Boolean(incomingItem.completed)
            || normalizedExisting.completed
            || progressPercent >= 90;

        return normalizeProgressItem(normalizedExisting.key, 'video', {
            progress_percent: completed ? 100 : progressPercent,
            position_seconds: positionSeconds,
            duration_seconds: durationSeconds,
            completed,
        });
    }

    function mergeBlockProgressItem(existingItem, incomingItem) {
        const normalizedExisting = normalizeProgressItem(existingItem.key ?? incomingItem.key, 'block', existingItem);
        const completed = Boolean(incomingItem.completed)
            || normalizedExisting.completed
            || clampLessonProgressPercent(incomingItem.progress_percent ?? 0) >= 100;

        return normalizeProgressItem(normalizedExisting.key, 'block', {
            progress_percent: completed
                ? 100
                : Math.max(normalizedExisting.progress_percent, clampLessonProgressPercent(incomingItem.progress_percent ?? 0)),
            completed,
        });
    }

    function calculateLessonProgressSummary() {
        const trackableItems = Array.from(lessonProgressState.trackableItems.values());

        if (trackableItems.length === 0) {
            return {
                progressPercent: 0,
                watchedSeconds: 0,
                lastPositionSeconds: 0,
            };
        }

        let completedUnits = 0;
        let watchedSeconds = 0;
        let lastPositionSeconds = 0;

        trackableItems.forEach(({ key, kind }) => {
            const item = lessonProgressState.itemsState.get(key);

            if (!item) {
                return;
            }

            if (kind === 'video') {
                const progressPercent = clampLessonProgressPercent(item.progress_percent ?? 0);
                const durationSeconds = Math.max(0, Math.round(Number(item.duration_seconds ?? 0) || 0));
                completedUnits += progressPercent / 100;
                watchedSeconds += Math.round((progressPercent / 100) * durationSeconds);
                lastPositionSeconds = Math.max(lastPositionSeconds, Math.max(0, Math.round(Number(item.position_seconds ?? 0) || 0)));
            } else {
                completedUnits += item.completed || clampLessonProgressPercent(item.progress_percent ?? 0) >= 100 ? 1 : 0;
            }
        });

        return {
            progressPercent: clampLessonProgressPercent((completedUnits / trackableItems.length) * 100),
            watchedSeconds,
            lastPositionSeconds,
        };
    }

    function applySavedProgressItems(items) {
        if (!items || typeof items !== 'object') {
            return;
        }

        Object.entries(items).forEach(([key, item]) => {
            const existingTrackable = lessonProgressState.trackableItems.get(key);
            const kind = existingTrackable?.kind ?? (item?.kind === 'video' ? 'video' : 'block');

            if (lessonProgressState.pendingItems.has(key)) {
                return;
            }

            lessonProgressState.itemsState.set(key, normalizeProgressItem(key, kind, item));
        });
    }

    function queueProgressItem(item) {
        if (!isAuthenticatedViewer) {
            return;
        }

        const key = String(item?.key ?? '');

        if (key === '') {
            return;
        }

        const trackable = lessonProgressState.trackableItems.get(key);
        const kind = trackable?.kind ?? item.kind ?? null;

        if (!kind) {
            return;
        }

        const existingItem = lessonProgressState.itemsState.get(key) ?? normalizeProgressItem(key, kind, {});
        const mergedItem = kind === 'video'
            ? mergeVideoProgressItem(existingItem, { ...item, key })
            : mergeBlockProgressItem(existingItem, { ...item, key });

        lessonProgressState.itemsState.set(key, mergedItem);
        lessonProgressState.pendingItems.set(key, mergedItem);

        const summary = calculateLessonProgressSummary();
        updateLessonProgressUi(summary.progressPercent, lessonProgressMessage(summary.progressPercent));
        scheduleLessonProgressFlush(summary.progressPercent >= 100 ? 600 : 1500);
    }

    function clearLessonProgressStatusTimer() {
        if (lessonProgressState.statusTimerId) {
            clearTimeout(lessonProgressState.statusTimerId);
            lessonProgressState.statusTimerId = null;
        }
    }

    function scheduleLessonProgressFlush(delay = 1500) {
        if (lessonProgressState.flushTimerId) {
            clearTimeout(lessonProgressState.flushTimerId);
        }

        lessonProgressState.flushTimerId = window.setTimeout(() => {
            lessonProgressState.flushTimerId = null;
            void flushLessonProgress();
        }, delay);
    }

    async function flushLessonProgress({ keepalive = false } = {}) {
        if (!isAuthenticatedViewer || !lessonProgressState.endpoint) {
            return;
        }

        if (lessonProgressState.saveInFlight || lessonProgressState.pendingItems.size === 0) {
            return;
        }

        const items = Array.from(lessonProgressState.pendingItems.values())
            .slice(0, 25)
            .map((item) => ({ ...item }));

        if (items.length === 0) {
            return;
        }

        items.forEach((item) => {
            lessonProgressState.pendingItems.delete(item.key);
        });

        lessonProgressState.saveInFlight = true;

        try {
            const response = await fetch(lessonProgressState.endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                credentials: 'same-origin',
                keepalive,
                body: JSON.stringify({
                    items: items.map((item) => ({
                        key: item.key,
                        kind: item.kind,
                        progress_percent: item.progress_percent,
                        position_seconds: item.position_seconds,
                        duration_seconds: item.duration_seconds,
                        completed: item.completed,
                    })),
                }),
            });

            const responseData = await response.json().catch(() => null);

            if (!response.ok || !responseData?.success) {
                throw new Error('Unable to save lesson progress.');
            }

            applySavedProgressItems(responseData.progress_state?.items ?? {});

            const summary = calculateLessonProgressSummary();
            clearLessonProgressStatusTimer();
            updateLessonProgressUi(
                summary.progressPercent,
                summary.progressPercent >= 100 ? translations.progress_complete : translations.progress_saved
            );

            lessonProgressState.statusTimerId = window.setTimeout(() => {
                const nextSummary = calculateLessonProgressSummary();
                updateLessonProgressUi(nextSummary.progressPercent, lessonProgressMessage(nextSummary.progressPercent));
            }, 2500);
        } catch (error) {
            items.forEach((item) => {
                lessonProgressState.pendingItems.set(item.key, item);
            });
            console.error(error);
        } finally {
            lessonProgressState.saveInFlight = false;

            if (lessonProgressState.pendingItems.size > 0) {
                scheduleLessonProgressFlush(1200);
            }
        }
    }

    function bindBlockCompletionTracking() {
        if (lessonProgressState.observer) {
            lessonProgressState.observer.disconnect();
            lessonProgressState.observer = null;
        }

        const blocks = document.querySelectorAll('.js-track-block[data-progress-key]');

        if (blocks.length === 0 || !('IntersectionObserver' in window)) {
            blocks.forEach((block) => {
                queueProgressItem({
                    key: block.dataset.progressKey,
                    kind: 'block',
                    completed: true,
                });
            });
            return;
        }

        lessonProgressState.observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting || entry.intersectionRatio < 0.6) {
                    return;
                }

                const key = entry.target.dataset.progressKey;
                if (!key) {
                    return;
                }

                queueProgressItem({
                    key,
                    kind: 'block',
                    completed: true,
                });

                lessonProgressState.observer?.unobserve(entry.target);
            });
        }, {
            threshold: [0.6],
        });

        blocks.forEach((block) => {
            const savedItem = lessonProgressState.itemsState.get(block.dataset.progressKey || '');

            if (savedItem?.completed) {
                return;
            }

            lessonProgressState.observer?.observe(block);
        });
    }

    function bindFileProgressTracking() {
        document.querySelectorAll('.js-track-file-link[data-progress-key]').forEach((link) => {
            if (link.dataset.progressBound === 'true') {
                return;
            }

            link.dataset.progressBound = 'true';
            link.addEventListener('click', () => {
                queueProgressItem({
                    key: link.dataset.progressKey,
                    kind: link.dataset.progressKind || 'block',
                    completed: true,
                });

                void flushLessonProgress({ keepalive: true });
            });
        });
    }

    function bindFallbackVideoProgressTracking() {
        document.querySelectorAll('.js-track-video-link[data-progress-key]').forEach((link) => {
            if (link.dataset.progressBound === 'true') {
                return;
            }

            link.dataset.progressBound = 'true';
            link.addEventListener('click', () => {
                queueProgressItem({
                    key: link.dataset.progressKey,
                    kind: 'video',
                    completed: true,
                    progress_percent: 100,
                });

                void flushLessonProgress({ keepalive: true });
            });
        });
    }

    function loadYouTubeApi() {
        if (window.YT?.Player) {
            return Promise.resolve(window.YT);
        }

        if (lessonProgressState.youtubeApiPromise) {
            return lessonProgressState.youtubeApiPromise;
        }

        lessonProgressState.youtubeApiPromise = new Promise((resolve, reject) => {
            const existingScript = document.querySelector('script[data-progress-youtube-api="true"]');

            const handleReady = () => resolve(window.YT);
            const previousReady = window.onYouTubeIframeAPIReady;
            window.onYouTubeIframeAPIReady = () => {
                if (typeof previousReady === 'function') {
                    previousReady();
                }

                handleReady();
            };

            if (!existingScript) {
                const script = document.createElement('script');
                script.src = 'https://www.youtube.com/iframe_api';
                script.async = true;
                script.dataset.progressYoutubeApi = 'true';
                script.onerror = () => reject(new Error('Unable to load YouTube API.'));
                document.head.appendChild(script);
            }
        });

        return lessonProgressState.youtubeApiPromise;
    }

    function loadVimeoApi() {
        if (window.Vimeo?.Player) {
            return Promise.resolve(window.Vimeo);
        }

        if (lessonProgressState.vimeoApiPromise) {
            return lessonProgressState.vimeoApiPromise;
        }

        lessonProgressState.vimeoApiPromise = new Promise((resolve, reject) => {
            const existingScript = document.querySelector('script[data-progress-vimeo-api="true"]');

            if (existingScript) {
                existingScript.addEventListener('load', () => resolve(window.Vimeo), { once: true });
                existingScript.addEventListener('error', () => reject(new Error('Unable to load Vimeo API.')), { once: true });
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://player.vimeo.com/api/player.js';
            script.async = true;
            script.dataset.progressVimeoApi = 'true';
            script.onload = () => resolve(window.Vimeo);
            script.onerror = () => reject(new Error('Unable to load Vimeo API.'));
            document.head.appendChild(script);
        });

        return lessonProgressState.vimeoApiPromise;
    }

    function bindYouTubeVideoTracking(iframe) {
        if (iframe.dataset.progressBound === 'true') {
            return;
        }

        iframe.dataset.progressBound = 'true';

        const key = iframe.dataset.progressKey;
        let syncTimerId = null;

        const stopSync = () => {
            if (syncTimerId) {
                clearInterval(syncTimerId);
                syncTimerId = null;
            }
        };

        const player = new window.YT.Player(iframe.id, {
            events: {
                onStateChange: (event) => {
                    const durationSeconds = Math.round(event.target.getDuration?.() || 0);
                    const positionSeconds = Math.round(event.target.getCurrentTime?.() || 0);
                    const progressPercent = durationSeconds > 0
                        ? clampLessonProgressPercent((positionSeconds / durationSeconds) * 100)
                        : 0;

                    const syncProgress = (forceComplete = false) => {
                        const currentDuration = Math.round(event.target.getDuration?.() || durationSeconds || 0);
                        const currentPosition = Math.round(event.target.getCurrentTime?.() || positionSeconds || 0);
                        const currentPercent = currentDuration > 0
                            ? clampLessonProgressPercent((currentPosition / currentDuration) * 100)
                            : progressPercent;

                        queueProgressItem({
                            key,
                            kind: 'video',
                            progress_percent: forceComplete ? 100 : currentPercent,
                            position_seconds: forceComplete ? currentDuration : currentPosition,
                            duration_seconds: currentDuration,
                            completed: forceComplete || currentPercent >= 90,
                        });
                    };

                    switch (event.data) {
                        case window.YT.PlayerState.PLAYING:
                            syncProgress();
                            stopSync();
                            syncTimerId = window.setInterval(() => syncProgress(), 5000);
                            break;
                        case window.YT.PlayerState.PAUSED:
                        case window.YT.PlayerState.BUFFERING:
                            stopSync();
                            syncProgress();
                            break;
                        case window.YT.PlayerState.ENDED:
                            stopSync();
                            syncProgress(true);
                            break;
                        default:
                            break;
                    }
                },
            },
        });

        iframe.addEventListener('remove', stopSync);
        lessonProgressState.itemsState.set(
            key,
            normalizeProgressItem(key, 'video', lessonProgressState.itemsState.get(key) ?? {})
        );
    }

    function bindVimeoVideoTracking(iframe) {
        if (iframe.dataset.progressBound === 'true') {
            return;
        }

        iframe.dataset.progressBound = 'true';

        const key = iframe.dataset.progressKey;
        const player = new window.Vimeo.Player(iframe);
        let lastQueuedAt = 0;

        player.on('timeupdate', (data) => {
            const now = Date.now();
            const percent = clampLessonProgressPercent((Number(data.percent ?? 0) || 0) * 100);

            if (percent < 90 && now - lastQueuedAt < 4000) {
                return;
            }

            lastQueuedAt = now;
            queueProgressItem({
                key,
                kind: 'video',
                progress_percent: percent,
                position_seconds: Math.round(Number(data.seconds ?? 0) || 0),
                duration_seconds: Math.round(Number(data.duration ?? 0) || 0),
                completed: percent >= 90,
            });
        });

        player.on('ended', () => {
            player.getDuration().then((duration) => {
                queueProgressItem({
                    key,
                    kind: 'video',
                    progress_percent: 100,
                    position_seconds: Math.round(Number(duration ?? 0) || 0),
                    duration_seconds: Math.round(Number(duration ?? 0) || 0),
                    completed: true,
                });
            }).catch(() => {
                queueProgressItem({
                    key,
                    kind: 'video',
                    progress_percent: 100,
                    completed: true,
                });
            });
        });
    }

    function bindVideoProgressTracking() {
        const youtubeFrames = Array.from(document.querySelectorAll('.js-track-video[data-video-provider="youtube"]'));
        const vimeoFrames = Array.from(document.querySelectorAll('.js-track-video[data-video-provider="vimeo"]'));

        youtubeFrames.forEach((iframe, index) => {
            if (!iframe.id) {
                iframe.id = `lesson-youtube-video-${index + 1}`;
            }
        });

        if (youtubeFrames.length > 0) {
            loadYouTubeApi()
                .then(() => {
                    youtubeFrames.forEach((iframe) => bindYouTubeVideoTracking(iframe));
                })
                .catch((error) => console.error(error));
        }

        if (vimeoFrames.length > 0) {
            loadVimeoApi()
                .then(() => {
                    vimeoFrames.forEach((iframe) => bindVimeoVideoTracking(iframe));
                })
                .catch((error) => console.error(error));
        }
    }

    function markQuizProgressComplete(quizId) {
        if (!isAuthenticatedViewer) {
            return;
        }

        const block = document.querySelector(`[data-quiz-id="${quizId}"]`);
        const key = block?.dataset.progressKey;

        if (!key) {
            return;
        }

        queueProgressItem({
            key,
            kind: 'block',
            completed: true,
        });
    }

    function switchLessonSegment(segmentId, { updateHash = true } = {}) {
        const normalizedSegmentId = String(segmentId ?? '');
        const targetSegment = document.querySelector(`.segment-container[data-segment="${normalizedSegmentId}"]`);

        if (!targetSegment) {
            return;
        }

        document.querySelectorAll('.segment-container[data-segment]').forEach((segment) => {
            segment.classList.toggle('active', segment.dataset.segment === normalizedSegmentId);
        });

        document.querySelectorAll('[data-segment-target]').forEach((control) => {
            control.classList.toggle('active', control.dataset.segmentTarget === normalizedSegmentId);
        });

        if (updateHash) {
            const nextHash = `#lesson-segment-${normalizedSegmentId}`;

            if (window.history?.replaceState) {
                window.history.replaceState(null, '', nextHash);
            } else {
                window.location.hash = nextHash;
            }
        }

        if (isAuthenticatedViewer) {
            bindBlockCompletionTracking();
        }
    }

    function initializeLessonSegmentNavigation() {
        const segmentContainers = Array.from(document.querySelectorAll('.segment-container[data-segment]'));

        if (segmentContainers.length === 0) {
            return;
        }

        document.querySelectorAll('[data-segment-target]').forEach((control) => {
            if (control.dataset.segmentBound === 'true') {
                return;
            }

            control.dataset.segmentBound = 'true';
            control.addEventListener('click', (event) => {
                event.preventDefault();
                switchLessonSegment(control.dataset.segmentTarget);
            });
        });

        const hashMatch = window.location.hash.match(/^#lesson-segment-([A-Za-z0-9_-]+)$/);
        const initialSegmentId = hashMatch?.[1]
            ?? segmentContainers.find((segment) => segment.classList.contains('active'))?.dataset.segment
            ?? segmentContainers[0]?.dataset.segment;

        if (initialSegmentId) {
            switchLessonSegment(initialSegmentId, { updateHash: false });
        }
    }

    function initializeLessonProgressTracking() {
        initializeLessonSegmentNavigation();

        if (!isAuthenticatedViewer) {
            return;
        }

        applySavedProgressItems(initialLessonProgressItems);

        document.querySelectorAll('[data-progress-key][data-progress-kind]').forEach((element) => {
            registerTrackableItem(
                element.dataset.progressKey,
                element.dataset.progressKind === 'video' ? 'video' : 'block'
            );
        });

        const summary = calculateLessonProgressSummary();
        updateLessonProgressUi(summary.progressPercent, lessonProgressMessage(summary.progressPercent));

        bindBlockCompletionTracking();
        bindFileProgressTracking();
        bindFallbackVideoProgressTracking();
        bindVideoProgressTracking();

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                void flushLessonProgress({ keepalive: true });
            }
        });

        window.addEventListener('pagehide', () => {
            void flushLessonProgress({ keepalive: true });
        });
    }

    initializeLessonProgressTracking();

    document.querySelectorAll('.quiz-opt').forEach((opt) => {
        opt.addEventListener('click', function () {
            const radio = this.querySelector('.quiz-radio');
            if (radio && !this.closest('.quiz-block').classList.contains('quiz-answered')) {
                this.parentElement.querySelectorAll('.quiz-opt').forEach((answerOption) => {
                    answerOption.classList.remove('selected');
                });
                this.classList.add('selected');
                radio.checked = true;
            }
        });
    });

    function checkQuizAnswer(quizId) {
        const block = document.querySelector(`[data-quiz-id="${quizId}"]`);
        if (!block || block.classList.contains('quiz-answered')) {
            return;
        }

        const selected = block.querySelector('input[type="radio"]:checked');
        if (!selected) {
            alert(translations.please_select_answer);
            return;
        }

        const selectedAnswer = parseInt(selected.value, 10);
        const correctAnswer = parseInt(block.querySelector('.quiz-correct-answer').value, 10);
        const feedback = block.querySelector('.quiz-feedback');
        const button = block.querySelector('.quiz-check-btn');

        block.classList.add('quiz-answered');
        button.disabled = true;

        block.querySelectorAll('.quiz-opt').forEach((answerOption, index) => {
            if (index === correctAnswer) {
                answerOption.classList.add('correct-answer');
                if (!answerOption.querySelector('.correct-badge')) {
                    const badge = document.createElement('span');
                    badge.className = 'correct-badge';
                    badge.textContent = translations.correct_label;
                    answerOption.appendChild(badge);
                }
            }

            if (index === selectedAnswer && selectedAnswer !== correctAnswer) {
                answerOption.classList.add('wrong-answer');
            }
        });

        feedback.style.display = 'flex';

        if (selectedAnswer === correctAnswer) {
            feedback.className = 'quiz-feedback success';
            feedback.textContent = translations.correct_label + '!';
        } else {
            feedback.className = 'quiz-feedback error';
            feedback.textContent = translations.your_answer + ': ' + String.fromCharCode(65 + selectedAnswer);
        }

        markQuizProgressComplete(quizId);
    }

    let examData = {
        lesson_id: {{ $lesson->id }},
        exam_index: 0,
        current_question: 0,
    };

    function initializeExam(examIndex = 0) {
        const examSegments = @json($examSegments->values());
        const examSegment = examSegments[examIndex] || null;
        if (!examSegment) {
            alert(@js(__('lessons.exam_not_found')));
            return;
        }

        examData.exam_index = examIndex;
        examData.current_question = 0;
        examData.questions = examSegment.questions || [];
        examData.exam_settings = examSegment.exam_settings || {};
        examData.time_limit = examSegment.exam_settings?.time_limit || 0;
        examData.answers = {};
        examData.questions.forEach((_, index) => {
            examData.answers[index] = null;
        });

        document.getElementById('totalQuestions').textContent = examData.questions.length;
        document.getElementById('examQuestionView').style.display = 'flex';
        document.getElementById('examResultsView').classList.remove('show');
        document.getElementById('examTakingContainer').classList.add('active');

        if (examData.timer_interval) {
            clearInterval(examData.timer_interval);
            examData.timer_interval = null;
        }

        if (examData.time_limit > 0) {
            examData.start_time = Date.now();
            examData.time_limit_ms = examData.time_limit * 60 * 1000;
            document.getElementById('examTimerDisplay').style.display = 'flex';
            startTimer();
        } else {
            document.getElementById('examTimerDisplay').style.display = 'none';
            document.getElementById('timerText').textContent = '00:00';
        }

        renderQuestion();
    }

    function startTimer() {
        examData.timer_interval = setInterval(() => {
            const elapsed = Date.now() - examData.start_time;
            const remaining = examData.time_limit_ms - elapsed;

            if (remaining <= 0) {
                clearInterval(examData.timer_interval);
                submitExam();
                return;
            }

            const minutes = Math.floor(remaining / 60000);
            const seconds = Math.floor((remaining % 60000) / 1000);
            document.getElementById('timerText').textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            const timerElement = document.getElementById('examTimerDisplay');
            timerElement.className = remaining < 60000
                ? 'exam-timer time-critical'
                : remaining < 180000
                    ? 'exam-timer time-warning'
                    : 'exam-timer';
        }, 1000);
    }

    function renderQuestion() {
        const question = examData.questions[examData.current_question];
        if (!question) {
            return;
        }

        document.getElementById('qNumber').textContent = examData.current_question + 1;
        document.getElementById('currentQuestion').textContent = examData.current_question + 1;
        document.getElementById('qText').textContent = question.question || '';
        document.getElementById('qType').textContent = translations[question.type] || question.type;

        const container = document.getElementById('answersContainer');
        container.innerHTML = '';

        if (question.type === 'multiple_choice' && question.answers) {
            renderExamOptionGroup(container, question.answers, examData.answers[examData.current_question]);
        } else if (question.type === 'true_false') {
            renderExamOptionGroup(container, [
                translations.true_answer,
                translations.false_answer,
            ], examData.answers[examData.current_question]);
        } else if (question.type === 'short_answer') {
            const label = document.createElement('label');
            label.className = 'sr-only';
            label.setAttribute('for', 'exam-short-answer');
            label.textContent = question.question || translations.short_answer;

            const input = document.createElement('input');
            input.type = 'text';
            input.id = 'exam-short-answer';
            input.className = 'exam-sa-input';
            input.placeholder = translations.short_answer;
            input.value = examData.answers[examData.current_question] || '';
            input.oninput = () => {
                examData.answers[examData.current_question] = input.value;
            };
            container.appendChild(label);
            container.appendChild(input);
            input.focus();
        }

        document.getElementById('prevBtn').disabled = examData.current_question === 0;
        const isLastQuestion = examData.current_question === examData.questions.length - 1;
        document.getElementById('nextBtn').style.display = isLastQuestion ? 'none' : 'flex';
        document.getElementById('submitBtn').style.display = isLastQuestion ? 'flex' : 'none';
    }

    function renderExamOptionGroup(container, options, storedAnswer) {
        const fieldset = document.createElement('fieldset');
        fieldset.className = 'exam-answer-group';
        fieldset.setAttribute('aria-labelledby', 'qText');

        const legend = document.createElement('legend');
        legend.className = 'sr-only';
        legend.textContent = document.getElementById('qText')?.textContent || translations.multiple_choice;
        fieldset.appendChild(legend);

        options.forEach((optionLabel, index) => {
            const optionId = `exam-answer-${examData.current_question}-${index}`;
            const option = document.createElement('label');
            option.className = 'exam-a-opt';
            option.setAttribute('for', optionId);

            const input = document.createElement('input');
            input.type = 'radio';
            input.id = optionId;
            input.name = `exam-answer-${examData.current_question}`;
            input.className = 'exam-answer-radio';
            input.value = index;
            input.checked = storedAnswer === index;
            input.onchange = () => {
                examData.answers[examData.current_question] = index;
                updateExamOptionSelection(fieldset);
            };

            const letter = document.createElement('span');
            letter.className = 'exam-a-letter';
            letter.textContent = String.fromCharCode(65 + index);

            const text = document.createElement('span');
            text.className = 'exam-a-text';
            text.textContent = optionLabel;

            option.appendChild(input);
            option.appendChild(letter);
            option.appendChild(text);
            fieldset.appendChild(option);
        });

        updateExamOptionSelection(fieldset);
        container.appendChild(fieldset);

        const activeInput = fieldset.querySelector('.exam-answer-radio:checked') || fieldset.querySelector('.exam-answer-radio');
        activeInput?.focus();
    }

    function updateExamOptionSelection(fieldset) {
        fieldset.querySelectorAll('.exam-a-opt').forEach((option) => {
            const input = option.querySelector('.exam-answer-radio');
            option.classList.toggle('exam-selected', Boolean(input?.checked));
        });
    }

    function nextQuestion() {
        if (examData.current_question < examData.questions.length - 1) {
            examData.current_question++;
            renderQuestion();
        }
    }

    function previousQuestion() {
        if (examData.current_question > 0) {
            examData.current_question--;
            renderQuestion();
        }
    }

    async function submitExam() {
        if (examData.timer_interval) {
            clearInterval(examData.timer_interval);
            examData.timer_interval = null;
        }

        const timeTaken = examData.time_limit > 0 ? Math.floor((Date.now() - examData.start_time) / 1000) : 0;
        const answerArray = examData.questions.map((_, index) => examData.answers[index] ?? null);
        const submitButton = document.getElementById('submitBtn');
        const originalLabel = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.style.opacity = '0.7';

        try {
            const response = await fetch('{{ route('lessons.grade-exam', $lesson) }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    exam_index: examData.exam_index,
                    answers: answerArray,
                    time_taken: timeTaken,
                }),
            });

            const responseData = await response.json().catch(() => null);

            if (!response.ok || !responseData || responseData.success === false) {
                throw new Error(responseData?.message || @js(__('lessons.exam_submit_failed')));
            }

            showResults(responseData);
        } catch (error) {
            console.error(error);
            alert(error.message || @js(__('lessons.exam_submit_failed')));
        } finally {
            submitButton.disabled = false;
            submitButton.style.opacity = '1';
            submitButton.innerHTML = originalLabel;
        }
    }

    function showResults(data) {
        const scoreDisplay = document.getElementById('scoreDisplay');
        const statusElement = document.getElementById('statusDisplay');
        const correctCount = document.getElementById('correctCount');
        const passingScore = document.getElementById('passingScore');
        const timeTaken = document.getElementById('timeTaken');
        const breakdown = document.getElementById('resultsBreakdown');
        const questionView = document.getElementById('examQuestionView');
        const resultsView = document.getElementById('examResultsView');

        if (!scoreDisplay || !statusElement || !correctCount || !passingScore || !timeTaken || !breakdown || !questionView || !resultsView) {
            console.error('Exam result elements are missing from the page.');
            alert(@js(__('lessons.exam_submit_failed')));
            return;
        }

        scoreDisplay.textContent = `${data.score}%`;
        statusElement.textContent = data.passed
            ? '{{ __('lessons.you_passed') }}'
            : (data.message || '{{ __('lessons.you_did_not_pass') }}');
        statusElement.className = `results-status ${data.passed ? 'passed' : 'failed'}`;

        correctCount.textContent = `${data.correct_count}/${data.total_questions}`;
        passingScore.textContent = `${examData.exam_settings?.passing_score || 70}%`;

        const minutes = Math.floor(data.time_taken / 60);
        const seconds = data.time_taken % 60;
        timeTaken.textContent = `${minutes}m ${seconds}s`;
        breakdown.innerHTML = '';

        data.results.forEach((result, index) => {
            const item = document.createElement('div');
            item.className = `result-item ${result.is_correct ? 'correct' : 'incorrect'}`;

            let answersMarkup = '';

            if (result.type === 'multiple_choice') {
                const question = examData.questions[index];
                answersMarkup = `<div class="result-answer"><span class="result-answer-label">{{ __('lessons.your_answer') }}:</span><span class="result-answer-value ${result.is_correct ? 'correct' : 'incorrect'}">${result.user_answer !== null ? String.fromCharCode(65 + result.user_answer) + '. ' + question.answers[result.user_answer] : translations.not_answered}</span></div>`;
                if (!result.is_correct) {
                    answersMarkup += `<div class="result-answer"><span class="result-answer-label">{{ __('lessons.correct_answer') }}:</span><span class="result-answer-value correct">${String.fromCharCode(65 + result.correct_answer) + '. ' + question.answers[result.correct_answer]}</span></div>`;
                }
            } else if (result.type === 'true_false') {
                const userAnswer = result.user_answer === 0
                    ? translations.true_answer
                    : result.user_answer === 1
                        ? translations.false_answer
                        : translations.not_answered;
                const correctAnswer = result.correct_answer === 'true' || result.correct_answer === 0
                    ? translations.true_answer
                    : translations.false_answer;
                answersMarkup = `<div class="result-answer"><span class="result-answer-label">{{ __('lessons.your_answer') }}:</span><span class="result-answer-value ${result.is_correct ? 'correct' : 'incorrect'}">${userAnswer}</span></div>`;
                if (!result.is_correct) {
                    answersMarkup += `<div class="result-answer"><span class="result-answer-label">{{ __('lessons.correct_answer') }}:</span><span class="result-answer-value correct">${correctAnswer}</span></div>`;
                }
            } else {
                answersMarkup = `<div class="result-answer"><span class="result-answer-label">{{ __('lessons.your_answer') }}:</span><span class="result-answer-value ${result.is_correct ? 'correct' : 'incorrect'}">${result.user_answer || translations.not_answered}</span></div>`;
                if (!result.is_correct) {
                    answersMarkup += `<div class="result-answer"><span class="result-answer-label">{{ __('lessons.correct_answer') }}:</span><span class="result-answer-value correct">${result.correct_answer}</span></div>`;
                }
            }

            item.innerHTML = `<div class="result-q-text"><strong>Q${result.question_index + 1}:</strong> ${result.question}</div>${answersMarkup}`;
            breakdown.appendChild(item);
        });

        questionView.style.display = 'none';
        resultsView.classList.add('show');
    }

    function retakeExam() {
        examData.current_question = 0;
        examData.answers = {};
        examData.questions.forEach((_, index) => {
            examData.answers[index] = null;
        });

        document.getElementById('examQuestionView').style.display = 'flex';
        document.getElementById('examResultsView').classList.remove('show');

        if (examData.time_limit) {
            examData.start_time = Date.now();
            startTimer();
        }

        renderQuestion();
    }

    function exitExam() {
        if (confirm('{{ __('lessons.are_you_sure') }}')) {
            if (examData.timer_interval) {
                clearInterval(examData.timer_interval);
                examData.timer_interval = null;
            }

            document.getElementById('examTakingContainer').classList.remove('active');
            document.getElementById('examTakingContainer').setAttribute('aria-hidden', 'true');
            document.getElementById('examTimerDisplay').style.display = 'none';
            examData = {
                lesson_id: {{ $lesson->id }},
                exam_index: 0,
                current_question: 0,
            };
        }
    }

    function startExam(examIndex = 0) {
        document.getElementById('examTakingContainer')?.setAttribute('aria-hidden', 'false');
        initializeExam(examIndex);
    }
</script>
