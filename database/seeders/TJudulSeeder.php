<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TJudulSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Bastian - judul only (no ajuan/tim/penilaian)
        $bastian = DB::table('t_user')->where('NIP_NIM', '32224001')->first();
        DB::table('t_judul')->insert([
            'JUDUL' => 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat',
            'ID_USER_MHS' => $bastian->id,
            'NIM' => '32224001',
            'THN_CREATE' => 2024,
            'TGL_CREATE' => $now,
            'TGL_UPDATE' => $now,
        ]);

        // dede - has penilaian tahap I
        $dede = DB::table('t_user')->where('NIP_NIM', '456783210')->first();
        DB::table('t_judul')->insert([
            'JUDUL' => 'Karakterisasi Litologi dan Struktur Geologi di Daerah Tambang Emas Pongkor',
            'ID_USER_MHS' => $dede->id,
            'NIM' => '456783210',
            'THN_CREATE' => 2023,
            'TGL_CREATE' => $now,
            'TGL_UPDATE' => $now,
        ]);
    }
}
