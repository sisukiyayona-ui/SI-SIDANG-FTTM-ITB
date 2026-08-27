@extends('layouts.auth')

@section('title', 'Lupa Password - SI SIDANG FTTM ITB')

@section('auth-content')
    <div style="margin-bottom: 24px; text-align: center;">
        <h2 style="font-size: 1.1rem; font-weight: 600; color: var(--text); margin-bottom: 8px;">Lupa Password</h2>
        <p style="font-size: 0.85rem; color: var(--muted); line-height: 1.4;">Masukkan email Anda untuk menerima tautan reset password.</p>
    </div>

    <form action="{{ route('password.email') }}" method="POST" autocomplete="off">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <div class="input-wrap">
                <i class="fas fa-envelope icon-left"></i>
                <input type="email" name="email" id="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="email@fttm.itb.ac.id" required autofocus>
            </div>
            @error('email')
                <span class="text-danger"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn-submit" style="margin-top: 12px;">
            <i class="fas fa-paper-plane"></i> Kirim Tautan Reset
        </button>

        <div class="register-row" style="margin-top: 24px;">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
            </a>
        </div>
    </form>
@endsection
