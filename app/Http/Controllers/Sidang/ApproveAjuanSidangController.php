<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApproveAjuanSidangController extends Controller
{
    public function index($strata)
    {
        $strata = strtoupper($strata);
        if (!in_array($strata, ['S1', 'S2', 'S3'])) {
            abort(404);
        }

        $totalKpps = DB::table('t_kpps')->count();
        $authUser = session('auth_user');
        $currentUserId = $authUser['id'] ?? 0;

        $approvalSub = "SELECT COUNT(DISTINCT app.ID_USER) FROM t_app_ajuan_sidang app WHERE app.ID_AJUAN_SIDANG = a.id AND app.STATUS_APPROVE = 't'";
        $myApprovalSub = "SELECT COUNT(*) FROM t_app_ajuan_sidang app WHERE app.ID_AJUAN_SIDANG = a.id AND app.ID_USER = {$currentUserId} AND app.STATUS_APPROVE = 't'";
        $usulanSub = "SELECT app.USULAN_PERBAIKAN FROM t_app_ajuan_sidang app WHERE app.ID_AJUAN_SIDANG = a.id AND app.STATUS_APPROVE = 't' AND app.USULAN_PERBAIKAN IS NOT NULL AND app.USULAN_PERBAIKAN != '' ORDER BY app.id DESC LIMIT 1";

        $q = DB::table('t_ajuan_sidang as a')
            ->where('a.STRATA', $strata)
            ->where('a.STATUS_AJUKAN_KPPS', 'y')
            ->where('a.TAHAPAN_SIDANG', '!=', 'tahap I');

        // Filter pencarian
        $search = trim((string) request()->query('search', ''));
        if ($search !== '') {
            $q->where(function ($sub) use ($search) {
                $sub->where('a.NIM', 'like', "%{$search}%")
                    ->orWhere('a.NAMA_MHS', 'like', "%{$search}%")
                    ->orWhere('a.JUDUL', 'like', "%{$search}%");
            });
        }
        $tahapan = trim((string) request()->query('tahapan', ''));
        if ($tahapan !== '') {
            $q->where('a.TAHAPAN_SIDANG', $tahapan);
        }
        $status = trim((string) request()->query('status', ''));
        if ($status === 'approved') {
            $q->whereRaw("({$approvalSub}) >= ?", [$totalKpps]);
        } elseif ($status === 'rejected') {
            $q->where('a.STATUS_LULUS', 'rejected');
        } elseif ($status === 'belum') {
            $q->whereRaw("({$approvalSub}) < ?", [$totalKpps]);
        }

        $rows = $q->select(
                'a.id',
                'a.NIM',
                'a.NAMA_MHS',
                'a.JUDUL',
                'a.NAMA_PRODI',
                'a.TAHAPAN_SIDANG',
                'a.TGL_SIDANG',
                'a.STATUS_AJUKAN_KPPS',
                'a.STATUS_LULUS',
                DB::raw("({$approvalSub}) as kpps_approved_count"),
                DB::raw("({$myApprovalSub}) as my_approved"),
                DB::raw("({$usulanSub}) as usulan_perbaikan")
            )
            ->orderBy('a.id', 'desc')
            ->get();

        return view('sidang.approve-ajuan-sidang', compact('rows', 'strata', 'totalKpps'));
    }

    public function show($strata, $id)
    {
        $strata = strtoupper($strata);
        if (!in_array($strata, ['S1', 'S2', 'S3'])) {
            abort(404);
        }

        $ajuan = \App\Models\TAjuanSidang::find($id);
        if (!$ajuan || $ajuan->strata !== $strata) {
            abort(404);
        }

        $idJudul = $ajuan->ID_JUDUL;
        $tahapan = $ajuan->TAHAPAN_SIDANG;

        $allAjuan = \App\Models\TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->orderBy('id')
            ->get();

        $allAjuanJson = $allAjuan->map(function ($a) {
            return [
                'id' => $a->id,
                'tgl_sidang' => $a->tgl_sidang,
                'waktu_sidang' => $a->waktu_sidang,
                'ruang_sidang' => $a->ruang_sidang,
                'tgl_surat_undangan' => $a->tgl_undangan,
                'no_surat_undangan' => $a->NO_UNDANGAN,
                'tgl_surat_penelaah' => $a->tgl_penelaah,
                'no_surat_penelaah' => $a->no_surat_penelaah,
                'tgl_hasil_penelahan' => $a->TGL_HASIL_PENELAHAN,
                'email_surat' => $a->email_surat,
                'no_sk_kelulusan' => $a->SK_LULUS,
            ];
        })->values();

        $kodeProdi = $ajuan->kode_prodi;
        $prodiId = null;
        if ($kodeProdi) {
            $prodi = \App\Models\TProdi::where('kode_prodi', $kodeProdi)->first();
            $prodiId = $prodi?->id;
        }

        $cekPersyaratan = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)
            ->get();

        $persyaratan = collect();
        if ($cekPersyaratan->isEmpty()) {
            $q = \App\Models\TSyaratSidang::where('TAHAPAN_SIDANG', $tahapan)
                ->where('STATUS_AKTIF', 'AKTIF');
            if ($prodiId) {
                $q->where('ID_PRODI', $prodiId);
            }
            $persyaratan = $q->get();
        } else {
            $q = \App\Models\TSyaratSidang::where('TAHAPAN_SIDANG', $tahapan)
                ->whereIn('id', $cekPersyaratan->pluck('ID_SYARAT_SIDANG'))
                ->get()
                ->keyBy('id');
            $persyaratan = $cekPersyaratan->map(function ($c) use ($q) {
                $c->NAMA_PERSYARATAN = optional($q->get($c->ID_SYARAT_SIDANG))->NAMA_PERSYARATAN ?? $c->PERSYARATAN;
                return $c;
            });
        }

        $timSidang = \App\Models\TTimSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->get();

        $penilaian = \App\Models\TPenilaian::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->get();

        // Get mahasiswa's prodi from ajuan
        $mahasiswaProdi = $ajuan->kode_prodi;
        
        // Filter point penilaian by prodi
        $pointPenilaianQuery = \App\Models\TPointPenilaian::where('tahapan_sidang', $tahapan)
            ->where('status_aktif', 'AKTIF');
        
        if ($mahasiswaProdi) {
            $pointPenilaianQuery->where('KODE_PRODI', $mahasiswaProdi);
        }
        
        $pointPenilaian = $pointPenilaianQuery->select('no_form')
            ->distinct()
            ->orderBy('no_form')
            ->get();

        $allPointPenilaianQuery = \App\Models\TPointPenilaian::where('tahapan_sidang', $tahapan)
            ->where('status_aktif', 'AKTIF');
        
        if ($mahasiswaProdi) {
            $allPointPenilaianQuery->where('KODE_PRODI', $mahasiswaProdi);
        }
        
        $allPointPenilaian = $allPointPenilaianQuery->get();

        if (request()->ajax()) {
            return view('sidang.kpps-tahap-content', compact(
                'ajuan',
                'allAjuan',
                'allAjuanJson',
                'idJudul',
                'tahapan',
                'persyaratan',
                'timSidang',
                'penilaian',
                'pointPenilaian',
                'allPointPenilaian',
                'strata'
            ))->render();
        }

        return view('sidang.kpps-tahap', compact(
            'ajuan',
            'allAjuan',
            'allAjuanJson',
            'idJudul',
            'tahapan',
            'persyaratan',
            'timSidang',
            'penilaian',
            'pointPenilaian',
            'allPointPenilaian',
            'strata'
        ));
    }

    public function store(Request $request)
    {
        $authUser = session('auth_user');
        $userId = $authUser['id'] ?? null;

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $now = now()->toDateString();
        $inserted = 0;

        foreach ($request->ids as $ajuanId) {
            $exists = DB::table('t_app_ajuan_sidang')
                ->where('ID_AJUAN_SIDANG', $ajuanId)
                ->where('ID_USER', $userId)
                ->first();

            if ($exists) {
                DB::table('t_app_ajuan_sidang')
                    ->where('id', $exists->id)
                    ->update([
                        'STATUS_APPROVE' => 't',
                        'TGL_UPDATE' => $now,
                        'TGL_APPROVE' => $now,
                    ]);
                $inserted++;
                continue;
            }

            DB::table('t_app_ajuan_sidang')->insert([
                'ID_USER' => $userId,
                'ID_AJUAN_SIDANG' => $ajuanId,
                'STATUS_APPROVE' => 't',
                'TGL_CREATE' => $now,
                'TGL_UPDATE' => $now,
                'TGL_APPROVE' => $now,
            ]);
            $inserted++;
        }

        return response()->json([
            'success' => true,
            'inserted' => $inserted,
            'message' => $inserted . ' ajuan sidang berhasil di-approve',
        ]);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $authUser = session('auth_user');
        $userId = $authUser['id'] ?? null;
        $now = now()->toDateString();
        $rejected = 0;

        foreach ($request->ids as $ajuanId) {
            $updated = DB::table('t_ajuan_sidang')
                ->where('id', $ajuanId)
                ->update([
                    'STATUS_LULUS' => null,
                    'STATUS_AJUKAN_KPPS' => null,
                    'STATUS_SUBMIT' => 't',
                    'TGL_AJUKAN_KPPS' => null,
                    'TGL_UPDATE' => $now,
                ]);

            $exists = DB::table('t_app_ajuan_sidang')
                ->where('ID_AJUAN_SIDANG', $ajuanId)
                ->where('ID_USER', $userId)
                ->first();

            if ($exists) {
                DB::table('t_app_ajuan_sidang')
                    ->where('id', $exists->id)
                    ->update([
                        'STATUS_APPROVE' => 'f',
                        'TGL_UPDATE' => $now,
                        'TGL_APPROVE' => $now,
                    ]);
            } elseif ($userId) {
                DB::table('t_app_ajuan_sidang')->insert([
                    'ID_USER' => $userId,
                    'ID_AJUAN_SIDANG' => $ajuanId,
                    'STATUS_APPROVE' => 'f',
                    'TGL_CREATE' => $now,
                    'TGL_UPDATE' => $now,
                    'TGL_APPROVE' => $now,
                ]);
            }

            if ($updated) {
                $rejected++;
            }
        }

        return response()->json([
            'success' => true,
            'rejected' => $rejected,
            'message' => $rejected . ' ajuan sidang berhasil direject',
        ]);
    }

    public function simpanUsulanPerbaikan(Request $request)
    {
        $request->validate([
            'id_ajuan_sidang' => 'required|integer',
            'usulan_perbaikan' => 'nullable|string',
        ]);

        $idAjuan = (int) $request->id_ajuan_sidang;
        $usulan = $request->input('usulan_perbaikan');
        $usulan = $usulan === null ? '' : (string) $usulan;

        $authUser = session('auth_user');
        $userId = $authUser['id'] ?? null;

        $existing = DB::table('t_app_ajuan_sidang')
            ->where('ID_AJUAN_SIDANG', $idAjuan)
            ->where('ID_USER', $userId)
            ->first();

        if ($existing) {
            DB::table('t_app_ajuan_sidang')
                ->where('id', $existing->id)
                ->update([
                    'STATUS_APPROVE' => 't',
                    'USULAN_PERBAIKAN' => $usulan,
                    'TGL_UPDATE' => now()->toDateString(),
                    'TGL_APPROVE' => now()->toDateString(),
                ]);
        } else {
            $now = now()->toDateString();
            DB::table('t_app_ajuan_sidang')->insert([
                'ID_USER' => $userId,
                'ID_AJUAN_SIDANG' => $idAjuan,
                'STATUS_APPROVE' => 't',
                'USULAN_PERBAIKAN' => $usulan,
                'TGL_CREATE' => $now,
                'TGL_UPDATE' => $now,
                'TGL_APPROVE' => $now,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usulan perbaikan berhasil disimpan',
        ]);
    }

    public function getUsulanPerbaikan($idAjuan)
    {
        $authUser = session('auth_user');
        $userId = $authUser['id'] ?? null;

        $row = DB::table('t_app_ajuan_sidang')
            ->where('ID_AJUAN_SIDANG', (int) $idAjuan)
            ->where('ID_USER', $userId)
            ->first();

        return response()->json([
            'success' => true,
            'usulan_perbaikan' => $row->USULAN_PERBAIKAN ?? '',
        ]);
    }
}