<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('t_judul', 'ABSTRAK')) {
            Schema::table('t_judul', function (Blueprint $table) {
                $table->string('ABSTRAK', 1000)->nullable()->after('JUDUL');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('t_judul', 'ABSTRAK')) {
            Schema::table('t_judul', function (Blueprint $table) {
                $table->dropColumn('ABSTRAK');
            });
        }
    }
};
