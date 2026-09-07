<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_notif_approve_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ID_AJUAN_SIDANG');
            $table->unsignedBigInteger('ID_USER_KPPS');
            $table->string('EMAIL', 255)->nullable();
            $table->timestamp('TGL_KIRIM');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_notif_approve_log');
    }
};
