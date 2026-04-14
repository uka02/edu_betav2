<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title>{{ __('certificates.view_certificate') }} — EduDev</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #060c12; --surface: #0b1520; --s1: #0f1e2e; --s2: #152438;
            --b0: rgba(56,139,220,0.07); --b1: rgba(56,139,220,0.13); --b2: rgba(56,139,220,0.22);
            --tx: #e8f0f8; --tx2: #a8bdd0; --muted: #587089;
            --blue: #3b9eff; --blue-dim: #1e6fc4; --blue-soft: rgba(59,158,255,0.08); --blue-glow: rgba(59,158,255,0.15);
            --amber: #f5a623; --amber-soft: rgba(245,166,35,0.08);
            --sidebar-w: 252px;
        }

        [data-theme="light"] {
            --bg: #f0f4f8; --surface: #ffffff; --s1: #ffffff; --s2: #f0f4f8;
            --b0: rgba(56,139,220,0.10); --b1: rgba(56,139,220,0.18); --b2: rgba(56,139,220,0.30);
            --tx: #0f172a; --tx2: #334155; --muted: #64748b;
            --blue: #2563eb; --blue-dim: #1d4ed8; --blue-soft: rgba(37,99,235,0.08); --blue-glow: rgba(37,99,235,0.15);
            --amber: #d97706; --amber-soft: rgba(217,119,6,0.08);
        }

        html { height: 100%; }
        body { font-family: "Roboto", sans-serif; background: var(--bg); color: var(--tx); min-height: 100vh; display: flex; flex-direction: column; }
        .sidebar { width: var(--sidebar-w); flex-shrink: 0; background: var(--surface); border-right: 1px solid var(--b0); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; z-index: 50; }
        .sb-brand { display: flex; align-items: center; gap: 10px; padding: 18px 16px 16px; border-bottom: 1px solid var(--b0); }
        .sb-mark { width: 30px; height: 30px; border-radius: 7px; background: linear-gradient(135deg, var(--blue-dim), var(--blue)); display: grid; place-items: center; font-size: 11px; font-weight: 800; color: white; letter-spacing: -0.04em; flex-shrink: 0; box-shadow: 0 4px 12px var(--blue-glow); }
        .sb-name { font-size: 15px; font-weight: 800; letter-spacing: -0.04em; color: var(--tx); }
        .sb-name span { color: var(--blue); }
        .sb-nav { flex: 1; overflow-y: auto; padding: 10px 8px; }
        .nav-grp-lbl { font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); padding: 12px 10px 5px; }
        .nav-a { display: flex; align-items: center; gap: 9px; padding: 8px 10px; border-radius: 10px; font-size: 13.5px; font-weight: 500; color: var(--tx2); text-decoration: none; transition: all .15s; margin-bottom: 1px; position: relative; }
        .nav-a:hover { background: var(--s2); color: var(--tx); }
        .nav-a.active { background: var(--blue-soft); color: var(--tx); }
        .nav-a.active::before { content: ''; position: absolute; left: 0; top: 22%; bottom: 22%; width: 3px; border-radius: 0 3px 3px 0; background: var(--blue); }
        .nav-a svg { flex-shrink: 0; opacity: .55; transition: opacity .15s; }
        .nav-a:hover svg, .nav-a.active svg { opacity: 1; }
        .sb-foot { padding: 8px; border-top: 1px solid var(--b0); }
        .user-row { display: flex; align-items: center; gap: 9px; padding: 8px 10px; border-radius: 10px; cursor: pointer; transition: background .15s; }
        .user-row:hover { background: var(--s2); }
        .u-av { width: 30px; height: 30px; border-radius: 7px; background: linear-gradient(135deg, var(--blue-dim), var(--blue)); display: grid; place-items: center; font-size: 12px; font-weight: 700; color: white; flex-shrink: 0; overflow: hidden; }
        .u-av img { width: 100%; height: 100%; object-fit: cover; }
        .u-info { flex: 1; min-width: 0; }
        .u-name { font-size: 12.5px; font-weight: 600; color: var(--tx); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .u-email { font-size: 11px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .main-col { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { height: 56px; display: flex; align-items: center; padding: 0 24px; border-bottom: 1px solid var(--b0); background: color-mix(in srgb, var(--bg) 85%, transparent); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 40; gap: 10px; }
        .tb-title { font-size: 14px; font-weight: 700; color: var(--tx2); letter-spacing: -.02em; }
        .tb-sep { width: 1px; height: 14px; background: var(--b1); }
        .tb-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }
        .tb-btn { width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--b1); background: var(--s1); color: var(--tx2); display: grid; place-items: center; cursor: pointer; transition: all .15s; position: relative; }
        .tb-btn:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }
        .btn-signout { display: flex; align-items: center; gap: 6px; padding: 6px 13px; background: transparent; border: 1px solid var(--b1); color: var(--muted); border-radius: 7px; font-family: "Roboto", sans-serif; font-size: 12.5px; font-weight: 500; cursor: pointer; transition: all .15s; }
        .btn-signout:hover { background: rgba(240,80,80,.12); border-color: rgba(240,80,80,.25); color: #f05050; }
        .content { padding: 24px; flex: 1; }
        .page-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
        .page-title { font-size: 22px; font-weight: 800; letter-spacing: -.04em; color: var(--tx); }
        .btn-row { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-cta, .btn-sec { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: all .15s; }
        .btn-cta { background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: white; box-shadow: 0 4px 14px var(--blue-glow); }
        .btn-cta:hover { opacity: .9; transform: translateY(-1px); }
        .btn-sec { background: var(--s1); color: var(--tx2); border: 1px solid var(--b1); }
        .btn-sec:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }
        .certificate-shell { max-width: 940px; margin: 0 auto; }
        .certificate-card { background: linear-gradient(180deg, color-mix(in srgb, var(--s1) 88%, transparent), var(--s1)); border: 1px solid var(--b1); border-radius: 24px; padding: 36px; box-shadow: 0 18px 48px rgba(0,0,0,.16); overflow: hidden; position: relative; }
        .certificate-card::before { content: ''; position: absolute; inset: 0; pointer-events: none; background: radial-gradient(circle at top right, rgba(59,158,255,.12), transparent 38%), radial-gradient(circle at bottom left, rgba(245,166,35,.10), transparent 30%); }
        .certificate-inner { position: relative; z-index: 1; border: 1px solid var(--b0); border-radius: 18px; padding: 32px; background: color-mix(in srgb, var(--s1) 92%, transparent); }
        .cert-kicker { display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 999px; background: var(--amber-soft); color: var(--amber); font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 18px; }
        .cert-title { font-family: "Roboto", sans-serif; font-size: 34px; font-weight: 800; letter-spacing: -.05em; margin-bottom: 10px; }
        .cert-copy { font-size: 14px; color: var(--tx2); line-height: 1.7; }
        .cert-name { font-family: "Roboto", sans-serif; font-size: 30px; font-weight: 800; letter-spacing: -.04em; color: var(--blue); margin: 20px 0 8px; }
        .cert-lesson { font-size: 19px; font-weight: 700; color: var(--tx); margin-bottom: 24px; }
        .meta-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-top: 24px; }
        .meta-box { background: var(--s2); border: 1px solid var(--b0); border-radius: 12px; padding: 14px; }
        .meta-lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); margin-bottom: 6px; }
        .meta-val { font-size: 14px; font-weight: 700; color: var(--tx2); }

        @media (max-width: 900px) {
            .meta-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-col { margin-left: 0; }
            .certificate-card { padding: 22px; }
            .certificate-inner { padding: 22px; }
            .meta-grid { grid-template-columns: 1fr; }
            .cert-title { font-size: 28px; }
            .cert-name { font-size: 24px; }
        }
    </style>
