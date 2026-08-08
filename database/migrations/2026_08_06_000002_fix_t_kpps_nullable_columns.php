<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_kpps', function (Blueprint $table) {
            $table->dropForeign(['ID_USER']);
        });

        Schema::table('t_kpps', function (Blueprint $table) {
            $table->unsignedBigInteger('ID_USER')->nullable()->change();
            $table->string('NIP', 50)->nullable()->change();
            $table->string('KODE_PRODI', 50)->nullable()->change();
            $table->string('NAMA_PRODI', 250)->nullable()->change();
            $table->string('KODE_FS', 50)->nullable()->change();
            $table->string('NAMA_FS', 250)->nullable()->change();
            $table->string('STATUS_AKTIF', 50)->change();
        });

        Schema::table('t_kpps', function (Blueprint $table) {
            $table->foreign('ID_USER')->references('id')->on('t_user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('t_kpps', function (Blueprint $table) {
            $table->dropForeign(['ID_USER']);
        });

        Schema::table('t_kpps', function (Blueprint $table) {
            $table->unsignedBigInteger('ID_USER')->nullable(false)->change();
            $table->string('NIP', 50)->nullable(false)->change();
            $table->string('KODE_PRODI', 50)->nullable(false)->change();
            $table->string('NAMA_PRODI', 250)->nullable(false)->change();
            $table->string('KODE_FS', 50)->nullable(false)->change();
            $table->string('NAMA_FS', 250)->nullable(false)->change();
            $table->char('STATUS_AKTIF', 1)->change();
        });

        Schema::table('t_kpps', function (Blueprint $table) {
            $table->foreign('ID_USER')->references('id')->on('t_user')->onDelete('cascade');
        });
    }
};
