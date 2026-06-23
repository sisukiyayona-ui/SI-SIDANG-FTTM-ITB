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
            --bg:       #0f1117;
            --bg2:      #16181f;
            --bg3:      #1c1f2a;
            --border:   rgba(255,255,255,0.08);
            --text:     #e2e8f0;
            --muted:    rgba(226,232,240,0.55);
            --accent:   #3b82f6;
            --accent2:  #6366f1;
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
            background: #fff;
            border-radius: 50%;
            padding: 4px;
        }
        .nav-brand-text {
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }
        .nav-brand-sub {
            font-size: 0.7rem;
            font-weight: 400;
            color: var(--muted);
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
        .btn-nav-ghost:hover { color: #fff; background: rgba(255,255,255,0.06); }

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
            max-width: 420px;
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(0,0,0,0.5);
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
        .form-control::placeholder { color: var(--muted); }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
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
            color: #f87171;
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
            box-shadow: 0 4px 20px rgba(59,130,246,0.3);
        }
        .btn-submit:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(59,130,246,0.4);
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

        /* Demo account section */
        .demo-box {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
            margin-top: 18px;
        }
        .demo-title {
            font-size: 0.73rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        .demo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 6px;
        }
        .demo-item {
            background: rgba(255,255,255,0.04);
            border-radius: 7px;
            padding: 8px 10px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .demo-item:hover { background: rgba(59,130,246,0.1); }
        .demo-role {
            font-size: 0.7rem;
            color: var(--accent);
            font-weight: 600;
            margin-bottom: 3px;
        }
        .demo-cred { font-size: 0.72rem; color: var(--muted); }

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
            border-radius: 12px;
            padding: 14px 18px;
            display: none;
            align-items: flex-start;
            gap: 12px;
            min-width: 280px;
            max-width: 340px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            border: 1px solid var(--border);
            animation: slideInRight 0.3s ease;
        }
        .toast.show { display: flex; }
        .toast-success { border-left: 3px solid #10b981; }
        .toast-error { border-left: 3px solid #ef4444; }
        .toast-icon { font-size: 1.1rem; margin-top: 1px; flex-shrink: 0; }
        .toast-success .toast-icon { color: #10b981; }
        .toast-error .toast-icon { color: #ef4444; }
        .toast-body { flex: 1; }
        .toast-title { font-size: 0.85rem; font-weight: 600; color: #fff; margin-bottom: 2px; }
        .toast-msg { font-size: 0.78rem; color: var(--muted); }
        .toast-close {
            background: none;
            border: none;
            color: var(--muted);
            font-size: 1rem;
            cursor: pointer;
            line-height: 1;
            padding: 0;
        }
        @keyframes slideInRight {
            from { transform: translateX(60px); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- NAVBAR -->
    <nav class="top-nav">
        <div class="nav-inner">
            <a href="{{ url('/') }}" class="nav-brand">
                <img src="{{ asset('images/itb-logo.svg') }}" alt="ITB" class="nav-brand-logo">
                <div class="nav-brand-text">
                    Sistem Informasi Penjadwalan Sidang
                    <div class="nav-brand-sub">FTTM Institut Teknologi Bandung</div>
                </div>
            </a>
            <div class="nav-actions">
                <a href="{{ url('/') }}" class="btn-nav btn-nav-ghost">
                    <i class="fas fa-home"></i> Beranda
                </a>
            </div>
        </div>
    </nav>

    <!-- LOGIN FORM AREA -->
    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-head">
                <div class="login-head-inner">
                    <img src="{{ asset('images/itb-logo.svg') }}" alt="ITB Logo" class="login-logo">
                    <h1>SI SIDANG FTTM ITB</h1>
                    <p>Sistem Informasi Sidang<br>Fakultas Teknik Pertambangan dan Perminyakan</p>
                </div>
            </div>
            <div class="login-body">
                @yield('auth-content')
            </div>
        </div>
    </div>

    <!-- TOASTS -->
    <div class="toast-wrap">
        <div id="toastSuccess" class="toast toast-success">
            <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
            <div class="toast-body">
                <div class="toast-title">Berhasil</div>
                <div class="toast-msg" id="toastSuccessMsg"></div>
            </div>
            <button class="toast-close" onclick="hideToast('success')">&times;</button>
        </div>
        <div id="toastError" class="toast toast-error">
            <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="toast-body">
                <div class="toast-title">Gagal</div>
                <div class="toast-msg" id="toastErrorMsg"></div>
            </div>
            <button class="toast-close" onclick="hideToast('error')">&times;</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showToast(type, message) {
            var id = type === 'success' ? '#toastSuccess' : '#toastError';
            var msgId = type === 'success' ? '#toastSuccessMsg' : '#toastErrorMsg';
            $(msgId).text(message);
            $(id).addClass('show');
            setTimeout(function() { hideToast(type); }, 4000);
        }
        function hideToast(type) {
            var id = type === 'success' ? '#toastSuccess' : '#toastError';
            $(id).removeClass('show');
        }
        $(function () {
            @if(session('success')) showToast('success', '{{ session('success') }}'); @endif
            @if(session('error')) showToast('error', '{{ session('error') }}'); @endif
        });
    </script>
    @stack('scripts')
</body>
</html>
