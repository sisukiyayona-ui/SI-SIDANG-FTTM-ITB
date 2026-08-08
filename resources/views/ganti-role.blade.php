@extends('layouts.master')

@section('title', 'Ganti Role - SI SIDANG FTTM ITB')
@section('page_title', 'Ganti Role')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Ganti Role</li>
    </ol>
@endsection

@php
    $authUser   = session('auth_user');
    $userRow    = $authUser ? \App\Models\TUser::find($authUser['id']) : null;
    $roles      = $authUser ? \App\Models\TUserRole::where('ID_USER', $authUser['id'])->orderBy('STATUS_DEFAULT', 'desc')->get() : collect();
    $activeRole = session('auth_user.role');
@endphp

@section('content')
<div class="card">
    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff;">
        <h5 class="mb-0" style="color: #fff;"><i class="fas fa-exchange-alt mr-2"></i>Ganti Role</h5>
    </div>
    <div class="card-body">
        @if($userRow)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIP/NIM</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Status Pegawai</th>
                            <th>Program Studi</th>
                            <th>Status Aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>{{ $userRow->NIP_NIM }}</td>
                            <td>{{ $userRow->NAMA_LENGKAP }}</td>
                            <td>{{ $userRow->EMAIL }}</td>
                            <td>{{ $userRow->STATUS_PEGAWAI ?? '-' }}</td>
                            <td>{{ $userRow->NAMA_PRODI ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $userRow->STATUS_AKTIF === 'AKTIF' ? 'success' : 'danger' }}">
                                    {{ $userRow->STATUS_AKTIF }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr>
            <h6 class="text-secondary"><i class="fas fa-user-tag mr-1"></i>Role Anda:</h6>
            <div class="d-flex flex-wrap" style="gap: 10px;">
                @forelse($roles as $role)
                    <form method="POST" action="{{ route('ganti-role') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="role" value="{{ $role->ROLE }}">
                        <button type="submit"
                                class="btn {{ $role->ROLE === $activeRole ? 'btn-primary' : 'btn-outline-primary' }}"
                                style="border-radius: 20px; padding: 6px 16px;">
                            {{ $role->ROLE }}
                            @if($role->STATUS_DEFAULT === 't')
                                <span class="badge badge-light ml-1">default</span>
                            @endif
                        </button>
                    </form>
                @empty
                    <p class="text-muted mb-0">Tidak ada role tersedia.</p>
                @endforelse
            </div>
            <small class="text-muted d-block mt-3">Klik salah satu role untuk langsung beralih tanpa login ulang.</small>
        @else
            <p class="text-muted mb-0">Data user tidak ditemukan.</p>
        @endif
    </div>
</div>
@endsection
