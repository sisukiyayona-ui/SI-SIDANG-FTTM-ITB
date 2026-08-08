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
            <h5 class="mb-0"><i class="fas fa-file-alt mr-2"></i>Daftar Persyaratan</h5>
            <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                <a class="btn btn-sm btn-light" href="{{ route('master.persyaratan.template') }}">
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
                <form method="GET" action="{{ route('master.persyaratan.index') }}" id="filterForm" autocomplete="off">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Persyaratan</th>
                            <th>Tahapan Sidang</th>
                            <th>Strata</th>
                            <th>Program Studi</th>
                            <th>Status</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="nama_persyaratan" placeholder="Cari..." value="{{ request('nama_persyaratan') }}"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="tahapan_sidang" placeholder="Cari..." value="{{ request('tahapan_sidang') }}"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="strata" placeholder="Cari..." value="{{ request('strata') }}"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="nama_prodi" placeholder="Cari..." value="{{ request('nama_prodi') }}"></th>
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
                        @foreach($persyaratan as $i => $item)
                            <tr>
                                <td>{{ $persyaratan->firstItem() + $i }}</td>
                                <td><a href="javascript:void(0)" onclick="openEdit({{ $item['id'] }})" class="text-decoration-none">{{ $item['nama'] }}</a></td>
                                <td>{{ $item['tahapan_sidang'] }}</td>
                                <td>{{ $item['strata'] }}</td>
                                <td>{{ $item['kode_prodi'] }} - {{ $item['nama_prodi'] }}</td>
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
                {{ $persyaratan->links() }}
            </div>
        </div>
    </div>
</div>

    {{-- Form Container (In-Page CRUD Form) --}}
    <div id="formContainer" class="card" style="display: none;">
        <div class="card-header" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white;">
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
                            <label for="tahapan_sidang" class="form-label fw-semibold text-secondary">Tahapan Sidang</label>
                            <select name="tahapan_sidang" id="f_tahapan_sidang" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" required>
                                @foreach($tahapans as $t)
                                    <option value="{{ $t->Tahapan }}">{{ $t->Tahapan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="strata" class="form-label fw-semibold text-secondary">Strata</label>
                            <select name="strata" id="f_strata" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" required>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3" selected>S3</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="f_id_prodi" class="form-label fw-semibold text-secondary">Program Studi</label>
                            @php $isTuProdi = session('auth_user.role') === 'TU Prodi'; @endphp
                            <select name="id_prodi" id="f_id_prodi" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" {{ $isTuProdi ? 'disabled' : '' }}>
                                @forelse($prodis as $p)
                                    <option value="{{ $p->id }}" {{ $isTuProdi ? 'selected' : '' }}>{{ $p->kode_prodi }} - {{ $p->nama_prodi }}</option>
                                @empty
                                    <option value="">Prodi tidak tersedia</option>
                                @endforelse
                            </select>
                            @if($isTuProdi)
                                <input type="hidden" name="id_prodi" id="f_id_prodi_hidden" value="{{ $userProdiId }}">
                            @endif
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
@endsection

@push('scripts')
<script>
    function uploadImport(input) {
        if (!input.files.length) return;
        const file = input.files[0];
        const fd = new FormData();
        fd.append('file', file);
        fd.append('_token', document.querySelector('input[name="_token"]').value);
        fetch('{{ route("master.persyaratan.import") }}', {
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

    const userProdiId = @json($userProdiId);
    const isTuProdi = @json(session('auth_user.role')) === 'TU Prodi';

    function openCreate() {
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah Persyaratan';
        document.getElementById('mainForm').action = '{{ route("master.persyaratan.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('dataId').value = '';
        document.getElementById('f_nama').value = '';
        document.getElementById('f_tahapan_sidang').selectedIndex = 0;
        document.getElementById('f_strata').value = 'S3';
        if (isTuProdi && userProdiId) {
            document.getElementById('f_id_prodi').value = userProdiId;
            document.getElementById('f_id_prodi_hidden').value = userProdiId;
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
            document.getElementById('f_tahapan_sidang').value = item.tahapan_sidang;
            document.getElementById('f_strata').value = item.strata;
            if (isTuProdi && userProdiId) {
                document.getElementById('f_id_prodi').value = userProdiId;
                document.getElementById('f_id_prodi_hidden').value = userProdiId;
            } else if (item.id_prodi) {
                document.getElementById('f_id_prodi').value = item.id_prodi;
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
        fetch(this.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: new FormData(this)
        }).then(() => {
            closeForm();
            showToast('success', 'Data persyaratan berhasil disimpan.');
            setTimeout(() => location.reload(), 1200);
        });
    });

    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch(this.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: new FormData(this)
        }).then(() => {
            bootstrap.Modal.getInstance(document.getElementById('modalDelete')).hide();
            showToast('success', 'Data persyaratan berhasil dihapus.');
            setTimeout(() => location.reload(), 1200);
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
