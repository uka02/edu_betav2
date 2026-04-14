@once
    <style>
        .app-settings-trigger {
            appearance: none;
            -webkit-appearance: none;
            background: transparent;
            font: inherit;
        }
    </style>
@endonce

<button
    type="button"
    class="app-settings-trigger {{ $buttonClass ?? 'tb-btn' }}"
    id="{{ $buttonId ?? 'settingsBtn2' }}"
    aria-label="{{ $title ?? __('dashboard.settings') }}"
    @if(!empty($title)) title="{{ $title }}" @endif
>
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="3"/>
        <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
    </svg>
</button>
