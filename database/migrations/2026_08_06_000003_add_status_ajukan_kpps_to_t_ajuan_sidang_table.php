<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('t_ajuan_sidang', 'STATUS_AJUKAN_KPPS')) {
            Schema::table('t_ajuan_sidang', function (Blueprint $table) {
                $table->char('STATUS_AJUKAN_KPPS', 1)->nullable()->after('STATUS_AJUKAN_PRODI');
            });
        }
    }

    public function down(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->dropColumn('STATUS_AJUKAN_KPPS');
        });
    }
};