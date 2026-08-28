@php
    $tahapanLabels = [
        'tahap 1' => 'Ujian Kualifikasi',
        'tahap I' => 'Ujian Kualifikasi',
        'tahap 2' => 'Ujian Proposal',
        'tahap II' => 'Ujian Proposal',
        'tahap 3' => 'Tahap III',
        'tahap III' => 'Tahap III',
        'tahap 4' => 'Sidang Terbuka / Tertutup',
        'tahap IV' => 'Sidang Terbuka / Tertutup',
        'SK I' => 'SK I',
        'SK II' => 'SK II',
        'SK III' => 'SK III',
        'SK IV' => 'SK IV',
    ];
@endphp
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Nama Persyaratan</th>
            <th>Tahapan Sidang</th>
            <th>Strata</th>
            <th>Program Studi</th>
            <th>Status</th>
        </tr>
        <tr>
            <th></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama_persyaratan" placeholder="Cari..." value="{{ request('nama_persyaratan') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="tahapan_sidang" placeholder="Cari..." value="{{ request('tahapan_sidang') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="strata" placeholder="Cari..." value="{{ request('strata') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama_prodi" placeholder="Cari..." value="{{ request('nama_prodi') }}"></th>
            <th>
                <select class="form-control form-control-sm column-search" name="status_aktif">
                    <option value="">Semua</option>
                    <option value="AKTIF" {{ request('status_aktif') == 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                    <option value="NON AKTIF" {{ request('status_aktif') == 'NON AKTIF' ? 'selected' : '' }}>NON AKTIF</option>
                </select>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($persyaratan as $i => $item)
            <tr>
                <td>{{ $persyaratan->firstItem() + $i }}</td>
                <td><a href="#" onclick="event.preventDefault(); openEdit({{ $item['id'] }})" class="text-decoration-none">{{ $item['nama'] }}</a></td>
                <td>{{ $tahapanLabels[$item['tahapan_sidang']] ?? $item['tahapan_sidang'] }}</td>
                <td>{{ $item['strata'] }}</td>
                <td>{{ $item['kode_prodi'] }} - {{ $item['nama_prodi'] }}</td>
                <td>
                    <span class="badge bg-{{ $item['status_aktif'] === 'AKTIF' ? 'success' : 'danger' }}">
                        {{ $item['status_aktif'] }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-3 d-flex justify-content-end">
    {{ $persyaratan->links() }}
</div>
