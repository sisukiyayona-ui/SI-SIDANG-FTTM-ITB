<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_dashboard_s3");
        DB::statement("
            CREATE VIEW v_dashboard_s3 AS
            SELECT
                a.TAHUN AS TAHUN,
                a.id_prodi AS id_prodi,
                a.nama_prodi AS nama_prodi,
                tahap1.jum_tahap1 AS jum_tahap1,
                tahap2.jum_tahap2 AS jum_tahap2,
                tahap3.jum_tahap3 AS jum_tahap3,
                tahap4.jum_tahap4 AS jum_tahap4
            FROM (
                SELECT DISTINCT
                    YEAR(t_ajuan_sidang.TGL_CREATE) AS TAHUN,
                    t_ajuan_sidang.ID_PRODI AS id_prodi,
                    t_ajuan_sidang.NAMA_PRODI AS nama_prodi
                FROM t_ajuan_sidang
                WHERE (t_ajuan_sidang.STATUS_LULUS = 'Lulus'
                    OR t_ajuan_sidang.STATUS_LULUS LIKE 'Layak%')
            ) a
            LEFT JOIN (
                SELECT t.TAHUN AS tahun, t.id_prodi AS id_prodi, t.nama_prodi AS nama_prodi,
                    COUNT(0) AS jum_tahap1
                FROM (
                    SELECT YEAR(t_ajuan_sidang.TGL_CREATE) AS TAHUN,
                        t_ajuan_sidang.ID_PRODI AS id_prodi,
                        t_ajuan_sidang.NAMA_PRODI AS nama_prodi
                    FROM t_ajuan_sidang
                    WHERE ((t_ajuan_sidang.STATUS_LULUS = 'Lulus'
                        OR t_ajuan_sidang.STATUS_LULUS LIKE 'Layak%')
                        AND t_ajuan_sidang.TAHAPAN_SIDANG = 'tahap I')
                ) t
                GROUP BY t.TAHUN, t.id_prodi, t.nama_prodi
            ) tahap1 ON (a.TAHUN = tahap1.tahun AND a.id_prodi = tahap1.id_prodi)
            LEFT JOIN (
                SELECT t.TAHUN AS tahun, t.id_prodi AS id_prodi, t.nama_prodi AS nama_prodi,
                    COUNT(0) AS jum_tahap2
                FROM (
                    SELECT YEAR(t_ajuan_sidang.TGL_CREATE) AS TAHUN,
                        t_ajuan_sidang.ID_PRODI AS id_prodi,
                        t_ajuan_sidang.NAMA_PRODI AS nama_prodi
                    FROM t_ajuan_sidang
                    WHERE ((t_ajuan_sidang.STATUS_LULUS = 'Lulus'
                        OR t_ajuan_sidang.STATUS_LULUS LIKE 'Layak%')
                        AND t_ajuan_sidang.TAHAPAN_SIDANG = 'tahap II')
                ) t
                GROUP BY t.TAHUN, t.id_prodi, t.nama_prodi
            ) tahap2 ON (a.TAHUN = tahap2.tahun AND a.id_prodi = tahap2.id_prodi)
            LEFT JOIN (
                SELECT t.TAHUN AS tahun, t.id_prodi AS id_prodi, t.nama_prodi AS nama_prodi,
                    COUNT(0) AS jum_tahap3
                FROM (
                    SELECT YEAR(t_ajuan_sidang.TGL_CREATE) AS TAHUN,
                        t_ajuan_sidang.ID_PRODI AS id_prodi,
                        t_ajuan_sidang.NAMA_PRODI AS nama_prodi
                    FROM t_ajuan_sidang
                    WHERE ((t_ajuan_sidang.STATUS_LULUS = 'Lulus'
                        OR t_ajuan_sidang.STATUS_LULUS LIKE 'Layak%')
                        AND t_ajuan_sidang.TAHAPAN_SIDANG = 'SK IV')
                ) t
                GROUP BY t.TAHUN, t.id_prodi, t.nama_prodi
            ) tahap3 ON (a.TAHUN = tahap3.tahun AND a.id_prodi = tahap3.id_prodi)
            LEFT JOIN (
                SELECT t.TAHUN AS tahun, t.id_prodi AS id_prodi, t.nama_prodi AS nama_prodi,
                    COUNT(0) AS jum_tahap4
                FROM (
                    SELECT YEAR(t_ajuan_sidang.TGL_CREATE) AS TAHUN,
                        t_ajuan_sidang.ID_PRODI AS id_prodi,
                        t_ajuan_sidang.NAMA_PRODI AS nama_prodi
                    FROM t_ajuan_sidang
                    WHERE ((t_ajuan_sidang.STATUS_LULUS = 'Lulus'
                        OR t_ajuan_sidang.STATUS_LULUS LIKE 'Layak%')
                        AND t_ajuan_sidang.TAHAPAN_SIDANG = 'tahap IV')
                ) t
                GROUP BY t.TAHUN, t.id_prodi, t.nama_prodi
            ) tahap4 ON (a.TAHUN = tahap4.tahun AND a.id_prodi = tahap4.id_prodi)
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_dashboard_s3");
    }
};
