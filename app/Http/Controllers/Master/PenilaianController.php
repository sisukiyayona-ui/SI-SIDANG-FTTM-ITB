<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TPointPenilaian;
use App\Models\TProdi;
use App\Models\TTahapan;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index()
    {
        $user = session('auth_user');
        $query = TPointPenilaian::query();

        if ($user['role'] === 'TU Prodi') {
            $query->where('kode_prodi', $user['kode_prodi']);
        }

        $penilaianRaw = $query->get();

        $penilaian = $penilaianRaw->map(function($item) {
            return [
                'id' => $item->id,
                'nama' => $item->Penilaian,
                'keterangan' => 'Tahapan: ' . $item->tahapan_sidang . ' (' . $item->strata . ') - ' . $item->nama_prodi,
                'status' => $item->status_aktif === 'AKTIF' ? 'Aktif' : 'Nonaktif',
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

        return view('master.penilaian', compact('penilaian', 'prodis', 'tahapans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
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

        TPointPenilaian::create([
            'Penilaian' => $request->nama,
            'id_prodi' => $prodiId,
            'kode_prodi' => $kodeProdi,
            'nama_prodi' => $namaProdi,
            'tahapan_sidang' => $request->tahapan_sidang,
            'strata' => $request->strata,
            'status_aktif' => $request->status_aktif,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'tahapan_sidang' => 'required',
            'strata' => 'required',
            'status_aktif' => 'required',
        ]);

        $item = TPointPenilaian::find((int) $id);
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
                'Penilaian' => $request->nama,
                'id_prodi' => $prodiId,
                'kode_prodi' => $kodeProdi,
                'nama_prodi' => $namaProdi,
                'tahapan_sidang' => $request->tahapan_sidang,
                'strata' => $request->strata,
                'status_aktif' => $request->status_aktif,
                'tgl_update' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $item = TPointPenilaian::find((int) $id);
        if ($item) {
            $item->delete();
        }
        return response()->json(['success' => true]);
    }
}

