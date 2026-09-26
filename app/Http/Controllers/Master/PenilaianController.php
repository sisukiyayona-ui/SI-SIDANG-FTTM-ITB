<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TPointPenilaian;
use App\Models\TProdi;
use App\Models\TTahapan;
use App\Services\MasterExcelService;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function template()
    {
        return MasterExcelService::template('penilaian');
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        // File hanya dibaca dari temp PHP lalu dibuang otomatis — tidak disimpan ke folder Laravel.
        $rows = MasterExcelService::previewItems('penilaian', $request->file('file'), session('auth_user') ?? []);

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

        $result = MasterExcelService::storeItems('penilaian', $request->input('rows'), session('auth_user') ?? []);

        return response()->json(['success' => true] + $result);
    }

    public function create()
    {
        $prodis = $this->filteredProdis();
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
                'kode_prodi' => $item->kode_prodi,
                'nama_prodi_item' => $item->nama_prodi,
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

        $prodis = $this->filteredProdis();
        $tahapans = TTahapan::all();
        return view('master.penilaian-form', compact('penilaian', 'prodis', 'tahapans'));
    }

    public function index(Request $request)
    {
        $user = session('auth_user');
        $query = TPointPenilaian::query();

        if ($user['role'] === 'TU Prodi') {
            $query->whereIn('ID_PRODI', $user['id_prodi'] ?? []);
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

        $prodis = $this->filteredProdis();
        $tahapans = TTahapan::all();

        if ($request->ajax()) {
            $tableHtml = view('master._penilaian_table', compact('penilaian'))->render();
            return response()->json(['html' => $tableHtml]);
        }

        return view('master.penilaian', compact('penilaian', 'prodis', 'tahapans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'penilaian' => 'required',
            'tahapan_sidang' => 'required',
            'strata' => 'required',
            'status_aktif' => 'required',
            'id_prodi' => 'required|integer|exists:t_prodi,id',
        ]);

        $user = session('auth_user');

        $prodi = $this->resolveProdi($request, $user);
        if (!$prodi) {
            return $this->failResponse($request, 'Prodi tidak ditemukan atau tidak berhak mengakses prodi tersebut');
        }

        $check = MasterExcelService::validateItemRow([
            $prodi->kode_prodi,
            $request->penilaian,
            $request->no_form,
            $request->tahapan_sidang,
            $request->strata,
            $request->status_catatan,
            $request->Keterangan,
        ], 'penilaian');

        if ($check['result'] !== 'ok') {
            return $this->failResponse($request, $check['message']);
        }

        TPointPenilaian::create([
            'PENILAIAN' => $check['nama'],
            'ID_PRODI' => $prodi->id,
            'KODE_PRODI' => $prodi->kode_prodi,
            'NAMA_PRODI' => $prodi->nama_prodi,
            'TAHAPAN_SIDANG' => $check['tahapan'],
            'STRATA' => $check['strata'],
            // Form manual tetap memakai pilihan user; import selalu AKTIF.
            'STATUS_AKTIF' => $request->status_aktif === 'NON AKTIF' ? 'NON AKTIF' : 'AKTIF',
            'NO_FORM' => $check['no_form'],
            'STATUS_CATATAN' => $check['status_catatan'],
            'KETERANGAN' => $check['keterangan'],
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
            'id_prodi' => 'required|integer|exists:t_prodi,id',
        ]);

        $item = TPointPenilaian::find((int) $id);
        if ($item) {
            $user = session('auth_user');

            $prodi = $this->resolveProdi($request, $user, $item->id_prodi);
            if (!$prodi) {
                return $this->failResponse($request, 'Prodi tidak ditemukan atau tidak berhak mengakses prodi tersebut');
            }

            $check = MasterExcelService::validateItemRow([
                $prodi->kode_prodi,
                $request->penilaian,
                $request->no_form,
                $request->tahapan_sidang,
                $request->strata,
                $request->status_catatan,
                $request->Keterangan,
            ], 'penilaian', [], $item->id);

            if ($check['result'] !== 'ok') {
                return $this->failResponse($request, $check['message']);
            }

            $item->update([
                'PENILAIAN' => $check['nama'],
                'ID_PRODI' => $prodi->id,
                'KODE_PRODI' => $prodi->kode_prodi,
                'NAMA_PRODI' => $prodi->nama_prodi,
                'TAHAPAN_SIDANG' => $check['tahapan'],
                'STRATA' => $check['strata'],
                // Form manual tetap memakai pilihan user; import selalu AKTIF.
                'STATUS_AKTIF' => $request->status_aktif === 'NON AKTIF' ? 'NON AKTIF' : 'AKTIF',
                'NO_FORM' => $check['no_form'],
                'STATUS_CATATAN' => $check['status_catatan'],
                'KETERANGAN' => $check['keterangan'],
                'TGL_UPDATE' => now(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.penilaian.index')->with('success', 'Data komponen berhasil disimpan.');
    }

    private function failResponse(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['error' => $message], 422);
        }

        return redirect()->back()->with('error', $message);
    }

    public function destroy($id)
    {
        $item = TPointPenilaian::find((int) $id);
        if ($item) {
            $item->delete();
        }
        return response()->json(['success' => true]);
    }

    private function filteredProdis()
    {
        $user = session('auth_user');
        $query = TProdi::where('status_aktif', 'AKTIF');

        // TU Prodi: hanya prodi yang di-assign dari t_user_prodi
        if ($user['role'] === 'TU Prodi') {
            $query->whereIn('id', $user['id_prodi'] ?? []);
        } elseif (!empty($user['kode_fs'])) {
            // Selain TU Prodi: sesuai fakultas dari akun yang login
            $query->where('kode_fs', $user['kode_fs']);
        }

        return $query->get();
    }

    /**
     * Resolve prodi from request by role.
     * TU Prodi: must be one of assigned prodi (respects selected id_prodi).
     * Other roles: any existing prodi from request.
     */
    private function resolveProdi(Request $request, array $user, $fallbackId = null): ?TProdi
    {
        $requestedId = $request->input('id_prodi');

        if (($user['role'] ?? '') === 'TU Prodi') {
            $allowedIds = array_map('intval', $user['id_prodi'] ?? []);
            $requestedInt = (int) $requestedId;

            if ($requestedInt > 0 && in_array($requestedInt, $allowedIds, true)) {
                return TProdi::find($requestedInt);
            }

            // Fallback: keep existing prodi if still allowed, else first assigned
            $fallbackInt = (int) $fallbackId;
            if ($fallbackInt > 0 && in_array($fallbackInt, $allowedIds, true)) {
                return TProdi::find($fallbackInt);
            }

            return TProdi::whereIn('id', $allowedIds)->first();
        }

        return $requestedId ? TProdi::find($requestedId) : null;
    }
}

