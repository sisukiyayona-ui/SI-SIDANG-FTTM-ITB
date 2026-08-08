<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_fs', function (Blueprint $table) {
            $table->id();
            $table->string('KODE_FS', 50);
            $table->string('NAMA_FS', 250);
            $table->date('TGL_CREATE');
            $table->date('TGL_UPDATE');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_fs');
    }
};
