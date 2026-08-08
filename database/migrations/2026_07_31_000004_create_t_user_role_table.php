<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_user_role', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ID_USER');
            $table->string('ROLE', 250);
            $table->char('STATUS_DEFAULT', 1);
            $table->date('TGL_CREATE');
            $table->date('TGL_UPDATE');

            $table->foreign('ID_USER')->references('id')->on('t_user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_user_role');
    }
};
