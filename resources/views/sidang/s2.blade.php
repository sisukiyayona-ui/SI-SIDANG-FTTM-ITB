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
                                <a href="#" class="text-decoration-none">{{ $item->Judul }}</a>
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

    function uploadFile(input, idSyarat, tahapan) {
        if (!input.files || input.files.length === 0) return;

        // Create upload progress modal
        let modalHtml = `
            <div class="modal fade" id="uploadProgressModal" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Upload File</h5>
                        </div>
                        <div class="modal-body text-center">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <h4 id="uploadPercentage">0%</h4>
                            <p class="text-muted">Mengupload file...</p>
                            <div class="progress" style="height: 10px;">
                                <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add modal to body
        let modalContainer = document.createElement('div');
        modalContainer.innerHTML = modalHtml;
        document.body.appendChild(modalContainer);

        let progressBar = document.getElementById('uploadProgressBar');
        let percentageText = document.getElementById('uploadPercentage');

        let formData = new FormData();
        formData.append('file', input.files[0]);
        formData.append('id_syarat_sidang', idSyarat);
        formData.append('tahapan_sidang', tahapan);
        formData.append('_token', '{{ csrf_token() }}');

        let xhr = new XMLHttpRequest();
        xhr.open('POST', '/mahasiswa/upload-persyaratan', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                let percentComplete = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percentComplete + '%';
                percentageText.textContent = percentComplete + '%';
            }
        };

        xhr.onload = function () {
            // Remove modal
            document.body.removeChild(modalContainer);

            if (xhr.status === 200) {
                try {
                    let data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        // Show check icon
                        let checkIcon = document.getElementById('check-' + idSyarat);
                        if (checkIcon) {
                            checkIcon.classList.remove('d-none');
                        }
                        // Show file link
                        let linkElement = document.getElementById('link-' + idSyarat);
                        if (linkElement && data.file_url) {
                            linkElement.href = data.file_url;
                            linkElement.classList.remove('d-none');
                        }
                        // Show success toast
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
        };

        xhr.onerror = function () {
            // Remove modal
            document.body.removeChild(modalContainer);
            showCustomToast('Upload error occurred', 'error');
        };

        xhr.send(formData);
    }

    function showCustomToast(message, type) {
        // Create toast element
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

        // Remove after 3 seconds
        setTimeout(function() {
            toast.remove();
        }, 3000);
    }

    function savePersyaratan(tahapan) {
        showCustomToast('Persyaratan berhasil disimpan', 'success');
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
                form.reset();
                showCustomToast('No SK berhasil dibuat', 'success');
                if (typeof showTahapForm === 'function') {
                    showTahapForm(tahapan, idJudul);
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