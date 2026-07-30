<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TTimSidangSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('t_tim_sidang')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $pembimbing = DB::table('t_user')->where('USERNAME', 'pembimbing')->first();
        $penguji = DB::table('t_user')->where('USERNAME', 'penguji')->first();

        $judulAde = DB::table('t_judul')->where('NIM', '32322004')->first();
        $now = now();

        $skAde = DB::table('t_sk')
            ->where('ID_JUDUL', $judulAde->id)
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->first();

        DB::table('t_tim_sidang')->insert([
            // Ade - Tahap I
            [
                'TAHAPAN_SIDANG' => 'tahap I',
                'ID_JUDUL' => $judulAde->id,
                'STATUS_TIM_SIDANG' => 'Ketua Sidang',
                'ID_USER_PENILAI' => $penguji->id,
                'NIP' => $penguji->NIP_NIM,
                'NAMA' => $penguji->NAMA_LENGKAP,
                'TGL_CREATE' => $now,
                'TGL_UPDATE' => $now,
                'ID_SK' => $skAde?->id,
                'URUTAN' => 1,
            ],
            [
                'TAHAPAN_SIDANG' => 'tahap I',
                'ID_JUDUL' => $judulAde->id,
                'STATUS_TIM_SIDANG' => 'Ketua Pembimbing',
                'ID_USER_PENILAI' => $pembimbing->id,
                'NIP' => $pembimbing->NIP_NIM,
                'NAMA' => $pembimbing->NAMA_LENGKAP,
                'TGL_CREATE' => $now,
                'TGL_UPDATE' => $now,
                'ID_SK' => $skAde?->id,
                'URUTAN' => 2,
            ],
        ]);
    }
}
