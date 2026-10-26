<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([['Tesis I', 'TS1'], ['Tesis II', 'TS2']] as [$tahapan, $kode]) {
            $exists = DB::table('t_tahapan')
                ->whereRaw('UPPER(TRIM(TAHAPAN)) = ?', [strtoupper($tahapan)])
                ->exists();
            if ($exists) {
                continue;
            }

            DB::table('t_tahapan')->insert([
                'TAHAPAN' => $tahapan,
                'KODE_TAHAP' => $kode,
                'STRATA' => 'S2',
                'TGL_BUAT' => now(),
                'TGL_UPDATE' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('t_tahapan')->whereIn('TAHAPAN', ['Tesis I', 'Tesis II'])->where('STRATA', 'S2')->delete();
    }
};
