@extends('layouts.master')

@section('title', 'Master Data KPPS - SI SIDANG FTTM ITB')
@section('page_title', 'Master Data KPPS')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">Master Data KPPS</li>
    </ol>
@endsection

@push('styles')
<style>
    .master-data-container {
        --table-header-bg-light: #f8f9fa;
        --table-header-text-light: #2d3748;
        --table-header-border-light: #dee2e6;
        --table-row-hover-light: #f8f9fa;
        --table-border-light: #dee2e6;

        --table-header-bg-dark: #334155;
        --table-header-text-dark: #f1f5f9;
        --table-header-border-dark: #475569;
        --table-row-hover-dark: #2d3748;
        --table-border-dark: #475569;
    }

    .master-data-container .table thead th {
        background-color: var(--table-header-bg-light) !important;
        color: var(--table-header-text-light) !important;
        border-color: var(--table-header-border-light) !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        padding: 12px 10px !important;
        vertical-align: middle !important;
    }

    html.dark-mode .master-data-container .table thead th {
        background-color: var(--table-header-bg-dark) !important;
        color: var(--table-header-text-dark) !important;
        border-color: var(--table-header-border-dark) !important;
    }

    .master-data-container .table tbody tr:hover {
        background-color: var(--table-row-hover-light) !important;
    }

    html.dark-mode .master-data-container .table tbody tr:hover {
        background-color: var(--table-row-hover-dark) !important;
    }

    .master-data-container .table {
        border-color: var(--table-border-light) !important;
    }

    html.dark-mode .master-data-container .table {
        border-color: var(--table-border-dark) !important;
    }

    .master-data-container .table td,
    .master-data-container .table th {
        border-color: var(--table-border-light) !important;
    }

    html.dark-mode .master-data-container .table td,
    html.dark-mode .master-data-container .table th {
        border-color: var(--table-border-dark) !important;
    }

    .master-data-container .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white !important;
        border: none;
        padding: 16px 20px;
    }

    .master-data-container .card-header h5 {
        color: white !important;
        font-weight: 600;
        margin: 0;
    }

    html.dark-mode .master-data-container .card {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }

    html.dark-mode .master-data-container .card-body {
        background-color: #1e293b !important;
    }
</style>
@endpush

