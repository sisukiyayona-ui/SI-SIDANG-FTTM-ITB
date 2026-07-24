@extends('layouts.master')

@section('title', 'Report - SI SIDANG FTTM ITB')
@section('page_title', 'Report Progress Sidang')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Report</li>
    </ol>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table mr-2"></i>Report Progress Sidang Mahasiswa S3</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th rowspan="2">Judul</th>
                            <th rowspan="2">Tahap 1</th>
                            <th rowspan="2">Tahap II (Proposal)</th>
                            <th colspan="3">Tahap III</th>
                            <th rowspan="2">Tahap IV (Sidang Akhir)</th>
                        </tr>
                        <tr>
                            <th>SK I</th>
                            <th>SK II</th>
                            <th>SK III</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $item)
                            <tr>
                                <td>
                                    <a href="#" onclick="showDetail({{ $item->id_judul }})" class="text-primary">
                                        {{ $item->Judul }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ getStatusColor($item->tahap1) }}">
                                        {{ $item->tahap1 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ getStatusColor($item->tahap2) }}">
                                        {{ $item->tahap2 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ getStatusColor($item->sk1) }}">
                                        {{ $item->sk1 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ getStatusColor($item->sk2) }}">
                                        {{ $item->sk2 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ getStatusColor($item->sk3) }}">
                                        {{ $item->sk3 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ getStatusColor($item->tahap4) }}">
                                        {{ $item->tahap4 }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Progress Sidang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detailContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showDetail(idJudul) {
        $('#detailModal').modal('show');
        $('#detailContent').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
        
        // Show simple detail info for now
        fetch(`/report/detail/${idJudul}/tahap I`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    $('#detailContent').html('<div class="alert alert-info">Detail untuk judul ini akan ditampilkan lebih lengkap setelah data tersedia.</div>');
                } else {
                    let html = '<div class="row">';
                    html += '<div class="col-md-12"><strong>Judul:</strong> ' + data.Judul + '</div>';
                    html += '<div class="col-md-6 mt-2"><strong>Tahapan:</strong> ' + data.tahapan_sidang + '</div>';
                    html += '<div class="col-md-6 mt-2"><strong>Tanggal:</strong> ' + (data.tgl_sidang || '-') + '</div>';
                    html += '<div class="col-md-6 mt-2"><strong>Waktu:</strong> ' + (data.waktu_sidang || '-') + '</div>';
                    html += '<div class="col-md-6 mt-2"><strong>Ruang:</strong> ' + (data.ruang_sidang || '-') + '</div>';
                    html += '<div class="col-md-6 mt-2"><strong>Status:</strong> ' + (data.status_lulus || '-') + '</div>';
                    html += '</div>';
                    $('#detailContent').html(html);
                }
            })
            .catch(error => {
                $('#detailContent').html('<div class="alert alert-info">Detail untuk judul ini akan ditampilkan lebih lengkap setelah data tersedia.</div>');
            });
    }
</script>
@endpush

@php
function getStatusColor($status) {
    switch($status) {
        case 'belum diajukan':
            return 'secondary';
        case 'dalam proses':
            return 'warning';
        case 'Lulus':
            return 'success';
        default:
            return 'info';
    }
}
@endphp
