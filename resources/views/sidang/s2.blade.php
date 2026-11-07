@extends('layouts.master')

@section('title', 'Progress Sidang - SI SIDANG FTTM ITB')
@section('page_title', 'Progress Sidang')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">Progress Sidang</li>
    </ol>
@endsection

@section('content')
    <!-- Tracking Progress Sidang -->
    <div id="trackingCard" class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tasks mr-2"></i>Tracking Progress Sidang S2</h5>
                <div class="d-flex align-items-center">
                @if(in_array(session('auth_user.role'), ['TU Prodi', 'FS']))
                    <button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="modal" data-target="#tambahJudulModal">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                @endif
                @if($juduls && $juduls->count() > 1)
                    <div class="form-group mb-0">
                        <select class="form-control" onchange="window.location.href='{{ route('mahasiswa.set-judul', '__ID__') }}'.replace('__ID__', this.value)">
                            @foreach($juduls as $judul)
                                <option value="{{ $judul->id }}" {{ $activeJudulId == $judul->id ? 'selected' : '' }}>
                                    {{ $judul->Judul }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <style>
                #trackingCard .badge { white-space: normal; }
                #trackingCard td { word-break: break-word; }
                #tahapModal .modal-body {
                    max-height: calc(100vh - 140px);
                    overflow-y: auto;
                }
                #tahapModal .select2-dropdown { z-index: 1056; }
            </style>
            <div class="table-responsive" id="trackingTableContainer">
                @include('sidang._s2_table')
            </div>
        </div>
    </div>

    <!-- Modal Tahapan -->
    <div class="modal fade" id="tahapModal" tabindex="-1" role="dialog" aria-labelledby="tahapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tahapModalLabel"><i class="fas fa-clipboard-list mr-2"></i>Form Tahapan: <span id="tahapTitle"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="tahapFormContent">
                        <div class="text-center py-4 text-muted">Memuat...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Judul (TU Prodi / FS) -->
    <div class="modal fade" id="tambahJudulModal" tabindex="-1" role="dialog" aria-labelledby="tambahJudulModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content tambah-judul-modal">
                <form action="{{ route('sidang.s2.store-judul') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahJudulModalLabel"><i class="fas fa-plus-circle mr-2"></i>Tambah Judul</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="fw-semibold">Nama Mahasiswa <span class="text-danger">*</span></label>
                                    <select name="id_user_mhs" id="mhsSelect" class="form-control form-control-lg" required onchange="document.getElementById('nipDisplay').value = this.options[this.selectedIndex].dataset.nip || ''">
                                        <option value="">-- Pilih Mahasiswa --</option>
                                        @foreach($mahasiswaList as $mhs)
                                            <option value="{{ $mhs->id }}" {{ old('id_user_mhs') == $mhs->id ? 'selected' : '' }} data-nip="{{ $mhs->NIP_NIM }}">{{ $mhs->NAMA_LENGKAP }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="fw-semibold">NIP/NIM</label>
                                    <input type="text" id="nipDisplay" class="form-control form-control-lg" readonly placeholder="-">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="fw-semibold">Judul <span class="text-danger">*</span></label>
                            <textarea name="judul" class="form-control" rows="2" required placeholder="Masukkan judul penelitian"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="fw-semibold">Abstrak <span class="text-danger">*</span></label>
                            <textarea name="abstrak" id="abstrakInput" class="form-control" rows="7" required placeholder="Masukkan abstrak penelitian"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-lg px-4" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-lg px-4"><i class="fas fa-save mr-1"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .tambah-judul-modal .modal-header {
            background: #1a1f2e; color: #fff; border: none;
            padding: 1rem 1.5rem; border-radius: .5rem .5rem 0 0;
        }
        .tambah-judul-modal .modal-title { font-weight: 600; font-size: 1.15rem; }
        .tambah-judul-modal .modal-header .close { color: #fff; opacity: .85; text-shadow: none; }
        .tambah-judul-modal .modal-header .close:hover { opacity: 1; }
        .tambah-judul-modal .modal-body { padding: 1.5rem; }
        .tambah-judul-modal .form-group label { font-size: .92rem; margin-bottom: .35rem; color: var(--bs-body-color, #212529); }
        .tambah-judul-modal .form-control { border-radius: .4rem; font-size: .95rem; padding: .55rem .8rem; }
        .tambah-judul-modal textarea.form-control { resize: vertical; min-height: 70px; }
        .tambah-judul-modal #abstrakInput { min-height: 140px; }
        .tambah-judul-modal .form-control[readonly] { background-color: #f1f3f5; border-style: dashed; }
        .tambah-judul-modal .modal-footer { border-top: 1px solid rgba(0,0,0,.08); padding: 1rem 1.5rem; }
        .tambah-judul-modal .modal-footer .btn { border-radius: .4rem; font-weight: 500; }
        html.dark-mode .tambah-judul-modal .form-control { background-color: #2b3543; border-color: #445066; color: #e9ecef; }
        html.dark-mode .tambah-judul-modal .form-control[readonly] { background-color: #232b36; color: #adb5bd; }
        html.dark-mode .tambah-judul-modal .form-group label { color: #e9ecef; }
        html.dark-mode .tambah-judul-modal .modal-footer { border-top-color: rgba(255,255,255,.08); }
        html.dark-mode .tambah-judul-modal .btn-light { background: #3a4454; color: #e9ecef; border-color: #4a5568; }
        html.dark-mode .tambah-judul-modal .text-muted { color: #9aa6b8 !important; }
        @media (max-width: 576px) { .tambah-judul-modal .modal-body { padding: 1rem; } }
    </style>
@endsection

@push('scripts')
<script>
    function getTahapLabelJS(tahapan) {
        var labels = {
            'Tesis I': 'Tesis I',
            'Tesis II': 'Tesis II'
        };
        return labels[tahapan] || tahapan;
    }

    function showTahapForm(tahapan, idJudul, activeTab) {
        const title = document.getElementById('tahapTitle');
        const content = document.getElementById('tahapFormContent');

        title.textContent = getTahapLabelJS(tahapan);
        content.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 text-muted">Memuat...</p></div>';

        $('#tahapModal').modal('show');

        fetch(`/sidang/tahap/${encodeURIComponent(tahapan)}?id_judul=${idJudul}&_=${new Date().getTime()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;
            content.querySelectorAll('script').forEach(function(oldScript) {
                var newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(function(attr) {
                    newScript.setAttribute(attr.name, attr.value);
                });
                newScript.textContent = oldScript.textContent;
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });
            ['penilaianTableBody', 'penilaianReportBody'].forEach(function(id) {
                var tb = document.getElementById(id);
                var emptyId = id === 'penilaianTableBody' ? 'penilaianFormEmptyRow' : 'penilaianEmptyRow';
                if (tb) {
                    Array.from(tb.children).forEach(function(r) {
                        if (r.id !== emptyId) r.style.display = 'none';
                    });
                    var er = document.getElementById(emptyId);
                    if (er) er.style.display = '';
                }
            });
            if (activeTab) {
                var tabLink = content.querySelector('a[href="#' + activeTab + '"]');
                if (tabLink) $(tabLink).tab('show');
            }
        })
        .catch(error => {
            content.innerHTML = '<p class="text-danger text-center py-4">Error loading form: ' + error + '</p>';
        });
    }

    function updateKelengkapan(idSyarat, isChecked) {
        let formData = new FormData();
        formData.append('id_syarat_sidang', idSyarat);
        formData.append('status_lengkap', isChecked ? 'y' : 't');
        formData.append('_token', '{{ csrf_token() }}');
        fetch('/mahasiswa/update-kelengkapan', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        }).then(r => r.json()).then(data => {
            showCustomToast(data.success ? 'Status kelengkapan diperbarui' : 'Gagal update', data.success ? 'success' : 'error');
        }).catch(() => showCustomToast('Terjadi kesalahan', 'error'));
    }

    function uploadFile(input, idSyarat, tahapan, idJudul) {
        if (!input.files || input.files.length === 0) return;
        let file = input.files[0];
        let progressContainer = document.getElementById('progress-container-' + idSyarat);
        let progressBar = document.getElementById('progress-bar-' + idSyarat);
        if (progressContainer) progressContainer.classList.remove('d-none');
        if (progressBar) progressBar.style.width = '0%';
        let formData = new FormData();
        formData.append('file', file);
        formData.append('id_syarat_sidang', idSyarat);
        formData.append('tahapan_sidang', tahapan);
        if (idJudul) formData.append('id_judul', idJudul);
        formData.append('_token', '{{ csrf_token() }}');
        let xhr = new XMLHttpRequest();
        xhr.open('POST', '/mahasiswa/upload-persyaratan', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable && progressBar) progressBar.style.width = Math.round((e.loaded / e.total) * 100) + '%';
        };
        xhr.onload = function() {
            setTimeout(() => { if (progressContainer) progressContainer.classList.add('d-none'); }, 1000);
            if (xhr.status === 200) {
                try {
                    let data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        let checkIcon = document.getElementById('check-' + idSyarat);
                        if (checkIcon) checkIcon.classList.remove('d-none');
                        let linkEl = document.getElementById('link-' + idSyarat);
                        if (linkEl && data.file_url) { linkEl.href = data.file_url; linkEl.classList.remove('d-none'); }
                        showCustomToast('File berhasil diupload', 'success');
                    } else {
                        showCustomToast(data.message || 'Gagal upload file', 'error');
                    }
                } catch(e) { showCustomToast('Error: ' + e.message, 'error'); }
            } else {
                showCustomToast('Upload gagal (HTTP ' + xhr.status + ')', 'error');
            }
            input.value = '';
        };
        xhr.onerror = function() {
            if (progressContainer) progressContainer.classList.add('d-none');
            showCustomToast('Upload error', 'error');
            input.value = '';
        };
        xhr.send(formData);
    }

    function showCustomToast(message, type) { showToast(type, message); }

    function savePersyaratan(tahapan) {
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('tahapan_sidang', tahapan);
        var content = document.getElementById('tahapFormContent');
        var idJudulInput = content ? content.querySelector('[name="id_judul"]') : null;
        var idJudul = idJudulInput ? idJudulInput.value : '';
        if (!idJudul) { showCustomToast('ID Judul tidak ditemukan', 'error'); return; }
        formData.append('id_judul', idJudul);
        var seen = {};
        (content ? content.querySelectorAll('input[type="checkbox"][data-syarat-id]') : []).forEach(function(cb) {
            var id = cb.getAttribute('data-syarat-id');
            if (id && !seen[id]) { seen[id] = true; formData.append('kelengkapan[' + id + ']', cb.checked ? 'y' : 't'); }
        });
        if (typeof persyaratanFiles !== 'undefined') {
            Object.keys(persyaratanFiles).forEach(function(id) { formData.append('files[' + id + ']', persyaratanFiles[id]); });
        }
        fetch('/mahasiswa/save-all-persyaratan', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showCustomToast('Persyaratan berhasil disimpan', 'success');
                if (typeof persyaratanFiles !== 'undefined') persyaratanFiles = {};
                if (typeof showTahapForm === 'function') showTahapForm(tahapan, idJudul, 'persyaratan');
                else location.reload();
            } else {
                showCustomToast(data.message || 'Gagal menyimpan persyaratan', 'error');
            }
        })
        .catch(err => showCustomToast('Terjadi kesalahan: ' + err, 'error'));
    }

    function submitSkForm(event) {
        event.preventDefault();
        var form = event.target;
        var formData = new FormData(form);
        var idJudul = formData.get('id_judul');
        var tahapan = formData.get('tahapan_sidang');
        var submitBtn = form.querySelector('button[type="submit"]');
        var originalLabel = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...'; }
        fetch('/sidang/sk', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalLabel; }
            if (data.success) {
                $('#skModal').modal('hide');
                form.reset();
                showCustomToast('No SK berhasil dibuat', 'success');
                var skSelect = document.querySelector('.sk-container select[name="id_sk"]');
                if (skSelect && data.sk) {
                    var opt = document.createElement('option');
                    opt.value = data.sk.id; opt.textContent = data.sk.no_sk;
                    skSelect.appendChild(opt); skSelect.value = data.sk.id;
                } else { location.reload(); }
            } else {
                showCustomToast(data.message || 'Gagal membuat No SK', 'error');
            }
        })
        .catch(err => {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalLabel; }
            showCustomToast('Error: ' + err, 'error');
        });
    }

    // Delegated submit for tim sidang forms inside modal
    document.addEventListener('submit', function(e) {
        if (e.target && (e.target.id === 'timSidangForm' || e.target.id === 'timSidangFormTahap2')) {
            e.preventDefault(); e.stopPropagation();
            const form = e.target;
            const formData = new FormData(form);
            const timId = form.querySelector('[name="id"]').value;
            if (timId) formData.append('_method', 'PUT');
            const submitBtn = form.querySelector('button[type="submit"]');
            const origLabel = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...'; }
            fetch('/sidang/tim-sidang' + (timId ? '/' + timId : ''), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = origLabel; }
                if (data.success) {
                    showCustomToast('Tim Pembimbing berhasil disimpan', 'success');
                    if (typeof upsertTimSidangRow === 'function') {
                        upsertTimSidangRow(form, formData, data.tim || null);
                        form.reset();
                        var idInput = form.querySelector('[name="id"]'); if (idInput) idInput.value = '';
                        var nipInput = form.querySelector('[name="nip"]'); if (nipInput) nipInput.value = '';
                        if (window.jQuery && jQuery.fn.select2) jQuery(form).find('select[name="id_user_penilai"]').val('').trigger('change');
                    } else {
                        const tahapan = formData.get('tahapan_sidang');
                        const idJudul = formData.get('id_judul');
                        showTahapForm(tahapan, idJudul);
                    }
                } else {
                    showCustomToast(data.message || 'Gagal menyimpan tim pembimbing', 'error');
                }
            })
            .catch(err => {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = origLabel; }
                showCustomToast('Terjadi kesalahan: ' + err, 'error');
            });
        }
    });

    function saveStatusLulus(triggerBtn) {
        const statusLulus = document.getElementById('statusLulusDisplay') ? document.getElementById('statusLulusDisplay').value : null;
        const statusLulus2 = document.getElementById('statusLulusDisplay2') ? document.getElementById('statusLulusDisplay2').value : null;
        const finalStatus = statusLulus || statusLulus2;
        if (!finalStatus) { showCustomToast('Pilih status lulus terlebih dahulu', 'error'); return; }
        const idJudul = document.querySelector('input[name="id_judul"]') ? document.querySelector('input[name="id_judul"]').value : null;
        const tahapan = document.querySelector('input[name="tahapan_sidang"]') ? document.querySelector('input[name="tahapan_sidang"]').value : null;
        if (!idJudul) { showCustomToast('ID Judul tidak ditemukan', 'error'); return; }
        var btn = triggerBtn || document.querySelector('button[onclick*="saveStatusLulus"]');
        var origLabel = btn ? btn.innerHTML : '';
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...'; }
        fetch('/sidang/status-lulus/' + idJudul, {
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ status_lulus: finalStatus, tahapan_sidang: tahapan })
        })
        .then(r => r.json())
        .then(data => {
            if (btn) { btn.disabled = false; btn.innerHTML = origLabel; }
            showCustomToast(data.success ? 'Status Lulus berhasil disimpan' : 'Error: ' + (data.error || 'Terjadi kesalahan'), data.success ? 'success' : 'error');
        })
        .catch(err => {
            if (btn) { btn.disabled = false; btn.innerHTML = origLabel; }
            showCustomToast('Error: ' + err, 'error');
        });
    }

    // Fallback openJadwalForm
    if (typeof window.openJadwalForm !== 'function') {
        window.openJadwalForm = function(el) {
            var form = document.getElementById('jadwalFormTahap2Form');
            if (!form) return;
            var fields = ['tgl_sidang','waktu_sidang','waktu_selesai','ruang_sidang','tgl_surat_undangan','no_surat_undangan','tgl_surat_penelaah','no_surat_penelaah','tgl_hasil_penelahan','email_surat','no_sk_kelulusan'];
            var idInput = document.getElementById('id_ajuan_tahap2');
            var data = null;
            if (el && el.dataset) {
                data = {
                    id: el.dataset.id || '',
                    tgl_sidang: el.dataset.tglSidang || null,
                    waktu_sidang: el.dataset.waktuSidang || null,
                    waktu_selesai: el.dataset.waktuSelesai || null,
                    ruang_sidang: el.dataset.ruangSidang || null,
                    tgl_surat_undangan: el.dataset.tglSuratUndangan || null,
                    no_surat_undangan: el.dataset.noSuratUndangan || null,
                    tgl_surat_penelaah: el.dataset.tglSuratPenelaah || null,
                    no_surat_penelaah: el.dataset.noSuratPenelaah || null,
                    tgl_hasil_penelahan: el.dataset.tglHasilPenelahan || null,
                    email_surat: el.dataset.emailSurat || null,
                    no_sk_kelulusan: el.dataset.noSkKelulusan || null
                };
            }
            if (data && data.id) {
                fields.forEach(function(name) {
                    var f = form.querySelector('[name="' + name + '"]');
                    if (f && data[name] !== undefined && data[name] !== null && data[name] !== '') f.value = data[name];
                });
                if (idInput) idInput.value = data.id;
            } else {
                fields.forEach(function(name) { var f = form.querySelector('[name="' + name + '"]'); if (f) f.value = ''; });
                if (idInput) idInput.value = '';
            }
            var listEl = document.getElementById('jadwalListTahap2');
            var frmEl  = document.getElementById('jadwalFormTahap2');
            if (listEl) listEl.style.display = 'none';
            if (frmEl)  frmEl.style.display  = 'block';
        };
    }

    // Delegated click for jadwal date links
    document.addEventListener('click', function(e) {
        var t = e.target;
        var link = t && t.closest ? t.closest('.jadwal-date-link') : null;
        if (!link) return;
        if (link.classList.contains('text-muted')) return;
        var content = document.getElementById('tahapFormContent');
        if (content && !content.contains(link)) return;
        e.preventDefault();
        if (typeof window.openJadwalForm === 'function') window.openJadwalForm(link);
    });

    if (document.getElementById('mhsSelect') && document.getElementById('mhsSelect').value) {
        document.getElementById('mhsSelect').dispatchEvent(new Event('change'));
        $('#tambahJudulModal').modal('show');
    }

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
            .catch(function(err) { console.error('AJAX filter error:', err); container.style.opacity = '1'; });
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
            input._changeHandler = function() { ajaxFilter(routeName, containerId); };
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
                    .then(function(data) { pagContainer.innerHTML = data.html; pagContainer.style.opacity = '1'; bindFilters(routeName, containerId); })
                    .catch(function(err) { console.error('Pagination error:', err); pagContainer.style.opacity = '1'; });
            };
            link.addEventListener('click', link._ajaxHandler);
        });
    }

    // Saat modal Form Tahapan ditutup, refresh tabel tracking
    $('#tahapModal').on('hidden.bs.modal', function() {
        window.__penilaianChanged = false;
        ajaxFilter('{{ route("sidang.s2") }}', 'trackingTableContainer');
    });

    bindFilters('{{ route("sidang.s2") }}', 'trackingTableContainer');
</script>
@endpush
