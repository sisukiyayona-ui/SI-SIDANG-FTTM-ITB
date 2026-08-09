<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('t_ajuan_sidang', 'LINK_FILE_PENELAAH')) {
            Schema::table('t_ajuan_sidang', function (Blueprint $table) {
                $table->dropColumn('LINK_FILE_PENELAAH');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('t_ajuan_sidang', 'LINK_FILE_PENELAAH')) {
            Schema::table('t_ajuan_sidang', function (Blueprint $table) {
                $table->string('LINK_FILE_PENELAAH', 500)->nullable();
            });
        }
    }
};