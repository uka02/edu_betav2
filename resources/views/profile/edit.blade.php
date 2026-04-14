<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title>{{ __('dashboard.profile') }} - EduDev</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/dashboard.css')
    <style>
        .profile-top-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid var(--b1);
            background: var(--s1);
            color: var(--tx2);
            text-decoration: none;
            font: 600 12px "Roboto", sans-serif;
            transition: all .15s ease;
        }

        .profile-top-link:hover {
            background: var(--s2);
            border-color: var(--b2);
            color: var(--tx);
        }

        .profile-hero {
            margin-bottom: 22px;
        }

        .profile-hero .hero-r {
            align-items: flex-start;
        }

        .profile-role-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid var(--b1);
            background: rgba(6, 12, 18, 0.24);
            color: var(--tx2);
            font: 700 11px "Roboto", sans-serif;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(280px, .95fr);
            gap: 22px;
            align-items: start;
        }

        .profile-panel {
            background: var(--s1);
            border: 1px solid var(--b0);
            border-radius: var(--rl);
            padding: 22px;
            animation: riseIn .45s cubic-bezier(.16,1,.3,1) both;
        }

        .profile-section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .profile-section-title {
            font: 800 18px "Roboto", sans-serif;
            letter-spacing: -.03em;
            color: var(--tx);
            margin-bottom: 6px;
        }

        .profile-section-copy {
            max-width: 560px;
            color: var(--tx2);
            font-size: 13px;
            line-height: 1.65;
        }

        .profile-field-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .profile-field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .profile-field-full {
            grid-column: 1 / -1;
        }

        .profile-label {
            color: var(--tx2);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .03em;
        }

        .profile-input {
            width: 100%;
            min-height: 46px;
            padding: 0 14px;
            border-radius: 12px;
            border: 1px solid var(--b1);
            background: var(--s2);
            color: var(--tx);
            font: 500 14px "Roboto", sans-serif;
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .profile-input::placeholder {
            color: var(--muted);
        }

        .profile-input:focus {
            border-color: var(--b2);
            background: var(--s3);
            box-shadow: 0 0 0 4px rgba(59, 158, 255, 0.12);
        }

        .profile-input.is-error {
            border-color: rgba(240, 80, 80, 0.52);
            box-shadow: 0 0 0 4px rgba(240, 80, 80, 0.1);
        }

        .profile-field-hint {
            color: var(--muted);
            font-size: 11.5px;
            line-height: 1.5;
        }

        .profile-field-error {
            color: #ff8b8b;
            font-size: 12px;
            line-height: 1.4;
        }

        .profile-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid var(--b0);
        }

        .profile-actions-copy {
            max-width: 400px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.55;
        }

        .profile-save {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 170px;
            min-height: 44px;
            padding: 0 18px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--blue-dim), var(--blue));
            color: #fff;
            font: 700 13px "Roboto", sans-serif;
            cursor: pointer;
            box-shadow: 0 12px 28px rgba(30, 111, 196, 0.22);
            transition: transform .15s ease, opacity .15s ease;
        }

        .profile-save:hover {
            opacity: .94;
            transform: translateY(-1px);
        }

        .profile-side-stack {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .profile-summary-card {
            position: relative;
            overflow: hidden;
        }

        .profile-summary-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(59,158,255,0.12), transparent 42%),
                radial-gradient(circle at bottom left, rgba(46,204,138,0.08), transparent 36%);
            pointer-events: none;
        }

        .profile-summary-inner {
            position: relative;
            z-index: 1;
        }

        .profile-avatar {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            display: grid;
            place-items: center;
            overflow: hidden;
            background: linear-gradient(135deg, var(--blue-dim), var(--blue));
            color: #fff;
            font: 800 28px "Roboto", sans-serif;
            box-shadow: 0 16px 32px rgba(30, 111, 196, 0.2);
            margin-bottom: 14px;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-summary-name {
            font: 800 22px "Roboto", sans-serif;
            letter-spacing: -.04em;
            color: var(--tx);
            margin-bottom: 5px;
        }

        .profile-summary-sub {
            color: var(--tx2);
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .profile-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }

        .profile-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--b1);
            background: rgba(255,255,255,0.03);
            color: var(--tx2);
            font: 700 11px "Roboto", sans-serif;
        }

        .profile-meta-list {
            display: grid;
            gap: 12px;
        }

        .profile-meta-item {
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid var(--b0);
            background: rgba(255,255,255,0.02);
        }

        .profile-meta-label {
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .profile-meta-value {
            color: var(--tx);
            font-size: 14px;
            font-weight: 600;
            line-height: 1.45;
            word-break: break-word;
        }

        .profile-note-card {
            border-style: dashed;
        }

        .profile-note-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: var(--blue-soft);
            color: var(--blue);
            margin-bottom: 12px;
        }

        .profile-note-title {
            color: var(--tx);
            font: 700 15px "Roboto", sans-serif;
            margin-bottom: 6px;
        }

        .profile-note-copy {
            color: var(--tx2);
            font-size: 13px;
            line-height: 1.65;
        }

        @media (max-width: 1080px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .profile-field-grid {
                grid-template-columns: 1fr;
            }

            .profile-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .profile-save {
                width: 100%;
            }

            .profile-hero {
                padding: 20px;
            }

            .profile-panel {
                padding: 18px;
            }
        }
    </style>
