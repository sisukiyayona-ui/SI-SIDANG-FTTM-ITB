<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_app_ajuan_sidang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ID_USER');
            $table->unsignedBigInteger('ID_AJUAN_SIDANG');
            $table->char('STATUS_APPROVE', 1);
            $table->date('TGL_CREATE');
            $table->date('TGL_UPDATE');
            $table->date('TGL_APPROVE');

            $table->foreign('ID_USER')->references('id')->on('t_user')->onDelete('cascade');
            $table->foreign('ID_AJUAN_SIDANG')->references('id')->on('t_ajuan_sidang')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_app_ajuan_sidang');
    }
};
