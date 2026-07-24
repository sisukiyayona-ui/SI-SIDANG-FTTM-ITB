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
                <button type="button" class="btn-toggle-pass" onclick="togglePass()">
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

        <hr class="divider">

        <div class="register-row">
            Belum punya akun?
            <a href="{{ route('register') }}">Daftar Sekarang</a>
        </div>

        <div class="demo-section">
            <div class="demo-header">
                <i class="fas fa-user-circle"></i> Akun Demo
            </div>
            <div class="demo-cards">
                @php
                    $demos = [
                        ['role' => 'Admin', 'icon' => 'fa-user-shield', 'user' => 'admin', 'pass' => 'admin123'],
                        ['role' => 'TU Prodi', 'icon' => 'fa-user-tie', 'user' => 'tuprodi', 'pass' => 'prodi123'],
                        ['role' => 'TU FS', 'icon' => 'fa-building', 'user' => 'tufs', 'pass' => 'fs123'],
                        ['role' => 'Pembimbing', 'icon' => 'fa-chalkboard-teacher', 'user' => 'pembimbing', 'pass' => 'dosen123'],
                        ['role' => 'Penguji', 'icon' => 'fa-user-check', 'user' => 'penguji', 'pass' => 'dosen123'],
                        ['role' => 'Monev', 'icon' => 'fa-clipboard-check', 'user' => 'monev', 'pass' => 'dosen123'],
                        ['role' => 'Mhs S3', 'icon' => 'fa-user-graduate', 'user' => 'ulvienin', 'pass' => 'mhs123'],
                        ['role' => 'Mhs S3', 'icon' => 'fa-user-graduate', 'user' => 'dede', 'pass' => 'mhs123'],
                    ];
                @endphp
                @foreach($demos as $demo)
                    <div class="demo-card" onclick="fillCred('{{ $demo['user'] }}','{{ $demo['pass'] }}', true)">
                        <div class="demo-card-icon">
                            <i class="fas {{ $demo['icon'] }}"></i>
                        </div>
                        <div class="demo-card-content">
                            <div class="demo-card-role">{{ $demo['role'] }}</div>
                            <div class="demo-card-username">{{ $demo['user'] }}</div>
                            <div class="demo-card-password">{{ $demo['pass'] }}</div>
                        </div>
                        <div class="demo-card-action">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if(config('sso.enabled'))
        <hr class="divider">

        <div class="sso-section">
            <a href="{{ route('sso.redirect') }}" class="btn-sso">
                <img src="{{ asset('images/itb-logo.svg') }}" alt="ITB" class="sso-logo">
                Masuk dengan SSO ITB
            </a>
        </div>
        @endif
    </form>

    @push('scripts')
    <script>
        function togglePass() {
            var p = document.getElementById('password');
            var e = document.getElementById('eyeIcon');
            if (p.type === 'password') {
                p.type = 'text';
                e.className = 'fas fa-eye-slash';
            } else {
                p.type = 'password';
                e.className = 'fas fa-eye';
            }
        }
        function fillCred(user, pass, autoSubmit) {
            document.getElementById('username').value = user;
            document.getElementById('password').value = pass;
            if (autoSubmit) {
                document.getElementById('loginForm').submit();
            }
        }
    </script>
    @endpush
@endsection
