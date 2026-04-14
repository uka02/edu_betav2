<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title>{{ ($isAdminLessonWorkspace ?? false) ? __('dashboard.view_all_lessons') : __('lessons.my_lessons') }} - EduDev</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #060c12;
            --surface: #0b1520;
            --s1:      #0f1e2e;
            --s2:      #152438;
            --s3:      #1c2e44;
            --b0: rgba(56,139,220,0.07);
            --b1: rgba(56,139,220,0.13);
            --b2: rgba(56,139,220,0.22);
            --tx:   #e8f0f8;
            --tx2:  #a8bdd0;
            --muted:#587089;
            --blue:     #3b9eff;
            --blue-dim: #1e6fc4;
            --blue-soft:rgba(59,158,255,0.08);
            --blue-glow:rgba(59,158,255,0.15);
            --green:     #2ecc8a;
            --green-soft:rgba(46,204,138,0.08);
            --amber:     #f5a623;
            --amber-soft:rgba(245,166,35,0.08);
            --red:      #f05050;
            --red-soft: rgba(240,80,80,0.08);
            --sidebar-w: 252px;
            --r:  10px;
            --rl: 14px;
        }

        [data-theme="light"] {
            --bg:      #f0f4f8; --surface: #ffffff; --s1: #ffffff;
            --s2:      #f0f4f8; --s3:      #e2e8f0;
            --b0: rgba(56,139,220,0.10); --b1: rgba(56,139,220,0.18); --b2: rgba(56,139,220,0.30);
            --tx: #0f172a; --tx2: #334155; --muted: #64748b;
            --blue: #2563eb; --blue-dim: #1d4ed8;
            --blue-soft: rgba(37,99,235,0.08); --blue-glow: rgba(37,99,235,0.15);
            --green: #059669; --green-soft: rgba(5,150,105,0.08);
            --amber: #d97706; --amber-soft: rgba(217,119,6,0.08);
            --red: #dc2626; --red-soft: rgba(220,38,38,0.08);
        }

        html { height: 100%; }
        body { font-family: "Roboto", sans-serif; background: var(--bg); color: var(--tx); min-height: 100vh; display: flex; flex-direction: column; transition: background 0.3s, color 0.3s; }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-w); flex-shrink: 0;
            background: var(--surface); border-right: 1px solid var(--b0);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0; z-index: 50;
            transition: background 0.3s;
        }

        .sb-brand { display: flex; align-items: center; gap: 10px; padding: 18px 16px 16px; border-bottom: 1px solid var(--b0); }
        .sb-mark { width: 30px; height: 30px; border-radius: 7px; background: linear-gradient(135deg, var(--blue-dim), var(--blue)); display: grid; place-items: center; font-size: 11px; font-weight: 800; color: white; letter-spacing: -0.04em; flex-shrink: 0; box-shadow: 0 4px 12px var(--blue-glow); }
        .sb-name { font-size: 15px; font-weight: 800; letter-spacing: -0.04em; color: var(--tx); }
        .sb-name span { color: var(--blue); }

        .sb-nav { flex: 1; overflow-y: auto; padding: 10px 8px; }
        .nav-grp-lbl { font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); padding: 12px 10px 5px; }

        .nav-a { display: flex; align-items: center; gap: 9px; padding: 8px 10px; border-radius: var(--r); font-size: 13.5px; font-weight: 500; color: var(--tx2); text-decoration: none; transition: all .15s; margin-bottom: 1px; position: relative; }
        .nav-a:hover { background: var(--s2); color: var(--tx); }
        .nav-a.active { background: var(--blue-soft); color: var(--tx); }
        .nav-a.active::before { content: ''; position: absolute; left: 0; top: 22%; bottom: 22%; width: 3px; border-radius: 0 3px 3px 0; background: var(--blue); }
        .nav-a svg { flex-shrink: 0; opacity: .55; transition: opacity .15s; }
        .nav-a:hover svg, .nav-a.active svg { opacity: 1; }

        .sb-foot { padding: 8px; border-top: 1px solid var(--b0); }
        .user-row { display: flex; align-items: center; gap: 9px; padding: 8px 10px; border-radius: var(--r); cursor: pointer; transition: background .15s; }
        .user-row:hover { background: var(--s2); }
        .u-av { width: 30px; height: 30px; border-radius: 7px; background: linear-gradient(135deg, var(--blue-dim), var(--blue)); display: grid; place-items: center; font-size: 12px; font-weight: 700; color: white; flex-shrink: 0; overflow: hidden; }
        .u-av img { width: 100%; height: 100%; object-fit: cover; }
        .u-info { flex: 1; min-width: 0; }
        .u-name  { font-size: 12.5px; font-weight: 600; color: var(--tx); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .u-email { font-size: 11px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* MAIN */
        .main-col { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-width: 0; }

        .topbar { height: 56px; display: flex; align-items: center; padding: 0 24px; border-bottom: 1px solid var(--b0); background: color-mix(in srgb, var(--bg) 85%, transparent); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 40; gap: 10px; transition: background 0.3s; }
        .tb-title { font-size: 14px; font-weight: 700; color: var(--tx2); letter-spacing: -.02em; }
        .tb-sep   { width: 1px; height: 14px; background: var(--b1); }
        .tb-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }

        .tb-btn { width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--b1); background: var(--s1); color: var(--tx2); display: grid; place-items: center; cursor: pointer; transition: all .15s; position: relative; }
        .tb-btn:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }

        .btn-signout { display: flex; align-items: center; gap: 6px; padding: 6px 13px; background: transparent; border: 1px solid var(--b1); color: var(--muted); border-radius: 7px; font-family: "Roboto", sans-serif; font-size: 12.5px; font-weight: 500; cursor: pointer; transition: all .15s; }
        .btn-signout:hover { background: var(--red-soft); border-color: rgba(240,80,80,.25); color: var(--red); }

        .content { padding: 24px; flex: 1; }

        /* PAGE HEADER */
        .page-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .page-title { font-size: 20px; font-weight: 800; letter-spacing: -.04em; color: var(--tx); }

        .btn-cta { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: white; border-radius: 8px; font-size: 12.5px; font-weight: 700; text-decoration: none; transition: opacity .15s, transform .15s; box-shadow: 0 4px 14px var(--blue-glow); border: none; cursor: pointer; font-family: "Roboto", sans-serif; }
        .btn-cta:hover { opacity: .9; transform: translateY(-1px); }

        .btn-sec { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: var(--s1); color: var(--tx2); border-radius: 8px; font-size: 12.5px; font-weight: 500; text-decoration: none; transition: all .15s; border: 1px solid var(--b1); }
        .btn-sec:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }

        .btn-row { display: flex; gap: 8px; }

        /* ALERT */
        .alert-ok { display: flex; align-items: center; gap: 8px; background: var(--green-soft); border: 1px solid rgba(46,204,138,.2); color: var(--green); padding: 10px 14px; border-radius: var(--r); font-size: 13px; font-weight: 500; margin-bottom: 20px; }

        /* FILTER BAR */
        .filter-bar { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }

        .filter-chip { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 99px; font-size: 12px; font-weight: 600; border: 1px solid var(--b1); background: var(--s1); color: var(--muted); cursor: pointer; transition: all .15s; text-decoration: none; }
        .filter-chip:hover, .filter-chip.active { background: var(--blue-soft); border-color: rgba(59,158,255,.3); color: var(--blue); }

        /* LESSONS GRID */
        .lessons-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 14px; }

        .lcard { background: var(--s1); border: 1px solid var(--b0); border-radius: var(--rl); overflow: hidden; text-decoration: none; display: flex; flex-direction: column; transition: all .2s cubic-bezier(.16,1,.3,1); animation: riseIn .4s cubic-bezier(.16,1,.3,1) both; }
        .lcard:hover { border-color: var(--b2); transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.2), 0 0 0 1px rgba(59,158,255,.1); }

        .lcard-thumb { width: 100%; height: 160px; background: var(--s2); display: grid; place-items: center; overflow: hidden; position: relative; border-bottom: 1px solid var(--b0); flex-shrink: 0; }
        .lcard-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s cubic-bezier(.16,1,.3,1); }
        .lcard:hover .lcard-thumb img { transform: scale(1.04); }
        .lcard-ph { font-size: 36px; }

        .lcard-badge { position: absolute; top: 8px; right: 8px; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; }
        .lb-pub  { background: var(--green-soft); color: var(--green); border: 1px solid rgba(46,204,138,.2); }
        .lb-dft  { background: rgba(87,112,137,.15); color: var(--muted); border: 1px solid var(--b1); }

        .lcard-type { position: absolute; top: 8px; left: 8px; font-size: 9.5px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; padding: 3px 7px; border-radius: 4px; background: rgba(6,12,18,.75); backdrop-filter: blur(4px); border: 1px solid var(--b1); color: var(--tx2); }
        [data-theme="light"] .lcard-type { background: rgba(240,244,248,.85); }

        .lcard-body { padding: 14px 16px 16px; flex: 1; display: flex; flex-direction: column; }
        .lcard-title { font-size: 14px; font-weight: 700; color: var(--tx); letter-spacing: -.02em; line-height: 1.35; margin-bottom: 8px; }

        .lcard-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
        .lm-chip { font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px; }
        .lm-free { background: var(--green-soft); color: var(--green); }
        .lm-paid { background: var(--amber-soft); color: var(--amber); }
        .lm-diff { background: var(--blue-soft); color: var(--blue); }
        .lm-dur  { font-size: 11px; color: var(--muted); display: flex; align-items: center; gap: 3px; margin-left: auto; }

        .lcard-actions { display: flex; gap: 8px; margin-top: auto; flex-wrap: wrap; }
        .lcard-actions .btn-sec { flex: 1; justify-content: center; }
        .lcard-actions .btn-cta { flex: 1; justify-content: center; box-shadow: none; }
        .lcard-actions form { flex: 1; }

        /* EMPTY STATE */
        .empty { background: var(--s1); border: 1px dashed var(--b2); border-radius: var(--rl); padding: 60px 24px; text-align: center; }
        .empty-ico { width: 52px; height: 52px; border-radius: 12px; background: var(--s2); display: grid; place-items: center; margin: 0 auto 16px; color: var(--muted); }
        .empty-ttl { font-size: 16px; font-weight: 700; color: var(--tx2); margin-bottom: 6px; }
        .empty-sub { font-size: 13px; color: var(--muted); margin-bottom: 20px; }

        /* PAGINATION */
        .pagination-wrap { margin-top: 24px; display: flex; justify-content: center; }
        .pagination-wrap nav { display: flex; gap: 4px; }
        .pagination-wrap .page-item .page-link { display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 7px; border: 1px solid var(--b1); background: var(--s1); color: var(--tx2); text-decoration: none; font-size: 13px; font-weight: 500; transition: all .15s; }
        .pagination-wrap .page-item.active .page-link { background: var(--blue-soft); border-color: rgba(59,158,255,.3); color: var(--blue); }
        .pagination-wrap .page-item .page-link:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }

        @keyframes riseIn { from{opacity:0;transform:translateY(12px);} to{opacity:1;transform:translateY(0);} }

        ::-webkit-scrollbar { width: 5px; } ::-webkit-scrollbar-track { background: transparent; } ::-webkit-scrollbar-thumb { background: var(--s3); border-radius: 99px; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-col { margin-left: 0; }
            .lessons-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

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
            @include('partials.app.user-summary')
        </div>
    </aside>

    <div class="main-col">
        <header class="topbar">
            <span class="tb-title">{{ ($isAdminLessonWorkspace ?? false) ? __('dashboard.view_all_lessons') : __('dashboard.my_lessons') }}</span>
            <div class="tb-sep"></div>
            <div class="tb-right">
                @include('partials.app.settings-button', [
                    'buttonClass' => 'tb-btn',
                    'buttonId' => 'settingsBtn2',
                    'title' => __('dashboard.settings'),
                ])
                @include('partials.app.logout-button')
            </div>
        </header>

        <div class="content">

            @if(session('success'))
                <div class="alert-ok">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-ok" style="background:var(--red-soft);border-color:rgba(240,80,80,.2);color:var(--red);">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="page-head">
                <div class="page-title">{{ ($isAdminLessonWorkspace ?? false) ? __('dashboard.view_all_lessons') : __('lessons.my_lessons') }}</div>
                <div class="btn-row">
                    <a href="{{ route('dashboard') }}" class="btn-sec">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        {{ __('lessons.back') }}
                    </a>
                    @if($canCreateLessons ?? true)
                        <a href="{{ route('lessons.create') }}" class="btn-cta">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            {{ __('lessons.create_lesson') }}
                        </a>
                    @endif
                </div>
            </div>

            @if($lessons->count() > 0)

                <div class="filter-bar">
                    <span style="font-size:12px;color:var(--muted);font-weight:600;">{{ $lessons->total() }} {{ __('lessons.total') ?? 'total' }}</span>
                </div>

                <div class="lessons-grid">
                    @foreach($lessons as $i => $lesson)
                        @include('partials.lessons.manage-card', [
                            'animationDelay' => $i * 0.04,
                        ])
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $lessons->links() }}
                </div>

            @else
                <div class="empty">
                    <div class="empty-ico">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div class="empty-ttl">{{ __('lessons.no_lessons') }}</div>
                    <div class="empty-sub">{{ __('lessons.create_first_lesson') }}</div>
                    @if($canCreateLessons ?? true)
                        <a href="{{ route('lessons.create') }}" class="btn-cta">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            {{ __('lessons.create_lesson') }}
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>
</div>

@include('partials.app.settings-panel')
</body>
</html>

