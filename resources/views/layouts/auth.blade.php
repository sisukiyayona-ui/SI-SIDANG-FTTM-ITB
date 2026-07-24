<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login - SI SIDANG FTTM ITB')</title>
    <meta name="description" content="Masuk ke Sistem Informasi Penjadwalan Sidang FTTM ITB">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/itb-logo.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #f8fafc;
            --bg2:      #ffffff;
            --bg3:      #f1f5f9;
            --border:   #e2e8f0;
            --text:     #1e293b;
            --muted:    #64748b;
            --accent:   #2563eb;
            --accent2:  #4f46e5;
            --link:     #2563eb;
        }

        html.dark-mode {
            --bg:       #0f172a;
            --bg2:      #1e293b;
            --bg3:      #334155;
            --border:   #334155;
            --text:     #e2e8f0;
            --muted:    #94a3b8;
            --accent:   #60a5fa;
            --accent2:  #818cf8;
            --link:     #60a5fa;
        }
        html.dark-mode body {
            background: var(--bg) !important;
            color: var(--text) !important;
        }
        html.dark-mode .top-nav {
            background: var(--bg2) !important;
            border-bottom-color: var(--border) !important;
        }
        html.dark-mode .nav-brand-name {
            color: var(--text) !important;
        }
        html.dark-mode .nav-brand-logo {
            background: var(--accent) !important;
        }
        html.dark-mode .login-box {
            background: var(--bg2) !important;
            border-color: var(--border) !important;
            box-shadow: 0 24px 80px rgba(0,0,0,0.5) !important;
        }
        html.dark-mode .form-control {
            background: var(--bg3) !important;
            border-color: var(--border) !important;
            color: var(--text) !important;
        }
        html.dark-mode .form-control:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px rgba(96,165,250,0.15) !important;
        }
        html.dark-mode .form-control::placeholder {
            color: var(--muted) !important;
        }
        html.dark-mode .form-label {
            color: var(--muted) !important;
        }
        html.dark-mode .check-label {
            color: var(--muted) !important;
        }
        html.dark-mode .register-row {
            color: var(--muted) !important;
        }
        html.dark-mode .register-row a {
            color: var(--link) !important;
        }
        html.dark-mode .forgot-link {
            color: var(--link) !important;
        }
        html.dark-mode .demo-card {
            background: var(--bg3) !important;
            border-color: var(--border) !important;
        }
        html.dark-mode .demo-card-role {
            color: var(--text) !important;
        }
        html.dark-mode .demo-card-username,
        html.dark-mode .demo-card-password {
            color: var(--muted) !important;
        }
        html.dark-mode .divider {
            border-top-color: var(--border) !important;
        }
        html.dark-mode .toast {
            background: var(--bg2) !important;
            border-color: var(--border) !important;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4) !important;
        }
        html.dark-mode .toast-msg {
            color: var(--text) !important;
        }
        html.dark-mode .toast-close {
            color: var(--muted) !important;
        }
        html.dark-mode .toast-close:hover {
            color: var(--text) !important;
        }
        html.dark-mode .input-wrap i.icon-left {
            color: var(--muted) !important;
        }
        html.dark-mode .btn-toggle-pass {
            color: var(--muted) !important;
        }
        html.dark-mode .btn-toggle-pass:hover {
            color: var(--text) !important;
        }
        html.dark-mode .demo-header {
            color: var(--muted) !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── NAVBAR ── */
        .top-nav {
            background: var(--bg2);
            border-bottom: 1px solid var(--border);
        }
        .nav-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .nav-brand-logo {
            width: 32px;
            height: 32px;
            background: #1e3a8a;
            border-radius: 50%;
            padding: 4px;
        }
        .nav-brand-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }
        .nav-brand-name {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--text);
            letter-spacing: 0.3px;
            line-height: 1.1;
        }
        .nav-brand-sub {
            font-size: 0.68rem;
            font-weight: 500;
            color: var(--muted);
            letter-spacing: 0.2px;
            line-height: 1.1;
        }
        .nav-actions { display: flex; align-items: center; gap: 8px; }
        .btn-nav {
            padding: 7px 18px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .btn-nav-ghost { color: var(--muted); background: transparent; }
        .btn-nav-ghost:hover { color: var(--text); background: var(--bg3); }

        /* ── PAGE BODY ── */
        .login-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .login-box {
            width: 100%;
            max-width: 560px;
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }

        .login-head {
            background: linear-gradient(135deg, #1e3a5f 0%, #1a1f6e 50%, #312477 100%);
            padding: 32px 32px 28px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .login-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at top right, rgba(99,102,241,0.3) 0%, transparent 60%),
                        radial-gradient(ellipse at bottom left, rgba(59,130,246,0.2) 0%, transparent 60%);
        }
        .login-head-inner { position: relative; z-index: 1; }
        .login-logo {
            width: 56px;
            height: 56px;
            background: #fff;
            border-radius: 50%;
            padding: 8px;
            margin: 0 auto 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }
        .login-head h1 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }
        .login-head p {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.7);
            line-height: 1.5;
        }

        .login-body { padding: 28px 32px 32px; }

        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 7px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap i.icon-left {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 0.85rem;
            pointer-events: none;
        }
        .form-control {
            width: 100%;
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.88rem;
            padding: 10px 40px 10px 38px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control::placeholder { color: #94a3b8; }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .form-control.is-invalid { border-color: #ef4444; }
        .btn-toggle-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 0.85rem;
            padding: 4px;
            transition: color 0.2s;
        }
        .btn-toggle-pass:hover { color: var(--text); }
        .text-danger {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 5px;
            display: block;
        }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }
        .check-label {
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            font-size: 0.82rem;
            color: var(--muted);
        }
        .check-label input[type=checkbox] {
            width: 16px;
            height: 16px;
            accent-color: var(--accent);
            cursor: pointer;
        }
        .forgot-link {
            font-size: 0.82rem;
            color: var(--accent);
            text-decoration: none;
        }
        .forgot-link:hover { text-decoration: underline; }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none;
            border-radius: 10px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-submit:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(37,99,235,0.35);
        }

        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 22px 0;
        }

        .register-row {
            text-align: center;
            font-size: 0.82rem;
            color: var(--muted);
        }
        .register-row a {
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
            margin-left: 4px;
        }
        .register-row a:hover { text-decoration: underline; }

        /* ── DEMO CARDS ── */
        .demo-section {
            margin-top: 28px;
        }
        .demo-header {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .demo-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        @media (min-width: 520px) {
            .demo-cards { grid-template-columns: repeat(3, 1fr); }
        }
        .demo-card {
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .demo-card:hover {
            border-color: var(--accent);
            background: rgba(37,99,235,0.04);
            transform: translateY(-2px);
        }
        .demo-card-icon {
            width: 36px;
            height: 36px;
            background: rgba(37,99,235,0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 0.9rem;
        }
        .demo-card-content {
            flex: 1;
        }
        .demo-card-role {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 2px;
        }
        .demo-card-username {
            font-size: 0.7rem;
            color: var(--muted);
            margin-bottom: 1px;
        }
        .demo-card-password {
            font-size: 0.7rem;
            color: var(--muted);
        }
        .demo-card-action {
            color: var(--muted);
            font-size: 0.75rem;
            transition: color 0.2s;
        }
        .demo-card:hover .demo-card-action {
            color: var(--accent);
        }

        /* Toast */
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
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 16px;
            min-width: 280px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .toast-success { border-left: 3px solid #16a34a; }
        .toast-error { border-left: 3px solid #dc2626; }
        .toast-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .toast-icon {
            font-size: 1rem;
        }
        .toast-success .toast-icon { color: #16a34a; }
        .toast-error .toast-icon { color: #dc2626; }
        .toast-msg {
            font-size: 0.82rem;
            color: var(--text);
            flex: 1;
        }
        .toast-close {
            background: none;
            border: none;
            color: var(--muted);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0 0 0 8px;
            line-height: 1;
            transition: color 0.2s;
        }
        .toast-close:hover { color: var(--text); }

        /* Responsive */
        /* ── SSO BUTTON ── */
        .sso-section {
            text-align: center;
        }
        .btn-sso {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 24px;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--muted);
            background: var(--bg3);
            border: 1px solid var(--border);
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-sso:hover {
            color: var(--text);
            border-color: var(--accent);
            background: rgba(37,99,235,0.04);
            transform: translateY(-1px);
        }
        .sso-logo {
            width: 20px;
            height: 20px;
        }
        html.dark-mode .btn-sso {
            color: var(--muted);
        }
        html.dark-mode .btn-sso:hover {
            color: var(--text);
        }

        @media (max-width: 480px) {
            .nav-brand-name { font-size: 0.85rem; }
            .nav-brand-sub { font-size: 0.62rem; }
            .login-box { max-width: 100%; }
            .demo-cards { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="top-nav">
        <div class="nav-inner">
            <a href="{{ url('/') }}" class="nav-brand">
                <img src="{{ asset('images/itb-logo.svg') }}" alt="ITB Logo" class="nav-brand-logo">
                <div class="nav-brand-text">
                    <span class="nav-brand-name">SI SIDANG FTTM ITB</span>
                    <span class="nav-brand-sub">Sistem Informasi Sidang</span>
                </div>
            </a>
            <div class="nav-actions">
                <button class="btn-nav btn-nav-ghost" id="darkModeToggle" onclick="toggleDarkMode()" title="Toggle Dark Mode" style="border:none; font-size:1rem;">
                    <i class="fas fa-moon" id="darkModeIcon"></i>
                </button>
                <a href="{{ url('/') }}" class="btn-nav btn-nav-ghost">
                    <i class="fas fa-home"></i> Beranda
                </a>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-head">
                <div class="login-head-inner">
                    <img src="{{ asset('images/itb-logo.svg') }}" alt="ITB Logo" class="login-logo">
                    <h1>Sistem Informasi Sidang</h1>
                    <p>Fakultas Teknik Pertambangan dan Perminyakan</p>
                </div>
            </div>
            <div class="login-body">
                @yield('auth-content')
            </div>
        </div>
    </div>

    <!-- Toasts -->
    <div class="toast-wrap" id="toastWrap">
        @if(session('success'))
            <div class="toast toast-success" data-toast>
                <div class="toast-content">
                    <i class="fas fa-check-circle toast-icon"></i>
                    <span class="toast-msg">{{ session('success') }}</span>
                    <button type="button" class="toast-close" onclick="this.closest('[data-toast]').remove()">&times;</button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast toast-error" data-toast>
                <div class="toast-content">
                    <i class="fas fa-exclamation-circle toast-icon"></i>
                    <span class="toast-msg">{{ session('error') }}</span>
                    <button type="button" class="toast-close" onclick="this.closest('[data-toast]').remove()">&times;</button>
                </div>
            </div>
        @endif
    </div>

    @stack('scripts')

    <script>
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

        function toggleDarkMode() {
            var html = document.documentElement;
            var icon = document.getElementById('darkModeIcon');
            var isDark = html.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', isDark ? 'true' : 'false');
            icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        }

        (function initDarkMode() {
            var html = document.documentElement;
            var icon = document.getElementById('darkModeIcon');
            var saved = localStorage.getItem('darkMode');
            if (saved === 'true') {
                html.classList.add('dark-mode');
                if (icon) icon.className = 'fas fa-sun';
            } else {
                if (icon) icon.className = 'fas fa-moon';
            }
        })();
    </script>
</body>
</html>
