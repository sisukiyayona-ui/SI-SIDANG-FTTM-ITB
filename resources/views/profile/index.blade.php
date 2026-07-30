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
<style>
    /* Profile Container Styles */
    .profile-container {
        --profile-card-bg: #ffffff;
        --profile-border: #e5e7eb;
        --profile-hover-bg: #f9fafb;
        --profile-text-primary: #1f2937;
        --profile-text-secondary: #6b7280;
        --profile-avatar-border: #3b82f6;
        --profile-badge-bg: #3b82f6;
        --profile-badge-text: #ffffff;
        --profile-success: #10b981;
        --profile-warning: #f59e0b;
        --profile-info-bg: #f8fafc;
        --profile-code-bg: #f1f5f9;
        --profile-code-text: #3b82f6;
    }

    html.dark-mode .profile-container {
        --profile-card-bg: #1e293b;
        --profile-border: #334155;
        --profile-hover-bg: #2d3748;
        --profile-text-primary: #f1f5f9;
        --profile-text-secondary: #94a3b8;
        --profile-avatar-border: #60a5fa;
        --profile-badge-bg: #3b82f6;
        --profile-badge-text: #ffffff;
        --profile-success: #10b981;
        --profile-warning: #f59e0b;
        --profile-info-bg: #0f172a;
        --profile-code-bg: #334155;
        --profile-code-text: #60a5fa;
    }

    .profile-container .profile-card {
        background: var(--profile-card-bg);
        border: 1px solid var(--profile-border);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    html.dark-mode .profile-container .profile-card {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
    }

    .profile-container .profile-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    html.dark-mode .profile-container .profile-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
    }

    .profile-container .profile-avatar {
        width: 140px;
        height: 140px;
        border: 4px solid var(--profile-avatar-border);
        object-fit: cover;
        border-radius: 50%;
        transition: transform 0.3s ease;
    }

    .profile-container .profile-avatar:hover {
        transform: scale(1.05);
    }

    .profile-container .profile-status-dot {
        width: 22px;
        height: 22px;
        background: var(--profile-success);
        border: 3px solid var(--profile-card-bg);
        border-radius: 50%;
        position: absolute;
        bottom: 10px;
        right: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .profile-container .profile-name {
        color: var(--profile-text-primary);
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .profile-container .profile-badge {
        background: var(--profile-badge-bg);
        color: var(--profile-badge-text);
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-block;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }

    .profile-container .profile-info-row {
        border-bottom: 1px solid var(--profile-border);
        transition: background-color 0.2s ease;
    }

    .profile-container .profile-info-row:last-child {
        border-bottom: none;
    }

    .profile-container .profile-info-row:hover {
        background-color: var(--profile-hover-bg);
    }

    .profile-container .profile-info-label {
        width: 220px;
        padding: 18px 24px;
        background: var(--profile-info-bg);
        border: none;
        color: var(--profile-text-secondary);
        font-weight: 600;
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .profile-container .profile-info-value {
        padding: 18px 24px;
        border: none;
        font-weight: 500;
        color: var(--profile-text-primary);
        vertical-align: middle;
    }

    .profile-container .profile-code {
        background: var(--profile-code-bg);
        padding: 4px 12px;
        border-radius: 6px;
        color: var(--profile-code-text);
        font-size: 0.875rem;
        font-family: 'Courier New', monospace;
        font-weight: 600;
    }

    .profile-container .profile-section-title {
        color: var(--profile-text-primary);
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 0;
    }

    .profile-container .profile-icon {
        color: var(--profile-avatar-border);
        width: 20px;
        text-align: center;
    }

    .profile-container .profile-divider {
        border-color: var(--profile-border);
        opacity: 1;
    }

    .profile-container .profile-detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        font-size: 0.875rem;
    }

    .profile-container .profile-detail-label {
        color: var(--profile-text-secondary);
        font-weight: 500;
    }

    .profile-container .profile-detail-value {
        color: var(--profile-text-primary);
        font-weight: 600;
    }

    .profile-container .profile-status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .profile-container .profile-status-active {
        background: rgba(16, 185, 129, 0.1);
        color: var(--profile-success);
    }

    html.dark-mode .profile-container .profile-status-active {
        background: rgba(16, 185, 129, 0.2);
    }

    .profile-container .profile-status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: var(--profile-warning);
    }

    html.dark-mode .profile-container .profile-status-pending {
        background: rgba(245, 158, 11, 0.2);
    }

    .profile-container .card-header {
        background: transparent;
        border-bottom: 1px solid var(--profile-border);
        padding: 20px 24px;
    }

    .profile-container .card-body {
        padding: 0;
    }

    .profile-container .profile-sidebar {
        padding: 32px 24px;
    }

    @media (max-width: 768px) {
        .profile-container .profile-info-label {
            width: 140px;
            padding: 14px 16px;
            font-size: 0.8rem;
        }
        
        .profile-container .profile-info-value {
            padding: 14px 16px;
            font-size: 0.85rem;
        }
    }
</style>

@php $user = session('auth_user'); @endphp
<div class="profile-container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="profile-card">
                <div class="profile-sidebar text-center">
                    <div class="position-relative d-inline-block mb-4">
                        <img src="{{ $user['avatar'] }}" alt="Avatar" class="profile-avatar">
                        <span class="profile-status-dot"></span>
                    </div>
                    <h5 class="profile-name">{{ $user['nama_lengkap'] }}</h5>
                    <p class="mb-3">
                        <span class="profile-badge">{{ $user['role'] }}</span>
                    </p>
                    @if($user['strata'])
                        <span class="badge badge-secondary px-3 py-1" style="font-size: 0.85rem;">{{ $user['strata'] }}</span>
                    @endif
                    <hr class="profile-divider my-4">
                    <div class="text-start">
                        @if($user['nip_nim'])
                            <div class="profile-detail-item">
                                <span class="profile-detail-label">NIP/NIM</span>
                                <span class="profile-detail-value">{{ $user['nip_nim'] }}</span>
                            </div>
                        @endif
                        @if($user['strata'])
                            <div class="profile-detail-item">
                                <span class="profile-detail-label">Strata</span>
                                <span class="profile-detail-value">{{ $user['strata'] }}</span>
                            </div>
                        @endif
                        @if($user['nama_prodi'])
                            <div class="profile-detail-item">
                                <span class="profile-detail-label">Program Studi</span>
                                <span class="profile-detail-value">{{ $user['nama_prodi'] }}</span>
                            </div>
                        @endif
                        <div class="profile-detail-item">
                            <span class="profile-detail-label">Status</span>
                            <span class="profile-status-badge {{ $user['status'] === 'approved' ? 'profile-status-active' : 'profile-status-pending' }}">
                                {{ $user['status'] === 'approved' ? 'Active' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-4">
            <div class="profile-card">
                <div class="card-header">
                    <h5 class="profile-section-title">
                        <i class="fas fa-id-card profile-icon mr-2"></i>Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0" style="border-collapse: separate; border-spacing: 0;">
                            <tbody>
                                <tr class="profile-info-row">
                                    <th class="profile-info-label">
                                        <i class="fas fa-user profile-icon mr-2"></i>Nama Lengkap
                                    </th>
                                    <td class="profile-info-value">
                                        {{ $user['nama_lengkap'] }}
                                    </td>
                                </tr>
                                <tr class="profile-info-row">
                                    <th class="profile-info-label">
                                        <i class="fas fa-envelope profile-icon mr-2"></i>Email
                                    </th>
                                    <td class="profile-info-value">
                                        {{ $user['email'] }}
                                    </td>
                                </tr>
                                <tr class="profile-info-row">
                                    <th class="profile-info-label">
                                        <i class="fas fa-user-circle profile-icon mr-2"></i>Username
                                    </th>
                                    <td class="profile-info-value">
                                        <code class="profile-code">{{ $user['Username'] }}</code>
                                    </td>
                                </tr>
                                <tr class="profile-info-row">
                                    <th class="profile-info-label">
                                        <i class="fas fa-shield-alt profile-icon mr-2"></i>Role
                                    </th>
                                    <td class="profile-info-value">
                                        <span class="profile-badge">{{ $user['role'] }}</span>
                                    </td>
                                </tr>
                                <tr class="profile-info-row">
                                    <th class="profile-info-label">
                                        <i class="fas fa-globe profile-icon mr-2"></i>Akun INA
                                    </th>
                                    <td class="profile-info-value">
                                        @if($user['akun_ina'])
                                            <span class="d-inline-flex align-items-center">
                                                <i class="fas fa-check-circle mr-2" style="color: var(--profile-success);"></i>
                                                {{ $user['akun_ina'] }}
                                            </span>
                                        @else
                                            <span style="color: var(--profile-text-secondary); font-style: italic;">—</span>
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
</div>
@endsection
