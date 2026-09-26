@extends('layouts.master')

@section('title', 'Persyaratan - SI SIDANG FTTM ITB')
@section('page_title', 'Data Master Persyaratan')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Data Master</a></li>
        <li class="breadcrumb-item active">Persyaratan</li>
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
        background: #ffffff;
        color: #1e293b !important;
        border: none;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px;
    }

    .master-data-container .card-header h5 {
        color: #1e293b !important;
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
        background: #ffffff !important;
        color: #1e293b !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    html.dark-mode .master-data-container .card-header h5 {
        color: #1e293b !important;
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
    $tahapanLabels = [
        'tahap 1' => 'Ujian Kualifikasi',
        'tahap I' => 'Ujian Kualifikasi',
        'tahap 2' => 'Ujian Proposal',
        'tahap II' => 'Ujian Proposal',
        'tahap 3' => 'Tahap III',
        'tahap III' => 'Tahap III',
        'tahap 4' => 'Sidang Terbuka / Tertutup',
        'tahap IV' => 'Sidang Terbuka / Tertutup',
        'SK I' => 'SK I',
        'SK II' => 'SK II',
        'SK III' => 'SK III',
        'SK IV' => 'SK IV',
    ];
@endphp
<div class="master-data-container">
    <div id="listContainer" class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-alt mr-2"></i>Daftar Persyaratan</h5>
            <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('master.persyaratan.template') }}">
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
            <div class="table-responsive" id="persyaratanTableContainer">
                @include('master._persyaratan_table')
            </div>
        </div>
    </div>
