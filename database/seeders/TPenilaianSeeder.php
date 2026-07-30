<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TPenilaianSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('t_penilaian')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $tuprodi = DB::table('t_user')->where('USERNAME', 'Dede')->first();
        $prodi = DB::table('t_prodi')->where('KODE_PRODI', '322')->value('id');

        $judul = DB::table('t_judul')->where('NIM', '32322004')->first();
        $ajuan = DB::table('t_ajuan_sidang')
            ->where('NIM', '32322004')
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->first();

        $pointList = DB::table('t_point_penilaian')
            ->where('ID_PRODI', $prodi)
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->get();

        $timList = DB::table('t_tim_sidang')
            ->where('ID_JUDUL', $judul->id)
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->get();

        $now = now();

        foreach ($timList as $tim) {
            foreach ($pointList as $point) {
                DB::table('t_penilaian')->insert([
                    'ID_AJUAN' => $ajuan->id,
                    'ID_JUDUL' => $judul->id,
                    'JUDUL' => $judul->JUDUL,
                    'NIM' => '32322004',
                    'NAMA_MHS' => 'Ade',
                    'TAHAPAN_SIDANG' => 'tahap I',
                    'ID_TIM_SIDANG' => $tim->id,
                    'ID_USER_PENILAI' => $tim->ID_USER_PENILAI,
                    'STATUS_TIM_SIDANG' => $tim->STATUS_TIM_SIDANG,
                    'NIP' => $tim->NIP,
                    'NAMA' => $tim->NAMA,
                    'ID_PENILAIAN' => $point->id,
                    'NAMA_PENILAIAN' => $point->PENILAIAN,
                    'NILAI' => rand(80, 95),
                    'CATATAN' => 'Baik, perlu sedikit perbaikan',
                    'STATUS_SUBMIT' => 't',
                    'TGL_CREATE' => $now,
                    'TGL_UPDATE' => $now,
                    'ID_USER_CREATE' => $tuprodi->id,
                    'NAMA_USER_CREATE' => $tuprodi->NAMA_LENGKAP,
                    'NO_FORM' => $point->NO_FORM,
                ]);
            }
        }
    }
}
