<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('t_ajuan_sidang', 'TGL_AJUKAN_KPPS')) {
            Schema::table('t_ajuan_sidang', function (Blueprint $table) {
                $table->date('TGL_AJUKAN_KPPS')->nullable()->after('STATUS_AJUKAN_KPPS');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('t_ajuan_sidang', 'TGL_AJUKAN_KPPS')) {
            Schema::table('t_ajuan_sidang', function (Blueprint $table) {
                $table->dropColumn('TGL_AJUKAN_KPPS');
            });
        }
    }
};
