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
        Schema::table('t_user', function (Blueprint $table) {
            if (!Schema::hasColumn('t_user', 'STATUS_WDA')) {
                $table->string('STATUS_WDA', 1)->nullable()->default('f');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_user', function (Blueprint $table) {
            $table->dropColumn('STATUS_WDA');
        });
    }
};