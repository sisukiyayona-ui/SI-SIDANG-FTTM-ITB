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

    /* Dark mode: card header adapt */
    html.dark-mode .master-data-container .card-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
        color: #f1f5f9 !important;
        border-bottom: 1px solid #334155 !important;
    }
    html.dark-mode .master-data-container .card-header h5 {
        color: #f1f5f9 !important;
    }
    /* Dark mode: form container */
    html.dark-mode #formContainer .card-body {
        background-color: #1e293b !important;
    }
    html.dark-mode #formContainer .form-label {
        color: #e2e8f0 !important;
    }
    html.dark-mode #formContainer .form-control {
        background-color: #334155 !important;
        border-color: #475569 !important;
        color: #f1f5f9 !important;
    }
    html.dark-mode #formContainer select.form-control option {
        background-color: #334155 !important;
        color: #f1f5f9 !important;
    }
    html.dark-mode #formContainer .form-check-label {
        color: #e2e8f0 !important;
    }
    html.dark-mode #formContainer small.text-muted {
        color: #94a3b8 !important;
    }
</style>
@endpush

@section('content')
@php
    $authUserKpps = session('auth_user');
    $isTuProdiKpps = ($authUserKpps['role'] ?? null) === 'TU Prodi';
@endphp
<div class="master-data-container">
    <div id="listContainer" class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-tie mr-2"></i>Daftar KPPS</h5>
            <button class="btn btn-sm btn-primary" onclick="openCreate()">
                <i class="fas fa-plus mr-1"></i> Tambah
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="kppsTableContainer">
                @include('master._kpps_table')
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
                    @if($isTuProdiKpps)
                        <input type="hidden" name="kode_fs" value="{{ $authUserKpps['kode_fs'] }}">
                        <input type="hidden" name="nama_fs" value="{{ $authUserKpps['nama_fs'] }}">
                    @endif
                    <select name="kode_fs" id="f_kode_fs" class="form-control" onchange="setNamaFs(this)" {{ $isTuProdiKpps ? 'disabled' : '' }}>
                        <option value="">-- Pilih Fakultas --</option>
                        @if($isTuProdiKpps)
                            <option value="{{ $authUserKpps['kode_fs'] }}" data-nama="{{ $authUserKpps['nama_fs'] }}" selected>{{ $authUserKpps['nama_fs'] }}</option>
                        @endif
                        @foreach($fakultas as $fs)
                            <option value="{{ $fs->KODE_FS }}" data-nama="{{ $fs->NAMA_FS }}" {{ !$isTuProdiKpps && request('kode_fs') == $fs->KODE_FS ? 'selected' : '' }}>{{ $fs->NAMA_FS }}</option>
                        @endforeach
                    </select>
                    @if($isTuProdiKpps)
                        <small class="text-muted">Mengikuti fakultas user login ({{ $authUserKpps['nama_fs'] }})</small>
                    @endif
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Program Studi</label>
                    @if($isTuProdiKpps)
                        <input type="hidden" name="kode_prodi" value="{{ $authUserKpps['kode_prodi'] }}">
                        <input type="hidden" name="nama_prodi" value="{{ $authUserKpps['nama_prodi'] }}">
                    @endif
                    <select name="kode_prodi" id="f_kode_prodi" class="form-control" {{ $isTuProdiKpps ? 'disabled' : '' }}>
                        <option value="">-- Tidak ada / Non Prodi --</option>
                        @if($isTuProdiKpps)
                            <option value="{{ $authUserKpps['kode_prodi'] }}" data-nama="{{ $authUserKpps['nama_prodi'] }}" selected>{{ $authUserKpps['nama_prodi'] }}</option>
                        @endif
                        @foreach($prodis as $p)
                            <option value="{{ $p->KODE_PRODI }}" data-nama="{{ $p->NAMA_PRODI }}">{{ $p->NAMA_PRODI }}</option>
                        @endforeach
                    </select>
                    @if($isTuProdiKpps)
                        <small class="text-muted">Mengikuti prodi user login ({{ $authUserKpps['nama_prodi'] }})</small>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status Tim</label>
                    <select name="status_tim" id="f_status_tim" class="form-control">
                        <option value="">-- Pilih Status Tim --</option>
                        <option value="Ketua">Ketua</option>
                        <option value="Sekretaris">Sekretaris</option>
                        <option value="Anggota">Anggota</option>
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
        var locked = document.getElementById('f_kode_fs').disabled;
        if (!opt || !opt.value) {
            document.getElementById('f_nama').value = '';
            if (!locked) {
                document.getElementById('f_kode_fs').value = '';
                document.getElementById('f_nama_fs').value = 'FTTM';
                document.getElementById('f_kode_prodi').value = '';
            }
            return;
        }
        document.getElementById('f_nama').value = opt.dataset.nama || '';
        if (!locked) {
            document.getElementById('f_kode_fs').value = opt.dataset.kodeFs || '';
            document.getElementById('f_nama_fs').value = opt.dataset.namaFs || 'FTTM';
            document.getElementById('f_kode_prodi').value = opt.dataset.kodeProdi || '';
        }
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
        document.getElementById('f_status_tim').value = '';
