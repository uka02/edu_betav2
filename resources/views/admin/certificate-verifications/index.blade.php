<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title>{{ __('certificates.admin_queue_title') }} - EduDev</title>
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
            --green: #2fb777; --green-soft: rgba(47,183,119,0.10);
            --amber: #f5a623; --amber-soft: rgba(245,166,35,0.10);
            --red: #f05050; --red-soft: rgba(240,80,80,0.10);
            --sidebar-w: 252px;
        }
        [data-theme="light"] {
            --bg: #f0f4f8; --surface: #ffffff; --s1: #ffffff; --s2: #f0f4f8; --s3: #e2e8f0;
            --b0: rgba(56,139,220,0.10); --b1: rgba(56,139,220,0.18); --b2: rgba(56,139,220,0.30);
            --tx: #0f172a; --tx2: #334155; --muted: #64748b;
            --blue: #2563eb; --blue-dim: #1d4ed8; --blue-soft: rgba(37,99,235,0.08); --blue-glow: rgba(37,99,235,0.15);
            --green: #15803d; --green-soft: rgba(21,128,61,0.10);
            --amber: #d97706; --amber-soft: rgba(217,119,6,0.10);
            --red: #dc2626; --red-soft: rgba(220,38,38,0.08);
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
        .page-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
        .page-title { font-size: 24px; font-weight: 800; letter-spacing: -.05em; }
        .page-sub { color: var(--muted); font-size: 13px; margin-top: 6px; line-height: 1.6; max-width: 800px; }
        .alert { border-radius: 12px; padding: 14px 16px; margin-bottom: 16px; font-size: 13px; border: 1px solid transparent; }
        .alert-success { background: var(--green-soft); border-color: rgba(47,183,119,.18); color: var(--green); }
        .stat-strip { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 20px; }
        .stat-card { background: var(--s1); border: 1px solid var(--b0); border-radius: 14px; padding: 16px; }
        .stat-label { font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
        .stat-value { font-size: 30px; font-weight: 800; letter-spacing: -.05em; }
        .filters { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; }
        .search-form { display: flex; gap: 8px; flex: 1; min-width: 280px; }
        .search-form input, .review-notes {
            width: 100%; border-radius: 10px; border: 1px solid var(--b1); background: var(--s2); color: var(--tx);
            padding: 11px 12px; font: inherit;
        }
        .search-form button, .btn-sec, .btn-cta, .btn-danger {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 14px; border-radius: 10px;
            font-size: 12.5px; font-weight: 600; text-decoration: none; border: 1px solid var(--b1); cursor: pointer; transition: all .15s;
        }
        .btn-sec { background: var(--s1); color: var(--tx2); }
        .btn-sec:hover { background: var(--s2); color: var(--tx); border-color: var(--b2); }
        .btn-cta { background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: #fff; border: none; box-shadow: 0 4px 14px var(--blue-glow); }
        .btn-cta:hover { opacity: .92; transform: translateY(-1px); }
        .btn-danger { background: transparent; color: var(--red); border-color: rgba(240,80,80,.24); }
        .btn-danger:hover { background: var(--red-soft); }
        .status-row { display: flex; gap: 8px; flex-wrap: wrap; }
        .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 11px; border-radius: 999px; border: 1px solid var(--b1); color: var(--tx2); text-decoration: none; font-size: 12px; font-weight: 700; }
        .status-pill.active { background: var(--blue-soft); color: var(--blue); border-color: rgba(59,158,255,.26); }
        .queue-grid { display: grid; gap: 14px; }
        .request-card { background: var(--s1); border: 1px solid var(--b0); border-radius: 16px; padding: 18px; }
        .request-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
        .request-title { font-size: 16px; font-weight: 700; line-height: 1.35; }
        .request-sub { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .status-badge { display: inline-flex; align-items: center; padding: 4px 9px; border-radius: 999px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; }
        .status-badge.pending { background: var(--amber-soft); color: var(--amber); }
        .status-badge.approved { background: var(--green-soft); color: var(--green); }
        .status-badge.rejected { background: var(--red-soft); color: var(--red); }
        .meta-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin-bottom: 14px; }
        .meta-box { background: var(--s2); border: 1px solid var(--b0); border-radius: 10px; padding: 10px 12px; }
        .meta-lbl { font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; }
        .meta-val { font-size: 13px; color: var(--tx2); line-height: 1.5; word-break: break-word; }
        .notes-block { background: color-mix(in srgb, var(--s2) 88%, transparent); border: 1px dashed var(--b1); border-radius: 10px; padding: 12px; font-size: 12px; color: var(--tx2); line-height: 1.6; margin-bottom: 14px; }
        .review-form { display: grid; grid-template-columns: 1fr auto auto auto; gap: 8px; align-items: start; }
        .empty { background: var(--s1); border: 1px dashed var(--b2); border-radius: 16px; padding: 44px 24px; text-align: center; color: var(--muted); }
        .pagination-wrap { margin-top: 20px; display: flex; justify-content: center; }
        @media (max-width: 1100px) { .meta-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .review-form { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-col { margin-left: 0; }
            .content { padding: 18px; }
            .stat-strip, .meta-grid { grid-template-columns: 1fr; }
            .search-form { flex-direction: column; }
        }
    </style>
</head>
<body>
<div style="display:flex;flex:1;min-height:100vh;">
    <aside class="sidebar">
        @include('partials.app.sidebar-brand')
        <nav class="sb-nav">
            @include('partials.app.nav-links', [
                'activeKey' => 'admin-verifications',
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
            <span class="tb-title">{{ __('certificates.admin_queue_title') }}</span>
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
                    <div class="page-title">{{ __('certificates.admin_queue_title') }}</div>
                    <div class="page-sub">{{ __('certificates.admin_queue_description') }}</div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="stat-strip">
                <div class="stat-card">
                    <div class="stat-label">{{ __('certificates.pending_requests') }}</div>
                    <div class="stat-value">{{ $pendingCount }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">{{ __('certificates.approved_requests') }}</div>
                    <div class="stat-value">{{ $approvedCount }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">{{ __('certificates.rejected_requests') }}</div>
                    <div class="stat-value">{{ $rejectedCount }}</div>
                </div>
            </div>

            <div class="filters">
                <form method="GET" action="{{ route('admin.certificate-verifications.index') }}" class="search-form">
                    <input type="search" name="search" value="{{ $search }}" placeholder="{{ __('certificates.admin_search_placeholder') }}">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <button type="submit" class="btn-sec">{{ __('certificates.search') }}</button>
                </form>

                <div class="status-row">
                    @foreach(['pending', 'approved', 'rejected', 'all'] as $statusOption)
                        <a href="{{ route('admin.certificate-verifications.index', ['status' => $statusOption, 'search' => $search]) }}" class="status-pill {{ $status === $statusOption ? 'active' : '' }}">
                            {{ __('certificates.status_' . $statusOption) }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if($requests->count() > 0)
                <div class="queue-grid">
                    @foreach($requests as $verification)
                        <article class="request-card">
                            <div class="request-top">
                                <div>
                                    <div class="request-title">{{ $verification->title }}</div>
                                    <div class="request-sub">{{ $verification->lesson?->title ?? __('certificates.not_available') }}</div>
                                </div>
                                <span class="status-badge {{ $verification->status }}">{{ __('certificates.status_' . $verification->status) }}</span>
                            </div>

                            <div class="meta-grid">
                                <div class="meta-box">
                                    <div class="meta-lbl">{{ __('certificates.requested_by') }}</div>
                                    <div class="meta-val">{{ $verification->user?->name }}<br>{{ $verification->user?->email }}</div>
                                </div>
                                <div class="meta-box">
                                    <div class="meta-lbl">{{ __('certificates.lesson') }}</div>
                                    <div class="meta-val">{{ $verification->lesson?->title ?? __('certificates.not_available') }}</div>
                                </div>
                                <div class="meta-box">
                                    <div class="meta-lbl">{{ __('certificates.passing_score') }}</div>
                                    <div class="meta-val">{{ $verification->passing_score !== null ? $verification->passing_score . '%' : __('certificates.not_available') }}</div>
                                </div>
                                <div class="meta-box">
                                    <div class="meta-lbl">{{ __('certificates.uploaded_file') }}</div>
                                    <div class="meta-val">{{ $verification->original_filename }}</div>
                                </div>
                                <div class="meta-box">
                                    <div class="meta-lbl">{{ __('certificates.uploaded_on') }}</div>
                                    <div class="meta-val">{{ optional($verification->created_at)->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="meta-box">
                                    <div class="meta-lbl">{{ __('certificates.reviewed_by') }}</div>
                                    <div class="meta-val">{{ $verification->reviewer?->name ?? __('certificates.not_available') }}</div>
                                </div>
                            </div>

                            @if($verification->notes)
                                <div class="notes-block">
                                    <strong>{{ __('certificates.user_notes') }}:</strong><br>
                                    {{ $verification->notes }}
                                </div>
                            @endif

                            @if($verification->review_notes)
                                <div class="notes-block">
                                    <strong>{{ __('certificates.review_notes') }}:</strong><br>
                                    {{ $verification->review_notes }}
                                </div>
                            @endif

                            <div class="review-form">
                                <form method="POST" action="{{ route('admin.certificate-verifications.approve', $verification) }}" style="display:contents;">
                                    @csrf
                                    @method('PATCH')
                                    <input class="review-notes" type="text" name="review_notes" value="{{ $verification->review_notes }}" placeholder="{{ __('certificates.review_notes_placeholder') }}">
                                    <a href="{{ route('certificate-verifications.download', $verification) }}" class="btn-sec">{{ __('certificates.view_file') }}</a>
                                    <button type="submit" class="btn-cta">{{ __('certificates.approve') }}</button>
                                </form>

                                <form method="POST" action="{{ route('admin.certificate-verifications.reject', $verification) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="review_notes" value="{{ $verification->review_notes }}">
                                    <button type="submit" class="btn-danger">{{ __('certificates.reject') }}</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="empty">{{ __('certificates.no_verification_requests') }}</div>
            @endif
        </div>
    </div>
</div>

@include('partials.app.settings-panel')
</body>
</html>
