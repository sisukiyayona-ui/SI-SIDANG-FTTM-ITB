<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TFs;
use App\Models\TProdi;
use App\Services\MasterExcelService;
use App\Services\SpsiService;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function template()
    {
        return MasterExcelService::template('prodi');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $result = MasterExcelService::import('prodi', $request->file('file'), session('auth_user'));

        $message = 'Import selesai: ' . $result['inserted'] . ' data ditambahkan, ' . $result['skipped'] . ' dilewati.';
        if (!empty($result['errors'])) {
            $message .= ' Rincian: ' . implode(' | ', array_slice($result['errors'], 0, 10));
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'errors' => $result['errors']]);
        }

        return redirect()->route('master.prodi.index')->with('success', $message);
    }

    public function index(Request $request)
    {
        $query = TProdi::query();

        if ($s = $request->get('kode_prodi')) {
            $query->where('KODE_PRODI', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('nama_prodi')) {
            $query->where('NAMA_PRODI', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('strata')) {
            $query->where('STRATA', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('status_aktif')) {
            $query->where('STATUS_AKTIF', $s);
        }

        $prodi = $query->orderBy('KODE_PRODI', 'asc')->paginate(10)->withQueryString()->through(function($p) {
            return [
                'id' => $p->id,
                'kode' => $p->kode_prodi,
                'nama' => $p->nama_prodi,
                'strata' => $p->strata ?? '',
                'status' => $p->status_aktif,
            ];
        });

        $allProdi = TProdi::all()->map(function($p) {
            return [
                'id' => $p->id,
                'kode' => $p->kode_prodi,
                'nama' => $p->nama_prodi,
                'strata' => $p->strata ?? '',
                'status' => $p->status_aktif,
            ];
        });

        return view('master.prodi', compact('prodi', 'allProdi'));
    }

    public function create()
    {
        return view('master.prodi-form', ['prodi' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_prodi' => 'required',
            'nama_prodi' => 'required',
            'status_aktif' => 'required',
        ]);

        $authUser = session('auth_user');

        TProdi::create([
            'KODE_PRODI' => $request->kode_prodi,
            'NAMA_PRODI' => $request->nama_prodi,
            'STATUS_AKTIF' => $request->status_aktif,
            'KODE_FS' => $authUser['kode_fs'] ?? '',
            'NAMA_FS' => $authUser['nama_fs'] ?? '',
            'TGL_CREATE' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.prodi.index')->with('success', 'Prodi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $p = TProdi::find((int) $id);
        if (!$p) abort(404);
        $prodi = [
            'id' => $p->id,
            'kode' => $p->kode_prodi,
            'nama' => $p->nama_prodi,
            'status' => $p->status_aktif,
        ];
        return view('master.prodi-form', compact('prodi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_prodi' => 'required',
            'nama_prodi' => 'required',
            'status_aktif' => 'required',
        ]);

        $p = TProdi::find((int) $id);
        if ($p) {
            $p->update([
                'KODE_PRODI' => $request->kode_prodi,
                'NAMA_PRODI' => $request->nama_prodi,
                'STATUS_AKTIF' => $request->status_aktif,
                'TGL_UPDATE' => now(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.prodi.index')->with('success', 'Prodi berhasil diperbarui.');
    }

    public function show($id)
    {
        $p = TProdi::find((int) $id);
        if (!$p) abort(404);
        $prodi = [
            'id' => $p->id,
            'kode' => $p->kode_prodi,
            'nama' => $p->nama_prodi,
            'status' => $p->status_aktif,
        ];
        return view('master.prodi-detail', compact('prodi'));
    }

    public function destroy(Request $request, $id)
    {
        $p = TProdi::find((int) $id);
        if ($p) {
            $p->delete();
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.prodi.index')->with('success', 'Prodi berhasil dihapus.');
    }

    public function syncSpsi(Request $request)
    {
        try {
            $items = SpsiService::fetch('mst_prodi');
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        $namaFakultas = TFs::pluck('NAMA_FS', 'KODE_FS');

        $inserted = 0;
        $updated = 0;

        foreach ($items as $item) {
            $kode = trim((string) ($item['no_ps'] ?? ''));
            $nama = trim((string) ($item['nama_id'] ?? ''));
            $kdFak = trim((string) ($item['kd_fak'] ?? ''));

            if ($kode === '' || $nama === '') {
                continue;
            }

            $namaFak = $namaFakultas[$kdFak] ?? $kdFak;
            $status = !empty($item['status_aktif_prodi']) ? 'AKTIF' : 'NON AKTIF';

            $prodi = TProdi::where('KODE_PRODI', $kode)->first();

            if ($prodi) {
                $prodi->update([
                    'NAMA_PRODI' => $nama,
                    'STATUS_AKTIF' => $status,
                    'KODE_FS' => $kdFak,
                    'NAMA_FS' => $namaFak,
                    'TGL_UPDATE' => now(),
                ]);
                $updated++;
            } else {
                TProdi::create([
                    'KODE_PRODI' => $kode,
                    'NAMA_PRODI' => $nama,
                    'STATUS_AKTIF' => $status,
                    'KODE_FS' => $kdFak,
                    'NAMA_FS' => $namaFak,
                    'TGL_CREATE' => now(),
                    'TGL_UPDATE' => now(),
                ]);
                $inserted++;
            }
        }

        $message = 'Tarik data SPSI selesai: ' . $inserted . ' prodi ditambahkan, ' . $updated . ' diperbarui.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return redirect()->route('master.prodi.index')->with('success', $message);
    }
}
