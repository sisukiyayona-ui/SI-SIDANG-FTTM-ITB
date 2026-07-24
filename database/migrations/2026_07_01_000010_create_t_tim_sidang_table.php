<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('T_TIM_SIDANG', function (Blueprint $table) {
            $table->id();
            $table->string('TAHAPAN_SIDANG', 100);
            $table->unsignedBigInteger('ID_JUDUL');
            $table->string('STATUS_TIM_SIDANG', 250);
            $table->unsignedBigInteger('ID_USER_PENILAI');
            $table->string('NIP', 50);
            $table->string('NAMA', 250);
            $table->date('TGL_CREATE')->useCurrent();
            $table->date('TGL_UPDATE')->useCurrent();
            $table->unsignedBigInteger('ID_SK')->nullable();
            $table->integer('URUTAN')->nullable();

            $table->foreign('ID_JUDUL')->references('id')->on('T_JUDUL')->onDelete('cascade');
            $table->foreign('ID_USER_PENILAI')->references('id')->on('T_USER')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('T_TIM_SIDANG');
    }
};