@section('content')
<div class="master-data-container">
    <div id="listContainer" class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-tie mr-2"></i>Daftar KPPS</h5>
            <button class="btn btn-sm btn-primary" onclick="openCreate()">
                <i class="fas fa-plus mr-1"></i> Tambah
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <form method="GET" action="{{ route('master.kpps.index') }}" id="filterForm" autocomplete="off">
                <table class="table table-striped table-hover" id="kppsTable">
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th>NIP</th>
                            <th>Nama Lengkap</th>
                            <th>Fakultas</th>
                            <th>Program Studi</th>
                            <th style="width:100px;">Status</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="nip" placeholder="Cari..." data-col="1" value="{{ request('nip') }}"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="nama" placeholder="Cari..." data-col="2" value="{{ request('nama') }}"></th>
                            <th>
                                <select class="form-control form-control-sm column-search" name="kode_fs" data-col="3">
                                    <option value="">Semua</option>
                                    @foreach($fakultas as $fs)
                                        <option value="{{ $fs->KODE_FS }}" {{ request('kode_fs') == $fs->KODE_FS ? 'selected' : '' }}>{{ $fs->NAMA_FS }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th>
                                <select class="form-control form-control-sm column-search" name="kode_prodi" data-col="4">
                                    <option value="">Semua</option>
                                    @foreach($prodis as $p)
                                        <option value="{{ $p->KODE_PRODI }}" {{ request('kode_prodi') == $p->KODE_PRODI ? 'selected' : '' }}>{{ $p->NAMA_PRODI }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th>
                                <select class="form-control form-control-sm column-search" name="status_aktif" data-col="5">
                                    <option value="">Semua</option>
                                    <option value="AKTIF" {{ request('status_aktif') == 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                                    <option value="NON AKTIF" {{ request('status_aktif') == 'NON AKTIF' ? 'selected' : '' }}>NON AKTIF</option>
                                </select>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kpps as $i => $item)
                            <tr>
                                <td>{{ $kpps->firstItem() + $i }}</td>
                                <td>{{ $item->NIP ?? '-' }}</td>
                                <td><a href="javascript:void(0)" onclick="openEdit({{ $item->id }})" class="text-decoration-none">{{ $item->NAMA }}</a></td>
                                <td>{{ $item->NAMA_FS ?? '-' }}</td>
                                <td>{{ $item->NAMA_PRODI ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->STATUS_AKTIF === 'AKTIF' ? 'success' : 'danger' }}">
                                        {{ $item->STATUS_AKTIF }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">Tidak ada data KPPS</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </form>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $kpps->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Form Container (In-Page CRUD Form) --}}
<div id="formContainer" class="card" style="display: none;">
    <div class="card-header">
        <h5 class="mb-0" id="modalKppsTitle"><i class="fas fa-plus mr-2"></i>Tambah KPPS</h5>
    </div>
    <div class="card-body">
        <form id="formKpps" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodKpps" value="POST">
            <input type="hidden" name="nama_fs" id="f_nama_fs" value="FTTM">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIP <span class="text-danger">*</span></label>
                    <select name="nip" id="f_nip" class="form-control select2-nip" required>
                        <option value="">-- Pilih NIP / Cari --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->NIP_NIM }}"
                                data-id="{{ $u->id }}"
                                data-nama="{{ $u->NAMA_LENGKAP }}"
                                data-kode-prodi="{{ $u->KODE_PRODI }}"
                                data-nama-prodi="{{ $u->NAMA_PRODI }}"
                                data-kode-fs="{{ $u->KODE_FS }}"
                                data-nama-fs="{{ $u->NAMA_FS }}">{{ $u->NIP_NIM }} - {{ $u->NAMA_LENGKAP }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" id="f_nama" class="form-control" placeholder="Nama lengkap" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fakultas *</label>
                    <select name="kode_fs" id="f_kode_fs" class="form-control" onchange="setNamaFs(this)">
                        <option value="">-- Pilih Fakultas --</option>
                        @foreach($fakultas as $fs)
                            <option value="{{ $fs->KODE_FS }}" data-nama="{{ $fs->NAMA_FS }}">{{ $fs->NAMA_FS }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Program Studi</label>
                    <select name="kode_prodi" id="f_kode_prodi" class="form-control">
                        <option value="">-- Tidak ada / Non Prodi --</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->KODE_PRODI }}" data-nama="{{ $p->NAMA_PRODI }}">{{ $p->NAMA_PRODI }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status Aktif <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" name="status_aktif" value="AKTIF" class="form-check-input" id="saAktifKpps" checked>
                            <label class="form-check-label" for="saAktifKpps">AKTIF</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="status_aktif" value="NON AKTIF" class="form-check-input" id="saNonAktifKpps">
                            <label class="form-check-label" for="saNonAktifKpps">NON AKTIF</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3" style="display:none;">
                    <label class="form-label">ID User (Opsional)</label>
                    <select name="id_user" id="f_id_user" class="form-control">
                        <option value="">-- Pilih --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->NAMA_LENGKAP }} ({{ $u->NIP_NIM }})</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Kosongkan jika tidak dikaitkan dengan user</small>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end" style="gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="closeForm()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Delete --}}
