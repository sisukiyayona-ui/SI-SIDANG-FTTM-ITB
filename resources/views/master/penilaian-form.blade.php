@extends('layouts.master')

@section('title', $penilaian ? 'Edit Komponen Penilaian' : 'Tambah Komponen Penilaian')
@section('page_title', $penilaian ? 'Edit Komponen Penilaian' : 'Tambah Komponen Penilaian')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Data Master</a></li>
        <li class="breadcrumb-item"><a href="{{ route('master.penilaian.index') }}">Penilaian</a></li>
        <li class="breadcrumb-item active">{{ $penilaian ? 'Edit' : 'Tambah' }}</li>
    </ol>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $penilaian ? 'Edit Komponen Penilaian' : 'Tambah Komponen Penilaian Baru' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ $penilaian ? route('master.penilaian.update', $penilaian['id']) : route('master.penilaian.store') }}" method="POST">
                @csrf
                @if($penilaian) @method('PUT') @endif

                <div class="mb-3">
                    <label for="nama" class="form-label">Parameter Penilaian</label>
                    <input type="text" name="nama" id="nama" class="form-control"
                           value="{{ $penilaian['nama'] ?? '' }}" placeholder="Nama parameter komponen" required>
                </div>
                <div class="mb-3">
                    <label for="no_form" class="form-label">No Form</label>
                    <input type="text" name="no_form" id="no_form" class="form-control"
                           value="{{ $penilaian['no_form'] ?? '' }}" placeholder="Nomor form">
                </div>
                <div class="mb-3">
                    <label for="tahapan_sidang" class="form-label">Tahapan Sidang</label>
                    <select name="tahapan_sidang" id="tahapan_sidang" class="form-control" required>
                        @foreach($tahapans as $t)
                            <option value="{{ $t->Tahapan }}" {{ isset($penilaian) && $penilaian['tahapan_sidang'] == $t->Tahapan ? 'selected' : '' }}>{{ $t->Tahapan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="strata" class="form-label">Strata</label>
                    <select name="strata" id="strata" class="form-control" required>
                        <option value="S1" {{ isset($penilaian) && $penilaian['strata'] == 'S1' ? 'selected' : '' }}>S1</option>
                        <option value="S2" {{ isset($penilaian) && $penilaian['strata'] == 'S2' ? 'selected' : '' }}>S2</option>
                        <option value="S3" {{ !isset($penilaian) || $penilaian['strata'] == 'S3' ? 'selected' : '' }}>S3</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_prodi" class="form-label">Program Studi</label>
                    <select name="id_prodi" id="id_prodi" class="form-control" required>
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}" {{ isset($penilaian) && $penilaian['id_prodi'] == $p->id ? 'selected' : '' }}>{{ $p->kode_prodi }} - {{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status Aktif</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" name="status_aktif" value="AKTIF" class="form-check-input" id="statusAktif"
                                {{ !isset($penilaian) || $penilaian['status_aktif'] == 'AKTIF' ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusAktif">AKTIF</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="status_aktif" value="NON AKTIF" class="form-check-input" id="statusNonaktif"
                                {{ isset($penilaian) && $penilaian['status_aktif'] == 'NON AKTIF' ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusNonaktif">NON AKTIF</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status Catatan</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" name="status_catatan" value="y" class="form-check-input" id="statusCatatanYa"
                                {{ isset($penilaian) && $penilaian['status_catatan'] == 'y' ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusCatatanYa">Ya (y)</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="status_catatan" value="t" class="form-check-input" id="statusCatatanTidak"
                                {{ !isset($penilaian) || $penilaian['status_catatan'] == 't' ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusCatatanTidak">Tidak (t)</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="Keterangan" id="keterangan" class="form-control" placeholder="Keterangan tambahan" rows="2">{{ $penilaian['Keterangan'] ?? '' }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('master.penilaian.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
