<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TProdiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('t_prodi')->insert([
            [
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'STATUS_AKTIF' => 'AKTIF',
                'TGL_CREATE' => now(),
                'TGL_UPDATE' => now(),
            ],
        ]);
    }
}
