<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login - SI SIDANG FTTM ITB')</title>
    <meta name="description" content="Masuk ke Sistem Informasi Penjadwalan Sidang FTTM ITB">
    <meta name="theme-color" content="#1462ad">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/itb-logo.svg') }}">
    @include('layouts.partials.theme-head')
    <link rel="stylesheet" href="{{ asset('vendor/inter/300.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/inter/400.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/inter/500.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/inter/600.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/inter/700.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --page-bg: #eef4fa;
            --surface: #ffffff;
            --input-bg: #ffffff;
            --border: #d9e4ef;
            --text: #17263b;
            --muted: #607187;
            --placeholder: #65778c;
            --brand: #1462ad;
            --brand-strong: #0d4e93;
            --accent: #0098db;
            --button-bg: #1462ad;
            --button-hover: #0d4e93;
            --focus-ring: rgba(0, 152, 219, 0.18);
            --panel-from: #1462ad;
            --panel-to: #0d4e93;
            --panel-text: #ffffff;
            --panel-muted: rgba(255, 255, 255, 0.76);
            --panel-line: rgba(255, 255, 255, 0.2);
            --shadow: 0 24px 64px rgba(20, 98, 173, 0.14);
        }

        html.dark-mode {
            --page-bg: #0a1220;
            --surface: #0f172a;
            --input-bg: #0b1728;
            --border: #293c55;
            --text: #f1f5f9;
            --muted: #9fb0c6;
            --placeholder: #71849d;
            --brand: #38bdf8;
            --brand-strong: #7dd3fc;
            --accent: #38bdf8;
            --button-bg: #1676b8;
            --button-hover: #0f659e;
            --focus-ring: rgba(56, 189, 248, 0.2);
            --panel-from: #13243e;
            --panel-to: #0b1424;
            --panel-text: #f8fafc;
            --panel-muted: rgba(203, 213, 225, 0.74);
            --panel-line: rgba(148, 163, 184, 0.18);
            --shadow: 0 24px 70px rgba(0, 0, 0, 0.32);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--page-bg);
            color: var(--text);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
        }

        button, input { font: inherit; }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible {
            outline: 3px solid var(--accent);
            outline-offset: 3px;
        }

        .login-wrapper {
            flex: 1;
            display: grid;
            place-items: center;
            padding: 40px 28px 52px;
        }

        .login-box {
            width: min(1040px, 100%);
            display: grid;
            grid-template-columns: minmax(0, 0.92fr) minmax(0, 1.08fr);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .login-panel {
            min-height: 596px;
            padding: 42px 44px 38px;
            display: flex;
            flex-direction: column;
            background: linear-gradient(155deg, var(--panel-from), var(--panel-to));
            color: var(--panel-text);
        }

        .login-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .login-logo {
            width: 54px;
            height: 54px;
            flex: 0 0 auto;
            background: #ffffff;
            border-radius: 14px;
            padding: 8px;
        }

        .login-brand-name {
            font-size: 0.84rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .login-brand-sub {
            margin-top: 4px;
            color: var(--panel-muted);
            font-size: 0.72rem;
        }

        .login-panel-copy {
            margin-top: auto;
            padding-top: 64px;
        }

        .login-panel-copy h1 {
            max-width: 11ch;
            font-size: clamp(2rem, 3vw, 2.65rem);
            font-weight: 700;
            letter-spacing: -0.035em;
            line-height: 1.12;
        }

        .login-panel-copy p {
            max-width: 34ch;
            margin-top: 20px;
            color: var(--panel-muted);
            font-size: 0.92rem;
            line-height: 1.75;
        }

        .login-panel-footer {
            margin-top: 38px;
            padding-top: 22px;
            border-top: 1px solid var(--panel-line);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: var(--panel-muted);
            font-size: 0.76rem;
            line-height: 1.55;
        }

        .login-panel-footer::before {
            content: '';
            width: 28px;
            height: 3px;
            flex: 0 0 auto;
            margin-top: 8px;
            border-radius: 2px;
            background: var(--accent);
        }

        .login-content {
            min-height: 596px;
            padding: 48px 56px 42px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form-head {
            margin-bottom: 30px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
        }

        .login-form-head-copy { min-width: 0; }

        .theme-toggle {
            width: 40px;
            height: 40px;
            flex: 0 0 auto;
            display: grid;
            place-items: center;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--muted);
            font-size: 0.95rem;
            cursor: pointer;
            transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
        }

        .theme-toggle:hover {
            background: var(--page-bg);
            border-color: var(--accent);
            color: var(--text);
        }

        .login-form-head h2 {
            color: var(--text);
            font-size: 1.55rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            line-height: 1.25;
        }

        .login-form-head p {
            margin-top: 9px;
            color: var(--muted);
            font-size: 0.84rem;
            line-height: 1.6;
        }

        .form-group { margin-bottom: 19px; }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .input-wrap { position: relative; }

        .input-wrap i.icon-left {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 0.84rem;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            height: 48px;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 9px;
            color: var(--text);
            font-size: 0.88rem;
            padding: 10px 44px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control::placeholder { color: var(--placeholder); }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--focus-ring);
        }

        .form-control.is-invalid { border-color: #dc2626; }

        .form-control.is-invalid:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.12);
        }

        .btn-toggle-pass {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            background: transparent;
            border: none;
            border-radius: 6px;
            color: var(--muted);
            font-size: 0.86rem;
            cursor: pointer;
            transition: color 0.2s ease, background-color 0.2s ease;
        }

        .btn-toggle-pass:hover {
            background: var(--page-bg);
            color: var(--text);
        }

        .text-danger {
            display: block;
            margin-top: 6px;
            color: #dc2626;
            font-size: 0.75rem;
            line-height: 1.5;
        }

        html.dark-mode .text-danger { color: #fca5a5; }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin: 3px 0 24px;
        }

        .check-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 0.8rem;
            cursor: pointer;
        }

        .check-label input[type=checkbox] {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: var(--button-bg);
            cursor: pointer;
        }

        .forgot-link {
            color: var(--brand);
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-submit {
            width: 100%;
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            background: var(--button-bg);
            border: 1px solid transparent;
            border-radius: 9px;
            color: #ffffff;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-submit:hover {
            background: var(--button-hover);
            box-shadow: 0 8px 20px rgba(20, 98, 173, 0.2);
        }

        html.dark-mode .btn-submit:hover { box-shadow: 0 8px 22px rgba(0, 0, 0, 0.28); }

        .sso-section { margin-top: 22px; }

        .sso-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            color: var(--muted);
            font-size: 0.72rem;
        }

        .sso-divider::before,
        .sso-divider::after {
            content: '';
            height: 1px;
            flex: 1;
            background: var(--border);
        }

        .btn-sso {
            min-height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 9px;
            color: var(--text);
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }

        .btn-sso:hover {
            background: var(--page-bg);
            border-color: var(--accent);
        }

        .sso-logo {
            width: 20px;
            height: 20px;
        }

        .toast-wrap {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            max-width: min(380px, calc(100vw - 32px));
            padding: 13px 15px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-left: 3px solid #16a34a;
            border-radius: 9px;
            box-shadow: var(--shadow);
            animation: slideIn 0.25s ease;
        }

        .toast-error { border-left-color: #dc2626; }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toast-icon { color: #16a34a; font-size: 1rem; }
        .toast-error .toast-icon { color: #dc2626; }

        .toast-msg {
            flex: 1;
            color: var(--text);
            font-size: 0.8rem;
            line-height: 1.5;
        }

        .toast-close {
            width: 26px;
            height: 26px;
            flex: 0 0 auto;
            background: transparent;
            border: none;
            border-radius: 5px;
            color: var(--muted);
            font-size: 1.2rem;
            line-height: 1;
            cursor: pointer;
        }

        .toast-close:hover { color: var(--text); }

        @keyframes slideIn {
            from { transform: translateY(-8px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 900px) {
            .login-wrapper { padding: 32px 22px 40px; }
            .login-box { max-width: 620px; grid-template-columns: 1fr; }
            .login-panel {
                min-height: 0;
                padding: 28px 34px;
                display: grid;
                grid-template-columns: auto 1fr;
                align-items: center;
                gap: 24px;
            }
            .login-panel-copy { margin: 0; padding: 0; }
            .login-panel-copy h1 { max-width: none; font-size: 1.55rem; }
            .login-panel-copy p { margin-top: 8px; font-size: 0.8rem; line-height: 1.55; }
            .login-panel-footer { display: none; }
            .login-content { min-height: 0; padding: 36px 40px 40px; }
        }

        @media (max-width: 560px) {
            .login-wrapper { padding: 20px 14px 28px; }
            .login-box { border-radius: 16px; }
            .login-panel {
                padding: 24px;
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .login-logo { width: 46px; height: 46px; border-radius: 12px; }
            .login-panel-copy h1 { font-size: 1.45rem; }
            .login-panel-copy p { max-width: 38ch; }
            .login-content { padding: 30px 24px 32px; }
            .login-form-head { margin-bottom: 25px; }
            .login-form-head h2 { font-size: 1.35rem; }
            .remember-row { gap: 10px; }
            .toast-wrap { top: 12px; right: 12px; }
        }

        @media (max-width: 360px) {
            .login-panel, .login-content { padding-left: 20px; padding-right: 20px; }
            .remember-row { align-items: flex-start; flex-direction: column; gap: 12px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <main class="login-wrapper">
        <div class="login-box">
            <section class="login-panel" aria-labelledby="loginTitle">
                <div class="login-brand">
                    <img src="{{ asset('images/itb-logo.svg') }}" alt="Logo ITB" class="login-logo">
                    <div>
                        <div class="login-brand-name">SI SIDANG FTTM ITB</div>
                        <div class="login-brand-sub">Fakultas Teknik Pertambangan dan Perminyakan</div>
                    </div>
                </div>
                <div class="login-panel-copy">
                    <h1 id="loginTitle">Sistem Informasi Sidang</h1>
                    <p>Kelola persyaratan, jadwal, dan penilaian sidang dalam satu tempat.</p>
                </div>
                <div class="login-panel-footer">
                    <span>Layanan akademik Fakultas Teknik Pertambangan dan Perminyakan ITB.</span>
                </div>
            </section>

            <section class="login-content" aria-label="Formulir masuk">
                <div class="login-form-head">
                    <div class="login-form-head-copy">
                        <h2>Masuk ke akun Anda</h2>
                        <p>Gunakan akun SI SIDANG untuk melanjutkan.</p>
                    </div>
                    <button type="button" class="theme-toggle" id="darkModeToggle" onclick="toggleDarkMode()" title="Ganti tema" aria-label="Ganti tema">
                        <i class="fas fa-moon" id="darkModeIcon" aria-hidden="true"></i>
                    </button>
                </div>
                @yield('auth-content')
            </section>
        </div>
    </main>

    <div class="toast-wrap" id="toastWrap" aria-live="polite" aria-atomic="false">
        @if(session('success'))
            <div class="toast toast-success" data-toast>
                <div class="toast-content">
                    <i class="fas fa-check-circle toast-icon" aria-hidden="true"></i>
                    <span class="toast-msg">{{ session('success') }}</span>
                    <button type="button" class="toast-close" aria-label="Tutup notifikasi">&times;</button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast toast-error" data-toast>
                <div class="toast-content">
                    <i class="fas fa-exclamation-circle toast-icon" aria-hidden="true"></i>
                    <span class="toast-msg">{{ session('error') }}</span>
                    <button type="button" class="toast-close" aria-label="Tutup notifikasi">&times;</button>
                </div>
            </div>
        @endif
    </div>

    @stack('scripts')

    <script nonce="{{ request()->attributes->get('csp_nonce') }}">
        document.querySelectorAll('.toast-close').forEach(function(btn) {
            btn.addEventListener('click', function() {
                this.closest('[data-toast]').remove();
            });
        });

        document.querySelectorAll('[data-toast]').forEach(function(el) {
            var timer = setTimeout(function() {
                if (el.parentNode) el.remove();
            }, 3000);
            el.addEventListener('mouseenter', function() { clearTimeout(timer); });
            el.addEventListener('mouseleave', function() {
                timer = setTimeout(function() {
                    if (el.parentNode) el.remove();
                }, 3000);
            });
        });
    </script>

    <script nonce="{{ request()->attributes->get('csp_nonce') ?? '' }}">
        document.addEventListener('themechange', function(event) {
            var themeColor = document.querySelector('meta[name="theme-color"]');
            if (themeColor) themeColor.setAttribute('content', event.detail.dark ? '#13243e' : '#1462ad');
        });
    </script>

    @include('layouts.partials.theme-script')
</body>
</html>
