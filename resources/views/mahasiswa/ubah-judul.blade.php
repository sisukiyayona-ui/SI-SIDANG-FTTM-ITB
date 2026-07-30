@extends('layouts.master')

@section('title', 'Ubah Judul - SI SIDANG FTTM ITB')
@section('page_title', 'Ubah Judul')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('mahasiswa.dashboard') }}">Progress Sidang</a></li>
        <li class="breadcrumb-item active">Ubah Judul</li>
    </ol>
@endsection

@section('content')
    <div class="alert alert-info">
        <i class="fas fa-info-circle mr-2"></i>
        <strong>Informasi:</strong> Sebagai mahasiswa, Anda hanya dapat melihat riwayat perubahan judul. Untuk mengubah judul, silakan hubungi staf prodi atau TU Prodi.
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-history mr-2"></i>Riwayat Perubahan Judul</h5>
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

    <div class="mt-3">
        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
    </div>
@endsection
