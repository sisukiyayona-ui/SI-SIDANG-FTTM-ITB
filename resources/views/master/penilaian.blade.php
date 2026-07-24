@extends('layouts.master')

@section('title', 'Penilaian - SI SIDANG FTTM ITB')
@section('page_title', 'Data Master Penilaian')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Data Master</a></li>
        <li class="breadcrumb-item active">Penilaian</li>
    </ol>
@endsection

@section('content')
    <div id="listContainer" class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Daftar Komponen Penilaian</h5>
            <button class="btn btn-sm btn-accent" onclick="openCreate()">
                <i class="fas fa-plus mr-1"></i> Tambah Komponen
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Parameter Penilaian</th>
                            <th>No Form</th>
                            <th>Tahapan Sidang</th>
                            <th>Strata</th>
                            <th>Program Studi</th>
                            <th>Status Catatan</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cari..." data-col="1"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cari..." data-col="2"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cari..." data-col="3"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cari..." data-col="4"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cari..." data-col="5"></th>
                            <th>
                                <select class="form-control form-control-sm column-search" data-col="6">
                                    <option value="">Semua</option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                            </th>
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cari..." data-col="7"></th>
                            <th>
                                <select class="form-control form-control-sm column-search" data-col="8">
                                    <option value="">Semua</option>
                                    <option value="AKTIF">AKTIF</option>
                                    <option value="NON AKTIF">NON AKTIF</option>
                                </select>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penilaian as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><a href="javascript:void(0)" onclick="openEdit({{ $item['id'] }})" class="text-decoration-none">{{ $item['nama'] }}</a></td>
                                <td>{{ $item['no_form'] ?? '-' }}</td>
                                <td>{{ $item['tahapan_sidang'] }}</td>
                                <td>{{ $item['strata'] }}</td>
                                <td>{{ $item['kode_prodi'] }} - {{ $item['nama_prodi'] }}</td>
                                <td>
                                    <span class="badge bg-{{ in_array($item['status_catatan'], ['t', 'y'], true) ? 'success' : 'secondary' }}">
                                        {{ in_array($item['status_catatan'], ['t', 'y'], true) ? 'Ya' : 'Tidak' }}
                                    </span>
                                </td>
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
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $penilaian->links() }}
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
                            <label for="f_tahapan_sidang" class="form-label fw-semibold text-secondary">Tahapan Sidang</label>
                            <select name="tahapan_sidang" id="f_tahapan_sidang" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" required>
                                @foreach($tahapans as $t)
                                    <option value="{{ $t->Tahapan }}">{{ $t->Tahapan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="f_strata" class="form-label fw-semibold text-secondary">Strata</label>
                            <select name="strata" id="f_strata" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" required>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3" selected>S3</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="f_id_prodi" class="form-label fw-semibold text-secondary">Program Studi</label>
                            <select name="id_prodi" id="f_id_prodi" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" required>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id }}">{{ $p->kode_prodi }} - {{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
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
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Status Catatan</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input type="radio" name="status_catatan" value="y" class="form-check-input" id="statusCatatanYa">
                                    <label class="form-check-label" for="statusCatatanYa">Ya (y)</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="status_catatan" value="t" class="form-check-input" id="statusCatatanTidak" checked>
                                    <label class="form-check-label" for="statusCatatanTidak">Tidak (t)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="f_keterangan" class="form-label fw-semibold text-secondary">Keterangan</label>
                            <textarea name="Keterangan" id="f_keterangan" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px;" placeholder="Keterangan tambahan" rows="2"></textarea>
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
    function openCreate() {
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah Komponen';
        document.getElementById('mainForm').action = '{{ route("master.penilaian.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('dataId').value = '';
        document.getElementById('f_penilaian').value = '';
        document.getElementById('f_no_form').value = '';
        document.getElementById('f_tahapan_sidang').selectedIndex = 0;
        document.getElementById('f_strata').value = 'S3';
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
            document.getElementById('f_tahapan_sidang').value = item.tahapan_sidang;
            document.getElementById('f_strata').value = item.strata;
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

    document.querySelectorAll('.column-search').forEach(input => {
        input.addEventListener('input', filterTable);
        input.addEventListener('change', filterTable);
    });

    function filterTable() {
        const filters = Array.from(document.querySelectorAll('.column-search')).map(input => ({
            colIndex: parseInt(input.dataset.col),
            value: input.value.toLowerCase()
        }));

        document.querySelectorAll('table tbody tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            if (!cells || cells.length === 0) return;
            let isMatch = true;
            filters.forEach(filter => {
                if (filter.value && cells[filter.colIndex]) {
                    const cellText = cells[filter.colIndex].textContent.toLowerCase();
                    if (!cellText.includes(filter.value)) {
                        isMatch = false;
                    }
                }
            });
            row.style.display = isMatch ? '' : 'none';
        });
    }
</script>
@endpush
