<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_ba_nilai_penilai");
        DB::statement("
            CREATE VIEW v_ba_nilai_penilai AS
            SELECT
                p.ID_AJUAN,
                p.ID_TIM_SIDANG,
                p.ID_JUDUL,
                p.TAHAPAN_SIDANG,
                p.ID_USER_PENILAI,
                MAX(p.NAMA) AS NAMA,
                MAX(p.NIP) AS NIP,
                MAX(p.STATUS_TIM_SIDANG) AS STATUS_TIM_SIDANG,
                ROUND(SUM(p.NILAI) / 5, 2) AS NILAI_RATA2,
                SUM(p.NILAI) AS TOTAL_NILAI,
                COUNT(p.NILAI) AS JUMLAH_ITEM
            FROM t_penilaian p
            WHERE p.NILAI IS NOT NULL
            GROUP BY
                p.ID_AJUAN,
                p.ID_TIM_SIDANG,
                p.ID_JUDUL,
                p.TAHAPAN_SIDANG,
                p.ID_USER_PENILAI
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_ba_nilai_penilai");
    }
};
