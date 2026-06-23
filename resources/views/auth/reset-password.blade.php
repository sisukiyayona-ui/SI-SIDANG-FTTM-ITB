@extends('layouts.auth')

@section('title', 'Reset Password - SI SIDANG FTTM ITB')

@section('auth-content')
    <div class="card auth-card">
        <div class="card-header text-center">
            <img src="{{ asset('images/itb-logo.svg') }}"
                 alt="ITB Logo" class="auth-logo">
            <h3 class="text-white">SI SIDANG FTTM ITB</h3>
            <p class="text-white mb-0">Reset Password</p>
        </div>
        <div class="card-body">
            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token ?? '' }}">

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
                    <label for="password" class="form-label">Password Baru</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Min. 6 karakter" required>
                    </div>
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control" placeholder="Ulangi password baru" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-auth text-white w-100">
                    <i class="fas fa-save mr-2"></i> Reset Password
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="mb-0">
                    <a href="{{ route('login') }}" class="text-decoration-none" style="color: var(--accent);">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
