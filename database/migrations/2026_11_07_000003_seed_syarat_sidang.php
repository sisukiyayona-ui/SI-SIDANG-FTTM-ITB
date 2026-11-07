<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $prodiByKode = DB::table('t_prodi')
            ->get(['id', 'KODE_PRODI'])
            ->keyBy(fn ($p) => (string) $p->KODE_PRODI);

        foreach ($this->rows() as [$id, $nama, $kodeProdi, $fallbackProdiId, $namaProdi, $tahapan, $status, $strata, $tglCreate, $tglUpdate]) {
            $prodiId = $prodiByKode[(string) $kodeProdi]->id ?? $fallbackProdiId;

            DB::table('t_syarat_sidang')->updateOrInsert(
                ['id' => $id],
                [
                    'NAMA_PERSYARATAN' => $nama,
                    'ID_PRODI' => $prodiId,
                    'KODE_PRODI' => $kodeProdi,
                    'NAMA_PRODI' => $namaProdi,
                    'TAHAPAN_SIDANG' => $tahapan,
                    'STATUS_AKTIF' => $status,
                    'STRATA' => $strata,
                    'TGL_CREATE' => $tglCreate,
                    'TGL_UPDATE' => $tglUpdate,
                ]
            );
        }
    }

    public function down(): void
    {
        $ids = array_column($this->rows(), 0);

        DB::table('t_syarat_sidang')->whereIn('id', $ids)->delete();
    }

    private function rows(): array
    {
        return [
            [42, 'persyaratan ujian kualifikasi', '323', 336, 'Teknik Geofisika', 'tahap I', 'AKTIF', 'S3', null, null],
            [43, 'persyaratan ujian proposal 1', '323', 336, 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-09-23', null],
            [44, 'persyaratan ujian proposal 2', '323', 336, 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-09-23', null],
            [45, 'persyaratan SK I', '323', 336, 'Teknik Geofisika', 'SK I', 'AKTIF', 'S3', '2026-09-23', null],
            [46, 'persyaratan SK I 2', '323', 336, 'Teknik Geofisika', 'SK I', 'AKTIF', 'S3', '2026-09-23', null],
            [47, 'persyaratan SK II 1', '323', 336, 'Teknik Geofisika', 'SK II', 'AKTIF', 'S3', '2026-09-23', null],
            [48, 'persyaratan SK II 2', '323', 336, 'Teknik Geofisika', 'SK II', 'AKTIF', 'S3', '2026-09-23', null],
            [49, 'persyaratan SK III 1', '323', 336, 'Teknik Geofisika', 'SK III', 'AKTIF', 'S3', '2026-09-23', null],
            [50, 'persyaratan SK III 2', '323', 336, 'Teknik Geofisika', 'SK III', 'AKTIF', 'S3', '2026-09-23', '2026-09-23'],
            [51, 'persyaratan SK IV 1', '323', 336, 'Teknik Geofisika', 'SK IV', 'AKTIF', 'S3', '2026-09-23', null],
            [52, 'persyaratan SK IV 2', '323', 336, 'Teknik Geofisika', 'SK IV', 'AKTIF', 'S3', '2026-09-23', null],
            [53, 'persyaratan sidang terbuka/tertutup 1', '323', 336, 'Teknik Geofisika', 'tahap IV', 'AKTIF', 'S3', '2026-09-23', null],
            [54, 'persyaratan sidang terbuka/tertutup 2', '323', 336, 'Teknik Geofisika', 'tahap IV', 'AKTIF', 'S3', '2026-09-23', '2026-09-23'],
            [55, 'persyaratan ujian kualifikasi', '325', 338, 'Teknik Metalurgi', 'tahap I', 'AKTIF', 'S3', null, null],
            [56, 'persyaratan ujian proposal 1', '325', 338, 'Teknik Metalurgi', 'tahap II', 'AKTIF', 'S3', null, null],
            [57, 'persyaratan ujian proposal 2', '325', 338, 'Teknik Metalurgi', 'tahap II', 'AKTIF', 'S3', null, null],
            [58, 'persyaratan SK I', '325', 338, 'Teknik Metalurgi', 'SK I', 'AKTIF', 'S3', null, null],
            [59, 'persyaratan SK I 2', '325', 338, 'Teknik Metalurgi', 'SK I', 'AKTIF', 'S3', null, null],
            [60, 'persyaratan SK II 1', '325', 338, 'Teknik Metalurgi', 'SK II', 'AKTIF', 'S3', null, null],
            [61, 'persyaratan SK II 2', '325', 338, 'Teknik Metalurgi', 'SK II', 'AKTIF', 'S3', null, null],
            [62, 'persyaratan SK III 1', '325', 338, 'Teknik Metalurgi', 'SK III', 'AKTIF', 'S3', null, null],
            [63, 'persyaratan SK III 2', '325', 338, 'Teknik Metalurgi', 'SK III', 'AKTIF', 'S3', null, null],
            [64, 'persyaratan SK IV 1', '325', 338, 'Teknik Metalurgi', 'SK IV', 'AKTIF', 'S3', null, null],
            [65, 'persyaratan SK IV 2', '325', 338, 'Teknik Metalurgi', 'SK IV', 'AKTIF', 'S3', null, null],
            [66, 'persyaratan sidang terbuka/tertutup 1', '325', 338, 'Teknik Metalurgi', 'tahap IV', 'AKTIF', 'S3', null, null],
            [67, 'persyaratan sidang terbuka/tertutup 2', '325', 338, 'Teknik Metalurgi', 'tahap IV', 'AKTIF', 'S3', null, null],
            [70, 'persyaratan ujian kualifikasi', '321', 334, 'Rekayasa Pertambangan', 'tahap I', 'AKTIF', 'S3', null, null],
            [71, 'persyaratan ujian proposal 1', '321', 334, 'Rekayasa Pertambangan', 'tahap II', 'AKTIF', 'S3', null, null],
            [72, 'persyaratan ujian proposal 2', '321', 334, 'Rekayasa Pertambangan', 'tahap II', 'AKTIF', 'S3', null, null],
            [73, 'persyaratan SK I', '321', 334, 'Rekayasa Pertambangan', 'SK I', 'AKTIF', 'S3', null, null],
            [74, 'persyaratan SK I 2', '321', 334, 'Rekayasa Pertambangan', 'SK I', 'AKTIF', 'S3', null, null],
            [75, 'persyaratan SK II 1', '321', 334, 'Rekayasa Pertambangan', 'SK II', 'AKTIF', 'S3', null, null],
            [76, 'persyaratan SK II 2', '321', 334, 'Rekayasa Pertambangan', 'SK II', 'AKTIF', 'S3', null, null],
            [77, 'persyaratan SK III 1', '321', 334, 'Rekayasa Pertambangan', 'SK III', 'AKTIF', 'S3', null, null],
            [78, 'persyaratan SK III 2', '321', 334, 'Rekayasa Pertambangan', 'SK III', 'AKTIF', 'S3', null, null],
            [79, 'persyaratan SK IV 1', '321', 334, 'Rekayasa Pertambangan', 'SK IV', 'AKTIF', 'S3', null, null],
            [80, 'persyaratan SK IV 2', '321', 334, 'Rekayasa Pertambangan', 'SK IV', 'AKTIF', 'S3', null, null],
            [81, 'persyaratan sidang terbuka/tertutup 1', '321', 334, 'Rekayasa Pertambangan', 'tahap IV', 'AKTIF', 'S3', null, null],
            [82, 'persyaratan sidang terbuka/tertutup 2', '321', 334, 'Rekayasa Pertambangan', 'tahap IV', 'AKTIF', 'S3', null, null],
            [85, 'persyaratan ujian kualifikasi', '322', 335, 'Teknik Perminyakan', 'tahap I', 'AKTIF', 'S3', null, null],
            [86, 'persyaratan ujian proposal 1', '322', 335, 'Teknik Perminyakan', 'tahap II', 'AKTIF', 'S3', null, null],
            [87, 'persyaratan ujian proposal 2', '322', 335, 'Teknik Perminyakan', 'tahap II', 'AKTIF', 'S3', null, null],
            [88, 'persyaratan SK I', '322', 335, 'Teknik Perminyakan', 'SK I', 'AKTIF', 'S3', null, null],
            [89, 'persyaratan SK I 2', '322', 335, 'Teknik Perminyakan', 'SK I', 'AKTIF', 'S3', null, null],
            [90, 'persyaratan SK II 1', '322', 335, 'Teknik Perminyakan', 'SK II', 'AKTIF', 'S3', null, null],
            [91, 'persyaratan SK II 2', '322', 335, 'Teknik Perminyakan', 'SK II', 'AKTIF', 'S3', null, null],
            [92, 'persyaratan SK III 1', '322', 335, 'Teknik Perminyakan', 'SK III', 'AKTIF', 'S3', null, null],
            [93, 'persyaratan SK III 2', '322', 335, 'Teknik Perminyakan', 'SK III', 'AKTIF', 'S3', null, null],
            [94, 'persyaratan SK IV 1', '322', 335, 'Teknik Perminyakan', 'SK IV', 'AKTIF', 'S3', null, null],
            [95, 'persyaratan SK IV 2', '322', 335, 'Teknik Perminyakan', 'SK IV', 'AKTIF', 'S3', null, null],
            [96, 'persyaratan sidang terbuka/tertutup 1', '322', 335, 'Teknik Perminyakan', 'tahap IV', 'AKTIF', 'S3', null, null],
            [97, 'persyaratan sidang terbuka/tertutup 2', '322', 335, 'Teknik Perminyakan', 'tahap IV', 'AKTIF', 'S3', null, null],
            [100, 'persyaratan ujian proposal 3_1', '323', 336, 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-09-24', '2026-09-24'],
            [101, 'Persyaratan Sidang S3 - Test', '121', 208, 'Teknik Pertambangan', 'tahap IV', 'AKTIF', 'S3', '2026-09-24', '2026-09-24'],
            [102, 'ujian proposal', '323', 336, 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-09-24', null],
            [103, 'Bukti Publikasi', '321', 334, 'Rekayasa Pertambangan', 'TA 1', 'NON AKTIF', 'S2', '2026-09-24', '2026-09-24'],
            [104, 'Persyaratan Ujian Proposal', '321', 334, 'Rekayasa Pertambangan', 'tahap II', 'AKTIF', 'S3', '2026-09-24', '2026-09-24'],
            [105, 'Persyaratan Ujian Proposal S3 baru', '325', 338, 'Teknik Metalurgi', 'tahap I', 'AKTIF', 'S3', '2026-09-24', null],
            [106, 'Persyaratan Ujian Proposal - Test1', '323', 336, 'Teknik Geofisika', 'SK IV', 'NON AKTIF', 'S3', '2026-09-24', '2026-10-02'],
            [107, 'Ujian Proposal 1', '321', 334, 'Rekayasa Pertambangan', '', 'AKTIF', '', '2026-09-24', null],
            [108, 'Ujian Proposal321', '321', 334, 'Rekayasa Pertambangan', 'tahap II', 'NON AKTIF', 'S3', '2026-09-24', '2026-10-02'],
            [109, 'Ujian Proposal 1', '321', 334, 'Rekayasa Pertambangan', 'tahap II', 'NON AKTIF', 'S3', '2026-09-24', '2026-10-02'],
            [110, 'Ujian Proposal 2', '322', 335, 'Teknik Perminyakan', 'tahap II', 'NON AKTIF', 'S3', '2026-09-24', '2026-10-02'],
            [111, 'Ujian Proposal 8', '323', 336, 'Teknik Geofisika', 'tahap II', 'NON AKTIF', 'S3', '2026-09-24', '2026-10-02'],
            [112, 'test', '323', 336, 'Teknik Geofisika', 'tahap I', 'NON AKTIF', 'S3', '2026-09-24', '2026-09-25'],
            [113, 'Persyaratan Test Ujian Kualifikasi', '323', 336, 'Teknik Geofisika', 'tahap I', 'NON AKTIF', 'S3', '2026-09-25', '2026-09-25'],
            [114, 'Persyaratan Test Ujian Proposal', '323', 336, 'Teknik Geofisika', 'tahap II', 'NON AKTIF', 'S3', '2026-09-25', '2026-09-25'],
            [115, 'Persyaratan Test SK I', '323', 336, 'Teknik Geofisika', 'SK I', 'NON AKTIF', 'S3', '2026-09-25', '2026-09-25'],
            [116, 'Persyaratan Test SK I', '323', 336, 'Teknik Geofisika', 'SK II', 'NON AKTIF', 'S3', '2026-09-25', '2026-09-25'],
            [117, 'test2', '323', 336, 'Teknik Geofisika', 'tahap II', 'NON AKTIF', 'S3', '2026-09-27', '2026-10-02'],
            [119, 'Test', '323', 336, 'Teknik Geofisika', 'tahap II', 'NON AKTIF', 'S3', '2026-10-02', '2026-10-02'],
            [123, 'Test3', '323', 336, 'Teknik Geofisika', 'tahap II', 'NON AKTIF', 'S3', '2026-10-02', '2026-10-02'],
            [124, 'Test3', '323', 336, 'Teknik Geofisika', 'tahap I', 'NON AKTIF', 'S3', '2026-10-02', '2026-10-02'],
            [125, 'Test2', '123', 210, 'Teknik Geofisika', 'TA2', 'NON AKTIF', 'S1', '2026-10-02', '2026-10-02'],
            [126, 'Ujian Proposal 15', '321', 334, 'Rekayasa Pertambangan', 'tahap II', 'NON AKTIF', 'S3', '2026-10-02', '2026-11-04'],
            [127, 'syarat tesis I', '223', 281, 'Teknik Geofisika', 'Tesis I', 'AKTIF', 'S2', '2026-11-06', null],
            [128, 'syarat tesis II', '223', 281, 'Teknik Geofisika', 'Tesis II', 'AKTIF', 'S2', '2026-11-06', null],
        ];
    }
};
