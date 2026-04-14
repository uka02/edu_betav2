@php
    $user = auth()->user();
    $roleLabel = $user?->isAdmin()
        ? __('dashboard.role_admin')
        : ($user?->isEducator() ? __('dashboard.role_educator') : __('dashboard.role_learner'));
    $accountLabel = $user?->google_id ? __('dashboard.google_account') : __('dashboard.email_account');
@endphp

@once
    <style>
        .app-user-summary {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: 10px;
            transition: background .15s ease;
        }

        .app-user-summary:hover {
            background: var(--s2, #152438);
        }

        .app-user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: linear-gradient(135deg, var(--blue-dim, #1e6fc4), var(--blue, #3b9eff));
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            overflow: hidden;
        }

        .app-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .app-user-info {
            flex: 1;
            min-width: 0;
        }

        .app-user-name {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--tx, #e8f0f8);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .app-user-email {
            font-size: 11px;
            color: var(--muted, #587089);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
@endonce

<div class="app-user-summary {{ $wrapperClass ?? 'user-row' }}">
    <div class="app-user-avatar {{ $avatarClass ?? 'u-av' }}">
        @if($user?->avatar)
            <img src="{{ $user->avatar }}" alt="{{ $avatarAlt ?? 'avatar' }}">
        @else
            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
        @endif
    </div>
    <div class="app-user-info {{ $infoClass ?? 'u-info' }}">
        <div class="app-user-name {{ $nameClass ?? 'u-name' }}">{{ $user?->name ?? '' }}</div>
        <div class="app-user-email {{ $emailClass ?? 'u-email' }}">{{ $user?->email ?? '' }}</div>
        @if($user)
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:6px;">
                <span style="display:inline-flex;align-items:center;padding:2px 7px;border-radius:999px;background:var(--blue-soft, rgba(59,158,255,0.08));border:1px solid rgba(59,158,255,.18);color:var(--blue, #3b9eff);font-size:10px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;">
                    {{ $roleLabel }}
                </span>
                <span style="font-size:10px;color:var(--muted, #587089);">
                    {{ $accountLabel }}
                </span>
            </div>
        @endif
    </div>
</div>
