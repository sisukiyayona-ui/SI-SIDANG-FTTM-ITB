<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_mk_prodi', function (Blueprint $table) {
            $table->unique(['KODE_PRODI', 'TAHAPAN_SIDANG'], 'uq_mk_prodi_kode_prodi_tahapan');
        });
    }

    public function down(): void
    {
        Schema::table('t_mk_prodi', function (Blueprint $table) {
            $table->dropUnique('uq_mk_prodi_kode_prodi_tahapan');
        });
    }
};