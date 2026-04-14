@once
    <style>
        .app-guest-card {
            padding: 14px;
            border-radius: 12px;
            background: var(--s1, #0f1e2e);
            border: 1px solid var(--b0, rgba(56,139,220,0.07));
        }

        .app-guest-card-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--tx, #e8f0f8);
        }

        .app-guest-card-copy {
            margin-top: 6px;
            font-size: 12px;
            line-height: 1.6;
            color: var(--muted, #587089);
        }

        .app-guest-card-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .app-guest-card-actions .btn-sec,
        .app-guest-card-actions .btn-cta {
            flex: 1;
            justify-content: center;
        }
    </style>
@endonce

<div class="app-guest-card">
    <div class="app-guest-card-title">{{ $title ?? __('lessons.track_progress') }}</div>
    <div class="app-guest-card-copy">{{ $copy ?? __('auth.sign_in_to_continue') }}</div>
    <div class="app-guest-card-actions">
        <a href="{{ route('login') }}" class="btn-sec">{{ __('auth.sign_in') }}</a>
        <a href="{{ route('signup') }}" class="btn-cta">{{ __('auth.create_account') }}</a>
    </div>
</div>
