<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TPenilaianSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('T_PENILAIAN')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $tuprodi = DB::table('T_USER')->where('USERNAME', 'tuprodi')->first();
        $prodi = DB::table('T_PRODI')->where('KODE_PRODI', '322')->value('id');

        $judul = DB::table('T_JUDUL')->where('NIM', '456783210')->first();
        $ajuan = DB::table('T_AJUAN_SIDANG')
            ->where('NIM', '456783210')
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->first();

        $pointList = DB::table('T_POINT_PENILAIAN')
            ->where('ID_PRODI', $prodi)
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->get();

        $timList = DB::table('T_TIM_SIDANG')
            ->where('ID_JUDUL', $judul->id)
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->get();

        $now = now();

        foreach ($timList as $tim) {
            foreach ($pointList as $point) {
                DB::table('T_PENILAIAN')->insert([
                    'ID_AJUAN' => $ajuan->id,
                    'ID_JUDUL' => $judul->id,
                    'JUDUL' => $judul->JUDUL,
                    'NIM' => '456783210',
                    'NAMA_MHS' => 'dede',
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
