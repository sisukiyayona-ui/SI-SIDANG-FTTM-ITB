@php
function getStatusColor($status) {
    $s = strtolower($status ?? '');
    switch($s) {
        case 'belum diajukan':
            return 'secondary';
        case 'diproses di tu prodi':
            return 'warning';
        case 'diproses di fakultas':
            return 'orange';
        case 'menunggu pelaksanaan sidang':
        case 'menunggu approve kpps':
            return 'purple';
        case 'terjadwal':
            return 'primary';
        case 'dalam proses':
            return 'warning';
        case 'lulus':
            return 'success';
        case 'tidak lulus':
            return 'danger';
        case 'rejected':
        case 'ditolak':
            return 'danger';
        default:
            return 'info';
    }
}
@endphp
<table class="table table-bordered table-hover text-center" style="table-layout: fixed;">
    <colgroup>
        <col style="width: 60px;">
        <col style="width: 140px;">
        <col style="width: 140px;">
        <col style="width: 300px;">
        <col style="width: 160px;">
        <col style="width: 150px;">
        <col style="width: 150px;">
        <col style="width: 180px;">
    </colgroup>
    <thead style="background-color: #6998d3; color: white;">
        <tr>
            <th class="align-middle" style="width: 50px;">No</th>
            <th class="align-middle">NIM</th>
            <th class="align-middle">Nama</th>
            <th class="align-middle">Judul</th>
            <th class="align-middle">Prodi</th>
            <th class="align-middle">Tesis I</th>
            <th class="align-middle">Tesis II</th>
            <th class="align-middle">Sidang Terbuka / Tertutup</th>
        </tr>
        <tr class="tracking-filter-row" style="background-color: #f8f9fa;">
            <th></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nim" placeholder="Cari..." value="{{ request('nim') }}" style="color: #495057;"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama" placeholder="Cari..." value="{{ request('nama') }}" style="color: #495057;"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="judul" placeholder="Cari..." value="{{ request('judul') }}" style="color: #495057;"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama_prodi" placeholder="Cari Prodi..." value="{{ request('nama_prodi') }}" style="color: #495057;"></th>
            <th>
                <select class="form-control form-control-sm column-search" name="tesis1" style="color: #495057;">
                    <option value="">Semua</option>
                    <option value="belum diajukan" {{ request('tesis1') == 'belum diajukan' ? 'selected' : '' }}>Belum diajukan</option>
                    <option value="diproses di TU Prodi" {{ request('tesis1') == 'diproses di TU Prodi' ? 'selected' : '' }}>Diproses di TU Prodi</option>
                    <option value="diproses di fakultas" {{ request('tesis1') == 'diproses di fakultas' ? 'selected' : '' }}>Diproses di fakultas</option>
                    <option value="terjadwal" {{ request('tesis1') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="lulus" {{ request('tesis1') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="tidak lulus" {{ request('tesis1') == 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="tesis2" style="color: #495057;">
                    <option value="">Semua</option>
                    <option value="belum diajukan" {{ request('tesis2') == 'belum diajukan' ? 'selected' : '' }}>Belum diajukan</option>
                    <option value="diproses di TU Prodi" {{ request('tesis2') == 'diproses di TU Prodi' ? 'selected' : '' }}>Diproses di TU Prodi</option>
                    <option value="diproses di fakultas" {{ request('tesis2') == 'diproses di fakultas' ? 'selected' : '' }}>Diproses di fakultas</option>
                    <option value="terjadwal" {{ request('tesis2') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="lulus" {{ request('tesis2') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="tidak lulus" {{ request('tesis2') == 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="tahap4" style="color: #495057;">
                    <option value="">Semua</option>
                    <option value="belum diajukan" {{ request('tahap4') == 'belum diajukan' ? 'selected' : '' }}>Belum diajukan</option>
                    <option value="diproses di TU Prodi" {{ request('tahap4') == 'diproses di TU Prodi' ? 'selected' : '' }}>Diproses di TU Prodi</option>
                    <option value="diproses di fakultas" {{ request('tahap4') == 'diproses di fakultas' ? 'selected' : '' }}>Diproses di fakultas</option>
                    <option value="terjadwal" {{ request('tahap4') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="lulus" {{ request('tahap4') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="tidak lulus" {{ request('tahap4') == 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
            </th>
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
                <td class="align-middle">{{ $item->nama_prodi ?? '—' }}</td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->tesis1 ?? 'belum diajukan') }}" role="button" onclick="showTahapForm('Tesis I', {{ $item->id_judul }})">
                        {{ ucfirst($item->tesis1 ?? 'belum diajukan') }}
                    </span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->tesis2 ?? 'belum diajukan') }}" role="button" onclick="showTahapForm('Tesis II', {{ $item->id_judul }})">
                        {{ ucfirst($item->tesis2 ?? 'belum diajukan') }}
                    </span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->tahap4 ?? 'belum diajukan') }}" role="button" onclick="showTahapForm('tahap IV', {{ $item->id_judul }})">
                        {{ ucfirst($item->tahap4 ?? 'belum diajukan') }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
    <div class="d-flex align-items-center mb-2 mb-md-0">
        <label class="mb-0 mr-2 text-muted" style="font-size: 0.875rem; white-space: nowrap;">Tampilkan</label>
        <select class="form-control form-control-sm column-search" name="per_page" style="width: auto; min-width: 70px; color: #495057;">
            @foreach([10, 25, 50, 100] as $n)
                <option value="{{ $n }}" {{ (int) $tracking->perPage() === $n ? 'selected' : '' }}>{{ $n }}</option>
            @endforeach
        </select>
        <span class="ml-2 text-muted" style="font-size: 0.875rem; white-space: nowrap;">data per halaman</span>
    </div>
    <div>
        {{ $tracking->links('pagination::bootstrap-4') }}
    </div>
</div>
