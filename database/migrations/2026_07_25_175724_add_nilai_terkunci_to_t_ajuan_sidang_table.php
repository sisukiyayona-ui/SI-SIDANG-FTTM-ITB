<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            if (!Schema::hasColumn('t_ajuan_sidang', 'NILAI_TERKUNCI')) {
                $table->char('NILAI_TERKUNCI', 1)->default('t')->after('STATUS_LULUS');
            }
            if (!Schema::hasColumn('t_ajuan_sidang', 'STATUS_SUBMIT')) {
                $table->char('STATUS_SUBMIT', 1)->default('t')->after('NILAI_TERKUNCI');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            if (Schema::hasColumn('t_ajuan_sidang', 'STATUS_SUBMIT')) {
                $table->dropColumn('STATUS_SUBMIT');
            }
            if (Schema::hasColumn('t_ajuan_sidang', 'NILAI_TERKUNCI')) {
                $table->dropColumn('NILAI_TERKUNCI');
            }
        });
    }
};
