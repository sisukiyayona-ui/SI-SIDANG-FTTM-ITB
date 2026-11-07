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

function s2StatusOptions($name) {
    $options = [
        'belum diajukan' => 'Belum diajukan',
        'diproses di TU Prodi' => 'Diproses di TU Prodi',
        'diproses di fakultas' => 'Diproses di fakultas',
        'terjadwal' => 'Terjadwal',
        'rejected' => 'Ditolak',
        'lulus' => 'Lulus',
        'tidak lulus' => 'Tidak Lulus',
    ];
    echo '<option value="">Semua</option>';
    foreach ($options as $value => $label) {
        $selected = request($name) == $value ? 'selected' : '';
        echo '<option value="' . e($value) . '" ' . $selected . '>' . e($label) . '</option>';
    }
}
@endphp
<table class="table table-bordered table-hover text-center" style="table-layout: fixed;">
    <colgroup>
        <col style="width: 60px;">
        <col style="width: 140px;">
        <col style="width: 160px;">
        <col style="width: 320px;">
        <col style="width: 160px;">
        <col style="width: 150px;">
        <col style="width: 150px;">
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
        </tr>
        <tr class="tracking-filter-row" style="background-color: #f8f9fa;">
            <th></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nim" placeholder="Cari..." value="{{ request('nim') }}" style="color: #495057;"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama" placeholder="Cari..." value="{{ request('nama') }}" style="color: #495057;"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="judul" placeholder="Cari..." value="{{ request('judul') }}" style="color: #495057;"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama_prodi" placeholder="Cari Prodi..." value="{{ request('nama_prodi') }}" style="color: #495057;"></th>
            <th>
                <select class="form-control form-control-sm column-search" name="tesis1" style="color: #495057;">
                    @php s2StatusOptions('tesis1'); @endphp
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="tesis2" style="color: #495057;">
                    @php s2StatusOptions('tesis2'); @endphp
                </select>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($tracking as $item)
            <tr>
                <td class="align-middle">{{ $tracking->firstItem() + $loop->index }}</td>
                <td class="align-middle">{{ $item->Nim }}</td>
                <td class="align-middle">{{ $item->nama_mhs }}</td>
                <td class="text-left text-muted">
                    @if(session('auth_user.role') === 'TU Prodi')
                        <a href="{{ route('sidang.s2.ubah-judul', $item->id_judul) }}" class="text-decoration-none text-primary">{{ $item->Judul }}</a>
                    @else
                        <a href="#" class="text-decoration-none">{{ $item->Judul }}</a>
                    @endif
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
