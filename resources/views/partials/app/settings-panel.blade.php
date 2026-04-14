@php
    $showFloatingTrigger = $showFloatingTrigger ?? false;
    $hoverTooltipCopy = [
        'open' => __('messages.tooltip_open'),
        'submit' => __('messages.tooltip_submit'),
        'action' => __('messages.tooltip_action'),
        'close' => __('messages.tooltip_close_action'),
        'genericClose' => __('messages.close'),
    ];
@endphp

@if($showFloatingTrigger)
    @include('partials.app.settings-button', [
        'buttonClass' => 'app-settings-fab',
        'buttonId' => 'settingsBtn2',
        'title' => __('dashboard.settings'),
    ])
@endif

<div class="app-settings-modal" id="settingsModal">
    <div class="app-settings-card">
        <div class="app-settings-head">
            <span class="app-settings-title">{{ __('dashboard.settings') }}</span>
            <button
                type="button"
                class="app-settings-close"
                id="settingsClose"
                aria-label="{{ __('messages.close') }}"
                title="{{ __('messages.close') }}"
            >&times;</button>
        </div>

        <div class="app-settings-sec">
            <div class="app-settings-lbl">{{ __('messages.app_settings') }}</div>
            <div class="app-settings-row">
                <span class="app-settings-row-lbl">{{ __('messages.dark_mode') }}</span>
                @include('partials.app.theme-toggle-button', [
                    'buttonClass' => 'app-settings-action',
                    'toggleId' => 'themeToggle',
                    'iconId' => 'themeIcon',
                    'title' => __('messages.dark_mode'),
                ])
            </div>
            <div class="app-settings-row">
                <span class="app-settings-row-lbl">{{ __('messages.language') }}</span>
                @include('partials.app.locale-switcher', ['variant' => 'settings'])
            </div>
        </div>

        <button type="button" class="app-settings-done" id="closeSettingsBtn">{{ __('messages.done') }}</button>
    </div>
</div>

<div class="app-hover-tooltip" id="appHoverTooltip" aria-hidden="true"></div>