</div>

    {{-- Form Container (In-Page CRUD Form) --}}
    <div id="formContainer" class="card" style="display: none;">
        <div class="card-header" style="background: #ffffff; color: #1e293b; border-bottom: 1px solid #e2e8f0;">
            <h5 class="mb-0" id="formTitle"><i class="fas fa-plus mr-2"></i>Tambah Persyaratan</h5>
        </div>
        <div class="card-body">
            <form id="mainForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">
                <input type="hidden" name="id" id="dataId">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_persyaratan" class="form-label fw-semibold text-secondary">Nama Persyaratan</label>
                            <input type="text" name="nama_persyaratan" id="f_nama" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" placeholder="Nama persyaratan" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="strata" class="form-label fw-semibold text-secondary">Strata</label>
                            <select name="strata" id="f_strata" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" required onchange="filterTahapanByStrata()">
                                <option value="S3" selected>S3</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tahapan_sidang" class="form-label fw-semibold text-secondary">Tahapan Sidang</label>
                            <select name="tahapan_sidang" id="f_tahapan_sidang" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" required>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="f_id_prodi" class="form-label fw-semibold text-secondary">Program Studi</label>
                            <select name="id_prodi" id="f_id_prodi" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;">
                                @forelse($prodis as $p)
                                    <option value="{{ $p->id }}" {{ (string) ($userProdiId ?? '') === (string) $p->id ? 'selected' : '' }}>{{ $p->kode_prodi }} - {{ $p->nama_prodi }}</option>
                                @empty
                                    <option value="">Prodi tidak tersedia</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary">Status Aktif</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input type="radio" name="status_aktif" value="AKTIF" class="form-check-input" id="statusAktif" checked>
                            <label class="form-check-label" for="statusAktif">AKTIF</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="status_aktif" value="NON AKTIF" class="form-check-input" id="statusNonaktif">
                            <label class="form-check-label" for="statusNonaktif">NON AKTIF</label>
                        </div>
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
    <div class="modal fade" id="modalDelete" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                        <h6>Yakin ingin menghapus persyaratan ini?</h6>
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
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header header-white">
                    <h5 class="modal-title" id="importModalTitle"><i class="fas fa-file-excel mr-2"></i>Preview Import Persyaratan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                            Periksa data berikut sebelum disimpan. Anda dapat <strong>mengedit</strong> atau <strong>menghapus</strong> baris.
                            <span class="badge badge-success">Baru</span> aman disimpan,
                            <span class="badge badge-warning">Duplikat</span> sudah ada di database / file,
                            <span class="badge badge-danger">Tidak Valid</span> strata, tahapan, atau program studi tidak sesuai master.
                        </div>
                        <datalist id="tahapanListImport">
                            @foreach(\App\Services\MasterExcelService::tahapanOptions() as $opt)
                                <option value="{{ $opt }}">{{ \App\Services\MasterExcelService::tahapanLabel($opt) }}</option>
                            @endforeach
                        </datalist>
                        <datalist id="prodiListImport">
                            @foreach($prodis as $pd)
                                <option value="{{ $pd->nama_prodi }}">{{ $pd->nama_prodi }} ({{ $pd->kode_prodi }})</option>
                            @endforeach
                        </datalist>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">No</th>
                                        <th style="width:195px;">Program Studi</th>
                                        <th>Nama Persyaratan</th>
                                        <th style="width:265px; min-width:265px;">Tahapan Sidang</th>
                                        <th style="width:140px; min-width:140px;">Strata</th>
                                        <th style="width:170px;">Status</th>
                                        <th style="width:45px;"></th>
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
                                        <th style="width:45px;">No</th>
                                        <th style="width:200px;">Program Studi</th>
                                        <th>Nama Persyaratan</th>
                                        <th style="width:150px;">Tahapan</th>
                                        <th style="width:120px;">Hasil</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="importResultBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="importFooter">
                    <div class="mr-auto small text-muted" id="importSummary"></div>
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
    let importMeta = { tahapan: [], strata: ['S1', 'S2', 'S3'] };
    var tahapanLabelsClient = @json(\App\Services\MasterExcelService::TAHAPAN_LABELS);

    function csrfToken() {
        return document.querySelector('input[name="_token"]').value;
    }

    function escapeAttr(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function normalisasiTahap(v) {
        return String(v ?? '').toLowerCase().replace(/\s+/g, ' ').trim();
    }

    function canonicalStrataClient(v) {
        const raw = String(v ?? '').toUpperCase().replace(/\s+/g, '');
        return /^S?[123]$/.test(raw) ? 'S' + raw.replace(/^S?/, '') : '';
    }

    function canonicalTahapanClient(v) {
        const raw = String(v ?? '').trim();
        if (!raw) return '';
        const needle = normalisasiTahap(raw);
        for (const t of importMeta.tahapan) {
            if (normalisasiTahap(t) === needle) return t;
        }
        for (const t of importMeta.tahapan) {
            const label = tahapanLabelsClient[t];
            if (label && normalisasiTahap(label) === needle) return t;
        }
        return '';
    }

    function statusBadge(r) {
        if (r.result === 'ok') return { label: 'Baru', cls: 'success' };
        if (r.result === 'duplicate') return { label: 'Duplikat', cls: 'warning' };
        return { label: 'Tidak Valid', cls: 'danger' };
    }

    function uploadImport(input) {
        if (!input.files.length) return;
        const file = input.files[0];
        const fd = new FormData();
        fd.append('file', file);

        const loading = document.getElementById('importLoading');
        document.getElementById('importLoadingText').textContent = 'Membaca file Excel...';
        loading.style.display = 'flex';

        fetch('{{ route("master.persyaratan.import-preview") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
            body: fd
        }).then(r => r.json()).then(data => {
            loading.style.display = 'none';
            if (!data.success) {
                const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Gagal membaca file Excel.');
                showToast('error', msg);
                return;
            }
            if (!data.rows || !data.rows.length) {
                showToast('error', 'File tidak berisi data. Pastikan baris header sudah sesuai template.');
                return;
            }
            importRows = data.rows;
            importMeta.tahapan = data.tahapan_options || [];
            importMeta.strata = data.strata_options || ['S1', 'S2', 'S3'];
            renderImportPreview();
            document.getElementById('importPreviewWrap').style.display = 'block';
            document.getElementById('importResultWrap').style.display = 'none';
            document.getElementById('importModalTitle').innerHTML = '<i class="fas fa-file-excel mr-2"></i>Preview Import Persyaratan';
            document.getElementById('importBtnSave').style.display = 'inline-block';
            document.getElementById('importBtnCancel').innerHTML = 'Batal';
            document.getElementById('importBtnCancel').onclick = null;
            new bootstrap.Modal(document.getElementById('modalImport')).show();
        }).catch(() => {
            loading.style.display = 'none';
            showToast('error', 'Gagal membaca file Excel.');
        });
        input.value = '';
    }

    function tahapanSelectOptions(current) {
        const opts = importMeta.tahapan.map(t => {
            const label = tahapanLabelsClient[t] || t;
            return { value: t, label: label };
        });
        // Nilai dari file yang tidak ada di master tetap ditampilkan agar user bisa melihat & memperbaiki.
        if (current && !opts.some(o => o.value === current)) {
            opts.unshift({ value: current, label: current + ' (tidak valid)' });
        }
        return opts;
    }

    function renderImportPreview() {
        const tbody = document.getElementById('importPreviewBody');
        tbody.innerHTML = importRows.map((r, i) => {
            const st = statusBadge(r);
            const tahapOpts = tahapanSelectOptions(r.tahapan);
            return `<tr data-idx="${i}">
                <td class="text-center">${i + 1}</td>
                <td><input type="text" class="form-control form-control-sm ${r.result === 'ok' ? '' : 'is-invalid'}" list="prodiListImport" value="${escapeAttr(r.program_studi)}" oninput="updateImportRow(${i}, 'program_studi', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${escapeAttr(r.nama)}" oninput="updateImportRow(${i}, 'nama', this.value)"></td>
                <td>
                    <select class="form-control form-control-sm ${r.result === 'ok' ? '' : 'is-invalid'}" onchange="updateImportRow(${i}, 'tahapan', this.value)">
                        ${tahapOpts.map(o => `<option value="${escapeAttr(o.value)}" ${o.value === r.tahapan ? 'selected' : ''}>${escapeAttr(o.label)}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select class="form-control form-control-sm" onchange="updateImportRow(${i}, 'strata', this.value)">
                        ${['', 'S1', 'S2', 'S3'].map(s => `<option value="${s}" ${String(r.strata).toUpperCase() === s ? 'selected' : ''}>${s || '-'}</option>`).join('')}
                    </select>
                </td>
                <td class="text-center">
                    <span class="badge badge-${st.cls}">${st.label}</span>
                    <div class="small text-muted mt-1" style="max-width:180px;">${escapeAttr(r.message)}</div>
                </td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" title="Hapus baris" onclick="removeImportRow(${i})"><i class="fas fa-times"></i></button></td>
            </tr>`;
        }).join('');

        updateImportSummary();
    }

    function updateImportSummary() {
        const ok = importRows.filter(r => r.result === 'ok').length;
        const dup = importRows.filter(r => r.result === 'duplicate').length;
        const bad = importRows.filter(r => r.result === 'error').length;
        const el = document.getElementById('importSummary');
        if (el) {
            el.innerHTML = `<i class="fas fa-info-circle mr-1"></i>${ok} baris akan disimpan, ${dup} duplikat, ${bad} tidak valid.`;
        }
    }

    function updateImportRow(idx, field, value) {
        importRows[idx][field] = value;
        const r = importRows[idx];

        const programStudi = String(r.program_studi).trim();
        const nama = String(r.nama).trim();
        const strata = canonicalStrataClient(r.strata);
        const tahapan = canonicalTahapanClient(r.tahapan);

        r.result = 'ok';
        r.message = 'Siap disimpan';
        if (!programStudi) { r.result = 'error'; r.message = 'PROGRAM STUDI kosong'; }
        else if (!nama) { r.result = 'error'; r.message = 'NAMA PERSYARATAN kosong'; }
        else if (!strata) { r.result = 'error'; r.message = 'Strata harus S1/S2/S3'; }
        else if (!tahapan) { r.result = 'error'; r.message = 'Tahapan tidak terdaftar di master'; }
        else { r.tahapan = tahapan; r.strata = strata; }

        if (r.result === 'ok') {
            const key = programStudi.toLowerCase() + '|' + strata + '|' + String(r.tahapan).toLowerCase() + '|' + nama.toLowerCase();
            for (let j = 0; j < importRows.length; j++) {
                if (j === idx) continue;
                const o = importRows[j];
                if (String(o.result) !== 'ok') continue;
                const okey = String(o.program_studi).trim().toLowerCase() + '|' + canonicalStrataClient(o.strata) + '|'
                    + String(o.tahapan).toLowerCase() + '|' + String(o.nama).trim().toLowerCase();
                if (okey === key) { r.result = 'duplicate'; r.message = 'Duplikat baris lain di file'; break; }
            }
        }

        const tr = document.querySelector(`#importPreviewBody tr[data-idx="${idx}"]`);
        if (tr) {
            const st = statusBadge(r);
            const badge = tr.querySelector('.badge');
            badge.className = 'badge badge-' + st.cls;
            badge.textContent = st.label;
            const msg = tr.querySelector('.small.text-muted');
            if (msg) msg.textContent = r.message;
        }
        updateImportSummary();
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

        fetch('{{ route("master.persyaratan.import-store") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json', 'Content-Type': 'application/json' },
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
        document.getElementById('importModalTitle').innerHTML = '<i class="fas fa-clipboard-check mr-2"></i>Hasil Import Persyaratan';
        document.getElementById('importBtnSave').style.display = 'none';
        document.getElementById('importSummary').innerHTML = '';
        document.getElementById('importBtnCancel').innerHTML = 'Selesai';
        document.getElementById('importBtnCancel').onclick = () => location.reload();

        document.getElementById('importResultSummary').innerHTML =
            `<div class="alert ${data.inserted > 0 ? 'alert-success' : 'alert-warning'} py-2 mb-0">
                <i class="fas ${data.rolled_back ? 'fa-ban' : 'fa-check-circle'} mr-1"></i>
                ${data.rolled_back
                    ? `<strong>Import dibatalkan — tidak ada data yang disimpan.</strong> <strong>${data.failed}</strong> baris gagal, sehingga seluruh baris ikut dibatalkan.`
                    : `<strong>${data.inserted}</strong> data berhasil disimpan, <strong>${data.failed}</strong> gagal / dilewati.`}
            </div>`;

        document.getElementById('importResultBody').innerHTML = data.results.map((r, i) => `
            <tr>
                <td class="text-center">${i + 1}</td>
                <td>${escapeAttr(r.program_studi) || '-'}</td>
                <td>${escapeAttr(r.nama) || '-'}</td>
                <td>${escapeAttr(r.tahapan) || '-'}</td>
                <td class="text-center">
                    ${r.status === 'success'
                        ? '<span class="badge badge-success"><i class="fas fa-check mr-1"></i>Berhasil</span>'
                        : '<span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Gagal</span>'}
                </td>
                <td class="small">${escapeAttr(r.message)}</td>
            </tr>`).join('');

        showToast(data.failed > 0 ? 'error' : 'success',
            data.rolled_back
                ? `Import dibatalkan: tidak ada data yang disimpan (${data.failed} baris gagal).`
                : `Import selesai: ${data.inserted} berhasil, ${data.failed} gagal.`);
    }

    const userProdiId = @json($userProdiId);
    const isTuProdi = @json(session('auth_user.role')) === 'TU Prodi';

    // Options tahapan diambil dari master t_tahapan, dikelompokkan per strata.
    // Master saat ini hanya memuat tahapan S3, jadi strata tanpa daftar sendiri
    // tetap boleh memakai semua master tahapan (server juga memvalidasi begitu).
    var tahapanAll = @json(
        $tahapans->map(function ($t) {
            return [
                'value' => $t->tahapan,
                'label' => \App\Services\MasterExcelService::tahapanLabel($t->tahapan),
            ];
        })->values()
    );
    var tahapanByStrata = @json(
        collect($tahapans)->groupBy('strata')->map(function ($rows) {
            return $rows->map(function ($t) {
                return [
                    'value' => $t->tahapan,
                    'label' => \App\Services\MasterExcelService::tahapanLabel($t->tahapan),
                ];
            })->values();
        })
    );
    ['S1', 'S2', 'S3'].forEach(function (s) {
        if (!tahapanByStrata[s]) tahapanByStrata[s] = [];
    });

    function filterTahapanByStrata() {
        var strata = document.getElementById('f_strata').value;
        var tahapanSelect = document.getElementById('f_tahapan_sidang');
        var currentVal = tahapanSelect.value;
        var options = tahapanByStrata[strata] || [];
        if (!options.length) options = tahapanAll;
        tahapanSelect.innerHTML = '';
        var placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = '-- Pilih Tahapan --';
        tahapanSelect.appendChild(placeholder);
        options.forEach(function(opt) {
            var el = document.createElement('option');
            el.value = opt.value;
            el.textContent = opt.label;
            tahapanSelect.appendChild(el);
        });
        // Restore previous value if still available
        if (currentVal) {
            for (var i = 0; i < tahapanSelect.options.length; i++) {
                if (tahapanSelect.options[i].value === currentVal) {
                    tahapanSelect.value = currentVal;
                    break;
                }
            }
        }
    }

    function openCreate() {
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah Persyaratan';
        document.getElementById('mainForm').action = '{{ route("master.persyaratan.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('dataId').value = '';
        document.getElementById('f_nama').value = '';
        document.getElementById('f_strata').value = 'S3';
        filterTahapanByStrata();
        if (isTuProdi && userProdiId) {
            document.getElementById('f_id_prodi').value = userProdiId;
        }
        document.getElementById('statusAktif').checked = true;

        document.getElementById('listContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
    }

    function openEdit(id) {
        fetch('{{ url("master/persyaratan") }}/' + id + '/edit', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
        })
        .then(function(r) { return r.json(); })
        .then(function(item) {
            if (!item) return;
            document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit mr-2"></i>Edit Persyaratan';
            document.getElementById('mainForm').action = '{{ url("master/persyaratan") }}/' + id;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('dataId').value = id;
            document.getElementById('f_nama').value = item.nama;
            document.getElementById('f_strata').value = item.strata;
            filterTahapanByStrata();
            document.getElementById('f_tahapan_sidang').value = item.tahapan_sidang;
            if (item.id_prodi) {
                document.getElementById('f_id_prodi').value = item.id_prodi;
            } else if (isTuProdi && userProdiId) {
                document.getElementById('f_id_prodi').value = userProdiId;
            }
            (item.status_aktif === 'AKTIF' ? document.getElementById('statusAktif') : document.getElementById('statusNonaktif')).checked = true;

            document.getElementById('listContainer').style.display = 'none';
            document.getElementById('formContainer').style.display = 'block';
        });
    }

    function closeForm() {
        document.getElementById('formContainer').style.display = 'none';
        document.getElementById('listContainer').style.display = 'block';
    }

    function openDelete(id) {
        document.getElementById('deleteForm').action = '{{ url("master/persyaratan") }}/' + id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    }

    document.getElementById('mainForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) { submitBtn.disabled = true; }
        fetch(this.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Accept': 'application/json' },
            body: new FormData(this)
        }).then(function(r) {
            return r.json().then(function(data) {
                return { ok: r.ok, data: data };
            }).catch(function() {
                return { ok: r.ok, data: null };
            });
        }).then(function(res) {
            if (submitBtn) { submitBtn.disabled = false; }
            if (res.ok && res.data && res.data.success) {
                closeForm();
                showToast('success', 'Data persyaratan berhasil disimpan.');
                setTimeout(() => location.reload(), 1200);
            } else {
                var msg = res.data && res.data.message ? res.data.message : 'Data tidak valid. Periksa kembali inputan.';
                if (res.data && res.data.errors) {
                    msg = Object.values(res.data.errors).flat().join('\n');
                }
                showToast('error', msg);
            }
        }).catch(function(err) {
            if (submitBtn) { submitBtn.disabled = false; }
            showToast('error', 'Terjadi kesalahan, coba lagi.');
        });
    });

    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) { submitBtn.disabled = true; }
        fetch(this.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Accept': 'application/json' },
            body: new FormData(this)
        }).then(function(r) {
            return r.json().then(function(data) {
                return { ok: r.ok, data: data };
            }).catch(function() {
                return { ok: r.ok, data: null };
            });
        }).then(function(res) {
            if (submitBtn) { submitBtn.disabled = false; }
            if (res.ok && res.data && res.data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalDelete')).hide();
                showToast('success', 'Data persyaratan berhasil dihapus.');
                setTimeout(() => location.reload(), 1200);
            } else {
                var msg = res.data && res.data.message ? res.data.message : 'Gagal menghapus data.';
                showToast('error', msg);
            }
        }).catch(function(err) {
            if (submitBtn) { submitBtn.disabled = false; }
            showToast('error', 'Terjadi kesalahan, coba lagi.');
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
    bindFilters('{{ route("master.persyaratan.index") }}', 'persyaratanTableContainer');
</script>
@endpush
