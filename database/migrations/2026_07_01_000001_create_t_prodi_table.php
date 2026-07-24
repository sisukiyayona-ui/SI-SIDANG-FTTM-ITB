<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_prodi', function (Blueprint $table) {
            $table->id();
            $table->string('KODE_PRODI', 50);
            $table->string('NAMA_PRODI', 250);
            $table->date('TGL_CREATE')->useCurrent();
            $table->date('TGL_UPDATE')->useCurrent();
            $table->string('STATUS_AKTIF', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_prodi');
    }
};
