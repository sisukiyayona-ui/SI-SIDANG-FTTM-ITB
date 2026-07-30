<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TAjuanSidangSeeder extends Seeder
{
    public function run(): void
    {
        $tuprodi = DB::table('t_user')->where('USERNAME', 'Dede')->first();
        $prodi = DB::table('t_prodi')->where('KODE_PRODI', '322')->first();
        $now = now();

        // Ade - Mahasiswa Teknik Perminyakan dengan ajuan sidang tahap I
        $ade = DB::table('t_user')->where('NIP_NIM', '32322004')->first();
        $judulAde = DB::table('t_judul')->where('NIM', '32322004')->first();

        DB::table('t_ajuan_sidang')->insert([
            'ID_USER' => $ade->id,
            'NIM' => '32322004',
            'NAMA_MHS' => 'Ade',
            'ANGKATAN' => 2023,
            'ID_JUDUL' => $judulAde->id,
            'JUDUL' => 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat',
            'TAHAPAN_SIDANG' => 'tahap I',
            'STRATA' => 'S3',
            'TGL_SIDANG' => '2024-07-22',
            'WAKTU_SIDANG' => '10:00:00',
            'RUANG_SIDANG' => 'Ruang Seminar 1',
            'STATUS_LULUS' => null,
            'NILAI_TERKUNCI' => 't',
            'STATUS_SUBMIT' => 't',
            'STATUS_AJUKAN_MHS' => 'y',
            'STATUS_AJUKAN_PRODI' => 'y',
            'TGL_CREATE' => $now,
            'TGL_UPDATE' => $now,
            'ID_USER_CREATE' => $tuprodi->id,
            'NAMA_USER_CREATE' => $tuprodi->NAMA_LENGKAP,
            'THN_CREATE' => 2024,
            'ID_PRODI' => $prodi->id,
            'KODE_PRODI' => '322',
            'NAMA_PRODI' => 'Teknik Perminyakan',
        ]);
    }
}
