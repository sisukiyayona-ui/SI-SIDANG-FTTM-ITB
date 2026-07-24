<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('T_JUDUL', function (Blueprint $table) {
            $table->id();
            $table->string('JUDUL', 500);
            $table->unsignedBigInteger('ID_USER_MHS');
            $table->string('NIM', 50);
            $table->integer('THN_CREATE');
            $table->date('TGL_CREATE')->useCurrent();
            $table->date('TGL_UPDATE')->useCurrent();

            $table->foreign('ID_USER_MHS')->references('id')->on('T_USER')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('T_JUDUL');
    }
};
