<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_publikasi', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('PUBLIKASI', 250)->default('0');
            $table->char('STATUS', 1)->nullable();
            $table->bigInteger('ID_AJUAN_SIDANG')->default(0);
            $table->date('TGL_CREATE')->nullable();
            $table->date('TGL_UPDATE')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_publikasi');
    }
};
