<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('T_PENILAIAN', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ID_AJUAN');
            $table->unsignedBigInteger('ID_JUDUL');
            $table->string('JUDUL', 500);
            $table->string('NIM', 50);
            $table->string('NAMA_MHS', 250);
            $table->string('TAHAPAN_SIDANG', 100);
            $table->unsignedBigInteger('ID_TIM_SIDANG');
            $table->unsignedBigInteger('ID_USER_PENILAI');
            $table->string('STATUS_TIM_SIDANG', 250);
            $table->string('NIP', 50);
            $table->string('NAMA', 250);
            $table->unsignedBigInteger('ID_PENILAIAN');
            $table->string('NAMA_PENILAIAN', 250);
            $table->decimal('NILAI', 5, 2)->nullable();
            $table->string('CATATAN', 500)->nullable();
            $table->char('STATUS_SUBMIT', 1);
            $table->date('TGL_CREATE')->useCurrent();
            $table->date('TGL_UPDATE')->useCurrent();
            $table->unsignedBigInteger('ID_USER_CREATE');
            $table->string('NAMA_USER_CREATE', 250);
            $table->string('NO_FORM', 50)->nullable();

            $table->foreign('ID_AJUAN')->references('id')->on('T_AJUAN_SIDANG')->onDelete('cascade');
            $table->foreign('ID_JUDUL')->references('id')->on('T_JUDUL')->onDelete('cascade');
            $table->foreign('ID_TIM_SIDANG')->references('id')->on('T_TIM_SIDANG')->onDelete('cascade');
            $table->foreign('ID_USER_PENILAI')->references('id')->on('T_USER')->onDelete('cascade');
            $table->foreign('ID_PENILAIAN')->references('id')->on('T_POINT_PENILAIAN')->onDelete('cascade');
            $table->foreign('ID_USER_CREATE')->references('id')->on('T_USER')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('T_PENILAIAN');
    }
};
