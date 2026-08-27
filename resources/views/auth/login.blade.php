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
                <button type="button" class="btn-toggle-pass" id="togglePassBtn">
                    <i class="fas fa-eye" id="eyeIcon"></i>
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
        <div class="sso-section" style="margin-top: 12px;">
            <a href="{{ route('sso.redirect') }}" class="btn-sso">
                <img src="{{ asset('images/itb-logo.svg') }}" alt="ITB" class="sso-logo">
                Masuk dengan SSO ITB
            </a>
        </div>
        @endif


    </form>

    @push('scripts')
    <script>
        document.getElementById('togglePassBtn').addEventListener('click', function() {
            var p = document.getElementById('password');
            var e = document.getElementById('eyeIcon');
            if (p.type === 'password') {
                p.type = 'text';
                e.className = 'fas fa-eye-slash';
            } else {
                p.type = 'password';
                e.className = 'fas fa-eye';
            }
        });


    </script>
    @endpush
@endsection
