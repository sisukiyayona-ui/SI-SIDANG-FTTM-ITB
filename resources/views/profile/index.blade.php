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
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{ $user['avatar'] }}" alt="Avatar" class="img-circle img-fluid mb-3"
                         style="width: 120px; height: 120px; border: 4px solid #2f5597;">
                    <h5>{{ $user['name'] }}</h5>
                    <p class="text-muted">{{ $user['role'] }}</p>
                    <span class="badge bg-success">Active</span>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">Nama Lengkap</th>
                            <td>{{ $user['name'] }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user['email'] }}</td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td>{{ $user['username'] }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td><span class="badge bg-primary">{{ $user['role'] }}</span></td>
                        </tr>
                        <tr>
                            <th>Akun INA</th>
                            <td>{{ $user['akun_ina'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
