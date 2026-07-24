<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ajuan_sidang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ID_USER');
            $table->string('NIM', 50);
            $table->string('NAMA_MHS', 250);
            $table->integer('ANGKATAN');
            $table->unsignedBigInteger('ID_JUDUL');
            $table->string('JUDUL', 500);
            $table->string('TAHAPAN_SIDANG', 100);
            $table->string('STRATA', 10);
            $table->date('TGL_SIDANG')->nullable();
            $table->time('WAKTU_SIDANG')->nullable();
            $table->string('RUANG_SIDANG', 250)->nullable();
            $table->string('STATUS_LULUS', 50)->nullable();
            $table->char('STATUS_AJUKAN_MHS', 1)->nullable();
            $table->string('NO_UNDANGAN', 250)->nullable();
            $table->char('STATUS_AJUKAN_PRODI', 1)->nullable();
            $table->string('NO_BA_SIDANG', 250)->nullable();
            $table->string('SK_LULUS', 250)->nullable();
            $table->date('TGL_CREATE')->useCurrent();
            $table->date('TGL_UPDATE')->useCurrent();
            $table->unsignedBigInteger('ID_USER_CREATE');
            $table->string('NAMA_USER_CREATE', 250);
            $table->integer('THN_CREATE');
            $table->unsignedBigInteger('ID_PRODI');
            $table->string('KODE_PRODI', 50);
            $table->string('NAMA_PRODI', 250);
            $table->date('TGL_UNDANGAN')->nullable();
            $table->date('TGL_PENGUMPULAN')->nullable();
            $table->date('TGL_PENELAAH')->nullable();
            $table->string('NO_SURAT_PENELAAH', 250)->nullable();
            $table->string('EMAIL_SURAT', 500)->nullable();

            $table->foreign('ID_USER')->references('id')->on('t_user')->onDelete('cascade');
            $table->foreign('ID_JUDUL')->references('id')->on('t_judul')->onDelete('cascade');
            $table->foreign('ID_USER_CREATE')->references('id')->on('t_user')->onDelete('cascade');
            $table->foreign('ID_PRODI')->references('id')->on('t_prodi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_ajuan_sidang');
    }
};
