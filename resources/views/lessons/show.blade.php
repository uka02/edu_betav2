<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.app.theme-boot')
    <title>{{ $lesson->title }} - EduDev</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #060c12; --surface: #0b1520; --s1: #0f1e2e; --s2: #152438; --s3: #1c2e44;
            --b0: rgba(56,139,220,0.07); --b1: rgba(56,139,220,0.13); --b2: rgba(56,139,220,0.22);
            --tx: #e8f0f8; --tx2: #a8bdd0; --muted: #587089;
            --blue: #3b9eff; --blue-dim: #1e6fc4; --blue-soft: rgba(59,158,255,0.08); --blue-glow: rgba(59,158,255,0.15);
            --green: #2ecc8a; --green-soft: rgba(46,204,138,0.08);
            --amber: #f5a623; --amber-soft: rgba(245,166,35,0.08);
            --red: #f05050; --red-soft: rgba(240,80,80,0.08);
            --sidebar-w: 252px; --r: 10px; --rl: 14px;
        }

        [data-theme="light"] {
            --bg: #f4f7fb; --surface: #ffffff; --s1: #ffffff; --s2: #eef2f7; --s3: #e1e8f0;
            --b0: rgba(37,99,235,0.08); --b1: rgba(37,99,235,0.15); --b2: rgba(37,99,235,0.28);
            --tx: #0f172a; --tx2: #334155; --muted: #64748b;
            --blue: #2563eb; --blue-dim: #1d4ed8; --blue-soft: rgba(37,99,235,0.07); --blue-glow: rgba(37,99,235,0.15);
            --green: #059669; --green-soft: rgba(5,150,105,0.07);
            --amber: #d97706; --amber-soft: rgba(217,119,6,0.07);
            --red: #dc2626; --red-soft: rgba(220,38,38,0.07);
        }

        html { height: 100%; }
        body { font-family: "Roboto", sans-serif; background: var(--bg); color: var(--tx); min-height: 100vh; display: flex; flex-direction: column; transition: background 0.3s, color 0.3s; }

        .sidebar { width: var(--sidebar-w); flex-shrink: 0; background: var(--surface); border-right: 1px solid var(--b0); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; z-index: 50; transition: background 0.3s; }
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

        .main-col { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { height: 56px; display: flex; align-items: center; padding: 0 24px; border-bottom: 1px solid var(--b0); background: color-mix(in srgb, var(--bg) 85%, transparent); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 40; gap: 10px; transition: background 0.3s; }
        .tb-title { font-size: 14px; font-weight: 700; color: var(--tx2); letter-spacing: -.02em; }
        .tb-sep   { width: 1px; height: 14px; background: var(--b1); }
        .tb-right { margin-left: auto; display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .tb-btn { width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--b1); background: var(--s1); color: var(--tx2); display: grid; place-items: center; cursor: pointer; transition: all .15s; }
        .tb-btn:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }
        .btn-signout { display: flex; align-items: center; gap: 6px; padding: 6px 13px; background: transparent; border: 1px solid var(--b1); color: var(--muted); border-radius: 7px; font-family: "Roboto", sans-serif; font-size: 12.5px; font-weight: 500; cursor: pointer; transition: all .15s; }
        .btn-signout:hover { background: var(--red-soft); border-color: rgba(240,80,80,.25); color: var(--red); }

        .content { padding: 32px 24px; flex: 1; display: flex; flex-direction: column; align-items: center; }
        .content-inner { width: 100%; max-width: 1360px; }

        .btn-cta { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: white; border-radius: 8px; font-size: 12.5px; font-weight: 700; text-decoration: none; transition: opacity .15s, transform .15s; box-shadow: 0 4px 14px var(--blue-glow); border: none; cursor: pointer; font-family: "Roboto", sans-serif; }
        .btn-cta:hover { opacity: .9; transform: translateY(-1px); }
        .btn-sec { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: var(--s1); color: var(--tx2); border-radius: 8px; font-size: 12.5px; font-weight: 500; text-decoration: none; transition: all .15s; border: 1px solid var(--b1); }
        .btn-sec:hover { background: var(--s2); border-color: var(--b2); color: var(--tx); }
        .btn-danger { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: var(--red-soft); color: var(--red); border-radius: 8px; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: all .15s; border: 1px solid rgba(240,80,80,.2); cursor: pointer; font-family: "Roboto", sans-serif; }
        .btn-danger:hover { background: rgba(240,80,80,.15); }
        .btn-publish { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: var(--green-soft); color: var(--green); border-radius: 8px; font-size: 12.5px; font-weight: 700; text-decoration: none; transition: all .15s; border: 1px solid rgba(46,204,138,.2); cursor: pointer; font-family: "Roboto", sans-serif; }
        .btn-publish:hover { background: rgba(46,204,138,.15); transform: translateY(-1px); }
        .lesson-header-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .lesson-header-side { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; flex-shrink: 0; }
        .btn-row { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
        .hero-kicker { display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 999px; background: var(--blue-soft); border: 1px solid rgba(59,158,255,.18); color: var(--blue); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 12px; }

        .alert-box { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: var(--r); font-size: 13px; font-weight: 500; margin-bottom: 18px; }
        .alert-ok { background: var(--green-soft); border: 1px solid rgba(46,204,138,.2); color: var(--green); }
        .alert-error { background: var(--red-soft); border: 1px solid rgba(240,80,80,.2); color: var(--red); }

        .lesson-head { margin-bottom: 20px; }
        .lesson-title { font-size: 24px; font-weight: 800; letter-spacing: -.04em; color: var(--tx); line-height: 1.2; margin-bottom: 8px; }
        .lesson-author { font-size: 13px; color: var(--muted); margin-bottom: 12px; }

        .tag-row { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 16px; }
        .tag { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: .05em; }
        .tag-blue  { background: var(--blue-soft);  color: var(--blue);  border: 1px solid rgba(59,158,255,.2); }
        .tag-green { background: var(--green-soft); color: var(--green); border: 1px solid rgba(46,204,138,.2); }
        .tag-amber { background: var(--amber-soft); color: var(--amber); border: 1px solid rgba(245,166,35,.2); }
        .tag-muted { background: var(--s2); color: var(--muted); border: 1px solid var(--b1); }

        .lesson-card { background: var(--s1); border: 1px solid var(--b0); border-radius: var(--rl); overflow: hidden; margin-bottom: 20px; }

        .lthumb { width: 100%; height: 340px; background: var(--s2); display: grid; place-items: center; font-size: 64px; overflow: hidden; }
        .lthumb img { width: 100%; height: 100%; object-fit: cover; }

        .video-wrap { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; }
        .video-wrap iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }
        .video-actions { padding: 14px 18px; border-top: 1px solid var(--b0); background: var(--s2); display: flex; justify-content: flex-end; }
        .video-source-link { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 12.5px; font-weight: 700; color: var(--blue); background: var(--blue-soft); border: 1px solid rgba(59,158,255,.18); }
        .video-source-link:hover { opacity: .92; transform: translateY(-1px); }

        .lesson-body { padding: 28px; }

        .meta-bar { display: flex; gap: 20px; flex-wrap: wrap; padding: 12px 16px; background: var(--s2); border: 1px solid var(--b0); border-radius: var(--r); margin-bottom: 24px; }
        .meta-item { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--tx2); }
        .meta-item svg { color: var(--blue); flex-shrink: 0; }

        .lesson-progress-box { width: min(240px, 100%); padding: 10px 12px; background: color-mix(in srgb, var(--s2) 92%, transparent); border: 1px solid var(--b0); border-radius: 12px; box-shadow: 0 8px 20px rgba(6,12,18,.14); backdrop-filter: blur(10px); }
        .lesson-progress-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 8px; }
        .lesson-progress-label { font-size: 10px; font-weight: 700; color: var(--tx2); text-transform: uppercase; letter-spacing: .08em; }
        .lesson-progress-value { font-size: 13px; font-weight: 800; color: var(--blue); }
        .lesson-progress-track { height: 6px; background: var(--s3); border-radius: 999px; overflow: hidden; }
        .lesson-progress-fill { height: 100%; background: linear-gradient(90deg, var(--blue-dim), var(--blue)); border-radius: 999px; transition: width .35s ease; }
        .lesson-progress-status { margin-top: 7px; font-size: 11px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .lesson-segment-layout { display: grid; grid-template-columns: 280px minmax(0, 1fr); gap: 20px; align-items: start; margin-bottom: 20px; }
        .lesson-segment-sidebar { background: var(--s1); border: 1px solid var(--b0); border-radius: 14px; padding: 20px; height: fit-content; position: sticky; top: 148px; max-height: calc(100vh - 168px); overflow-y: auto; }
        .lesson-stats-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-bottom: 20px; }
        .lesson-stat-card { background: linear-gradient(180deg, var(--s2), var(--s3)); border: 1px solid var(--b1); border-radius: 10px; padding: 12px; text-align: center; }
        .lesson-stat-value { font-size: 18px; font-weight: 700; color: var(--blue); line-height: 1; }
        .lesson-stat-label { font-size: 11px; color: var(--muted); margin-top: 4px; text-transform: uppercase; letter-spacing: .05em; }
        .course-outline { background: linear-gradient(180deg, var(--s2), var(--s1)); border: 1px solid var(--b1); border-radius: 12px; padding: 16px; margin-bottom: 20px; max-height: 300px; overflow-y: auto; }
        .outline-title { font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--tx2); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--b1); letter-spacing: .06em; }
        .outline-list { display: flex; flex-direction: column; gap: 4px; }
        .outline-item { width: 100%; padding: 6px 8px; background: transparent; border: none; border-left: 3px solid var(--b1); color: var(--muted); border-radius: 2px; cursor: pointer; transition: all .2s; text-align: left; font-size: 12px; font-family: "Roboto", sans-serif; }
        .outline-item:hover { border-left-color: var(--blue); background: var(--blue-soft); color: var(--tx); }
        .outline-item.active { border-left-color: var(--blue); background: rgba(59,158,255,.15); color: var(--tx); font-weight: 600; }
        .outline-item-icon { display: inline-block; margin-right: 6px; min-width: 20px; color: inherit; }
        .sidebar-title { font-size: 14px; font-weight: 600; text-transform: uppercase; color: var(--muted); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--b1); letter-spacing: .04em; }
        .segment-nav { display: flex; flex-direction: column; gap: 8px; }
        .segment-nav-item { width: 100%; padding: 10px 12px; background: transparent; border: 1px solid transparent; border-radius: 8px; color: var(--muted); cursor: pointer; font-size: 13px; font-weight: 500; transition: all .2s; text-align: left; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; font-family: "Roboto", sans-serif; }
        .segment-nav-item:hover { background: var(--s2); color: var(--tx); }
        .segment-nav-item.active { background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: white; border-color: rgba(59,158,255,.22); box-shadow: 0 4px 12px var(--blue-glow); }
        .lesson-segment-content { display: flex; flex-direction: column; gap: 20px; min-width: 0; }
        .segment-container { background: var(--s1); border: 1px solid var(--b0); border-radius: 14px; padding: 28px; display: none; scroll-margin-top: 148px; }
        .segment-container.active { display: block; animation: riseIn .22s ease; }
        .segment-panel-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 22px; padding-bottom: 16px; border-bottom: 2px solid var(--b1); }
        .segment-header { font-size: 20px; font-weight: 700; color: var(--tx); margin: 0; }
        .segment-meta { display: inline-flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: 999px; background: var(--s2); border: 1px solid var(--b1); color: var(--muted); font-size: 12px; font-weight: 600; white-space: nowrap; }
        .segment-content { display: flex; flex-direction: column; gap: 20px; }
        .basic-info-grid { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(280px, .8fr); gap: 22px; align-items: start; }
        .basic-info-side { display: flex; flex-direction: column; gap: 16px; }
        .basic-info-card { background: linear-gradient(180deg, var(--s2), var(--s1)); border: 1px solid var(--b1); border-radius: 12px; padding: 18px; }
        .basic-info-list { display: flex; flex-direction: column; gap: 12px; }
        .basic-info-item { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; font-size: 13px; }
        .basic-info-item-label { color: var(--muted); font-weight: 600; }
        .basic-info-item-value { color: var(--tx); font-weight: 600; text-align: right; }
        .basic-info-tags { display: flex; gap: 6px; flex-wrap: wrap; }
        .segment-empty-note { padding: 16px 18px; background: var(--s2); border: 1px dashed var(--b1); border-radius: 12px; color: var(--muted); font-size: 13px; line-height: 1.6; }

        .content-blocks { display: flex; flex-direction: column; gap: 20px; }

        .cb-text { font-size: 15px; line-height: 1.8; color: var(--tx); }
        .cb-subheading { font-size: 19px; font-weight: 700; color: var(--tx); letter-spacing: -.02em; padding-bottom: 8px; border-bottom: 1px solid var(--b1); }
        .cb-image { width: 100%; border-radius: var(--r); border: 1px solid var(--b1); transition: transform .3s; }
        .cb-image:hover { transform: scale(1.01); }
        .cb-caption { font-size: 12.5px; color: var(--muted); margin-top: 6px; font-style: italic; }
        .cb-code { font-family: 'Courier New', monospace; font-size: 13.5px; line-height: 1.6; color: var(--tx); white-space: pre-wrap; padding: 18px; background: var(--s2); border: 1px solid var(--b1); border-radius: var(--r); overflow-x: auto; }
        .cb-divider { height: 1px; background: linear-gradient(90deg, transparent, var(--b2), transparent); }
        .cb-video iframe { width: 100%; height: 380px; border: none; border-radius: var(--r); }
        .cb-video-actions { margin-top: 10px; display: flex; justify-content: flex-end; }

        .cb-callout { padding: 14px 18px; border-radius: var(--r); border-left: 3px solid; font-size: 14px; font-weight: 500; line-height: 1.6; }
        .callout-info    { background: var(--blue-soft);  border-left-color: var(--blue);  color: var(--blue); }
        .callout-warning { background: var(--amber-soft); border-left-color: var(--amber); color: var(--amber); }
        .callout-success { background: var(--green-soft); border-left-color: var(--green); color: var(--green); }
        .callout-danger  { background: var(--red-soft);   border-left-color: var(--red);   color: var(--red); }

        .quiz-block { background: var(--s2); border: 1px solid var(--b1); border-radius: var(--rl); padding: 22px; }
        .quiz-q { font-size: 16px; font-weight: 700; color: var(--tx); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--b0); }
        .quiz-answers { display: flex; flex-direction: column; gap: 10px; }
        .quiz-opt { display: flex; align-items: center; gap: 12px; padding: 13px 16px; background: var(--s1); border: 1px solid var(--b0); border-radius: var(--r); cursor: pointer; transition: all .2s; }
        .quiz-opt:hover { border-color: var(--b2); }
        .quiz-opt.selected { background: var(--blue-soft); border-color: rgba(59,158,255,.3); }
        .quiz-opt.correct-answer { background: var(--green-soft); border-color: rgba(46,204,138,.3); }
        .quiz-opt.wrong-answer   { background: var(--red-soft);   border-color: rgba(240,80,80,.3); }
        .quiz-radio { display: none; }
        .answer-letter { width: 28px; height: 28px; border-radius: 6px; background: var(--blue-soft); border: 1px solid rgba(59,158,255,.2); color: var(--blue); display: grid; place-items: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
        .correct-answer .answer-letter { background: var(--green-soft); border-color: rgba(46,204,138,.2); color: var(--green); }
        .wrong-answer .answer-letter   { background: var(--red-soft);   border-color: rgba(240,80,80,.2);   color: var(--red); }
        .answer-text { font-size: 14px; color: var(--tx); flex: 1; }
        .correct-badge { display: inline-flex; align-items: center; gap: 3px; padding: 3px 8px; background: var(--green-soft); color: var(--green); font-size: 11px; font-weight: 700; border-radius: 4px; }
        .quiz-check-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: white; border: none; border-radius: 7px; font-size: 13px; font-weight: 600; cursor: pointer; margin-top: 14px; transition: opacity .15s; font-family: "Roboto", sans-serif; box-shadow: 0 4px 12px var(--blue-glow); }
        .quiz-check-btn:hover { opacity: .9; }
        .quiz-check-btn:disabled { opacity: .5; cursor: not-allowed; }
        .quiz-feedback { margin-top: 12px; padding: 12px 16px; border-radius: var(--r); font-size: 14px; font-weight: 600; display: none; }
        .quiz-feedback.success { background: var(--green-soft); border: 1px solid rgba(46,204,138,.2); color: var(--green); }
        .quiz-feedback.error   { background: var(--red-soft);   border: 1px solid rgba(240,80,80,.2);   color: var(--red); }

        .doc-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px; }
        .doc-preview-link,
        .doc-dl { display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; border-radius: var(--r); text-decoration: none; font-weight: 600; font-size: 13.5px; transition: all .15s; }
        .doc-preview-link { background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: #fff; box-shadow: 0 4px 12px var(--blue-glow); }
        .doc-preview-link:hover { opacity: .92; transform: translateY(-1px); }
        .doc-dl { background: var(--s2); border: 1px solid var(--b1); color: var(--tx2); }
        .doc-dl:hover { background: var(--s3); border-color: var(--b2); color: var(--tx); }

        .engagement-section { margin-top: 24px; }
        .engagement-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 18px; margin-bottom: 18px; flex-wrap: wrap; }
        .engagement-kicker { font-size: 11px; font-weight: 800; color: var(--blue); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 8px; }
        .engagement-title { font-size: 22px; font-weight: 800; color: var(--tx); letter-spacing: -.03em; margin-bottom: 6px; }
        .engagement-copy { color: var(--muted); max-width: 60ch; line-height: 1.7; font-size: 14px; }
        .engagement-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; min-width: min(100%, 360px); flex: 1; }
        .engagement-summary-card { background: var(--s1); border: 1px solid var(--b0); border-radius: 14px; padding: 16px; text-align: center; }
        .engagement-summary-value { font-size: 24px; font-weight: 800; color: var(--blue); line-height: 1; }
        .engagement-summary-label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-top: 8px; }
        .engagement-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .engagement-grid.single { grid-template-columns: minmax(0, 1fr); }
        .engagement-card { background: var(--s1); border: 1px solid var(--b0); border-radius: 18px; padding: 22px; }
        .engagement-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 10px; flex-wrap: wrap; }
        .engagement-card-head h3 { font-size: 18px; font-weight: 700; color: var(--tx); letter-spacing: -.02em; }
        .engagement-badge { display: inline-flex; align-items: center; justify-content: center; min-height: 24px; padding: 0 10px; border-radius: 999px; background: var(--blue-soft); color: var(--blue); font-size: 11px; font-weight: 700; }
        .engagement-badge.danger { background: var(--red-soft); color: var(--red); }
        .engagement-card-copy { color: var(--muted); font-size: 14px; line-height: 1.7; margin-bottom: 16px; }
        .engagement-form { display: flex; flex-direction: column; gap: 16px; }
        .engagement-label { display: block; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: var(--tx2); text-transform: uppercase; letter-spacing: .07em; }
        .rating-scale { display: flex; flex-wrap: wrap; gap: 10px; }
        .rating-option { position: relative; }
        .rating-option input { position: absolute; opacity: 0; pointer-events: none; }
        .rating-option span { display: inline-flex; align-items: center; justify-content: center; min-height: 42px; min-width: 68px; padding: 0 14px; border-radius: 12px; background: var(--s2); border: 1px solid var(--b1); color: var(--tx2); font-weight: 700; cursor: pointer; transition: all .15s; }
        .rating-option span:hover { border-color: var(--b2); color: var(--tx); }
        .rating-option input:checked + span { background: var(--blue-soft); border-color: rgba(59,158,255,.3); color: var(--blue); box-shadow: 0 0 0 1px rgba(59,158,255,.08); }
        .engagement-select,
        .engagement-textarea { width: 100%; background: var(--s2); border: 1px solid var(--b1); border-radius: 12px; color: var(--tx); font-family: "Roboto", sans-serif; font-size: 14px; transition: border-color .15s, box-shadow .15s; }
        .engagement-select { min-height: 46px; padding: 0 14px; }
        .engagement-textarea { padding: 12px 14px; resize: vertical; min-height: 120px; line-height: 1.6; }
        .engagement-select:focus,
        .engagement-textarea:focus { outline: none; border-color: rgba(59,158,255,.35); box-shadow: 0 0 0 3px rgba(59,158,255,.12); }
        .engagement-error { margin-top: 8px; font-size: 12px; color: var(--red); }
        .engagement-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .engagement-submit,
        .engagement-ghost { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 0 16px; border-radius: 12px; font-size: 13px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; font-family: "Roboto", sans-serif; transition: transform .15s ease, opacity .15s ease, background .15s ease; }
        .engagement-submit { background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: #fff; box-shadow: 0 6px 16px var(--blue-glow); }
        .engagement-submit.muted { background: linear-gradient(135deg, #b45309, var(--amber)); box-shadow: 0 6px 16px rgba(245,166,35,.18); }
        .engagement-submit:hover,
        .engagement-ghost:hover { opacity: .94; transform: translateY(-1px); }
        .engagement-ghost { background: var(--s2); border: 1px solid var(--b1); color: var(--tx2); }
        .engagement-list { display: flex; flex-direction: column; gap: 12px; }
        .engagement-list-item { padding: 14px 16px; background: var(--s2); border: 1px solid var(--b1); border-radius: 14px; }
        .engagement-list-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; font-size: 14px; color: var(--tx); margin-bottom: 6px; flex-wrap: wrap; }
        .engagement-list-meta { font-size: 12px; color: var(--muted); margin-bottom: 8px; }
        .engagement-list-copy { font-size: 14px; color: var(--tx2); line-height: 1.7; white-space: pre-wrap; }
        .engagement-feedback-stack { display: flex; flex-direction: column; gap: 10px; }
        .engagement-feedback-note { padding: 12px 14px; border-radius: 12px; border: 1px solid var(--b1); }
        .engagement-feedback-note.positive { background: var(--green-soft); border-color: rgba(46,204,138,.2); }
        .engagement-feedback-note.negative { background: var(--red-soft); border-color: rgba(240,80,80,.2); }
        .engagement-feedback-note.neutral { background: var(--s1); border-color: var(--b0); }
        .engagement-feedback-note-label { font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 6px; color: var(--tx2); }
        .engagement-feedback-note-copy { font-size: 14px; color: var(--tx); line-height: 1.7; white-space: pre-wrap; }
        .engagement-empty { padding: 18px; border-radius: 14px; background: var(--s2); border: 1px dashed var(--b1); color: var(--muted); font-size: 14px; line-height: 1.7; }
        .reason-pill { display: inline-flex; align-items: center; min-height: 24px; padding: 0 10px; border-radius: 999px; background: var(--amber-soft); color: var(--amber); font-size: 11px; font-weight: 700; }

        .exam-section { margin-top: 32px; padding-top: 32px; border-top: 1px solid var(--b1); }
        .exam-head { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .exam-head h2 { font-size: 20px; font-weight: 800; color: var(--amber); letter-spacing: -.03em; }

        .exam-info-box { display: flex; gap: 10px; padding: 14px 16px; background: var(--amber-soft); border: 1px solid rgba(245,166,35,.2); border-radius: var(--r); margin-bottom: 20px; color: var(--amber); font-size: 13px; line-height: 1.6; }

        .exam-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; margin-bottom: 24px; }
        .exam-stat { background: var(--amber-soft); border: 1px solid rgba(245,166,35,.2); border-radius: var(--r); padding: 14px; text-align: center; }
        .exam-stat-lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); margin-bottom: 6px; }
        .exam-stat-val { font-size: 22px; font-weight: 800; color: var(--amber); }

        .start-exam-btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 24px; background: linear-gradient(135deg, var(--amber-dim, #c47e0e), var(--amber)); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; transition: opacity .15s, transform .15s; box-shadow: 0 4px 14px rgba(245,166,35,.3); font-family: "Roboto", sans-serif; margin-top: 20px; }
        .start-exam-btn:hover { opacity: .9; transform: translateY(-1px); }

        .login-req { margin-top: 20px; padding: 14px 16px; background: var(--red-soft); border: 1px solid rgba(240,80,80,.2); border-radius: var(--r); color: var(--red); font-size: 13px; text-align: center; }
        .login-req a { color: var(--amber); text-decoration: underline; }

        .exam-overlay { display: none; position: fixed; inset: 0; background: var(--bg); z-index: 1000; overflow-y: auto; padding: 20px; }
        .exam-overlay.active { display: flex; flex-direction: column; }
        .exam-wrapper { max-width: 860px; margin: 0 auto; width: 100%; flex: 1; }

        .exam-topbar { position: sticky; top: 0; background: var(--s1); border: 1px solid var(--b1); border-radius: var(--r); padding: 12px 18px; display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 20px; z-index: 999; }
        .exam-tb-title { font-size: 14px; font-weight: 700; color: var(--blue); }
        .exam-timer { display: flex; align-items: center; gap: 6px; padding: 6px 12px; background: var(--s2); border: 1px solid var(--b1); border-radius: 7px; font-weight: 700; font-size: 14px; }
        .exam-timer.time-warning { border-color: rgba(245,166,35,.4); color: var(--amber); background: var(--amber-soft); }
        .exam-timer.time-critical { border-color: rgba(240,80,80,.4); color: var(--red); background: var(--red-soft); animation: critPulse 1s infinite; }
        @keyframes critPulse { 0%,100%{opacity:1;} 50%{opacity:.6;} }
        .exam-progress-txt { font-size: 13px; color: var(--muted); }
        .exam-exit-btn { display: flex; align-items: center; gap: 5px; padding: 6px 12px; background: var(--red-soft); border: 1px solid rgba(240,80,80,.2); color: var(--red); border-radius: 7px; font-size: 12.5px; font-weight: 600; cursor: pointer; font-family: "Roboto", sans-serif; transition: all .15s; }
        .exam-exit-btn:hover { background: rgba(240,80,80,.15); }

        .exam-q-card { background: var(--s1); border: 1px solid var(--b0); border-radius: var(--rl); padding: 32px; margin-bottom: 20px; min-height: 360px; display: flex; flex-direction: column; }
        .exam-q-head { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 20px; }
        .exam-q-num { width: 32px; height: 32px; border-radius: 7px; background: var(--amber-soft); border: 1px solid rgba(245,166,35,.2); color: var(--amber); display: grid; place-items: center; font-size: 13px; font-weight: 800; flex-shrink: 0; }
        .exam-q-text { font-size: 17px; font-weight: 700; color: var(--tx); line-height: 1.4; flex: 1; }
        .exam-q-type { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; background: var(--amber-soft); color: var(--amber); text-transform: uppercase; letter-spacing: .05em; flex-shrink: 0; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }

        .exam-answers-list { display: flex; flex-direction: column; gap: 10px; flex: 1; }
        .exam-answer-group { border: 0; padding: 0; margin: 0; min-inline-size: 0; display: flex; flex-direction: column; gap: 10px; }
        .exam-a-opt { position: relative; display: flex; align-items: center; gap: 10px; padding: 13px 16px; background: var(--s2); border: 1px solid var(--b0); border-radius: var(--r); cursor: pointer; transition: all .15s; user-select: none; }
        .exam-a-opt:hover { border-color: var(--b2); }
        .exam-a-opt.exam-selected { background: var(--blue-soft); border-color: rgba(59,158,255,.3); }
        .exam-a-opt:focus-within { border-color: rgba(59,158,255,.4); box-shadow: 0 0 0 3px rgba(59,158,255,.14); }
        .exam-answer-radio { position: absolute; opacity: 0; pointer-events: none; width: 1px; height: 1px; }
        .exam-a-letter { width: 26px; height: 26px; border-radius: 5px; background: var(--s3); color: var(--muted); display: grid; place-items: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }
        .exam-selected .exam-a-letter { background: var(--blue-soft); color: var(--blue); }
        .exam-a-text { font-size: 14px; color: var(--tx); }

        .exam-sa-input { width: 100%; padding: 12px 16px; background: var(--s2); border: 1px solid var(--b1); border-radius: var(--r); color: var(--tx); font-size: 14px; font-family: "Roboto", sans-serif; transition: border-color .15s; }
        .exam-sa-input:focus { outline: none; border-color: var(--b2); }

        .exam-btn-row { display: flex; gap: 10px; margin-top: 20px; }
        .exam-nav-btn { display: flex; align-items: center; gap: 6px; padding: 9px 18px; background: var(--s2); border: 1px solid var(--b1); color: var(--tx2); border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .15s; font-family: "Roboto", sans-serif; }
        .exam-nav-btn:hover:not(:disabled) { background: var(--s3); border-color: var(--b2); color: var(--tx); }
        .exam-nav-btn:disabled { opacity: .4; cursor: not-allowed; }
        .exam-submit-btn { display: flex; align-items: center; gap: 6px; padding: 9px 18px; background: linear-gradient(135deg, var(--blue-dim), var(--blue)); color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: opacity .15s; font-family: "Roboto", sans-serif; box-shadow: 0 4px 12px var(--blue-glow); }
        .exam-submit-btn:hover { opacity: .9; }

        .exam-results { background: var(--s1); border: 1px solid var(--b0); border-radius: var(--rl); padding: 40px; text-align: center; display: none; }
        .exam-results.show { display: block; }
        .results-score { font-size: 64px; font-weight: 900; letter-spacing: -.05em; color: var(--blue); margin-bottom: 8px; }
        .results-status { font-size: 20px; font-weight: 700; margin-bottom: 32px; }
        .results-status.passed { color: var(--green); }
        .results-status.failed { color: var(--red); }

        .results-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-bottom: 32px; }
        .result-stat { background: var(--s2); border: 1px solid var(--b0); border-radius: var(--r); padding: 16px; }
        .result-stat-lbl { font-size: 11px; color: var(--muted); margin-bottom: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
        .result-stat-val { font-size: 20px; font-weight: 800; color: var(--tx); }

        .results-breakdown { text-align: left; margin-top: 24px; }
        .results-breakdown h3 { font-size: 14px; font-weight: 700; margin-bottom: 14px; color: var(--tx); }
        .result-item { padding: 14px 16px; border-radius: var(--r); margin-bottom: 8px; border: 1px solid; }
        .result-item.correct   { background: var(--green-soft); border-color: rgba(46,204,138,.2); }
        .result-item.incorrect { background: var(--red-soft);   border-color: rgba(240,80,80,.2); }
        .result-q-text { font-size: 13.5px; font-weight: 600; color: var(--tx); margin-bottom: 8px; }
        .result-answer { display: flex; gap: 8px; font-size: 12.5px; margin-top: 4px; }
        .result-answer-label { color: var(--muted); min-width: 90px; }
        .result-answer-value { color: var(--tx); }
        .result-answer-value.correct   { color: var(--green); font-weight: 600; }
        .result-answer-value.incorrect { color: var(--red);   font-weight: 600; }

        .results-btns { display: flex; gap: 10px; justify-content: center; margin-top: 28px; }

        @keyframes riseIn { from{opacity:0;transform:translateY(12px);} to{opacity:1;transform:translateY(0);} }
        ::-webkit-scrollbar { width: 5px; } ::-webkit-scrollbar-track { background: transparent; } ::-webkit-scrollbar-thumb { background: var(--s3); border-radius: 99px; }

        @media (max-width: 1100px) {
            .lesson-segment-layout { grid-template-columns: 1fr; }
            .lesson-segment-sidebar { position: static; max-height: none; }
            .basic-info-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-col { margin-left: 0; }
            .content { padding: 16px 12px; }
            .results-grid { grid-template-columns: 1fr; }
            .lesson-header-side { width: 100%; align-items: stretch; }
            .btn-row { justify-content: flex-start; }
            .lesson-progress-box { width: 100%; }
            .segment-container { padding: 20px; scroll-margin-top: 132px; }
            .segment-panel-head { margin-bottom: 16px; padding-bottom: 12px; align-items: flex-start; flex-wrap: wrap; }
            .segment-header { font-size: 19px; }
            .engagement-grid { grid-template-columns: 1fr; }
            .engagement-summary { min-width: 0; width: 100%; }
        }

        [data-theme="light"] .lesson-card  { background: #ffffff; border-color: rgba(37,99,235,0.12); box-shadow: 0 2px 12px rgba(15,23,42,0.06); }
        [data-theme="light"] .lthumb       { background: #eef2f7; }
        [data-theme="light"] .meta-bar     { background: #f4f7fb; border-color: rgba(37,99,235,0.10); }
        [data-theme="light"] .lesson-segment-sidebar { background: #ffffff; border-color: rgba(37,99,235,0.12); box-shadow: 0 2px 12px rgba(15,23,42,0.06); }
        [data-theme="light"] .lesson-stat-card { background: linear-gradient(180deg, #f8fbff, #eef5ff); border-color: rgba(37,99,235,0.12); }
        [data-theme="light"] .course-outline { background: linear-gradient(180deg, #f8fbff, #ffffff); border-color: rgba(37,99,235,0.12); }
        [data-theme="light"] .segment-nav-item:hover { background: #eef5ff; }
        [data-theme="light"] .segment-container { background: #ffffff; border-color: rgba(37,99,235,0.12); box-shadow: 0 2px 12px rgba(15,23,42,0.06); }
        [data-theme="light"] .segment-meta { background: #f4f7fb; border-color: rgba(37,99,235,0.12); color: #64748b; }
        [data-theme="light"] .cb-code      { background: #f4f7fb; border-color: rgba(37,99,235,0.12); color: #0f172a; }
        [data-theme="light"] .quiz-block   { background: #f4f7fb; border-color: rgba(37,99,235,0.15); }
        [data-theme="light"] .quiz-opt     { background: #ffffff; border-color: rgba(37,99,235,0.12); }
        [data-theme="light"] .quiz-opt:hover { background: #eef2f7; }
        [data-theme="light"] .quiz-opt.selected {
            background: linear-gradient(180deg, rgba(37,99,235,0.14), rgba(37,99,235,0.08));
            border-color: rgba(37,99,235,0.3);
            box-shadow: 0 0 0 1px rgba(37,99,235,0.06);
        }
        [data-theme="light"] .quiz-opt.correct-answer {
            background: linear-gradient(180deg, rgba(5,150,105,0.14), rgba(5,150,105,0.08));
            border-color: rgba(5,150,105,0.26);
            box-shadow: 0 0 0 1px rgba(5,150,105,0.05);
        }
        [data-theme="light"] .quiz-opt.wrong-answer {
            background: linear-gradient(180deg, rgba(220,38,38,0.12), rgba(220,38,38,0.07));
            border-color: rgba(220,38,38,0.24);
            box-shadow: 0 0 0 1px rgba(220,38,38,0.04);
        }
        [data-theme="light"] .quiz-opt.selected .answer-letter {
            background: rgba(37,99,235,0.14);
            border-color: rgba(37,99,235,0.24);
            color: #1d4ed8;
        }
        [data-theme="light"] .correct-answer .answer-letter {
            background: rgba(5,150,105,0.14);
            border-color: rgba(5,150,105,0.24);
        }
        [data-theme="light"] .wrong-answer .answer-letter {
            background: rgba(220,38,38,0.12);
            border-color: rgba(220,38,38,0.22);
        }
        [data-theme="light"] .correct-badge {
            background: rgba(5,150,105,0.14);
            color: #047857;
        }
        [data-theme="light"] .quiz-feedback.success {
            background: rgba(5,150,105,0.12);
            border-color: rgba(5,150,105,0.22);
            color: #047857;
        }
        [data-theme="light"] .quiz-feedback.error {
            background: rgba(220,38,38,0.1);
            border-color: rgba(220,38,38,0.2);
            color: #b91c1c;
        }
        [data-theme="light"] .doc-preview-link { box-shadow: 0 6px 16px rgba(37,99,235,0.18); }
        [data-theme="light"] .doc-dl       { background: #f4f7fb; border-color: rgba(37,99,235,0.15); color: #334155; }
        [data-theme="light"] .doc-dl:hover { background: #eef2f7; }
        [data-theme="light"] .engagement-summary-card,
        [data-theme="light"] .engagement-card { background: #ffffff; border-color: rgba(37,99,235,0.12); box-shadow: 0 2px 12px rgba(15,23,42,0.06); }
        [data-theme="light"] .rating-option span,
        [data-theme="light"] .engagement-select,
        [data-theme="light"] .engagement-textarea,
        [data-theme="light"] .engagement-list-item,
        [data-theme="light"] .engagement-empty,
        [data-theme="light"] .engagement-ghost { background: #f4f7fb; border-color: rgba(37,99,235,0.12); color: #334155; }
        [data-theme="light"] .rating-option input:checked + span { background: rgba(37,99,235,0.12); border-color: rgba(37,99,235,0.24); color: #1d4ed8; }
        [data-theme="light"] .exam-stat    { background: rgba(217,119,6,0.06); border-color: rgba(217,119,6,0.18); }
        [data-theme="light"] .exam-info-box { background: rgba(217,119,6,0.06); border-color: rgba(217,119,6,0.18); }
        [data-theme="light"] .exam-q-card  { background: #ffffff; border-color: rgba(37,99,235,0.12); box-shadow: 0 2px 12px rgba(15,23,42,0.06); }
        [data-theme="light"] .exam-a-opt   { background: #f4f7fb; border-color: rgba(37,99,235,0.10); }
        [data-theme="light"] .exam-a-opt:hover { background: #eef2f7; }
        [data-theme="light"] .exam-a-opt.exam-selected {
            background: linear-gradient(180deg, rgba(37,99,235,0.14), rgba(37,99,235,0.08));
            border-color: rgba(37,99,235,0.3);
            box-shadow: 0 0 0 1px rgba(37,99,235,0.06);
        }
        [data-theme="light"] .exam-selected .exam-a-letter {
            background: rgba(37,99,235,0.14);
            color: #1d4ed8;
        }
        [data-theme="light"] .exam-results { background: #ffffff; border-color: rgba(37,99,235,0.12); }
        [data-theme="light"] .result-stat  { background: #f4f7fb; border-color: rgba(37,99,235,0.10); }
        [data-theme="light"] .tag-muted    { background: #eef2f7; color: #64748b; border-color: rgba(37,99,235,0.12); }
        [data-theme="light"] .lc-tag       { background: rgba(255,255,255,0.9); color: #334155; }
        [data-theme="light"] .exam-topbar  { background: #ffffff; border-color: rgba(37,99,235,0.15); }
        [data-theme="light"] .sidebar      { background: #ffffff; }
        [data-theme="light"] .nav-a:hover  { background: #f4f7fb; }
        [data-theme="light"] .user-row:hover { background: #f4f7fb; }
        [data-theme="light"] .topbar       { background: rgba(244,247,251,0.92); }

    </style>
</head>
<body>
@php
    $isAuthenticatedViewer = Auth::check();
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
            @if($isAuthenticatedViewer)
                @include('partials.app.user-summary')
            @else
                @include('partials.app.guest-access-card')
            @endif
        </div>
    </aside>

    <div class="main-col">
        <header class="topbar">
            <span class="tb-title">{{ ($canManageLesson ?? false) ? __('dashboard.my_lessons') : __('lessons.explore_lessons') }}</span>
            <div class="tb-sep"></div>
            <div class="tb-right">
                @include('partials.app.settings-button', [
                    'buttonClass' => 'tb-btn',
                    'buttonId' => 'settingsBtn2',
                    'title' => __('dashboard.settings'),
                ])
                @if($isAuthenticatedViewer)
                    @include('partials.app.logout-button')
                @else
                    <a href="{{ route('login') }}" class="btn-sec">{{ __('auth.sign_in') }}</a>
                    <a href="{{ route('signup') }}" class="btn-cta">{{ __('auth.create_account') }}</a>
                @endif
            </div>
        </header>

        <div class="content">
          <div class="content-inner">
            @if(session('success'))
                <div class="alert-box alert-ok">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-box alert-error">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @php
                $contentSegments = collect($lesson->segments ?? [])->filter(fn($segment) => ($segment['type'] ?? null) === 'content')->values();
                $examSegments = collect($lesson->segments ?? [])->filter(fn($segment) => ($segment['type'] ?? null) === 'exam')->values();
                $lessonProgressPercent = (int) ($lessonProgress?->progress_percent ?? 0);
                $lessonProgressItems = $lessonProgress?->progress_state['items'] ?? [];
                $viewerCanManageLesson = (bool) ($canManageLesson ?? false);
                $isAdminViewer = Auth::user()?->isAdmin() ?? false;
                $viewerSections = collect([
                    [
                        'id' => 'basic-info',
                        'type' => 'basic',
                        'title' => __('lessons.basic_info'),
                        'label' => __('lessons.basic_info'),
                        'icon' => 'B',
                    ],
                ]);

                $contentSegments->each(function ($segment, $index) use (&$viewerSections) {
                    $segmentId = (int) ($segment['id'] ?? ($index + 1));
                    $segmentName = trim((string) ($segment['custom_name'] ?? ''));

                    $viewerSections->push([
                        'id' => 'content-' . $segmentId,
                        'type' => 'content',
                        'title' => $segmentName !== '' ? $segmentName : __('lessons.content_blocks') . ' ' . ($index + 1),
                        'label' => $segmentName !== '' ? $segmentName : __('lessons.content_blocks') . ' ' . ($index + 1),
                        'icon' => 'C',
                        'segment' => $segment,
                        'index' => $index,
                    ]);
                });

                if ($contentSegments->isEmpty() && filled($lesson->content)) {
                    $viewerSections->push([
                        'id' => 'legacy-content',
                        'type' => 'legacy-content',
                        'title' => __('lessons.content_blocks') . ' 1',
                        'label' => __('lessons.content_blocks') . ' 1',
                        'icon' => 'C',
                    ]);
                }

                $examSegments->each(function ($segment, $index) use (&$viewerSections) {
                    $segmentName = trim((string) ($segment['custom_name'] ?? ''));

                    $viewerSections->push([
                        'id' => 'exam-' . $index,
                        'type' => 'exam',
                        'title' => $segmentName !== '' ? $segmentName : __('lessons.exam_index_label') . ' ' . ($index + 1),
                        'label' => $segmentName !== '' ? $segmentName : __('lessons.exam_index_label') . ' ' . ($index + 1),
                        'icon' => 'E',
                        'segment' => $segment,
                        'index' => $index,
                    ]);
                });

                $sectionCount = $viewerSections->count();
                $contentBlockCount = $contentSegments->sum(fn ($segment) => is_array($segment['blocks'] ?? null) ? count($segment['blocks']) : 0);
                $examQuestionCount = $examSegments->sum(fn ($segment) => count($segment['questions'] ?? []));
                $viewerItemCount = max(1, $contentBlockCount + $examQuestionCount);
                $ownsLesson = Auth::id() === $lesson->user_id;
                $lessonSubject = \App\Models\Lesson::normalizeSubject($lesson->subject ?? \App\Models\Lesson::defaultSubject());
                $progressStatusLabel = $isAuthenticatedViewer && ! $viewerCanManageLesson
                    ? __('lessons.progress_autosave_notice')
                    : __('lessons.track_progress');
            @endphp
            <div class="lesson-header-row">
                <div class="lesson-head">
                    <div class="hero-kicker">
                        {{ $viewerCanManageLesson ? (($isAdminViewer && ! $ownsLesson) ? __('dashboard.role_admin') : __('dashboard.my_lessons')) : __('lessons.learner_view') }}
                    </div>
                    <h1 class="lesson-title">{{ $lesson->title }}</h1>
                    @if($lesson->user)
                        <p class="lesson-author">{{ __('lessons.by') }} {{ $lesson->user->name }}</p>
                    @endif
                    <div class="tag-row">
                        <span class="tag tag-muted">{{ __('lessons.subject_' . $lessonSubject) }}</span>
                        <span class="tag tag-blue">{{ __('lessons.' . $lesson->type) }}</span>
                        <span class="tag {{ $lesson->is_published ? 'tag-green' : 'tag-muted' }}">
                            {{ $lesson->is_published ? __('lessons.published') : __('lessons.draft') }}
                        </span>
                        @if($lesson->is_free)
                            <span class="tag tag-green">{{ __('lessons.free_badge') }}</span>
                        @else
                            <span class="tag tag-amber">{{ __('lessons.paid') }}</span>
                        @endif
                        @if($lesson->difficulty)
                            <span class="tag tag-muted">{{ __('lessons.' . $lesson->difficulty) }}</span>
                        @endif
                        @if($lesson->segments && is_array($lesson->segments) && collect($lesson->segments)->contains('type','exam'))
                            <span class="tag tag-amber">{{ __('lessons.exam_badge') }}</span>
                        @endif
                    </div>
                </div>
                <div class="lesson-header-side">
                    <div class="lesson-progress-box">
                        <div class="lesson-progress-head">
                            <span class="lesson-progress-label">{{ __('lessons.lesson_progress') }}</span>
                            <span class="lesson-progress-value" id="lessonProgressPercent">{{ $lessonProgressPercent }}%</span>
                        </div>
                        <div class="lesson-progress-track">
                            <div class="lesson-progress-fill" id="lessonProgressFill" style="width:{{ $lessonProgressPercent }}%;"></div>
                        </div>
                        <div class="lesson-progress-status" id="lessonProgressStatus">{{ $progressStatusLabel }}</div>
                    </div>

                    <div class="btn-row">
                        <a href="{{ route('lessons.index') }}" class="btn-sec">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                            {{ __('lessons.back') }}
                        </a>
                        @if(! $viewerCanManageLesson)
                            <a href="#course-outline" class="btn-cta">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 5v14l11-7z"/></svg>
                                {{ $lessonProgressPercent > 0 ? __('lessons.continue_learning') : __('lessons.start_learning') }}
                            </a>
                        @endif
                        @if($viewerCanManageLesson)
                            @unless($lesson->is_published)
                                <form action="{{ route('lessons.publish', $lesson) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-publish">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12"/><path d="m17 8-5-5-5 5"/><path d="M5 21h14"/></svg>
                                        {{ __('lessons.publish') }}
                                    </button>
                                </form>
                            @endunless
                            <a href="{{ route('lessons.edit', $lesson) }}" class="btn-cta">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                {{ __('lessons.edit') }}
                            </a>
                            <form action="{{ route('lessons.destroy', $lesson) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ __('lessons.confirm_delete') ?? 'Are you sure?' }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    {{ __('lessons.delete') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="meta-bar">
                <div class="meta-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    <span>{{ __('lessons.subject_' . $lessonSubject) }}</span>
                </div>
                <div class="meta-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h7v7H3z"/><path d="M14 3h7v7h-7z"/><path d="M14 14h7v7h-7z"/><path d="M3 14h7v7H3z"/></svg>
                    <span>{{ $sectionCount }} {{ __('lessons.sections') }}</span>
                </div>
                <div class="meta-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="3"/><path d="M12 11v10"/><path d="M8 21h8"/></svg>
                    <span>{{ count($examSegments) }} {{ __('lessons.exams') }}</span>
                </div>
                <div class="meta-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18"/><path d="M3 6h18"/><path d="M3 18h18"/></svg>
                    <span>{{ $viewerItemCount }} {{ __('lessons.blocks') }}</span>
                </div>
                <div class="meta-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <span>
                        @if($feedbackAverageRating !== null)
                            {{ number_format($feedbackAverageRating, 1) }}/5 · {{ $feedbackCount }} {{ __('lessons.ratings') }}
                        @else
                            {{ __('lessons.no_ratings_yet') }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="lesson-segment-layout">
                <aside class="lesson-segment-sidebar">
                    <div class="lesson-stats-grid">
                        <div class="lesson-stat-card">
                            <div class="lesson-stat-value">{{ $sectionCount }}</div>
                            <div class="lesson-stat-label">{{ __('lessons.segments') }}</div>
                        </div>
                        <div class="lesson-stat-card">
                            <div class="lesson-stat-value">{{ $viewerItemCount }}</div>
                            <div class="lesson-stat-label">{{ __('lessons.blocks') }}</div>
                        </div>
                    </div>

                    <div class="course-outline" id="course-outline">
                        <div class="outline-title">{{ __('lessons.course_structure') }}</div>
                        <div class="outline-list">
                            @foreach($viewerSections as $sectionIndex => $viewerSection)
                                <button
                                    type="button"
                                    class="outline-item{{ $sectionIndex === 0 ? ' active' : '' }}"
                                    data-segment-target="{{ $viewerSection['id'] }}"
                                >
                                    <span class="outline-item-icon">{{ $viewerSection['icon'] }}</span>{{ $viewerSection['title'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="sidebar-title">{{ __('lessons.segments') }}</div>
                    <div class="segment-nav">
                        @foreach($viewerSections as $sectionIndex => $viewerSection)
                            <button
                                type="button"
                                class="segment-nav-item{{ $sectionIndex === 0 ? ' active' : '' }}"
                                data-segment-target="{{ $viewerSection['id'] }}"
                            >
                                {{ $viewerSection['label'] }}
                            </button>
                        @endforeach
                    </div>
                </aside>

                <div class="lesson-segment-content">
                    @foreach($viewerSections as $sectionIndex => $viewerSection)
                        @if($viewerSection['type'] === 'basic')
                            @include('partials.lessons.show-basic-info-segment', [
                                'lesson' => $lesson,
                                'lessonProgressPercent' => $lessonProgressPercent,
                                'sectionId' => $viewerSection['id'],
                                'isActive' => $sectionIndex === 0,
                            ])
                        @elseif($viewerSection['type'] === 'content')
                            @include('partials.lessons.show-content-segment', [
                                'segment' => $viewerSection['segment'],
                                'segmentIndex' => $viewerSection['index'],
                                'displayIndex' => $viewerSection['index'] + 1,
                                'sectionId' => $viewerSection['id'],
                                'isActive' => $sectionIndex === 0,
                            ])
                        @elseif($viewerSection['type'] === 'legacy-content')
                            <article class="segment-container{{ $sectionIndex === 0 ? ' active' : '' }}" data-segment="{{ $viewerSection['id'] }}" id="lesson-segment-{{ $viewerSection['id'] }}">
                                <div class="segment-panel-head">
                                    <h2 class="segment-header">{{ $viewerSection['title'] }}</h2>
                                    <div class="segment-meta">{{ __('lessons.content_segment') }}</div>
                                </div>
                                <div class="segment-content">
                                    <div class="cb-text js-track-block" data-progress-key="legacy-content" data-progress-kind="block">{!! nl2br(e($lesson->content)) !!}</div>
                                </div>
                            </article>
                        @elseif($viewerSection['type'] === 'exam')
                            @include('partials.lessons.show-exam-segment', [
                                'examSegment' => $viewerSection['segment'],
                                'examIndex' => $viewerSection['index'],
                                'examTitle' => $viewerSection['title'],
                                'sectionId' => $viewerSection['id'],
                                'isActive' => $sectionIndex === 0,
                            ])
                        @endif
                    @endforeach
                </div>
            </div>

            @include('partials.lessons.show-engagement-section', [
                'lesson' => $lesson,
                'canManageLesson' => $viewerCanManageLesson,
                'isAuthenticatedViewer' => $isAuthenticatedViewer,
                'lessonEngagementEnabled' => $lessonEngagementEnabled,
                'structuredLessonFeedbackEnabled' => $structuredLessonFeedbackEnabled,
                'currentUserFeedback' => $currentUserFeedback,
                'currentUserReport' => $currentUserReport,
                'recentLessonFeedback' => $recentLessonFeedback,
                'recentLessonReports' => $recentLessonReports,
                'feedbackAverageRating' => $feedbackAverageRating,
                'feedbackCount' => $feedbackCount,
                'reportCount' => $reportCount,
            ])

        </div><!-- /content-inner -->
        </div><!-- /content -->
    </div>
</div>

@include('partials.lessons.show-exam-overlay')
@include('partials.app.settings-panel')
@include('partials.lessons.show-script')
</body>
</html>
