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

        $rows = DB::table('t_ajuan_sidang as a')
            ->leftJoin('t_app_ajuan_sidang as app', function ($join) {
                $join->on('app.ID_AJUAN_SIDANG', '=', 'a.id')
                    ->where('app.STATUS_APPROVE', '=', 't');
            })
            ->where('a.STRATA', $strata)
            ->where('a.STATUS_AJUKAN_KPPS', 't')
            ->where('a.TAHAPAN_SIDANG', '!=', 'tahap I')
            ->select(
                'a.id',
                'a.NIM',
                'a.NAMA_MHS',
                'a.JUDUL',
                'a.TAHAPAN_SIDANG',
                'a.TGL_SIDANG',
                'a.STATUS_AJUKAN_KPPS',
                DB::raw('CASE WHEN app.id IS NOT NULL THEN 1 ELSE 0 END as approved'),
                'app.TGL_APPROVE'
            )
            ->orderByRaw('app.id IS NOT NULL, a.id desc')
            ->get();

        return view('sidang.approve-ajuan-sidang', compact('rows', 'strata'));
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
                ->where('STATUS_APPROVE', 't')
                ->exists();

            if ($exists) {
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
}