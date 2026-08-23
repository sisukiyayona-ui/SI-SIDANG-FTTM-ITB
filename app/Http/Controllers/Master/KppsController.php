<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TKpps;
use App\Models\TUser;
use App\Models\TProdi;
use App\Models\TFs;
use Illuminate\Http\Request;

class KppsController extends Controller
{
    public function index(Request $request)
    {
        $authUser = session('auth_user');
        $query = TKpps::query();

        if ($authUser['role'] === 'TU Prodi' && $authUser['kode_prodi']) {
            $query->where('KODE_PRODI', $authUser['kode_prodi']);
        }

        if ($searchNama = $request->get('nama')) {
            $query->where('NAMA', 'like', '%' . $searchNama . '%');
        }
        if ($searchNip = $request->get('nip')) {
            $query->where('NIP', 'like', '%' . $searchNip . '%');
        }
        if ($searchProdi = $request->get('kode_prodi')) {
            $query->where('KODE_PRODI', $searchProdi);
        }
        if ($searchStatus = $request->get('status_aktif')) {
            $query->where('STATUS_AKTIF', $searchStatus);
        }

        $kpps = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $prodis = TProdi::where('STATUS_AKTIF', 'AKTIF')->get();
        $fakultas = TFs::all();
        $users = TUser::where('STATUS_AKTIF', 'AKTIF')
            ->where('STATUS_PEGAWAI', 'Dosen')
            ->orderBy('NAMA_LENGKAP')
            ->get();

        return view('master.kpps', compact('kpps', 'prodis', 'fakultas', 'users'));
    }

    public function create()
    {
        $prodis = TProdi::where('STATUS_AKTIF', 'AKTIF')->get();
        $fakultas = TFs::all();
        $users = TUser::where('JENIS_USER', 'Pembimbing')->orWhere('JENIS_USER', 'Penguji')->get();

        return response()->json([
            'prodis' => $prodis,
            'fakultas' => $fakultas,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required',
            'nip'           => 'nullable',
            'kode_fs'       => 'nullable',
            'kode_prodi'    => 'nullable',
            'status_aktif'  => 'required',
            'status_tim'    => 'nullable|in:Ketua,Sekretaris,Anggota',
        ]);

        $this->applyTuProdiScope($request);

        $fs = null;
        if ($request->filled('kode_fs')) {
            $fs = TFs::where('KODE_FS', $request->kode_fs)->first();
        }

        $prodi = null;
        if ($request->filled('kode_prodi')) {
            $prodi = TProdi::where('KODE_PRODI', $request->kode_prodi)->first();
        }

        TKpps::create([
            'ID_USER'       => $request->id_user,
            'NIP'           => $request->nip,
            'NAMA'          => $request->nama,
            'STATUS_TIM'    => $request->status_tim,
            'KODE_PRODI'    => $prodi ? $prodi->KODE_PRODI : $request->kode_prodi,
            'NAMA_PRODI'    => $prodi ? $prodi->NAMA_PRODI : $request->nama_prodi,
            'KODE_FS'       => $fs ? $fs->KODE_FS : ($request->kode_fs ?? '164'),
            'NAMA_FS'       => $fs ? $fs->NAMA_FS : ($request->nama_fs ?? 'FTTM'),
            'STATUS_AKTIF'  => $request->status_aktif,
            'TGL_CREATE'    => now(),
            'TGL_UPDATE'    => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $kpps = TKpps::find((int) $id);
        if (!$kpps) {
            return response()->json(null, 404);
        }
        return response()->json($kpps);
    }

    public function edit($id)
    {
        $kpps = TKpps::find((int) $id);
        if (!$kpps) {
            return response()->json(null, 404);
        }

        return response()->json([
            'id'            => $kpps->id,
            'id_user'       => $kpps->ID_USER,
            'nip'           => $kpps->NIP,
            'nama'          => $kpps->NAMA,
            'status_tim'    => $kpps->STATUS_TIM,
            'kode_prodi'    => $kpps->KODE_PRODI,
            'nama_prodi'    => $kpps->NAMA_PRODI,
            'kode_fs'       => $kpps->KODE_FS,
            'nama_fs'       => $kpps->NAMA_FS,
            'status_aktif'  => $kpps->STATUS_AKTIF,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'          => 'required',
            'nip'           => 'nullable',
            'kode_fs'       => 'nullable',
            'kode_prodi'    => 'nullable',
            'status_aktif'  => 'required',
            'status_tim'    => 'nullable|in:Ketua,Sekretaris,Anggota',
        ]);

        $this->applyTuProdiScope($request);

        $kpps = TKpps::find((int) $id);
        if ($kpps) {
            $fs = null;
            if ($request->filled('kode_fs')) {
                $fs = TFs::where('KODE_FS', $request->kode_fs)->first();
            }

            $prodi = null;
            if ($request->filled('kode_prodi')) {
                $prodi = TProdi::where('KODE_PRODI', $request->kode_prodi)->first();
            }

            $kpps->update([
                'ID_USER'       => $request->id_user ?: null,
                'NIP'           => $request->nip,
                'NAMA'          => $request->nama,
                'STATUS_TIM'    => $request->status_tim,
                'KODE_PRODI'    => $prodi ? $prodi->KODE_PRODI : $request->kode_prodi,
                'NAMA_PRODI'    => $prodi ? $prodi->NAMA_PRODI : $request->nama_prodi,
                'KODE_FS'       => $fs ? $fs->KODE_FS : ($request->kode_fs ?? '164'),
                'NAMA_FS'       => $fs ? $fs->NAMA_FS : ($request->nama_fs ?? 'FTTM'),
                'STATUS_AKTIF'  => $request->status_aktif,
                'TGL_UPDATE'    => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function applyTuProdiScope(Request $request): void
    {
        $authUser = session('auth_user');

        if ($authUser && ($authUser['role'] ?? null) === 'TU Prodi') {
            $request->merge([
                'kode_prodi' => $authUser['kode_prodi'] ?? null,
                'nama_prodi' => $authUser['nama_prodi'] ?? null,
                'kode_fs'    => $authUser['kode_fs'] ?? null,
                'nama_fs'    => $authUser['nama_fs'] ?? null,
            ]);
        }
    }

    public function destroy($id)
    {
        $kpps = TKpps::find((int) $id);
        if ($kpps) {
            $kpps->delete();
        }
        return response()->json(['success' => true]);
    }
}
