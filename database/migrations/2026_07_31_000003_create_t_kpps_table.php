<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_kpps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ID_USER')->nullable();
            $table->string('NIP', 50)->nullable();
            $table->string('NAMA', 250);
            $table->string('KODE_PRODI', 50)->nullable();
            $table->string('NAMA_PRODI', 250)->nullable();
            $table->string('KODE_FS', 50)->nullable();
            $table->string('NAMA_FS', 250)->nullable();
            $table->string('STATUS_AKTIF', 50);
            $table->date('TGL_CREATE');
            $table->date('TGL_UPDATE');

            $table->foreign('ID_USER')->references('id')->on('t_user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_kpps');
    }
};
