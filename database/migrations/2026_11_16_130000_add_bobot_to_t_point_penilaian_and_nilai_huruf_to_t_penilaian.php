<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_point_penilaian', function (Blueprint $table) {
            $table->decimal('BOBOT', 5, 2)->nullable()->after('KETERANGAN');
        });

        Schema::table('t_penilaian', function (Blueprint $table) {
            $table->string('NILAI_HURUF', 5)->nullable()->after('NILAI');
        });
    }

    public function down(): void
    {
        Schema::table('t_point_penilaian', function (Blueprint $table) {
            $table->dropColumn('BOBOT');
        });

        Schema::table('t_penilaian', function (Blueprint $table) {
            $table->dropColumn('NILAI_HURUF');
        });
    }
};