</head>
<body>
<div style="display:flex;flex:1;min-height:100vh;">
    <aside class="sidebar">
        @include('partials.app.sidebar-brand')
        <nav class="sb-nav">
            @include('partials.app.nav-links', [
                'activeKey' => 'certificates',
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
            <span class="tb-title">{{ __('certificates.view_certificate') }}</span>
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
            <div class="page-head">
                <div class="page-title">{{ __('certificates.issued_certificate') }}</div>
                <div class="btn-row">
                    <a href="{{ route('certificates.index') }}" class="btn-sec">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        {{ __('certificates.back_to_certificates') }}
                    </a>
                    <a href="{{ route('certificates.download', $certificate) }}" class="btn-sec">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                        {{ __('lessons.download') }}
                    </a>
                    @if($certificate->lesson)
                        <a href="{{ route('lessons.show', $certificate->lesson) }}" class="btn-cta">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            {{ __('certificates.open_lesson') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="certificate-shell">
                <div class="certificate-card">
                    <div class="certificate-inner">
                        <div class="cert-kicker">{{ __('certificates.certificate_of_completion') }}</div>
                        <div class="cert-title">{{ __('certificates.certificate_of_completion') }}</div>
                        <div class="cert-copy">{{ __('certificates.awarded_to') }}</div>
                        <div class="cert-name">{{ $learnerName }}</div>
                        <div class="cert-copy">{{ __('certificates.for_lesson') }}</div>
                        <div class="cert-lesson">{{ $lessonTitle }}</div>
                        <div class="cert-copy">{{ $examTitle }}</div>
                        <div class="cert-copy">{{ __('certificates.issued_by') }}: {{ $issuerName }}</div>

                        <div class="meta-grid">
                            <div class="meta-box">
                                <div class="meta-lbl">{{ __('certificates.score') }}</div>
                                <div class="meta-val">{{ $score !== null ? $score . '%' : __('certificates.not_available') }}</div>
                            </div>
                            <div class="meta-box">
                                <div class="meta-lbl">{{ __('certificates.issued_on') }}</div>
                                <div class="meta-val">{{ optional($certificate->issued_at)->format('Y-m-d') }}</div>
                            </div>
                            <div class="meta-box">
                                <div class="meta-lbl">{{ __('certificates.issued_by') }}</div>
                                <div class="meta-val">{{ $issuerName }}</div>
                            </div>
                            <div class="meta-box">
                                <div class="meta-lbl">{{ __('certificates.certificate_code') }}</div>
                                <div class="meta-val">{{ $certificate->certificate_code }}</div>
                            </div>
                            @if($isManaging ?? false)
                                <div class="meta-box">
                                    <div class="meta-lbl">{{ __('certificates.validated_on') }}</div>
                                    <div class="meta-val">{{ optional($certificate->validated_at)->format('Y-m-d H:i') ?? __('certificates.not_available') }}</div>
                                </div>
                                <div class="meta-box">
                                    <div class="meta-lbl">{{ __('certificates.validation_notes') }}</div>
                                    <div class="meta-val">{{ $certificate->validation_notes ?: __('certificates.no_validation_notes') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.app.settings-panel')
</body>
</html>
