<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_user', function (Blueprint $table) {
            $table->string('KK', 250)->nullable()->after('SIGNATURE');
        });

        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->string('JENIS_SIDANG', 250)->nullable()->after('EMAIL_SURAT');
        });
    }

    public function down(): void
    {
        Schema::table('t_user', function (Blueprint $table) {
            $table->dropColumn('KK');
        });

        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->dropColumn('JENIS_SIDANG');
        });
    }
};