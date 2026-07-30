@extends('layouts.master')

@section('title', $persyaratan ? 'Edit Persyaratan' : 'Tambah Persyaratan')
@section('page_title', $persyaratan ? 'Edit Persyaratan' : 'Tambah Persyaratan')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Data Master</a></li>
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
                    <label for="nama_persyaratan" class="form-label">Nama Persyaratan</label>
                    <input type="text" name="nama_persyaratan" id="nama_persyaratan" class="form-control"
                           value="{{ $persyaratan['nama'] ?? '' }}" placeholder="Nama persyaratan" required>
                </div>

                <div class="mb-3">
                    <label for="tahapan_sidang" class="form-label">Tahapan Sidang</label>
                    <select name="tahapan_sidang" id="tahapan_sidang" class="form-control" required>
                        @foreach($tahapans as $t)
                            <option value="{{ $t->Tahapan }}" {{ isset($persyaratan) && $persyaratan['tahapan_sidang'] == $t->Tahapan ? 'selected' : '' }}>{{ $t->Tahapan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="strata" class="form-label">Strata</label>
                    <select name="strata" id="strata" class="form-control" required>
                        <option value="S1" {{ isset($persyaratan) && $persyaratan['strata'] == 'S1' ? 'selected' : '' }}>S1</option>
                        <option value="S2" {{ isset($persyaratan) && $persyaratan['strata'] == 'S2' ? 'selected' : '' }}>S2</option>
                        <option value="S3" {{ !isset($persyaratan) || $persyaratan['strata'] == 'S3' ? 'selected' : '' }}>S3</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_prodi" class="form-label">Program Studi</label>
                    @php $isTuProdi = session('auth_user.role') === 'TU Prodi'; @endphp
                    <select name="id_prodi" id="id_prodi" class="form-control" {{ $isTuProdi ? 'disabled' : '' }}>
                        @forelse($prodis as $p)
                            <option value="{{ $p->id }}" {{ $isTuProdi ? 'selected' : (isset($persyaratan) && $persyaratan['id_prodi'] == $p->id ? 'selected' : '') }}>{{ $p->kode_prodi }} - {{ $p->nama_prodi }}</option>
                        @empty
                            <option value="">Prodi tidak tersedia</option>
                        @endforelse
                    </select>
                    @if($isTuProdi)
                        <input type="hidden" name="id_prodi" value="{{ $userProdiId }}">
                    @endif
                </div>

                <div class="mb-4">
                    <label class="form-label">Status Aktif</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" name="status_aktif" value="AKTIF" class="form-check-input" id="statusAktif"
                                {{ !isset($persyaratan) || $persyaratan['status_aktif'] == 'AKTIF' ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusAktif">AKTIF</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="status_aktif" value="NON AKTIF" class="form-check-input" id="statusNonaktif"
                                {{ isset($persyaratan) && $persyaratan['status_aktif'] == 'NON AKTIF' ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusNonaktif">NON AKTIF</label>
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
