<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_penilaian', function (Blueprint $table) {
            $table->unsignedBigInteger('ID_TIM_SIDANG')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('t_penilaian', function (Blueprint $table) {
            $table->unsignedBigInteger('ID_TIM_SIDANG')->nullable(false)->change();
        });
    }
};
