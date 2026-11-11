<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_prodi', function (Blueprint $table) {
            if (!Schema::hasColumn('t_prodi', 'FAX')) {
                $table->string('FAX', 50)->nullable()->after('EMAIL');
            }
            if (!Schema::hasColumn('t_prodi', 'WEB')) {
                $table->string('WEB', 250)->nullable()->after('FAX');
            }
        });
    }

    public function down(): void
    {
        Schema::table('t_prodi', function (Blueprint $table) {
            $cols = array_values(array_filter(
                ['FAX', 'WEB'],
                fn ($c) => Schema::hasColumn('t_prodi', $c)
            ));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
