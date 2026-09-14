<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->decimal('IP', 4, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->decimal('IP', 4, 0)->nullable()->change();
        });
    }
};
