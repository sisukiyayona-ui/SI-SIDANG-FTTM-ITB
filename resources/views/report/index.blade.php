@extends('layouts.master')

@section('title', 'Report Tipe I - SI SIDANG FTTM ITB')
@section('page_title', 'Report Tipe I')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Report Tipe I</li>
    </ol>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table mr-2"></i>Report Tipe I</h5>
            <a href="{{ route('report.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="reportTable">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th>No</th>
                            <th>Tahun</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Judul</th>
                            <th>NIP</th>
                            <th>Nama Dosen</th>
                            <th>Status Tim Sidang</th>
                            <th>Tahapan</th>
                            <th>Tanggal Sidang</th>
                            <th>Status lulus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $item->tahun }}</td>
                                <td>{{ $item->NIM }}</td>
                                <td>{{ $item->nama_mahasiswa }}</td>
                                <td>{{ $item->JUDUL }}</td>
                                <td>{{ $item->NIP }}</td>
                                <td>{{ $item->pembimbing_penguji }}</td>
                                <td>{{ $item->STATUS_TIM_SIDANG }}</td>
                                <td>{{ $item->tahapan_sidang }}</td>
                                <td>
                                    @if(isset($item->tgl_sidang) && $item->tgl_sidang)
                                        {{ \Carbon\Carbon::parse($item->tgl_sidang)->translatedFormat('d M Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ getStatusColor($item->status_lulus) }}">
                                        {{ $item->status_lulus ?? 'belum diajukan' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
<script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#reportTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 25,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});
</script>
@endpush

@php
function getStatusColor($status) {
    switch($status) {
        case 'lulus':
            return 'success';
        case 'tidak lulus':
            return 'danger';
        case 'dalam proses':
            return 'warning';
        case 'belum diajukan':
        case null:
            return 'secondary';
        default:
            return 'info';
    }
}
@endphp
