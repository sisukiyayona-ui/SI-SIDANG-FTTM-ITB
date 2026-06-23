@extends('layouts.auth')

@section('title', 'Register - SI SIDANG FTTM ITB')

@section('auth-content')
    <div class="card auth-card">
        <div class="card-header text-center">
            <img src="{{ asset('images/itb-logo.svg') }}"
                 alt="ITB Logo" class="auth-logo">
            <h3 class="text-white">SI SIDANG FTTM ITB</h3>
            <p class="text-white mb-0">Daftar Akun Baru</p>
        </div>
        <div class="card-body">
            <form action="{{ route('register') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="Nama Lengkap" required>
                    </div>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="email@fttm.itb.ac.id" required>
                    </div>
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <input type="text" name="username" id="username"
                               class="form-control @error('username') is-invalid @enderror"
                               value="{{ old('username') }}" placeholder="Username" required>
                    </div>
                    @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 6 karakter" required>
                        </div>
                        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control" placeholder="Ulangi password" required>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="akun_ina" class="form-label">Akun INA</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="text" name="akun_ina" id="akun_ina"
                               class="form-control @error('akun_ina') is-invalid @enderror"
                               value="{{ old('akun_ina') }}" placeholder="username.ina" required>
                    </div>
                    @error('akun_ina') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <button type="submit" class="btn btn-auth text-white w-100">
                    <i class="fas fa-user-plus mr-2"></i> Daftar
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="mb-0">Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold" style="color: var(--primary);">
                        Login
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
