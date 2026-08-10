@extends('layouts.master')

@section('title', 'Progress Sidang - SI SIDANG FTTM ITB')
@section('page_title', 'Progress Sidang')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">Progress Sidang</li>
    </ol>
@endsection

@section('content')
    <!-- Info Mahasiswa -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Nama:</strong> {{ session('auth_user.nama_lengkap') }}
                </div>
                <div class="col-md-4">
                    <strong>NIM:</strong> {{ session('auth_user.nip_nim') }}
                </div>
                <div class="col-md-4">
                    <strong>Strata:</strong> {{ $strata }}
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking Progress Sidang -->
    <div id="trackingCard" class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tasks mr-2"></i>Tracking Progress Sidang</h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="modal" data-target="#tambahJudulModal" {{ $juduls && $juduls->count() > 0 ? 'disabled' : '' }}>
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                @if($juduls && $juduls->count() > 1)
                    <div class="form-group mb-0">
                        <select class="form-control" onchange="window.location.href='{{ route('mahasiswa.set-judul', '__ID__') }}'.replace('__ID__', this.value)">
                            @foreach($juduls as $judul)
                                <option value="{{ $judul->id }}" {{ $activeJudulId == $judul->id ? 'selected' : '' }}>
                                    {{ $judul->Judul }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <!-- Table untuk semua strata (S1, S2, S3) -->
                <table id="trackingTable" class="table table-bordered table-hover text-center">
                    <thead style="background-color: #6998d3; color: white;">
                        <tr>
                            <th rowspan="2" class="align-middle" style="width: 25%;">Judul</th>
                            <th rowspan="2" class="align-middle">Ujian Kualifikasi</th>
                            <th rowspan="2" class="align-middle">Ujian Proposal</th>
                            <th colspan="4" class="align-middle">Tahap III</th>
                            <th rowspan="2" class="align-middle">Sidang Terbuka / Tertutup</th>
                        </tr>
                        <tr>
                            <th class="align-middle" style="background-color: #9fbce4; color: white;">SK I</th>
                            <th class="align-middle" style="background-color: #9fbce4; color: white;">SK II</th>
                            <th class="align-middle" style="background-color: #9fbce4; color: white;">SK III</th>
                            <th class="align-middle" style="background-color: #9fbce4; color: white;">SK IV</th>
                        </tr>
                        <tr class="tracking-filter-row" style="background-color: #f8f9fa;">
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cari..." data-col="0" style="color: #495057;"></th>
                            @for($i = 1; $i <= 7; $i++)
                            <th>
                                <select class="form-control form-control-sm column-search" data-col="{{ $i }}" style="color: #495057;">
                                    <option value="">Semua</option>
                                    <option value="belum diajukan">Belum diajukan</option>
                                    <option value="dalam proses">Dalam proses</option>
                                    <option value="lulus">Lulus</option>
                                    <option value="tidak lulus">Tidak lulus</option>
                                </select>
                            </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tracking as $item)
                            <tr>
                                <td class="text-left text-muted">
                                    <a href="{{ route('mahasiswa.ubah-judul', $item->id_judul) }}" class="text-decoration-none text-primary" title="Lihat riwayat perubahan judul">{{ $item->Judul }}</a>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-{{ getStatusColor($item->tahap1) }}" role="button" onclick="showTahapForm('tahap I', '{{ $item->id_judul }}')">
                                        {{ ucfirst($item->tahap1) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-{{ getStatusColor($item->tahap2) }}" role="button" onclick="showTahapForm('tahap II', '{{ $item->id_judul }}')">
                                        {{ ucfirst($item->tahap2) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-{{ getStatusColor($item->sk1) }}" role="button" onclick="showTahapForm('SK I', '{{ $item->id_judul }}')">
                                        {{ ucfirst($item->sk1) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-{{ getStatusColor($item->sk2) }}" role="button" onclick="showTahapForm('SK II', '{{ $item->id_judul }}')">
                                        {{ ucfirst($item->sk2) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-{{ getStatusColor($item->sk3) }}" role="button" onclick="showTahapForm('SK III', '{{ $item->id_judul }}')">
                                        {{ ucfirst($item->sk3) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-{{ getStatusColor($item->sk4) }}" role="button" onclick="showTahapForm('SK IV', '{{ $item->id_judul }}')">
                                        {{ ucfirst($item->sk4) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-{{ getStatusColor($item->tahap4) }}" role="button" onclick="showTahapForm('tahap IV', '{{ $item->id_judul }}')">
                                        {{ ucfirst($item->tahap4) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tahapan -->
    <div class="modal fade" id="tahapModal" tabindex="-1" role="dialog" aria-labelledby="tahapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tahapModalLabel"><i class="fas fa-clipboard-list mr-2"></i>Form Tahapan: <span id="tahapTitle"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="tahapFormContent">
                        <div class="text-center py-4 text-muted">Memuat...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Judul (Mahasiswa) -->
    <div class="modal fade" id="tambahJudulModal" tabindex="-1" role="dialog" aria-labelledby="tambahJudulModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('mahasiswa.store-judul') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahJudulModalLabel"><i class="fas fa-plus mr-2"></i>Tambah Judul</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control" value="{{ session('auth_user.nama_lengkap') }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>NIM</label>
                            <input type="text" class="form-control" value="{{ session('auth_user.nip_nim') }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Judul</label>
                            <textarea name="judul" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function getTahapLabelJS(tahapan) {
        var labels = {
            'tahap I': 'Ujian Kualifikasi',
            'tahap II': 'Ujian Proposal',
            'tahap IV': 'Sidang Terbuka / Tertutup'
        };
        return labels[tahapan] || tahapan;
    }

    function showTahapForm(tahapan, idJudul, activeTab) {
        const title = document.getElementById('tahapTitle');
        const content = document.getElementById('tahapFormContent');
        
        title.textContent = getTahapLabelJS(tahapan);
        content.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 text-muted">Memuat...</p></div>';
        
        $('#tahapModal').modal('show');
        
        var url = `/mahasiswa/tahap/${tahapan}?_=${new Date().getTime()}`;
        if (idJudul) {
            url += `&id_judul=${idJudul}`;
        }
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const formContent = doc.querySelector('.tahap-container');
                if (formContent) {
                    content.innerHTML = formContent.outerHTML;
                    content.querySelectorAll('script').forEach(function(oldScript) {
                        var newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(function(attr) {
                            newScript.setAttribute(attr.name, attr.value);
                        });
                        newScript.textContent = oldScript.textContent;
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });
                } else if (html.includes('tahap-container')) {
                    content.innerHTML = html;
                    content.querySelectorAll('script').forEach(function(oldScript) {
                        var newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(function(attr) {
                            newScript.setAttribute(attr.name, attr.value);
                        });
                        newScript.textContent = oldScript.textContent;
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });
                } else {
                    content.innerHTML = '<p class="text-center text-muted py-4">Form tidak tersedia untuk tahapan ini.</p>';
                }
                if (activeTab) {
                    var tabLink = content.querySelector('a[href="#' + activeTab + '"]');
                    if (tabLink) {
                        $(tabLink).tab('show');
                    }
                }
            })
            .catch(error => {
                content.innerHTML = '<p class="text-danger text-center py-4">Error loading form: ' + error + '</p>';
            });
    }

    function filterTrackingTable() {
        const filters = Array.from(document.querySelectorAll('#trackingTable .column-search')).map(input => ({
            colIndex: parseInt(input.dataset.col),
            value: input.value.toLowerCase()
        }));

        document.querySelectorAll('#trackingTable tbody tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            if (!cells || cells.length === 0) return;
            let isMatch = true;
            filters.forEach(filter => {
                if (filter.value && cells[filter.colIndex]) {
                    const cellText = cells[filter.colIndex].textContent.toLowerCase().trim();
                    if (!cellText.includes(filter.value)) {
                        isMatch = false;
                    }
                }
            });
            row.style.display = isMatch ? '' : 'none';
        });
    }

    document.querySelectorAll('#trackingTable .column-search').forEach(input => {
        input.addEventListener('input', filterTrackingTable);
        input.addEventListener('change', filterTrackingTable);
    });

    // Event listener untuk reload dashboard ketika modal tahapan ditutup
    $('#tahapModal').on('hidden.bs.modal', function() {
        // Reload dashboard untuk update status terbaru
        location.reload();
    });
</script>
@endpush

@php
function getStatusColor($status) {
    $s = strtolower($status ?? '');
    switch($s) {
        case 'belum diajukan':
            return 'secondary';
        case 'dalam proses':
            return 'warning';
        case 'lulus':
            return 'success';
        case 'tidak lulus':
            return 'danger';
        default:
            return 'info';
    }
}

function getTahapLabel($tahapan) {
    $labels = [
        'tahap I'  => 'Ujian Kualifikasi',
        'tahap II' => 'Ujian Proposal',
        'tahap IV' => 'Sidang Terbuka / Tertutup',
    ];
    return $labels[strtolower($tahapan)] ?? str_replace('tahap', 'Tahap', $tahapan);
}
@endphp
@endphp