<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TProdi;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index()
    {
        $prodi = TProdi::all()->map(function($p) {
            return [
                'id' => $p->id,
                'kode' => $p->kode_prodi,
                'nama' => $p->nama_prodi,
                'status' => $p->status_aktif === 'AKTIF' ? 'Aktif' : 'Nonaktif',
            ];
        });
        return view('master.prodi', compact('prodi'));
    }

    public function create()
    {
        return view('master.prodi-form', ['prodi' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required',
            'nama' => 'required',
            'status' => 'required',
        ]);

        TProdi::create([
            'kode_prodi' => $request->kode,
            'nama_prodi' => $request->nama,
            'status_aktif' => strtoupper($request->status),
        ]);

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
            'status' => $p->status_aktif === 'AKTIF' ? 'Aktif' : 'Nonaktif',
        ];
        return view('master.prodi-form', compact('prodi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required',
            'nama' => 'required',
            'status' => 'required',
        ]);

        $p = TProdi::find((int) $id);
        if ($p) {
            $p->update([
                'kode_prodi' => $request->kode,
                'nama_prodi' => $request->nama,
                'status_aktif' => strtoupper($request->status),
                'tgl_update' => now(),
            ]);
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
            'status' => $p->status_aktif === 'AKTIF' ? 'Aktif' : 'Nonaktif',
        ];
        return view('master.prodi-detail', compact('prodi'));
    }

    public function destroy($id)
    {
        $p = TProdi::find((int) $id);
        if ($p) {
            $p->delete();
        }
        return redirect()->route('master.prodi.index')->with('success', 'Prodi berhasil dihapus.');
    }
}
