<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('t_kpps', 'STATUS_TIM')) {
            Schema::table('t_kpps', function (Blueprint $table) {
                $table->string('STATUS_TIM', 50)->nullable()->after('NAMA');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('t_kpps', 'STATUS_TIM')) {
            Schema::table('t_kpps', function (Blueprint $table) {
                $table->dropColumn('STATUS_TIM');
            });
        }
    }
};
