<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TCekPersyaratanSeeder extends Seeder
{
    public function run(): void
    {
        $prodi = DB::table('t_prodi')->where('KODE_PRODI', '322')->value('id');
        $judulAde = DB::table('t_judul')->where('NIM', '32322004')->value('id');
        $now = now();
        $nim = '32322004';

        $syaratList = DB::table('t_syarat_sidang')
            ->where('ID_PRODI', $prodi)
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->get();

        foreach ($syaratList as $syarat) {
            DB::table('t_cek_persyaratan')->insert([
                'TAHAPAN_SIDANG' => 'tahap I',
                'ID_JUDUL' => $judulAde,
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
