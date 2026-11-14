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
            if (!Schema::hasColumn('t_ajuan_sidang', 'SEMESTER')) {
                $table->string('SEMESTER', 100)->nullable()->after('NAMA_PRODI');
            }
            if (!Schema::hasColumn('t_ajuan_sidang', 'THN_AKADEMIK')) {
                $table->string('THN_AKADEMIK', 100)->nullable()->after('SEMESTER');
            }
            if (!Schema::hasColumn('t_ajuan_sidang', 'REKOMENDASI_YUDISIUM')) {
                $table->string('REKOMENDASI_YUDISIUM', 250)->nullable()->after('THN_AKADEMIK');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->dropColumn(['SEMESTER', 'THN_AKADEMIK', 'REKOMENDASI_YUDISIUM']);
        });
    }
};
