<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_user_prodi', function (Blueprint $table) {
            $table->integer('ID', true);
            $table->integer('ID_USER');
            $table->integer('ID_PRODI');
            $table->date('TGL_BUAT')->nullable();
            $table->date('TGL_UPDATE')->nullable();

            $table->unique(['ID_USER', 'ID_PRODI']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_user_prodi');
    }
};