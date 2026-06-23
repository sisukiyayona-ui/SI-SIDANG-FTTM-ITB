@extends('layouts.master')

@section('title', $prodi ? 'Edit Prodi' : 'Tambah Prodi')
@section('page_title', $prodi ? 'Edit Program Studi' : 'Tambah Program Studi')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('master.prodi.index') }}">Prodi</a></li>
        <li class="breadcrumb-item active">{{ $prodi ? 'Edit' : 'Tambah' }}</li>
    </ol>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $prodi ? 'Edit Program Studi' : 'Tambah Program Studi Baru' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ $prodi ? route('master.prodi.update', $prodi['id']) : route('master.prodi.store') }}" method="POST">
                @csrf
                @if($prodi) @method('PUT') @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kode" class="form-label">Kode Prodi</label>
                        <input type="text" name="kode" id="kode" class="form-control"
                               value="{{ $prodi['kode'] ?? '' }}" placeholder="Contoh: 322" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama" class="form-label">Nama Prodi</label>
                        <input type="text" name="nama" id="nama" class="form-control"
                               value="{{ $prodi['nama'] ?? '' }}" placeholder="Nama Program Studi" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" name="status" value="Aktif" class="form-check-input"
                                {{ !$prodi || $prodi['status'] === 'Aktif' ? 'checked' : '' }}>
                            <label class="form-check-label">Aktif</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="status" value="Nonaktif" class="form-check-input"
                                {{ $prodi && $prodi['status'] === 'Nonaktif' ? 'checked' : '' }}>
                            <label class="form-check-label">Nonaktif</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('master.prodi.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
