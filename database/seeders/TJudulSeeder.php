<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TJudulSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Ade - Mahasiswa Teknik Perminyakan
        $ade = DB::table('t_user')->where('NIP_NIM', '32322004')->first();
        DB::table('t_judul')->insert([
            'JUDUL' => 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat',
            'ID_USER_MHS' => $ade->id,
            'NIM' => '32322004',
            'THN_CREATE' => 2024,
            'TGL_CREATE' => $now,
            'TGL_UPDATE' => $now,
        ]);
    }
}
