<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="lesson-id" content="">
    <title>{{ __('lessons.create_lesson') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
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
            --green-dim: #1a9960;
            --green-soft:rgba(46,204,138,0.08);
            --amber:     #f5a623;
            --amber-soft:rgba(245,166,35,0.08);
            --red:      #f05050;
            --surface1: #0f1e2e;
            --surface2: #152438;
            --surface3: #1c2e44;
            --border:   rgba(56,139,220,0.07);
            --border2:  rgba(56,139,220,0.13);
            --text:     #e8f0f8;
            --muted2:   #a8bdd0;
            --accent:   #3b9eff;
            --accent-hover: #1e6fc4;
            --purple:   #3b9eff;
            --orange:   #f5a623;
            --cyan:     #2ecc8a;
        }

        [data-theme="light"] {
            --bg:      #f0f4f8;
            --surface: #ffffff;
            --s1:      #ffffff;
            --s2:      #ffffff;
            --s3:      #ffffff;
            --b0: rgba(56,139,220,0.10);
            --b1: rgba(56,139,220,0.18);
            --b2: rgba(56,139,220,0.30);
            --tx:   #0f172a;
            --tx2:  #334155;
            --muted:#64748b;
            --muted2: #334155;
            --blue:     #2563eb;
            --blue-dim: #1d4ed8;
            --blue-soft:rgba(37,99,235,0.08);
            --blue-glow:rgba(37,99,235,0.15);
            --green:     #059669;
            --green-dim: #047857;
            --green-soft:rgba(5,150,105,0.08);
            --amber:     #d97706;
            --amber-soft:rgba(217,119,6,0.08);
            --red:      #dc2626;
            --red-soft: rgba(220,38,38,0.08);
            --surface1: #ffffff;
            --surface2: #ffffff;
            --surface3: #ffffff;
            --border:   rgba(56,139,220,0.10);
            --border2:  rgba(56,139,220,0.18);
            --text:     #0f172a;
            --accent:   #2563eb;
            --accent-hover: #1d4ed8;
            --purple:   #2563eb;
            --orange:   #d97706;
            --cyan:     #059669;
        }

        [data-theme="light"] .form-input,
        [data-theme="light"] .form-select,
        [data-theme="light"] .form-textarea {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .form-input:focus,
        [data-theme="light"] .form-select:focus,
        [data-theme="light"] .form-textarea:focus {
            background: #ffffff;
        }

        [data-theme="light"] .content-builder {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .content-block {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .block-textarea {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .block-textarea:focus {
            background: #ffffff;
        }

        [data-theme="light"] .file-label {
            background: #ffffff;
            border: 2px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .quiz-answer {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .quiz-question-input {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .quiz-question-input:focus {
            background: #ffffff;
        }

        [data-theme="light"] .quiz-answer-input {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .quiz-answer-input:focus {
            background: #ffffff;
        }

        [data-theme="light"] .video-url-input {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .video-url-input:focus {
            background: #ffffff;
        }

        [data-theme="light"] .segment-type-btn {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .segment-type-btn:hover {
            background: #ffffff;
            border: 1px solid rgba(37,99,235,0.4);
        }

        [data-theme="light"] .course-outline {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .stat-card {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .btn-secondary {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .btn-secondary:hover {
            background: #ffffff;
        }

        [data-theme="light"] .content-dropdown .dropdown-menu {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        [data-theme="light"] .dropdown-menu button:hover {
            background: rgba(37,99,235,0.08);
        }

        [data-theme="light"] .segment-type-modal-content {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        .exam-settings {
            background: linear-gradient(135deg, rgba(42, 54, 84, 0.6), rgba(31, 43, 74, 0.6));
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        [data-theme="light"] .exam-settings {
            background: #ffffff;
            border: 1px solid rgba(56,139,220,0.15);
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        body { 
            font-family:'Roboto',sans-serif; 
            background:var(--bg); 
            color:var(--text); 
            line-height:1.6;
            min-height:100vh;
            padding-bottom:40px;
        }
        
        .container {
            width: 100%;
            margin: 0px auto;
            padding: 40px 20px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .main-wrapper {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 20px;
            align-items: start;
        }

        .sidebar {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            height: fit-content;
            position: sticky;
            top: 20px;
            max-height: 80vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .sidebar-quickbar {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .sidebar-nav-panel {
            display: flex;
            flex-direction: column;
            gap: 14px;
            flex: 1 1 auto;
            min-height: 0;
        }

        .sidebar-title {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .segment-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        .segment-nav-item {
            flex: 1;
            padding: 10px 12px;
            background: transparent;
            border: 1px solid transparent;
            border-radius: 6px;
            color: var(--muted);
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            text-align: left;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .segment-nav-item:hover {
            background: var(--surface2);
            color: var(--text);
        }

        .segment-nav-item.active {
            background: linear-gradient(135deg, var(--accent), var(--purple));
            color: white;
            border-color: var(--accent);
        }

        .segment-nav-wrapper {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px;
            background: transparent;
            border-radius: 8px;
            transition: all 0.2s;
            width: 100%;
        }

        .segment-nav-wrapper:hover {
            background: var(--surface2);
        }

        .segment-nav-wrapper:hover .segment-action-btn {
            opacity: 1;
        }

        .segment-action-btn {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
            padding: 4px 6px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            min-width: 24px;
        }

        .segment-action-btn:hover {
            color: var(--text);
            border-color: var(--purple);
            background: var(--surface3);
        }

        .course-outline {
            background: linear-gradient(135deg, var(--surface1), var(--surface2));
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            max-height: 300px;
            overflow-y: auto;
        }

        .outline-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
        }

        .outline-item {
            font-size: 12px;
            padding: 6px 8px;
            margin-bottom: 4px;
            background: transparent;
            border-left: 3px solid var(--border);
            color: var(--muted);
            border-radius: 2px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .outline-item:hover {
            border-left-color: var(--accent);
            background: rgba(99, 102, 241, 0.1);
            color: var(--text);
        }

        .outline-item.active {
            border-left-color: var(--purple);
            background: rgba(99, 102, 241, 0.15);
            color: var(--text);
            font-weight: 600;
        }

        .outline-item-icon {
            display: inline-block;
            margin-right: 6px;
            min-width: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 12px;
            margin-bottom: 0;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--surface2), var(--surface3));
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: var(--accent);
        }

        .stat-label {
            font-size: 11px;
            color: var(--muted);
            margin-top: 4px;
            text-transform: uppercase;
        }

        .add-segment-btn {
            width: 100%;
            padding: 10px 12px;
            background: linear-gradient(135deg, var(--accent), var(--purple));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .add-segment-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .course-outline {
            background: linear-gradient(180deg, color-mix(in srgb, var(--surface1) 92%, transparent), color-mix(in srgb, var(--surface2) 94%, transparent));
            border: 1px solid var(--border2);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
            flex: 1 1 auto;
            min-height: 320px;
            max-height: 420px;
            overflow-y: auto;
        }

        .publish-checklist {
            background: linear-gradient(180deg, color-mix(in srgb, var(--surface1) 94%, transparent), color-mix(in srgb, var(--surface2) 92%, transparent));
            border: 1px solid var(--border2);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 18px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .publish-checklist.is-ready {
            border-color: rgba(46, 204, 138, 0.24);
            box-shadow: inset 0 0 0 1px rgba(46, 204, 138, 0.08);
        }

        .publish-checklist-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .publish-checklist-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--muted);
        }

        .publish-checklist-subtitle {
            margin-top: 6px;
            font-size: 12px;
            line-height: 1.5;
            color: var(--tx2);
        }

        .publish-checklist-summary {
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: color-mix(in srgb, var(--surface2) 86%, transparent);
            color: var(--tx2);
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .publish-checklist.is-ready .publish-checklist-summary {
            border-color: rgba(46, 204, 138, 0.24);
            background: rgba(46, 204, 138, 0.1);
            color: var(--green);
        }

        .publish-checklist-items {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .publish-checklist-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: color-mix(in srgb, var(--surface2) 88%, transparent);
            color: var(--tx2);
            transition: all 0.18s ease;
        }

        .publish-checklist-item.is-complete {
            border-color: rgba(46, 204, 138, 0.2);
            background: rgba(46, 204, 138, 0.08);
            color: var(--text);
        }

        .publish-checklist-bullet {
            width: 11px;
            height: 11px;
            border-radius: 999px;
            border: 2px solid rgba(240, 80, 80, 0.45);
            background: transparent;
            transition: all 0.18s ease;
        }

        .publish-checklist-item.is-complete .publish-checklist-bullet {
            border-color: rgba(46, 204, 138, 0.45);
            background: var(--green);
        }

        .publish-checklist-label {
            min-width: 0;
            font-size: 12.5px;
            font-weight: 500;
            line-height: 1.4;
        }

        .publish-checklist-state {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
        }

        .publish-checklist-item.is-complete .publish-checklist-state {
            color: var(--green);
        }

        .publish-checklist-error {
            margin-top: -2px;
        }

        @include('partials.lessons.learner-preview-styles')

        .outline-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--muted);
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }

        #courseOutline {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .outline-item-wrap {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 8px;
            padding: 4px;
            border-radius: 14px;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.01);
            transition: all 0.18s ease;
        }

        .outline-item-wrap:hover {
            border-color: var(--border2);
            background: color-mix(in srgb, var(--surface2) 88%, transparent);
        }

        .outline-item-wrap.active {
            border-color: rgba(59, 158, 255, 0.22);
            background: color-mix(in srgb, var(--blue-soft) 70%, var(--surface2));
            box-shadow: inset 0 0 0 1px rgba(59, 158, 255, 0.06);
        }

        .outline-item-wrap.is-empty {
            border-color: rgba(240, 80, 80, 0.18);
        }

        .outline-item-wrap.is-partial {
            border-color: rgba(245, 166, 35, 0.18);
        }

        .outline-item-main {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            width: 100%;
            border: none;
            background: transparent;
            color: inherit;
            text-align: left;
            padding: 10px;
            border-radius: 12px;
            cursor: pointer;
        }

        .outline-item-icon {
            width: 34px;
            height: 34px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            background: color-mix(in srgb, var(--s2) 88%, transparent);
            border: 1px solid var(--border);
            color: var(--blue);
            font-size: 11px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .outline-item-wrap.active .outline-item-icon {
            background: linear-gradient(135deg, var(--blue-dim), var(--blue));
            border-color: transparent;
            color: #fff;
            box-shadow: 0 8px 20px var(--blue-glow);
        }

        .outline-item-wrap.is-empty .outline-item-icon {
            color: var(--red);
        }

        .outline-item-wrap.is-partial .outline-item-icon {
            color: var(--amber);
        }

        .outline-item-copy {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
            flex: 1;
        }

        .outline-item-kicker {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .outline-item-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }

        .outline-item-name {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.35;
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .outline-item-meta {
            font-size: 11px;
            color: var(--muted);
            line-height: 1.45;
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .outline-item-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            padding: 4px 8px;
            border-radius: 999px;
            border: 1px solid rgba(59, 158, 255, 0.18);
            background: var(--blue-soft);
            color: var(--blue);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .02em;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .outline-item-wrap.is-empty .outline-item-status {
            border-color: rgba(240, 80, 80, 0.2);
            background: rgba(240, 80, 80, 0.1);
            color: var(--red);
        }

        .outline-item-wrap.is-partial .outline-item-status {
            border-color: rgba(245, 166, 35, 0.22);
            background: rgba(245, 166, 35, 0.1);
            color: var(--amber);
        }

        .outline-item-actions {
            display: flex;
            gap: 6px;
            padding-right: 6px;
        }

        .segment-action-btn {
            opacity: 1;
            min-width: 28px;
            width: 28px;
            height: 28px;
            padding: 0;
            border-radius: 8px;
            background: color-mix(in srgb, var(--s1) 92%, transparent);
            border: 1px solid var(--border);
            color: var(--muted);
            font-size: 12px;
        }

        .segment-action-btn:hover {
            color: var(--text);
            border-color: var(--border2);
            background: var(--surface3);
        }

        .content-area {
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
        }

        .builder-footer-panel {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 32px;
        }

        .builder-footer-panel .publish-checklist {
            margin-bottom: 0;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        
        .page-title {
            font-family: 'Roboto', sans-serif;
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent), var(--purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 40px rgba(168, 85, 247, 0.3);
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, var(--surface2), var(--surface3));
            color: var(--text);
            border: 1px solid var(--border2);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, var(--surface3), var(--surface2));
            border-color: var(--purple);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.4);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--purple));
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent-hover), var(--purple));
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(124, 58, 237, 0.5);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.5);
        }

        .btn-danger {
            background: var(--red);
            color: white;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
        }
        
        .form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 32px;
            margin-bottom: 24px;
        }

        .segment-container {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 32px;
            display: none;
        }

        .segment-container.active {
            display: block;
        }

        .segment-header {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--border2);
        }

        .segment-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
            justify-content: flex-end;
        }

        .segment-remove-btn {
            background: var(--red);
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .segment-remove-btn:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text);
        }
        
        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            background: linear-gradient(135deg, rgba(42, 54, 84, 0.6), rgba(31, 43, 74, 0.6));
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 10px;
            color: var(--text);
            font-size: 14px;
            font-family: 'Roboto', sans-serif;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(168, 85, 247, 0);
        }
        
        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            text-color: var(--text);
            outline: none;
            border-color: rgba(168, 85, 247, 0.6);
            background: linear-gradient(135deg, var(--surface2), var(--surface3));
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-help {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
        }
        
        .form-error {
            font-size: 12px;
            color: var(--red);
            margin-top: 4px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .checkbox-input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .file-input-wrapper {
            position: relative;
        }
        
        .file-input {
            display: none;
        }
        
        .file-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, var(--surface2), var(--surface3));
            border: 2px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }
        
        .file-label:hover {
            background: linear-gradient(135deg, var(--surface3), var(--surface2));
            border-color: var(--purple);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.4);
        }
        
        .file-name {
            margin-top: 8px;
            font-size: 13px;
            color: var(--muted);
        }
        
        .preview-image {
            margin-top: 12px;
            max-width: 200px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        
        .form-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 0;
        }

        .draft-status {
            flex-basis: 100%;
            font-size: 12px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 20px;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: color-mix(in srgb, var(--surface2) 90%, transparent);
        }

        .draft-status::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--muted);
            flex-shrink: 0;
        }

        .draft-status[data-state="dirty"] {
            color: var(--amber);
            border-color: rgba(245, 166, 35, 0.24);
            background: rgba(245, 166, 35, 0.08);
        }

        .draft-status[data-state="dirty"]::before {
            background: var(--amber);
        }

        .draft-status[data-state="saving"] {
            color: var(--blue);
            border-color: rgba(59, 158, 255, 0.24);
            background: rgba(59, 158, 255, 0.08);
        }

        .draft-status[data-state="saving"]::before {
            background: var(--blue);
            animation: draftStatusPulse 1s ease-in-out infinite;
        }

        .draft-status[data-state="saved"] {
            color: var(--green);
            border-color: rgba(46, 204, 138, 0.22);
            background: rgba(46, 204, 138, 0.08);
        }

        .draft-status[data-state="saved"]::before {
            background: var(--green);
        }

        .draft-status[data-state="restored"] {
            color: var(--accent);
            border-color: rgba(59, 158, 255, 0.24);
            background: rgba(59, 158, 255, 0.08);
        }

        .draft-status[data-state="restored"]::before {
            background: var(--accent);
        }

        @keyframes draftStatusPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.45; transform: scale(0.85); }
        }

        .draft-recovery {
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
            padding: 16px 18px;
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: 12px;
        }

        .draft-recovery-copy {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .lesson-template-panel {
            margin-bottom: 20px;
            padding: 18px;
            border-radius: 16px;
            border: 1px solid var(--border2);
            background: linear-gradient(180deg, color-mix(in srgb, var(--surface1) 94%, transparent), color-mix(in srgb, var(--surface2) 90%, transparent));
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .lesson-template-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .lesson-template-panel-kicker {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--muted);
        }

        .lesson-template-panel-copy {
            margin-top: 6px;
            font-size: 13px;
            line-height: 1.55;
            color: var(--tx2);
            max-width: 760px;
        }

        .lesson-template-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .lesson-template-card {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: color-mix(in srgb, var(--surface2) 88%, transparent);
            color: var(--text);
            text-align: left;
            cursor: pointer;
            transition: transform .18s ease, border-color .18s ease, background .18s ease, box-shadow .18s ease;
        }

        .lesson-template-card:hover {
            transform: translateY(-2px);
            border-color: rgba(59, 158, 255, 0.28);
            background: color-mix(in srgb, var(--blue-soft) 60%, var(--surface2));
            box-shadow: 0 10px 24px rgba(3, 10, 18, 0.18);
        }

        .lesson-template-card.is-active {
            border-color: rgba(59, 158, 255, 0.32);
            background: color-mix(in srgb, var(--blue-soft) 72%, var(--surface2));
            box-shadow: inset 0 0 0 1px rgba(59, 158, 255, 0.08);
        }

        .lesson-template-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--accent), var(--purple));
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .06em;
        }

        .lesson-template-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }

        .lesson-template-desc {
            font-size: 12.5px;
            line-height: 1.55;
            color: var(--tx2);
        }

        .draft-recovery-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
        }

        .draft-recovery-meta {
            font-size: 12px;
            color: var(--muted);
        }

        .draft-recovery-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .hidden {
            display: none;
        }

        .duration-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .input-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .input-group .form-input {
            flex: 1;
        }

        .input-label {
            font-size: 13px;
            color: var(--muted);
            min-width: 60px;
        }

        .content-builder {
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.6), rgba(22, 33, 62, 0.6));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 0 20px rgba(168, 85, 247, 0.1);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .builder-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 2px solid;
            border-image: linear-gradient(135deg, var(--accent), var(--purple));        }

        .builder-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
 
            margin: 0;
        }

        .builder-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: auto;
            padding-top: 24px;
            justify-content: flex-end;
            align-self: flex-end;
            width: 100%;
        }

        .builder-actions .btn {
            position: relative;
            overflow: hidden;
        }

        .builder-actions .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transition: left 0.3s;
        }

        .builder-actions .btn:hover::before {
            left: 100%;
        }

        .content-blocks {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .content-block {
            background: linear-gradient(135deg, rgba(42, 54, 84, 0.5), rgba(31, 43, 74, 0.5));
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 12px;
            padding: 20px;
            position: relative;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3), 0 0 20px rgba(168, 85, 247, 0.05);
        }

        .content-block:hover {
            border-color: rgba(168, 85, 247, 0.4);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4), 0 0 30px rgba(168, 85, 247, 0.15);
            transform: translateY(-2px);
        }

        .block-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .block-type {
            font-size: 13px;
            font-weight: 600;
            color: var(--accent);
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .block-actions {
            display: flex;
            gap: 8px;
        }

        .content-dropdown {
            position: relative;
            display: inline-block;
        }
        .content-dropdown .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: linear-gradient(135deg, var(--surface2), var(--surface1));
            border: 1px solid rgba(168, 85, 247, 0.3);
            border-radius: 10px;
            padding: 8px 0;
            min-width: 140px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4), 0 0 20px rgba(168, 85, 247, 0.1);
            z-index: 10;
            animation: slideDown 0.2s ease-out;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .content-dropdown.open .dropdown-menu {
            display: block;
        }
        .dropdown-menu button {
            width: 100%;
            padding: 12px 16px;
            background: none;
            border: none;
            text-align: left;
            font-size: 14px;
            font-weight: 500;
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s;
        }
        .dropdown-menu button:hover {
            background: linear-gradient(90deg, rgba(168, 85, 247, 0.2), transparent);
            padding-left: 20px;
        }

        .block-textarea {
            width: 100%;
            min-height: 100px;
            padding: 12px 16px;
            background: linear-gradient(135deg, rgba(42, 54, 84, 0.6), rgba(31, 43, 74, 0.6));
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 10px;
            color: var(--text);
            font-size: 14px;
            font-family: 'Roboto', sans-serif;
            resize: vertical;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .block-textarea:focus {
            outline: none;
            border-color: rgba(168, 85, 247, 0.6);
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .block-image-preview {
            margin-top: 12px;
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .empty-builder {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }

        .empty-builder-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .divider-preview {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(168, 85, 247, 0.4), transparent);
            margin: 20px 0;
            border-radius: 1px;
        }

        .move-handle {
            cursor: move;
            color: var(--muted);
            padding: 4px;
        }

        .move-handle:hover {
            color: var(--text);
        }

        .segment-type-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .segment-type-modal.active {
            display: flex;
        }

        .segment-type-modal-content {
            background: var(--surface1);
            border: 1px solid rgba(168, 85, 247, 0.3);
            border-radius: 15px;
            padding: 30px;
            max-width: 400px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .segment-type-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 20px;
            text-align: center;
        }

        .segment-type-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .segment-type-btn {
            padding: 14px 20px;
            background: linear-gradient(135deg, rgba(42, 54, 84, 0.6), rgba(31, 43, 74, 0.6));
            border: 1px solid rgba(168, 85, 247, 0.3);
            border-radius: 10px;
            color: var(--text);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: left;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .segment-type-btn:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.4), rgba(168, 85, 247, 0.3));
            border-color: rgba(168, 85, 247, 0.6);
        }

        .segment-type-btn-icon {
            font-size: 20px;
        }

        .segment-type-btn-text {
            flex: 1;
        }

        .segment-type-btn-desc {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        .quiz-question {
            margin-bottom: 16px;
        }

        .quiz-question-input {
            width: 100%;
            padding: 12px 16px;
            background: linear-gradient(135deg, rgba(42, 54, 84, 0.6), rgba(31, 43, 74, 0.6));
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 8px;
            color: var(--text);
            font-size: 14px;
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .quiz-question-input:focus {
            outline: none;
            border-color: rgba(168, 85, 247, 0.6);
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.15);
        }

        .quiz-answers {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 12px;
        }

        .quiz-answer {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            background: var(--surface3);
            border: 1px solid var(--border);
            border-radius: 6px;
        }

        .quiz-answer.correct {
            border-color: var(--green);
            background: rgba(34, 197, 94, 0.1);
        }

        .quiz-answer-radio {
            flex-shrink: 0;
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        .quiz-answer-input {
            flex: 1;
            padding: 8px 12px;
            background: linear-gradient(135deg, rgba(42, 54, 84, 0.5), rgba(31, 43, 74, 0.5));
            border: 1px solid rgba(168, 85, 247, 0.15);
            border-radius: 4px;
            color: var(--text);
            font-size: 13px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .quiz-answer-input:focus {
            outline: none;
            border-color: rgba(168, 85, 247, 0.4);
            box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.1);
        }

        .quiz-add-answer {
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }

        .quiz-add-answer:hover {
            background: var(--accent-hover);
        }

        .video-url-input {
            width: 100%;
            padding: 10px 14px;
            background: linear-gradient(135deg, rgba(42, 54, 84, 0.6), rgba(31, 43, 74, 0.6));
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 6px;
            color: var(--text);
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .video-url-input:focus {
            outline: none;
            border-color: rgba(168, 85, 247, 0.6);
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
        }

        .quiz-answer-remove {
            padding: 6px;
            background: transparent;
            border: none;
            color: var(--red);
            cursor: pointer;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quiz-answer-remove:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .quiz-hint {
            font-size: 12px;
            color: var(--muted);
            margin-top: 8px;
        }

        body { display:flex; flex-direction:column; }
        body.nav-open { overflow:hidden; }
        .app-shell { display:flex; flex:1; min-height:100vh; }
        .nav-overlay {
            position: fixed;
            inset: 0;
            background: rgba(3, 10, 18, 0.72);
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
            z-index: 180;
        }
        .nav-sidebar {
            width: 252px;
            flex-shrink: 0;
            background: linear-gradient(180deg, color-mix(in srgb, var(--surface) 96%, transparent), color-mix(in srgb, var(--s1) 94%, transparent));
            border-right: 1px solid var(--b0);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 200;
            transition: transform .22s ease, box-shadow .22s ease, background .3s ease;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.02);
        }
        .nav-sidebar::after {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(180deg, rgba(59, 158, 255, 0.07), transparent 24%);
            opacity: .8;
        }
        .nsb-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 1px solid var(--b0);
            padding-right: 12px;
            position: relative;
            z-index: 1;
        }
        .nsb-brand {
            display:flex;
            align-items:center;
            gap:10px;
            padding:18px 16px 16px;
            border-bottom:none;
            flex: 1;
            min-width: 0;
        }
        .nsb-mark { width:30px; height:30px; border-radius:7px; background:linear-gradient(135deg,var(--blue-dim),var(--blue)); display:grid; place-items:center; font-size:11px; font-weight:800; color:white; letter-spacing:-0.04em; flex-shrink:0; box-shadow:0 4px 12px var(--blue-glow); }
        .nsb-name { font-size:15px; font-weight:800; letter-spacing:-0.04em; color:var(--tx); }
        .nsb-name span { color:var(--blue); }
        .nsb-close,
        .ptb-menu {
            appearance: none;
            -webkit-appearance: none;
            border: 1px solid var(--b1);
            background: var(--s1);
            color: var(--tx2);
            border-radius: 10px;
            width: 38px;
            height: 38px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s ease;
            flex-shrink: 0;
        }
        .nsb-close:hover,
        .ptb-menu:hover {
            background: var(--s2);
            border-color: var(--b2);
            color: var(--tx);
        }
        .nsb-nav {
            flex: 1;
            overflow-y: auto;
            padding: 14px 10px 16px;
            position: relative;
            z-index: 1;
        }
        .nsb-grp {
            font-size:10px;
            font-weight:700;
            letter-spacing:0.1em;
            text-transform:uppercase;
            color:var(--muted);
            padding:14px 10px 6px;
        }
        .nsb-a {
            display:flex;
            align-items:center;
            gap:10px;
            padding:10px 12px;
            border-radius:12px;
            font-size:13.5px;
            font-weight:500;
            color:var(--tx2);
            text-decoration:none;
            transition:all .15s ease;
            margin-bottom:4px;
            position:relative;
            border:1px solid transparent;
        }
        .nsb-a:hover {
            background: color-mix(in srgb, var(--s2) 88%, transparent);
            border-color: var(--b0);
            color:var(--tx);
            transform: translateX(2px);
        }
        .nsb-a.active {
            background:var(--blue-soft);
            border-color: rgba(59, 158, 255, 0.16);
            color:var(--tx);
            box-shadow: inset 0 0 0 1px rgba(59, 158, 255, 0.05);
        }
        .nsb-a.active::before { content:''; position:absolute; left:0; top:22%; bottom:22%; width:3px; border-radius:0 3px 3px 0; background:var(--blue); }
        .nsb-a svg {
            flex-shrink:0;
            opacity:.8;
            transition:opacity .15s ease, background .15s ease, border-color .15s ease;
            width: 18px;
            height: 18px;
            padding: 2px;
            border-radius: 7px;
            background: color-mix(in srgb, var(--s2) 88%, transparent);
            border: 1px solid rgba(56, 139, 220, 0.08);
        }
        .nsb-a:hover svg,
        .nsb-a.active svg {
            opacity:1;
            background: color-mix(in srgb, var(--blue-soft) 76%, var(--s1));
            border-color: rgba(59, 158, 255, 0.16);
        }
        .nsb-foot {
            padding:12px 10px calc(12px + env(safe-area-inset-bottom, 0px));
            border-top:1px solid var(--b0);
            position: relative;
            z-index: 1;
        }
        .nsb-user {
            display:flex;
            align-items:center;
            gap:9px;
            padding:10px 12px;
            border-radius:12px;
            cursor:pointer;
            transition:all .15s ease;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.01);
        }
        .nsb-user:hover {
            background:var(--s2);
            border-color: var(--b1);
        }
        .nsb-av { width:30px; height:30px; border-radius:7px; background:linear-gradient(135deg,var(--blue-dim),var(--blue)); display:grid; place-items:center; font-size:12px; font-weight:700; color:white; flex-shrink:0; overflow:hidden; }
        .nsb-av img { width:100%; height:100%; object-fit:cover; }
        .nsb-uinfo { flex:1; min-width:0; }
        .nsb-uname  { font-size:12.5px; font-weight:600; color:var(--tx); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .nsb-uemail { font-size:11px; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .page-main { margin-left:252px; flex:1; display:flex; flex-direction:column; min-width:0; }
        .page-topbar {
            min-height: 68px;
            display:flex;
            align-items:center;
            justify-content: space-between;
            padding:14px 24px;
            border-bottom:1px solid var(--b0);
            background:color-mix(in srgb, var(--bg) 84%, transparent);
            backdrop-filter:blur(16px);
            position:sticky;
            top:0;
            z-index:170;
            gap:16px;
            transition:background 0.3s;
        }
        .ptb-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .ptb-copy {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }
        .ptb-kicker {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .ptb-title {
            font-size:15px;
            font-weight:700;
            color:var(--tx);
            letter-spacing:-.02em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ptb-right { margin-left:auto; display:flex; align-items:center; gap:8px; }
        .ptb-btn   { width:30px; height:30px; border-radius:7px; border:1px solid var(--b1); background:var(--s1); color:var(--tx2); display:grid; place-items:center; cursor:pointer; transition:all .15s; }
        .ptb-btn:hover { background:var(--s2); border-color:var(--b2); color:var(--tx); }
        .ptb-signout { display:flex; align-items:center; gap:6px; padding:6px 13px; background:transparent; border:1px solid var(--b1); color:var(--muted); border-radius:7px; font-family:"Roboto",sans-serif; font-size:12.5px; font-weight:500; cursor:pointer; transition:all .15s; }
        .ptb-signout:hover { background:var(--red-soft); border-color:rgba(240,80,80,.25); color:var(--red); }
        @media (max-width: 1100px) {
            .container { padding: 28px 18px 40px; }
            .main-wrapper { grid-template-columns: 260px 1fr; }
            .lesson-template-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 900px) {
            .main-wrapper {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: static;
                max-height: none;
            }
            .course-outline {
                min-height: 0;
                max-height: none;
            }
            .nav-overlay {
                opacity: 0;
                pointer-events: none;
            }
            body.nav-open .nav-overlay {
                opacity: 1;
                pointer-events: auto;
            }
            .nav-sidebar {
                transform: translateX(-100%);
                box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45);
            }
            body.nav-open .nav-sidebar {
                transform: translateX(0);
            }
            .page-main { margin-left:0; }
            .ptb-menu,
            .nsb-close { display:inline-flex; }
            .page-topbar { padding: 12px 16px; }
        }
        @media (max-width: 680px) {
            .container { padding: 20px 14px 32px; }
            .lesson-template-grid { grid-template-columns: 1fr; }
            .page-topbar {
                align-items: flex-start;
                flex-wrap: wrap;
            }
            .ptb-right {
                width: 100%;
                justify-content: flex-end;
            }
        }

    </style>
</head>
<body>

<div class="app-shell">
    <aside class="nav-sidebar" id="builderSidebar">
        <div class="nsb-top">
            @include('partials.app.sidebar-brand', [
                'wrapperClass' => 'nsb-brand',
                'markClass' => 'nsb-mark',
                'nameClass' => 'nsb-name',
            ])
            <button type="button" class="nsb-close" data-builder-nav-close aria-label="Close navigation">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <nav class="nsb-nav">
            @include('partials.app.nav-links', [
                'linkClass' => 'nsb-a',
                'navGroupClass' => 'nsb-grp',
                'activeKey' => 'create',
                'showSettings' => true,
                'settingsId' => 'settingsBtn',
                'settingsGroupStyle' => 'margin-top:8px;',
            ])
        </nav>
        <div class="nsb-foot">
            @include('partials.app.user-summary', [
                'wrapperClass' => 'nsb-user',
                'avatarClass' => 'nsb-av',
                'infoClass' => 'nsb-uinfo',
                'nameClass' => 'nsb-uname',
                'emailClass' => 'nsb-uemail',
                'avatarAlt' => 'av',
            ])
        </div>
    </aside>
    <button type="button" class="nav-overlay" id="builderNavOverlay" data-builder-nav-close aria-label="Close navigation"></button>
    <div class="page-main">
        <header class="page-topbar">
            <div class="ptb-left">
                <button
                    type="button"
                    class="ptb-menu"
                    data-builder-nav-toggle
                    aria-controls="builderSidebar"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div class="ptb-copy">
                    <span class="ptb-kicker">{{ __('lessons.my_lessons') }}</span>
                    <span class="ptb-title">{{ __('lessons.create_lesson') }}</span>
                </div>
            </div>
            <div class="ptb-right">
                @include('partials.app.settings-button', [
                    'buttonClass' => 'ptb-btn',
                    'buttonId' => 'settingsBtn2',
                    'title' => __('dashboard.settings'),
                ])
                @include('partials.app.logout-button', ['buttonClass' => 'ptb-signout'])
            </div>
        </header>
    <div class="container">
        <div class="header">
            <h1 class="page-title">{{ __('lessons.create_lesson') }}</h1>
            <div class="header-actions">
                <button type="button" class="btn btn-secondary learner-preview-open" id="openLearnerPreviewBtn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    {{ __('lessons.learner_preview') }}
                </button>
                <a href="{{ route('lessons.index') }}" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    {{ __('lessons.back') }}
                </a>
            </div>
        </div>

        <form action="{{ route('lessons.store') }}" method="POST" enctype="multipart/form-data" id="lessonForm">
            @csrf
            <div class="draft-recovery" id="draftRecoveryPanel">
                <div class="draft-recovery-copy">
                    <div class="draft-recovery-title">{{ __('lessons.draft_recovery_available') }}</div>
                    <div class="draft-recovery-meta" id="draftRecoveryMeta">{{ __('lessons.draft_recovery_message') }}</div>
                </div>
                <div class="draft-recovery-actions">
                    <button type="button" class="btn btn-secondary" id="restoreDraftBtn">{{ __('lessons.restore_draft') }}</button>
                    <button type="button" class="btn btn-secondary" id="discardDraftBtn">{{ __('lessons.discard_draft') }}</button>
                </div>
            </div>

            @include('partials.lessons.lesson-template-picker')

            <div class="segment-type-modal" id="segmentTypeModal">
                <div class="segment-type-modal-content">
                    <div class="segment-type-title">{{ __('lessons.segment_type_select') }}</div>
                    <div class="segment-type-options">
                        <button type="button" class="segment-type-btn" onclick="selectSegmentType('content')">
                            <div class="segment-type-btn-icon">C</div>
                            <div>
                                <div class="segment-type-btn-text">{{ __('lessons.content_segment') }}</div>
                                <div class="segment-type-btn-desc">{{ __('lessons.content_segment_desc') }}</div>
                            </div>
                        </button>
                        <button type="button" class="segment-type-btn" onclick="selectSegmentType('exam')">
                            <div class="segment-type-btn-icon">E</div>
                            <div>
                                <div class="segment-type-btn-text">{{ __('lessons.exam_segment') }}</div>
                                <div class="segment-type-btn-desc">{{ __('lessons.exam_segment_desc') }}</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <div class="main-wrapper">
                <div class="sidebar">
                    <div class="sidebar-quickbar">
                        <button type="button" class="add-segment-btn" id="addSegmentBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            + {{ __('lessons.add_segment') }}
                        </button>

                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-value" id="segmentCount">3</div>
                                <div class="stat-label">{{ __('lessons.segments') }}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value" id="blockCount">0</div>
                                <div class="stat-label">{{ __('lessons.blocks') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-nav-panel">
                        <div class="course-outline">
                            <div class="outline-title">{{ __('lessons.course_structure') }}</div>
                            <div id="courseOutline"></div>
                        </div>
                    </div>
                </div>

                <div class="content-area">
                    <div class="segment-container active" data-segment="0">
                        <h2 class="segment-header">{{ __('lessons.basic_info') }}</h2>
                        <div class="form-group">
                            <label class="form-label" for="title">{{ __('lessons.title') }}</label>
                            <input type="text" id="title" name="title" class="form-input" value="{{ old('title') }}" placeholder="{{ __('lessons.title_placeholder') }}" required>
                            <p class="form-error" id="titleError" style="display: none;"></p>
                            @error('title')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="type">{{ __('lessons.type') }}</label>
                            <select id="type" name="type" class="form-select" required>
                                <option value="">{{ __('lessons.select_type') }}</option>
                                <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>{{ __('lessons.video') }}</option>
                                <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>{{ __('lessons.text') }}</option>
                                <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>{{ __('lessons.document_type') }}</option>
                            </select>
                            @error('type')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group" id="video-url-group" style="display: none;">
                            <label class="form-label" for="video_url">{{ __('lessons.video_url') }}</label>
                            <input type="url" id="video_url" name="video_url" class="form-input" value="{{ old('video_url') }}" placeholder="{{ __('lessons.video_url_placeholder') }}">
                            <p class="form-help">{{ __('lessons.video_help') }}</p>
                            @error('video_url')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group" id="document-group" style="display: none;">
                            <label class="form-label" for="document">{{ __('lessons.document') }}</label>
                            <div class="file-input-wrapper">
                                <input type="file" id="document" name="document" class="file-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" data-existing-document="0" data-existing-document-url="">
                                <label for="document" class="file-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    {{ __('lessons.choose_file') }}
                                </label>
                                <p class="file-name" id="document-name">{{ __('lessons.no_file_chosen') }}</p>
                            </div>
                            <p class="form-help">{{ __('lessons.document_help') }}</p>
                            @error('document')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="thumbnail">{{ __('lessons.thumbnail') }}</label>
                            <div class="file-input-wrapper">
                                <input type="file" id="thumbnail" name="thumbnail" class="file-input" accept="image/*" data-existing-thumbnail-url="">
                                <label for="thumbnail" class="file-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                    {{ __('lessons.choose_file') }}
                                </label>
                                <p class="file-name" id="thumbnail-name">{{ __('lessons.no_file_chosen') }}</p>
                                <img id="thumbnail-preview" class="preview-image" style="display: none;">
                            </div>
                            <p class="form-help">{{ __('lessons.thumbnail_help') }}</p>
                            @error('thumbnail')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('lessons.duration') }}</label>
                            <div class="duration-inputs">
                                <div class="input-group">
                                    <span class="input-label">{{ __('lessons.hours') }}:</span>
                                    <input type="number" name="duration_hours" class="form-input" value="{{ old('duration_hours', 0) }}" min="0" max="99">
                                </div>
                                <div class="input-group">
                                    <span class="input-label">{{ __('lessons.minutes') }}:</span>
                                    <input type="number" name="duration_minutes" class="form-input" value="{{ old('duration_minutes', 0) }}" min="0" max="59">
                                </div>
                            </div>
                            @error('duration_hours')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            @error('duration_minutes')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="difficulty">{{ __('lessons.difficulty') }}</label>
                            <select id="difficulty" name="difficulty" class="form-select">
                                <option value="">--</option>
                                <option value="beginner" {{ old('difficulty') == 'beginner' ? 'selected' : '' }}>{{ __('lessons.beginner') }}</option>
                                <option value="intermediate" {{ old('difficulty') == 'intermediate' ? 'selected' : '' }}>{{ __('lessons.intermediate') }}</option>
                                <option value="advanced" {{ old('difficulty') == 'advanced' ? 'selected' : '' }}>{{ __('lessons.advanced') }}</option>
                            </select>
                            @error('difficulty')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="subject">{{ __('lessons.subject') }}</label>
                            <select id="subject" name="subject" class="form-select">
                                @php($selectedSubject = \App\Models\Lesson::normalizeSubject(old('subject', \App\Models\Lesson::defaultSubject())))
                                @foreach(\App\Models\Lesson::subjectOptions() as $subjectOption)
                                    <option value="{{ $subjectOption }}" {{ $selectedSubject === $subjectOption ? 'selected' : '' }}>
                                        {{ __('lessons.subject_' . $subjectOption) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="is_published" name="is_published" class="checkbox-input" value="1" {{ old('is_published') ? 'checked' : '' }}>
                                <label class="form-label" for="is_published" style="margin: 0;">{{ __('lessons.is_published') }}</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="is_free" name="is_free" class="checkbox-input" value="1" {{ old('is_free') ? 'checked' : '' }}>
                                <label class="form-label" for="is_free" style="margin: 0;">{{ __('lessons.is_free') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="segment-container active" data-segment="1">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <h2 class="segment-header" style="margin: 0;">{{ __('lessons.content_blocks') }} 1</h2>
                        </div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label">{{ __('lessons.segment_name') }}</label>
                            <input type="text" name="segments[1][custom_name]" class="form-input segment-name-input" placeholder="{{ __('lessons.segment_name_placeholder') }}" data-segment-id="1">
                        </div>

                        <div class="content-builder">
                            <div class="builder-header">
                                <h3 class="builder-title">{{ __('lessons.content_blocks') }}</h3>
                            </div>

                            <div id="contentBlocks" class="content-blocks">
                                <div class="empty-builder">
                                    <div class="empty-builder-icon">B</div>
                                    <p>{{ __('lessons.no_content') }}</p>
                                </div>
                            </div>

                            <div class="builder-actions">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="addTextBlock()">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    {{ __('lessons.block_text') }}
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="addSubheadingBlock()">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <text x="4" y="16" font-size="12" font-weight="700">H</text>
                                    </svg>
                                    {{ __('lessons.block_subheading') }}
                                </button>
                                <div class="content-dropdown">
                                    <button type="button" class="btn btn-secondary btn-sm">{{ __('lessons.add_content') }} v</button>
                                    <div class="dropdown-menu">
                                        <button type="button" onclick="addImageBlock()">{{ __('lessons.block_image') }}</button>
                                        <button type="button" onclick="addVideoBlock()">{{ __('lessons.block_video') }}</button>
                                        <button type="button" onclick="addFileBlock()">{{ __('lessons.block_file') }}</button>
                                        <button type="button" onclick="addCalloutBlock()">{{ __('lessons.block_callout') }}</button>
                                        <button type="button" onclick="addCodeBlock()">{{ __('lessons.block_code') }}</button>
                                        <button type="button" onclick="addDividerBlock()">{{ __('lessons.block_divider') }}</button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="addQuizBlock()">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                    </svg>
                                    {{ __('lessons.block_quiz') }}
                                </button>
                            </div>
                        </div>

                        <div class="segment-actions">
                            <button type="button" class="segment-remove-btn" onclick="removeSegment(1)">{{ __('lessons.remove_segment') }}</button>
                        </div>
                    </div>

                    <div class="builder-footer-panel">
                        @include('partials.lessons.publish-checklist')

                        <div class="form-actions">
                            <div class="draft-status" id="draftStatus"></div>
                            <button type="submit" class="btn btn-secondary" name="save_action" value="draft" formnovalidate>
                                {{ __('lessons.save_draft') }}
                            </button>
                            <button type="submit" class="btn btn-success">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                {{ __('lessons.create') }}
                            </button>
                            <a href="{{ route('lessons.index') }}" class="btn btn-secondary">{{ __('lessons.back') }}</a>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    @include('partials.lessons.learner-preview-modal')

    <script>
        const lessonDraftCleanupKeys = @json(session('clear_lesson_draft_keys', []));

        const i18n = {
            noContent: '{{ __('lessons.no_content') }}',
            noQuestions: '{{ __('lessons.no_questions') }}',
            cannotRemoveBasic: '{{ __('lessons.cannot_remove_basic') }}',
            confirmRemoveSegment: '{{ __('lessons.confirm_remove_segment') }}',
            addQuestion: '{{ __('lessons.add_question') }}',
            contentSegment: '{{ __('lessons.content_blocks') }}',
            examSegment: '{{ __('lessons.exam_segment') }}',
            basicInfo: '{{ __('lessons.basic_info') }}',
            questionMultipleChoice: '{{ __('lessons.question_multiple_choice') }}',
            questionTrueFalse: '{{ __('lessons.question_true_false') }}',
            questionShortAnswer: '{{ __('lessons.question_short_answer') }}',
            trueAnswer: '{{ __('lessons.true_answer') }}',
            falseAnswer: '{{ __('lessons.false_answer') }}',
            removeQuestion: '{{ __('lessons.remove_question') }}',
            removeSegment: '{{ __('lessons.remove_segment') }}',
            addAnswer: '{{ __('lessons.add_answer') }}',
            correctAnswer: '{{ __('lessons.correct_answer') }}',
            answers: '{{ __('lessons.answers') }}',
            answersInstruction: '{{ __('lessons.answers_instruction') }}',
            correctAnswerHint: '{{ __('lessons.correct_answer_hint') }}',
            caseSensitive: '{{ __('lessons.case_sensitive') }}',
            questionPlaceholder: '{{ __('lessons.question_placeholder') }}',
            answerCorrectPlaceholder: '{{ __('lessons.answer_correct_placeholder') }}',
            contentPlaceholderText: '{{ __('lessons.content_placeholder_text') }}',
            contentPlaceholderSubheading: '{{ __('lessons.content_placeholder_subheading') }}',
            contentPlaceholderCallout: '{{ __('lessons.content_placeholder_callout') }}',
            moveUp: '{{ __('lessons.move_up') }}',
            moveDown: '{{ __('lessons.move_down') }}',
            segmentNamePlaceholder: '{{ __('lessons.segment_name_placeholder') }}',
            blockText: '{{ __('lessons.block_text') }}',
            blockSubheading: '{{ __('lessons.block_subheading') }}',
            blockImage: '{{ __('lessons.block_image') }}',
            blockVideo: '{{ __('lessons.block_video') }}',
            blockFile: '{{ __('lessons.block_file') }}',
            blockCallout: '{{ __('lessons.block_callout') }}',
            blockCode: '{{ __('lessons.block_code') }}',
            blockDivider: '{{ __('lessons.block_divider') }}',
            blockQuiz: '{{ __('lessons.block_quiz') }}',
            addContent: '{{ __('lessons.add_content') }}',
            blockImageSelect: '{{ __('lessons.block_image_select') }}',
            blockFileSelect: '{{ __('lessons.block_file_select') }}',
            videoUrlLabel: '{{ __('lessons.video_url_label') }}',
            imageCaptionLabel: '{{ __('lessons.image_caption_label') }}',
            calloutTypeLabel: '{{ __('lessons.callout_type_label') }}',
            codeLanguageLabel: '{{ __('lessons.code_language_label') }}',
            dividerLabel: '{{ __('lessons.divider_label') }}',
            examIndexLabel: '{{ __('lessons.exam_index_label') }}',
            answersInstructionChoose: '{{ __('lessons.answers_instruction_choose') }}',
            minAnswersValidation: '{{ __('lessons.min_answers_validation') }}',
            addContentEmpty: '{{ __('lessons.add_content_empty') }}',
            placeholderAnswer: '{{ __('lessons.placeholder_answer') }}',
            placeholderFileNotChosen: '{{ __('lessons.placeholder_file_not_chosen') }}',
            examSettings: '{{ __('lessons.exam_settings') }}',
            timeLimit: '{{ __('lessons.time_limit') }}',
            timeLimitHelp: '{{ __('lessons.time_limit_help') }}',
            passingScore: '{{ __('lessons.passing_score') }}',
            examQuestions: '{{ __('lessons.exam_questions') }}',
            segmentName: '{{ __('lessons.segment_name') }}',
            noFileChosen: '{{ __('lessons.no_file_chosen') }}',
            question: '{{ __('lessons.question') }}',
            calloutInfo: '{{ __('lessons.callout_info') }}',
            calloutWarning: '{{ __('lessons.callout_warning') }}',
            calloutSuccess: '{{ __('lessons.callout_success') }}',
            calloutDanger: '{{ __('lessons.callout_danger') }}',
            calloutType: '{{ __('lessons.callout_type') }}',
            codeLanguage: '{{ __('lessons.code_language') }}',
            codePlaceholder: '{{ __('lessons.code_placeholder') }}',
            titleDuplicate: '{{ __('lessons.title_duplicate') }}',
            validating: '{{ __('lessons.validating') }}',
            draftRecoveryMessage: '{{ __('lessons.draft_recovery_message') }}',
            localDraftSaved: '{{ __('lessons.local_draft_saved') }}',
            localDraftRestored: '{{ __('lessons.local_draft_restored') }}',
            localDraftDiscarded: '{{ __('lessons.local_draft_discarded') }}',
            localDraftFilesNotice: '{{ __('lessons.local_draft_files_notice') }}',
            draftStatusReady: '{{ __('lessons.draft_status_ready') }}',
            draftStatusUnsaved: '{{ __('lessons.draft_status_unsaved') }}',
            draftStatusSaving: '{{ __('lessons.draft_status_saving') }}',
            draftStatusSaved: '{{ __('lessons.draft_status_saved') }}',
            draftStatusSavedJustNow: '{{ __('lessons.draft_status_saved_just_now') }}',
            draftStatusRestored: '{{ __('lessons.draft_status_restored') }}',
            draftStatusRestoredSaved: '{{ __('lessons.draft_status_restored_saved') }}',
            draftStatusDiscarded: '{{ __('lessons.draft_status_discarded') }}',
            draftStatusError: '{{ __('lessons.draft_status_error') }}',
            publishChecklistReady: '{{ __('lessons.publish_checklist_ready') }}',
            publishChecklistRemaining: '{{ __('lessons.publish_checklist_remaining', ['count' => '__COUNT__']) }}',
            publishChecklistDone: '{{ __('lessons.publish_checklist_done') }}',
            publishChecklistMissing: '{{ __('lessons.publish_checklist_missing') }}',
            learnerPreview: '{{ __('lessons.learner_preview') }}',
            learnerView: '{{ __('lessons.learner_view') }}',
            previewCurrentDraft: '{{ __('lessons.preview_current_draft') }}',
            previewUsesDraftState: '{{ __('lessons.preview_uses_draft_state') }}',
            closePreview: '{{ __('lessons.close_preview') }}',
            untitledDraft: '{{ __('lessons.untitled_draft') }}',
            min: '{{ __('lessons.min') }}',
            sections: '{{ __('lessons.sections') }}',
            exams: '{{ __('lessons.exams') }}',
            subject: '{{ __('lessons.subject') }}',
            type: '{{ __('lessons.type') }}',
            difficulty: '{{ __('lessons.difficulty') }}',
            duration: '{{ __('lessons.duration') }}',
            isFreeLabel: '{{ __('lessons.is_free') }}',
            isPublishedLabel: '{{ __('lessons.is_published') }}',
            published: '{{ __('lessons.published') }}',
            draft: '{{ __('lessons.draft') }}',
            freeBadge: '{{ __('lessons.free_badge') }}',
            paid: '{{ __('lessons.paid') }}',
            lessonProgress: '{{ __('lessons.lesson_progress') }}',
            downloadDocument: '{{ __('lessons.download_document') }}',
            downloadFile: '{{ __('lessons.download_file') }}',
            openVideo: '{{ __('lessons.open_video') }}',
            examMode: '{{ __('lessons.exam_mode') }}',
            examContains: '{{ __('lessons.exam_contains', ['count' => ':count']) }}',
            passLabel: '{{ __('lessons.pass_label') }}',
            timeLabel: '{{ __('lessons.time_label') }}',
            questionsLabel: '{{ __('lessons.questions_label') }}',
            startExam: '{{ __('lessons.start_exam') }}',
            duplicateSegment: '{{ __('lessons.duplicate_segment') }}',
            duplicateBlock: '{{ __('lessons.duplicate_block') }}',
            duplicateQuestion: '{{ __('lessons.duplicate_question') }}',
        };

        let blockCounter = 0;
        let segmentCounter = 2;
        let contentSegmentIndex = 1;

        let segments = [
            { id: 0, label: `${i18n.basicInfo}`, type: 'basic' },
            { id: 1, label: `${i18n.contentSegment} 1`, type: 'content'}
        ];

        @include('partials.lessons.builder-segment-helpers')

        document.addEventListener('DOMContentLoaded', function() {
            initializeSegments();
            bindSharedBuilderEvents();
            bindLessonBuilderFormHelpers({
                titleCheckUrl: @js(route('lessons.check-title')),
                validatingMessage: i18n.validating,
                duplicateMessage: i18n.titleDuplicate,
                minLengthMessage: @js(__('validation.min.string', ['attribute' => __('lessons.title'), 'min' => 3])),
            });
            bindLessonDraftSupport({
                storageKey: 'lesson-builder-draft:create',
                clearStorageKeysOnLoad: lessonDraftCleanupKeys,
                autosaveUrl: @js(route('lessons.autosave')),
                lessonStorageKeyPrefix: 'lesson-builder-draft:edit:',
            });
        });

        @include('partials.lessons.builder-create-segment-factories')

        @include('partials.lessons.builder-exam-question-helpers')

        @include('partials.lessons.builder-form-helpers')

        @include('partials.lessons.builder-create-block-factories')

        @include('partials.lessons.builder-duplicate-helpers')

        @include('partials.lessons.builder-template-helpers')

        @include('partials.lessons.builder-draft-helpers')
    </script>
    <script>
        @include('partials.lessons.builder-learner-preview-helpers')
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const body = document.body;
            const toggleButtons = document.querySelectorAll('[data-builder-nav-toggle]');
            const closeButtons = document.querySelectorAll('[data-builder-nav-close]');

            if (!toggleButtons.length) {
                return;
            }

            const setNavState = (isOpen) => {
                body.classList.toggle('nav-open', isOpen);
                toggleButtons.forEach((button) => {
                    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
            };

            toggleButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    setNavState(!body.classList.contains('nav-open'));
                });
            });

            closeButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    setNavState(false);
                });
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 900 && body.classList.contains('nav-open')) {
                    setNavState(false);
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && body.classList.contains('nav-open')) {
                    setNavState(false);
                }
            });
        });
    </script>
    </div>
</div>

    @include('partials.app.settings-panel')

</body>
</html>
