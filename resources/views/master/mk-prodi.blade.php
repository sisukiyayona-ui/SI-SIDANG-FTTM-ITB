@extends('layouts.master')

@section('title', 'Data Mata Kuliah Prodi - SI SIDANG FTTM ITB')
@section('page_title', 'Data Master Mata Kuliah Prodi')

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="#">Data Master</a></li>
    <li class="breadcrumb-item active">Mata Kuliah Prodi</li>
</ol>
@endsection

@section('content')
<div id="listContainer">
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Data Mata Kuliah Prodi</h3>
        <button class="btn btn-sm btn-primary" onclick="openCreate()">
            <i class="fas fa-plus mr-1"></i> Tambah
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive" id="mkProdiTableContainer">
            @include('master._mk_prodi_table', ['items' => $items])
        </div>
    </div>
</div>
</div>

{{-- In-page form --}}
<div id="formContainer" class="card" style="display: none;">
    <div class="card-header" style="background: #ffffff; color: #1e293b; border-bottom: 1px solid #e2e8f0;">
        <h5 class="mb-0" id="formTitle"><i class="fas fa-plus mr-2"></i>Tambah Mata Kuliah Prodi</h5>
    </div>
    <div class="card-body">
        <form id="mainForm" method="POST" action="{{ route('master.mk-prodi.store') }}">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            <input type="hidden" name="id" id="dataId">

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="kode_prodi" class="form-label fw-semibold text-secondary">Kode Prodi</label>
                        <select name="kode_prodi" id="f_kode_prodi" class="form-control" required onchange="syncNamaProdi()">
                            <option value="">-- Pilih Prodi --</option>
                            @foreach($prodiList as $p)
                                <option value="{{ $p['kode'] }}" data-nama="{{ $p['nama'] }}">{{ $p['kode'] }} - {{ $p['nama'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nama_prodi" class="form-label fw-semibold text-secondary">Nama Prodi</label>
                        <input type="text" name="nama_prodi" id="f_nama_prodi" class="form-control" placeholder="Nama Prodi" required readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="kode_mk" class="form-label fw-semibold text-secondary">Kode MK</label>
                        <input type="text" name="kode_mk" id="f_kode_mk" class="form-control" placeholder="Kode MK" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tahapan_sidang" class="form-label fw-semibold text-secondary">Tahapan Sidang</label>
                        <select name="tahapan_sidang" id="f_tahapan_sidang" class="form-control" required>
                            <option value="">-- Pilih Tahapan --</option>
                            @foreach($tahapanList as $t)
                                <option value="{{ $t['value'] }}">{{ $t['label'] }}</option>
                            @endforeach
                        </select>
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
                    <h6>Yakin ingin menghapus mata kuliah prodi ini?</h6>
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
function syncNamaProdi() {
    var sel = document.getElementById('f_kode_prodi');
    var nama = document.getElementById('f_nama_prodi');
    var opt = sel.options[sel.selectedIndex];
    if (opt && opt.dataset.nama) {
        nama.value = opt.dataset.nama;
    } else {
        nama.value = '';
    }
}

function openCreate() {
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah Mata Kuliah Prodi';
    document.getElementById('mainForm').action = '{{ route('master.mk-prodi.store') }}';
    document.getElementById('methodField').value = 'POST';
    document.getElementById('dataId').value = '';
    document.getElementById('f_kode_prodi').value = '';
    document.getElementById('f_nama_prodi').value = '';
    document.getElementById('f_kode_mk').value = '';
    document.getElementById('f_tahapan_sidang').value = '';
    document.getElementById('listContainer').style.display = 'none';
    document.getElementById('formContainer').style.display = 'block';
    document.getElementById('formContainer').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function openEdit(id) {
    fetch('{{ url('master/mk-prodi') }}/' + id + '/edit', { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit mr-2"></i>Edit Mata Kuliah Prodi';
            document.getElementById('mainForm').action = '{{ url('master/mk-prodi') }}/' + id;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('dataId').value = data.id;
            var sel = document.getElementById('f_kode_prodi');
            sel.value = data.kode_prodi;
            syncNamaProdi();
            document.getElementById('f_nama_prodi').value = data.nama_prodi;
            document.getElementById('f_kode_mk').value = data.kode_mk;
            document.getElementById('f_tahapan_sidang').value = data.tahapan_sidang;
            document.getElementById('listContainer').style.display = 'none';
    document.getElementById('formContainer').style.display = 'block';
            document.getElementById('formContainer').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
}

function closeForm() {
    document.getElementById('formContainer').style.display = 'none';
    document.getElementById('listContainer').style.display = 'block';
}

function openDelete(id) {
    if (!confirm('Yakin ingin menghapus data ini? Data yang dihapus tidak dapat dikembalikan.')) {
        return;
    }
    fetch('{{ url('master/mk-prodi') }}/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    }).then(function () { location.reload(); });
}

var _searchTimer;
function applyFilters() {
    var params = new URLSearchParams();
    document.querySelectorAll('.column-search').forEach(function (el) {
        if (el.name && el.value) {
            params.set(el.name, el.value);
        }
    });
    fetch('{{ route('master.mk-prodi.index') }}?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function (r) { return r.json(); })
    .then(function (d) {
        if (d && d.html) {
            document.getElementById('mkProdiTableContainer').innerHTML = d.html;
        }
    });
}

document.addEventListener('input', function (e) {
    if (e.target.classList && e.target.classList.contains('column-search')) {
        clearTimeout(_searchTimer);
        _searchTimer = setTimeout(applyFilters, 400);
    }
});
document.addEventListener('change', function (e) {
    if (e.target.classList && e.target.classList.contains('column-search')) {
        clearTimeout(_searchTimer);
        _searchTimer = setTimeout(applyFilters, 200);
    }
});

function openDelete(id) {
    document.getElementById('deleteForm').action = '{{ url('master/mk-prodi') }}/' + id;
    new bootstrap.Modal(document.getElementById('modalDelete')).show();
}

document.getElementById('mainForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) { submitBtn.disabled = true; }
    fetch(this.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Accept': 'application/json' },
        body: new FormData(this)
    }).then(function (r) {
        return r.json().then(function (data) { return { ok: r.ok, data: data }; })
            .catch(function () { return { ok: r.ok, data: null }; });
    }).then(function (res) {
        if (submitBtn) { submitBtn.disabled = false; }
        if (res.ok && res.data && res.data.success) {
            closeForm();
            showToast('success', 'Data mata kuliah prodi berhasil disimpan.');
            setTimeout(function () { location.reload(); }, 1200);
        } else {
            var msg = 'Data tidak valid. Periksa kembali inputan.';
            if (res.data && res.data.errors) {
                msg = Object.values(res.data.errors).flat().join('\n');
            } else if (res.data && (res.data.message || res.data.error)) {
                msg = res.data.message || res.data.error;
            }
            showToast('error', msg);
        }
    }).catch(function () {
        if (submitBtn) { submitBtn.disabled = false; }
        showToast('error', 'Terjadi kesalahan, coba lagi.');
    });
});

document.getElementById('deleteForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) { submitBtn.disabled = true; }
    fetch(this.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Accept': 'application/json' },
        body: new FormData(this)
    }).then(function (r) {
        return r.json().then(function (data) { return { ok: r.ok, data: data }; })
            .catch(function () { return { ok: r.ok, data: null }; });
    }).then(function (res) {
        if (submitBtn) { submitBtn.disabled = false; }
        if (res.ok && res.data && res.data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalDelete')).hide();
            showToast('success', 'Data mata kuliah prodi berhasil dihapus.');
            setTimeout(function () { location.reload(); }, 1200);
        } else {
            showToast('error', (res.data && res.data.message) ? res.data.message : 'Gagal menghapus data.');
        }
    }).catch(function () {
        if (submitBtn) { submitBtn.disabled = false; }
        showToast('error', 'Terjadi kesalahan, coba lagi.');
    });
});

function ajaxFilter(routeName, containerId) {
    var params = {};
    document.querySelectorAll('.column-search').forEach(function (input) {
        if (input.value) params[input.name] = input.value;
    });
    var qs = Object.keys(params).map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(params[k]); }).join('&');
    var url = routeName + (qs ? '?' + qs : '');
    var container = document.getElementById(containerId);

    container.style.opacity = '0.5';
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            container.innerHTML = data.html;
            container.style.opacity = '1';
            bindFilters(routeName, containerId);
        })
        .catch(function (err) {
            console.error('AJAX filter error:', err);
            container.style.opacity = '1';
        });
}