<div class="modal fade" id="modalDeleteKpps" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="formDeleteKpps" method="POST">
                @csrf @method('DELETE')
                <div class="modal-body text-center py-4">
                    <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                    <h6>Yakin ingin menghapus KPPS ini?</h6>
                    <p class="text-muted small mb-0">Data yang dihapus tidak dapat dikembalikan.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setNamaFs(select) {
        var selected = select.options[select.selectedIndex];
        var namaFs = selected.dataset.nama || selected.textContent.trim();
        document.getElementById('f_nama_fs').value = namaFs;
    }

    function fillFromNip() {
        var nipEl = document.getElementById('f_nip');
        var opt = nipEl.options[nipEl.selectedIndex];
        if (!opt || !opt.value) {
            document.getElementById('f_nama').value = '';
            document.getElementById('f_kode_fs').value = '';
            document.getElementById('f_nama_fs').value = 'FTTM';
            document.getElementById('f_kode_prodi').value = '';
            return;
        }
        document.getElementById('f_nama').value = opt.dataset.nama || '';
        document.getElementById('f_kode_fs').value = opt.dataset.kodeFs || '';
        document.getElementById('f_nama_fs').value = opt.dataset.namaFs || 'FTTM';
        document.getElementById('f_kode_prodi').value = opt.dataset.kodeProdi || '';
    }

    jQuery(document).ready(function() {
        jQuery('#f_nip').on('change', function() {
            fillFromNip();
            var opt = this.options[this.selectedIndex];
            document.getElementById('f_id_user').value = (opt && opt.value) ? (opt.dataset.id || '') : '';
        });

        if (jQuery.fn.select2) {
            jQuery('.select2-nip').select2({
                theme: 'bootstrap',
                width: '100%',
                placeholder: '-- Pilih NIP / Cari --',
                allowClear: true
            });
        }
    });

    function openCreate() {
        document.getElementById('modalKppsTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah KPPS';
        document.getElementById('formKpps').action = '{{ route("master.kpps.store") }}';
        document.getElementById('methodKpps').value = 'POST';
        document.getElementById('formKpps').reset();
        document.getElementById('saAktifKpps').checked = true;
        document.getElementById('f_kode_fs').value = '';
        document.getElementById('f_nama_fs').value = 'FTTM';
        document.getElementById('f_kode_prodi').value = '';
        document.getElementById('f_id_user').value = '';
        document.getElementById('f_nip').value = '';
        if (window.jQuery && jQuery.fn.select2) {
            jQuery('#f_nip').trigger('change').val('').trigger('change');
        }

        document.getElementById('listContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
    }

    function openEdit(id) {
        fetch('{{ url("master/kpps") }}/' + id + '/edit', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
        })
        .then(function(r) { return r.json(); })
        .then(function(item) {
            if (!item) return;

            document.getElementById('modalKppsTitle').innerHTML = '<i class="fas fa-edit mr-2"></i>Edit KPPS';
            document.getElementById('formKpps').action = '{{ url("master/kpps") }}/' + id;
            document.getElementById('methodKpps').value = 'PUT';

            document.getElementById('f_nip').value          = item.nip ?? '';
            document.getElementById('f_nama').value         = item.nama ?? '';
            document.getElementById('f_kode_fs').value      = item.kode_fs ?? '';
            document.getElementById('f_nama_fs').value      = item.nama_fs ?? '';
            document.getElementById('f_kode_prodi').value   = item.kode_prodi ?? '';

            var nipEl = document.getElementById('f_nip');
            if (item.nip) {
                var exists = Array.prototype.some.call(nipEl.options, function(o) { return o.value === item.nip; });
                if (!exists) {
                    var o = document.createElement('option');
                    o.value = item.nip;
                    o.textContent = item.nip + ' - ' + (item.nama || '');
                    o.dataset.nama = item.nama || '';
                    o.dataset.kodeProdi = item.kode_prodi || '';
                    o.dataset.namaProdi = item.nama_prodi || '';
                    o.dataset.kodeFs = item.kode_fs || '';
                    o.dataset.namaFs = item.nama_fs || '';
                    nipEl.appendChild(o);
                }
            }
            if (window.jQuery && jQuery.fn.select2) {
                jQuery(nipEl).val(item.nip ?? '').trigger('change');
            }
            document.getElementById('f_id_user').value = item.id_user ?? '';

            document.getElementById(item.status_aktif === 'AKTIF' ? 'saAktifKpps' : 'saNonAktifKpps').checked = true;

            document.getElementById('listContainer').style.display = 'none';
            document.getElementById('formContainer').style.display = 'block';
        });
    }

    function closeForm() {
        document.getElementById('formContainer').style.display = 'none';
        document.getElementById('listContainer').style.display = 'block';
    }

    function openDelete(id) {
        document.getElementById('formDeleteKpps').action = '{{ url("master/kpps") }}/' + id;
        new bootstrap.Modal(document.getElementById('modalDeleteKpps')).show();
    }

    document.getElementById('formKpps').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: new FormData(this)
        }).then(async r => {
            const data = await r.json().catch(() => ({}));
            if (r.ok && data.success) {
                closeForm();
                showToast('success', 'Data KPPS berhasil disimpan.');
                setTimeout(() => location.reload(), 1200);
                return;
            }
            if (r.status === 422 && data.errors) {
                const firstError = Object.values(data.errors)[0];
                showToast('error', Array.isArray(firstError) ? firstError[0] : firstError);
                return;
            }
            showToast('error', data.message || 'Gagal menyimpan data KPPS. Periksa data form.');
        }).catch(() => {
            showToast('error', 'Terjadi kesalahan, coba lagi.');
        });
    });

    document.getElementById('formDeleteKpps').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch(this.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: new FormData(this)
        }).then(r => r.json()).then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalDeleteKpps')).hide();
                showToast('success', 'Data KPPS berhasil dihapus.');
                setTimeout(() => location.reload(), 1200);
            }
        });
    });

    var filterTimeout;
    document.querySelectorAll('.column-search').forEach(function(input) {
        input.addEventListener('input', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(function() { document.getElementById('filterForm').submit(); }, 400);
        });
        input.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
</script>
@endpush
