<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title>{{ __('auth.sign_in') }} — EduDev</title>
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
            --red:         #ef4444;
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
            --red:         #dc2626;
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

        /* Background */
        .bg-orb { position:fixed; border-radius:50%; filter:blur(90px); pointer-events:none; animation:drift 14s ease-in-out infinite alternate; }
        .bg-orb-1 { width:600px; height:600px; background:radial-gradient(circle,rgba(79,142,247,0.07),transparent 70%); top:-150px; left:-150px; }
        .bg-orb-2 { width:500px; height:500px; background:radial-gradient(circle,rgba(139,92,246,0.06),transparent 70%); bottom:-100px; right:-100px; animation-delay:-6s; }
        @keyframes drift { 0%{transform:translate(0,0);} 100%{transform:translate(30px,20px);} }

        .bg-grid { position:fixed; inset:0; background-image:radial-gradient(rgba(255,255,255,0.025) 1px,transparent 1px); background-size:30px 30px; pointer-events:none; }

        /* Layout */
        .page-wrap {
            display: flex;
            width: 100%; max-width: 920px;
            min-height: 560px;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 40px 100px rgba(0,0,0,0.6);
            position: relative; z-index: 1;
            animation: fadeUp .6s cubic-bezier(.16,1,.3,1) both;
        }

        [data-theme="light"] .page-wrap {
            box-shadow: 0 28px 70px rgba(15,23,42,0.14);
        }

        @keyframes fadeUp { from{opacity:0;transform:translateY(28px);} to{opacity:1;transform:translateY(0);} }

        /* Left hero panel */
        .panel-left {
            flex: 1;
            background: linear-gradient(150deg, #0c1728 0%, #090e18 60%, #110c1e 100%);
            padding: 52px 44px;
            display: flex; flex-direction: column; justify-content: space-between;
            position: relative; overflow: hidden;
        }

        [data-theme="light"] .panel-left {
            background: linear-gradient(150deg, #ffffff 0%, #f8fbff 58%, #eef5ff 100%);
        }

        .panel-left::before {
            content:''; position:absolute; inset:0;
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

        .panel-inner { position:relative; z-index:1; }

        .brand { display:flex; align-items:center; gap:11px; margin-bottom:60px; }
        .brand-link { color:inherit; text-decoration:none; width:fit-content; }
        .brand-icon {
            width:40px; height:40px;
            background:linear-gradient(135deg, var(--accent), var(--purple));
            border-radius:12px; display:grid; place-items:center; font-size:12px; font-weight:800; letter-spacing:.08em;
            box-shadow:0 4px 20px rgba(79,142,247,0.4);
        }
        .brand-name { font-family:'Roboto',sans-serif; font-size:20px; font-weight:800; letter-spacing:-0.03em; }
        .brand-tag { font-size:11px; color:var(--muted); background:var(--surface2); border:1px solid var(--border); padding:2px 8px; border-radius:99px; margin-left:4px; }

        .hero-headline {
            font-family: "Roboto", sans-serif;
            font-size:38px; font-weight:800; line-height:1.12; letter-spacing:-0.04em;
            margin-bottom:16px;
        }
        .hero-headline span {
            background:linear-gradient(135deg, var(--accent), var(--purple));
            -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
        }

        .hero-sub { color:var(--muted2); font-size:15px; line-height:1.65; max-width:320px; margin-bottom:44px; }

        /* Feature pills */
        .features { display:flex; flex-direction:column; gap:10px; }
        .feature-pill {
            display:flex; align-items:center; gap:10px;
            background:rgba(255,255,255,0.04); border:1px solid var(--border);
            border-radius:10px; padding:10px 14px; font-size:13px; color:var(--muted2);
        }

        [data-theme="light"] .feature-pill {
            background: rgba(15,23,42,0.03);
        }
        .feature-pill .dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }

        /* Right form panel */
        .panel-right {
            width: 400px; flex-shrink:0;
            background: var(--surface);
            padding: 48px 40px;
            display:flex; flex-direction:column; justify-content:center;
        }

        .form-title { font-family: "Roboto", sans-serif; font-size:23px; font-weight:700; letter-spacing:-0.03em; margin-bottom:6px; }
        .form-sub { font-size:14px; color:var(--muted2); margin-bottom:28px; }

        /* Alerts */
        .alert { display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:10px; font-size:13px; margin-bottom:18px; animation:slideIn .3s ease; }
        @keyframes slideIn { from{opacity:0;transform:translateY(-8px);} to{opacity:1;transform:translateY(0);} }
        .alert-error   { background:rgba(239,68,68,0.1);  border:1px solid rgba(239,68,68,0.2);  color:#fca5a5; }
        .alert-success { background:rgba(34,197,94,0.1);  border:1px solid rgba(34,197,94,0.2);  color:#86efac; }

        [data-theme="light"] .alert-error   { color: #b91c1c; background: rgba(220,38,38,0.08); border-color: rgba(220,38,38,0.16); }
        [data-theme="light"] .alert-success { color: #166534; background: rgba(22,163,74,0.08); border-color: rgba(22,163,74,0.16); }

        /* Google button */
        .btn-google {
            display:flex; align-items:center; justify-content:center; gap:12px;
            width:100%; padding:13px 20px;
            background:#fff; color:#1f2937;
            border:none; border-radius:12px;
            font-family:'Roboto',sans-serif; font-size:14px; font-weight:500;
            cursor:pointer; text-decoration:none;
            transition:all .2s; box-shadow:0 2px 12px rgba(0,0,0,0.25);
        }
        .btn-google:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.35); }
        .btn-google:active { transform:translateY(0); }

        [data-theme="light"] .btn-google {
            box-shadow: 0 10px 24px rgba(15,23,42,0.08);
        }

        [data-theme="light"] .btn-google:hover {
            box-shadow: 0 16px 30px rgba(15,23,42,0.12);
        }

        .divider { display:flex; align-items:center; gap:12px; margin:22px 0; color:var(--muted); font-size:11px; text-transform:uppercase; letter-spacing:0.1em; }
        .divider::before,.divider::after { content:''; flex:1; height:1px; background:var(--border); }

        /* Fields */
        .field { margin-bottom:14px; }
        .field-label { display:flex; justify-content:space-between; align-items:center; margin-bottom:7px; }
        label { font-size:13px; font-weight:500; color:var(--muted2); }
        .forgot { font-size:12px; color:var(--accent); text-decoration:none; }
        .forgot:hover { text-decoration:underline; }

        .input-wrap { position:relative; }
        .input-wrap input {
            width:100%; background:var(--surface2); border:1px solid var(--border); border-radius:10px;
            padding:11px 38px 11px 14px; color:var(--text);
            font-family:'Roboto',sans-serif; font-size:14px; outline:none;
            transition:border-color .2s, box-shadow .2s;
        }
        .input-wrap input:focus { border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-glow); }
        .input-wrap input.is-error { border-color:var(--red); box-shadow:0 0 0 3px rgba(239,68,68,0.12); }
        .input-wrap input.is-valid { border-color:var(--green); }
        .input-wrap input::placeholder { color:var(--muted); }

        .eye-toggle { position:absolute; right:11px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--muted); cursor:pointer; display:grid; place-items:center; transition:color .2s; }
        .eye-toggle:hover { color:var(--muted2); }

        .field-error { font-size:12px; color:#fca5a5; margin-top:5px; }

        [data-theme="light"] .field-error {
            color: #b91c1c;
        }

        .remember-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
        .check-label { display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted2); cursor:pointer; }
        .check-label input { accent-color:var(--accent); }

        /* Submit */
        .btn-primary {
            width:100%; padding:13px;
            background:linear-gradient(135deg, var(--accent), #6ba3f8);
            color:#fff; border:none; border-radius:12px;
            font-family:'Roboto',sans-serif; font-size:14px; font-weight:600;
            cursor:pointer; transition:all .25s; position:relative; overflow:hidden;
        }
        .btn-primary:hover { transform:translateY(-1px); box-shadow:0 6px 24px rgba(79,142,247,0.45); }
        .btn-primary:active { transform:translateY(0); }
        .btn-primary.loading { pointer-events:none; opacity:0.8; }
        .btn-primary .btn-text { transition:opacity .2s; }
        .btn-primary .spinner { display:none; width:16px; height:16px; border:2px solid rgba(255,255,255,0.3); border-top-color:#fff; border-radius:50%; animation:spin .6s linear infinite; position:absolute; left:50%; top:50%; transform:translate(-50%,-50%); }
        .btn-primary.loading .btn-text { opacity:0; }
        .btn-primary.loading .spinner { display:block; }
        @keyframes spin { to{transform:translate(-50%,-50%) rotate(360deg);} }

        .footer-text { text-align:center; margin-top:20px; font-size:13px; color:var(--muted); }
        .footer-text a { color:var(--accent); text-decoration:none; }
        .footer-text a:hover { text-decoration:underline; }

        @media(max-width:700px) {
            .panel-left { display:none; }
            .page-wrap { max-width:420px; }
            .panel-right { width:100%; padding:36px 28px; }
        }
    </style>
</head>
<body>
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>
    <div class="bg-grid"></div>

    <div class="page-wrap">

        <!-- Left panel -->
        <div class="panel-left">
            <div class="panel-inner">
                <a href="{{ route('home') }}" class="brand brand-link">
                    <div class="brand-icon">ED</div>
                    <span class="brand-name">EduDev</span>
                    <span class="brand-tag">{{ __('auth.beta') }}</span>
                </a>
                <h2 class="hero-headline">{!! __('auth.learn_build_ship') !!}</h2>
                <p class="hero-sub">{{ __('auth.hero_subtitle') }}</p>
            </div>

            <div class="features">
                <div class="feature-pill">
                    <div class="dot" style="background:#4f8ef7"></div>
                    {{ __('auth.feature_1') }}
                </div>
                <div class="feature-pill">
                    <div class="dot" style="background:#8b5cf6"></div>
                    {{ __('auth.feature_2') }}
                </div>
                <div class="feature-pill">
                    <div class="dot" style="background:#22c55e"></div>
                    {{ __('auth.feature_3') }}
                </div>
            </div>
        </div>

        <!-- Right panel: form -->
        <div class="panel-right">
            <h1 class="form-title">{{ __('auth.welcome_back') }}</h1>
            <p class="form-sub">{{ __('auth.sign_in_to_continue') }}</p>

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Google Sign In -->
            <a href="{{ route('google.redirect', ['context' => 'login']) }}" class="btn-google" id="googleBtn">
                <svg width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                {{ __('auth.continue_with_google') }}
            </a>

            <div class="divider">{{ __('auth.or_sign_in_with_email') }}</div>

            <form method="POST" action="{{ route('login.post') }}" id="loginForm" novalidate>
                @csrf

                <div class="field">
                    <div class="field-label">
                        <label for="login">{{ __('auth.email_or_username') }}</label>
                    </div>
                    <div class="input-wrap">
                        <input type="text" id="login" name="login"
                               placeholder="{{ __('auth.login_placeholder') }}"
                               value="{{ old('login') }}"
                               class="{{ $errors->has('login') ? 'is-error' : '' }}">
                    </div>
                    @error('login')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <div class="field-label">
                        <label for="password">{{ __('auth.password') }}</label>
                        <a href="#" class="forgot">{{ __('auth.forgot_password') }}</a>
                    </div>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password" placeholder="{{ __('auth.password_placeholder') }}"
                               class="{{ $errors->has('password') ? 'is-error' : '' }}">
                        <button
                            type="button"
                            class="eye-toggle"
                            id="eyeToggle"
                            aria-label="{{ __('messages.show_password') }}"
                            title="{{ __('messages.show_password') }}"
                        >
                            <svg id="eyeIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-row">
                    <label class="check-label">
                        <input type="checkbox" name="remember"> {{ __('auth.remember_me') }}
                    </label>
                </div>

                <button type="submit" class="btn-primary" id="submitBtn">
                    <span class="btn-text">{{ __('auth.sign_in') }}</span>
                    <div class="spinner"></div>
                </button>
            </form>

            <p class="footer-text">
                {{ __('auth.dont_have_account') }} <a href="{{ route('signup') }}">{{ __('auth.create_one_free') }}</a>
            </p>
        </div>
    </div>

    <script>
        // Eye toggle
        document.getElementById('eyeToggle').addEventListener('click', () => {
            const inp = document.getElementById('password');
            const toggle = document.getElementById('eyeToggle');
            const show = inp.type === 'password';
            inp.type = show ? 'text' : 'password';
            const tooltipLabel = show ? @json(__('messages.hide_password')) : @json(__('messages.show_password'));
            toggle.setAttribute('aria-label', tooltipLabel);
            toggle.setAttribute('title', tooltipLabel);
            document.getElementById('eyeIcon').innerHTML = show
                ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`
                : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        });

        // Form submit loading
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const login = document.getElementById('login');
            const pass  = document.getElementById('password');
            let valid = true;
            if (!login.value.trim()) { login.classList.add('is-error'); valid = false; }
            else { login.classList.remove('is-error'); login.classList.add('is-valid'); }
            if (!pass.value) { pass.classList.add('is-error'); valid = false; }
            else { pass.classList.remove('is-error'); }
            if (!valid) { e.preventDefault(); return; }
            document.getElementById('submitBtn').classList.add('loading');
        });

        // Google loading
        document.getElementById('googleBtn').addEventListener('click', function() {
            this.style.opacity = '0.7';
            this.style.pointerEvents = 'none';
            this.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="2.5" style="animation:spin .7s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> {{ __('auth.redirecting_to_google') }}`;
        });

        // Email blur validation
        document.getElementById('login').addEventListener('blur', function() {
            if (!this.value) return;
            this.classList.remove('is-error');
            this.classList.add('is-valid');
        });
    </script>
    @include('partials.app.settings-panel', ['showFloatingTrigger' => true])
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
</body>
</html>
