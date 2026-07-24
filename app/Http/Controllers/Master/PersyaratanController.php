<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TSyaratSidang;
use App\Models\TProdi;
use App\Models\TTahapan;
use Illuminate\Http\Request;

class PersyaratanController extends Controller
{
    public function create()
    {
        $prodis = TProdi::where('status_aktif', 'AKTIF')->get();
        $tahapans = TTahapan::all();
        return view('master.persyaratan-form', compact('prodis', 'tahapans'))->with('persyaratan', null);
    }

    public function edit($id)
    {
        $item = TSyaratSidang::find((int) $id);
        if (!$item) {
            return redirect()->route('master.persyaratan.index')->with('error', 'Data tidak ditemukan.');
        }

        if (request()->wantsJson()) {
            return response()->json([
                'id' => $item->id,
                'nama' => $item->nama_persyaratan,
                'id_prodi' => $item->id_prodi,
                'tahapan_sidang' => $item->tahapan_sidang,
                'strata' => $item->strata,
                'status_aktif' => $item->status_aktif,
            ]);
        }

        $persyaratan = [
            'id' => $item->id,
            'nama' => $item->nama_persyaratan,
            'id_prodi' => $item->id_prodi,
            'tahapan_sidang' => $item->tahapan_sidang,
            'strata' => $item->strata,
            'status_aktif' => $item->status_aktif,
        ];

        $prodis = TProdi::where('status_aktif', 'AKTIF')->get();
        $tahapans = TTahapan::all();
        return view('master.persyaratan-form', compact('persyaratan', 'prodis', 'tahapans'));
    }

    public function index()
    {
        $user = session('auth_user');
        $query = TSyaratSidang::query();

        if ($user['role'] === 'TU Prodi') {
            $query->where('kode_prodi', $user['kode_prodi']);
        }

        $persyaratan = $query->orderBy('id', 'desc')->paginate(10)->through(function($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama_persyaratan,
                'keterangan' => 'Tahapan: ' . $item->tahapan_sidang . ' (' . $item->strata . ') - ' . $item->nama_prodi,
                'wajib' => $item->status_aktif === 'AKTIF',
                'id_prodi' => $item->id_prodi,
                'kode_prodi' => $item->kode_prodi,
                'nama_prodi' => $item->nama_prodi,
                'tahapan_sidang' => $item->tahapan_sidang,
                'strata' => $item->strata,
                'status_aktif' => $item->status_aktif,
            ];
        });

        $prodis = TProdi::where('status_aktif', 'AKTIF')->get();
        $tahapans = TTahapan::all();

        return view('master.persyaratan', compact('persyaratan', 'prodis', 'tahapans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_persyaratan' => 'required',
            'tahapan_sidang' => 'required',
            'strata' => 'required',
            'status_aktif' => 'required',
        ]);

        $user = session('auth_user');

        if ($user['role'] === 'TU Prodi') {
            $prodiId = TProdi::where('kode_prodi', $user['kode_prodi'])->value('id');
            $kodeProdi = $user['kode_prodi'];
            $namaProdi = $user['nama_prodi'];
        } else {
            $prodi = TProdi::find($request->id_prodi);
            $prodiId = $prodi->id;
            $kodeProdi = $prodi->kode_prodi;
            $namaProdi = $prodi->nama_prodi;
        }

        TSyaratSidang::create([
            'NAMA_PERSYARATAN' => $request->nama_persyaratan,
            'ID_PRODI' => $prodiId,
            'KODE_PRODI' => $kodeProdi,
            'NAMA_PRODI' => $namaProdi,
            'TAHAPAN_SIDANG' => $request->tahapan_sidang,
            'STRATA' => $request->strata,
            'STATUS_AKTIF' => $request->status_aktif,
            'TGL_CREATE' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.persyaratan.index')->with('success', 'Data persyaratan berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_persyaratan' => 'required',
            'tahapan_sidang' => 'required',
            'strata' => 'required',
            'status_aktif' => 'required',
        ]);

        $item = TSyaratSidang::find((int) $id);
        if ($item) {
            $user = session('auth_user');

            if ($user['role'] === 'TU Prodi') {
                $prodiId = TProdi::where('kode_prodi', $user['kode_prodi'])->value('id');
                $kodeProdi = $user['kode_prodi'];
                $namaProdi = $user['nama_prodi'];
            } else {
                $prodi = TProdi::find($request->id_prodi);
                $prodiId = $prodi->id;
                $kodeProdi = $prodi->kode_prodi;
                $namaProdi = $prodi->nama_prodi;
            }

            $item->update([
                'NAMA_PERSYARATAN' => $request->nama_persyaratan,
                'ID_PRODI' => $prodiId,
                'KODE_PRODI' => $kodeProdi,
                'NAMA_PRODI' => $namaProdi,
                'TAHAPAN_SIDANG' => $request->tahapan_sidang,
                'STRATA' => $request->strata,
                'STATUS_AKTIF' => $request->status_aktif,
                'TGL_UPDATE' => now(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.persyaratan.index')->with('success', 'Data persyaratan berhasil disimpan.');
    }

    public function destroy($id)
    {
        $item = TSyaratSidang::find((int) $id);
        if ($item) {
            $item->delete();
        }
        return response()->json(['success' => true]);
    }
}

