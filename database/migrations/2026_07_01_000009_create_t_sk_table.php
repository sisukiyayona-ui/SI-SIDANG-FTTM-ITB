<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_sk', function (Blueprint $table) {
            $table->id();
            $table->string('NO_SK', 250);
            $table->unsignedBigInteger('ID_JUDUL')->nullable();
            $table->string('TAHAPAN_SIDANG', 100);
            $table->date('TGL_BUAT')->useCurrent();
            $table->date('TGL_UPDATE')->useCurrent();

            $table->foreign('ID_JUDUL')->references('id')->on('t_judul')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_sk');
    }
};
