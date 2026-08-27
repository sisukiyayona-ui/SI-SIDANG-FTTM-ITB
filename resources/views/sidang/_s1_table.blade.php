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
            return 'purple';
        case 'terjadwal':
            return 'primary';
        case 'dalam proses':
            return 'warning';
        case 'lulus':
            return 'success';
        case 'tidak lulus':
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
        <col style="width: 125px;">
        <col style="width: 125px;">
        <col style="width: 125px;">
        <col style="width: 125px;">
        <col style="width: 125px;">
        <col style="width: 125px;">
        <col style="width: 125px;">
    </colgroup>
    <thead style="background-color: #6998d3; color: white;">
        <tr>
            <th rowspan="2" class="align-middle" style="width: 50px;">No</th>
            <th rowspan="2" class="align-middle">NIM</th>
            <th rowspan="2" class="align-middle">Nama</th>
            <th rowspan="2" class="align-middle">Judul</th>
            <th rowspan="2" class="align-middle">Ujian<br>Kualifikasi</th>
            <th rowspan="2" class="align-middle">Ujian<br>Proposal</th>
            <th colspan="4" class="align-middle">Tahap III</th>
            <th rowspan="2" class="align-middle">Sidang<br>Terbuka /<br>Tertutup</th>
        </tr>
        <tr>
            <th class="align-middle" style="background-color: #9fbce4; color: white;">SK I</th>
            <th class="align-middle" style="background-color: #9fbce4; color: white;">SK II</th>
            <th class="align-middle" style="background-color: #9fbce4; color: white;">SK III</th>
            <th class="align-middle" style="background-color: #9fbce4; color: white;">SK IV</th>
        </tr>
        <tr class="tracking-filter-row" style="background-color: #f8f9fa;">
            <th></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nim" placeholder="Cari..." value="{{ request('nim') }}" style="color: #495057;"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama" placeholder="Cari..." value="{{ request('nama') }}" style="color: #495057;"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="judul" placeholder="Cari..." value="{{ request('judul') }}" style="color: #495057;"></th>
            <th>
                <select class="form-control form-control-sm column-search" name="tahap1" style="color: #495057;">
                    <option value="">Semua</option>
                    <option value="belum diajukan" {{ request('tahap1') == 'belum diajukan' ? 'selected' : '' }}>Belum diajukan</option>
                    <option value="diproses di TU Prodi" {{ request('tahap1') == 'diproses di TU Prodi' ? 'selected' : '' }}>Diproses di TU Prodi</option>
                    <option value="diproses di fakultas" {{ request('tahap1') == 'diproses di fakultas' ? 'selected' : '' }}>Diproses di fakultas</option>
                    <option value="menunggu pelaksanaan sidang" {{ request('tahap1') == 'menunggu pelaksanaan sidang' ? 'selected' : '' }}>Menunggu Pelaksanaan Sidang</option>
                    <option value="terjadwal" {{ request('tahap1') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="lulus" {{ request('tahap1') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="tidak lulus" {{ request('tahap1') == 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="tahap2" style="color: #495057;">
                    <option value="">Semua</option>
                    <option value="belum diajukan" {{ request('tahap2') == 'belum diajukan' ? 'selected' : '' }}>Belum diajukan</option>
                    <option value="diproses di TU Prodi" {{ request('tahap2') == 'diproses di TU Prodi' ? 'selected' : '' }}>Diproses di TU Prodi</option>
                    <option value="diproses di fakultas" {{ request('tahap2') == 'diproses di fakultas' ? 'selected' : '' }}>Diproses di fakultas</option>
                    <option value="menunggu pelaksanaan sidang" {{ request('tahap2') == 'menunggu pelaksanaan sidang' ? 'selected' : '' }}>Menunggu Pelaksanaan Sidang</option>
                    <option value="terjadwal" {{ request('tahap2') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="lulus" {{ request('tahap2') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="tidak lulus" {{ request('tahap2') == 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="sk1" style="color: #495057;">
                    <option value="">Semua</option>
                    <option value="belum diajukan" {{ request('sk1') == 'belum diajukan' ? 'selected' : '' }}>Belum diajukan</option>
                    <option value="diproses di TU Prodi" {{ request('sk1') == 'diproses di TU Prodi' ? 'selected' : '' }}>Diproses di TU Prodi</option>
                    <option value="diproses di fakultas" {{ request('sk1') == 'diproses di fakultas' ? 'selected' : '' }}>Diproses di fakultas</option>
                    <option value="menunggu pelaksanaan sidang" {{ request('sk1') == 'menunggu pelaksanaan sidang' ? 'selected' : '' }}>Menunggu Pelaksanaan Sidang</option>
                    <option value="terjadwal" {{ request('sk1') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="lulus" {{ request('sk1') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="tidak lulus" {{ request('sk1') == 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="sk2" style="color: #495057;">
                    <option value="">Semua</option>
                    <option value="belum diajukan" {{ request('sk2') == 'belum diajukan' ? 'selected' : '' }}>Belum diajukan</option>
                    <option value="diproses di TU Prodi" {{ request('sk2') == 'diproses di TU Prodi' ? 'selected' : '' }}>Diproses di TU Prodi</option>
                    <option value="diproses di fakultas" {{ request('sk2') == 'diproses di fakultas' ? 'selected' : '' }}>Diproses di fakultas</option>
                    <option value="menunggu pelaksanaan sidang" {{ request('sk2') == 'menunggu pelaksanaan sidang' ? 'selected' : '' }}>Menunggu Pelaksanaan Sidang</option>
                    <option value="terjadwal" {{ request('sk2') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="lulus" {{ request('sk2') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="tidak lulus" {{ request('sk2') == 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="sk3" style="color: #495057;">
                    <option value="">Semua</option>
                    <option value="belum diajukan" {{ request('sk3') == 'belum diajukan' ? 'selected' : '' }}>Belum diajukan</option>
                    <option value="diproses di TU Prodi" {{ request('sk3') == 'diproses di TU Prodi' ? 'selected' : '' }}>Diproses di TU Prodi</option>
                    <option value="diproses di fakultas" {{ request('sk3') == 'diproses di fakultas' ? 'selected' : '' }}>Diproses di fakultas</option>
                    <option value="menunggu pelaksanaan sidang" {{ request('sk3') == 'menunggu pelaksanaan sidang' ? 'selected' : '' }}>Menunggu Pelaksanaan Sidang</option>
                    <option value="terjadwal" {{ request('sk3') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="lulus" {{ request('sk3') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="tidak lulus" {{ request('sk3') == 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="sk4" style="color: #495057;">
                    <option value="">Semua</option>
                    <option value="belum diajukan" {{ request('sk4') == 'belum diajukan' ? 'selected' : '' }}>Belum diajukan</option>
                    <option value="diproses di TU Prodi" {{ request('sk4') == 'diproses di TU Prodi' ? 'selected' : '' }}>Diproses di TU Prodi</option>
                    <option value="diproses di fakultas" {{ request('sk4') == 'diproses di fakultas' ? 'selected' : '' }}>Diproses di fakultas</option>
                    <option value="menunggu pelaksanaan sidang" {{ request('sk4') == 'menunggu pelaksanaan sidang' ? 'selected' : '' }}>Menunggu Pelaksanaan Sidang</option>
                    <option value="terjadwal" {{ request('sk4') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="lulus" {{ request('sk4') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="tidak lulus" {{ request('sk4') == 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="tahap4" style="color: #495057;">
                    <option value="">Semua</option>
                    <option value="belum diajukan" {{ request('tahap4') == 'belum diajukan' ? 'selected' : '' }}>Belum diajukan</option>
                    <option value="diproses di TU Prodi" {{ request('tahap4') == 'diproses di TU Prodi' ? 'selected' : '' }}>Diproses di TU Prodi</option>
                    <option value="diproses di fakultas" {{ request('tahap4') == 'diproses di fakultas' ? 'selected' : '' }}>Diproses di fakultas</option>
                    <option value="menunggu pelaksanaan sidang" {{ request('tahap4') == 'menunggu pelaksanaan sidang' ? 'selected' : '' }}>Menunggu Pelaksanaan Sidang</option>
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
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->tahap1) }}" role="button" onclick="showTahapForm('tahap I', {{ $item->id_judul }})">
                        {{ ucfirst($item->tahap1) }}
                    </span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->tahap2) }}" role="button" onclick="showTahapForm('tahap II', {{ $item->id_judul }})">
                        {{ ucfirst($item->tahap2) }}
                    </span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->sk1) }}" role="button" onclick="showTahapForm('SK I', {{ $item->id_judul }})">
                        {{ ucfirst($item->sk1) }}
                    </span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->sk2) }}" role="button" onclick="showTahapForm('SK II', {{ $item->id_judul }})">
                        {{ ucfirst($item->sk2) }}
                    </span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->sk3) }}" role="button" onclick="showTahapForm('SK III', {{ $item->id_judul }})">
                        {{ ucfirst($item->sk3) }}
                    </span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->sk4) }}" role="button" onclick="showTahapForm('SK IV', {{ $item->id_judul }})">
                        {{ ucfirst($item->sk4) }}
                    </span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->tahap4) }}" role="button" onclick="showTahapForm('tahap IV', {{ $item->id_judul }})">
                        {{ ucfirst($item->tahap4) }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-3">
    {{ $tracking->links('pagination::bootstrap-4') }}
</div>
