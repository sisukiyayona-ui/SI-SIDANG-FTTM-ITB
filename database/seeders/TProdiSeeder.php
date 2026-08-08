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
                'KODE_FS' => '164',
                'NAMA_FS' => 'FTTM',
                'TGL_CREATE' => now(),
                'TGL_UPDATE' => now(),
            ],
            [
                'KODE_PRODI' => '323',
                'NAMA_PRODI' => 'Teknik Geofisika',
                'STATUS_AKTIF' => 'AKTIF',
                'KODE_FS' => '164',
                'NAMA_FS' => 'FTTM',
                'TGL_CREATE' => now(),
                'TGL_UPDATE' => now(),
            ],
        ]);
    }
}
