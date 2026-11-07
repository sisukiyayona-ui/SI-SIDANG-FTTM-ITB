<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->rows() as $row) {
            DB::table('t_tahapan')->updateOrInsert(
                ['TAHAPAN' => $row['TAHAPAN'], 'STRATA' => $row['STRATA']],
                ['KODE_TAHAP' => $row['KODE_TAHAP']]
            );
        }
    }

    public function down(): void
    {
        DB::table('t_tahapan')->where('STRATA', 'S1')->whereIn('TAHAPAN', ['TA I', 'TA II'])->delete();

        DB::table('t_tahapan')->where('STRATA', 'S2')->where('TAHAPAN', 'Tesis I')->update(['KODE_TAHAP' => 'TS1']);
        DB::table('t_tahapan')->where('STRATA', 'S2')->where('TAHAPAN', 'Tesis II')->update(['KODE_TAHAP' => 'TS2']);
    }

    private function rows(): array
    {
        return [
            ['TAHAPAN' => 'TA I', 'KODE_TAHAP' => 'TA I', 'STRATA' => 'S1'],
            ['TAHAPAN' => 'TA II', 'KODE_TAHAP' => 'TA II', 'STRATA' => 'S1'],
            ['TAHAPAN' => 'Tesis I', 'KODE_TAHAP' => 'Tesis I', 'STRATA' => 'S2'],
            ['TAHAPAN' => 'Tesis II', 'KODE_TAHAP' => 'Tesis II', 'STRATA' => 'S2'],
        ];
    }
};
