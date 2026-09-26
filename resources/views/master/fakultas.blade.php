@extends('layouts.master')

@section('title', 'Data Fakultas - SI SIDANG FTTM ITB')
@section('page_title', 'Data Master Fakultas')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Data Master</a></li>
        <li class="breadcrumb-item active">Fakultas</li>
    </ol>
@endsection

@push('styles')
<style>
    /* Master Data Table Styles - Professional Dark/Light Mode */
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
        font-size: 0.9rem !important;
        padding: 14px 12px !important;
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

    /* Card Header Styling */
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
<div class="master-data-container">
    <div id="listContainer" class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-landmark mr-2"></i>Daftar Fakultas</h5>
            <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                <button type="button" class="btn btn-sm btn-info" onclick="syncSpsi()" id="btnSyncSpsi">
                    <i class="fas fa-sync-alt mr-1"></i> Tarik Data SPSI
                </button>
                <a class="btn btn-sm btn-light" href="{{ route('master.fakultas.template') }}">
                    <i class="fas fa-download mr-1"></i> Template
                </a>
                <button type="button" class="btn btn-sm btn-warning" onclick="document.getElementById('importFile').click()">
                    <i class="fas fa-upload mr-1"></i> Upload
                </button>
                <input type="file" id="importFile" accept=".xlsx,.xls" hidden onchange="uploadImport(this)">
                <button class="btn btn-sm btn-primary" onclick="openCreate()">
                    <i class="fas fa-plus mr-1"></i> Tambah
                </button>
            </div>
        </div>
        <div class="card-body">

            <div class="table-responsive" id="fakultasTableContainer">
                @include('master._fakultas_table')
            </div>
        </div>
    </div>
