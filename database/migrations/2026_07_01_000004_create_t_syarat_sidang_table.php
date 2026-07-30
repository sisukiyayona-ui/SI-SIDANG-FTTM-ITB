<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_syarat_sidang', function (Blueprint $table) {
            $table->id();
            $table->string('NAMA_PERSYARATAN', 500);
            $table->unsignedBigInteger('ID_PRODI');
            $table->string('KODE_PRODI', 50);
            $table->string('NAMA_PRODI', 250);
            $table->string('TAHAPAN_SIDANG', 100);
            $table->string('STATUS_AKTIF', 50);
            $table->string('STRATA', 10);
            $table->date('TGL_CREATE')->nullable();
            $table->date('TGL_UPDATE')->nullable();

            $table->foreign('ID_PRODI')->references('id')->on('t_prodi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_syarat_sidang');
    }
};