<style>
    .app-settings-modal {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(4, 10, 18, 0.66);
        backdrop-filter: blur(8px);
        z-index: 180;
    }

    [data-theme="light"] .app-settings-modal {
        background: rgba(148, 163, 184, 0.26);
    }

    .app-settings-modal.active { display: flex; }

    .app-settings-card {
        width: min(420px, 100%);
        border-radius: 18px;
        border: 1px solid var(--b1, var(--border, rgba(255,255,255,0.08)));
        background: linear-gradient(180deg, var(--surface, #0f1219), var(--s1, var(--surface2, #161b26)));
        color: var(--tx, var(--text, #eef0f6));
        box-shadow: 0 28px 70px rgba(0, 0, 0, 0.45);
        overflow: hidden;
    }

    [data-theme="light"] .app-settings-card {
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
    }

    .app-settings-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 20px 14px;
        border-bottom: 1px solid var(--b0, var(--border, rgba(255,255,255,0.06)));
    }

    .app-settings-title {
        font: 800 15px "Roboto", sans-serif;
        letter-spacing: -0.03em;
    }

    .app-settings-close {
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 8px;
        background: var(--s2, var(--surface2, rgba(255,255,255,0.05)));
        color: var(--muted, var(--muted2, #94a3b8));
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        transition: all .15s ease;
    }

    .app-settings-close:hover {
        background: var(--s3, var(--surface2, rgba(255,255,255,0.08)));
        color: var(--tx, var(--text, #eef0f6));
    }

    [data-theme="light"] .app-settings-close:hover {
        color: var(--tx, #0f172a);
    }

    .app-settings-sec {
        padding: 18px 20px 10px;
    }

    .app-settings-lbl {
        margin-bottom: 12px;
        color: var(--muted, var(--muted2, #94a3b8));
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .app-settings-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 8px 0;
    }

    .app-settings-row-lbl {
        color: var(--tx2, var(--text, #eef0f6));
        font-size: 13px;
    }

    .app-settings-action {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 1px solid var(--b1, var(--border, rgba(255,255,255,0.08)));
        background: var(--s1, var(--surface2, rgba(255,255,255,0.05)));
        color: var(--tx2, var(--text, #eef0f6));
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all .15s ease;
    }

    .app-settings-action:hover {
        background: var(--s2, var(--surface2, rgba(255,255,255,0.08)));
        color: var(--tx, var(--text, #fff));
    }

    .app-settings-done {
        width: calc(100% - 40px);
        margin: 0 20px 20px;
        padding: 11px 16px;
        border: none;
        border-radius: 12px;
        background: var(--blue, var(--accent, #4f8ef7));
        color: #fff;
        font: 700 13px "Roboto", sans-serif;
        cursor: pointer;
        transition: transform .15s ease, opacity .15s ease;
    }

    .app-settings-done:hover {
        opacity: .92;
        transform: translateY(-1px);
    }

    .app-settings-fab {
        position: fixed;
        top: 16px;
        right: 16px;
        z-index: 120;
        width: 40px;
        height: 40px;
        border-radius: 999px;
        border: 1px solid var(--border, rgba(255,255,255,0.08));
        background: rgba(15, 18, 25, 0.88);
        color: var(--text, #eef0f6);
        display: grid;
        place-items: center;
        cursor: pointer;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.28);
        backdrop-filter: blur(10px);
    }

    [data-theme="light"] .app-settings-fab {
        border-color: rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.92);
        color: var(--text, #0f172a);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.14);
    }

    .app-hover-tooltip {
        position: fixed;
        top: 0;
        left: 0;
        max-width: min(280px, calc(100vw - 24px));
        padding: 9px 12px;
        border-radius: 12px;
        border: 1px solid var(--b1, var(--border, rgba(255,255,255,0.08)));
        background: linear-gradient(180deg, rgba(11, 21, 32, 0.97), rgba(15, 30, 46, 0.95));
        color: var(--tx, var(--text, #eef0f6));
        font: 600 12px/1.45 "Roboto", sans-serif;
        letter-spacing: -.01em;
        box-shadow: 0 16px 34px rgba(0, 0, 0, 0.32);
        backdrop-filter: blur(10px);
        pointer-events: none;
        opacity: 0;
        transform: translateY(6px) scale(.98);
        transition: opacity .14s ease, transform .14s ease;
        z-index: 260;
    }

    [data-theme="light"] .app-hover-tooltip {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(240, 244, 248, 0.96));
        color: var(--tx, #0f172a);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.16);
    }

    .app-hover-tooltip.active {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    @media (max-width: 640px) {
        .app-settings-modal { padding: 16px; }
        .app-settings-card { border-radius: 16px; }
        .app-settings-row { align-items: flex-start; flex-direction: column; }
        .app-settings-row-lbl { font-size: 12px; }
        .app-hover-tooltip { display: none; }
    }
</style>

@include('partials.app.theme-toggle-script')
<script>
    (() => {
        const modal = document.getElementById('settingsModal');

        if (!modal) {
            return;
        }

        const open = () => modal.classList.add('active');
        const close = () => modal.classList.remove('active');

        document.getElementById('settingsBtn')?.addEventListener('click', event => {
            event.preventDefault();
            open();
        });

        document.getElementById('settingsBtn2')?.addEventListener('click', event => {
            event.preventDefault();
            open();
        });

        document.getElementById('settingsClose')?.addEventListener('click', close);
        document.getElementById('closeSettingsBtn')?.addEventListener('click', close);

        modal.addEventListener('click', event => {
            if (event.target === modal) {
                close();
            }
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && modal.classList.contains('active')) {
                close();
            }
        });
    })();

    (() => {
        const tooltip = document.getElementById('appHoverTooltip');

        if (!tooltip) {
            return;
        }

        const copy = @json($hoverTooltipCopy);

        const selector = 'button, a[href], input[type="submit"], input[type="button"], [role="button"]';
        let activeTarget = null;
        let activeMode = null;

        const normalize = value => (value || '').replace(/\s+/g, ' ').trim();
        const clamp = (value, min, max) => Math.min(Math.max(value, min), max);
        const formatTooltip = (template, label) => template.replace(':label', label);

        const collectRawLabel = element => {
            const explicit = normalize(
                element.getAttribute('data-tooltip')
                || element.getAttribute('data-hover-help')
            );

            if (explicit) {
                return { text: explicit, explicit: true };
            }

            const title = normalize(element.getAttribute('title'));

            if (title) {
                element.setAttribute('data-native-title', title);
                element.removeAttribute('title');

                return { text: title, explicit: true };
            }

            const nativeTitle = normalize(element.getAttribute('data-native-title'));

            if (nativeTitle) {
                return { text: nativeTitle, explicit: true };
            }

            const ariaLabel = normalize(element.getAttribute('aria-label'));

            if (ariaLabel) {
                return { text: ariaLabel, explicit: true };
            }

            const value = normalize(element.getAttribute('value'));

            if (value) {
                return { text: value, explicit: false };
            }

            return { text: normalize(element.textContent), explicit: false };
        };

        const buildTooltipText = element => {
            const { text, explicit } = collectRawLabel(element);
            const label = text.length > 96 ? `${text.slice(0, 93)}...` : text;

            if (!label) {
                return '';
            }

            if (explicit) {
                return label;
            }

            const tagName = element.tagName.toLowerCase();
            const type = normalize(element.getAttribute('type')).toLowerCase();

            if (label === '×' || /^(close|хаах)$/i.test(label)) {
                return copy.genericClose;
            }

            if (/^(close|dismiss|cancel|хаах)$/i.test(label)) {
                return formatTooltip(copy.close, label);
            }

            if (tagName === 'a') {
                return formatTooltip(copy.open, label);
            }

            if (type === 'submit' || (tagName === 'button' && (type === '' || type === 'submit') && element.form)) {
                return formatTooltip(copy.submit, label);
            }

            return formatTooltip(copy.action, label);
        };

        const isTooltipTarget = element => {
            if (!element || !element.matches(selector)) {
                return false;
            }

            if (element.closest('#appHoverTooltip')) {
                return false;
            }

            if (element.hasAttribute('disabled') || element.getAttribute('aria-disabled') === 'true') {
                return false;
            }

            return true;
        };

        const resolveTarget = start => {
            if (!(start instanceof Element)) {
                return null;
            }

            const target = start.closest(selector);

            return isTooltipTarget(target) ? target : null;
        };

        const placeTooltip = (element, event) => {
            const viewportPadding = 12;
            const tooltipRect = tooltip.getBoundingClientRect();

            let left;
            let top;

            if (event) {
                left = event.clientX + 18;
                top = event.clientY + 20;
            } else {
                const rect = element.getBoundingClientRect();
                left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                top = rect.top - tooltipRect.height - 14;
            }

            if (left + tooltipRect.width > window.innerWidth - viewportPadding) {
                left = window.innerWidth - tooltipRect.width - viewportPadding;
            }

            left = clamp(left, viewportPadding, Math.max(viewportPadding, window.innerWidth - tooltipRect.width - viewportPadding));

            if (top + tooltipRect.height > window.innerHeight - viewportPadding) {
                top = window.innerHeight - tooltipRect.height - viewportPadding;
            }

            if (top < viewportPadding) {
                const rect = element.getBoundingClientRect();
                top = rect.bottom + 14;
            }

            top = clamp(top, viewportPadding, Math.max(viewportPadding, window.innerHeight - tooltipRect.height - viewportPadding));

            tooltip.style.left = `${left}px`;
            tooltip.style.top = `${top}px`;
        };

        const showTooltip = (element, options = {}) => {
            const text = buildTooltipText(element);

            if (!text) {
                return;
            }

            activeTarget = element;
            activeMode = options.mode || 'pointer';
            tooltip.textContent = text;
            tooltip.classList.add('active');
            tooltip.setAttribute('aria-hidden', 'false');
            placeTooltip(element, options.event || null);
        };

        const hideTooltip = () => {
            activeTarget = null;
            activeMode = null;
            tooltip.classList.remove('active');
            tooltip.setAttribute('aria-hidden', 'true');
        };

        document.addEventListener('pointerover', event => {
            if (event.pointerType && event.pointerType !== 'mouse' && event.pointerType !== 'pen') {
                hideTooltip();
                return;
            }

            const target = resolveTarget(event.target);

            if (!target) {
                hideTooltip();
                return;
            }

            if (target === activeTarget) {
                placeTooltip(target, event);
                return;
            }

            showTooltip(target, { mode: 'pointer', event });
        });

        document.addEventListener('pointermove', event => {
            if (activeTarget && activeMode === 'pointer') {
                placeTooltip(activeTarget, event);
            }
        });

        document.addEventListener('pointerout', event => {
            const target = resolveTarget(event.target);

            if (!target || target !== activeTarget) {
                return;
            }

            if (event.relatedTarget instanceof Element && target.contains(event.relatedTarget)) {
                return;
            }

            hideTooltip();
        });

        document.addEventListener('focusin', event => {
            const target = resolveTarget(event.target);

            if (!target) {
                return;
            }

            showTooltip(target, { mode: 'focus' });
        });

        document.addEventListener('focusout', event => {
            const target = resolveTarget(event.target);

            if (target && target === activeTarget) {
                hideTooltip();
            }
        });

        document.addEventListener('click', hideTooltip);
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                hideTooltip();
            }
        });

        window.addEventListener('scroll', () => {
            if (activeTarget && activeMode === 'focus') {
                placeTooltip(activeTarget, null);
            }
        }, true);

        window.addEventListener('resize', () => {
            if (activeTarget) {
                placeTooltip(activeTarget, null);
            }
        });
    })();
</script>
