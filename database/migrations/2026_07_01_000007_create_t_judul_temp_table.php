<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_judul_temp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ID_JUDUL');
            $table->string('JUDUL', 500);
            $table->unsignedBigInteger('ID_USER_MHS');
            $table->string('NIM', 50);
            $table->string('JUDUL_BARU', 500);
            $table->string('TAHAP_PERUBAHAN', 250);
            $table->string('ALASAN_PERUBAHAN', 500);
            $table->date('TGL_CREATE')->useCurrent();
            $table->date('TGL_UPDATE')->useCurrent();

            $table->foreign('ID_USER_MHS')->references('id')->on('t_user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_judul_temp');
    }
};
