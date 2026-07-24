<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_user', function (Blueprint $table) {
            $table->id();
            $table->string('NIP_NIM', 50);
            $table->string('NAMA_LENGKAP', 500);
            $table->string('EMAIL', 250);
            $table->string('AKUN_INA', 250)->nullable();
            $table->string('USERNAME', 50)->nullable();
            $table->string('PASSWORD', 250)->nullable();
            $table->string('STATUS_PEGAWAI', 250)->nullable();
            $table->string('JENIS_USER', 250);
            $table->string('KODE_PRODI', 50)->nullable();
            $table->string('NAMA_PRODI', 250)->nullable();
            $table->string('KODE_FS', 50);
            $table->string('NAMA_FS', 250);
            $table->string('STRATA', 10)->nullable();
            $table->integer('THN_ANGKATAN')->nullable();
            $table->string('STATUS_AKTIF', 50);
            $table->char('STATUS_APPROVE', 1);
            $table->date('TGL_CREATE')->useCurrent();
            $table->date('TGL_UPDATE')->useCurrent();
            $table->char('STATUS_KAPRODI', 1)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_user');
    }
};
