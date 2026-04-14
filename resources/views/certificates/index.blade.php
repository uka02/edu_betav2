<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title>{{ __('certificates.title') }} — EduDev</title>
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
            --amber: #f5a623; --amber-soft: rgba(245,166,35,0.08);
            --sidebar-w: 252px; --r: 10px; --rl: 14px;
        }

        [data-theme="light"] {
            --bg: #f0f4f8; --surface: #ffffff; --s1: #ffffff; --s2: #f0f4f8; --s3: #e2e8f0;
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
        .page-sub { color: var(--muted); font-size: 13px; margin-top: 5px; }
        .btn-row { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-cta, .btn-sec { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: all .15s; }
        .btn-cta { background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: white; box-shadow: 0 4px 14px var(--blue-glow); }
        .btn-cta:hover { opacity: .9; transform: translateY(-1px); }
        .btn-sec { background: var(--s1); color: var(--tx2); border: 1px solid var(--b1); }
        .btn-sec:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }
        .stat-strip { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
        .stat-chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; background: var(--blue-soft); border: 1px solid rgba(59,158,255,.2); color: var(--blue); font-size: 12px; font-weight: 700; }
        .cert-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 14px; }
        .cert-card { background: var(--s1); border: 1px solid var(--b0); border-radius: var(--rl); padding: 18px; display: flex; flex-direction: column; gap: 14px; transition: all .2s cubic-bezier(.16,1,.3,1); }
        .cert-card:hover { border-color: var(--b2); transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,.18), 0 0 0 1px rgba(59,158,255,.08); }
        .cert-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
        .cert-title { font-size: 15px; font-weight: 700; letter-spacing: -.02em; color: var(--tx); line-height: 1.35; }
        .cert-sub { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .cert-code { display: inline-flex; align-items: center; padding: 4px 8px; border-radius: 999px; background: var(--amber-soft); color: var(--amber); font-size: 10px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
        .cert-meta { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .meta-box { background: var(--s2); border: 1px solid var(--b0); border-radius: 10px; padding: 10px 12px; }
        .meta-lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); margin-bottom: 4px; }
        .meta-val { font-size: 13px; font-weight: 600; color: var(--tx2); }
        .cert-actions { display: flex; gap: 8px; margin-top: auto; flex-wrap: wrap; }
        .cert-actions .btn-sec, .cert-actions .btn-cta { flex: 1 1 140px; justify-content: center; }
        .empty { background: var(--s1); border: 1px dashed var(--b2); border-radius: var(--rl); padding: 60px 24px; text-align: center; }
        .empty-ico { width: 52px; height: 52px; border-radius: 12px; background: var(--s2); display: grid; place-items: center; margin: 0 auto 16px; color: var(--muted); }
        .empty-ttl { font-size: 16px; font-weight: 700; color: var(--tx2); margin-bottom: 6px; }
        .empty-sub { font-size: 13px; color: var(--muted); margin-bottom: 20px; }
        .pagination-wrap { margin-top: 24px; display: flex; justify-content: center; }
        .pagination-wrap nav { display: flex; gap: 4px; }
        .pagination-wrap .page-item .page-link { display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 7px; border: 1px solid var(--b1); background: var(--s1); color: var(--tx2); text-decoration: none; font-size: 13px; font-weight: 500; transition: all .15s; }
        .pagination-wrap .page-item.active .page-link { background: var(--blue-soft); border-color: rgba(59,158,255,.3); color: var(--blue); }
        .pagination-wrap .page-item .page-link:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }
        .alert { border-radius: 12px; padding: 14px 16px; margin-bottom: 16px; font-size: 13px; border: 1px solid transparent; }
        .alert-success { background: rgba(47,183,119,.12); border-color: rgba(47,183,119,.18); color: #4ade80; }
        .alert-danger { background: rgba(240,80,80,.10); border-color: rgba(240,80,80,.18); color: #fca5a5; }
        .alert ul { padding-left: 18px; }
        .section-card { background: var(--s1); border: 1px solid var(--b0); border-radius: var(--rl); padding: 18px; margin-bottom: 18px; }
        .section-title { font-size: 16px; font-weight: 700; color: var(--tx); letter-spacing: -.03em; }
        .section-copy { font-size: 12.5px; color: var(--muted); line-height: 1.6; margin-top: 6px; }
        .upload-grid { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(280px, .9fr); gap: 16px; margin-top: 18px; }
        .upload-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .field { display: flex; flex-direction: column; gap: 7px; }
        .field.full { grid-column: 1 / -1; }
        .field label { font-size: 12px; font-weight: 700; color: var(--tx2); }
        .field input, .field textarea {
            width: 100%; border-radius: 10px; border: 1px solid var(--b1); background: var(--s2); color: var(--tx);
            padding: 11px 12px; font: inherit;
        }
        .field textarea { min-height: 110px; resize: vertical; }
        .field-error { font-size: 11px; color: #fca5a5; }
        .upload-actions { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 4px; }
        .upload-actions small { font-size: 11px; color: var(--muted); line-height: 1.5; }
        .verify-stats { display: grid; gap: 10px; }
        .verify-stat { background: var(--s2); border: 1px solid var(--b0); border-radius: 12px; padding: 14px; }
        .verify-stat-label { font-size: 10px; font-weight: 700; color: var(--muted); letter-spacing: .08em; text-transform: uppercase; margin-bottom: 7px; }
        .verify-stat-value { font-size: 24px; font-weight: 800; letter-spacing: -.04em; color: var(--tx); }
        .verify-stat-note { font-size: 12px; color: var(--tx2); line-height: 1.5; margin-top: 6px; }
        .verification-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 14px; margin-top: 16px; }
        .verification-card { background: var(--s1); border: 1px solid var(--b0); border-radius: var(--rl); padding: 18px; display: flex; flex-direction: column; gap: 14px; }
        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 9px; border-radius: 999px; font-size: 10px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
        .status-badge.pending { background: rgba(245,166,35,.10); color: var(--amber); }
        .status-badge.approved { background: rgba(47,183,119,.12); color: #4ade80; }
        .status-badge.rejected { background: rgba(240,80,80,.10); color: #f87171; }
        .verification-notes { border-radius: 10px; background: color-mix(in srgb, var(--s2) 88%, transparent); border: 1px dashed var(--b1); padding: 12px; font-size: 12px; color: var(--tx2); line-height: 1.6; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-col { margin-left: 0; }
            .cert-grid, .cert-meta { grid-template-columns: 1fr; }
            .upload-grid, .upload-form, .verification-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
@php
    $showVerificationWorkflow = $showVerificationWorkflow ?? true;
    $verificationRequests = $verificationRequests ?? collect();
    $pendingVerificationCount = $verificationRequests->where('status', 'pending')->count();
    $approvedVerificationCount = $verificationRequests->where('status', 'approved')->count();
    $rejectedVerificationCount = $verificationRequests->where('status', 'rejected')->count();
@endphp
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
            <span class="tb-title">{{ __('certificates.title') }}</span>
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
                <div>
                    <div class="page-title">{{ __('certificates.earned_certificates') }}</div>
                    <div class="page-sub">{{ __('certificates.earned_certificates_description') }}</div>
                </div>
                <div class="btn-row">
                    <a href="{{ route('dashboard') }}" class="btn-sec">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        {{ __('certificates.back_to_dashboard') }}
                    </a>
                    <a href="{{ route('lessons.index') }}" class="btn-cta">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        {{ __('certificates.browse_lessons') }}
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($showVerificationWorkflow)
            <section class="section-card">
                <div class="section-title">{{ __('certificates.upload_for_verification') }}</div>
                <div class="section-copy">{{ __('certificates.upload_for_verification_description') }}</div>

                <div class="upload-grid">
                    <form action="{{ route('certificate-verifications.store') }}" method="POST" enctype="multipart/form-data" class="upload-form">
                        @csrf

                        <div class="field">
                            <label for="verificationTitle">{{ __('certificates.certificate_title') }}</label>
                            <input id="verificationTitle" type="text" name="title" value="{{ old('title') }}" placeholder="{{ __('certificates.certificate_title_placeholder') }}">
                            @error('title') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="issuerName">{{ __('certificates.issuer_name') }}</label>
                            <input id="issuerName" type="text" name="issuer_name" value="{{ old('issuer_name') }}" placeholder="{{ __('certificates.issuer_name_placeholder') }}">
                            @error('issuer_name') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field full">
                            <label for="document">{{ __('certificates.certificate_pdf') }}</label>
                            <input id="document" type="file" name="document" accept="application/pdf">
                            @error('document') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field full">
                            <label for="verificationNotes">{{ __('certificates.notes') }}</label>
                            <textarea id="verificationNotes" name="notes" placeholder="{{ __('certificates.notes_placeholder') }}">{{ old('notes') }}</textarea>
                            @error('notes') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field full">
                            <div class="upload-actions">
                                <small>{{ __('certificates.verification_flow_note') }}</small>
                                <button type="submit" class="btn-cta">{{ __('certificates.submit_for_verification') }}</button>
                            </div>
                        </div>
                    </form>

                    <div class="verify-stats">
                        <div class="verify-stat">
                            <div class="verify-stat-label">{{ __('certificates.pending_requests') }}</div>
                            <div class="verify-stat-value">{{ $pendingVerificationCount }}</div>
                            <div class="verify-stat-note">{{ __('certificates.pending_requests_note') }}</div>
                        </div>
                        <div class="verify-stat">
                            <div class="verify-stat-label">{{ __('certificates.approved_requests') }}</div>
                            <div class="verify-stat-value">{{ $approvedVerificationCount }}</div>
                            <div class="verify-stat-note">{{ __('certificates.approved_requests_note') }}</div>
                        </div>
                        <div class="verify-stat">
                            <div class="verify-stat-label">{{ __('certificates.rejected_requests') }}</div>
                            <div class="verify-stat-value">{{ $rejectedVerificationCount }}</div>
                            <div class="verify-stat-note">{{ __('certificates.rejected_requests_note') }}</div>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            @if($certificates->count() > 0)
                <div class="stat-strip">
                    <div class="stat-chip">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                        {{ $certificates->total() }} {{ __('dashboard.certificates') }}
                    </div>
                </div>

                <div class="cert-grid">
                    @foreach($certificates as $certificate)
                        @php
                            $lessonTitle = $certificate->displayLessonTitle();
                            $issuerName = $certificate->displayIssuerName();
                            $score = $certificate->displayScore();
                            $examTitle = $certificate->displayExamTitle();
                        @endphp
                        <div class="cert-card">
                            <div class="cert-top">
                                <div>
                                    <div class="cert-title">{{ $lessonTitle }}</div>
                                    <div class="cert-sub">{{ $examTitle }}</div>
                                </div>
                                <div class="cert-code">{{ $certificate->certificate_code }}</div>
                            </div>

                            <div class="cert-meta">
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
                            </div>

                            <div class="cert-actions">
                                <a href="{{ route('certificates.show', $certificate) }}" class="btn-cta">{{ __('certificates.view_certificate') }}</a>
                                <a href="{{ route('certificates.download', $certificate) }}" class="btn-sec">{{ __('lessons.download') }}</a>
                                @if($certificate->lesson)
                                    <a href="{{ route('lessons.show', $certificate->lesson) }}" class="btn-sec">{{ __('certificates.open_lesson') }}</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $certificates->links() }}
                </div>
            @else
                <div class="empty">
                    <div class="empty-ico">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                    </div>
                    <div class="empty-ttl">{{ __('certificates.no_certificates') }}</div>
                    <div class="empty-sub">{{ __('certificates.no_certificates_description') }}</div>
                    <a href="{{ route('lessons.index') }}" class="btn-cta">{{ __('certificates.browse_lessons') }}</a>
                </div>
            @endif

            @if($showVerificationWorkflow)
            <section class="section-card">
                <div class="section-title">{{ __('certificates.verification_requests') }}</div>
                <div class="section-copy">{{ __('certificates.verification_requests_description') }}</div>

                @if($verificationRequests->count() > 0)
                    <div class="verification-grid">
                        @foreach($verificationRequests as $verification)
                            <article class="verification-card">
                                <div class="cert-top">
                                    <div>
                                        <div class="cert-title">{{ $verification->title }}</div>
                                        <div class="cert-sub">{{ $verification->issuer_name ?: __('certificates.not_available') }}</div>
                                    </div>
                                    <span class="status-badge {{ $verification->status }}">{{ __('certificates.status_' . $verification->status) }}</span>
                                </div>

                                <div class="cert-meta">
                                    <div class="meta-box">
                                        <div class="meta-lbl">{{ __('certificates.uploaded_on') }}</div>
                                        <div class="meta-val">{{ optional($verification->created_at)->format('Y-m-d') }}</div>
                                    </div>
                                    <div class="meta-box">
                                        <div class="meta-lbl">{{ __('certificates.reviewed_on') }}</div>
                                        <div class="meta-val">{{ optional($verification->reviewed_at)->format('Y-m-d') ?? __('certificates.not_available') }}</div>
                                    </div>
                                    <div class="meta-box">
                                        <div class="meta-lbl">{{ __('certificates.uploaded_file') }}</div>
                                        <div class="meta-val">{{ $verification->original_filename }}</div>
                                    </div>
                                    <div class="meta-box">
                                        <div class="meta-lbl">{{ __('certificates.reviewed_by') }}</div>
                                        <div class="meta-val">{{ $verification->reviewer?->name ?? __('certificates.not_available') }}</div>
                                    </div>
                                </div>

                                @if($verification->notes)
                                    <div class="verification-notes">
                                        <strong>{{ __('certificates.user_notes') }}:</strong><br>
                                        {{ $verification->notes }}
                                    </div>
                                @endif

                                @if($verification->review_notes)
                                    <div class="verification-notes">
                                        <strong>{{ __('certificates.review_notes') }}:</strong><br>
                                        {{ $verification->review_notes }}
                                    </div>
                                @endif

                                <div class="cert-actions">
                                    <a href="{{ route('certificate-verifications.download', $verification) }}" class="btn-sec">{{ __('certificates.view_pdf') }}</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="empty" style="margin-top:16px;">
                        <div class="empty-ttl">{{ __('certificates.no_verification_requests_user') }}</div>
                        <div class="empty-sub">{{ __('certificates.no_verification_requests_user_description') }}</div>
                    </div>
                @endif
            </section>
            @endif
        </div>
    </div>
</div>

@include('partials.app.settings-panel')
</body>
</html>
