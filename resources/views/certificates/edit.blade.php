<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title>{{ __('certificates.edit_certificate') }} - EduDev</title>
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
            --green: #2fb777; --green-soft: rgba(47,183,119,0.12);
            --red: #f05050; --red-soft: rgba(240,80,80,0.10);
            --sidebar-w: 252px; --r: 10px; --rl: 14px;
        }

        [data-theme="light"] {
            --bg: #f0f4f8; --surface: #ffffff; --s1: #ffffff; --s2: #f0f4f8;
            --b0: rgba(56,139,220,0.10); --b1: rgba(56,139,220,0.18); --b2: rgba(56,139,220,0.30);
            --tx: #0f172a; --tx2: #334155; --muted: #64748b;
            --blue: #2563eb; --blue-dim: #1d4ed8; --blue-soft: rgba(37,99,235,0.08); --blue-glow: rgba(37,99,235,0.15);
            --green: #15803d; --green-soft: rgba(21,128,61,0.10);
            --red: #dc2626; --red-soft: rgba(220,38,38,0.08);
        }

        html { height: 100%; }
        body { font-family: "Roboto", sans-serif; background: var(--bg); color: var(--tx); min-height: 100vh; display: flex; flex-direction: column; }
        .sidebar { width: var(--sidebar-w); flex-shrink: 0; background: var(--surface); border-right: 1px solid var(--b0); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; z-index: 50; }
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
        .page-title { font-size: 24px; font-weight: 800; letter-spacing: -.05em; color: var(--tx); }
        .page-sub { color: var(--muted); font-size: 13px; margin-top: 6px; line-height: 1.6; }
        .btn-row { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-cta, .btn-sec { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: all .15s; }
        .btn-cta { background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: white; box-shadow: 0 4px 14px var(--blue-glow); border: none; cursor: pointer; }
        .btn-cta:hover { opacity: .92; transform: translateY(-1px); }
        .btn-sec { background: var(--s1); color: var(--tx2); border: 1px solid var(--b1); }
        .btn-sec:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }
        .alert { border-radius: 12px; padding: 14px 16px; margin-bottom: 16px; font-size: 13px; line-height: 1.6; border: 1px solid transparent; }
        .alert-success { background: var(--green-soft); border-color: rgba(47,183,119,.18); color: var(--green); }
        .alert-danger { background: var(--red-soft); border-color: rgba(240,80,80,.18); color: #fda4af; }
        [data-theme="light"] .alert-danger { color: var(--red); }
        .alert ul { padding-left: 18px; }
        .layout { display: grid; grid-template-columns: minmax(0, 1fr) minmax(320px, 360px); gap: 16px; }
        .panel { background: var(--s1); border: 1px solid var(--b0); border-radius: var(--rl); padding: 20px; }
        .panel-title { font-size: 16px; font-weight: 700; letter-spacing: -.03em; color: var(--tx); }
        .panel-sub { font-size: 12.5px; color: var(--muted); margin-top: 6px; line-height: 1.6; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
        .field { display: flex; flex-direction: column; gap: 7px; }
        .field.full { grid-column: 1 / -1; }
        .field label { font-size: 12px; font-weight: 700; color: var(--tx2); }
        .field input, .field textarea {
            width: 100%; border-radius: 10px; border: 1px solid var(--b1); background: var(--s2); color: var(--tx);
            padding: 11px 12px; font: inherit; transition: all .15s;
        }
        .field textarea { resize: vertical; min-height: 130px; }
        .field input:focus, .field textarea:focus {
            outline: none; border-color: rgba(59,158,255,.45); box-shadow: 0 0 0 3px rgba(59,158,255,.12);
        }
        .field small { font-size: 11px; color: var(--muted); line-height: 1.5; }
        .field-error { font-size: 11px; color: #fda4af; }
        [data-theme="light"] .field-error { color: var(--red); }
        .summary-grid { display: grid; gap: 10px; margin-top: 18px; }
        .summary-card { background: var(--s2); border: 1px solid var(--b0); border-radius: 12px; padding: 14px; }
        .summary-lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); margin-bottom: 6px; }
        .summary-val { font-size: 13px; font-weight: 600; color: var(--tx2); line-height: 1.6; word-break: break-word; }
        .chip-row { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 18px; }
        .chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 11px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .chip.blue { color: var(--blue); background: var(--blue-soft); }
        .chip.green { color: var(--green); background: var(--green-soft); }

        @media (max-width: 1024px) {
            .layout { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-col { margin-left: 0; }
            .content { padding: 18px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
@php
    $snapshot = $certificate->snapshot ?? [];
    $lessonTitle = $certificate->displayLessonTitle();
    $learnerName = $certificate->displayLearnerName();
    $issuerName = $certificate->displayIssuerName();
    $examTitle = trim((string) data_get($snapshot, 'exam_title', ''));
    $score = old('score', data_get($snapshot, 'score', $certificate->attempt?->score));
    $passingScore = old('passing_score', data_get($snapshot, 'passing_score'));

    if ($examTitle === '') {
        $examTitle = $certificate->displayExamTitle();
    }
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
            <span class="tb-title">{{ __('certificates.edit_certificate') }}</span>
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
                    <div class="page-title">{{ __('certificates.edit_certificate') }}</div>
                    <div class="page-sub">{{ __('certificates.edit_certificate_description') }}</div>
                </div>
                <div class="btn-row">
                    <a href="{{ route('certificates.index') }}" class="btn-sec">{{ __('certificates.back_to_certificates') }}</a>
                    <a href="{{ route('certificates.show', $certificate) }}" class="btn-sec">{{ __('certificates.view_certificate') }}</a>
                    @if($certificate->lesson)
                        <a href="{{ route('lessons.show', $certificate->lesson) }}" class="btn-cta">{{ __('certificates.open_lesson') }}</a>
                    @endif
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

            <div class="layout">
                <section class="panel">
                    <div class="panel-title">{{ __('certificates.validation_workspace') }}</div>
                    <div class="panel-sub">{{ __('certificates.validation_workspace_description') }}</div>

                    <form action="{{ route('certificates.update', $certificate) }}" method="POST" class="form-grid" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="field">
                            <label for="issuedAt">{{ __('certificates.issued_on') }}</label>
                            <input id="issuedAt" type="date" name="issued_at" value="{{ old('issued_at', optional($certificate->issued_at)->toDateString()) }}">
                            @error('issued_at') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="score">{{ __('certificates.score') }}</label>
                            <input id="score" type="number" min="0" max="100" step="0.01" name="score" value="{{ $score }}">
                            @error('score') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field full">
                            <label for="examTitle">{{ __('certificates.exam_title') }}</label>
                            <input id="examTitle" type="text" name="exam_title" value="{{ old('exam_title', $examTitle) }}">
                            @error('exam_title') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="passingScore">{{ __('certificates.passing_score') }}</label>
                            <input id="passingScore" type="number" min="0" max="100" step="1" name="passing_score" value="{{ $passingScore }}">
                            @error('passing_score') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label>{{ __('certificates.exam_reference') }}</label>
                            <input type="text" value="{{ __('lessons.exam_index_label') . ' ' . ($certificate->exam_index + 1) }}" readonly>
                            <small>{{ __('certificates.exam_reference_hint') }}</small>
                        </div>

                        <div class="field full">
                            <label for="validationNotes">{{ __('certificates.validation_notes') }}</label>
                            <textarea id="validationNotes" name="validation_notes" placeholder="{{ __('certificates.validation_notes_placeholder') }}">{{ old('validation_notes', $certificate->validation_notes) }}</textarea>
                            <small>{{ __('certificates.validation_notes_edit_hint') }}</small>
                            @error('validation_notes') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field full" style="display:flex;align-items:flex-end;">
                            <button type="submit" class="btn-cta">{{ __('certificates.save_certificate_changes') }}</button>
                        </div>
                    </form>
                </section>

                <aside class="panel">
                    <div class="panel-title">{{ __('certificates.certificate_summary') }}</div>
                    <div class="panel-sub">{{ __('certificates.certificate_summary_description') }}</div>

                    <div class="chip-row">
                        <span class="chip blue">{{ $certificate->certificate_code }}</span>
                        <span class="chip green">{{ __('certificates.validated_label') }}</span>
                    </div>

                    <div class="summary-grid">
                        <div class="summary-card">
                            <div class="summary-lbl">{{ __('certificates.awarded_to_label') }}</div>
                            <div class="summary-val">{{ $learnerName }}<br>{{ $certificate->user?->email }}</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-lbl">{{ __('certificates.lesson') }}</div>
                            <div class="summary-val">{{ $lessonTitle }}</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-lbl">{{ __('certificates.issued_by') }}</div>
                            <div class="summary-val">{{ $issuerName }}</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-lbl">{{ __('certificates.validated_on') }}</div>
                            <div class="summary-val">{{ optional($certificate->validated_at)->format('Y-m-d H:i') ?? __('certificates.not_available') }}</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-lbl">{{ __('certificates.exam_reference') }}</div>
                            <div class="summary-val">{{ $examTitle }}</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-lbl">{{ __('certificates.current_score') }}</div>
                            <div class="summary-val">{{ $score !== null && $score !== '' ? $score . '%' : __('certificates.not_available') }}</div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>

@include('partials.app.settings-panel')
</body>
</html>
