<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserProdiSeeder extends Seeder
{
    public function run(): void
    {
        $prodiIdByKode = DB::table('t_prodi')
            ->select('id', 'KODE_PRODI')
            ->pluck('id', 'KODE_PRODI');

        $users = DB::table('t_user')
            ->select('id', 'KODE_PRODI', 'TGL_CREATE', 'TGL_UPDATE')
            ->whereNotNull('KODE_PRODI')
            ->where('KODE_PRODI', '!=', '')
            ->get();

        $now = now()->toDateString();
        $rows = [];
        foreach ($users as $user) {
            if (!isset($prodiIdByKode[$user->KODE_PRODI])) {
                continue;
            }
            $rows[] = [
                'ID_USER'   => $user->id,
                'ID_PRODI'  => $prodiIdByKode[$user->KODE_PRODI],
                'TGL_BUAT'  => $user->TGL_CREATE ?? $now,
                'TGL_UPDATE' => $user->TGL_UPDATE ?? $now,
            ];
        }

        DB::table('t_user_prodi')->insertOrIgnore($rows);
    }
}