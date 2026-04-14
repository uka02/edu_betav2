@once
    <style>
        .app-signout-button {
            appearance: none;
            -webkit-appearance: none;
            background: transparent;
            font: inherit;
        }

        .btn-signout {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 13px;
            background: transparent;
            border: 1px solid var(--b1);
            color: var(--muted);
            border-radius: 7px;
            font-family: "Roboto", sans-serif;
            font-size: 12.5px;
            font-weight: 500;
            cursor: pointer;
            transition: all .15s;
        }

        .btn-signout:hover {
            background: rgba(240,80,80,.12);
            border-color: rgba(240,80,80,.25);
            color: #f05050;
        }
    </style>
@endonce

<form method="POST" action="{{ route('logout') }}" style="display:inline;">
    @csrf
    <button
        type="submit"
        class="app-signout-button {{ $buttonClass ?? 'btn-signout' }}"
        aria-label="{{ $label ?? __('dashboard.sign_out') }}"
        title="{{ $label ?? __('dashboard.sign_out') }}"
    >
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        {{ $label ?? __('dashboard.sign_out') }}
    </button>
</form>