</div>

    {{-- Form Container (In-Page CRUD Form) --}}
    <div id="formContainer" class="card" style="display: none;">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h5 class="mb-0" id="modalFakultasTitle"><i class="fas fa-plus mr-2"></i>Tambah Fakultas</h5>
        </div>
        <div class="card-body">
            <form id="formFakultas" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodFakultas" value="POST">
                <input type="hidden" name="id" id="fakultasId">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="f_kode_fs" class="form-label fw-semibold text-secondary">Kode Fakultas</label>
                            <input type="text" name="kode_fs" id="f_kode_fs" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" placeholder="Contoh: FTTM" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="f_nama_fs" class="form-label fw-semibold text-secondary">Nama Fakultas</label>
                            <input type="text" name="nama_fs" id="f_nama_fs" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" placeholder="Nama Fakultas" required>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end" style="gap: 12px;">
                    <button type="button" class="btn btn-secondary" onclick="closeForm()">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px; padding: 10px 25px;"><i class="fas fa-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: #2f5597; color: #fff;">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detail Fakultas</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered mb-0">
                        <tr><th style="width: 140px;">Kode Fakultas</th><td id="detailKode"></td></tr>
                        <tr><th>Nama Fakultas</th><td id="detailNama"></td></tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div class="modal fade" id="modalDelete" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="formDelete" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                        <h6>Yakin ingin menghapus data ini?</h6>
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

    {{-- Modal Import (Preview -> Edit -> Simpan -> Hasil) --}}
    <div class="modal fade" id="modalImport" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff;">
                    <h5 class="modal-title" id="importModalTitle"><i class="fas fa-file-excel mr-2"></i>Preview Data Excel</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" style="position: relative;">
                    {{-- Loading overlay --}}
                    <div id="importLoading" style="display:none; position:absolute; inset:0; background:rgba(255,255,255,0.85); z-index:10; flex-direction:column; align-items:center; justify-content:center;">
                        <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
                        <div class="mt-3 font-weight-bold text-primary" id="importLoadingText">Memproses data...</div>
                    </div>

                    {{-- Preview / edit --}}
                    <div id="importPreviewWrap">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            Periksa data berikut. Anda dapat <strong>mengedit</strong> kode/nama, atau <strong>menghapus</strong> baris yang tidak diinginkan sebelum menyimpan.
                            Baris bertanda <span class="badge badge-warning">Duplikat</span> sudah aktif di database atau berulang di file.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">No</th>
                                        <th style="width:160px;">Kode Fakultas</th>
                                        <th>Nama Fakultas</th>
                                        <th style="width:130px;">Status</th>
                                        <th style="width:60px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="importPreviewBody"></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Hasil --}}
                    <div id="importResultWrap" style="display:none;">
                        <div class="mb-3" id="importResultSummary"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">No</th>
                                        <th style="width:160px;">Kode</th>
                                        <th>Nama</th>
                                        <th style="width:110px;">Hasil</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="importResultBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="importFooter">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="importBtnCancel">Batal</button>
                    <button type="button" class="btn btn-primary" id="importBtnSave" onclick="saveImport()"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let importRows = [];

    function uploadImport(input) {
        if (!input.files.length) return;
        const file = input.files[0];
        const fd = new FormData();
        fd.append('file', file);
        fetch('{{ route("master.fakultas.import-preview") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: fd
        }).then(r => r.json()).then(data => {
            if (!data.success || !data.rows || !data.rows.length) {
                showToast('error', 'File tidak berisi data yang valid.');
                return;
            }
            importRows = data.rows;
            renderImportPreview();
            document.getElementById('importPreviewWrap').style.display = 'block';
            document.getElementById('importResultWrap').style.display = 'none';
            document.getElementById('importModalTitle').innerHTML = '<i class="fas fa-file-excel mr-2"></i>Preview Data Excel';
            document.getElementById('importBtnSave').style.display = 'inline-block';
            document.getElementById('importBtnCancel').innerHTML = 'Batal';
            document.getElementById('importBtnCancel').onclick = null;
            new bootstrap.Modal(document.getElementById('modalImport')).show();
        }).catch(() => showToast('error', 'Gagal membaca file Excel.'));
        input.value = '';
    }

    function importRowStatus(r) {
        if (!r.kode || !r.nama) return { label: 'Tidak Valid', cls: 'danger', title: 'Kode dan Nama wajib diisi' };
        const inDb = fakultasData.some(f => String(f.kode) === String(r.kode));
        if (inDb) return { label: 'Duplikat', cls: 'warning', title: 'Kode sudah terdaftar (aktif) di database' };
        const dupIdx = importRows.findIndex(x => x !== r && String(x.kode) === String(r.kode));
        if (dupIdx !== -1) return { label: 'Duplikat', cls: 'warning', title: 'Kode berulang di dalam file' };
        return { label: 'Baru', cls: 'success', title: 'Siap ditambahkan' };
    }

    function renderImportPreview() {
        const tbody = document.getElementById('importPreviewBody');
        tbody.innerHTML = importRows.map((r, i) => {
            const st = importRowStatus(r);
            return `<tr data-idx="${i}">
                <td class="text-center">${i + 1}</td>
                <td><input type="text" class="form-control form-control-sm" value="${escapeAttr(r.kode)}" oninput="updateImportRow(${i}, 'kode', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${escapeAttr(r.nama)}" oninput="updateImportRow(${i}, 'nama', this.value)"></td>
                <td class="text-center"><span class="badge badge-${st.cls}" title="${st.title}">${st.label}</span></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" title="Hapus baris" onclick="removeImportRow(${i})"><i class="fas fa-times"></i></button></td>
            </tr>`;
        }).join('');
    }

    function escapeAttr(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function updateImportRow(idx, field, value) {
        importRows[idx][field] = value;
        // refresh semua badge status karena duplikat antar-baris bisa berubah
        document.querySelectorAll('#importPreviewBody tr').forEach(tr => {
            const i = Number(tr.dataset.idx);
            const st = importRowStatus(importRows[i]);
            const badge = tr.querySelector('.badge');
            badge.className = `badge badge-${st.cls}`;
            badge.title = st.title;
            badge.textContent = st.label;
        });
    }

    function removeImportRow(idx) {
        importRows.splice(idx, 1);
        renderImportPreview();
    }

    function saveImport() {
        const loading = document.getElementById('importLoading');
        const btnSave = document.getElementById('importBtnSave');
        const btnCancel = document.getElementById('importBtnCancel');
        document.getElementById('importLoadingText').textContent = 'Menyimpan data...';
        loading.style.display = 'flex';
        btnSave.disabled = true;
        btnCancel.disabled = true;

        fetch('{{ route("master.fakultas.import-store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ rows: importRows })
        }).then(r => r.json()).then(data => {
            loading.style.display = 'none';
            btnSave.disabled = false;
            btnCancel.disabled = false;

            if (!data.success) {
                showToast('error', data.message || 'Gagal menyimpan data.');
                return;
            }

            renderImportResult(data);
        }).catch(() => {
            loading.style.display = 'none';
            btnSave.disabled = false;
            btnCancel.disabled = false;
            showToast('error', 'Gagal menyimpan data.');
        });
    }

    function renderImportResult(data) {
        document.getElementById('importPreviewWrap').style.display = 'none';
        document.getElementById('importResultWrap').style.display = 'block';
        document.getElementById('importModalTitle').innerHTML = '<i class="fas fa-clipboard-check mr-2"></i>Hasil Import';
        document.getElementById('importBtnSave').style.display = 'none';
        document.getElementById('importBtnCancel').innerHTML = 'Selesai';
        document.getElementById('importBtnCancel').onclick = () => location.reload();

        document.getElementById('importResultSummary').innerHTML =
            `<div class="alert ${data.inserted > 0 ? 'alert-success' : 'alert-warning'} py-2 mb-0">
                <i class="fas fa-check-circle mr-1"></i>
                <strong>${data.inserted}</strong> data berhasil ditambahkan, <strong>${data.failed}</strong> gagal.
            </div>`;

        document.getElementById('importResultBody').innerHTML = data.results.map((r, i) => `
            <tr>
                <td class="text-center">${i + 1}</td>
                <td>${escapeAttr(r.kode) || '-'}</td>
                <td>${escapeAttr(r.nama) || '-'}</td>
                <td class="text-center">
                    ${r.status === 'success'
                        ? '<span class="badge badge-success"><i class="fas fa-check mr-1"></i>Berhasil</span>'
                        : '<span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Gagal</span>'}
                </td>
                <td class="small">${escapeAttr(r.message)}</td>
            </tr>`).join('');

        showToast(data.failed > 0 ? 'error' : 'success', `Import selesai: ${data.inserted} berhasil, ${data.failed} gagal.`);
    }

    function syncSpsi() {
        const btn = document.getElementById('btnSyncSpsi');
        const oldHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menarik Data...';
        fetch('{{ route("master.fakultas.sync-spsi") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        }).then(r => r.json()).then(data => {
            showToast(data.success ? 'success' : 'error', data.message || 'Terjadi kesalahan.');
            if (data.success) setTimeout(() => location.reload(), 1200);
        }).catch(() => showToast('error', 'Gagal menarik data SPSI.')).finally(() => {
            btn.disabled = false;
            btn.innerHTML = oldHtml;
        });
    }

    const fakultasData = @json($allFakultas);

    function openCreate() {
        document.getElementById('modalFakultasTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah Fakultas';
        document.getElementById('formFakultas').action = '{{ route("master.fakultas.store") }}';
        document.getElementById('methodFakultas').value = 'POST';
        document.getElementById('fakultasId').value = '';
        document.getElementById('f_kode_fs').value = '';
        document.getElementById('f_nama_fs').value = '';

        document.getElementById('listContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
    }

    function openEdit(id) {
        const item = fakultasData.find(f => f.id === id);
        if (!item) return;
        document.getElementById('modalFakultasTitle').innerHTML = '<i class="fas fa-edit mr-2"></i>Edit Fakultas';
        document.getElementById('formFakultas').action = '{{ url("master/fakultas") }}/' + id;
        document.getElementById('methodFakultas').value = 'PUT';
        document.getElementById('fakultasId').value = id;
        document.getElementById('f_kode_fs').value = item.kode;
        document.getElementById('f_nama_fs').value = item.nama;

        document.getElementById('listContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
    }

    function closeForm() {
        document.getElementById('formContainer').style.display = 'none';
        document.getElementById('listContainer').style.display = 'block';
    }

    function openDelete(id) {
        document.getElementById('formDelete').action = '{{ url("master/fakultas") }}/' + id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    }

    document.getElementById('formFakultas').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: new FormData(form)
        }).then(() => {
            closeForm();
            showToast('success', 'Data fakultas berhasil disimpan.');
            setTimeout(() => location.reload(), 1200);
        });
    });

    document.getElementById('formDelete').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: new FormData(form)
        }).then(() => {
            bootstrap.Modal.getInstance(document.getElementById('modalDelete')).hide();
            showToast('success', 'Data fakultas berhasil dihapus.');
            setTimeout(() => location.reload(), 1200);
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
    bindFilters('{{ route("master.fakultas.index") }}', 'fakultasTableContainer');

</script>
@endpush
