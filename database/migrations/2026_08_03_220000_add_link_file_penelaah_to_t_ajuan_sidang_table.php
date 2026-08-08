<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->string('LINK_FILE_PENELAAH', 500)->nullable()->after('EMAIL_SURAT');
        });
    }

    public function down(): void
    {
        Schema::table('t_ajuan_sidang', function (Blueprint $table) {
            $table->dropColumn('LINK_FILE_PENELAAH');
        });
    }
};
