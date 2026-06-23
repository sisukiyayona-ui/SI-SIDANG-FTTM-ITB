@extends('layouts.master')

@section('title', 'Detail Prodi - SI SIDANG FTTM ITB')
@section('page_title', 'Detail Program Studi')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('master.prodi.index') }}">Prodi</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Informasi Program Studi</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th style="width: 200px;">Kode Prodi</th>
                    <td><span class="badge bg-info">{{ $prodi['kode'] }}</span></td>
                </tr>
                <tr>
                    <th>Nama Prodi</th>
                    <td>{{ $prodi['nama'] }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-{{ $prodi['status'] === 'Aktif' ? 'success' : 'danger' }}">
                            {{ $prodi['status'] }}
                        </span>
                    </td>
                </tr>
            </table>

            <div class="d-flex gap-2">
                <a href="{{ route('master.prodi.edit', $prodi['id']) }}" class="btn btn-warning">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('master.prodi.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
@endsection
