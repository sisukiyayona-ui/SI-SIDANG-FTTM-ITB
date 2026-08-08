@extends('layouts.master')

@section('title', 'Detail Fakultas - SI SIDANG FTTM ITB')
@section('page_title', 'Detail Fakultas')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Data Master</a></li>
        <li class="breadcrumb-item"><a href="{{ route('master.fakultas.index') }}">Fakultas</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>
@endsection

@section('content')
    <div class="card">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Informasi Fakultas</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th style="width: 200px;" class="fw-semibold text-secondary">Kode Fakultas</th>
                    <td><span class="badge bg-info">{{ $fakultas['kode'] }}</span></td>
                </tr>
                <tr>
                    <th class="fw-semibold text-secondary">Nama Fakultas</th>
                    <td>{{ $fakultas['nama'] }}</td>
                </tr>
            </table>

            <div class="d-flex gap-2">
                <a href="{{ route('master.fakultas.edit', $fakultas['id']) }}" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; padding: 10px 25px; border: none;">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('master.fakultas.index') }}" class="btn" style="border-radius: 8px; padding: 10px 25px; border: 1px solid #6c757d;">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
@endsection
