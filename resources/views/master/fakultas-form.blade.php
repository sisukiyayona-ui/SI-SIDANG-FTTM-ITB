@extends('layouts.master')

@section('title', $fakultas ? 'Edit Fakultas' : 'Tambah Fakultas')
@section('page_title', $fakultas ? 'Edit Fakultas' : 'Tambah Fakultas')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Data Master</a></li>
        <li class="breadcrumb-item"><a href="{{ route('master.fakultas.index') }}">Fakultas</a></li>
        <li class="breadcrumb-item active">{{ $fakultas ? 'Edit' : 'Tambah' }}</li>
    </ol>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $fakultas ? 'Edit Fakultas' : 'Tambah Fakultas Baru' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ $fakultas ? route('master.fakultas.update', $fakultas['id']) : route('master.fakultas.store') }}" method="POST">
                @csrf
                @if($fakultas) @method('PUT') @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="f_kode_fs" class="form-label">Kode Fakultas</label>
                        <input type="text" name="kode_fs" id="f_kode_fs" class="form-control"
                               value="{{ $fakultas['kode'] ?? '' }}" placeholder="Contoh: FTTM" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="f_nama_fs" class="form-label">Nama Fakultas</label>
                        <input type="text" name="nama_fs" id="f_nama_fs" class="form-control"
                               value="{{ $fakultas['nama'] ?? '' }}" placeholder="Nama Fakultas" required>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('master.fakultas.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
