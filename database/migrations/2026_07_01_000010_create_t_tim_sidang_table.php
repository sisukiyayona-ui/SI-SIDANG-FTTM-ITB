<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_tim_sidang', function (Blueprint $table) {
            $table->id();
            $table->string('TAHAPAN_SIDANG', 100);
            $table->unsignedBigInteger('ID_JUDUL');
            $table->string('STATUS_TIM_SIDANG', 250);
            $table->unsignedBigInteger('ID_USER_PENILAI');
            $table->string('NIP', 50);
            $table->string('NAMA', 250);
            $table->date('TGL_CREATE')->nullable();
            $table->date('TGL_UPDATE')->nullable();
            $table->unsignedBigInteger('ID_SK')->nullable();
            $table->integer('URUTAN')->nullable();
            $table->string('FILE_PENELAAH', 500)->nullable();

            $table->foreign('ID_JUDUL')->references('id')->on('t_judul')->onDelete('cascade');
            $table->foreign('ID_USER_PENILAI')->references('id')->on('t_user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_tim_sidang');
    }
};
