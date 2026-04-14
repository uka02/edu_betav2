<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title>{{ __('certificates.educator_title') }} - EduDev</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #060c12; --surface: #0b1520; --s1: #0f1e2e; --s2: #152438;
            --b0: rgba(56,139,220,0.07); --b1: rgba(56,139,220,0.13); --b2: rgba(56,139,220,0.22);
            --tx: #e8f0f8; --tx2: #a8bdd0; --muted: #587089;
            --blue: #3b9eff; --blue-dim: #1e6fc4; --blue-soft: rgba(59,158,255,0.08);
            --green: #2fb777; --green-soft: rgba(47,183,119,0.12);
            --amber: #f5a623; --amber-soft: rgba(245,166,35,0.10);
            --red: #f05050; --red-soft: rgba(240,80,80,0.10);
            --sidebar-w: 252px;
        }
        [data-theme="light"] {
            --bg: #f0f4f8; --surface: #ffffff; --s1: #ffffff; --s2: #f0f4f8;
            --b0: rgba(56,139,220,0.10); --b1: rgba(56,139,220,0.18); --b2: rgba(56,139,220,0.30);
            --tx: #0f172a; --tx2: #334155; --muted: #64748b;
            --blue: #2563eb; --blue-dim: #1d4ed8; --blue-soft: rgba(37,99,235,0.08);
            --green: #15803d; --green-soft: rgba(21,128,61,0.10);
            --amber: #d97706; --amber-soft: rgba(217,119,6,0.10);
            --red: #dc2626; --red-soft: rgba(220,38,38,0.08);
        }
        body { font-family: "Roboto", sans-serif; background: var(--bg); color: var(--tx); min-height: 100vh; }
        .shell { display: flex; min-height: 100vh; }
        .sidebar { width: var(--sidebar-w); position: fixed; inset: 0 auto 0 0; background: var(--surface); border-right: 1px solid var(--b0); display: flex; flex-direction: column; }
        .sb-nav { flex: 1; overflow-y: auto; padding: 10px 8px; }
        .sb-foot { padding: 8px; border-top: 1px solid var(--b0); }
        .main { margin-left: var(--sidebar-w); flex: 1; min-width: 0; }
        .topbar { height: 56px; display: flex; align-items: center; gap: 10px; padding: 0 24px; border-bottom: 1px solid var(--b0); background: color-mix(in srgb, var(--bg) 85%, transparent); position: sticky; top: 0; z-index: 20; }
        .tb-title { font-size: 14px; font-weight: 700; color: var(--tx2); }
        .tb-sep { width: 1px; height: 14px; background: var(--b1); }
        .tb-right { margin-left: auto; display: flex; gap: 8px; }
        .tb-btn { width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--b1); background: var(--s1); color: var(--tx2); display: grid; place-items: center; cursor: pointer; }
        .content { padding: 24px; }
        .page-head { display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; flex-wrap: wrap; margin-bottom: 24px; }
        .page-title { font-size: 24px; font-weight: 800; letter-spacing: -.04em; }
        .page-sub { color: var(--muted); font-size: 13px; line-height: 1.6; margin-top: 6px; max-width: 760px; }
        .btn-row { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-cta, .btn-sec { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 14px; border-radius: 9px; font-size: 12.5px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .btn-cta { border: none; background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: #fff; }
        .btn-sec { border: 1px solid var(--b1); background: var(--s1); color: var(--tx2); }
        .alert { border-radius: 12px; padding: 14px 16px; margin-bottom: 16px; font-size: 13px; line-height: 1.6; border: 1px solid transparent; }
        .alert-success { background: var(--green-soft); border-color: rgba(47,183,119,.18); color: var(--green); }
        .alert-danger { background: var(--red-soft); border-color: rgba(240,80,80,.18); color: #fda4af; }
        .alert ul { padding-left: 18px; }
        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 18px; }
        .card { background: var(--s1); border: 1px solid var(--b0); border-radius: 14px; padding: 16px; }
        .stat-label, .meta-lbl { font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
        .stat-value { font-size: 28px; font-weight: 800; }
        .stat-note, .section-copy { font-size: 12px; color: var(--tx2); line-height: 1.6; }
        .section { margin-bottom: 16px; }
        .section-title { font-size: 16px; font-weight: 700; letter-spacing: -.02em; }
        .helper { margin-top: 12px; padding: 12px 14px; border-radius: 12px; background: var(--blue-soft); border: 1px solid rgba(59,158,255,.18); color: var(--tx2); font-size: 12.5px; line-height: 1.6; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 14px; margin-top: 16px; }
        .top { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
        .title { font-size: 15px; font-weight: 700; line-height: 1.35; }
        .sub { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .badge { display: inline-flex; align-items: center; padding: 4px 9px; border-radius: 999px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; }
        .badge.pending { background: var(--amber-soft); color: var(--amber); }
        .badge.approved { background: var(--green-soft); color: var(--green); }
        .badge.rejected { background: var(--red-soft); color: var(--red); }
        .meta-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .meta-box { background: var(--s2); border: 1px solid var(--b0); border-radius: 10px; padding: 10px 12px; }
        .meta-val { font-size: 13px; color: var(--tx2); line-height: 1.5; word-break: break-word; }
        .notes { padding: 12px; border-radius: 10px; border: 1px dashed var(--b1); background: color-mix(in srgb, var(--s2) 88%, transparent); font-size: 12px; line-height: 1.6; color: var(--tx2); }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: auto; }
        .actions > * { flex: 1; }
        .search-row { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .search-form { display: flex; gap: 8px; flex: 1; min-width: 280px; }
        .field { display: flex; flex-direction: column; gap: 7px; }
        .field label { font-size: 12px; font-weight: 700; color: var(--tx2); }
        .field input, .field select, .field textarea, .search-form input { width: 100%; border-radius: 10px; border: 1px solid var(--b1); background: var(--s2); color: var(--tx); padding: 11px 12px; font: inherit; }
        .field textarea { min-height: 120px; resize: vertical; }
        .field small { font-size: 11px; color: var(--muted); line-height: 1.5; }
        .field-error { font-size: 11px; color: #fda4af; }
        .empty { text-align: center; padding: 42px 24px; border: 1px dashed var(--b2); border-radius: 14px; background: var(--s1); }
        .empty-ttl { font-size: 16px; font-weight: 700; color: var(--tx2); margin-bottom: 8px; }
        .empty-sub { font-size: 13px; color: var(--muted); line-height: 1.6; }
        .pagination-wrap { margin-top: 20px; display: flex; justify-content: center; }
        .modal { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; padding: 20px; background: rgba(6,12,18,.72); z-index: 100; }
        .modal.active { display: flex; }
        .modal-card { width: min(720px, 100%); background: var(--surface); border: 1px solid var(--b1); border-radius: 16px; padding: 20px; }
        .modal-head { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; margin-bottom: 16px; }
        .modal-title { font-size: 20px; font-weight: 800; }
        .modal-sub { color: var(--muted); font-size: 13px; line-height: 1.6; margin-top: 6px; }
        .modal-close { width: 34px; height: 34px; border-radius: 10px; border: 1px solid var(--b1); background: var(--s1); color: var(--tx2); cursor: pointer; }
        .request-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .full { grid-column: 1 / -1; }
        .preview { padding: 14px; border-radius: 12px; border: 1px solid var(--b0); background: var(--s1); }
        .preview-line { display: flex; justify-content: space-between; gap: 12px; font-size: 12px; color: var(--tx2); }
        .preview-line + .preview-line { margin-top: 10px; }
        .preview strong { color: var(--tx); }
        .code { display: inline-flex; align-items: center; padding: 4px 8px; border-radius: 999px; background: var(--amber-soft); color: var(--amber); font-size: 10px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; }
            .stats, .meta-grid, .request-form { grid-template-columns: 1fr; }
            .search-form, .actions { flex-direction: column; }
        }
    </style>
</head>
<body>
@php
    $lessonOptions = collect($lessonOptions ?? []);
    $lessonOptionMap = $lessonOptions->keyBy('id');
    $certificationRequests = $certificationRequests ?? collect();
    $selectedLessonId = (int) old('lesson_id', 0);
@endphp
<div class="shell">
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

    <div class="main">
        <header class="topbar">
            <span class="tb-title">{{ __('certificates.educator_title') }}</span>
            <div class="tb-sep"></div>
            <div class="tb-right">
                @include('partials.app.settings-button', ['buttonClass' => 'tb-btn', 'buttonId' => 'settingsBtn2', 'title' => __('dashboard.settings')])
                @include('partials.app.logout-button')
            </div>
        </header>

        <div class="content">
            <div class="page-head">
                <div>
                    <div class="page-title">{{ __('certificates.educator_title') }}</div>
                    <div class="page-sub">{{ __('certificates.educator_description') }}</div>
                </div>
                <div class="btn-row">
                    <a href="{{ route('dashboard') }}" class="btn-sec">{{ __('certificates.back_to_dashboard') }}</a>
                    <a href="{{ route('lessons.index') }}" class="btn-sec">{{ __('certificates.open_lessons_workspace') }}</a>
                    <button type="button" class="btn-cta" id="openRequestModal" @disabled($lessonOptions->isEmpty())>{{ __('certificates.upload_for_verification') }}</button>
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

            @if($lessonOptions->isEmpty())
                <div class="alert alert-danger" style="margin-bottom: 16px;">
                    <strong>{{ __('certificates.no_lessons_with_exams_title') }}</strong><br>
                    {{ __('certificates.no_lessons_with_exams_description') }}
                    <a href="{{ route('lessons.index') }}" style="color: inherit; text-decoration: underline;">{{ __('certificates.edit_lessons') }}</a>
                </div>
            @endif

            <div class="stats">
                <div class="card">
                    <div class="stat-label">{{ __('certificates.total_managed') }}</div>
                    <div class="stat-value">{{ $certificateCount }}</div>
                    <div class="stat-note">{{ __('certificates.total_managed_note') }}</div>
                </div>
                <div class="card">
                    <div class="stat-label">{{ __('certificates.learners_recognized') }}</div>
                    <div class="stat-value">{{ $managedLearnerCount }}</div>
                    <div class="stat-note">{{ __('certificates.learners_recognized_note') }}</div>
                </div>
                <div class="card">
                    <div class="stat-label">{{ __('certificates.lessons_with_final_exam') }}</div>
                    <div class="stat-value">{{ $requestableLessonCount }}</div>
                    <div class="stat-note">{{ __('certificates.lessons_with_final_exam_note') }}</div>
                </div>
                <div class="card">
                    <div class="stat-label">{{ __('certificates.approved_requests') }}</div>
                    <div class="stat-value">{{ $approvedRequestCount }}</div>
                    <div class="stat-note">{{ __('certificates.approved_requests_note') }}</div>
                </div>
            </div>

            <section class="card section">
                <div class="section-title">{{ __('certificates.verification_requests') }}</div>
                <div class="section-copy">{{ __('certificates.verification_requests_description') }}</div>
                <div class="helper">{{ __('certificates.verification_flow_note') }}</div>

                @if($certificationRequests->isNotEmpty())
                    <div class="grid">
                        @foreach($certificationRequests as $verification)
                            @php $lessonMeta = $lessonOptionMap->get($verification->lesson_id); @endphp
                            <article class="card">
                                <div class="top">
                                    <div>
                                        <div class="title">{{ $verification->title }}</div>
                                        <div class="sub">{{ $verification->lesson?->title ?? __('certificates.not_available') }}</div>
                                    </div>
                                    <span class="badge {{ $verification->status }}">{{ __('certificates.status_' . $verification->status) }}</span>
                                </div>

                                <div class="meta-grid">
                                    <div class="meta-box">
                                        <div class="meta-lbl">{{ __('certificates.final_exam_label') }}</div>
                                        <div class="meta-val">{{ $lessonMeta['final_exam_title'] ?? __('certificates.not_available') }}</div>
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
                                        <div class="meta-lbl">{{ __('certificates.reviewed_by') }}</div>
                                        <div class="meta-val">{{ $verification->reviewer?->name ?? __('certificates.not_available') }}</div>
                                    </div>
                                </div>

                                @if($verification->notes)
                                    <div class="notes"><strong>{{ __('certificates.notes') }}:</strong><br>{{ $verification->notes }}</div>
                                @endif

                                @if($verification->review_notes)
                                    <div class="notes"><strong>{{ __('certificates.review_notes') }}:</strong><br>{{ $verification->review_notes }}</div>
                                @endif

                                <div class="actions">
                                    <a href="{{ route('certificate-verifications.download', $verification) }}" class="btn-sec">{{ __('certificates.view_file') }}</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="empty" style="margin-top: 16px;">
                        <div class="empty-ttl">{{ __('certificates.no_verification_requests_user') }}</div>
                        <div class="empty-sub">{{ __('certificates.no_verification_requests_user_description') }}</div>
                    </div>
                @endif
            </section>

            <section class="card section">
                <div class="section-title">{{ __('certificates.issued_certificates') }}</div>
                <div class="section-copy">{{ __('certificates.issued_certificates_description') }}</div>

                <div class="search-row" style="margin-top: 16px;">
                    <form method="GET" action="{{ route('certificates.index') }}" class="search-form">
                        <input type="search" name="search" value="{{ $search }}" placeholder="{{ __('certificates.search_placeholder') }}">
                        <button type="submit" class="btn-sec">{{ __('certificates.search') }}</button>
                    </form>
                    @if($search !== '')
                        <a href="{{ route('certificates.index') }}" class="btn-sec">{{ __('certificates.clear_search') }}</a>
                    @endif
                </div>

                @if($certificates->count() > 0)
                    <div class="grid">
                        @foreach($certificates as $certificate)
                            @php
                                $lessonTitle = $certificate->displayLessonTitle();
                                $examTitle = $certificate->displayExamTitle();
                                $learnerName = $certificate->displayLearnerName();
                                $score = $certificate->displayScore();
                            @endphp
                            <article class="card">
                                <div class="top">
                                    <div>
                                        <div class="title">{{ $lessonTitle }}</div>
                                        <div class="sub">{{ $examTitle }}</div>
                                    </div>
                                    <div class="code">{{ $certificate->certificate_code }}</div>
                                </div>
                                <div class="meta-grid">
                                    <div class="meta-box">
                                        <div class="meta-lbl">{{ __('certificates.awarded_to_label') }}</div>
                                        <div class="meta-val">{{ $learnerName }}</div>
                                    </div>
                                    <div class="meta-box">
                                        <div class="meta-lbl">{{ __('certificates.score') }}</div>
                                        <div class="meta-val">{{ $score !== null ? $score . '%' : __('certificates.not_available') }}</div>
                                    </div>
                                    <div class="meta-box">
                                        <div class="meta-lbl">{{ __('certificates.issued_on') }}</div>
                                        <div class="meta-val">{{ optional($certificate->issued_at)->format('Y-m-d') }}</div>
                                    </div>
                                    <div class="meta-box">
                                        <div class="meta-lbl">{{ __('certificates.open_lesson') }}</div>
                                        <div class="meta-val">{{ $certificate->lesson?->title ?? __('certificates.not_available') }}</div>
                                    </div>
                                </div>
                                <div class="actions">
                                    <a href="{{ route('certificates.show', $certificate) }}" class="btn-sec">{{ __('certificates.view_certificate') }}</a>
                                    @if($certificate->lesson)
                                        <a href="{{ route('lessons.show', $certificate->lesson) }}" class="btn-cta">{{ __('certificates.open_lesson') }}</a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="pagination-wrap">{{ $certificates->links() }}</div>
                @else
                    <div class="empty" style="margin-top: 16px;">
                        <div class="empty-ttl">{{ __('certificates.no_issued_certificates') }}</div>
                        <div class="empty-sub">{{ __('certificates.no_issued_certificates_description') }}</div>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>

<div class="modal{{ $errors->any() ? ' active' : '' }}" id="requestModal">
    <div class="modal-card">
        <div class="modal-head">
            <div>
                <div class="modal-title">{{ __('certificates.upload_for_verification') }}</div>
                <div class="modal-sub">{{ __('certificates.upload_for_verification_description') }}</div>
            </div>
            <button type="button" class="modal-close" id="closeRequestModal">x</button>
        </div>

        <form action="{{ route('certificate-verifications.store') }}" method="POST" enctype="multipart/form-data" class="request-form" novalidate>
            @csrf
            <div class="field">
                <label for="lessonId">{{ __('certificates.lesson') }}</label>
                <select id="lessonId" name="lesson_id">
                    <option value="">{{ __('certificates.select_lesson') }}</option>
                    @foreach($lessonOptions as $lessonOption)
                        <option value="{{ $lessonOption['id'] }}" @selected($selectedLessonId === (int) $lessonOption['id'])>{{ $lessonOption['title'] }}</option>
                    @endforeach
                </select>
                <small>{{ __('certificates.auto_issue_note') }}</small>
                @error('lesson_id') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="requestTitle">{{ __('certificates.certificate_title') }}</label>
                <input id="requestTitle" type="text" name="title" value="{{ old('title') }}" placeholder="{{ __('certificates.certificate_title_placeholder') }}">
                @error('title') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="passingScore">{{ __('certificates.passing_score') }}</label>
                <input id="passingScore" type="number" min="0" max="100" step="1" name="passing_score" value="{{ old('passing_score') }}">
                @error('passing_score') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="document">{{ __('certificates.certificate_pdf') }}</label>
                <input id="document" type="file" name="document" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                <small>{{ __('certificates.request_document_hint') }}</small>
                @error('document') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="field full">
                <div class="preview">
                    <div class="preview-line">
                        <span>{{ __('certificates.final_exam_label') }}</span>
                        <strong id="finalExamTitle">{{ __('certificates.final_exam_missing') }}</strong>
                    </div>
                    <div class="preview-line">
                        <span>{{ __('certificates.passing_score') }}</span>
                        <strong id="finalExamPassingScore">--</strong>
                    </div>
                </div>
            </div>
            <div class="field full">
                <label for="requestNotes">{{ __('certificates.notes') }}</label>
                <textarea id="requestNotes" name="notes" placeholder="{{ __('certificates.notes_placeholder') }}">{{ old('notes') }}</textarea>
                @error('notes') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="field full">
                <div class="btn-row" style="justify-content: flex-end;">
                    <button type="button" class="btn-sec" id="cancelRequestModal">{{ __('certificates.close') }}</button>
                    <button type="submit" class="btn-cta">{{ __('certificates.submit_for_verification') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('partials.app.settings-panel')

<script>
    const lessonOptions = @json($lessonOptions->keyBy('id'));
    const modal = document.getElementById('requestModal');
    const openBtn = document.getElementById('openRequestModal');
    const closeBtn = document.getElementById('closeRequestModal');
    const cancelBtn = document.getElementById('cancelRequestModal');
    const lessonSelect = document.getElementById('lessonId');
    const requestTitleInput = document.getElementById('requestTitle');
    const passingScoreInput = document.getElementById('passingScore');
    const finalExamTitle = document.getElementById('finalExamTitle');
    const finalExamPassingScore = document.getElementById('finalExamPassingScore');
    const emptyFinalExamLabel = @json(__('certificates.final_exam_missing'));

    function setModalState(isOpen) {
        modal?.classList.toggle('active', isOpen);
    }

    function syncLessonPreview() {
        const option = lessonOptions[String(lessonSelect?.value || '')];

        if (!option) {
            finalExamTitle.textContent = emptyFinalExamLabel;
            finalExamPassingScore.textContent = '--';
            return;
        }

        finalExamTitle.textContent = option.final_exam_title;
        finalExamPassingScore.textContent = `${option.default_passing_score}%`;

        if (requestTitleInput && requestTitleInput.value.trim() === '') {
            requestTitleInput.value = option.final_exam_title;
        }

        if (passingScoreInput && passingScoreInput.value === '') {
            passingScoreInput.value = String(option.default_passing_score);
        }
    }

    openBtn?.addEventListener('click', () => { setModalState(true); syncLessonPreview(); });
    closeBtn?.addEventListener('click', () => setModalState(false));
    cancelBtn?.addEventListener('click', () => setModalState(false));
    modal?.addEventListener('click', (event) => {
        if (event.target === modal) {
            setModalState(false);
        }
    });
    lessonSelect?.addEventListener('change', syncLessonPreview);
    syncLessonPreview();
    </script>
</body>
</html>
