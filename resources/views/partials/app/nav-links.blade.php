@php
    $linkClass = $linkClass ?? 'nav-a';
    $navGroupClass = $navGroupClass ?? 'nav-grp-lbl';
    $navUser = auth()->user();
    $isGuestNav = ! $navUser;
    $isAdminNav = $navUser?->isAdmin();
    $navGroupLabel = $navGroupLabel ?? ($isGuestNav
        ? __('lessons.explore_lessons')
        : ($isAdminNav
            ? __('dashboard.nav_admin')
            : ($navUser?->isEducator() ? __('dashboard.role_educator') : __('dashboard.nav_learning'))));
    $activeKey = $activeKey ?? null;
    $showCreateLink = $showCreateLink ?? $navUser?->isEducator();
    $lessonsNavLabel = $lessonsNavLabel ?? ($navUser?->isEducator() ? __('dashboard.my_lessons') : __('lessons.explore_lessons'));
    $showSettings = $showSettings ?? false;
    $settingsId = $settingsId ?? null;
    $settingsGroupLabel = $settingsGroupLabel ?? __('dashboard.settings');
    $settingsGroupStyle = $settingsGroupStyle ?? null;
    $navLinks = $isGuestNav
        ? [
            [
                'key' => 'home',
                'route' => route('home'),
                'label' => __('auth.back_home'),
                'icon' => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5 10.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9.5"/>',
            ],
            [
                'key' => 'lessons',
                'route' => route('lessons.index'),
                'label' => __('lessons.explore_lessons'),
                'icon' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
            ],
            [
                'key' => 'login',
                'route' => route('login'),
                'label' => __('auth.sign_in'),
                'icon' => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>',
            ],
            [
                'key' => 'signup',
                'route' => route('signup'),
                'label' => __('auth.create_account'),
                'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="16" y1="11" x2="22" y2="11"/>',
            ],
        ]
        : ($isAdminNav
        ? [
            [
                'key' => 'lessons',
                'route' => route('lessons.index'),
                'label' => __('dashboard.view_all_lessons'),
                'icon' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
            ],
            [
                'key' => 'admin-verifications',
                'route' => route('admin.certificate-verifications.index'),
                'label' => __('dashboard.certificate_reviews'),
                'icon' => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
            ],
        ]
        : [
            [
                'key' => 'dashboard',
                'route' => route('dashboard'),
                'label' => __('dashboard.dashboard'),
                'icon' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
            ],
            [
                'key' => 'lessons',
                'route' => route('lessons.index'),
                'label' => $lessonsNavLabel,
                'icon' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
            ],
            [
                'key' => 'profile',
                'route' => route('profile.edit'),
                'label' => __('dashboard.profile'),
                'icon' => '<path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/>',
            ],
        ]);

    if (! $isGuestNav && ! $isAdminNav && $showCreateLink) {
        $navLinks[] = [
            'key' => 'create',
            'route' => route('lessons.create'),
            'label' => __('lessons.create_lesson'),
            'icon' => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        ];
    }

    if (! $isGuestNav && ! $isAdminNav) {
        $navLinks[] = [
            'key' => 'certificates',
            'route' => route('certificates.index'),
            'label' => __('dashboard.certificates'),
            'icon' => '<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>',
        ];
    }
@endphp

@once
    <style>
        .app-nav-group {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted, #587089);
            padding: 12px 10px 5px;
        }

        .app-nav-link {
            display: flex;
            align-items: center;
            gap: 9px;
            width: 100%;
            padding: 8px 10px;
            border-radius: 10px;
            font: 500 13.5px "Roboto", sans-serif;
            color: var(--tx2, #a8bdd0);
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all .15s ease;
            margin-bottom: 1px;
            position: relative;
            appearance: none;
            -webkit-appearance: none;
        }

        .app-nav-link:hover {
            background: var(--s2, #152438);
            color: var(--tx, #e8f0f8);
        }

        .app-nav-link.active {
            background: var(--blue-soft, rgba(59,158,255,0.08));
            color: var(--tx, #e8f0f8);
        }

        .app-nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 22%;
            bottom: 22%;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: var(--blue, #3b9eff);
        }

        .app-nav-link svg {
            flex-shrink: 0;
            opacity: .55;
            transition: opacity .15s ease;
        }

        .app-nav-link:hover svg,
        .app-nav-link.active svg {
            opacity: 1;
        }
    </style>
@endonce

<div class="app-nav-group {{ $navGroupClass }}">{{ $navGroupLabel }}</div>

@foreach($navLinks as $navLink)
    <a
        href="{{ $navLink['route'] }}"
        class="app-nav-link {{ $linkClass }}{{ $activeKey === $navLink['key'] ? ' active' : '' }}"
        @if($activeKey === $navLink['key']) aria-current="page" @endif
    >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            {!! $navLink['icon'] !!}
        </svg>
        {{ $navLink['label'] }}
    </a>
@endforeach

@if($showSettings)
    <div class="app-nav-group {{ $navGroupClass }}" @if($settingsGroupStyle) style="{{ $settingsGroupStyle }}" @endif>
        {{ $settingsGroupLabel }}
    </div>

    <button
        type="button"
        class="app-nav-link {{ $linkClass }}"
        @if($settingsId) id="{{ $settingsId }}" @endif
        style="text-align:left;"
        aria-label="{{ __('dashboard.settings') }}"
    >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
        </svg>
        {{ __('dashboard.settings') }}
    </button>
@endif
