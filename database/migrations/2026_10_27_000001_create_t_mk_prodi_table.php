<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_mk_prodi', function (Blueprint $table) {
            $table->id();
            $table->string('KODE_PRODI', 50)->nullable();
            $table->string('KODE_MK', 50)->nullable();
            $table->date('TGL_BUAT')->nullable();
            $table->date('TGL_UPDATE')->nullable();
            $table->string('NAMA_PRODI', 250)->nullable();
            $table->string('TAHAPAN_SIDANG', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_mk_prodi');
    }
};