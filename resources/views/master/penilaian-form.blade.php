@extends('layouts.master')

@section('title', $penilaian ? 'Edit Komponen Penilaian' : 'Tambah Komponen Penilaian')
@section('page_title', $penilaian ? 'Edit Komponen Penilaian' : 'Tambah Komponen Penilaian')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
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

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama" class="form-label">Nama Komponen</label>
                        <input type="text" name="nama" id="nama" class="form-control"
                               value="{{ $penilaian['nama'] ?? '' }}" placeholder="Nama komponen" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="bobot" class="form-label">Bobot (%)</label>
                        <input type="number" name="bobot" id="bobot" class="form-control"
                               value="{{ $penilaian['bobot'] ?? '' }}" min="1" max="100" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="komponen" class="form-label">Komponen Sidang</label>
                        <select name="komponen" id="komponen" class="form-select">
                            @foreach(['Sidang Proposal', 'Seminar Kemajuan', 'Sidang Akhir'] as $opt)
                                <option value="{{ $opt }}" {{ ($penilaian['komponen'] ?? '') === $opt ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
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
