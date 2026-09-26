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

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        // File hanya dibaca dari temp PHP lalu dibuang otomatis — tidak disimpan ke folder Laravel.
        $parsed = MasterExcelService::parse('fakultas', $request->file('file'));

        $seen = [];
        $rows = [];
        foreach ($parsed as $p) {
            $kode = trim((string) ($p['values'][0] ?? ''));
            $nama = trim((string) ($p['values'][1] ?? ''));

            $existsInDb = $kode !== '' && TFs::where('KODE_FS', $kode)->exists();
            $dupInFile = $kode !== '' && isset($seen[$kode]);
            if ($kode !== '') {
                $seen[$kode] = true;
            }

            $rows[] = [
                'row' => $p['row'],
                'kode' => $kode,
                'nama' => $nama,
                'exists' => $existsInDb,
                'dup_in_file' => $dupInFile,
            ];
        }

        return response()->json(['success' => true, 'rows' => $rows]);
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'rows' => 'required|array',
        ]);

        $results = [];
        $inserted = 0;
        $failed = 0;

        foreach ($request->input('rows') as $r) {
            $kode = trim((string) ($r['kode'] ?? ''));
            $nama = trim((string) ($r['nama'] ?? ''));

            if ($kode === '' || $nama === '') {
                $results[] = ['kode' => $kode, 'nama' => $nama, 'status' => 'failed', 'message' => 'Kode dan Nama wajib diisi'];
                $failed++;
                continue;
            }

            if (TFs::where('KODE_FS', $kode)->exists()) {
                $results[] = ['kode' => $kode, 'nama' => $nama, 'status' => 'failed', 'message' => 'Kode fakultas ' . $kode . ' sudah terdaftar (aktif)'];
                $failed++;
                continue;
            }

            TFs::create([
                'KODE_FS' => $kode,
                'NAMA_FS' => $nama,
                'TGL_CREATE' => now(),
                'TGL_UPDATE' => now(),
            ]);

            $results[] = ['kode' => $kode, 'nama' => $nama, 'status' => 'success', 'message' => 'Berhasil ditambahkan'];
            $inserted++;
        }

        return response()->json([
            'success' => true,
            'inserted' => $inserted,
            'failed' => $failed,
            'results' => $results,
        ]);
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

        if ($request->ajax()) {
            $tableHtml = view('master._fakultas_table', compact('fakultas'))->render();
            return response()->json(['html' => $tableHtml]);
        }

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
