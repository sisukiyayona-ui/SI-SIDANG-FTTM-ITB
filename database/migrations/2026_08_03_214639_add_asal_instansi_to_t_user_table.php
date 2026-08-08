<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_user', function (Blueprint $table) {
            $table->string('ASAL_INSTANSI', 250)->nullable()->after('SIGNATURE');
            $table->string('INSTANSI', 500)->nullable()->after('ASAL_INSTANSI');
        });
    }

    public function down(): void
    {
        Schema::table('t_user', function (Blueprint $table) {
            $table->dropColumn(['ASAL_INSTANSI', 'INSTANSI']);
        });
    }
};