</head>
<body>
@php
    $profileUser = $user ?? auth()->user();
    $isEducatorProfile = $profileUser?->isEducator();
    $roleLabel = $profileUser?->isAdmin()
        ? __('dashboard.role_admin')
        : ($isEducatorProfile ? __('dashboard.role_educator') : __('dashboard.role_learner'));
    $accountLabel = $profileUser?->google_id ? __('dashboard.google_account') : __('dashboard.email_account');
    $memberSince = $profileUser?->created_at?->translatedFormat('F Y');
    $profileFirstName = old('first_name', $firstName ?? '');
    $profileLastName = old('last_name', $lastName ?? '');
    $profileEmail = old('email', $profileUser?->email ?? '');
    $profileUsername = old('username', $profileUser?->username ?? '');
    $displayName = trim($profileUser?->name ?? trim($profileFirstName . ' ' . $profileLastName));
@endphp

<div class="shell">
    <aside class="sidebar">
        @include('partials.app.sidebar-brand')

        <nav class="sb-nav">
            @include('partials.app.nav-links', [
                'activeKey' => 'profile',
                'showSettings' => true,
                'settingsId' => 'settingsBtn',
                'settingsGroupStyle' => 'margin-top:8px;',
            ])
        </nav>

        <div class="sb-foot">
            @include('partials.app.user-summary')
        </div>
    </aside>

    <div class="main-col">
        <header class="topbar">
            <span class="tb-title">{{ __('dashboard.manage_profile') }}</span>
            <div class="tb-sep"></div>
            <div class="tb-right">
                <a href="{{ route('dashboard') }}" class="profile-top-link">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    {{ __('dashboard.back_to_dashboard') }}
                </a>
                @include('partials.app.logout-button')
            </div>
        </header>

        <div class="content">
            @if(session('success'))
                <div class="alert-ok">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <section class="hero profile-hero">
                <div class="hero-l">
                    <div class="hero-date">{{ __('dashboard.profile') }}</div>
                    <div class="hero-title">{{ __('dashboard.manage_profile') }}</div>
                    <div class="hero-sub">{{ __('dashboard.profile_subtitle') }}</div>
                </div>
                <div class="hero-r">
                    <div class="profile-role-chip">{{ $roleLabel }}</div>
                    <div class="prog-box">
                        <div class="prog-head">
                            <span>{{ __('dashboard.sign_in_method') }}</span>
                            <span class="prog-pct">{{ $accountLabel }}</span>
                        </div>
                        <div class="hero-sub">{{ __('dashboard.profile_note') }}</div>
                    </div>
                </div>
            </section>

            <div class="profile-grid">
                <section class="profile-panel">
                    <div class="profile-section-head">
                        <div>
                            <div class="profile-section-title">{{ __('dashboard.personal_information') }}</div>
                            <div class="profile-section-copy">{{ __('dashboard.personal_information_copy') }}</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="profile-field-grid">
                            <div class="profile-field">
                                <label for="first_name" class="profile-label">{{ __('auth.first_name') }}</label>
                                <input
                                    id="first_name"
                                    type="text"
                                    name="first_name"
                                    value="{{ $profileFirstName }}"
                                    placeholder="{{ __('auth.first_name_placeholder') }}"
                                    autocomplete="given-name"
                                    class="profile-input{{ $errors->has('first_name') ? ' is-error' : '' }}"
                                    required
                                >
                                @error('first_name')
                                    <div class="profile-field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="profile-field">
                                <label for="last_name" class="profile-label">{{ __('auth.last_name') }}</label>
                                <input
                                    id="last_name"
                                    type="text"
                                    name="last_name"
                                    value="{{ $profileLastName }}"
                                    placeholder="{{ __('auth.last_name_placeholder') }}"
                                    autocomplete="family-name"
                                    class="profile-input{{ $errors->has('last_name') ? ' is-error' : '' }}"
                                >
                                @error('last_name')
                                    <div class="profile-field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="profile-field profile-field-full">
                                <label for="email" class="profile-label">{{ __('auth.email_address') }}</label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ $profileEmail }}"
                                    placeholder="{{ __('auth.email_placeholder') }}"
                                    autocomplete="email"
                                    class="profile-input{{ $errors->has('email') ? ' is-error' : '' }}"
                                    required
                                >
                                @error('email')
                                    <div class="profile-field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="profile-field profile-field-full">
                                <label for="username" class="profile-label">{{ __('dashboard.username') }}</label>
                                <input
                                    id="username"
                                    type="text"
                                    name="username"
                                    value="{{ $profileUsername }}"
                                    placeholder="edudev_user"
                                    autocomplete="username"
                                    class="profile-input{{ $errors->has('username') ? ' is-error' : '' }}"
                                >
                                <div class="profile-field-hint">{{ __('dashboard.username_helper') }}</div>
                                @error('username')
                                    <div class="profile-field-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="profile-actions">
                            <div class="profile-actions-copy">{{ __('dashboard.profile_save_hint') }}</div>
                            <button type="submit" class="profile-save">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                    <polyline points="17 21 17 13 7 13 7 21"/>
                                    <polyline points="7 3 7 8 15 8"/>
                                </svg>
                                {{ __('dashboard.save_changes') }}
                            </button>
                        </div>
                    </form>
                </section>

                <aside class="profile-side-stack">
                    <section class="profile-panel profile-summary-card">
                        <div class="profile-summary-inner">
                            <div class="profile-avatar">
                                @if($profileUser?->avatar)
                                    <img src="{{ $profileUser->avatar }}" alt="{{ $displayName }}">
                                @else
                                    {{ strtoupper(substr($displayName !== '' ? $displayName : 'U', 0, 1)) }}
                                @endif
                            </div>

                            <div class="profile-summary-name">{{ $displayName !== '' ? $displayName : __('dashboard.profile') }}</div>
                            <div class="profile-summary-sub">{{ __('dashboard.account_overview_copy') }}</div>

                            <div class="profile-pill-row">
                                <span class="profile-pill">{{ $roleLabel }}</span>
                                <span class="profile-pill">{{ $accountLabel }}</span>
                            </div>

                            <div class="profile-meta-list">
                                <div class="profile-meta-item">
                                    <div class="profile-meta-label">{{ __('dashboard.display_name') }}</div>
                                    <div class="profile-meta-value">{{ $displayName !== '' ? $displayName : __('dashboard.not_set') }}</div>
                                </div>

                                <div class="profile-meta-item">
                                    <div class="profile-meta-label">{{ __('dashboard.username') }}</div>
                                    <div class="profile-meta-value">{{ $profileUser?->username ?: __('dashboard.not_set') }}</div>
                                </div>

                                <div class="profile-meta-item">
                                    <div class="profile-meta-label">{{ __('dashboard.member_since') }}</div>
                                    <div class="profile-meta-value">{{ $memberSince ?: __('dashboard.not_set') }}</div>
                                </div>

                                <div class="profile-meta-item">
                                    <div class="profile-meta-label">{{ __('dashboard.sign_in_method') }}</div>
                                    <div class="profile-meta-value">{{ $accountLabel }}</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="profile-panel profile-note-card">
                        <div class="profile-note-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 20h9"/>
                                <path d="M12 4h9"/>
                                <path d="M4 9h16"/>
                                <path d="M4 15h16"/>
                            </svg>
                        </div>
                        <div class="profile-note-title">{{ __('dashboard.account_overview') }}</div>
                        <div class="profile-note-copy">{{ __('dashboard.profile_identity_note') }}</div>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="ft-grid">
        <div class="ft-col">
            <h4>{{ __('messages.platform') }}</h4>
            <ul>
                <li><a href="{{ route('dashboard') }}">{{ __('messages.why_choose') }}</a></li>
                <li><a href="{{ route('lessons.index') }}">{{ __('lessons.browse') }}</a></li>
                <li><a href="#">{{ __('messages.pricing') }}</a></li>
                <li><a href="#">{{ __('messages.community') }}</a></li>
            </ul>
        </div>
        <div class="ft-col">
            <h4>{{ __('messages.learn') }}</h4>
            <ul>
                <li><a href="#">{{ __('messages.getting_started') }}</a></li>
                <li><a href="#">{{ __('messages.best_practices') }}</a></li>
                <li><a href="#">{{ __('messages.tutorials') }}</a></li>
                <li><a href="#">{{ __('messages.documentation') }}</a></li>
            </ul>
        </div>
        <div class="ft-col">
            <h4>{{ __('messages.company') }}</h4>
            <ul>
                <li><a href="#">{{ __('messages.about') }}</a></li>
                <li><a href="#">{{ __('messages.blog') }}</a></li>
                <li><a href="#">{{ __('messages.careers') }}</a></li>
                <li><a href="#">{{ __('messages.contact') }}</a></li>
            </ul>
        </div>
        <div class="ft-col">
            <h4>{{ __('messages.legal') }}</h4>
            <ul>
                <li><a href="#">{{ __('messages.privacy') }}</a></li>
                <li><a href="#">{{ __('messages.terms') }}</a></li>
                <li><a href="#">{{ __('messages.security') }}</a></li>
                <li><a href="#">{{ __('messages.cookies') }}</a></li>
            </ul>
        </div>
    </div>
    <div class="ft-bot">
        <span class="ft-copy">&copy; {{ date('Y') }} EduDev. {{ __('messages.all_rights_reserved') }}</span>
        <div class="ft-socials">
            <a href="https://twitter.com" title="X">X</a>
            <a href="https://linkedin.com" title="LinkedIn">in</a>
            <a href="https://github.com" title="GitHub">GH</a>
        </div>
    </div>
</footer>

@include('partials.app.settings-panel')
</body>
</html>
