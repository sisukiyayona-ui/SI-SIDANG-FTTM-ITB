@extends('layouts.master')

@section('title', 'Approve User - SI SIDANG FTTM ITB')
@section('page_title', 'Approve User')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Approve User</li>
    </ol>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user-check mr-2"></i>Data Pendaftaran User</h5>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="userTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#pending" data-toggle="tab">
                        Pending <span class="badge bg-warning ms-1">2</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#approved" data-toggle="tab">
                        Approved <span class="badge bg-success ms-1">1</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#rejected" data-toggle="tab">
                        Rejected <span class="badge bg-danger ms-1">1</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="pending">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Akun INA</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($users as $user)
                                    @if($user['status'] === 'pending')
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $user['name'] }}</td>
                                            <td>{{ $user['email'] }}</td>
                                            <td>{{ $user['username'] }}</td>
                                            <td><span class="badge bg-info">{{ $user['role'] }}</span></td>
                                            <td>{{ $user['akun_ina'] }}</td>
                                            <td>{{ $user['registered_at'] }}</td>
                                            <td>
                                                <form action="{{ route('approve.user.approve', $user['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('approve.user.reject', $user['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Tolak user ini?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if($no === 1)
                                    <tr><td colspan="8" class="text-center">Tidak ada data pending.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane" id="approved">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($users as $user)
                                    @if($user['status'] === 'approved')
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $user['name'] }}</td>
                                            <td>{{ $user['email'] }}</td>
                                            <td>{{ $user['username'] }}</td>
                                            <td><span class="badge bg-info">{{ $user['role'] }}</span></td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane" id="rejected">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($users as $user)
                                    @if($user['status'] === 'rejected')
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $user['name'] }}</td>
                                            <td>{{ $user['email'] }}</td>
                                            <td>{{ $user['username'] }}</td>
                                            <td><span class="badge bg-info">{{ $user['role'] }}</span></td>
                                            <td>{{ $user['rejection_reason'] ?? '-' }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
