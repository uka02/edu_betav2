<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title>{{ $pageTitle }} - EduDev</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
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
            --orange:      #f97316;
            --yellow:      #eab308;
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
            --orange:      #ea580c;
            --yellow:      #ca8a04;
        }

        html, body { height: 100%; }

        body {
            font-family: "Roboto", sans-serif;
            background: var(--bg); color: var(--text);
            min-height: 100vh; display: flex;
            align-items: center; justify-content: center;
            padding: 24px; overflow: hidden; position: relative;
        }

        [data-theme="light"] .bg-grid {
            background-image: radial-gradient(rgba(15,23,42,0.05) 1px,transparent 1px);
        }

        .bg-orb { position:fixed; border-radius:50%; filter:blur(90px); pointer-events:none; animation:drift 14s ease-in-out infinite alternate; }
        .bg-orb-1 { width:500px; height:500px; background:radial-gradient(circle,rgba(139,92,246,0.07),transparent 70%); top:-100px; right:-100px; }
        .bg-orb-2 { width:500px; height:500px; background:radial-gradient(circle,rgba(79,142,247,0.06),transparent 70%); bottom:-100px; left:-100px; animation-delay:-7s; }
        @keyframes drift { 0%{transform:translate(0,0);} 100%{transform:translate(25px,18px);} }
        .bg-grid { position:fixed; inset:0; background-image:radial-gradient(rgba(255,255,255,0.025) 1px,transparent 1px); background-size:30px 30px; pointer-events:none; }

        .page-wrap {
            display:flex; width:100%; max-width:960px;
            border-radius:24px; overflow:hidden;
            border:1px solid var(--border);
            box-shadow:0 40px 100px rgba(0,0,0,0.6);
            position:relative; z-index:1;
            animation:fadeUp .6s cubic-bezier(.16,1,.3,1) both;
        }

        [data-theme="light"] .page-wrap {
            box-shadow: 0 28px 70px rgba(15,23,42,0.14);
        }
        @keyframes fadeUp { from{opacity:0;transform:translateY(28px);} to{opacity:1;transform:translateY(0);} }

        .panel-left {
            width:440px; flex-shrink:0;
            background:var(--surface);
            padding:44px 40px;
            display:flex; flex-direction:column; justify-content:center;
        }

        .brand { display:flex; align-items:center; gap:10px; margin-bottom:28px; }
        .brand-link { color:inherit; text-decoration:none; width:fit-content; }
        .brand-icon { width:36px; height:36px; background:linear-gradient(135deg,var(--accent),var(--purple)); border-radius:10px; display:grid; place-items:center; font-size:12px; font-weight:800; letter-spacing:.08em; box-shadow:0 3px 14px rgba(79,142,247,0.35); }
        .brand-name { font-family:'Roboto',sans-serif; font-size:17px; font-weight:800; letter-spacing:-0.02em; }

        .form-title { font-family:'Roboto',sans-serif; font-size:22px; font-weight:700; letter-spacing:-0.03em; margin-bottom:5px; }
        .form-sub { font-size:13px; color:var(--muted2); margin-bottom:24px; }

        .alert { display:flex; align-items:center; gap:10px; padding:11px 14px; border-radius:10px; font-size:13px; margin-bottom:16px; animation:slideIn .3s ease; }
        @keyframes slideIn { from{opacity:0;transform:translateY(-8px);} to{opacity:1;transform:translateY(0);} }
        .alert-error   { background:rgba(239,68,68,0.1);  border:1px solid rgba(239,68,68,0.2);  color:#fca5a5; }
        .alert-success { background:rgba(34,197,94,0.1);  border:1px solid rgba(34,197,94,0.2);  color:#86efac; }

        [data-theme="light"] .alert-error   { color: #b91c1c; background: rgba(220,38,38,0.08); border-color: rgba(220,38,38,0.16); }
        [data-theme="light"] .alert-success { color: #166534; background: rgba(22,163,74,0.08); border-color: rgba(22,163,74,0.16); }

        .btn-google {
            display:flex; align-items:center; justify-content:center; gap:11px;
            width:100%; padding:12px 20px;
            background:#fff; color:#1f2937;
            border:none; border-radius:12px;
            font-family:'Roboto',sans-serif; font-size:14px; font-weight:500;
            cursor:pointer; text-decoration:none;
            transition:all .2s; box-shadow:0 2px 12px rgba(0,0,0,0.25);
        }
        .btn-google:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.35); }

        [data-theme="light"] .btn-google {
            box-shadow: 0 10px 24px rgba(15,23,42,0.08);
        }

        [data-theme="light"] .btn-google:hover {
            box-shadow: 0 16px 30px rgba(15,23,42,0.12);
        }

        .divider { display:flex; align-items:center; gap:12px; margin:18px 0; color:var(--muted); font-size:11px; text-transform:uppercase; letter-spacing:0.1em; }
        .divider::before,.divider::after { content:''; flex:1; height:1px; background:var(--border); }

        .field-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .field { margin-bottom:12px; }

        label { display:block; font-size:12px; font-weight:500; color:var(--muted2); margin-bottom:6px; }

        .input-wrap { position:relative; }
        .input-wrap input {
            width:100%; background:var(--surface2); border:1px solid var(--border); border-radius:10px;
            padding:10px 36px 10px 13px; color:var(--text);
            font-family:'Roboto',sans-serif; font-size:14px; outline:none;
            transition:border-color .2s, box-shadow .2s;
        }
        .input-wrap input:focus { border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-glow); }
        .input-wrap input.is-error { border-color:var(--red); box-shadow:0 0 0 3px rgba(239,68,68,0.12); }
        .input-wrap input.is-valid { border-color:var(--green); }
        .input-wrap input::placeholder { color:var(--muted); }

        .eye-toggle { position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--muted); cursor:pointer; display:grid; place-items:center; transition:color .2s; }
        .eye-toggle:hover { color:var(--muted2); }

        .field-error { font-size:12px; color:#fca5a5; margin-top:4px; }

        [data-theme="light"] .field-error,
        [data-theme="light"] .match-error {
            color: #b91c1c;
        }

        .strength-row { display:flex; gap:3px; margin-top:6px; }
        .strength-seg { flex:1; height:3px; border-radius:99px; background:var(--border); transition:background .3s; }
        .strength-label { font-size:11px; color:var(--muted); margin-top:4px; min-height:14px; transition:color .3s; }

        .pw-reqs { background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:10px; padding:10px 13px; margin-top:7px; display:none; }
        .pw-reqs.visible { display:block; animation:slideIn .2s ease; }
        .req-item { display:flex; align-items:center; gap:8px; font-size:12px; color:var(--muted); margin-bottom:4px; transition:color .2s; }
        .req-item:last-child { margin-bottom:0; }
        .req-item.met { color:#86efac; }
        .req-dot { width:6px; height:6px; border-radius:50%; background:var(--muted); flex-shrink:0; transition:background .2s; }
        .req-item.met .req-dot { background:var(--green); }

        [data-theme="light"] .pw-reqs {
            background: rgba(15,23,42,0.03);
        }

        .match-error { font-size:12px; color:#fca5a5; margin-top:4px; display:none; }

        .terms-row { display:flex; align-items:flex-start; gap:9px; margin:12px 0 16px; font-size:13px; color:var(--muted2); }
        .terms-row input { margin-top:2px; accent-color:var(--accent); flex-shrink:0; }
        .terms-row a { color:var(--accent); text-decoration:none; }
        .terms-row a:hover { text-decoration:underline; }

        .btn-primary {
            width:100%; padding:12px;
            background:linear-gradient(135deg,var(--accent),#6ba3f8);
            color:#fff; border:none; border-radius:12px;
            font-family:'Roboto',sans-serif; font-size:14px; font-weight:600;
            cursor:pointer; transition:all .25s; position:relative; overflow:hidden;
        }
        .btn-primary:hover { transform:translateY(-1px); box-shadow:0 6px 24px rgba(79,142,247,0.45); }
        .btn-primary.loading { pointer-events:none; opacity:0.8; }
        .btn-primary .btn-text { transition:opacity .2s; }
        .btn-primary .spinner { display:none; width:16px; height:16px; border:2px solid rgba(255,255,255,0.3); border-top-color:#fff; border-radius:50%; animation:spin .6s linear infinite; position:absolute; left:50%; top:50%; transform:translate(-50%,-50%); }
        .btn-primary.loading .btn-text { opacity:0; }
        .btn-primary.loading .spinner { display:block; }
        @keyframes spin { to{transform:translate(-50%,-50%) rotate(360deg);} }

        .footer-text { text-align:center; margin-top:16px; font-size:13px; color:var(--muted); }
        .footer-text a { color:var(--accent); text-decoration:none; }
        .footer-text a:hover { text-decoration:underline; }

        .panel-right {
            flex:1;
            background:linear-gradient(150deg,#0c1728 0%,#090e18 60%,#110c1e 100%);
            padding:52px 44px;
            display:flex; flex-direction:column; justify-content:center;
            position:relative; overflow:hidden;
        }

        [data-theme="light"] .panel-right {
            background: linear-gradient(150deg, #ffffff 0%, #f8fbff 58%, #eef5ff 100%);
        }
        .panel-right::before {
            content:''; position:absolute; inset:0;
            background:
                radial-gradient(ellipse 70% 50% at 80% 20%,rgba(139,92,246,0.1) 0%,transparent 60%),
                radial-gradient(ellipse 50% 70% at 20% 80%,rgba(79,142,247,0.08) 0%,transparent 60%);
            pointer-events:none;
        }

        [data-theme="light"] .panel-right::before {
            background:
                radial-gradient(ellipse 70% 50% at 80% 20%, rgba(124,58,237,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 50% 70% at 20% 80%, rgba(37,99,235,0.10) 0%, transparent 60%);
        }
        .panel-right-inner { position:relative; z-index:1; }

        .perks-title { font-family:'Roboto',sans-serif; font-size:30px; font-weight:800; letter-spacing:-0.04em; line-height:1.15; margin-bottom:10px; }
        .perks-title span { background:linear-gradient(135deg,var(--purple),var(--accent)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .perks-sub { font-size:14px; color:var(--muted2); line-height:1.65; margin-bottom:36px; }

        .perk-list { display:flex; flex-direction:column; gap:18px; margin-bottom:36px; }
        .perk-item { display:flex; align-items:flex-start; gap:14px; }
        .perk-icon { width:40px; height:40px; border-radius:11px; display:grid; place-items:center; font-size:11px; font-weight:800; letter-spacing:.08em; flex-shrink:0; }
        .pi-1 { background:rgba(79,142,247,0.12); border:1px solid rgba(79,142,247,0.2); }
        .pi-2 { background:rgba(139,92,246,0.12); border:1px solid rgba(139,92,246,0.2); }
        .pi-3 { background:rgba(34,197,94,0.12);  border:1px solid rgba(34,197,94,0.2); }
        .pi-4 { background:rgba(249,115,22,0.12); border:1px solid rgba(249,115,22,0.2); }
        .perk-text strong { display:block; font-size:14px; font-weight:600; margin-bottom:2px; }
        .perk-text span { font-size:13px; color:var(--muted2); line-height:1.5; }

        .social-proof { display:flex; align-items:center; gap:12px; padding-top:24px; border-top:1px solid var(--border); }
        .avatars { display:flex; }
        .av { width:28px; height:28px; border-radius:50%; border:2px solid var(--surface); margin-right:-7px; display:grid; place-items:center; font-size:11px; font-weight:600; }
        .av1{background:linear-gradient(135deg,#6366f1,#8b5cf6);}
        .av2{background:linear-gradient(135deg,#f97316,#ef4444);}
        .av3{background:linear-gradient(135deg,#22c55e,#16a34a);}
        .av4{background:linear-gradient(135deg,#0ea5e9,#6366f1);}
        .social-text { font-size:13px; color:var(--muted2); line-height:1.4; }
        .social-text strong { color:var(--text); }

        @media(max-width:800px) {
            .panel-right { display:none; }
            .page-wrap { max-width:460px; }
            .panel-left { width:100%; padding:36px 28px; }
        }
    </style>
</head>
<body>
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>
    <div class="bg-grid"></div>

    <div class="page-wrap">
        <div class="panel-left">
            <a href="{{ route('home') }}" class="brand brand-link">
                <div class="brand-icon">ED</div>
                <span class="brand-name">EduDev</span>
            </a>

            <h1 class="form-title">{{ $formTitle }}</h1>
            <p class="form-sub">{{ $formSubtitle }}</p>

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <a href="{{ route('google.redirect', ['context' => 'signup', 'role' => $signupRole]) }}" class="btn-google" id="googleBtn">
                <svg width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                {{ $googleButtonLabel }}
            </a>

            <div class="divider">{{ __('auth.or_create_with_email') }}</div>

            <form method="POST" action="{{ route('signup.post') }}" id="signupForm" novalidate>
                @csrf
                <input type="hidden" name="role" value="{{ old('role', $signupRole) }}">

                <div class="field-row">
                    <div class="field">
                        <label for="first_name">{{ __('auth.first_name') }}</label>
                        <div class="input-wrap">
                            <input type="text" id="first_name" name="first_name" placeholder="{{ __('auth.first_name_placeholder') }}" value="{{ old('first_name') }}">
                        </div>
                    </div>
                    <div class="field">
                        <label for="last_name">{{ __('auth.last_name') }}</label>
                        <div class="input-wrap">
                            <input type="text" id="last_name" name="last_name" placeholder="{{ __('auth.last_name_placeholder') }}" value="{{ old('last_name') }}">
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label for="email">{{ __('auth.email_address') }}</label>
                    <div class="input-wrap">
                        <input type="email" id="email" name="email" placeholder="{{ __('auth.email_placeholder') }}" value="{{ old('email') }}" class="{{ $errors->has('email') ? 'is-error' : '' }}">
                    </div>
                    @error('email') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="password">{{ __('auth.password') }}</label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password" placeholder="{{ __('auth.create_strong_password') }}" oninput="checkStrength(this.value)" class="{{ $errors->has('password') ? 'is-error' : '' }}">
                        <button
                            type="button"
                            class="eye-toggle"
                            id="eye1"
                            aria-label="{{ __('messages.show_password') }}"
                            title="{{ __('messages.show_password') }}"
                        >
                            <svg id="eyeIcon1" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <div class="strength-row">
                        <div class="strength-seg" id="s1"></div>
                        <div class="strength-seg" id="s2"></div>
                        <div class="strength-seg" id="s3"></div>
                        <div class="strength-seg" id="s4"></div>
                    </div>
                    <div class="strength-label" id="strengthLabel"></div>
                    <div class="pw-reqs" id="pwReqs">
                        <div class="req-item" id="req-len"><div class="req-dot"></div>{{ __('auth.at_least_8_chars') }}</div>
                        <div class="req-item" id="req-upper"><div class="req-dot"></div>{{ __('auth.one_uppercase') }}</div>
                        <div class="req-item" id="req-num"><div class="req-dot"></div>{{ __('auth.one_number') }}</div>
                        <div class="req-item" id="req-special"><div class="req-dot"></div>{{ __('auth.one_special') }}</div>
                    </div>
                    @error('password') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">{{ __('auth.confirm_password') }}</label>
                    <div class="input-wrap">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="{{ __('auth.repeat_password') }}" oninput="checkMatch()">
                        <button
                            type="button"
                            class="eye-toggle"
                            id="eye2"
                            aria-label="{{ __('messages.show_password') }}"
                            title="{{ __('messages.show_password') }}"
                        >
                            <svg id="eyeIcon2" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <div class="match-error" id="matchError">{{ __('auth.passwords_no_match') }}</div>
                </div>

                <div class="terms-row">
                    <input type="checkbox" name="terms" id="terms" required>
                    <label for="terms" style="margin:0;cursor:pointer;">
                        {{ __('auth.i_agree_to') }} <a href="#">{{ __('auth.terms_of_service') }}</a> {{ __('auth.and') }} <a href="#">{{ __('auth.privacy_policy') }}</a>
                    </label>
                </div>

                <button type="submit" class="btn-primary" id="submitBtn">
                    <span class="btn-text">{{ __('auth.create_account') }}</span>
                    <div class="spinner"></div>
                </button>
            </form>

            <p class="footer-text">{{ __('auth.already_have_account') }} <a href="{{ route('login') }}">{{ __('auth.sign_in_link') }}</a></p>
        </div>

        <div class="panel-right">
            <div class="panel-right-inner">
                <h2 class="perks-title">{!! $panelTitle !!}</h2>
                <p class="perks-sub">{{ $panelSubtitle }}</p>

                <div class="perk-list">
                    @foreach($perkItems as $perkItem)
                        <div class="perk-item">
                            <div class="perk-icon {{ $perkItem['icon_class'] }}">{{ $perkItem['badge'] }}</div>
                            <div class="perk-text">
                                <strong>{{ $perkItem['title'] }}</strong>
                                <span>{{ $perkItem['copy'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="social-proof">
                    <div class="avatars">
                        <div class="av av1">J</div>
                        <div class="av av2">M</div>
                        <div class="av av3">A</div>
                        <div class="av av4">R</div>
                    </div>
                    <div class="social-text">
                        <strong>{{ $socialProofLead }}</strong><br>
                        {{ $socialProofNote }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const strengthLabels = ['', '{{ __('auth.weak') }}', '{{ __('auth.fair') }}', '{{ __('auth.good') }}', '{{ __('auth.strong') }}'];

        function checkStrength(val) {
            const reqs = { 'req-len': val.length >= 8, 'req-upper': /[A-Z]/.test(val), 'req-num': /[0-9]/.test(val), 'req-special': /[^A-Za-z0-9]/.test(val) };
            document.getElementById('pwReqs').classList.toggle('visible', val.length > 0);
            Object.entries(reqs).forEach(([id, met]) => document.getElementById(id).classList.toggle('met', met));
            const score = Object.values(reqs).filter(Boolean).length;
            ['s1','s2','s3','s4'].forEach((id, i) => {
                document.getElementById(id).style.background = i < score ? ['#ef4444','#f97316','#eab308','#22c55e'][score-1] : 'var(--border)';
            });
            const lbl = document.getElementById('strengthLabel');
            lbl.textContent = val.length ? strengthLabels[score] : '';
            lbl.style.color = val.length ? ['','#fca5a5','#fdba74','#fde047','#86efac'][score] : 'var(--muted)';
            checkMatch();
        }

        function checkMatch() {
            const pw = document.getElementById('password').value;
            const conf = document.getElementById('password_confirmation');
            const err = document.getElementById('matchError');
            if (!conf.value) { err.style.display='none'; conf.classList.remove('is-error','is-valid'); return; }
            if (pw === conf.value) { conf.classList.remove('is-error'); conf.classList.add('is-valid'); err.style.display='none'; }
            else { conf.classList.remove('is-valid'); conf.classList.add('is-error'); err.style.display='block'; }
        }

        function makeEye(btnId, inputId, iconId) {
            document.getElementById(btnId).addEventListener('click', () => {
                const inp = document.getElementById(inputId);
                const toggle = document.getElementById(btnId);
                const show = inp.type === 'password';
                inp.type = show ? 'text' : 'password';
                const tooltipLabel = show ? @json(__('messages.hide_password')) : @json(__('messages.show_password'));
                toggle.setAttribute('aria-label', tooltipLabel);
                toggle.setAttribute('title', tooltipLabel);
                document.getElementById(iconId).innerHTML = show
                    ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`
                    : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
            });
        }
        makeEye('eye1','password','eyeIcon1');
        makeEye('eye2','password_confirmation','eyeIcon2');

        document.getElementById('email').addEventListener('blur', function() {
            if (!this.value) return;
            if (/\S+@\S+\.\S+/.test(this.value)) { this.classList.remove('is-error'); this.classList.add('is-valid'); }
            else { this.classList.remove('is-valid'); this.classList.add('is-error'); }
        });

        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const pw = document.getElementById('password').value;
            const conf = document.getElementById('password_confirmation').value;
            const terms = document.getElementById('terms').checked;
            let valid = true;
            if (!email || !/\S+@\S+\.\S+/.test(email)) { document.getElementById('email').classList.add('is-error'); valid = false; }
            if (!pw || pw.length < 8) { document.getElementById('password').classList.add('is-error'); valid = false; }
            if (pw !== conf) { document.getElementById('password_confirmation').classList.add('is-error'); document.getElementById('matchError').style.display='block'; valid = false; }
            if (!terms) valid = false;
            if (!valid) { e.preventDefault(); return; }
            document.getElementById('submitBtn').classList.add('loading');
        });

        document.getElementById('googleBtn').addEventListener('click', function() {
            this.style.opacity = '0.7'; this.style.pointerEvents = 'none';
            this.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="2.5" style="animation:spin .7s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Redirecting...`;
        });
    </script>
    @include('partials.app.settings-panel', ['showFloatingTrigger' => true])
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
</body>
</html>
