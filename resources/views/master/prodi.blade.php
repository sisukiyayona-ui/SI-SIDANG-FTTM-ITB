@extends('layouts.master')

@section('title', 'Data Prodi - SI SIDANG FTTM ITB')
@section('page_title', 'Data Master Prodi')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Prodi</li>
    </ol>
@endsection

@section('content')
    <div id="listContainer" class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-university mr-2"></i>Daftar Program Studi</h5>
            <button class="btn btn-sm btn-accent" onclick="openCreate()">
                <i class="fas fa-plus mr-1"></i> Tambah Prodi
            </button>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari prodi..." id="searchInput" onkeyup="filterTable()">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="prodiTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode Prodi</th>
                            <th>Nama Prodi</th>
                            <th>Status</th>
                            <th style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prodi as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><span class="badge bg-info">{{ $item['kode'] }}</span></td>
                                <td>{{ $item['nama'] }}</td>
                                <td>
                                    <span class="badge bg-{{ $item['status'] === 'Aktif' ? 'success' : 'danger' }}">
                                        {{ $item['status'] }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="Detail" onclick="openDetail({{ $item['id'] }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" title="Edit" onclick="openEdit({{ $item['id'] }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" title="Hapus" onclick="openDelete({{ $item['id'] }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <nav>
                <ul class="pagination pagination-dummy justify-content-center mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                </ul>
            </nav>
        </div>
    </div>

    {{-- Form Container (In-Page CRUD Form) --}}
    <div id="formContainer" class="card" style="display: none;">
        <div class="card-header">
            <h5 class="mb-0" id="modalProdiTitle"><i class="fas fa-plus mr-2"></i>Tambah Prodi</h5>
        </div>
        <div class="card-body">
            <form id="formProdi" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodProdi" value="POST">
                <input type="hidden" name="id" id="prodiId">
                <div class="mb-3">
                    <label for="kode" class="form-label">Kode Prodi</label>
                    <input type="text" name="kode" id="kode" class="form-control" placeholder="Contoh: 322" required>
                </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Prodi</label>
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama Program Studi" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" name="status" value="Aktif" class="form-check-input" id="statusAktif" checked>
                            <label class="form-check-label" for="statusAktif">Aktif</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="status" value="Nonaktif" class="form-check-input" id="statusNonaktif">
                            <label class="form-check-label" for="statusNonaktif">Nonaktif</label>
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

    {{-- Modal Detail --}}
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: #2f5597; color: #fff;">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detail Program Studi</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered mb-0">
                        <tr><th style="width: 140px;">Kode Prodi</th><td id="detailKode"></td></tr>
                        <tr><th>Nama Prodi</th><td id="detailNama"></td></tr>
                        <tr><th>Status</th><td id="detailStatus"></td></tr>
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
    const prodiData = @json($prodi);

    function openCreate() {
        document.getElementById('modalProdiTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah Prodi';
        document.getElementById('formProdi').action = '{{ route("master.prodi.store") }}';
        document.getElementById('methodProdi').value = 'POST';
        document.getElementById('prodiId').value = '';
        document.getElementById('kode').value = '';
        document.getElementById('nama').value = '';
        document.getElementById('statusAktif').checked = true;

        document.getElementById('listContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
    }

    function openEdit(id) {
        const item = prodiData.find(p => p.id === id);
        if (!item) return;
        document.getElementById('modalProdiTitle').innerHTML = '<i class="fas fa-edit mr-2"></i>Edit Prodi';
        document.getElementById('formProdi').action = '{{ url("master/prodi") }}/' + id;
        document.getElementById('methodProdi').value = 'PUT';
        document.getElementById('prodiId').value = id;
        document.getElementById('kode').value = item.kode;
        document.getElementById('nama').value = item.nama;
        document.getElementById(item.status === 'Aktif' ? 'statusAktif' : 'statusNonaktif').checked = true;

        document.getElementById('listContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
    }

    function closeForm() {
        document.getElementById('formContainer').style.display = 'none';
        document.getElementById('listContainer').style.display = 'block';
    }

    function openDetail(id) {
        const item = prodiData.find(p => p.id === id);
        if (!item) return;
        document.getElementById('detailKode').innerHTML = '<span class="badge bg-info">' + item.kode + '</span>';
        document.getElementById('detailNama').textContent = item.nama;
        document.getElementById('detailStatus').innerHTML = '<span class="badge bg-' + (item.status === 'Aktif' ? 'success' : 'danger') + '">' + item.status + '</span>';
        new bootstrap.Modal(document.getElementById('modalDetail')).show();
    }

    function openDelete(id) {
        document.getElementById('formDelete').action = '{{ url("master/prodi") }}/' + id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    }

    document.getElementById('formProdi').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        fetch(form.action, {
            method: form.querySelector('#methodProdi').value === 'PUT' ? 'POST' : 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: new FormData(form)
        }).then(() => {
            closeForm();
            showToast('success', 'Data prodi berhasil disimpan.');
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
            showToast('success', 'Data prodi berhasil dihapus.');
            setTimeout(() => location.reload(), 1200);
        });
    });

    function filterTable() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('#prodiTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
</script>
@endpush
