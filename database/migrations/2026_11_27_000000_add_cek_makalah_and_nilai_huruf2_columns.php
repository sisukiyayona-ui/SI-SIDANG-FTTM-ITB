<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('t_penilaian', 'CEK_MAKALAH')) {
            Schema::table('t_penilaian', function (Blueprint $table) {
                $table->char('CEK_MAKALAH', 1)->nullable()->after('NILAI_TERKUNCI');
            });
        }

        if (! Schema::hasColumn('t_ajuan_sidang', 'NILAI_HURUF2')) {
            Schema::table('t_ajuan_sidang', function (Blueprint $table) {
                $table->string('NILAI_HURUF2', 50)->nullable()->after('NILAI_HURUF');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('t_penilaian', 'CEK_MAKALAH')) {
            Schema::table('t_penilaian', function (Blueprint $table) {
                $table->dropColumn('CEK_MAKALAH');
            });
        }

        if (Schema::hasColumn('t_ajuan_sidang', 'NILAI_HURUF2')) {
            Schema::table('t_ajuan_sidang', function (Blueprint $table) {
                $table->dropColumn('NILAI_HURUF2');
            });
        }
    }
};
