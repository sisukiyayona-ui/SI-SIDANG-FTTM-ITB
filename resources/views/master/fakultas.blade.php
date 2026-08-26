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

            <div class="table-responsive">
                <form method="GET" action="{{ route('master.fakultas.index') }}" id="filterForm" autocomplete="off">
                <table class="table table-striped table-hover" id="fakultasTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode Fakultas</th>
                            <th>Nama Fakultas</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="kode_fs" placeholder="Cari..." value="{{ request('kode_fs') }}"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="nama_fs" placeholder="Cari..." value="{{ request('nama_fs') }}"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fakultas as $i => $item)
                            <tr>
                                <td>{{ $fakultas->firstItem() + $i }}</td>
                                <td><span class="badge bg-info">{{ $item['kode'] }}</span></td>
                                <td><a href="javascript:void(0)" onclick="openEdit({{ $item['id'] }})" class="text-decoration-none">{{ $item['nama'] }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </form>
            </div>

            <nav>
                <ul class="pagination justify-content-end mb-0">
                    {{ $fakultas->links() }}
                </ul>
            </nav>
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
@endsection

@push('scripts')
<script>
    function uploadImport(input) {
        if (!input.files.length) return;
        const file = input.files[0];
        const fd = new FormData();
        fd.append('file', file);
        fd.append('_token', document.querySelector('input[name="_token"]').value);
        fetch('{{ route("master.fakultas.import") }}', {
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
