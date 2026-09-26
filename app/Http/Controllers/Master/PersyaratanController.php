<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TSyaratSidang;
use App\Models\TProdi;
use App\Models\TTahapan;
use App\Services\MasterExcelService;
use Illuminate\Http\Request;

class PersyaratanController extends Controller
{
    public function template()
    {
        return MasterExcelService::template('persyaratan');
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        // File hanya dibaca dari temp PHP lalu dibuang otomatis — tidak disimpan ke folder Laravel.
        $rows = MasterExcelService::previewItems('persyaratan', $request->file('file'), session('auth_user') ?? []);

        return response()->json([
            'success' => true,
            'rows' => $rows,
            'tahapan_options' => MasterExcelService::tahapanOptions(),
            'strata_options' => MasterExcelService::VALID_STRATA,
        ]);
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'rows' => 'required|array',
        ]);

        $result = MasterExcelService::storeItems('persyaratan', $request->input('rows'), session('auth_user') ?? []);

        return response()->json(['success' => true] + $result);
    }

    public function create()
    {
        [$prodis, $userProdiId] = $this->prodiFormContext();
        $tahapans = TTahapan::all();
        return view('master.persyaratan-form', compact('prodis', 'tahapans', 'userProdiId'))->with('persyaratan', null);
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

        [$prodis, $userProdiId] = $this->prodiFormContext();
        $tahapans = TTahapan::all();
        return view('master.persyaratan-form', compact('persyaratan', 'prodis', 'tahapans', 'userProdiId'));
    }

    public function index(Request $request)
    {
        $user = session('auth_user');
        $query = TSyaratSidang::query();

        if ($user['role'] === 'TU Prodi') {
            $query->whereIn('ID_PRODI', $user['id_prodi'] ?? []);
        }

        if ($s = $request->get('nama_persyaratan')) {
            $query->where('NAMA_PERSYARATAN', 'like', '%' . $s . '%');
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
        if ($s = $request->get('status_aktif')) {
            $query->where('STATUS_AKTIF', $s);
        }

        $persyaratan = $query->orderBy('id', 'desc')->paginate(10)->withQueryString()->through(function($item) {
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

        [$prodis, $userProdiId] = $this->prodiFormContext();
        $tahapans = TTahapan::all();

        if ($request->ajax()) {
            $tableHtml = view('master._persyaratan_table', compact('persyaratan'))->render();
            return response()->json(['html' => $tableHtml]);
        }

        return view('master.persyaratan', compact('persyaratan', 'prodis', 'tahapans', 'userProdiId'));
    }

    private function prodiFormContext(): array
    {
        $user = session('auth_user');
        $userProdiId = null;

        $query = TProdi::where('status_aktif', 'AKTIF');

        // TU Prodi: hanya prodi yang di-assign dari t_user_prodi
        if ($user['role'] === 'TU Prodi') {
            $query->whereIn('id', $user['id_prodi'] ?? []);
        } elseif (!empty($user['kode_fs'])) {
            // Selain TU Prodi: sesuai fakultas dari akun yang login
            $query->where('kode_fs', $user['kode_fs']);
        }

        $prodis = $query->get();
        $userProdiId = $prodis->first()?->id;

        return [$prodis, $userProdiId];
    }

    private function resolveProdiFromSession(): array
    {
        $user = session('auth_user');

        // Use submitted id_prodi if available (admin can choose), otherwise from session
        $prodiId = request('id_prodi');
        if ($prodiId) {
            $prodi = TProdi::find((int) $prodiId);
            if ($prodi) {
                return [$prodi->id, $prodi->kode_prodi, $prodi->nama_prodi];
            }
        }

        // TU Prodi: prodi yang di-assign dari t_user_prodi saja
        if (($user['role'] ?? '') === 'TU Prodi') {
            $prodi = TProdi::whereIn('id', $user['id_prodi'] ?? [])->first();
            if ($prodi) {
                return [$prodi->id, $prodi->kode_prodi, $prodi->nama_prodi];
            }
        }

        $prodi = TProdi::where('kode_prodi', $user['kode_prodi'] ?? null)->first();

        return [
            $prodi?->id,
            $user['kode_prodi'] ?? null,
            $user['nama_prodi'] ?? $prodi?->nama_prodi,
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_persyaratan' => 'required',
            'tahapan_sidang' => 'required',
            'strata' => 'required',
            'status_aktif' => 'required',
        ]);

        [$prodiId, $kodeProdi, $namaProdi] = $this->resolveProdiFromSession();

        $check = MasterExcelService::validateItemRow([
            $kodeProdi,
            $request->nama_persyaratan,
            $request->tahapan_sidang,
            $request->strata,
        ], 'persyaratan');

        if ($check['result'] !== 'ok') {
            return $this->failResponse($request, $check['message']);
        }

        TSyaratSidang::create([
            'NAMA_PERSYARATAN' => $check['nama'],
            'ID_PRODI' => $prodiId,
            'KODE_PRODI' => $kodeProdi,
            'NAMA_PRODI' => $namaProdi,
            'TAHAPAN_SIDANG' => $check['tahapan'],
            'STRATA' => $check['strata'],
            // Form manual tetap memakai pilihan user; import selalu AKTIF.
            'STATUS_AKTIF' => $request->status_aktif === 'NON AKTIF' ? 'NON AKTIF' : 'AKTIF',
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
            [$prodiId, $kodeProdi, $namaProdi] = $this->resolveProdiFromSession();

            $check = MasterExcelService::validateItemRow([
                $kodeProdi,
                $request->nama_persyaratan,
                $request->tahapan_sidang,
                $request->strata,
            ], 'persyaratan', [], $item->id);

            if ($check['result'] !== 'ok') {
                return $this->failResponse($request, $check['message']);
            }

            $item->update([
                'NAMA_PERSYARATAN' => $check['nama'],
                'ID_PRODI' => $prodiId,
                'KODE_PRODI' => $kodeProdi,
                'NAMA_PRODI' => $namaProdi,
                'TAHAPAN_SIDANG' => $check['tahapan'],
                'STRATA' => $check['strata'],
                // Form manual tetap memakai pilihan user; import selalu AKTIF.
                'STATUS_AKTIF' => $request->status_aktif === 'NON AKTIF' ? 'NON AKTIF' : 'AKTIF',
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

    private function failResponse(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['error' => $message], 422);
        }

        return redirect()->back()->with('error', $message);
    }
}

