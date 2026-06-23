@extends('layouts.master')

@section('title', 'Persyaratan - SI SIDANG FTTM ITB')
@section('page_title', 'Data Master Persyaratan')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Persyaratan</li>
    </ol>
@endsection

@section('content')
    <div id="listContainer" class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-alt mr-2"></i>Daftar Persyaratan</h5>
            <button class="btn btn-sm btn-accent" onclick="openCreate()">
                <i class="fas fa-plus mr-1"></i> Tambah Persyaratan
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Persyaratan</th>
                            <th>Tahapan Sidang</th>
                            <th>Strata</th>
                            <th>Program Studi</th>
                            <th>Status</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($persyaratan as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $item['nama'] }}</td>
                                <td><span class="badge bg-secondary">{{ $item['tahapan_sidang'] }}</span></td>
                                <td><span class="badge bg-primary">{{ $item['strata'] }}</span></td>
                                <td>{{ $item['nama_prodi'] }} ({{ $item['kode_prodi'] }})</td>
                                <td>
                                    <span class="badge bg-{{ $item['status_aktif'] === 'AKTIF' ? 'success' : 'danger' }}">
                                        {{ $item['status_aktif'] }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="openEdit({{ $item['id'] }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="openDelete({{ $item['id'] }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Form Container (In-Page CRUD Form) --}}
    <div id="formContainer" class="card" style="display: none;">
        <div class="card-header">
            <h5 class="mb-0" id="formTitle"><i class="fas fa-plus mr-2"></i>Tambah Persyaratan</h5>
        </div>
        <div class="card-body">
            <form id="mainForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">
                <input type="hidden" name="id" id="dataId">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Persyaratan</label>
                    <input type="text" name="nama" id="f_nama" class="form-control" placeholder="Nama persyaratan" required>
                </div>
                <div class="mb-3">
                    <label for="tahapan_sidang" class="form-label">Tahapan Sidang</label>
                    <select name="tahapan_sidang" id="f_tahapan_sidang" class="form-control" required>
                        @foreach($tahapans as $t)
                            <option value="{{ $t->Tahapan }}">{{ $t->Tahapan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="strata" class="form-label">Strata</label>
                    <select name="strata" id="f_strata" class="form-control" required>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3" selected>S3</option>
                    </select>
                </div>
                
                @if(session('auth_user.role') === 'Admin')
                    <div class="mb-3">
                        <label for="id_prodi" class="form-label">Program Studi</label>
                        <select name="id_prodi" id="f_id_prodi" class="form-control" required>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label">Program Studi</label>
                        <input type="text" class="form-control" value="{{ session('auth_user.nama_prodi') }}" disabled>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Status Aktif</label>
                    <div class="d-flex gap-3">
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
    const syaratData = @json($persyaratan);

    function openCreate() {
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah Persyaratan';
        document.getElementById('mainForm').action = '{{ route("master.persyaratan.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('dataId').value = '';
        document.getElementById('f_nama').value = '';
        document.getElementById('f_tahapan_sidang').selectedIndex = 0;
        document.getElementById('f_strata').value = 'S3';
        const prodiSelect = document.getElementById('f_id_prodi');
        if (prodiSelect) prodiSelect.selectedIndex = 0;
        document.getElementById('statusAktif').checked = true;

        document.getElementById('listContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
    }

    function openEdit(id) {
        const item = syaratData.find(p => p.id === id);
        if (!item) return;
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit mr-2"></i>Edit Persyaratan';
        document.getElementById('mainForm').action = '{{ url("master/persyaratan") }}/' + id;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('dataId').value = id;
        document.getElementById('f_nama').value = item.nama;
        document.getElementById('f_tahapan_sidang').value = item.tahapan_sidang;
        document.getElementById('f_strata').value = item.strata;
        const prodiSelect = document.getElementById('f_id_prodi');
        if (prodiSelect) prodiSelect.value = item.id_prodi;
        (item.status_aktif === 'AKTIF' ? document.getElementById('statusAktif') : document.getElementById('statusNonaktif')).checked = true;

        document.getElementById('listContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
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
</script>
@endpush
