<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_app_ajuan_sidang', function (Blueprint $table) {
            $table->string('USULAN_PERBAIKAN', 1000)->nullable()->after('STATUS_APPROVE');
        });
    }

    public function down(): void
    {
        Schema::table('t_app_ajuan_sidang', function (Blueprint $table) {
            $table->dropColumn('USULAN_PERBAIKAN');
        });
    }
};
