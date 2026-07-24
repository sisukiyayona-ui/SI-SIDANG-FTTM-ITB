<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TCekPersyaratanSeeder extends Seeder
{
    public function run(): void
    {
        $prodi = DB::table('T_PRODI')->where('KODE_PRODI', '322')->value('id');
        $judulDede = DB::table('T_JUDUL')->where('NIM', '456783210')->value('id');
        $now = now();
        $nim = '456783210';

        $syaratList = DB::table('T_SYARAT_SIDANG')
            ->where('ID_PRODI', $prodi)
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->get();

        foreach ($syaratList as $syarat) {
            DB::table('T_CEK_PERSYARATAN')->insert([
                'TAHAPAN_SIDANG' => 'tahap I',
                'ID_JUDUL' => $judulDede,
                'ID_SYARAT_SIDANG' => $syarat->id,
                'PERSYARATAN' => $syarat->NAMA_PERSYARATAN,
                'STATUS_LENGKAP' => 't',
                'LINK_FILE' => 'storage/dokumen/' . $nim . '/' . strtolower(str_replace(' ', '_', $syarat->NAMA_PERSYARATAN)) . '.pdf',
                'TGL_BUAT' => $now,
                'TGL_UPDATE' => $now,
            ]);
        }
    }
}
