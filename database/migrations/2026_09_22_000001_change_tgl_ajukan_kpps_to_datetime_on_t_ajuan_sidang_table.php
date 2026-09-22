<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->dateTime('TGL_AJUKAN_KPPS')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->date('TGL_AJUKAN_KPPS')->nullable()->change();
        });
    }
};
