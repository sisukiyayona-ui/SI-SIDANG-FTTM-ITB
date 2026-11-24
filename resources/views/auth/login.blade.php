@extends('layouts.auth')

@section('title', 'Login - SI SIDANG FTTM ITB')

@section('auth-content')
    <form action="{{ route('login') }}" method="POST" autocomplete="off" id="loginForm">
        @csrf

        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <div class="input-wrap">
                <i class="fas fa-user icon-left"></i>
                <input type="text" name="username" id="username"
                       class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
            </div>
            @error('username')
                <span class="text-danger"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock icon-left"></i>
                <input type="password" name="password" id="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Masukkan password" required>
                <button type="button" class="btn-toggle-pass" id="togglePassBtn" aria-label="Tampilkan password" aria-pressed="false">
                    <i class="fas fa-eye" id="eyeIcon" aria-hidden="true"></i>
                </button>
            </div>
            @error('password')
                <span class="text-danger"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
            @enderror
        </div>

        <div class="remember-row">
            <label class="check-label">
                <input type="checkbox" name="remember" id="remember">
                Ingat Saya
            </label>
            <a href="{{ route('password.request') }}" class="forgot-link">Lupa Password?</a>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-sign-in-alt"></i> Masuk
        </button>

        @if(config('sso.enabled'))
        <div class="sso-section">
            <div class="sso-divider"><span>atau</span></div>
            <a href="{{ route('sso.redirect') }}" class="btn-sso">
                <img src="{{ asset('images/itb-logo.svg') }}" alt="" class="sso-logo">
                Masuk dengan SSO ITB
            </a>
        </div>
        @endif

    </form>

    @push('scripts')
    <script nonce="{{ request()->attributes->get('csp_nonce') ?? '' }}">
        document.getElementById('togglePassBtn').addEventListener('click', function() {
            var password = document.getElementById('password');
            var icon = document.getElementById('eyeIcon');
            var showPassword = password.type === 'password';
            password.type = showPassword ? 'text' : 'password';
            icon.className = showPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
            this.setAttribute('aria-label', showPassword ? 'Sembunyikan password' : 'Tampilkan password');
            this.setAttribute('aria-pressed', showPassword ? 'true' : 'false');
        });
    </script>
    @endpush
@endsection
