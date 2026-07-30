<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_tahapan', function (Blueprint $table) {
            $table->id();
            $table->string('TAHAPAN', 250);
            $table->string('KODE_TAHAP', 50);
            $table->string('STRATA', 10);
            $table->date('TGL_BUAT')->nullable();
            $table->date('TGL_UPDATE')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_tahapan');
    }
};
