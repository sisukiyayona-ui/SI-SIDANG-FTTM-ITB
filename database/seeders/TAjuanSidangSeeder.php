<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TAjuanSidangSeeder extends Seeder
{
    public function run(): void
    {
        $tuprodi = DB::table('t_user')->where('USERNAME', 'tuprodi')->first();
        $prodi = DB::table('t_prodi')->where('KODE_PRODI', '322')->first();
        $now = now();

        // dede - sudah sampai penilaian tahap I
        $dede = DB::table('t_user')->where('NIP_NIM', '456783210')->first();
        $judulDede = DB::table('t_judul')->where('NIM', '456783210')->first();

        DB::table('t_ajuan_sidang')->insert([
            'ID_USER' => $dede->id,
            'NIM' => '456783210',
            'NAMA_MHS' => 'dede',
            'ANGKATAN' => 2021,
            'ID_JUDUL' => $judulDede->id,
            'JUDUL' => 'Karakterisasi Litologi dan Struktur Geologi di Daerah Tambang Emas Pongkor',
            'TAHAPAN_SIDANG' => 'tahap I',
            'STRATA' => 'S3',
            'TGL_SIDANG' => '2023-06-10',
            'WAKTU_SIDANG' => '09:00:00',
            'RUANG_SIDANG' => 'Ruang Seminar 1',
            'STATUS_LULUS' => 'lulus',
            'STATUS_AJUKAN_MHS' => 'f',
            'STATUS_AJUKAN_PRODI' => 'y',
            'TGL_CREATE' => $now,
            'TGL_UPDATE' => $now,
            'ID_USER_CREATE' => $tuprodi->id,
            'NAMA_USER_CREATE' => $tuprodi->NAMA_LENGKAP,
            'THN_CREATE' => 2023,
            'ID_PRODI' => $prodi->id,
            'KODE_PRODI' => '322',
            'NAMA_PRODI' => 'Teknik Perminyakan',
        ]);

        // Bastian - data baru untuk testing
        $bastian = DB::table('t_user')->where('NIP_NIM', '32224001')->first();
        $judulBastian = DB::table('t_judul')->where('NIM', '32224001')->first();

        DB::table('t_ajuan_sidang')->insert([
            'ID_USER' => $bastian->id,
            'NIM' => '32224001',
            'NAMA_MHS' => 'Bastian',
            'ANGKATAN' => 2022,
            'ID_JUDUL' => $judulBastian->id,
            'JUDUL' => 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat',
            'TAHAPAN_SIDANG' => 'tahap I',
            'STRATA' => 'S3',
            'TGL_SIDANG' => '2024-07-22',
            'WAKTU_SIDANG' => '10:00:00',
            'RUANG_SIDANG' => 'Ruang Seminar 2',
            'STATUS_LULUS' => null,
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
