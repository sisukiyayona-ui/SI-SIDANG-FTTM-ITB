@extends('layouts.master')

@section('title', 'Form Mata Kuliah Prodi - SI SIDANG FTTM ITB')
@section('page_title', 'Form Mata Kuliah Prodi')

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="#">Data Master</a></li>
    <li class="breadcrumb-item"><a href="{{ route('master.mk-prodi.index') }}">Mata Kuliah Prodi</a></li>
    <li class="breadcrumb-item active">{{ $item ? 'Edit' : 'Tambah' }}</li>
</ol>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ $item ? route('master.mk-prodi.update', $item['id']) : route('master.mk-prodi.store') }}" method="POST">
            @csrf
            @if($item) @method('PUT') @endif
            <div class="form-group">
                <label>Kode Prodi</label>
                <select name="kode_prodi" id="kode_prodi" class="form-control" required>
                    <option value="">-- Pilih Prodi --</option>
                    @foreach($prodiList as $p)
                        <option value="{{ $p['kode'] }}" data-nama="{{ $p['nama'] }}" {{ ($item['kode_prodi'] ?? '') == $p['kode'] ? 'selected' : '' }}>
                            {{ $p['kode'] }} - {{ $p['nama'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Nama Prodi</label>
                <input type="text" name="nama_prodi" id="nama_prodi" class="form-control" value="{{ $item['nama_prodi'] ?? '' }}" required readonly>
            </div>
            <div class="form-group">
                <label>Kode MK</label>
                <input type="text" name="kode_mk" class="form-control" value="{{ $item['kode_mk'] ?? '' }}" required>
            </div>
            <div class="form-group">
                <label>Tahapan Sidang</label>
                <select name="tahapan_sidang" class="form-control" required>
                    <option value="">-- Pilih Tahapan --</option>
                    @foreach($tahapanList as $t)
                        <option value="{{ $t['value'] }}" {{ ($item['tahapan_sidang'] ?? '') == $t['value'] ? 'selected' : '' }}>
                            {{ $t['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('master.mk-prodi.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var sel = document.getElementById('kode_prodi');
    var nama = document.getElementById('nama_prodi');
    function sync() {
        var opt = sel.options[sel.selectedIndex];
        if (opt && opt.dataset.nama) {
            nama.value = opt.dataset.nama;
        }
    }
    sel.addEventListener('change', sync);
    sync();
});
</script>
@endpush
@endsection