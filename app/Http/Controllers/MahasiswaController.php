<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TAjuanSidang;
use App\Models\TJudul;
use App\Models\TPenilaian;
use App\Models\TProdi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        $user = session('auth_user');
        $strata = strtoupper((string) ($user['strata'] ?? $user['STRATA'] ?? 'S3'));
        if (!in_array($strata, ['S1', 'S2', 'S3'], true)) {
            $strata = 'S3';
        }

        // Kolom progress per strata. Hanya tahapan milik strata ini yang
        // dihitung, jadi query S1/S2 tidak ikut menghitung tahapan S3.
        $stagesByStrata = [
            'S1' => [
                ['key' => 'tahap1', 'tahapan' => 'TA I',  'label' => 'TA I'],
                ['key' => 'tahap2', 'tahapan' => 'TA II', 'label' => 'TA II'],
            ],
            'S2' => [
                ['key' => 'tesis1', 'tahapan' => 'Tesis I',  'label' => 'Tesis I'],
                ['key' => 'tesis2', 'tahapan' => 'Tesis II', 'label' => 'Tesis II'],
            ],
            'S3' => [
                ['key' => 'tahap1', 'tahapan' => 'tahap I',  'label' => 'Ujian Kualifikasi'],
                ['key' => 'tahap2', 'tahapan' => 'tahap II', 'label' => 'Ujian Proposal'],
                ['key' => 'sk1',    'tahapan' => 'SK I',     'label' => 'SK I',   'group' => 'Tahap III'],
                ['key' => 'sk2',    'tahapan' => 'SK II',    'label' => 'SK II',  'group' => 'Tahap III'],
                ['key' => 'sk3',    'tahapan' => 'SK III',   'label' => 'SK III', 'group' => 'Tahap III'],
                ['key' => 'sk4',    'tahapan' => 'SK IV',    'label' => 'SK IV',  'group' => 'Tahap III'],
                ['key' => 'tahap4', 'tahapan' => 'tahap IV', 'label' => 'Sidang Terbuka / Tertutup'],
            ],
        ];
        $stages = $stagesByStrata[$strata];

        // Header tabel dikelompokkan (mis. "Tahap III" untuk SK I–IV di S3).
        $headerGroups = [];
        $hasSubHeader = false;
        foreach ($stages as $stage) {
            $group = $stage['group'] ?? null;
            if ($group === null) {
                $headerGroups[] = ['label' => $stage['label'], 'stages' => [$stage]];
                continue;
            }
            $last = count($headerGroups) - 1;
            if ($last >= 0 && $headerGroups[$last]['label'] === $group) {
                $headerGroups[$last]['stages'][] = $stage;
            } else {
                $headerGroups[] = ['label' => $group, 'stages' => [$stage]];
            }
        }
        foreach ($headerGroups as $group) {
            if (count($group['stages']) > 1) {
                $hasSubHeader = true;
                break;
            }
        }

        // Identitas strata untuk badge & penanda tampilan dashboard.
        $strataMeta = [
            'S1' => ['label' => 'Sarjana', 'color' => '#2e7d32'],
            'S2' => ['label' => 'Magister', 'color' => '#1565c0'],
            'S3' => ['label' => 'Doktor', 'color' => '#6a1b9a'],
        ][$strata];

        // Ambil semua judul milik mahasiswa
        $juduls = TJudul::where('id_user_mhs', $user['id'])->get();
        
        // Set judul aktif jika belum ada
        $activeJudulId = session('active_judul_id');
        if (!$activeJudulId && $juduls->count() > 0) {
            $activeJudulId = $juduls->first()->id;
            session(['active_judul_id' => $activeJudulId]);
        }
        
        // Query tracking progress sidang — base table t_judul agar mahasiswa baru (belum punya ajuan) tetap muncul
        // Status CASE expression: consistent with SidangS1/S2/S3 controllers
        $statusCase = function ($alias, $tahapan) use ($strata) {
            $rejectSql = "EXISTS (
                        SELECT 1 FROM t_app_ajuan_sidang app
                        INNER JOIN t_ajuan_sidang x2 ON x2.id = app.ID_AJUAN_SIDANG
                        WHERE x2.id_judul = j.id AND x2.tahapan_sidang = '{$tahapan}' AND app.STATUS_APPROVE = 'f'
                    )";

            if ($strata === 'S2') {
                return "CASE
                    WHEN LOWER(MAX({$alias}.NILAI_TERKUNCI)) = 'y' AND LOWER(MAX({$alias}.STATUS_LULUS)) IN ('lulus sidang magister', 'tidak lulus sidang magister', 'mengulangi sidang magister') THEN MAX({$alias}.STATUS_LULUS)
                    WHEN MAX({$alias}.id) IS NULL OR COALESCE(MAX({$alias}.STATUS_AJUKAN_MHS), 't') != 'y' THEN 'belum diajukan'
                    WHEN COALESCE(MAX({$alias}.STATUS_AJUKAN_PRODI), 't') != 'y' THEN 'Diproses di TU Prodi'
                    ELSE 'Terjadwal'
                END";
            }

            if ($strata === 'S1') {
                return "CASE
                    WHEN MAX({$alias}.STATUS_LULUS) IS NOT NULL AND MAX({$alias}.STATUS_LULUS) != 'diajukan' THEN MAX({$alias}.STATUS_LULUS)
                    WHEN MAX({$alias}.id) IS NULL OR COALESCE(MAX({$alias}.STATUS_AJUKAN_MHS), 't') != 'y' THEN 'belum diajukan'
                    WHEN COALESCE(MAX({$alias}.STATUS_AJUKAN_PRODI), 't') != 'y' THEN 'Diproses di TU Prodi'
                    WHEN {$rejectSql} THEN 'Rejected'
                    WHEN MAX({$alias}.TGL_SIDANG) IS NOT NULL THEN 'Terjadwal'
                    ELSE 'Diproses di Fakultas'
                END";
            }

            return "CASE
                    WHEN MAX({$alias}.STATUS_LULUS) IS NOT NULL AND MAX({$alias}.STATUS_LULUS) != 'diajukan' THEN MAX({$alias}.STATUS_LULUS)
                    WHEN MAX({$alias}.id) IS NULL OR COALESCE(MAX({$alias}.STATUS_AJUKAN_MHS), 't') != 'y' THEN 'belum diajukan'
                    WHEN COALESCE(MAX({$alias}.STATUS_AJUKAN_PRODI), 't') != 'y' THEN 'Diproses di TU Prodi'
                    WHEN COALESCE(MAX({$alias}.STATUS_AJUKAN_KPPS), 't') = 'y' AND EXISTS (
                        SELECT 1 FROM t_kpps k0
                        INNER JOIN t_ajuan_sidang x0 ON x0.KODE_PRODI = k0.KODE_PRODI
                        WHERE x0.id_judul = j.id AND x0.tahapan_sidang = '{$tahapan}' AND k0.STATUS_AKTIF = 'AKTIF'
                    ) AND (
                        SELECT COUNT(DISTINCT app.ID_USER) FROM t_app_ajuan_sidang app
                        INNER JOIN t_ajuan_sidang x2 ON x2.id = app.ID_AJUAN_SIDANG
                        WHERE x2.id_judul = j.id AND x2.tahapan_sidang = '{$tahapan}' AND app.STATUS_APPROVE IN ('t', 'y')
                        AND EXISTS (SELECT 1 FROM t_kpps kk WHERE kk.ID_USER = app.ID_USER AND kk.STATUS_AKTIF = 'AKTIF')
                    ) >= (
                        SELECT COUNT(DISTINCT k.ID_USER) FROM t_kpps k
                        INNER JOIN t_ajuan_sidang x3 ON x3.KODE_PRODI = k.KODE_PRODI
                        WHERE x3.id_judul = j.id AND x3.tahapan_sidang = '{$tahapan}' AND k.STATUS_AKTIF = 'AKTIF'
                    ) THEN 'Terjadwal'
                    WHEN COALESCE(MAX({$alias}.STATUS_AJUKAN_KPPS), 't') = 'y' THEN 'Menunggu Approve KPPS'
                    WHEN {$rejectSql} THEN 'Rejected'
                    ELSE 'Diproses di Fakultas'
                END";
        };

        $selectCols = [
            'j.id as id_judul',
            'j.JUDUL as Judul',
            'u.NAMA_PRODI as nama_prodi',
        ];
        $stageJoinSql = '';
        foreach ($stages as $index => $stage) {
            $alias = 'a' . ($index + 1);
            $selectCols[] = $statusCase($alias, $stage['tahapan']) . " as {$stage['key']}";
            $stageJoinSql .= "
            LEFT JOIN (
                SELECT x.* FROM t_ajuan_sidang x
                INNER JOIN (
                    SELECT id_judul, MAX(id) as max_id
                    FROM t_ajuan_sidang
                    WHERE tahapan_sidang = '{$stage['tahapan']}'
                    GROUP BY id_judul
                ) y ON x.id = y.max_id AND x.id_judul = y.id_judul
            ) {$alias} ON j.id = {$alias}.ID_JUDUL";
        }

        $tracking = DB::select("
            SELECT DISTINCT
                " . implode(",\n                ", $selectCols) . "
            FROM t_judul j
            LEFT JOIN t_user u ON j.ID_USER_MHS = u.id
            {$stageJoinSql}
            WHERE j.ID_USER_MHS = ?
            GROUP BY j.id, j.JUDUL, u.NAMA_PRODI
        ", [$user['id']]);

        $stageLabels = [];
        foreach ($stages as $stage) {
            $stageLabels[$stage['tahapan']] = $stage['label'];
        }

        return view('mahasiswa.dashboard', compact(
            'tracking', 'juduls', 'activeJudulId', 'strata', 'strataMeta',
            'stages', 'headerGroups', 'hasSubHeader', 'stageLabels'
        ));
    }
    
    public function setActiveJudul($idJudul)
    {
        $user = session('auth_user');
        
        // Validasi bahwa judul milik mahasiswa
        $judul = TJudul::where('id_user_mhs', $user['id'])->where('id', $idJudul)->first();
        if (!$judul) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Judul tidak ditemukan.');
        }
        
        session(['active_judul_id' => $idJudul]);
        
        return redirect()->route('mahasiswa.dashboard')->with('success', 'Judul aktif berhasil diubah.');
    }
    
    public function showTahap($tahapan)
    {
        $user = session('auth_user');
        
        $tahapan = explode('?', $tahapan)[0];
        
        $idJudul = request('id_judul') ?? session('active_judul_id');
        
        // For Pembimbing/Penguji roles, get id_judul from request
        if (in_array($user['role'], ['Pembimbing', 'Penguji']) && request('id_judul')) {
            $idJudul = request('id_judul');
        }
        
        if (!$idJudul) {
            if (in_array($user['role'], ['Pembimbing', 'Penguji'])) {
                return response()->json(['error' => 'ID Judul diperlukan.'], 400);
            }
            $judul = TJudul::where('id_user_mhs', $user['id'])->first();
            if (!$judul) {
                return response()->json(['error' => 'Anda belum memiliki judul tesis.'], 404);
            }
            $idJudul = $judul->id;
            session(['active_judul_id' => $idJudul]);
        }

        // Validasi kepemilikan judul untuk role Mahasiswa
        if ($user['role'] === 'Mahasiswa') {
            $judulMhs = TJudul::where('id', $idJudul)->where('id_user_mhs', $user['id'])->exists();
            if (!$judulMhs) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }
        
        // Data nama, nim, judul, status lulus dari table t_ajuan_sidang
        $ajuan = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->latest('id')
            ->first();

        // All ajuan records for this judul+tahapan (for jadwal history table)
        $allAjuan = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->orderBy('id')
            ->get();

        $allAjuanJson = $allAjuan->map(function($a) {
            return [
                'id' => $a->id,
                'tgl_sidang' => $a->tgl_sidang,
                'waktu_sidang' => $a->waktu_sidang,
                'ruang_sidang' => $a->ruang_sidang,
                'tgl_surat_undangan' => $a->tgl_undangan,
                'no_surat_undangan' => $a->NO_UNDANGAN,
                'tgl_surat_penelaah' => $a->tgl_penelaah,
                'no_surat_penelaah' => $a->no_surat_penelaah,
                'tgl_pemanggilan_penilai' => $a->tgl_pemanggilan_penilai,
                'tgl_hasil_penelahan' => $a->TGL_HASIL_PENELAHAN,
                'email_surat' => $a->email_surat,
                'no_sk_kelulusan' => $a->SK_LULUS,
            ];
        })->values();

        // Get mahasiswa's prodi from judul->user relationship (source of truth)
        $judul = \App\Models\TJudul::find($idJudul);
        $mahasiswaProdi = null;
        if ($judul && $judul->user) {
            $mahasiswaProdi = $judul->user->KODE_PRODI;
        }

        // Get kode_prodi for persyaratan filtering.
        // Prioritas: prodi terkini mahasiswa (t_judul -> t_user), lalu KODE_PRODI
        // ajuan (snapshot saat ajuan dibuat — bisa berbeda bila data master user
        // dikoreksi setelah ajuan dibuat), terakhir session user.
        // Kandidat yang tidak ada di t_prodi dilewati supaya filter tetap valid.
        $kodeProdi = null;
        $prodiId = null;
        $prodi = null;
        foreach (array_filter([$mahasiswaProdi, $ajuan->kode_prodi ?? null, session('auth_user.kode_prodi')]) as $kandidat) {
            $prodi = TProdi::where('kode_prodi', $kandidat)->first();
            if ($prodi) {
                $kodeProdi = $kandidat;
                $prodiId = $prodi->id;
                break;
            }
        }

        // Strata pemilik judul — sumber utama untuk filter persyaratan & tab.
        // Nilai ajuan diutamakan, lalu data mahasiswa dari t_user, terakhir session.
        $strataView = strtoupper((string) ($ajuan->STRATA ?? $ajuan->strata ?? ''));
        if (!in_array($strataView, ['S1', 'S2', 'S3'], true)) {
            $strataView = strtoupper((string) ($judul?->user?->STRATA ?? session('auth_user.strata') ?? ''));
        }
        if (!in_array($strataView, ['S1', 'S2', 'S3'], true)) {
            $strataView = 'S3';
        }
        $showKppsTab = ($strataView === 'S3');
            
        // Table t_tim_sidang (Pembimbing / Penguji)
        // Urutan tampil: Ketua Pembimbing, Pembimbing I/II, Penguji I/II/III,
        // lalu role lain. S2 & S3: Ketua Sidang di paling bawah karena tidak
        // menginput nilai — penilai lain mengisi lebih dulu. Berlaku semua tahap
        // (Ujian Kualifikasi, Proposal, SK I–IV, Sidang Terbuka/Tertutup).
        $timSidangQuery = \App\Models\TTimSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan);

        if (in_array($strataView, ['S2', 'S3'], true)) {
            $timSidangQuery->orderByRaw("CASE
                WHEN UPPER(STATUS_TIM_SIDANG) LIKE '%KETUA SIDANG%' THEN 5
                WHEN UPPER(STATUS_TIM_SIDANG) LIKE '%KETUA PEMBIMBING%' THEN 1
                WHEN UPPER(STATUS_TIM_SIDANG) LIKE '%PEMBIMBING%' THEN 2
                WHEN UPPER(STATUS_TIM_SIDANG) LIKE '%PENGUJI%' THEN 3
                ELSE 4 END");
        } else {
            $timSidangQuery->orderByRaw("CASE
                WHEN UPPER(STATUS_TIM_SIDANG) LIKE '%KETUA SIDANG%' THEN 1
                WHEN UPPER(STATUS_TIM_SIDANG) LIKE '%KETUA PEMBIMBING%' THEN 2
                WHEN UPPER(STATUS_TIM_SIDANG) LIKE '%PEMBIMBING%' THEN 3
                WHEN UPPER(STATUS_TIM_SIDANG) LIKE '%PENGUJI%' THEN 4
                ELSE 5 END");
        }

        $timSidangQuery->orderByRaw("CASE
                WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'III$' THEN 3
                WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'II$' THEN 2
                WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'I$' THEN 1
                ELSE 0 END")
            ->orderBy('URUTAN');

        // For Pembimbing/Penguji, show all team members
        $timSidang = $timSidangQuery->get();
            
        // Get syarat IDs belonging to this prodi + strata (to filter cek_persyaratan)
        $syaratIdsForProdi = \App\Models\TSyaratSidang::where('TAHAPAN_SIDANG', $tahapan)
            ->where('STRATA', $strataView);
        if ($prodiId) {
            $syaratIdsForProdi->where('ID_PRODI', $prodiId);
        }
        $syaratIdsForProdi = $syaratIdsForProdi->pluck('id');

        // Table t_cek_persyaratan (Persyaratan) — filter by prodi syarat IDs
        $cekPersyaratan = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)
            ->whereIn('ID_SYARAT_SIDANG', $syaratIdsForProdi)
            ->get();

        // Data kelengkapan dari t_syarat_sidang (master daftar persyaratan aktif prodi)
        $syaratSidangList = function () use ($prodiId, $tahapan, $strataView) {
            $persyaratan = \App\Models\TSyaratSidang::where('TAHAPAN_SIDANG', $tahapan)
                ->where('STRATA', $strataView)
                ->where('STATUS_AKTIF', 'AKTIF');

            // Filter by prodi ID (integer) — berlaku untuk semua role
            if ($prodiId) {
                $persyaratan->where('ID_PRODI', $prodiId);
            }

            return $persyaratan->get();
        };

        // Prioritas data persyaratan:
        // 1. Jika sudah ada data di t_cek_persyaratan (mahasiswa upload/mencentang),
        //    tampilkan data tersebut (status ceklis & file tetap muncul), apapun
        //    status lulus/layak-nya.
        // 2. Jika belum ada data cek sama sekali: tampilkan daftar aktif dari
        //    t_syarat_sidang — termasuk untuk yang sudah lulus, supaya masih
        //    bisa upload/ubah file persyaratan setelah status lulus.
        $persyaratanQuery = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan);
        if ($strataView === 'S2' || $syaratIdsForProdi->isNotEmpty()) {
            $persyaratanQuery->whereIn('ID_SYARAT_SIDANG', $syaratIdsForProdi);
        }
        $persyaratan = $persyaratanQuery->get();

        if ($strataView === 'S2') {
            $tersimpan = $persyaratan->pluck('ID_SYARAT_SIDANG');
            $persyaratan = $persyaratan->concat($syaratSidangList()->reject(fn ($syarat) => $tersimpan->contains($syarat->id)))->values();
        } elseif ($persyaratan->isEmpty()) {
            $persyaratan = collect($syaratSidangList());
        }

        // Table penilaian - filter by user for Pembimbing/Penguji
        // Ketua Sidang melihat seluruh nilai tim (tanpa menginput nilai).
        $ketuaSidangSaya = $timSidang->first(function ($item) use ($user) {
            return strtolower(trim((string) ($item->status_tim_sidang ?? ''))) === 'ketua sidang'
                && (int) $item->id_user_penilai === (int) $user['id'];
        }) !== null;

        // Bila user yang sama juga tercatat sebagai pembimbing/penguji pada
        // tahapan ini, hak input nilainya tetap berlaku (form tidak view-only).
        $peranLainSaya = $timSidang->first(function ($item) use ($user) {
            $status = strtolower(trim((string) ($item->status_tim_sidang ?? '')));

            return $status !== '' && $status !== 'ketua sidang'
                && (int) $item->id_user_penilai === (int) $user['id'];
        }) !== null;

        $isKetuaSidangTim = $strataView === 'S2'
            ? $ketuaSidangSaya
            : ($ketuaSidangSaya || ($user['role'] ?? '') === 'Ketua Sidang') && ! $peranLainSaya;

        $penilaianQuery = \App\Models\TPenilaian::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan);
        if ($strataView === 'S2') {
            $penilaianQuery->where('ID_AJUAN', $ajuan?->id);
        }
        
        if (in_array($user['role'], ['Pembimbing', 'Penguji']) && !$isKetuaSidangTim) {
            $penilaianQuery->where('id_user_penilai', $user['id']);
        }
        
        $penilaian = $penilaianQuery->get();
        
        // Get point penilaian for form (distinct no_form based on tahapan and prodi)
        $pointPenilaianQuery = \App\Models\TPointPenilaian::where('tahapan_sidang', $tahapan)
            ->where('status_aktif', 'AKTIF');
        
        // Filter by prodi if mahasiswa prodi is available
        if ($mahasiswaProdi) {
            $pointPenilaianQuery->where('KODE_PRODI', $mahasiswaProdi);
        }
        
        $pointPenilaian = $pointPenilaianQuery->select('no_form')
            ->distinct()
            ->orderBy('no_form')
            ->get();
        
        // Get all point penilaian details (with same filters)
        $allPointPenilaianQuery = \App\Models\TPointPenilaian::where('tahapan_sidang', $tahapan)
            ->where('status_aktif', 'AKTIF');
        
        if ($mahasiswaProdi) {
            $allPointPenilaianQuery->where('KODE_PRODI', $mahasiswaProdi);
        }
        
        $allPointPenilaian = $allPointPenilaianQuery->get();
        
        // Additional data for TU Prodi/Admin/FS Tahap I & Tahap II
        $users = collect();
        $skList = collect();
        
        if (in_array($user['role'], ['TU Prodi', 'Admin', 'FS'])) {
            // Dropdown Tim: hanya user dengan role Pembimbing atau Penguji
            $pembimbingPengujiIds = \App\Models\TUserRole::whereIn('ROLE', ['Pembimbing', 'Penguji'])
                ->pluck('ID_USER')
                ->unique()
                ->toArray();
            $users = \App\Models\TUser::whereIn('ID', $pembimbingPengujiIds)
                ->select('ID', 'NAMA_LENGKAP', 'NIP_NIM')
                ->orderBy('NAMA_LENGKAP')
                ->get();
            
            // Get all SK records for this judul+tahapan
            $skList = DB::table('t_sk')
                ->where('id_judul', $idJudul)
                ->where('tahapan_sidang', $tahapan)
                ->orderBy('id', 'desc')
                ->get();
        }
        
        // Check if user is Ketua Tim Pembimbing (for status_lulus field)
        $isKetuaPembimbing = false;
        if ($user['role'] === 'Pembimbing') {
            $ketuaPembimbing = $timSidang->first(function($item) use ($user) {
                return str_contains($item->status_tim_sidang ?? '', 'Pembimbing')
                    && $item->id_user_penilai == $user['id'];
            });
            $isKetuaPembimbing = $ketuaPembimbing !== null;
        }
            
        // Kelengkapan nilai seluruh tim Pembimbing & Penguji (gate tombol Kunci Nilai)
        $kelengkapanNilai = \App\Services\KelengkapanNilaiSidang::cek($idJudul, $tahapan, $kodeProdi);

        // Gate approve KPPS prodi (khusus S3). S1/S2 tidak memakai KPPS.
        $kppsGate = $strataView === 'S3'
            ? \App\Services\KppsApproveGate::cek($idJudul, $tahapan, $mahasiswaProdi)
            : ['blocked' => false, 'total' => 0, 'approved' => 0, 'semua' => true];

        // Return HTML fragment for AJAX request
        $viewName = (in_array($user['role'], ['Admin', 'TU Prodi', 'FS', 'Pembimbing', 'Penguji', 'Ketua Sidang'])) ? 'sidang.tahap' : 'mahasiswa.tahap';

        $viewData = compact('ajuan', 'allAjuan', 'allAjuanJson', 'timSidang', 'persyaratan', 'cekPersyaratan', 'penilaian', 'tahapan', 'idJudul', 'pointPenilaian', 'allPointPenilaian', 'isKetuaPembimbing', 'isKetuaSidangTim', 'users', 'skList', 'kelengkapanNilai', 'kppsGate', 'strataView', 'showKppsTab', 'prodi');

        if (request()->ajax()) {
            return view($viewName, $viewData)->render();
        }
        
        return view($viewName, $viewData);
    }
    
    public function uploadPersyaratan(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');
        $idJudul = $request->id_judul ?? session('active_judul_id');

        if (!$idJudul) {
            \Log::error('ID Judul tidak ditemukan in uploadPersyaratan', ['request' => $request->all(), 'session' => session()->all()]);
            return response()->json(['success' => false, 'message' => 'ID Judul tidak ditemukan'], 400);
        }

        if ($user['role'] === 'Mahasiswa' && !TJudul::where('id', $idJudul)->where('id_user_mhs', $user['id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id_syarat_sidang' => 'required',
            'tahapan_sidang' => 'required',
            'file' => 'required|file|max:10240|mimetypes:application/pdf',
        ]);

        $tahapan = $request->tahapan_sidang;
        $idSyarat = $request->id_syarat_sidang;

        if (!is_numeric($idSyarat) || (int)$idSyarat <= 0) {
            return response()->json(['success' => false, 'message' => 'ID persyaratan tidak valid'], 422);
        }

        // S2: FS hanya view-only, tidak boleh mengupload persyaratan.
        $fsBlock = $this->fsViewOnlyS2Response($user, $idJudul, $tahapan);
        if ($fsBlock) {
            return $fsBlock;
        }

        try {
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file->getRealPath());
            if ($mimeType !== 'application/pdf') {
                return response()->json(['success' => false, 'message' => 'Hanya file PDF yang diizinkan'], 422);
            }

            $filename = time() . '_' . bin2hex(random_bytes(16)) . '.pdf';
            $path = $file->storeAs('uploads/persyaratan', $filename, 'public');
            if ($path === false) {
                \Log::error('uploadPersyaratan: gagal store file', ['id_judul' => $idJudul, 'id_syarat' => $idSyarat]);
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan file'], 500);
            }
            $linkFile = '/storage/' . $path;

            // Check if exist in t_cek_persyaratan
            $cek = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
                ->where('TAHAPAN_SIDANG', $tahapan)
                ->where('ID_SYARAT_SIDANG', $idSyarat)
                ->first();

            if ($cek) {
                // Hapus file lama jika ada
                if ($cek->LINK_FILE) {
                    $oldPath = str_replace('/storage/', '', $cek->LINK_FILE);
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                    }
                }

                // Update
                $cek->LINK_FILE = $linkFile;
                $cek->STATUS_LENGKAP = 'y';
                $cek->TGL_UPDATE = date('Y-m-d');
                $cek->save();
            } else {
                // Ambil info nama persyaratan dari TSyaratSidang
                $syarat = \App\Models\TSyaratSidang::find($idSyarat);

                // Hanya buat baris untuk syarat milik strata & prodi mahasiswa,
                // supaya persyaratan S3 tidak ikut muncul di S1/S2 (dan sebaliknya).
                $judulUpload = TJudul::find($idJudul);
                // Strata: coba dari ajuan (paling akurat), lalu user, lalu session
                $strataFromAjuan = strtoupper((string) (\App\Models\TAjuanSidang::where('ID_JUDUL', $idJudul)
                    ->whereNotNull('STRATA')->value('STRATA') ?? ''));
                $strataUpload = $strataFromAjuan !== ''
                    ? $strataFromAjuan
                    : strtoupper((string) ($judulUpload?->user?->STRATA ?? session('auth_user.strata') ?? ''));
                $prodiIdUpload = null;
                if ($judulUpload?->user?->KODE_PRODI) {
                    $prodiIdUpload = TProdi::where('kode_prodi', $judulUpload->user->KODE_PRODI)->value('id');
                }

                // Jika data kosong, insert semua persyaratan untuk tahapan ini terlebih dahulu agar berurutan
                $allSyaratQuery = \App\Models\TSyaratSidang::where('TAHAPAN_SIDANG', $tahapan)
                    ->where('STATUS_AKTIF', 'AKTIF');
                if ($strataUpload !== '') {
                    $allSyaratQuery->where('STRATA', $strataUpload);
                }
                if ($prodiIdUpload) {
                    $allSyaratQuery->where('ID_PRODI', $prodiIdUpload);
                }
                $allSyarat = $allSyaratQuery->get();

                $saved = false;
                foreach($allSyarat as $s) {
                    $newCek = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
                        ->where('TAHAPAN_SIDANG', $tahapan)
                        ->where('ID_SYARAT_SIDANG', $s->id)
                        ->first();
                    if (!$newCek) {
                        $newCek = new \App\Models\TCekPersyaratan();
                        $newCek->ID_JUDUL = $idJudul;
                        $newCek->TAHAPAN_SIDANG = $tahapan;
                        $newCek->ID_SYARAT_SIDANG = $s->id;
                        $newCek->PERSYARATAN = $s->NAMA_PERSYARATAN;
                        $newCek->STATUS_LENGKAP = 't';
                        $newCek->TGL_BUAT = date('Y-m-d');
                    }

                    if ($s->id == $idSyarat) {
                        $newCek->LINK_FILE = $linkFile;
                        $newCek->STATUS_LENGKAP = 'y';
                        $newCek->TGL_UPDATE = date('Y-m-d');
                        $saved = true;
                    }

                    $newCek->save();
                }

                // Syarat yang diupload bisa saja tidak lolos filter strata/prodi di
                // atas (mis. syarat lama tanpa ID_PRODI). Tetap simpan filenya
                // supaya upload tidak hilang tanpa pesan.
                if (!$saved) {
                    $target = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
                        ->where('TAHAPAN_SIDANG', $tahapan)
                        ->where('ID_SYARAT_SIDANG', $idSyarat)
                        ->first() ?: new \App\Models\TCekPersyaratan();
                    $target->ID_JUDUL = $idJudul;
                    $target->TAHAPAN_SIDANG = $tahapan;
                    $target->ID_SYARAT_SIDANG = $idSyarat;
                    $target->PERSYARATAN = $syarat->NAMA_PERSYARATAN ?? ('Persyaratan #' . $idSyarat);
                    $target->LINK_FILE = $linkFile;
                    $target->STATUS_LENGKAP = 'y';
                    $target->TGL_BUAT = $target->TGL_BUAT ?: date('Y-m-d');
                    $target->TGL_UPDATE = date('Y-m-d');
                    $target->save();
                }
            }

            return response()->json(['success' => true, 'message' => 'File berhasil diupload', 'file_url' => url($linkFile)]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal upload file'], 400);
        } catch (\Throwable $e) {
            \Log::error('uploadPersyaratan failed', [
                'id_judul' => $idJudul,
                'id_syarat' => $idSyarat,
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal mengupload file'], 500);
        }
    }

    public function updateKelengkapan(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');
        $idJudul = $request->id_judul ?? session('active_judul_id');
        
        if (!$idJudul) {
            \Log::error('ID Judul tidak ditemukan in updateKelengkapan', ['request' => $request->all(), 'session' => session()->all()]);
            return response()->json(['success' => false, 'message' => 'ID Judul tidak ditemukan'], 400);
        }

        if ($user['role'] === 'Mahasiswa' && !TJudul::where('id', $idJudul)->where('id_user_mhs', $user['id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'id_syarat_sidang' => 'required',
            'status_lengkap' => 'required',
        ]);
        
        $idSyarat = $request->id_syarat_sidang;
        $statusLengkap = $request->status_lengkap;
        
        // Get tahapan from the syarat
        $syarat = \App\Models\TSyaratSidang::find($idSyarat);
        if (!$syarat) {
            \Log::error('Persyaratan tidak ditemukan', ['id_syarat' => $idSyarat]);
            return response()->json(['success' => false, 'message' => 'Persyaratan tidak ditemukan'], 404);
        }
        
        $tahapan = $syarat->TAHAPAN_SIDANG;

        // S2: FS hanya view-only, tidak boleh mengubah kelengkapan.
        $fsBlock = $this->fsViewOnlyS2Response($user, $idJudul, $tahapan);
        if ($fsBlock) {
            return $fsBlock;
        }
        
        // Check if exist in t_cek_persyaratan
        $cek = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)
            ->where('ID_SYARAT_SIDANG', $idSyarat)
            ->first();
            
        if ($cek) {
            // Update
            $cek->STATUS_LENGKAP = $statusLengkap;
            $cek->TGL_UPDATE = date('Y-m-d');
            $cek->save();
            \Log::info('Updated kelengkapan', ['id_judul' => $idJudul, 'tahapan' => $tahapan, 'id_syarat' => $idSyarat, 'status' => $statusLengkap]);
        } else {
            // Insert new record
            $newCek = new \App\Models\TCekPersyaratan();
            $newCek->ID_JUDUL = $idJudul;
            $newCek->TAHAPAN_SIDANG = $tahapan;
            $newCek->ID_SYARAT_SIDANG = $idSyarat;
            $newCek->PERSYARATAN = $syarat->NAMA_PERSYARATAN;
            $newCek->STATUS_LENGKAP = $statusLengkap;
            $newCek->TGL_BUAT = date('Y-m-d');
            $newCek->save();
            \Log::info('Inserted kelengkapan', ['id_judul' => $idJudul, 'tahapan' => $tahapan, 'id_syarat' => $idSyarat, 'status' => $statusLengkap]);
        }
        
        return response()->json(['success' => true, 'message' => 'Status kelengkapan berhasil diupdate']);
    }

    public function saveAllPersyaratan(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');
        $idJudul = $request->id_judul ?? session('active_judul_id');
        $tahapan = $request->tahapan_sidang;

        if (!$idJudul || !$tahapan) {
            \Log::error('Data tidak lengkap in saveAllPersyaratan', ['request' => $request->all(), 'session' => session()->all()]);
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap'], 400);
        }

        if ($user['role'] === 'Mahasiswa' && !TJudul::where('id', $idJudul)->where('id_user_mhs', $user['id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // S2: FS hanya view-only, tidak boleh menyimpan persyaratan.
        $fsBlock = $this->fsViewOnlyS2Response($user, $idJudul, $tahapan);
        if ($fsBlock) {
            return $fsBlock;
        }

        $isS2Upload = in_array($tahapan, ['Tesis I', 'Tesis II'], true);
        $maxUploadKb = $isS2Upload ? 10240 : 2048;
        $request->validate([
            'files' => 'nullable|array',
            'files.*' => 'required|file|mimetypes:application/pdf|max:' . $maxUploadKb,
            'kelengkapan' => 'nullable|array',
            'kelengkapan.*' => 'required|in:y,t',
        ], [
            'files.*.mimetypes' => 'Hanya file PDF yang diizinkan.',
            'files.*.max' => 'Maksimal ukuran file ' . ($maxUploadKb / 1024) . ' MB.',
            'files.*.uploaded' => 'File gagal diunggah. Periksa ukuran file dan coba kembali.',
        ]);

        if ($isS2Upload) {
            $judulUpload = TJudul::with('user')->findOrFail($idJudul);
            $ajuanUpload = TAjuanSidang::where('ID_JUDUL', $idJudul)->where('TAHAPAN_SIDANG', $tahapan)->latest('id')->first();
            // Prodi terkini mahasiswa (t_judul -> t_user) diutamakan; KODE_PRODI
            // ajuan hanya snapshot saat ajuan dibuat.
            $kodeProdiUpload = $judulUpload->user?->KODE_PRODI ?? $ajuanUpload?->KODE_PRODI;
            $prodiIdUpload = TProdi::where('KODE_PRODI', $kodeProdiUpload)->value('id');
            $syaratIds = array_unique(array_merge(array_keys($request->input('kelengkapan', [])), array_keys($request->file('files', []))));
            $validIds = \App\Models\TSyaratSidang::where('STRATA', 'S2')
                ->where('TAHAPAN_SIDANG', $tahapan)->where('ID_PRODI', $prodiIdUpload)
                ->whereIn('id', $syaratIds)->pluck('id')->all();
            if (array_diff($syaratIds, $validIds)) {
                return response()->json(['success' => false, 'message' => 'Persyaratan tidak sesuai dengan tahapan dan prodi mahasiswa.'], 422);
            }
        }

        try {
        \Log::info('saveAllPersyaratan called', ['id_judul' => $idJudul, 'tahapan' => $tahapan, 'kelengkapan' => $request->kelengkapan]);

        // Check if ajuan record exists for this tahapan, if not create it
        $ajuan = \App\Models\TAjuanSidang::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)
            ->first();

        if (!$ajuan) {
            // Get judul information
            $judul = \App\Models\TJudul::find($idJudul);
            if ($judul) {
                $mahasiswa = \App\Models\TUser::find($judul->ID_USER_MHS);

                // t_judul tidak punya ID_PRODI — resolve prodi dari session lalu dari data mahasiswa
                $prodi = null;
                if ($isS2Upload && $mahasiswa) {
                    $prodi = TProdi::where('KODE_PRODI', $mahasiswa->KODE_PRODI)->first();
                } elseif (!empty($user['kode_prodi'])) {
                    $prodi = \App\Models\TProdi::where('KODE_PRODI', $user['kode_prodi'])->first();
                }
                if (!$prodi && $mahasiswa && !empty($mahasiswa->KODE_PRODI)) {
                    $prodi = \App\Models\TProdi::where('KODE_PRODI', $mahasiswa->KODE_PRODI)->first();
                }

                if (!$prodi) {
                    // Kolom ID_PRODI/KODE_PRODI/NAMA_PRODI NOT NULL — jangan insert baris null
                    \Log::error('saveAllPersyaratan: prodi tidak ditemukan, ajuan tidak dibuat', [
                        'id_judul' => $idJudul,
                        'tahapan' => $tahapan,
                        'user_kode_prodi' => $user['kode_prodi'] ?? null,
                        'mhs_kode_prodi' => $mahasiswa ? $mahasiswa->KODE_PRODI : null,
                    ]);
                } else {
                    $ajuan = new \App\Models\TAjuanSidang();
                    $ajuan->ID_USER = $judul->ID_USER_MHS;
                    $ajuan->NIM = ($mahasiswa && $mahasiswa->NIP_NIM) ? $mahasiswa->NIP_NIM : '-';
                    $ajuan->NAMA_MHS = ($mahasiswa && $mahasiswa->NAMA_LENGKAP) ? $mahasiswa->NAMA_LENGKAP : '-';
                    $ajuan->ANGKATAN = $mahasiswa && $mahasiswa->THN_ANGKATAN ? $mahasiswa->THN_ANGKATAN : (int)date('Y');
                    $ajuan->ID_JUDUL = $idJudul;
                    $ajuan->JUDUL = $judul->JUDUL ?: '-';
                    $ajuan->TAHAPAN_SIDANG = $tahapan;
                    $strataFallback = strtoupper((string) ($user['strata'] ?? ''));
                    $ajuan->STRATA = ($mahasiswa && $mahasiswa->STRATA)
                        ? $mahasiswa->STRATA
                        : (in_array($strataFallback, ['S1', 'S2', 'S3'], true) ? $strataFallback : 'S3');
                    $ajuan->STATUS_LULUS = null; // 'dalam proses'
                    $ajuan->STATUS_AJUKAN_MHS = 't';
                    $ajuan->STATUS_AJUKAN_PRODI = 't';
                    $ajuan->ID_PRODI = $prodi->id;
                    $ajuan->KODE_PRODI = $prodi->KODE_PRODI;
                    $ajuan->NAMA_PRODI = $prodi->NAMA_PRODI;
                    $ajuan->TGL_CREATE = date('Y-m-d');
                    $ajuan->TGL_UPDATE = date('Y-m-d');
                    $ajuan->ID_USER_CREATE = $user['id'];
                    $ajuan->NAMA_USER_CREATE = $user['nama_lengkap'] ?? '-';
                    $ajuan->THN_CREATE = date('Y');
                    $ajuan->save();
                    \Log::info('Created ajuan record for tahap', ['id_judul' => $idJudul, 'tahapan' => $tahapan]);
                }
            }
        }

        // Simpan status kelengkapan checkbox
        if ($request->has('kelengkapan')) {
            foreach ($request->kelengkapan as $idSyarat => $status) {
                // Lewati ID non-numerik (placeholder hf_* / dummy di view)
                if (!is_numeric($idSyarat) || (int)$idSyarat <= 0) {
                    \Log::warning('saveAllPersyaratan: skip non-numeric id_syarat', ['id_syarat' => $idSyarat]);
                    continue;
                }
                $syarat = \App\Models\TSyaratSidang::find($idSyarat);
                $tahapSyarat = $syarat ? $syarat->TAHAPAN_SIDANG : $tahapan;

                $cek = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
                    ->where('TAHAPAN_SIDANG', $tahapSyarat)
                    ->where('ID_SYARAT_SIDANG', $idSyarat)
                    ->first();

                if ($cek) {
                    $cek->STATUS_LENGKAP = $status;
                    $cek->TGL_UPDATE = date('Y-m-d');
                    $cek->save();
                    \Log::info('Updated kelengkapan in saveAll', ['id_judul' => $idJudul, 'tahapan' => $tahapSyarat, 'id_syarat' => $idSyarat, 'status' => $status]);
                } else {
                    $newCek = new \App\Models\TCekPersyaratan();
                    $newCek->ID_JUDUL = $idJudul;
                    $newCek->TAHAPAN_SIDANG = $tahapSyarat;
                    $newCek->ID_SYARAT_SIDANG = $idSyarat;
                    $newCek->PERSYARATAN = $syarat ? $syarat->NAMA_PERSYARATAN : ('Persyaratan #' . $idSyarat);
                    $newCek->STATUS_LENGKAP = $status;
                    $newCek->TGL_BUAT = date('Y-m-d');
                    $newCek->save();
                    \Log::info('Inserted kelengkapan in saveAll', ['id_judul' => $idJudul, 'tahapan' => $tahapSyarat, 'id_syarat' => $idSyarat, 'status' => $status]);
                }
            }
        } else {
            \Log::warning('No kelengkapan data in request', ['request' => $request->all()]);
        }

        // Simpan file upload
        if ($request->hasFile('files')) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);

            foreach ($request->file('files') as $idSyarat => $file) {
                if (!is_numeric($idSyarat) || (int)$idSyarat <= 0) {
                    \Log::warning('saveAllPersyaratan: skip non-numeric file id_syarat', ['id_syarat' => $idSyarat]);
                    continue;
                }
                $mimeType = $finfo->file($file->getRealPath());
                if ($mimeType !== 'application/pdf') {
                    return response()->json(['success' => false, 'message' => 'Hanya file PDF yang diizinkan'], 422);
                }
                if ($file->getSize() > $maxUploadKb * 1024) {
                    return response()->json(['success' => false, 'message' => 'Maksimal ukuran file ' . ($maxUploadKb / 1024) . ' MB'], 422);
                }

                $filename = time() . '_' . bin2hex(random_bytes(16)) . '.pdf';
                $path = $file->storeAs('uploads/persyaratan', $filename, 'public');
                if ($path === false) {
                    \Log::error('saveAllPersyaratan: gagal store file', ['id_judul' => $idJudul, 'id_syarat' => $idSyarat]);
                    return response()->json(['success' => false, 'message' => 'Gagal menyimpan file'], 500);
                }
                $linkFile = '/storage/' . $path;

                $syarat = \App\Models\TSyaratSidang::find($idSyarat);
                $tahapSyarat = $syarat ? $syarat->TAHAPAN_SIDANG : $tahapan;

                $cek = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
                    ->where('TAHAPAN_SIDANG', $tahapSyarat)
                    ->where('ID_SYARAT_SIDANG', $idSyarat)
                    ->first();

                if ($cek) {
                    if ($cek->LINK_FILE) {
                        $oldPath = str_replace('/storage/', '', $cek->LINK_FILE);
                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                        }
                    }
                    $cek->LINK_FILE = $linkFile;
                    // JANGAN set STATUS_LENGKAP otomatis, biarkan sesuai dengan checkbox user
                    // $cek->STATUS_LENGKAP = 'y';
                    $cek->TGL_UPDATE = date('Y-m-d');
                    $cek->save();
                } elseif ($isS2Upload) {
                    \App\Models\TCekPersyaratan::create([
                        'ID_JUDUL' => $idJudul,
                        'TAHAPAN_SIDANG' => $tahapan,
                        'ID_SYARAT_SIDANG' => $idSyarat,
                        'PERSYARATAN' => $syarat->NAMA_PERSYARATAN,
                        'STATUS_LENGKAP' => 't',
                        'LINK_FILE' => $linkFile,
                        'TGL_BUAT' => date('Y-m-d'),
                        'TGL_UPDATE' => date('Y-m-d'),
                    ]);
                } else {
                    // Get prodi ID from judul's user for filtering syarat
                    $prodiIdForSave = null;
                    $strataForSave = '';
                    $judulForSave = \App\Models\TJudul::find($idJudul);
                    if ($judulForSave && $judulForSave->user) {
                        $prodiForSave = \App\Models\TProdi::where('kode_prodi', $judulForSave->user->KODE_PRODI)->first();
                        $prodiIdForSave = $prodiForSave?->id;
                        $strataForSave = strtoupper((string) ($judulForSave->user->STRATA ?? ''));
                    }
                    // Fallback: ambil dari t_ajuan_sidang jika user.STRATA kosong
                    if ($strataForSave === '') {
                        $strataForSave = strtoupper((string) (\App\Models\TAjuanSidang::where('ID_JUDUL', $idJudul)
                            ->whereNotNull('STRATA')->value('STRATA') ?? ''));
                    }
                    if ($strataForSave === '') {
                        $strataForSave = strtoupper((string) (session('auth_user.strata') ?? ''));
                    }

                    $allSyaratQuery = \App\Models\TSyaratSidang::where('TAHAPAN_SIDANG', $tahapSyarat)
                        ->where('STATUS_AKTIF', 'AKTIF');
                    if ($prodiIdForSave) {
                        $allSyaratQuery->where('ID_PRODI', $prodiIdForSave);
                    }
                    if ($strataForSave !== '') {
                        $allSyaratQuery->where('STRATA', $strataForSave);
                    }
                    $allSyarat = $allSyaratQuery->get();

                    foreach ($allSyarat as $s) {
                        $newCek = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
                            ->where('TAHAPAN_SIDANG', $tahapSyarat)
                            ->where('ID_SYARAT_SIDANG', $s->id)
                            ->first();
                        if (!$newCek) {
                            $newCek = new \App\Models\TCekPersyaratan();
                            $newCek->ID_JUDUL = $idJudul;
                            $newCek->TAHAPAN_SIDANG = $tahapSyarat;
                            $newCek->ID_SYARAT_SIDANG = $s->id;
                            $newCek->PERSYARATAN = $s->NAMA_PERSYARATAN;
                            // JANGAN set STATUS_LENGKAP otomatis jadi 'y' hanya karena upload file
                            // Status lengkap hanya diset sesuai checkbox user atau default 't'
                            $newCek->STATUS_LENGKAP = 't';
                            $newCek->TGL_BUAT = date('Y-m-d');
                        }
                        if ($s->id == $idSyarat) {
                            $newCek->LINK_FILE = $linkFile;
                            $newCek->TGL_UPDATE = date('Y-m-d');
                        }
                        $newCek->save();
                    }
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Persyaratan berhasil disimpan']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            \Log::error('saveAllPersyaratan failed', [
                'id_judul' => $idJudul,
                'tahapan' => $tahapan,
                'kelengkapan' => $request->kelengkapan,
                'has_files' => $request->hasFile('files'),
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan persyaratan',
                'debug' => $e->getMessage() . ' @ ' . basename($e->getFile()) . ':' . $e->getLine(),
            ], 500);
        }
    }

    /**
     * S2: FS hanya view-only. Mengembalikan response 403 bila role FS
     * mencoba mengubah data sidang S2, null bila tidak diblokir.
     */
    private function fsViewOnlyS2Response($user, $idJudul, $tahapan): ?\Illuminate\Http\JsonResponse
    {
        if (($user['role'] ?? '') !== 'FS') {
            return null;
        }

        $strata = strtoupper(trim((string) (TAjuanSidang::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)->latest('id')->value('STRATA') ?? '')));
        if ($strata === '') {
            $strata = strtoupper(trim((string) (TJudul::find($idJudul)?->user?->STRATA ?? '')));
        }

        if ($strata === 'S2') {
            return response()->json([
                'success' => false,
                'message' => 'Role FS hanya dapat melihat data sidang S2 (view-only), tanpa menyimpan.',
            ], 403);
        }

        return null;
    }

    public function storeTimSidang(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');
        $idJudul = session('active_judul_id');

        $request->validate([
            'id_judul' => 'required',
            'tahapan_sidang' => 'required',
            'status_tim_sidang' => 'required',
            'urutan' => 'nullable',
            'file_penelaah' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            'file_penelaah.max' => 'Maksimal ukuran file 2MB',
        ]);

        $fsBlock = $this->fsViewOnlyS2Response($user, $request->id_judul, $request->tahapan_sidang);
        if ($fsBlock) {
            return $fsBlock;
        }

        // External penguji (manual input)
        $manualNama = $request->input('manual_nama');
        $manualNip = $request->input('manual_nip');

        if ($manualNama) {
            $timNama = $manualNama;
            $timNip = $manualNip ?: '';
            $idUserPenilai = null;
        } else {
            $request->validate([
                'id_user_penilai' => 'required',
            ]);
            $userPenilai = \App\Models\TUser::find($request->id_user_penilai);
            if (!$userPenilai) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
            }
            $timNama = $userPenilai->NAMA_LENGKAP;
            $timNip = $userPenilai->NIP_NIM;
            $idUserPenilai = $request->id_user_penilai;
        }

        // Auto-calculate urutan if not provided
        $urutan = $request->urutan;
        if (!$urutan) {
            $maxUrutan = \App\Models\TTimSidang::where('ID_JUDUL', $request->id_judul)
                ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
                ->max('URUTAN');
            $urutan = ($maxUrutan ?: 0) + 1;
        }

        $timSidang = new \App\Models\TTimSidang();
        $timSidang->ID_JUDUL = $request->id_judul;
        $timSidang->TAHAPAN_SIDANG = $request->tahapan_sidang;
        $timSidang->ID_USER_PENILAI = $idUserPenilai;
        $timSidang->STATUS_TIM_SIDANG = $request->status_tim_sidang;
        $timSidang->NIP = $timNip;
        $timSidang->NAMA = $timNama;
        $timSidang->URUTAN = $urutan;
        $timSidang->ID_SK = $request->id_sk;
        $timSidang->TGL_CREATE = date('Y-m-d');
        $timSidang->TGL_UPDATE = date('Y-m-d');

        // Handle penelaah file upload (stored per tim row in t_tim_sidang)
        if ($request->hasFile('file_penelaah')) {
            $file = $request->file('file_penelaah');
            $filename = time() . '_' . bin2hex(random_bytes(16)) . '.' . $file->getClientOriginalExtension();
            $path = 'penelaah/' . $filename;
            if (!\Illuminate\Support\Facades\Storage::disk('public')->put($path, file_get_contents($file->getRealPath()))) {
                return response()->json(['success' => false, 'message' => 'Gagal mengupload file penelaah'], 500);
            }
            $timSidang->FILE_PENELAAH = '/storage/' . $path;
        }

        $timSidang->save();

        return response()->json([
            'success' => true,
            'message' => 'Tim Pembimbing berhasil ditambahkan',
            'tim' => [
                'id' => $timSidang->id,
                'nip' => $timSidang->NIP,
                'nama' => $timSidang->NAMA,
                'status_tim_sidang' => $timSidang->STATUS_TIM_SIDANG,
                'urutan' => $timSidang->URUTAN,
                'id_sk' => $timSidang->ID_SK,
                'file_penelaah' => $timSidang->FILE_PENELAAH,
            ],
        ]);
    }

    public function updateTimSidang(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'status_tim_sidang' => 'required',
            'urutan' => 'nullable',
            'file_penelaah' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            'file_penelaah.max' => 'Maksimal ukuran file 2MB',
        ]);

        $timSidang = \App\Models\TTimSidang::find($id);
        if (!$timSidang) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $fsBlock = $this->fsViewOnlyS2Response(session('auth_user'), $timSidang->ID_JUDUL, $timSidang->TAHAPAN_SIDANG);
        if ($fsBlock) {
            return $fsBlock;
        }

        // External penguji (manual input)
        $manualNama = $request->input('manual_nama');
        $manualNip = $request->input('manual_nip');

        if ($manualNama) {
            $timNama = $manualNama;
            $timNip = $manualNip ?: '';
            $idUserPenilai = null;
        } else {
            $request->validate([
                'id_user_penilai' => 'required',
            ]);
            $userPenilai = \App\Models\TUser::find($request->id_user_penilai);
            if (!$userPenilai) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
            }
            $timNama = $userPenilai->NAMA_LENGKAP;
            $timNip = $userPenilai->NIP_NIM;
            $idUserPenilai = $request->id_user_penilai;
        }

        $urutan = $request->urutan;
        if (!$urutan) {
            $maxUrutan = \App\Models\TTimSidang::where('ID_JUDUL', $timSidang->ID_JUDUL)
                ->where('TAHAPAN_SIDANG', $timSidang->TAHAPAN_SIDANG)
                ->max('URUTAN');
            $urutan = ($maxUrutan ?: 0) + 1;
        }

        $timSidang->ID_USER_PENILAI = $idUserPenilai;
        $timSidang->STATUS_TIM_SIDANG = $request->status_tim_sidang;
        $timSidang->NIP = $timNip;
        $timSidang->NAMA = $timNama;
        $timSidang->URUTAN = $urutan;
        $timSidang->ID_SK = $request->id_sk;
        $timSidang->TGL_UPDATE = date('Y-m-d');

        // Handle penelaah file upload (stored per tim row in t_tim_sidang)
        if ($request->hasFile('file_penelaah')) {
            $file = $request->file('file_penelaah');
            $filename = time() . '_' . bin2hex(random_bytes(16)) . '.' . $file->getClientOriginalExtension();
            $path = 'penelaah/' . $filename;
            if (!\Illuminate\Support\Facades\Storage::disk('public')->put($path, file_get_contents($file->getRealPath()))) {
                return response()->json(['success' => false, 'message' => 'Gagal mengupload file penelaah'], 500);
            }
            $timSidang->FILE_PENELAAH = '/storage/' . $path;
        }

        $timSidang->save();

        return response()->json([
            'success' => true,
            'message' => 'Tim Pembimbing berhasil diupdate',
            'tim' => [
                'id' => $timSidang->id,
                'nip' => $timSidang->NIP,
                'nama' => $timSidang->NAMA,
                'status_tim_sidang' => $timSidang->STATUS_TIM_SIDANG,
                'urutan' => $timSidang->URUTAN,
                'id_sk' => $timSidang->ID_SK,
                'file_penelaah' => $timSidang->FILE_PENELAAH,
            ],
        ]);
    }

    public function deleteTimSidang($id)
    {
        if ((session('auth_user.role') ?? '') !== 'TU Prodi') {
            abort(403, 'Hanya TU Prodi yang dapat menghapus tim sidang.');
        }

        $timSidang = \App\Models\TTimSidang::find($id);
        if (!$timSidang) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $timSidang->delete();

        return response()->json(['success' => true, 'message' => 'Tim Pembimbing berhasil dihapus']);
    }

    public function getNextSkNumber($tahapan, \Illuminate\Http\Request $request)
    {
        $nextNumber = DB::table('t_sk')->where('TAHAPAN_SIDANG', $tahapan)->count() + 1;
        $idJudul = $request->query('id_judul');
        $prodi = '322';
        if ($idJudul) {
            $prodi = DB::table('t_judul')
                ->join('t_user', 't_judul.nim', '=', 't_user.nip_nim')
                ->where('t_judul.id', $idJudul)
                ->value('t_user.kode_prodi') ?? '322';
        }
        $tahun = date('Y');
        $noSk = 'SK/' . strtoupper(str_replace(' ', '', $tahapan)) . '/' . $prodi . $tahun . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'no_sk' => $noSk,
        ]);
    }

    public function storeSk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'id_judul' => 'required|exists:t_judul,id',
            'tahapan_sidang' => 'required',
        ]);

        $fsBlock = $this->fsViewOnlyS2Response(session('auth_user'), $request->id_judul, $request->tahapan_sidang);
        if ($fsBlock) {
            return $fsBlock;
        }

        $noSk = $request->no_sk;
        if (!$noSk) {
            $nextNumber = DB::table('t_sk')->where('TAHAPAN_SIDANG', $request->tahapan_sidang)->count() + 1;
            $prodi = DB::table('t_judul')
                ->join('t_user', 't_judul.nim', '=', 't_user.nip_nim')
                ->where('t_judul.id', $request->id_judul)
                ->value('t_user.kode_prodi') ?? '322';
            $tahun = date('Y');
            $noSk = 'SK/' . strtoupper(str_replace(' ', '', $request->tahapan_sidang)) . '/' . $prodi . $tahun . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        $skId = DB::table('t_sk')->insertGetId([
            'NO_SK' => $noSk,
            'ID_JUDUL' => $request->id_judul,
            'TAHAPAN_SIDANG' => $request->tahapan_sidang,
            'TGL_BUAT' => now(),
            'TGL_UPDATE' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'No SK berhasil dibuat.',
                'sk' => ['id' => $skId, 'no_sk' => $noSk],
            ]);
        }

        return redirect()->back()->with('success', 'No SK berhasil dibuat.');
    }

    public function storeJadwal(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');

        $request->validate([
            'id_judul' => 'required',
            'tahapan_sidang' => 'required',
            'tgl_sidang' => 'required|date',
            'waktu_sidang' => 'required',
        ]);

        $idJudul = $request->id_judul;
        $tahapan = $request->tahapan_sidang;
        $tglSidang = $request->tgl_sidang;
        $waktuSidang = $request->waktu_sidang;

        // S2: FS hanya view-only, tidak boleh menyimpan/meng-approve jadwal.
        $fsBlock = $this->fsViewOnlyS2Response($user, $idJudul, $tahapan);
        if ($fsBlock) {
            return $fsBlock;
        }

        $isS2 = in_array($tahapan, ['Tesis I', 'Tesis II'], true);
        if ($isS2) {
            abort_unless(in_array($user['role'] ?? '', ['TU Prodi', 'Admin', 'Mahasiswa'], true), 403);
            if (($user['role'] ?? '') === 'Mahasiswa') {
                abort_unless(TJudul::where('id', $idJudul)->where('ID_USER_MHS', $user['id'])->exists(), 403);
                $request->validate([
                    'is_ajukan_fs' => 'prohibited',
                    'no_surat_undangan' => 'prohibited',
                    'tgl_surat_undangan' => 'prohibited',
                    'no_sk_kelulusan' => 'prohibited',
                ]);
                if (TAjuanSidang::where('ID_JUDUL', $idJudul)->where('TAHAPAN_SIDANG', $tahapan)->latest('id')->first()?->STATUS_AJUKAN_PRODI === 'y') {
                    return response()->json(['success' => false, 'message' => 'Jadwal sudah di-approve TU Prodi dan tidak dapat diubah mahasiswa.'], 403);
                }
            }
            $request->validate([
                'status_lulus' => 'prohibited',
                'is_ajukan_kpps' => 'prohibited',
                'waktu_sidang' => 'date_format:H:i,H:i:s',
                'waktu_selesai' => 'nullable|date_format:H:i,H:i:s',
                'tgl_surat_undangan' => 'nullable|date',
                'no_surat_undangan' => 'nullable|string|max:250',
            ]);
            if ($request->filled('waktu_selesai') && strtotime($request->waktu_selesai) <= strtotime($request->waktu_sidang)) {
                return response()->json(['success' => false, 'message' => 'Waktu selesai harus setelah waktu mulai.'], 422);
            }
            $ajuanS2 = TAjuanSidang::where('ID_JUDUL', $idJudul)->where('TAHAPAN_SIDANG', $tahapan)->latest('id')->first();
            if ($request->is_ajukan_fs && ($ajuanS2?->STATUS_AJUKAN_MHS ?? 't') !== 'y') {
                return response()->json(['success' => false, 'message' => 'Mahasiswa belum mengajukan sidang.'], 422);
            }
        }

        // Check schedule conflicts for pembimbing/penguji (2 hour gap)
        $timSidang = \App\Models\TTimSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->get();

        if ($timSidang->isNotEmpty()) {
            $penilaiIds = $timSidang->pluck('id_user_penilai')->unique()->filter()->toArray();

            if (!empty($penilaiIds)) {
                $waktuMulai = date('H:i:s', strtotime($waktuSidang . ' -2 hours'));
                $waktuAkhir = date('H:i:s', strtotime($waktuSidang . ' +2 hours'));

                // Exclude current ajuan record(s) from conflict check
                $currentIds = \App\Models\TAjuanSidang::where('id_judul', $idJudul)
                    ->where('tahapan_sidang', $tahapan)
                    ->pluck('id')
                    ->toArray();

                $conflict = \App\Models\TAjuanSidang::where('TGL_SIDANG', $tglSidang)
                    ->whereNotIn('id', $currentIds)
                    ->whereExists(function ($q) use ($penilaiIds) {
                        $q->select(\Illuminate\Support\Facades\DB::raw(1))
                            ->from('t_tim_sidang')
                            ->whereColumn('t_tim_sidang.id_judul', 't_ajuan_sidang.id_judul')
                            ->whereColumn('t_tim_sidang.tahapan_sidang', 't_ajuan_sidang.tahapan_sidang')
                            ->whereIn('t_tim_sidang.id_user_penilai', $penilaiIds);
                    })
                    ->where('WAKTU_SIDANG', '>=', $waktuMulai)
                    ->where('WAKTU_SIDANG', '<=', $waktuAkhir)
                    ->first();

                if ($conflict) {
                    $conflictNama = $conflict->NAMA_MHS ?? 'mahasiswa lain';
                    return response()->json([
                        'success' => false,
                        'message' => "Jadwal bentrok! Terdapat jadwal sidang lain pada {$tglSidang} pukul {$conflict->WAKTU_SIDANG} untuk {$conflictNama} dengan pembimbing/penguji yang sama. Minimal jeda 2 jam."
                    ]);
                }
            }
        }

        // Find existing ajuan or create new one
        // If id_ajuan provided, update that specific record
        if ($request->filled('id_ajuan')) {
            $ajuan = TAjuanSidang::where('ID_JUDUL', $idJudul)->where('TAHAPAN_SIDANG', $tahapan)->find($request->id_ajuan);
            if (!$ajuan) {
                return response()->json(['success' => false, 'message' => 'Data jadwal tidak ditemukan'], 404);
            }
        } else {
            // If existing record has 'tidak lulus', create a new record (keep old for audit)
            $ajuan = TAjuanSidang::where('id_judul', $idJudul)
                ->where('tahapan_sidang', $tahapan)
                ->latest('id')
                ->first();

            $isTidakLulus = $ajuan && ($ajuan->STATUS_LULUS === 'tidak lulus');

            if (!$ajuan || $isTidakLulus) {
            // Get judul info
            $judul = TJudul::find($idJudul);
            if (!$judul) {
                return response()->json(['success' => false, 'message' => 'Judul tidak ditemukan'], 404);
            }

            // Get user mahasiswa from judul
            $mahasiswa = \App\Models\TUser::find($judul->id_user_mhs);
            $prodi = \App\Models\TProdi::where('KODE_PRODI', $mahasiswa->KODE_PRODI ?? $user['kode_prodi'])->first();

            // Kolom PK t_user fisiknya "id" (huruf kecil), jadi akses ->ID akan
            // selalu null dan ajuan tercatat atas nama operator, bukan mahasiswa.
            $strata = $mahasiswa->STRATA
                ?: TAjuanSidang::where('ID_JUDUL', $idJudul)->whereNotNull('STRATA')->value('STRATA')
                ?: ($user['strata'] ?? null);

            if (!$strata) {
                return response()->json([
                    'success' => false,
                    'message' => 'Strata mahasiswa ' . ($mahasiswa->NAMA_LENGKAP ?? $judul->NIM) . ' belum diisi. Lengkapi Strata di Data Master User dulu, lalu simpan jadwal lagi.',
                ], 422);
            }

            $ajuan = new TAjuanSidang();
            $ajuan->ID_USER = $mahasiswa->id ?? $user['id'];
            $ajuan->NIM = $mahasiswa->NIP_NIM ?? $user['nip_nim'];
            $ajuan->NAMA_MHS = $mahasiswa->NAMA_LENGKAP ?? $user['nama_lengkap'];
            $ajuan->ANGKATAN = $mahasiswa->THN_ANGKATAN ?? date('Y');
            $ajuan->ID_JUDUL = $idJudul;
            $ajuan->JUDUL = $judul->Judul;
            $ajuan->TAHAPAN_SIDANG = $tahapan;
            $ajuan->STRATA = $strata;
            $ajuan->STATUS_AJUKAN_MHS = 't';
            $ajuan->TGL_CREATE = now();
            $ajuan->ID_USER_CREATE = $user['id'];
            $ajuan->NAMA_USER_CREATE = $user['nama_lengkap'];
            $ajuan->THN_CREATE = date('Y');
            $ajuan->ID_PRODI = $prodi->id ?? null;
            $ajuan->KODE_PRODI = $mahasiswa->KODE_PRODI ?? $user['kode_prodi'];
            $ajuan->NAMA_PRODI = $prodi->NAMA_PRODI ?? $user['nama_prodi'];
            }
        }

        // Update jadwal fields
        $ajuan->TGL_SIDANG = $tglSidang;
        $ajuan->WAKTU_SIDANG = $waktuSidang;

        // Jika yang simpan bukan Mahasiswa (mis. TU Prodi), otomatis set STATUS_AJUKAN_MHS = 'y'
        // sehingga jadwal langsung terlihat di sisi mahasiswa
        if (!$isS2 && ($user['role'] ?? '') !== 'Mahasiswa') {
            $ajuan->STATUS_AJUKAN_MHS = 'y';
        }

        if ($request->filled('waktu_selesai')) {
            $ajuan->WAKTU_SELESAI = $request->waktu_selesai;
        }
        if ($request->filled('jenis_sidang')) {
            $ajuan->JENIS_SIDANG = $request->jenis_sidang;
        }

        if ($request->filled('ruang_sidang')) {
            $ajuan->RUANG_SIDANG = $request->ruang_sidang;
        }
        if ($request->filled('tgl_surat_undangan')) {
            $ajuan->TGL_UNDANGAN = $request->tgl_surat_undangan;
        }
        if ($request->filled('no_surat_undangan')) {
            $ajuan->NO_UNDANGAN = $request->no_surat_undangan;
        }
        if ($request->filled('tgl_surat_penelaah')) {
            $ajuan->TGL_PENELAAH = $request->tgl_surat_penelaah;
        }
        if ($request->filled('no_surat_penelaah')) {
            $ajuan->NO_SURAT_PENELAAH = $request->no_surat_penelaah;
        }
        if ($request->filled('tgl_pemanggilan_penilai')) {
            $ajuan->TGL_PENGUMPULAN = $request->tgl_pemanggilan_penilai;
        }
        if ($request->filled('tgl_hasil_penelahan')) {
            $ajuan->TGL_HASIL_PENELAHAN = $request->tgl_hasil_penelahan;
        }
        if ($request->filled('email_surat')) {
            $ajuan->EMAIL_SURAT = $request->email_surat;
        }

        if ($request->filled('no_sk_kelulusan')) {
            $ajuan->SK_LULUS = $request->no_sk_kelulusan;
        }
        if ($request->filled('status_lulus')) {
            $ajuan->STATUS_LULUS = $request->status_lulus;
        }

        if ($request->is_ajukan_fs && (!$isS2 || $ajuan->STATUS_AJUKAN_PRODI !== 'y')) {
            $ajuan->STATUS_AJUKAN_PRODI = 'y';
            $ajuan->STATUS_AJUKAN_MHS = 'y';
            $ajuan->STATUS_LULUS = 'diajukan';
        }

        if ($request->is_ajukan_kpps) {
            $ajuan->STATUS_AJUKAN_KPPS = 'y';
            $ajuan->TGL_AJUKAN_KPPS = now(); // jam klik ajukan. Jeda 15m di-handle oleh auto-approve
            $ajuan->STATUS_AJUKAN_PRODI = 'y';
            $ajuan->STATUS_AJUKAN_MHS = 'y';
            $ajuan->STATUS_SUBMIT = 'y';
            $ajuan->STATUS_LULUS = 'diajukan';
        }

        $ajuan->TGL_UPDATE = now();
        $ajuan->save();

        if ($request->is_ajukan_fs) {
            $msg = strtoupper((string) $ajuan->STRATA) === 'S2'
                ? 'Jadwal sidang berhasil di-approve. Status: Terjadwal.'
                : 'Berhasil diajukan ke FS';
            return response()->json(['success' => true, 'message' => $msg]);
        }

        if ($request->is_ajukan_kpps) {
            $antre = $this->kirimNotifikasiKeKpps($ajuan);

            if (isset($antre['error'])) {
                return response()->json(['success' => true, 'message' => 'Berhasil diajukan ke KPPS. (Email antrean gagal dibuat: ' . $antre['error'] . ')']);
            }

            $msg = 'Berhasil diajukan ke KPPS. ' . $antre['queued'] . ' email masuk antrean, akan dikirim otomatis.';
            if (!empty($antre['skipped_names'])) {
                $msg .= ' Dilewati: ' . implode(', ', $antre['skipped_names']);
            }

            return response()->json(['success' => true, 'message' => $msg, 'queued' => $antre['queued']]);
        }

        return response()->json(['success' => true, 'message' => 'Jadwal sidang berhasil disimpan']);
    }

    private function kirimNotifikasiKeKpps(TAjuanSidang $ajuan): array
    {
        try {
            // Hanya masuk antrean — kirim oleh cron email:kirim-antrian
            return app(\App\Http\Controllers\Sidang\NotifikasiApproveController::class)->antreKeKpps($ajuan);
        } catch (\Throwable $e) {
            Log::error('storeJadwal: Gagal antre notifikasi KPPS - ' . $e->getMessage());

            return ['error' => $e->getMessage()];
        }
    }

    public function deleteJadwal(\Illuminate\Http\Request $request, $id)
    {
        if ((session('auth_user.role') ?? '') !== 'TU Prodi') {
            abort(403, 'Hanya TU Prodi yang dapat menghapus jadwal sidang.');
        }

        $ajuan = TAjuanSidang::find((int) $id);

        if (!$ajuan) {
            return response()->json(['success' => false, 'message' => 'Data jadwal tidak ditemukan'], 404);
        }

        $sl = $ajuan->STATUS_LULUS ?? $ajuan->status_lulus ?? null;
        $mhs = $ajuan->STATUS_AJUKAN_MHS ?? $ajuan->status_ajukan_mhs ?? 't';
        $prodi = $ajuan->STATUS_AJUKAN_PRODI ?? $ajuan->status_ajukan_prodi ?? 't';

        $isDiprosesTuProdi = (empty($sl) || strtolower($sl) === 'diajukan')
            && $mhs === 'y'
            && (empty($prodi) || $prodi === 't');

        if (!$isDiprosesTuProdi) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal sidang hanya dapat dihapus jika statusnya "Diproses di TU Prodi".'
            ], 422);
        }

        TPenilaian::where('ID_AJUAN', $ajuan->id)->delete();

        $ajuan->delete();

        return response()->json(['success' => true, 'message' => 'Jadwal sidang beserta penilaian terkait berhasil dihapus.']);
    }

    public function storeJudul(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');

        $request->validate([
            'judul' => 'required|string|max:500',
            'abstrak' => 'required|string|max:1000',
        ]);

        $prodi = \App\Models\TProdi::where('KODE_PRODI', $user['kode_prodi'])->first();
        if (!$prodi) {
            return redirect()->back()->with('error', 'Prodi tidak ditemukan. Periksa data prodi akun Anda.');
        }

        $existingJudul = DB::table('t_judul')
            ->where(function ($q) use ($user) {
                $q->where('NIM', $user['nip_nim'])
                  ->orWhere('ID_USER_MHS', $user['id']);
            })
            ->first();
        if ($existingJudul) {
            return redirect()->back()->with('error', 'Anda sudah memiliki judul.');
        }

        // Tahapan awal mengikuti strata mahasiswa (master t_tahapan).
        $strataMhs = strtoupper((string) ($user['strata'] ?? ''));
        if (!in_array($strataMhs, ['S1', 'S2', 'S3'], true)) {
            $strataMhs = 'S3';
        }
        $tahapanAwal = ['S1' => 'TA I', 'S2' => 'Tesis I'][$strataMhs] ?? 'tahap I';

        try {
            $judulId = null;
            DB::transaction(function () use ($request, $user, $prodi, $strataMhs, $tahapanAwal, &$judulId) {
                $judul = TJudul::create([
                    'JUDUL' => $request->judul,
                    'ABSTRAK' => $request->abstrak,
                    'ID_USER_MHS' => $user['id'],
                    'NIM' => $user['nip_nim'],
                    'THN_CREATE' => date('Y'),
                    'TGL_CREATE' => now(),
                    'TGL_UPDATE' => now(),
                ]);
                $judulId = $judul->id;

                // Mahasiswa: belum diajukan ke TU Prodi (status list = "Belum diajukan")
                // Baru jadi "Diproses di TU Prodi" setelah mahasiswa klik Ajukan
                DB::table('t_ajuan_sidang')->insert([
                    'ID_USER' => $user['id'],
                    'NIM' => $user['nip_nim'],
                    'NAMA_MHS' => $user['nama_lengkap'],
                    'ANGKATAN' => $user['thn_angkatan'] ?? date('Y'),
                    'ID_JUDUL' => $judul->id,
                    'JUDUL' => $request->judul,
                    'TAHAPAN_SIDANG' => $tahapanAwal,
                    'STRATA' => $strataMhs,
                    'STATUS_AJUKAN_MHS' => 't',
                    'STATUS_AJUKAN_PRODI' => 't',
                    'TGL_CREATE' => now(),
                    'TGL_UPDATE' => now(),
                    'ID_USER_CREATE' => $user['id'],
                    'NAMA_USER_CREATE' => $user['nama_lengkap'],
                    'THN_CREATE' => date('Y'),
                    'ID_PRODI' => $prodi->id,
                    'KODE_PRODI' => $prodi->KODE_PRODI,
                    'NAMA_PRODI' => $prodi->NAMA_PRODI,
                ]);
            });
        } catch (\Throwable $e) {
            \Log::error('storeJudul mahasiswa gagal', ['error' => $e->getMessage(), 'user' => $user['id'] ?? null]);
            return redirect()->back()->with('error', 'Gagal menambah judul: ' . $e->getMessage());
        }

        session(['active_judul_id' => $judulId]);

        return redirect()->back()->with('success', 'Judul berhasil ditambahkan.');
    }

    public function ubahJudul($idJudul)
    {
        $user = session('auth_user');
        
        // Get current judul from t_judul
        $judul = DB::table('t_judul')
            ->select('t_judul.ID as id_judul', 't_judul.JUDUL as Judul', 't_user.NAMA_LENGKAP as nama_mhs', 't_user.NIP_NIM as Nim')
            ->join('t_user', 't_judul.NIM', '=', 't_user.NIP_NIM')
            ->where('t_judul.ID', $idJudul)
            ->where('t_judul.ID_USER_MHS', $user['id'])
            ->first();
        
        if (!$judul) {
            abort(404);
        }

        // Get judul history from t_judul_temp
        $history = DB::table('t_judul_temp')
            ->select('ID_JUDUL', 'JUDUL as judul_lama', 'JUDUL_BARU as judul_baru', 'TAHAP_PERUBAHAN as tahap', 'ALASAN_PERUBAHAN as alasan', 'TGL_CREATE as tgl_create')
            ->where('ID_JUDUL', $idJudul)
            ->orderBy('ID', 'desc')
            ->get();

        return view('mahasiswa.ubah-judul', compact('judul', 'history'));
    }

    public function ajukanProdi(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'id_judul' => 'required',
            'tahapan_sidang' => 'required|string',
        ]);

        $user = session('auth_user');

        $ajuan = TAjuanSidang::where('ID_JUDUL', $request->id_judul)
            ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
            ->latest('id')
            ->first();

        if (!$ajuan) {
            return response()->json(['error' => 'Data jadwal tidak ditemukan'], 404);
        }

        $ajuan->STATUS_AJUKAN_MHS = 'y';
        if ($ajuan->STATUS_LULUS === 'tidak lulus') {
            $ajuan->STATUS_LULUS = null;
        }
        $ajuan->TGL_UPDATE = now();
        $ajuan->save();

        return response()->json(['success' => true, 'message' => 'Berhasil diajukan ke Program Studi']);
    }
}