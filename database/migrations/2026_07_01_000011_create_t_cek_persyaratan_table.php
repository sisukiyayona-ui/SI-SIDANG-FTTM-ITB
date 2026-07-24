<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('T_CEK_PERSYARATAN', function (Blueprint $table) {
            $table->id();
            $table->string('TAHAPAN_SIDANG', 100);
            $table->unsignedBigInteger('ID_JUDUL');
            $table->unsignedBigInteger('ID_SYARAT_SIDANG');
            $table->string('PERSYARATAN', 250);
            $table->char('STATUS_LENGKAP', 1);
            $table->string('LINK_FILE', 2000)->nullable();
            $table->date('TGL_BUAT')->useCurrent();
            $table->date('TGL_UPDATE')->useCurrent();

            $table->foreign('ID_JUDUL')->references('id')->on('T_JUDUL')->onDelete('cascade');
            $table->foreign('ID_SYARAT_SIDANG')->references('id')->on('T_SYARAT_SIDANG')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('T_CEK_PERSYARATAN');
    }
};
