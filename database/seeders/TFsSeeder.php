<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TFsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('t_fs')->insert([
            [
                'KODE_FS'  => '164',
                'NAMA_FS'  => 'FTTM',
                'TGL_CREATE' => now(),
                'TGL_UPDATE' => now(),
            ],
            [
                'KODE_FS'  => '161',
                'NAMA_FS'  => 'FSTI',
                'TGL_CREATE' => now(),
                'TGL_UPDATE' => now(),
            ],
            [
                'KODE_FS'  => '162',
                'NAMA_FS'  => 'FKM',
                'TGL_CREATE' => now(),
                'TGL_UPDATE' => now(),
            ],
        ]);
    }
}