function bindFilters(routeName, containerId) {
    document.querySelectorAll('.column-search').forEach(function (input) {
        input.removeEventListener('input', input._ajaxHandler);
        input._ajaxHandler = function () {
            clearTimeout(window._filterTimeout);
            window._filterTimeout = setTimeout(function () { ajaxFilter(routeName, containerId); }, 400);
        };
        input.addEventListener('input', input._ajaxHandler);
        input.removeEventListener('change', input._changeHandler);
        input._changeHandler = function () {
            ajaxFilter(routeName, containerId);
        };
        input.addEventListener('change', input._changeHandler);
    });
    document.querySelectorAll('.pagination a').forEach(function (link) {
        link.removeEventListener('click', link._ajaxHandler);
        link._ajaxHandler = function (e) {
            e.preventDefault();
            var pagContainer = document.getElementById(containerId);
            pagContainer.style.opacity = '0.5';
            fetch(this.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    pagContainer.innerHTML = data.html;
                    pagContainer.style.opacity = '1';
                    bindFilters(routeName, containerId);
                })
                .catch(function (err) {
                    console.error('AJAX pagination error:', err);
                    pagContainer.style.opacity = '1';
                });
        };
        link.addEventListener('click', link._ajaxHandler);
    });
}
bindFilters('{{ route('master.mk-prodi.index') }}', 'mkProdiTableContainer');
</script>
@endpush