@if($isTuProdiKpps)
        document.getElementById('f_kode_fs').value = '{{ $authUserKpps['kode_fs'] }}';
        document.getElementById('f_nama_fs').value = @js($authUserKpps['nama_fs']);
        document.getElementById('f_kode_prodi').value = '{{ $authUserKpps['kode_prodi'] }}';
@else
        document.getElementById('f_kode_fs').value = '';
        document.getElementById('f_nama_fs').value = 'FTTM';
        document.getElementById('f_kode_prodi').value = '';
@endif
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
            document.getElementById('f_status_tim').value   = item.status_tim ?? '';
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

    function ajaxFilter(routeName, containerId) {
        var params = {};
        document.querySelectorAll('.column-search').forEach(function(input) {
            if (input.value) params[input.name] = input.value;
        });
        var qs = Object.keys(params).map(function(k) { return encodeURIComponent(k) + '=' + encodeURIComponent(params[k]); }).join('&');
        var url = routeName + (qs ? '?' + qs : '');
        var container = document.getElementById(containerId);

        container.style.opacity = '0.5';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                container.innerHTML = data.html;
                container.style.opacity = '1';
                bindFilters(routeName, containerId);
            })
            .catch(function(err) {
                console.error('AJAX filter error:', err);
                container.style.opacity = '1';
            });
    }

    function bindFilters(routeName, containerId) {
        document.querySelectorAll('.column-search').forEach(function(input) {
            input.removeEventListener('input', input._ajaxHandler);
            input._ajaxHandler = function() {
                clearTimeout(window._filterTimeout);
                window._filterTimeout = setTimeout(function() { ajaxFilter(routeName, containerId); }, 400);
            };
            input.addEventListener('input', input._ajaxHandler);
            input.removeEventListener('change', input._changeHandler);
            input._changeHandler = function() {
                ajaxFilter(routeName, containerId);
            };
            input.addEventListener('change', input._changeHandler);
        });
        document.querySelectorAll('.pagination a').forEach(function(link) {
            link.removeEventListener('click', link._ajaxHandler);
            link._ajaxHandler = function(e) {
                e.preventDefault();
                var pagContainer = document.getElementById(containerId);
                pagContainer.style.opacity = '0.5';
                fetch(this.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        pagContainer.innerHTML = data.html;
                        pagContainer.style.opacity = '1';
                        bindFilters(routeName, containerId);
                    })
                    .catch(function(err) {
                        console.error('AJAX pagination error:', err);
                        pagContainer.style.opacity = '1';
                    });
            };
            link.addEventListener('click', link._ajaxHandler);
        });
    }
    bindFilters('{{ route("master.kpps.index") }}', 'kppsTableContainer');
</script>
@endpush
