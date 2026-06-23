@extends('layouts.auth')

@section('title', 'Login - SI SIDANG FTTM ITB')

@section('auth-content')
    <form action="{{ route('login') }}" method="POST" autocomplete="off">
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

        {{-- Demo credentials --}}
        <div class="demo-box">
            <div class="demo-title"><i class="fas fa-info-circle mr-1"></i> Akun Demo</div>
            <div class="demo-grid">
                <div class="demo-item" onclick="fillCred('admin','admin123')">
                    <div class="demo-role">Admin</div>
                    <div class="demo-cred">admin</div>
                    <div class="demo-cred">admin123</div>
                </div>
                <div class="demo-item" onclick="fillCred('mahasiswa','mhs123')">
                    <div class="demo-role">Mahasiswa</div>
                    <div class="demo-cred">mahasiswa</div>
                    <div class="demo-cred">mhs123</div>
                </div>
                <div class="demo-item" onclick="fillCred('pembimbing','dosen123')">
                    <div class="demo-role">Dosen</div>
                    <div class="demo-cred">pembimbing</div>
                    <div class="demo-cred">dosen123</div>
                </div>
            </div>
        </div>
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
        function fillCred(user, pass) {
            document.getElementById('username').value = user;
            document.getElementById('password').value = pass;
        }
    </script>
    @endpush
@endsection
