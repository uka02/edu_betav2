<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title>{{ __('auth.choose_account_type') }} - EduDev</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:          #080b12;
            --surface:     #0f1219;
            --surface2:    #161b26;
            --border:      rgba(255,255,255,0.07);
            --text:        #eef0f6;
            --muted:       #5a6070;
            --muted2:      #8a92a0;
            --accent:      #4f8ef7;
            --accent-glow: rgba(79,142,247,0.2);
            --purple:      #8b5cf6;
            --green:       #22c55e;
        }

        [data-theme="light"] {
            --bg:          #eef3f8;
            --surface:     #ffffff;
            --surface2:    #f8fbff;
            --border:      rgba(15,23,42,0.08);
            --text:        #0f172a;
            --muted:       #64748b;
            --muted2:      #475569;
            --accent:      #2563eb;
            --accent-glow: rgba(37,99,235,0.16);
            --purple:      #7c3aed;
            --green:       #16a34a;
        }

        html, body { height: 100%; }

        body {
            font-family: "Roboto", sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow: hidden;
            position: relative;
        }

        [data-theme="light"] .bg-grid {
            background-image: radial-gradient(rgba(15,23,42,0.05) 1px,transparent 1px);
        }

        .bg-orb { position:fixed; border-radius:50%; filter:blur(90px); pointer-events:none; animation:drift 14s ease-in-out infinite alternate; }
        .bg-orb-1 { width:600px; height:600px; background:radial-gradient(circle,rgba(79,142,247,0.07),transparent 70%); top:-150px; left:-150px; }
        .bg-orb-2 { width:500px; height:500px; background:radial-gradient(circle,rgba(139,92,246,0.06),transparent 70%); bottom:-100px; right:-100px; animation-delay:-6s; }
        @keyframes drift { 0%{transform:translate(0,0);} 100%{transform:translate(30px,20px);} }
        .bg-grid { position:fixed; inset:0; background-image:radial-gradient(rgba(255,255,255,0.025) 1px,transparent 1px); background-size:30px 30px; pointer-events:none; }

        .page-wrap {
            display: flex;
            width: 100%;
            max-width: 940px;
            min-height: 560px;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 40px 100px rgba(0,0,0,0.6);
            position: relative;
            z-index: 1;
            animation: fadeUp .6s cubic-bezier(.16,1,.3,1) both;
        }

        [data-theme="light"] .page-wrap {
            box-shadow: 0 28px 70px rgba(15,23,42,0.14);
        }

        @keyframes fadeUp { from{opacity:0;transform:translateY(28px);} to{opacity:1;transform:translateY(0);} }

        .panel-left {
            flex: 1;
            background: linear-gradient(150deg, #0c1728 0%, #090e18 60%, #110c1e 100%);
            padding: 52px 44px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        [data-theme="light"] .panel-left {
            background: linear-gradient(150deg, #ffffff 0%, #f8fbff 58%, #eef5ff 100%);
        }

        .panel-left::before {
            content:'';
            position:absolute;
            inset:0;
            background:
                radial-gradient(ellipse 80% 50% at 20% 20%, rgba(79,142,247,0.1) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 90% 90%, rgba(139,92,246,0.08) 0%, transparent 60%);
            pointer-events:none;
        }

        [data-theme="light"] .panel-left::before {
            background:
                radial-gradient(ellipse 80% 50% at 20% 20%, rgba(37,99,235,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 90% 90%, rgba(124,58,237,0.08) 0%, transparent 60%);
        }

        .panel-inner { position: relative; z-index: 1; }

        .brand { display:flex; align-items:center; gap:11px; margin-bottom:56px; }
        .brand-link { color:inherit; text-decoration:none; width:fit-content; }
        .brand-icon {
            width:40px; height:40px;
            background:linear-gradient(135deg, var(--accent), var(--purple));
            border-radius:12px; display:grid; place-items:center; font-size:12px; font-weight:800; letter-spacing:.08em;
            box-shadow:0 4px 20px rgba(79,142,247,0.4);
        }
        .brand-name { font-size:20px; font-weight:800; letter-spacing:-0.03em; }
        .brand-tag { font-size:11px; color:var(--muted); background:var(--surface2); border:1px solid var(--border); padding:2px 8px; border-radius:99px; margin-left:4px; }

        .hero-headline {
            font-size:36px;
            font-weight:800;
            line-height:1.12;
            letter-spacing:-0.04em;
            margin-bottom:16px;
        }

        .hero-headline span {
            background:linear-gradient(135deg, var(--accent), var(--purple));
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
            background-clip:text;
        }

        .hero-sub {
            color:var(--muted2);
            font-size:15px;
            line-height:1.65;
            max-width:340px;
            margin-bottom:40px;
        }

        .feature-list {
            display:flex;
            flex-direction:column;
            gap:10px;
        }

        .feature-pill {
            display:flex;
            align-items:center;
            gap:10px;
            background:rgba(255,255,255,0.04);
            border:1px solid var(--border);
            border-radius:10px;
            padding:10px 14px;
            font-size:13px;
            color:var(--muted2);
        }

        [data-theme="light"] .feature-pill {
            background: rgba(15,23,42,0.03);
        }

        .feature-pill .dot {
            width:7px;
            height:7px;
            border-radius:50%;
            flex-shrink:0;
            background:var(--accent);
        }

        .panel-right {
            width: 420px;
            flex-shrink: 0;
            background: var(--surface);
            padding: 48px 40px;
            display:flex;
            flex-direction:column;
            justify-content:center;
        }

        .panel-title { font-size:23px; font-weight:700; letter-spacing:-0.03em; margin-bottom:6px; }
        .panel-sub { font-size:14px; color:var(--muted2); margin-bottom:26px; line-height:1.6; }

        .choice-stack {
            display:grid;
            gap:14px;
        }

        .choice-card {
            display:block;
            padding:18px 18px 16px;
            border-radius:16px;
            border:1px solid var(--border);
            background:rgba(255,255,255,0.03);
            color:inherit;
            text-decoration:none;
            transition:transform .2s ease, border-color .2s ease, background .2s ease, box-shadow .2s ease;
        }

        .choice-card:hover {
            transform:translateY(-2px);
            border-color:rgba(79,142,247,0.35);
            background:rgba(255,255,255,0.05);
            box-shadow:0 14px 30px rgba(0,0,0,0.18);
        }

        [data-theme="light"] .choice-card {
            background: rgba(15,23,42,0.03);
        }

        [data-theme="light"] .choice-card:hover {
            background: rgba(15,23,42,0.05);
            box-shadow: 0 16px 32px rgba(15,23,42,0.08);
        }

        .choice-card.accent {
            background:linear-gradient(135deg, rgba(79,142,247,0.14), rgba(107,163,248,0.08));
            border-color:rgba(79,142,247,0.24);
        }

        [data-theme="light"] .choice-card.accent {
            background: linear-gradient(135deg, rgba(37,99,235,0.12), rgba(96,165,250,0.08));
            border-color: rgba(37,99,235,0.18);
        }

        .choice-label {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:14px;
            font-size:16px;
            font-weight:700;
            margin-bottom:8px;
            letter-spacing:-0.02em;
        }

        .choice-arrow {
            color:var(--muted2);
            font-size:16px;
        }

        .choice-copy {
            color:var(--muted2);
            font-size:13px;
            line-height:1.6;
        }

        .back-link {
            display:inline-flex;
            width:fit-content;
            margin-top:20px;
            color:var(--accent);
            text-decoration:none;
            font-size:13px;
            font-weight:600;
        }

        .back-link:hover { text-decoration:underline; }

        @media(max-width:760px) {
            .panel-left { display:none; }
            .page-wrap { max-width:460px; min-height:auto; }
            .panel-right { width:100%; padding:36px 28px; }
        }
    </style>
</head>
<body>
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>
    <div class="bg-grid"></div>

    <div class="page-wrap">
        <div class="panel-left">
            <div class="panel-inner">
                <a href="{{ route('home') }}" class="brand brand-link">
                    <div class="brand-icon">ED</div>
                    <span class="brand-name">EduDev</span>
                    <span class="brand-tag">{{ __('auth.beta') }}</span>
                </a>

                <h1 class="hero-headline">{!! __('auth.choose_path_headline') !!}</h1>
                <p class="hero-sub">{{ __('auth.choose_path_subtitle') }}</p>
            </div>

            <div class="feature-list">
                <div class="feature-pill">
                    <span class="dot"></span>
                    {{ __('auth.choose_path_feature_one') }}
                </div>
                <div class="feature-pill">
                    <span class="dot" style="background:#8b5cf6"></span>
                    {{ __('auth.choose_path_feature_two') }}
                </div>
                <div class="feature-pill">
                    <span class="dot" style="background:#22c55e"></span>
                    {{ __('auth.choose_path_feature_three') }}
                </div>
            </div>
        </div>

        <div class="panel-right">
            <h2 class="panel-title">{{ __('auth.choose_account_type') }}</h2>
            <p class="panel-sub">{{ __('auth.choose_account_type_subtitle') }}</p>

            <div class="choice-stack">
                <a href="{{ route('signup.learner') }}" class="choice-card accent">
                    <div class="choice-label">
                        <span>{{ __('auth.i_am_learner') }}</span>
                        <span class="choice-arrow">→</span>
                    </div>
                    <div class="choice-copy">{{ __('auth.i_am_learner_copy') }}</div>
                </a>

                <a href="{{ route('signup.educator') }}" class="choice-card">
                    <div class="choice-label">
                        <span>{{ __('auth.i_am_educator') }}</span>
                        <span class="choice-arrow">→</span>
                    </div>
                    <div class="choice-copy">{{ __('auth.i_am_educator_copy') }}</div>
                </a>

                <a href="{{ route('login') }}" class="choice-card">
                    <div class="choice-label">
                        <span>{{ __('auth.i_already_have_account') }}</span>
                        <span class="choice-arrow">→</span>
                    </div>
                    <div class="choice-copy">{{ __('auth.i_already_have_account_copy') }}</div>
                </a>
            </div>

            <a href="{{ route('home') }}" class="back-link">{{ __('auth.back_home') }}</a>
        </div>
    </div>

    @include('partials.app.settings-panel', ['showFloatingTrigger' => true])
</body>
</html>
