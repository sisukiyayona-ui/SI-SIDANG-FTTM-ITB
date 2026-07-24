<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TSyaratSidangSeeder extends Seeder
{
    public function run(): void
    {
        $prodi = DB::table('t_prodi')->where('KODE_PRODI', '322')->first();
        $now = now();

        $data = [
            // Tahap I
            ['tahap I', 'Mengikuti mata kuliah tahap persiapan'],
            // Tahap II
            ['tahap II', 'Lulus ujian persiapan (tahap I)'],
            ['tahap II', 'Menyerahkan draft proposal riset yang sudah ditandatangi pembimbing'],
            ['tahap II', 'Menyerahkan form bimbingan/kemajuan akademik yang sudah ditandatangi pembimbing'],
            // SK I
            ['SK I', 'Lulus ujian proposal'],
            ['SK I', 'Menyerahkan formulir bimbingan yang sudah ditandatangi pembimbing'],
            ['SK I', 'Menyerahkan makalah/slide presentasi'],
            // SK II
            ['SK II', 'Menyerahkan laporan kemajuan I dan II'],
            ['SK II', 'Menyerahkan formulir bimbingan yang sudah ditandatangi pembimbing'],
            ['SK II', 'Menyerahkan makalah/slide presentasi'],
            // SK III
            ['SK III', 'Menyerahkan laporan kemajuan I, II dan III'],
            ['SK III', 'Menyerahkan formulir bimbingan yang sudah ditandatangi pembimbing'],
            ['SK III', 'Menyerahkan makalah/slide presentasi'],
            // SK IV
            ['SK IV', 'Menyerahkan naskah laporan kemajuan tahap akhir/penulisan disertasi'],
            ['SK IV', 'Melampirkan lembar pengesahan atau persetujuan dari Tim Dosen Pembimbing'],
            ['SK IV', 'Menyerahkan makalah/slide presentasi'],
            // Tahap IV
            ['tahap IV', 'Telah mengambil dan lulus semua mata kuliah selain Sidang Doktor'],
            ['tahap IV', 'Telah memiliki bukti status accepted/published untuk makalah riset di jurnal internasional bereputasi (first author, afiliasi ITB)'],
            ['tahap IV', 'Telah menyelesaikan dan memenuhi seluruh logbook bimbingan akademik dengan promotor/pembimbing'],
            ['tahap IV', 'Menyerahkan sejumlah draft disertasi dan ringkasan disertasi'],
        ];

        foreach ($data as $d) {
            DB::table('t_syarat_sidang')->insert([
                'NAMA_PERSYARATAN' => $d[1],
                'ID_PRODI' => $prodi->id,
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'TAHAPAN_SIDANG' => $d[0],
                'STATUS_AKTIF' => 'AKTIF',
                'STRATA' => 'S3',
                'TGL_CREATE' => $now,
                'TGL_UPDATE' => $now,
            ]);
        }
    }
}
