<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->decimal('IP', 4, 0)->nullable();
            $table->integer('JML_JURNAL_Q1')->nullable();
            $table->integer('JML_JURNAL_BEREPUTASI1')->nullable();
            $table->integer('JML_JURNAL_BEREPUTASI2')->nullable();
            $table->string('REKOMENDASI_YUDISIUM', 250)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->dropColumn([
                'IP',
                'JML_JURNAL_Q1',
                'JML_JURNAL_BEREPUTASI1',
                'JML_JURNAL_BEREPUTASI2',
                'REKOMENDASI_YUDISIUM',
            ]);
        });
    }
};