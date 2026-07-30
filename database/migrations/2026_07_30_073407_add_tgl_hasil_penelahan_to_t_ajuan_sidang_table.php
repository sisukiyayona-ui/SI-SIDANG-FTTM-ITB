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
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->date('TGL_HASIL_PENELAHAN')->nullable()->after('TGL_PENELAAH');
        });
    }

    public function down(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->dropColumn('TGL_HASIL_PENELAHAN');
        });
    }
};
