@extends('layouts.master')

@section('title', 'Penilaian - SI SIDANG FTTM ITB')
@section('page_title', 'Data Master Penilaian')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Data Master</a></li>
        <li class="breadcrumb-item active">Penilaian</li>
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
            <h5 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Daftar Komponen Penilaian</h5>
            <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                <a class="btn btn-sm btn-light" href="{{ route('master.penilaian.template') }}">
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
            <div class="table-responsive">
                <form method="GET" action="{{ route('master.penilaian.index') }}" id="filterForm" autocomplete="off">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Parameter Penilaian</th>
                            <th>No Form</th>
                            <th>Tahapan Sidang</th>
                            <th>Strata</th>
                            <th>Program Studi</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="penilaian" placeholder="Cari..." value="{{ request('penilaian') }}"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="no_form" placeholder="Cari..." value="{{ request('no_form') }}"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="tahapan_sidang" placeholder="Cari..." value="{{ request('tahapan_sidang') }}"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="strata" placeholder="Cari..." value="{{ request('strata') }}"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="nama_prodi" placeholder="Cari..." value="{{ request('nama_prodi') }}"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="Keterangan" placeholder="Cari..." value="{{ request('Keterangan') }}"></th>
                            <th>
                                <select class="form-control form-control-sm column-search" name="status_aktif">
                                    <option value="">Semua</option>
                                    <option value="AKTIF" {{ request('status_aktif') == 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                                    <option value="NON AKTIF" {{ request('status_aktif') == 'NON AKTIF' ? 'selected' : '' }}>NON AKTIF</option>
                                </select>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penilaian as $i => $item)
                            <tr>
                                <td>{{ $penilaian->firstItem() + $i }}</td>
                                <td><a href="javascript:void(0)" onclick="openEdit({{ $item['id'] }})" class="text-decoration-none">{{ $item['nama'] }}</a></td>
                                <td>{{ $item['no_form'] ?? '-' }}</td>
                                <td>{{ $tahapanLabels[$item['tahapan_sidang']] ?? $item['tahapan_sidang'] }}</td>
                                <td>{{ $item['strata'] }}</td>
                                <td>{{ $item['kode_prodi'] }} - {{ $item['nama_prodi'] }}</td>
                                <td>{{ $item['Keterangan'] ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $item['status_aktif'] === 'AKTIF' ? 'success' : 'danger' }}">
                                        {{ $item['status_aktif'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </form>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $penilaian->links() }}
            </div>
        </div>
    </div>
</div>

    {{-- Form Container (In-Page CRUD Form) --}}
    <div id="formContainer" class="card" style="display: none;">
        <div class="card-header" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white;">
            <h5 class="mb-0" id="formTitle"><i class="fas fa-plus mr-2"></i>Tambah Komponen</h5>
        </div>
        <div class="card-body">
            <form id="mainForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">
                <input type="hidden" name="id" id="dataId">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="penilaian" class="form-label fw-semibold text-secondary">Parameter Penilaian</label>
                            <input type="text" name="penilaian" id="f_penilaian" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" placeholder="Nama parameter komponen" required>
                        </div>
                        <div class="mb-3">
                            <label for="f_keterangan" class="form-label fw-semibold text-secondary">Keterangan</label>
                            <textarea name="Keterangan" id="f_keterangan" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" placeholder="Keterangan tambahan" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="f_no_form" class="form-label fw-semibold text-secondary">No Form</label>
                            <input type="text" name="no_form" id="f_no_form" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" placeholder="Nomor form">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="f_strata" class="form-label fw-semibold text-secondary">Strata</label>
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
                            <label for="f_tahapan_sidang" class="form-label fw-semibold text-secondary">Tahapan Sidang</label>
                            <select name="tahapan_sidang" id="f_tahapan_sidang" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" required>
                            </select>
                        </div>
                    </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="f_id_prodi" class="form-label fw-semibold text-secondary">Program Studi</label>
                            @if(session('auth_user.role') === 'TU Prodi')
                                @php $loginProdiP = \App\Models\TProdi::where('kode_prodi', session('auth_user.kode_prodi'))->first(); @endphp
                                <input type="hidden" name="id_prodi" value="{{ $loginProdiP?->id }}">
                                <input type="text" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px; background-color:#e9ecef;" value="{{ session('auth_user.kode_prodi') }} - {{ session('auth_user.nama_prodi') }}" disabled>
                            @else
                                <select name="id_prodi" id="f_id_prodi" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" required>
                                    @foreach($prodis as $p)
                                        <option value="{{ $p->id }}">{{ $p->kode_prodi }} - {{ $p->nama_prodi }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Status Aktif</label>
                            <div class="d-flex" style="gap: 20px;">
                                <div class="form-check">
                                    <input type="radio" name="status_aktif" value="AKTIF" class="form-check-input" id="statusAktif" checked>
                                    <label class="form-check-label" for="statusAktif">&nbsp;Aktif</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="status_aktif" value="NON AKTIF" class="form-check-input" id="statusNonaktif">
                                    <label class="form-check-label" for="statusNonaktif">&nbsp;Non Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Status Catatan</label>
                    <div class="d-flex" style="gap: 20px;">
                        <div class="form-check">
                            <input type="radio" name="status_catatan" value="y" class="form-check-input" id="statusCatatanYa">
                            <label class="form-check-label" for="statusCatatanYa">&nbsp;Ya</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="status_catatan" value="t" class="form-check-input" id="statusCatatanTidak" checked>
                            <label class="form-check-label" for="statusCatatanTidak">&nbsp;Tidak</label>
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
                        <h6>Yakin ingin menghapus komponen ini?</h6>
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
    function uploadImport(input) {
        if (!input.files.length) return;
        const file = input.files[0];
        const fd = new FormData();
        fd.append('file', file);
        fd.append('_token', document.querySelector('input[name="_token"]').value);
        fetch('{{ route("master.penilaian.import") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: fd
        }).then(r => r.json()).then(data => {
            const msg = data.message || (data.errors ? Object.values(data.errors).join('\n') : 'Terjadi kesalahan.');
            showToast(data.success ? 'success' : 'error', msg);
            setTimeout(() => location.reload(), 1500);
        }).catch(() => showToast('error', 'Gagal mengupload file.'));
        input.value = '';
    }

    // Item 15: Mapping strata -> tahapan options
    var tahapanByStrata = {
        'S1': [
            { value: 'TA 1', label: 'TA 1' },
            { value: 'TA 2', label: 'TA 2' }
        ],
        'S2': [
            { value: 'TA 1', label: 'TA 1' },
            { value: 'TA 2', label: 'TA 2' }
        ],
        'S3': [
            { value: 'tahap I', label: 'Ujian Kualifikasi' },
            { value: 'tahap II', label: 'Ujian Proposal' },
            { value: 'SK I', label: 'SK I' },
            { value: 'SK II', label: 'SK II' },
            { value: 'SK III', label: 'SK III' },
            { value: 'SK IV', label: 'SK IV' },
            { value: 'tahap IV', label: 'Sidang Terbuka / Tertutup' }
        ]
    };

    function filterTahapanByStrata() {
        var strata = document.getElementById('f_strata').value;
        var tahapanSelect = document.getElementById('f_tahapan_sidang');
        var currentVal = tahapanSelect.value;
        var options = tahapanByStrata[strata] || [];
        tahapanSelect.innerHTML = '';
        options.forEach(function(opt) {
            var el = document.createElement('option');
            el.value = opt.value;
            el.textContent = opt.label;
            tahapanSelect.appendChild(el);
        });
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
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah Komponen';
        document.getElementById('mainForm').action = '{{ route("master.penilaian.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('dataId').value = '';
        document.getElementById('f_penilaian').value = '';
        document.getElementById('f_no_form').value = '';
        document.getElementById('f_strata').value = 'S3';
        filterTahapanByStrata();
        const prodiSelect = document.getElementById('f_id_prodi');
        if (prodiSelect) prodiSelect.selectedIndex = 0;
        document.getElementById('statusAktif').checked = true;
        document.getElementById('statusCatatanTidak').checked = true;
        document.getElementById('f_keterangan').value = '';

        document.getElementById('listContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
    }

    function openEdit(id) {
        fetch('{{ url("master/penilaian") }}/' + id + '/edit', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
        })
        .then(function(r) { return r.json(); })
        .then(function(item) {
            if (!item) return;
            document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit mr-2"></i>Edit Komponen';
            document.getElementById('mainForm').action = '{{ url("master/penilaian") }}/' + id;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('dataId').value = id;
            document.getElementById('f_penilaian').value = item.nama;
            document.getElementById('f_no_form').value = item.no_form || '';
            document.getElementById('f_strata').value = item.strata;
            filterTahapanByStrata();
            document.getElementById('f_tahapan_sidang').value = item.tahapan_sidang;
            var prodiSelect = document.getElementById('f_id_prodi');
            if (prodiSelect) prodiSelect.value = item.id_prodi;
            (item.status_aktif === 'AKTIF' ? document.getElementById('statusAktif') : document.getElementById('statusNonaktif')).checked = true;
            (item.status_catatan === 'y' ? document.getElementById('statusCatatanYa') : document.getElementById('statusCatatanTidak')).checked = true;
            document.getElementById('f_keterangan').value = item.Keterangan || '';

            document.getElementById('listContainer').style.display = 'none';
            document.getElementById('formContainer').style.display = 'block';
        });
    }

    function closeForm() {
        document.getElementById('formContainer').style.display = 'none';
        document.getElementById('listContainer').style.display = 'block';
    }

    function openDelete(id) {
        document.getElementById('deleteForm').action = '{{ url("master/penilaian") }}/' + id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    }

    document.getElementById('mainForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const method = document.getElementById('methodField').value;
        
        if (method !== 'POST') {
            formData.append('_method', method);
        }
        
        // Debug: log form data
        console.log('Form action:', form.action);
        console.log('Form method:', method);
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        }).then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    let msg = 'Terjadi kesalahan.';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join('\n');
                    }
                    showToast('error', msg);
                }).catch(() => {
                    showToast('error', 'Terjadi kesalahan saat menyimpan.');
                });
            }
            closeForm();
            showToast('success', 'Data komponen berhasil disimpan.');
            setTimeout(() => location.reload(), 1200);
        }).catch(() => {
            showToast('error', 'Gagal terhubung ke server.');
        });
    });

    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new FormData(this)
        }).then(response => {
            if (!response.ok) {
                showToast('error', 'Gagal menghapus data.');
                return;
            }
            bootstrap.Modal.getInstance(document.getElementById('modalDelete')).hide();
            showToast('success', 'Data komponen berhasil dihapus.');
            setTimeout(() => location.reload(), 1200);
        }).catch(() => {
            showToast('error', 'Gagal terhubung ke server.');
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
