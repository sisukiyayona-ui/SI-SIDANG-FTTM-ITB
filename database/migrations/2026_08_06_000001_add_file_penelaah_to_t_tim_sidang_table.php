<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('t_tim_sidang', 'FILE_PENELAAH')) {
            Schema::table('t_tim_sidang', function (Blueprint $table) {
                $table->string('FILE_PENELAAH', 500)->nullable()->after('URUTAN');
            });
        }
    }

    public function down(): void
    {
        Schema::table('t_tim_sidang', function (Blueprint $table) {
            $table->dropColumn('FILE_PENELAAH');
        });
    }
};
