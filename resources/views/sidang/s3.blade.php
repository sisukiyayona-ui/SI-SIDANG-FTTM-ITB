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
                <h5 class="mb-0"><i class="fas fa-tasks mr-2"></i>Tracking Progress Sidang S3</h5>
                <div class="d-flex align-items-center">
                @if(in_array(session('auth_user.role'), ['TU Prodi', 'FS']))
                    <button type="button" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#tambahJudulModal">
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
        <div class="card-body">
            <div class="table-responsive">
                <!-- Table untuk semua strata (S1, S2, S3) -->
                <table class="table table-bordered table-hover text-center">
                    <thead style="background-color: #6998d3; color: white;">
                        <tr>
                            <th rowspan="2" class="align-middle" style="width: 50px;">No</th>
                            <th rowspan="2" class="align-middle">NIM</th>
                            <th rowspan="2" class="align-middle">Nama</th>
                            <th rowspan="2" class="align-middle" style="width: 25%;">Judul</th>
                            <th rowspan="2" class="align-middle">Tahap I</th>
                            <th rowspan="2" class="align-middle">Tahap II<br>(Proposal)</th>
                            <th colspan="4" class="align-middle">Tahap III</th>
                            <th rowspan="2" class="align-middle">Tahap IV<br>(Sidang Akhir)</th>
                        </tr>
                        <tr>
                            <th class="align-middle" style="background-color: #9fbce4; color: white;">SK I</th>
                            <th class="align-middle" style="background-color: #9fbce4; color: white;">SK II</th>
                            <th class="align-middle" style="background-color: #9fbce4; color: white;">SK III</th>
                            <th class="align-middle" style="background-color: #9fbce4; color: white;">SK IV</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tracking as $item)
                            <tr>
                            <td class="align-middle">{{ $loop->iteration }}</td>
                            <td class="align-middle">{{ $item->Nim }}</td>
                            <td class="align-middle">{{ $item->nama_mhs }}</td>
                            <td class="text-left text-muted">
                                @if(session('auth_user.role') === 'TU Prodi')
                                    <a href="{{ route('sidang.s3.ubah-judul', $item->id_judul) }}" class="text-decoration-none text-primary">{{ $item->Judul }}</a>
                                @else
                                    <a href="#" class="text-decoration-none">{{ $item->Judul }}</a>
                                @endif
                            </td>
                            <td class="align-middle">
                                    <span class="d-block mb-1 text-{{ getStatusColor($item->tahap1) == 'secondary' ? 'muted' : getStatusColor($item->tahap1) }}" role="button" onclick="showTahapForm('tahap I', {{ $item->id_judul }})">
                                        {{ ucfirst($item->tahap1) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="d-block mb-1 text-{{ getStatusColor($item->tahap2) == 'secondary' ? 'muted' : getStatusColor($item->tahap2) }}" role="button" onclick="showTahapForm('tahap II', {{ $item->id_judul }})">
                                        {{ ucfirst($item->tahap2) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="d-block mb-1 text-{{ getStatusColor($item->sk1) == 'secondary' ? 'muted' : getStatusColor($item->sk1) }}" role="button" onclick="showTahapForm('SK I', {{ $item->id_judul }})">
                                        {{ ucfirst($item->sk1) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="d-block mb-1 text-{{ getStatusColor($item->sk2) == 'secondary' ? 'muted' : getStatusColor($item->sk2) }}" role="button" onclick="showTahapForm('SK II', {{ $item->id_judul }})">
                                        {{ ucfirst($item->sk2) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="d-block mb-1 text-{{ getStatusColor($item->sk3) == 'secondary' ? 'muted' : getStatusColor($item->sk3) }}" role="button" onclick="showTahapForm('SK III', {{ $item->id_judul }})">
                                        {{ ucfirst($item->sk3) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="d-block mb-1 text-{{ getStatusColor($item->sk4) == 'secondary' ? 'muted' : getStatusColor($item->sk4) }}" role="button" onclick="showTahapForm('SK IV', {{ $item->id_judul }})">
                                        {{ ucfirst($item->sk4) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="d-block mb-1 text-{{ getStatusColor($item->tahap4) == 'secondary' ? 'muted' : getStatusColor($item->tahap4) }}" role="button" onclick="showTahapForm('tahap IV', {{ $item->id_judul }})">
                                        {{ ucfirst($item->tahap4) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $tracking->links('pagination::bootstrap-4') }}
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('sidang.s3.store-judul') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahJudulModalLabel"><i class="fas fa-plus mr-2"></i>Tambah Judul</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Mahasiswa</label>
                            <select name="id_user_mhs" id="mhsSelect" class="form-control" required onchange="document.getElementById('nipDisplay').value = this.options[this.selectedIndex].dataset.nip || ''">
                                <option value="">-- Pilih Mahasiswa --</option>
                                @foreach($mahasiswaList as $mhs)
                                    <option value="{{ $mhs->id }}" data-nip="{{ $mhs->NIP_NIM }}">{{ $mhs->NAMA_LENGKAP }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>NIP/NIM</label>
                            <input type="text" id="nipDisplay" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label>Judul</label>
                            <textarea name="judul" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showTahapForm(tahapan, idJudul, activeTab) {
        const title = document.getElementById('tahapTitle');
        const content = document.getElementById('tahapFormContent');
        
        title.textContent = tahapan;
        content.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 text-muted">Memuat...</p></div>';
        
        $('#tahapModal').modal('show');
        
        fetch(`/sidang/tahap/${encodeURIComponent(tahapan)}?id_judul=${idJudul}&_=${new Date().getTime()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
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
                // Force empty state for penilaian tables
                var tb = document.getElementById('penilaianTableBody');
                if (tb) {
                    Array.from(tb.children).forEach(function(r) {
                        if (r.id !== 'penilaianFormEmptyRow') r.style.display = 'none';
                    });
                    var er = document.getElementById('penilaianFormEmptyRow');
                    if (er) er.style.display = '';
                }
                var rb = document.getElementById('penilaianReportBody');
                if (rb) {
                    Array.from(rb.children).forEach(function(r) {
                        if (r.id !== 'penilaianEmptyRow') r.style.display = 'none';
                    });
                    var er2 = document.getElementById('penilaianEmptyRow');
                    if (er2) er2.style.display = '';
                }
                if (activeTab) {
                    var tabLink = content.querySelector('a[href="#' + activeTab + '"]');
                    if (tabLink) {
                        $(tabLink).tab('show');
                    }
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
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Status kelengkapan updated');
            } else {
                alert('Gagal update status kelengkapan');
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }

    function uploadFile(input, idSyarat, tahapan, idJudul) {
        if (!input.files || input.files.length === 0) return;

        let file = input.files[0];
        
        // Show filename
        let nameEl = document.getElementById('file-name-' + idSyarat);
        if (nameEl) nameEl.textContent = file.name;

        // Show inline progress bar
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
            if (e.lengthComputable && progressBar) {
                let percentComplete = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percentComplete + '%';
            }
        };

        xhr.onload = function () {
            setTimeout(() => {
                if (progressContainer) progressContainer.classList.add('d-none');
            }, 1000);

            if (xhr.status === 200) {
                try {
                    let data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        let checkIcon = document.getElementById('check-' + idSyarat);
                        if (checkIcon) checkIcon.classList.remove('d-none');
                        
                        let linkElement = document.getElementById('link-' + idSyarat);
                        if (linkElement && data.file_url) {
                            linkElement.href = data.file_url;
                            linkElement.classList.remove('d-none');
                        }

                        let btnLabel = document.getElementById('label-file-' + idSyarat);
                        let btnText = document.getElementById('btn-text-' + idSyarat);
                        let btnIcon = document.getElementById('icon-file-' + idSyarat);
                        
                        if (btnLabel) {
                            btnLabel.classList.remove('btn-primary');
                            btnLabel.classList.add('btn-outline-primary');
                        }
                        if (btnText) btnText.textContent = 'Ubah';
                        if (btnIcon) {
                            btnIcon.classList.remove('fa-upload');
                            btnIcon.classList.add('fa-edit');
                        }
                        
                        showCustomToast('File berhasil diupload', 'success');
                    } else {
                        showCustomToast(data.message || 'Gagal upload file', 'error');
                    }
                } catch (e) {
                    showCustomToast('Error parsing response: ' + e.message, 'error');
                }
            } else {
                showCustomToast('Upload failed with status: ' + xhr.status, 'error');
            }
            input.value = '';
        };

        xhr.onerror = function () {
            if (progressContainer) progressContainer.classList.add('d-none');
            showCustomToast('Upload error occurred', 'error');
            input.value = '';
        };

        xhr.send(formData);
    }

    function showCustomToast(message, type) {
        let toast = document.createElement('div');
        toast.className = 'toast show';
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.style.minWidth = '250px';
        toast.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
        toast.style.color = 'white';
        toast.style.padding = '15px';
        toast.style.borderRadius = '5px';
        toast.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
        toast.textContent = message;

        document.body.appendChild(toast);

        setTimeout(function() {
            toast.remove();
        }, 3000);
    }

    function savePersyaratan(tahapan) {
        showCustomToast('Persyaratan berhasil disimpan', 'success');
    }

    function submitTimSidang(event) {
        event.preventDefault();
        event.stopPropagation();
        const form = event.target;
        const formData = new FormData(form);

        fetch('/sidang/tim-sidang', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCustomToast('Tim Pembimbing berhasil disimpan', 'success');
                form.reset();
                var isTahap2 = form.id === 'timSidangFormTahap2';
                var formEl = document.getElementById(isTahap2 ? 'timFormTahap2' : 'timForm');
                var btnEl = document.getElementById(isTahap2 ? 'timAddBtnTahap2' : 'timAddBtn');
                if (formEl) formEl.style.display = 'none';
                if (btnEl) btnEl.style.display = 'block';
                // Reload the tahap content to show updated table
                const tahapan = formData.get('tahapan_sidang');
                const idJudul = formData.get('id_judul');
                showTahapForm(tahapan, idJudul);
            } else {
                showCustomToast(data.message || 'Gagal menyimpan tim pembimbing', 'error');
            }
        })
        .catch(error => {
            showCustomToast('Terjadi kesalahan: ' + error, 'error');
        });
    }

    function submitSkForm(event) {
        event.preventDefault();
        var form = event.target;
        var formData = new FormData(form);
        var idJudul = formData.get('id_judul');
        var tahapan = formData.get('tahapan_sidang');

        fetch('/sidang/sk', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#skModal').modal('hide');
                var skInput = form.querySelector('input[name="no_sk"]');
                var skValue = skInput ? skInput.value : '';
                form.reset();
                showCustomToast('No SK berhasil dibuat', 'success');
                // Refresh SK dropdown: add new option and select it
                var skSelect = document.querySelector('.sk-container select[name="id_sk"]');
                if (skSelect && data.sk) {
                    var opt = document.createElement('option');
                    opt.value = data.sk.id;
                    opt.textContent = data.sk.no_sk;
                    skSelect.appendChild(opt);
                    skSelect.value = data.sk.id;
                } else {
                    location.reload();
                }
            } else {
                showCustomToast(data.message || 'Gagal membuat No SK', 'error');
            }
        })
        .catch(error => {
            showCustomToast('Error: ' + error, 'error');
        });
    }

    // Global event listener for tim sidang form submission
    document.addEventListener('submit', function(e) {
        if (e.target && (e.target.id === 'timSidangForm' || e.target.id === 'timSidangFormTahap2')) {
            e.preventDefault();
            e.stopPropagation();
            const form = e.target;
            const formData = new FormData(form);
            const timId = form.querySelector('[name="id"]').value;

            if (timId) {
                formData.append('_method', 'PUT');
            }

            fetch('/sidang/tim-sidang' + (timId ? '/' + timId : ''), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCustomToast('Tim Pembimbing berhasil disimpan', 'success');
                    form.reset();
                    var isTahap2 = form.id === 'timSidangFormTahap2';
                    var formEl = document.getElementById(isTahap2 ? 'timFormTahap2' : 'timForm');
                    var btnEl = document.getElementById(isTahap2 ? 'timAddBtnTahap2' : 'timAddBtn');
                    if (formEl) formEl.style.display = 'none';
                    if (btnEl) btnEl.style.display = 'block';
                    // Reload the tahap content to show updated table
                    const tahapan = formData.get('tahapan_sidang');
                    const idJudul = formData.get('id_judul');
                    showTahapForm(tahapan, idJudul);
                } else {
                    showCustomToast(data.message || 'Gagal menyimpan tim pembimbing', 'error');
                }
            })
            .catch(error => {
                showCustomToast('Terjadi kesalahan: ' + error, 'error');
            });
        }
    });

    function saveStatusLulus() {
        const statusLulus = document.getElementById('statusLulusDisplay') ? document.getElementById('statusLulusDisplay').value : null;
        const statusLulus2 = document.getElementById('statusLulusDisplay2') ? document.getElementById('statusLulusDisplay2').value : null;
        const finalStatus = statusLulus || statusLulus2;

        if (!finalStatus) {
            showCustomToast('Pilih status lulus terlebih dahulu', 'error');
            return;
        }

        const idJudul = document.querySelector('input[name="id_judul"]') ? document.querySelector('input[name="id_judul"]').value : null;
        const tahapan = document.querySelector('input[name="tahapan_sidang"]') ? document.querySelector('input[name="tahapan_sidang"]').value : null;

        if (!idJudul) {
            showCustomToast('ID Judul tidak ditemukan', 'error');
            return;
        }

        fetch('/sidang/status-lulus/' + idJudul, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status_lulus: finalStatus,
                tahapan_sidang: tahapan
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCustomToast('Status Lulus berhasil disimpan', 'success');
            } else {
                showCustomToast('Error: ' + (data.error || 'Terjadi kesalahan'), 'error');
            }
        })
        .catch(error => {
            showCustomToast('Error: ' + error, 'error');
        });
    }
</script>
@endpush

@php
function getStatusColor($status) {
    switch($status) {
        case 'belum diajukan':
            return 'secondary';
        case 'dalam proses':
            return 'warning';
        case 'Lulus':
            return 'success';
        default:
            return 'info';
    }
}
@endphp