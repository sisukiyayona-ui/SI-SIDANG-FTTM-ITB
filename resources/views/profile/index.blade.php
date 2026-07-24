@extends('layouts.master')

@section('title', 'Profile - SI SIDANG FTTM ITB')
@section('page_title', 'Profile')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Profile</li>
    </ol>
@endsection

@section('content')
    @php $user = session('auth_user'); @endphp
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $user['avatar'] }}" alt="Avatar"
                             class="img-circle img-fluid"
                             style="width: 130px; height: 130px; border: 4px solid var(--primary-blue); object-fit: cover;">
                        <span class="position-absolute"
                              style="bottom: 5px; right: 5px; width: 22px; height: 22px; background: #22c55e; border: 3px solid #fff; border-radius: 50%; display: block;"></span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user['nama_lengkap'] }}</h5>
                    <p class="text-muted mb-2" style="font-size: 0.9rem;">
                        <span class="badge bg-primary px-3 py-1">{{ $user['role'] }}</span>
                    </p>
                    @if($user['strata'])
                        <span class="badge bg-secondary px-2 py-1">{{ $user['strata'] }}</span>
                    @endif
                    <hr class="my-3" style="border-color: #e5e7eb;">
                    <div class="text-start small">
                        @if($user['nip_nim'])
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">NIP/NIM</span>
                                <span class="fw-medium">{{ $user['nip_nim'] }}</span>
                            </div>
                        @endif
                        @if($user['strata'])
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Strata</span>
                                <span class="fw-medium">{{ $user['strata'] }}</span>
                            </div>
                        @endif
                        @if($user['nama_prodi'])
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Program Studi</span>
                                <span class="fw-medium">{{ $user['nama_prodi'] }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Status</span>
                            <span class="badge bg-{{ $user['status'] === 'approved' ? 'success' : 'warning' }} px-2 py-1">
                                {{ $user['status'] === 'approved' ? 'Active' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid #e5e7eb;">
                    <h5 class="mb-0 fw-bold" style="color: var(--text-dark);">
                        <i class="fas fa-id-card mr-2" style="color: var(--primary-blue);"></i>Informasi Akun
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" style="border-collapse: separate; border-spacing: 0;">
                            <tbody>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <th style="width: 200px; padding: 16px 24px; background: #f8fafc; border: none; color: var(--text-muted); font-weight: 500; font-size: 0.9rem; vertical-align: middle;">
                                        <i class="fas fa-user mr-2" style="color: var(--primary-blue); width: 18px;"></i>Nama Lengkap
                                    </th>
                                    <td style="padding: 16px 24px; border: none; font-weight: 500; color: var(--text-dark); vertical-align: middle;">
                                        {{ $user['nama_lengkap'] }}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <th style="width: 200px; padding: 16px 24px; background: #f8fafc; border: none; color: var(--text-muted); font-weight: 500; font-size: 0.9rem; vertical-align: middle;">
                                        <i class="fas fa-envelope mr-2" style="color: var(--primary-blue); width: 18px;"></i>Email
                                    </th>
                                    <td style="padding: 16px 24px; border: none; color: var(--text-dark); vertical-align: middle;">
                                        {{ $user['email'] }}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <th style="width: 200px; padding: 16px 24px; background: #f8fafc; border: none; color: var(--text-muted); font-weight: 500; font-size: 0.9rem; vertical-align: middle;">
                                        <i class="fas fa-user-circle mr-2" style="color: var(--primary-blue); width: 18px;"></i>Username
                                    </th>
                                    <td style="padding: 16px 24px; border: none; color: var(--text-dark); vertical-align: middle;">
                                        <code style="background: #f1f5f9; padding: 3px 10px; border-radius: 4px; color: var(--primary-blue); font-size: 0.85rem;">{{ $user['Username'] }}</code>
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <th style="width: 200px; padding: 16px 24px; background: #f8fafc; border: none; color: var(--text-muted); font-weight: 500; font-size: 0.9rem; vertical-align: middle;">
                                        <i class="fas fa-shield-alt mr-2" style="color: var(--primary-blue); width: 18px;"></i>Role
                                    </th>
                                    <td style="padding: 16px 24px; border: none; vertical-align: middle;">
                                        <span class="badge bg-primary px-3 py-1" style="font-size: 0.85rem;">{{ $user['role'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 200px; padding: 16px 24px; background: #f8fafc; border: none; color: var(--text-muted); font-weight: 500; font-size: 0.9rem; vertical-align: middle;">
                                        <i class="fas fa-globe mr-2" style="color: var(--primary-blue); width: 18px;"></i>Akun INA
                                    </th>
                                    <td style="padding: 16px 24px; border: none; color: var(--text-dark); vertical-align: middle;">
                                        @if($user['akun_ina'])
                                            <span class="d-inline-flex align-items-center">
                                                <i class="fas fa-check-circle text-success mr-1"></i>
                                                {{ $user['akun_ina'] }}
                                            </span>
                                        @else
                                            <span class="text-muted font-italic">—</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
