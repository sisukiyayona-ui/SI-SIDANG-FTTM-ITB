<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_prodi', function (Blueprint $table) {
            $table->string('ALAMAT', 250)->nullable()->after('NAMA_FS');
            $table->string('TLP', 50)->nullable()->after('ALAMAT');
            $table->string('EMAIL', 250)->nullable()->after('TLP');
        });
    }

    public function down(): void
    {
        Schema::table('t_prodi', function (Blueprint $table) {
            $table->dropColumn(['ALAMAT', 'TLP', 'EMAIL']);
        });
    }
};
