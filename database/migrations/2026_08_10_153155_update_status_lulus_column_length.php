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
        Schema::table('t_penilaian', function (Blueprint $table) {
            // Change STATUS_LULUS from VARCHAR(50) to VARCHAR(100) to accommodate longer status values
            $table->string('STATUS_LULUS', 100)->nullable()->change();
        });
        
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            // Also update t_ajuan_sidang table
            $table->string('STATUS_LULUS', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_penilaian', function (Blueprint $table) {
            $table->string('STATUS_LULUS', 50)->nullable()->change();
        });
        
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->string('STATUS_LULUS', 50)->nullable()->change();
        });
    }
};
