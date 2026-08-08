<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TFs;
use App\Services\MasterExcelService;
use App\Services\SpsiService;
use Illuminate\Http\Request;

class FakultasController extends Controller
{
    public function template()
    {
        return MasterExcelService::template('fakultas');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $result = MasterExcelService::import('fakultas', $request->file('file'), session('auth_user'));

        $message = 'Import selesai: ' . $result['inserted'] . ' data ditambahkan, ' . $result['skipped'] . ' dilewati.';
        if (!empty($result['errors'])) {
            $message .= ' Rincian: ' . implode(' | ', array_slice($result['errors'], 0, 10));
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'errors' => $result['errors']]);
        }

        return redirect()->route('master.fakultas.index')->with('success', $message);
    }

    public function index(Request $request)
    {
        $query = TFs::query();

        if ($s = $request->get('kode_fs')) {
            $query->where('KODE_FS', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('nama_fs')) {
            $query->where('NAMA_FS', 'like', '%' . $s . '%');
        }

        $fakultas = $query->orderBy('KODE_FS', 'asc')->paginate(10)->withQueryString()->through(function ($f) {
            return [
                'id' => $f->id,
                'kode' => $f->kode_fs,
                'nama' => $f->nama_fs,
            ];
        });

        $allFakultas = TFs::all()->map(function ($f) {
            return [
                'id' => $f->id,
                'kode' => $f->kode_fs,
                'nama' => $f->nama_fs,
            ];
        });

        return view('master.fakultas', compact('fakultas', 'allFakultas'));
    }

    public function create()
    {
        return view('master.fakultas-form', ['fakultas' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_fs' => 'required',
            'nama_fs' => 'required',
        ]);

        TFs::create([
            'KODE_FS' => $request->kode_fs,
            'NAMA_FS' => $request->nama_fs,
            'TGL_CREATE' => now(),
            'TGL_UPDATE' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.fakultas.index')->with('success', 'Fakultas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $f = TFs::find((int) $id);
        if (!$f) abort(404);
        $fakultas = [
            'id' => $f->id,
            'kode' => $f->kode_fs,
            'nama' => $f->nama_fs,
        ];
        return view('master.fakultas-form', compact('fakultas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_fs' => 'required',
            'nama_fs' => 'required',
        ]);

        $f = TFs::find((int) $id);
        if ($f) {
            $f->update([
                'KODE_FS' => $request->kode_fs,
                'NAMA_FS' => $request->nama_fs,
                'TGL_UPDATE' => now(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.fakultas.index')->with('success', 'Fakultas berhasil diperbarui.');
    }

    public function show($id)
    {
        $f = TFs::find((int) $id);
        if (!$f) abort(404);
        $fakultas = [
            'id' => $f->id,
            'kode' => $f->kode_fs,
            'nama' => $f->nama_fs,
        ];
        return view('master.fakultas-detail', compact('fakultas'));
    }

    public function destroy(Request $request, $id)
    {
        $f = TFs::find((int) $id);
        if ($f) {
            $f->delete();
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.fakultas.index')->with('success', 'Fakultas berhasil dihapus.');
    }

    public function syncSpsi(Request $request)
    {
        try {
            $items = SpsiService::fetch('mst_fakultas');
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        $inserted = 0;
        $updated = 0;

        foreach ($items as $item) {
            $kode = trim((string) ($item['kd_fak'] ?? ''));
            $nama = trim((string) ($item['nama_id'] ?? ''));

            if ($kode === '') {
                continue;
            }

            $fs = TFs::where('KODE_FS', $kode)->first();

            if ($fs) {
                $fs->update([
                    'NAMA_FS' => $nama,
                    'TGL_UPDATE' => now(),
                ]);
                $updated++;
            } else {
                TFs::create([
                    'KODE_FS' => $kode,
                    'NAMA_FS' => $nama,
                    'TGL_CREATE' => now(),
                    'TGL_UPDATE' => now(),
                ]);
                $inserted++;
            }
        }

        $message = 'Tarik data SPSI selesai: ' . $inserted . ' fakultas ditambahkan, ' . $updated . ' diperbarui.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return redirect()->route('master.fakultas.index')->with('success', $message);
    }
}
