@extends('layouts.master')

@section('title', 'Ubah Judul - SI SIDANG FTTM ITB')
@section('page_title', 'Ubah Judul')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('sidang.s3') }}">Sidang S3</a></li>
        <li class="breadcrumb-item active">Ubah Judul</li>
    </ol>
@endsection

@section('content')
    <div class="card mb-4" id="formCard" style="display: none;">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-edit mr-2"></i>Form Ubah Judul</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('sidang.s3.store-ubah-judul', $idJudul) }}">
                @csrf
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Judul Lama</label>
                        <input type="text" class="form-control" value="{{ $judul->Judul ?? '' }}" disabled>
                        <input type="hidden" name="judul_lama" value="{{ $judul->Judul ?? '' }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Judul Baru <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="judul_baru" rows="3" required>{{ old('judul_baru') }}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Perubahan pada Tahap <span class="text-danger">*</span></label>
                        <select class="form-control" name="tahap" required>
                            <option value="">-- Pilih Tahap --</option>
                            @foreach($tahapOptions as $tahap)
                                <option value="{{ $tahap }}">{{ $tahap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Alasan Perubahan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="alasan" rows="1" required placeholder="Masukkan alasan perubahan judul"></textarea>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" onclick="hideForm()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-history mr-2"></i>Riwayat Perubahan Judul</h5>
            <button class="btn btn-sm btn-primary" onclick="showForm()"><i class="fas fa-plus mr-1"></i> Tambah</button>
        </div>
        <div class="card-body">
            @if($history && $history->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Judul Lama</th>
                                <th>Judul Baru</th>
                                <th>Tahap Perubahan</th>
                                <th>Alasan Perubahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $judul->Nim ?? '-' }}</td>
                                    <td>{{ $judul->nama_mhs ?? '-' }}</td>
                                    <td>{{ $item->judul_lama ?? '-' }}</td>
                                    <td><strong>{{ $item->judul_baru }}</strong></td>
                                    <td><span class="badge badge-info">{{ $item->tahap }}</span></td>
                                    <td>{{ $item->alasan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-history fa-3x mb-3"></i>
                    <p>Belum ada riwayat perubahan judul</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showForm() {
        document.getElementById('formCard').style.display = 'block';
    }

    function hideForm() {
        document.getElementById('formCard').style.display = 'none';
    }
</script>
@endpush
