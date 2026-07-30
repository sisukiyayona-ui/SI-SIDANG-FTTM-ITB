<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TPointPenilaian;
use App\Models\TProdi;
use App\Models\TTahapan;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function create()
    {
        $prodis = TProdi::where('status_aktif', 'AKTIF')->get();
        $tahapans = TTahapan::all();
        return view('master.penilaian-form', compact('prodis', 'tahapans'))->with('penilaian', null);
    }

    public function edit($id)
    {
        $item = TPointPenilaian::find((int) $id);
        if (!$item) {
            return redirect()->route('master.penilaian.index')->with('error', 'Data tidak ditemukan.');
        }

        if (request()->wantsJson()) {
            return response()->json([
                'id' => $item->id,
                'nama' => $item->penilaian ?? $item->PENILAIAN,
                'id_prodi' => $item->id_prodi,
                'tahapan_sidang' => $item->tahapan_sidang,
                'strata' => $item->strata,
                'status_aktif' => $item->status_aktif,
                'no_form' => $item->no_form,
                'status_catatan' => $item->status_catatan,
                'Keterangan' => $item->keterangan ?? $item->KETERANGAN,
            ]);
        }

        $penilaian = [
            'id' => $item->id,
            'nama' => $item->penilaian ?? $item->PENILAIAN,
            'id_prodi' => $item->id_prodi,
            'tahapan_sidang' => $item->tahapan_sidang,
            'strata' => $item->strata,
            'status_aktif' => $item->status_aktif,
            'no_form' => $item->no_form,
            'status_catatan' => $item->status_catatan,
            'Keterangan' => $item->keterangan ?? $item->KETERANGAN,
        ];

        $prodis = TProdi::where('status_aktif', 'AKTIF')->get();
        $tahapans = TTahapan::all();
        return view('master.penilaian-form', compact('penilaian', 'prodis', 'tahapans'));
    }

    public function index(Request $request)
    {
        $user = session('auth_user');
        $query = TPointPenilaian::query();

        if ($user['role'] === 'TU Prodi') {
            $query->where('kode_prodi', $user['kode_prodi']);
        }

        if ($s = $request->get('penilaian')) {
            $query->where('PENILAIAN', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('no_form')) {
            $query->where('NO_FORM', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('tahapan_sidang')) {
            $query->where('TAHAPAN_SIDANG', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('strata')) {
            $query->where('STRATA', $s);
        }
        if ($s = $request->get('nama_prodi')) {
            $query->where('NAMA_PRODI', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('Keterangan')) {
            $query->where('KETERANGAN', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('status_aktif')) {
            $query->where('STATUS_AKTIF', $s);
        }

        $penilaian = $query->orderBy('id', 'desc')->paginate(10)->withQueryString()->through(function($item) {
            return [
                'id' => $item->id,
                'nama' => $item->penilaian ?? $item->PENILAIAN,
                'keterangan' => 'Tahapan: ' . $item->tahapan_sidang . ' (' . $item->strata . ') - ' . $item->nama_prodi,
                'status' => $item->status_aktif === 'AKTIF' ? 'Aktif' : 'Nonaktif',
                'id_prodi' => $item->id_prodi,
                'kode_prodi' => $item->kode_prodi,
                'nama_prodi' => $item->nama_prodi,
                'tahapan_sidang' => $item->tahapan_sidang,
                'strata' => $item->strata,
                'status_aktif' => $item->status_aktif,
                'no_form' => $item->no_form,
                'status_catatan' => $item->status_catatan,
                'Keterangan' => $item->keterangan ?? $item->KETERANGAN,
            ];
        });

        $prodis = TProdi::where('status_aktif', 'AKTIF')->get();
        $tahapans = TTahapan::all();

        return view('master.penilaian', compact('penilaian', 'prodis', 'tahapans'));
    }

    public function store(Request $request)
    {
        // Debug: log request data
        \Log::info('Penilaian store request data:', $request->all());
        
        $request->validate([
            'penilaian' => 'required',
            'tahapan_sidang' => 'required',
            'strata' => 'required',
            'status_aktif' => 'required',
        ]);

        $user = session('auth_user');
        \Log::info('User session:', $user);

        if ($user['role'] === 'TU Prodi') {
            $prodiId = TProdi::where('kode_prodi', $user['kode_prodi'])->value('id');
            $kodeProdi = $user['kode_prodi'];
            $namaProdi = $user['nama_prodi'];
        } else {
            $prodi = TProdi::find($request->id_prodi);
            if (!$prodi) {
                \Log::error('Prodi not found for id:', ['id_prodi' => $request->id_prodi]);
                return response()->json(['error' => 'Prodi tidak ditemukan'], 422);
            }
            $prodiId = $prodi->id;
            $kodeProdi = $prodi->kode_prodi;
            $namaProdi = $prodi->nama_prodi;
        }

        TPointPenilaian::create([
            'PENILAIAN' => $request->penilaian,
            'ID_PRODI' => $prodiId,
            'KODE_PRODI' => $kodeProdi,
            'NAMA_PRODI' => $namaProdi,
            'TAHAPAN_SIDANG' => $request->tahapan_sidang,
            'STRATA' => $request->strata,
            'STATUS_AKTIF' => $request->status_aktif,
            'NO_FORM' => $request->no_form,
            'STATUS_CATATAN' => $request->status_catatan,
            'KETERANGAN' => $request->Keterangan,
            'TGL_CREATE' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.penilaian.index')->with('success', 'Data komponen berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'penilaian' => 'required',
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
                'PENILAIAN' => $request->penilaian,
                'ID_PRODI' => $prodiId,
                'KODE_PRODI' => $kodeProdi,
                'NAMA_PRODI' => $namaProdi,
                'TAHAPAN_SIDANG' => $request->tahapan_sidang,
                'STRATA' => $request->strata,
                'STATUS_AKTIF' => $request->status_aktif,
                'NO_FORM' => $request->no_form,
                'STATUS_CATATAN' => $request->status_catatan,
                'KETERANGAN' => $request->Keterangan,
                'TGL_UPDATE' => now(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.penilaian.index')->with('success', 'Data komponen berhasil disimpan.');
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

