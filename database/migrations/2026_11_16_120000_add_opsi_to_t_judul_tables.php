<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_judul', function (Blueprint $table) {
            if (!Schema::hasColumn('t_judul', 'OPSI')) {
                $table->string('OPSI', 250)->nullable()->collation('utf8mb4_unicode_ci')->after('ABSTRAK');
            }
        });

        Schema::table('t_judul_temp', function (Blueprint $table) {
            if (!Schema::hasColumn('t_judul_temp', 'OPSI')) {
                $table->string('OPSI', 250)->nullable()->collation('utf8mb4_unicode_ci')->after('ALASAN_PERUBAHAN');
            }
            if (!Schema::hasColumn('t_judul_temp', 'OPSI_BARU')) {
                $table->string('OPSI_BARU', 250)->nullable()->collation('utf8mb4_unicode_ci')->after('OPSI');
            }
        });
    }

    public function down(): void
    {
        Schema::table('t_judul_temp', function (Blueprint $table) {
            if (Schema::hasColumn('t_judul_temp', 'OPSI_BARU')) {
                $table->dropColumn('OPSI_BARU');
            }
            if (Schema::hasColumn('t_judul_temp', 'OPSI')) {
                $table->dropColumn('OPSI');
            }
        });

        Schema::table('t_judul', function (Blueprint $table) {
            if (Schema::hasColumn('t_judul', 'OPSI')) {
                $table->dropColumn('OPSI');
            }
        });
    }
};
