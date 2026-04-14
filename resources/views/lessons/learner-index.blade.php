<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title>{{ __('lessons.explore_lessons') }} - EduDev</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #060c12; --surface: #0b1520; --s1: #0f1e2e; --s2: #152438; --s3: #1c2e44;
            --b0: rgba(56,139,220,0.07); --b1: rgba(56,139,220,0.13); --b2: rgba(56,139,220,0.22);
            --tx: #e8f0f8; --tx2: #a8bdd0; --muted: #587089;
            --blue: #3b9eff; --blue-dim: #1e6fc4; --blue-soft: rgba(59,158,255,0.08); --blue-glow: rgba(59,158,255,0.15);
            --green: #2ecc8a; --green-soft: rgba(46,204,138,0.08);
            --amber: #f5a623; --amber-soft: rgba(245,166,35,0.08);
            --sidebar-w: 252px; --r: 10px; --rl: 14px;
        }

        [data-theme="light"] {
            --bg: #f0f4f8; --surface: #ffffff; --s1: #ffffff; --s2: #f0f4f8; --s3: #e2e8f0;
            --b0: rgba(56,139,220,0.10); --b1: rgba(56,139,220,0.18); --b2: rgba(56,139,220,0.30);
            --tx: #0f172a; --tx2: #334155; --muted: #64748b;
            --blue: #2563eb; --blue-dim: #1d4ed8; --blue-soft: rgba(37,99,235,0.08); --blue-glow: rgba(37,99,235,0.15);
            --green: #059669; --green-soft: rgba(5,150,105,0.08);
            --amber: #d97706; --amber-soft: rgba(217,119,6,0.08);
        }

        html { height: 100%; }
        body { font-family: "Roboto", sans-serif; background: var(--bg); color: var(--tx); min-height: 100vh; display: flex; flex-direction: column; }
        .sidebar { width: var(--sidebar-w); flex-shrink: 0; background: var(--surface); border-right: 1px solid var(--b0); display: flex; flex-direction: column; position: fixed; inset: 0 auto 0 0; z-index: 50; }
        .sb-nav { flex: 1; overflow-y: auto; padding: 10px 8px; }
        .sb-foot { padding: 8px; border-top: 1px solid var(--b0); }
        .main-col { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { height: 56px; display: flex; align-items: center; padding: 0 24px; border-bottom: 1px solid var(--b0); background: color-mix(in srgb, var(--bg) 85%, transparent); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 40; gap: 10px; }
        .tb-title { font-size: 14px; font-weight: 700; color: var(--tx2); letter-spacing: -.02em; }
        .tb-sep { width: 1px; height: 14px; background: var(--b1); }
        .tb-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }
        .tb-btn { width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--b1); background: var(--s1); color: var(--tx2); display: grid; place-items: center; cursor: pointer; transition: all .15s; }
        .tb-btn:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }
        .content { padding: 24px; flex: 1; }

        .page-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 22px; flex-wrap: wrap; }
        .page-title { font-size: 22px; font-weight: 800; letter-spacing: -.04em; color: var(--tx); }
        .page-sub { color: var(--muted); font-size: 13px; margin-top: 5px; max-width: 760px; line-height: 1.65; }
        .btn-row { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-cta, .btn-sec {
            display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-size: 12.5px;
            font-weight: 600; text-decoration: none; transition: all .15s; border: 1px solid transparent;
        }
        .btn-cta { background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: #fff; box-shadow: 0 4px 14px var(--blue-glow); }
        .btn-cta:hover { opacity: .92; transform: translateY(-1px); }
        .btn-sec { background: var(--s1); color: var(--tx2); border-color: var(--b1); }
        .btn-sec:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }

        .stat-strip { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
        .stat-chip {
            display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px; border-radius: 999px;
            background: var(--blue-soft); border: 1px solid rgba(59,158,255,.2); color: var(--blue); font-size: 12px; font-weight: 700;
        }
        .stat-chip strong { color: inherit; font-size: 13px; }

        .toolbar-card, .results-card {
            background: var(--surface); border: 1px solid var(--b0); border-radius: var(--rl); padding: 18px;
        }
        .toolbar-card { margin-bottom: 20px; }
        .section-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .section-title { font-size: 18px; font-weight: 800; letter-spacing: -.03em; color: var(--tx); }
        .section-sub { color: var(--muted); font-size: 13px; margin-top: 4px; }

        .filter-grid { display: grid; grid-template-columns: minmax(240px, 1.5fr) repeat(4, minmax(140px, .5fr)) auto; gap: 12px; align-items: end; }
        .filter-group { display: flex; flex-direction: column; gap: 8px; }
        .filter-group label { font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
        .filter-input, .filter-select {
            width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid var(--b1); background: var(--s1); color: var(--tx);
            font: inherit; transition: border-color .15s, background .15s;
        }
        .filter-input:focus, .filter-select:focus { outline: none; border-color: rgba(59,158,255,.35); }
        .filter-actions { display: flex; gap: 8px; align-items: end; }
        .filter-actions .btn-cta, .filter-actions .btn-sec { justify-content: center; }

        .subject-strip {
            display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--b0);
        }
        .subject-chip {
            display: inline-flex; align-items: center; gap: 10px; padding: 8px 12px; border-radius: 999px; border: 1px solid var(--b1);
            background: var(--s1); color: var(--tx2); text-decoration: none; font-size: 12px; font-weight: 600; transition: all .15s;
        }
        .subject-chip strong { font-size: 11px; font-weight: 700; color: var(--muted); }
        .subject-chip:hover, .subject-chip.is-active { background: var(--blue-soft); border-color: rgba(59,158,255,.26); color: var(--blue); }
        .subject-chip:hover strong, .subject-chip.is-active strong { color: var(--blue); }

        .results-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .results-summary { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .summary-chip {
            display: inline-flex; align-items: center; padding: 6px 11px; border-radius: 999px; background: var(--s1);
            border: 1px solid var(--b1); color: var(--tx2); font-size: 12px; font-weight: 600;
        }
        .summary-chip.is-total { background: var(--blue-soft); border-color: rgba(59,158,255,.2); color: var(--blue); }

        .catalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 14px; }
        .catalog-card {
            background: var(--s1); border: 1px solid var(--b0); border-radius: var(--rl); overflow: hidden; display: flex; flex-direction: column;
            min-width: 0; transition: all .2s cubic-bezier(.16,1,.3,1);
        }
        .catalog-card:hover { border-color: var(--b2); transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,.18), 0 0 0 1px rgba(59,158,255,.08); }
        .catalog-thumb {
            position: relative; display: block; height: 164px; overflow: hidden; text-decoration: none;
            background: linear-gradient(135deg, var(--s2), color-mix(in srgb, var(--s3) 85%, var(--blue-soft)));
            border-bottom: 1px solid var(--b0);
        }
        .catalog-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .28s ease; }
        .catalog-card:hover .catalog-thumb img { transform: scale(1.03); }
        .catalog-thumb-fallback {
            width: 100%; height: 100%; display: grid; place-items: center; font-size: 52px; font-weight: 800;
            color: rgba(232,240,248,.16);
        }
        .catalog-subject, .catalog-price {
            position: absolute; top: 12px; padding: 6px 10px; border-radius: 999px; font-size: 10px; font-weight: 800; letter-spacing: .08em;
            text-transform: uppercase;
        }
        .catalog-subject { left: 12px; background: rgba(6,12,18,.72); border: 1px solid var(--b1); color: var(--tx2); }
        .catalog-price { right: 12px; }
        .catalog-price.is-free { background: var(--green-soft); color: var(--green); border: 1px solid rgba(46,204,138,.2); }
        .catalog-price.is-paid { background: var(--amber-soft); color: var(--amber); border: 1px solid rgba(245,166,35,.2); }
        [data-theme="light"] .catalog-subject { background: rgba(240,244,248,.88); }

        .catalog-body { padding: 16px; display: flex; flex-direction: column; gap: 12px; flex: 1; }
        .catalog-topline { display: flex; align-items: center; justify-content: space-between; gap: 10px; font-size: 12px; color: var(--muted); }
        .catalog-topline > span:first-child {
            min-width: 0; font-weight: 600; color: var(--tx2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .catalog-award {
            padding: 5px 9px; border-radius: 999px; background: var(--blue-soft); color: var(--blue); font-size: 10px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .08em; flex-shrink: 0;
        }
        .catalog-title { color: var(--tx); text-decoration: none; font-size: 16px; font-weight: 700; line-height: 1.35; letter-spacing: -.02em; }
        .catalog-meta { display: flex; flex-wrap: wrap; gap: 8px; font-size: 12px; color: var(--tx2); }
        .catalog-meta span { padding: 5px 9px; background: var(--s2); border: 1px solid var(--b0); border-radius: 999px; }
        .catalog-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
        .catalog-stat { padding: 10px; background: var(--s2); border: 1px solid var(--b0); border-radius: 10px; }
        .catalog-stat-value { display: block; font-size: 16px; font-weight: 800; color: var(--tx); }
        .catalog-stat-label { display: block; font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-top: 3px; }
        .catalog-progress-shell { padding: 12px; background: color-mix(in srgb, var(--s2) 92%, transparent); border: 1px solid var(--b0); border-radius: 12px; }
        .catalog-progress-head {
            display: flex; align-items: center; justify-content: space-between; gap: 10px; font-size: 12px; font-weight: 700; color: var(--tx2); margin-bottom: 8px;
        }
        .catalog-progress-track { height: 6px; border-radius: 999px; background: var(--s3); overflow: hidden; }
        .catalog-progress-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--blue-dim), var(--blue)); }
        .catalog-progress-note { margin-top: 8px; font-size: 11px; color: var(--muted); line-height: 1.5; }
        .catalog-actions { display: flex; gap: 8px; margin-top: auto; }
        .catalog-btn-primary, .catalog-btn-secondary {
            flex: 1; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border-radius: 10px;
            padding: 11px 12px; font-size: 12.5px; font-weight: 700; border: 1px solid transparent; transition: all .15s;
        }
        .catalog-btn-primary { background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: #fff; box-shadow: 0 4px 14px var(--blue-glow); }
        .catalog-btn-primary:hover { opacity: .92; transform: translateY(-1px); }
        .catalog-btn-secondary { background: var(--s2); color: var(--tx2); border-color: var(--b1); }
        .catalog-btn-secondary:hover { background: var(--s3); border-color: var(--b2); color: var(--tx); }

        .empty-state { padding: 56px 24px; border: 1px dashed var(--b2); border-radius: var(--rl); background: var(--s1); text-align: center; }
        .empty-state h3 { font-size: 18px; font-weight: 800; letter-spacing: -.03em; color: var(--tx); }
        .empty-state p { margin: 10px auto 20px; max-width: 540px; color: var(--muted); font-size: 14px; line-height: 1.7; }
        .pagination-wrap { margin-top: 24px; display: flex; justify-content: center; }
        .pagination-wrap nav { display: flex; gap: 4px; }
        .pagination-wrap .page-item .page-link {
            display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 7px;
            border: 1px solid var(--b1); background: var(--s1); color: var(--tx2); text-decoration: none; font-size: 13px; font-weight: 500; transition: all .15s;
        }
        .pagination-wrap .page-item.active .page-link { background: var(--blue-soft); border-color: rgba(59,158,255,.3); color: var(--blue); }
        .pagination-wrap .page-item .page-link:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }

        @media (max-width: 1200px) {
            .filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .filter-actions { grid-column: span 2; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-col { margin-left: 0; }
            .content { padding: 16px 12px 24px; }
            .toolbar-card, .results-card { padding: 16px; }
            .filter-grid, .catalog-grid, .catalog-stats, .catalog-actions { grid-template-columns: 1fr; }
            .filter-actions { grid-column: auto; }
            .page-head { align-items: flex-start; }
        }
    </style>
</head>
<body>
@php
    $isGuestCatalog = auth()->guest();
    $activeFilterCount = collect($filters)
        ->reject(fn ($value, $key) => $key === 'sort' && $value === 'newest')
        ->filter(fn ($value) => filled($value))
        ->count();

    $sharedSubjectQuery = array_filter([
        'q' => $filters['q'],
        'difficulty' => $filters['difficulty'],
        'price' => $filters['price'],
        'sort' => $filters['sort'],
    ]);
@endphp
<div style="display:flex;flex:1;min-height:100vh;">
    <aside class="sidebar">
        @include('partials.app.sidebar-brand')
        <nav class="sb-nav">
            @include('partials.app.nav-links', [
                'activeKey' => 'lessons',
                'showSettings' => true,
                'settingsId' => 'settingsBtn',
                'settingsGroupStyle' => 'margin-top:8px;',
            ])
        </nav>
        <div class="sb-foot">
            @if($isGuestCatalog)
                @include('partials.app.guest-access-card')
            @else
                @include('partials.app.user-summary')
            @endif
        </div>
    </aside>

    <div class="main-col">
        <header class="topbar">
            <span class="tb-title">{{ __('lessons.explore_lessons') }}</span>
            <div class="tb-sep"></div>
            <div class="tb-right">
                @include('partials.app.settings-button', [
                    'buttonClass' => 'tb-btn',
                    'buttonId' => 'settingsBtn2',
                    'title' => __('dashboard.settings'),
                ])
                @if($isGuestCatalog)
                    <a href="{{ route('login') }}" class="btn-sec">{{ __('auth.sign_in') }}</a>
                    <a href="{{ route('signup') }}" class="btn-cta">{{ __('auth.create_account') }}</a>
                @else
                    @include('partials.app.logout-button')
                @endif
            </div>
        </header>

        <div class="content">
            <div class="page-head">
                <div>
                    <div class="page-title">{{ __('lessons.explore_lessons') }}</div>
                    <div class="page-sub">{{ __('lessons.explore_subtitle') }}</div>
                </div>
                <div class="btn-row">
                    @if($isGuestCatalog)
                        <a href="{{ route('home') }}" class="btn-sec">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9.5"/></svg>
                            {{ __('auth.back_home') }}
                        </a>
                        <a href="{{ route('signup') }}" class="btn-cta">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="16" y1="11" x2="22" y2="11"/></svg>
                            {{ __('auth.create_account') }}
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn-sec">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                            {{ __('certificates.back_to_dashboard') }}
                        </a>
                        <a href="{{ route('certificates.index') }}" class="btn-cta">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                            {{ __('dashboard.certificates') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="stat-strip">
                @if($isGuestCatalog)
                    <div class="stat-chip"><strong>{{ count($subjectOptions) }}</strong> {{ __('lessons.all_subjects') }}</div>
                    <div class="stat-chip"><strong>{{ $lessons->total() }}</strong> {{ __('lessons.total_lessons') }}</div>
                @else
                    <div class="stat-chip"><strong>{{ $startedLessonsCount }}</strong> {{ __('lessons.started_lessons') }}</div>
                    <div class="stat-chip"><strong>{{ $completedLessonsCount }}</strong> {{ __('lessons.completed_lessons') }}</div>
                @endif
                <div class="stat-chip"><strong>{{ $publishedCatalogCount }}</strong> {{ __('lessons.published_courses') }}</div>
                @if(! $isGuestCatalog)
                    <div class="stat-chip"><strong>{{ $learnerCertificateCount }}</strong> {{ __('dashboard.certificates') }}</div>
                @endif
            </div>

            <section class="toolbar-card">
                <div class="section-head">
                    <div>
                        <div class="section-title">{{ __('lessons.catalog_heading') }}</div>
                        <div class="section-sub">{{ __('lessons.catalog_subheading') }}</div>
                    </div>
                    @if($activeFilterCount > 0)
                        <a href="{{ route('lessons.index') }}" class="btn-sec">{{ __('lessons.clear_filters') }}</a>
                    @endif
                </div>

                <form method="GET" action="{{ route('lessons.index') }}" class="filter-grid">
                    <div class="filter-group">
                        <label for="catalog-search">{{ __('lessons.search_label') }}</label>
                        <input
                            id="catalog-search"
                            type="text"
                            name="q"
                            value="{{ $filters['q'] }}"
                            class="filter-input"
                            placeholder="{{ __('lessons.search_placeholder') }}"
                        >
                    </div>

                    <div class="filter-group">
                        <label for="catalog-subject">{{ __('lessons.subject') }}</label>
                        <select id="catalog-subject" name="subject" class="filter-select">
                            <option value="">{{ __('lessons.all_subjects') }}</option>
                            @foreach($subjectOptions as $subjectOption)
                                <option value="{{ $subjectOption }}" @selected($filters['subject'] === $subjectOption)>{{ __('lessons.subject_' . $subjectOption) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="catalog-difficulty">{{ __('lessons.difficulty') }}</label>
                        <select id="catalog-difficulty" name="difficulty" class="filter-select">
                            <option value="">{{ __('lessons.all_levels') }}</option>
                            <option value="beginner" @selected($filters['difficulty'] === 'beginner')>{{ __('lessons.beginner') }}</option>
                            <option value="intermediate" @selected($filters['difficulty'] === 'intermediate')>{{ __('lessons.intermediate') }}</option>
                            <option value="advanced" @selected($filters['difficulty'] === 'advanced')>{{ __('lessons.advanced') }}</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="catalog-sort">{{ __('lessons.sort') }}</label>
                        <select id="catalog-sort" name="sort" class="filter-select">
                            <option value="newest" @selected($filters['sort'] === 'newest')>{{ __('lessons.sort_newest') }}</option>
                            <option value="popular" @selected($filters['sort'] === 'popular')>{{ __('lessons.sort_popular') }}</option>
                            <option value="quickest" @selected($filters['sort'] === 'quickest')>{{ __('lessons.sort_quickest') }}</option>
                            <option value="longest" @selected($filters['sort'] === 'longest')>{{ __('lessons.sort_longest') }}</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="catalog-price">{{ __('lessons.filter_price') }}</label>
                        <select id="catalog-price" name="price" class="filter-select">
                            <option value="">{{ __('lessons.all_prices') }}</option>
                            <option value="free" @selected($filters['price'] === 'free')>{{ __('lessons.free') }}</option>
                            <option value="paid" @selected($filters['price'] === 'paid')>{{ __('lessons.paid') }}</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-cta">{{ __('dashboard.search_lessons') }}</button>
                    </div>
                </form>

                <div class="subject-strip">
                    <a href="{{ route('lessons.index', $sharedSubjectQuery) }}" class="subject-chip{{ $filters['subject'] === null ? ' is-active' : '' }}">
                        <span>{{ __('lessons.all_subjects') }}</span>
                        <strong>{{ $publishedCatalogCount }}</strong>
                    </a>
                    @foreach($subjectOptions as $subjectOption)
                        <a
                            href="{{ route('lessons.index', array_merge($sharedSubjectQuery, ['subject' => $subjectOption])) }}"
                            class="subject-chip{{ $filters['subject'] === $subjectOption ? ' is-active' : '' }}"
                        >
                            <span>{{ __('lessons.subject_' . $subjectOption) }}</span>
                            <strong>{{ $subjectCounts->get($subjectOption, 0) }}</strong>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="results-card">
                <div class="results-head">
                    <div>
                        <div class="section-title">{{ __('lessons.catalog_heading') }}</div>
                        <div class="section-sub">{{ __('lessons.catalog_subheading') }}</div>
                    </div>
                    <div class="results-summary">
                        <span class="summary-chip is-total">{{ $lessons->total() }} {{ __('lessons.total') }}</span>
                        @if($filters['q'] !== '')
                            <span class="summary-chip">{{ $filters['q'] }}</span>
                        @endif
                        @if($filters['subject'])
                            <span class="summary-chip">{{ __('lessons.subject_' . $filters['subject']) }}</span>
                        @endif
                        @if($filters['difficulty'])
                            <span class="summary-chip">{{ __('lessons.' . $filters['difficulty']) }}</span>
                        @endif
                        @if($filters['price'])
                            <span class="summary-chip">{{ __('lessons.' . $filters['price']) }}</span>
                        @endif
                    </div>
                </div>

                @if($lessons->count() > 0)
                    <div class="catalog-grid">
                        @foreach($lessons as $lesson)
                            @include('partials.lessons.catalog-card', ['lesson' => $lesson])
                        @endforeach
                    </div>

                    <div class="pagination-wrap">
                        {{ $lessons->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <h3>{{ __('lessons.no_catalog_results') }}</h3>
                        <p>{{ __('lessons.no_catalog_results_description') }}</p>
                        <a href="{{ route('lessons.index') }}" class="btn-cta">{{ __('lessons.clear_filters') }}</a>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>

@include('partials.app.settings-panel')
</body>
</html>
