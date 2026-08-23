<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // migrate:fresh hanya drop BASE TABLE — view harus di-drop manual
        // agar migrasi idempotent.
        DB::statement("DROP VIEW IF EXISTS v_report_tipe_i");
        DB::statement("
            CREATE VIEW v_report_tipe_i AS
            SELECT 
                a.id,
                j.id as id_judul,
                YEAR(a.tgl_create) as tahun,
                j.NIM,
                u.NAMA_LENGKAP as nama_mahasiswa,
                j.JUDUL,
                ts.NIP,
                ts.NAMA as pembimbing_penguji,
                ts.STATUS_TIM_SIDANG,
                a.tahapan_sidang,
                a.status_lulus,
                a.strata,
                a.kode_prodi,
                ts.URUTAN,
                a.tgl_sidang,
                a.waktu_sidang,
                a.ruang_sidang,
                a.tgl_create,
                a.tgl_update,
                (SELECT ROUND(AVG(p.NILAI), 2) 
                 FROM t_penilaian p 
                 WHERE p.ID_TIM_SIDANG = ts.id 
                   AND p.TAHAPAN_SIDANG = a.tahapan_sidang) AS nilai_rata2
            FROM t_ajuan_sidang a
            INNER JOIN t_judul j ON a.id_judul = j.id
            INNER JOIN t_user u ON j.id_user_mhs = u.id
            INNER JOIN t_tim_sidang ts ON ts.id_judul = j.id 
                AND ts.TAHAPAN_SIDANG = a.tahapan_sidang
            WHERE a.strata = 'S3'
            ORDER BY j.id, a.tahapan_sidang, ts.URUTAN
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_report_tipe_i");
    }
};
