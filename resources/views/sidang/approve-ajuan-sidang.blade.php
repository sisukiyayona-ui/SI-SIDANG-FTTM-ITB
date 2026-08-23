@extends('layouts.master')

@php
    $tahapDisplay = [
        'tahap I' => 'Ujian Kualifikasi',
        'tahap II' => 'Ujian Proposal',
        'tahap IV' => 'Sidang Terbuka / Tertutup',
    ];
@endphp

@section('title', 'Approve Ajuan Sidang ' . $strata . ' - SI SIDANG FTTM ITB')
@section('page_title', 'Form Approval Ajuan Sidang ' . $strata)

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Approve Ajuan Sidang {{ $strata }}</li>
    </ol>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-clipboard-check mr-2"></i>Daftar Ajuan Sidang {{ $strata }}
        </h5>
        <button type="button" id="btnApprove" class="btn btn-success btn-sm d-none"
                onclick="approveSelected()">
            <i class="fas fa-check-circle mr-1"></i> Approve
        </button>
    </div>
    <div class="card-body py-2">
        <form method="GET" action="{{ request()->url() }}" id="filterForm" class="form-inline">
            @csrf
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2 mb-1"
                   placeholder="Cari NIM / Nama / Judul..." style="min-width: 240px;">
            <select name="tahapan" class="form-control form-control-sm mr-2 mb-1 auto-submit" style="min-width: 200px;">
                <option value="">Semua Tahapan</option>
                @foreach($strata === 'S3' ? ['SK I', 'SK II', 'SK III', 'SK IV', 'tahap IV'] : ['tahap II', 'tahap IV'] as $tp)
                    <option value="{{ $tp }}" {{ request('tahapan') === $tp ? 'selected' : '' }}>{{ $tahapDisplay[$tp] ?? $tp }}</option>
                @endforeach
            </select>
            <select name="status" class="form-control form-control-sm mr-2 mb-1 auto-submit" style="min-width: 150px;">
                <option value="">Semua Status</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum Approved</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary mb-1"><i class="fas fa-search"></i> Cari</button>
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover table-sm text-center mb-0">
            <thead style="background-color: #6998d3; color: white;">
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="checkAll" onclick="toggleAll(this)">
                    </th>
                    <th style="width: 50px;">No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Judul</th>
                    <th>Tahap Sidang</th>
                    <th>Tanggal Seminar / Sidang</th>
                    <th>Status KPPS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                    <tr class="{{ $row->approved ? 'table-secondary' : '' }}">
                        <td>
                            @if($row->approved)
                                <input type="checkbox" class="row-check" value="{{ $row->id }}" name="ids[]"
                                       disabled checked title="Sudah di-approve">
                            @else
                                <input type="checkbox" class="row-check" value="{{ $row->id }}" name="ids[]"
                                       data-nim="{{ $row->NIM }}">
                            @endif
                        </td>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->NIM }}</td>
                        <td class="text-left">{{ $row->NAMA_MHS }}</td>
                        <td class="text-left">
                            <a href="#" onclick="event.preventDefault(); openKppsTahap({{ $row->id }}, '{{ addslashes($row->TAHAPAN_SIDANG) }}', '{{ $strata }}')" class="text-primary text-decoration-underline">{{ $row->JUDUL }}</a>
                        </td>
                        <td>{{ $tahapDisplay[$row->TAHAPAN_SIDANG] ?? $row->TAHAPAN_SIDANG }}</td>
                        <td>{{ $row->TGL_SIDANG ?? '-' }}</td>
                        <td>
                            @if($row->approved)
                                <span class="badge bg-success">Approved</span>
                            @elseif($row->STATUS_AJUKAN_KPPS === 'y')
                                <span class="badge bg-warning">Diajukan</span>
                            @else
                                <span class="badge bg-secondary">{{ $row->STATUS_AJUKAN_KPPS ?? '-' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Tidak ada ajuan sidang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail Ajuan -->
<div class="modal fade" id="kppsTahapModal" tabindex="-1" role="dialog" aria-labelledby="kppsTahapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kppsTahapModalLabel"><i class="fas fa-clipboard-list mr-2"></i>Form Tahapan: <span id="kppsTahapTitle"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="kppsTahapFormContent">
                    <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 text-muted">Memuat...</p></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" id="kppsKembaliBtn" style="display:none; font-size:12px; border-radius:0; margin-right:auto;" onclick="hideKppsPenilaian()">&larr; Kembali</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('#filterForm .auto-submit').forEach(function (el) {
        el.addEventListener('change', function () {
            document.getElementById('filterForm').submit();
        });
    });

    function getKppsTahapLabel(tahapan) {
        var labels = {
            'tahap I': 'Ujian Kualifikasi',
            'tahap II': 'Ujian Proposal',
            'tahap IV': 'Sidang Terbuka / Tertutup'
        };
        return labels[tahapan] || tahapan;
    }

    function openKppsTahap(id, tahapan, strata) {
        var title = document.getElementById('kppsTahapTitle');
        var content = document.getElementById('kppsTahapFormContent');

        title.textContent = getKppsTahapLabel(tahapan);
        content.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 text-muted">Memuat...</p></div>';
        var kemb = document.getElementById('kppsKembaliBtn');
        if (kemb) { kemb.style.display = 'none'; }

        $('#kppsTahapModal').modal('show');

        fetch('/sidang/approve-ajuan-sidang/' + strata + '/' + id + '?_=' + new Date().getTime(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) { return response.text(); })
            .then(function (html) {
                content.innerHTML = html;
                content.querySelectorAll('script').forEach(function (oldScript) {
                    var newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(function (attr) {
                        newScript.setAttribute(attr.name, attr.value);
                    });
                    newScript.textContent = oldScript.textContent;
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
            })
            .catch(function () {
                content.innerHTML = '<div class="text-center py-4 text-danger">Terjadi kesalahan saat memuat data.</div>';
            });
    }

    function toggleAll(checkbox) {
        document.querySelectorAll('.row-check:not(:disabled)').forEach(function (cb) {
            cb.checked = checkbox.checked;
        });
        updateApproveButton();
    }

    function updateApproveButton() {
        var checked = document.querySelectorAll('.row-check:checked:not(:disabled)').length;
        var btn = document.getElementById('btnApprove');
        btn.classList.toggle('d-none', checked < 1);
        if (checked > 0) {
            btn.textContent = ' Approve (' + checked + ')';
        }
    }

    document.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('row-check')) {
            var all = document.querySelectorAll('.row-check:not(:disabled)');
            var allChecked = all.length > 0 && Array.prototype.every.call(all, function (cb) { return cb.checked; });
            document.getElementById('checkAll').checked = allChecked;
            updateApproveButton();
        }
    });

    function approveSelected() {
        var ids = Array.prototype.map.call(
            document.querySelectorAll('.row-check:checked:not(:disabled)'),
            function (cb) { return parseInt(cb.value, 10); }
        );

        if (ids.length === 0) {
            alert('Pilih minimal satu ajuan terlebih dahulu.');
            return;
        }

        var formData = new FormData();
        ids.forEach(function (id) { formData.append('ids[]', id); });

        fetch('{{ route("sidang.approve-ajuan.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast('success', data.message);
                } else {
                    alert(data.message);
                }
                setTimeout(function () { location.reload(); }, 1000);
            } else {
                alert(data.message || 'Gagal approve.');
            }
        })
        .catch(function () { alert('Terjadi kesalahan.'); });
    }
</script>
@endpush