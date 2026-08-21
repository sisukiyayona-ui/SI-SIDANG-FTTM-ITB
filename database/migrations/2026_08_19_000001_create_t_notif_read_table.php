<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_notif_read', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ID_USER')->nullable();
            $table->unsignedBigInteger('ID_AJUAN')->nullable();
            $table->timestamps();

            $table->index(['ID_USER', 'ID_AJUAN']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_notif_read');
    }
};
