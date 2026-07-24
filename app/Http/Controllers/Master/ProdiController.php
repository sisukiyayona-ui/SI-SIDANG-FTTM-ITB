<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TProdi;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index()
    {
        $prodi = TProdi::paginate(10);
        $prodi->getCollection()->transform(function($p) {
            return [
                'id' => $p->id,
                'kode' => $p->kode_prodi,
                'nama' => $p->nama_prodi,
                'status' => $p->status_aktif,
            ];
        });

        $allProdi = TProdi::all()->map(function($p) {
            return [
                'id' => $p->id,
                'kode' => $p->kode_prodi,
                'nama' => $p->nama_prodi,
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

        TProdi::create([
            'KODE_PRODI' => $request->kode_prodi,
            'NAMA_PRODI' => $request->nama_prodi,
            'STATUS_AKTIF' => $request->status_aktif,
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
}
