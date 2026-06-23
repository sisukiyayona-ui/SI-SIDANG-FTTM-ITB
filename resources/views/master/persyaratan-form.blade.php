@extends('layouts.master')

@section('title', $persyaratan ? 'Edit Persyaratan' : 'Tambah Persyaratan')
@section('page_title', $persyaratan ? 'Edit Persyaratan' : 'Tambah Persyaratan')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('master.persyaratan.index') }}">Persyaratan</a></li>
        <li class="breadcrumb-item active">{{ $persyaratan ? 'Edit' : 'Tambah' }}</li>
    </ol>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $persyaratan ? 'Edit Persyaratan' : 'Tambah Persyaratan Baru' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ $persyaratan ? route('master.persyaratan.update', $persyaratan['id']) : route('master.persyaratan.store') }}" method="POST">
                @csrf
                @if($persyaratan) @method('PUT') @endif

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Persyaratan</label>
                    <input type="text" name="nama" id="nama" class="form-control"
                           value="{{ $persyaratan['nama'] ?? '' }}" placeholder="Nama persyaratan" required>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="form-control"
                              placeholder="Deskripsi persyaratan">{{ $persyaratan['keterangan'] ?? '' }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" name="wajib" value="1" class="form-check-input"
                                {{ !isset($persyaratan['wajib']) || $persyaratan['wajib'] ? 'checked' : '' }}>
                            <label class="form-check-label">Wajib</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="wajib" value="0" class="form-check-input"
                                {{ isset($persyaratan['wajib']) && !$persyaratan['wajib'] ? 'checked' : '' }}>
                            <label class="form-check-label">Opsional</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('master.persyaratan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
