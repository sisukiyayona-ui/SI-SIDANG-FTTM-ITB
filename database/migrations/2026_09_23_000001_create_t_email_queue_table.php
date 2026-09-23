<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_email_queue', function (Blueprint $table) {
            $table->id();
            $table->string('TIPE', 50)->default('notifikasi_approve');
            $table->unsignedBigInteger('ID_AJUAN_SIDANG')->nullable();
            $table->unsignedBigInteger('ID_USER_PENERIMA')->nullable();
            $table->string('EMAIL', 500);
            $table->string('NAMA_PENERIMA', 250)->nullable();
            $table->text('PAYLOAD');
            $table->string('STATUS', 20)->default('pending'); // pending | sent | failed
            $table->unsignedTinyInteger('ATTEMPTS')->default(0);
            $table->text('LAST_ERROR')->nullable();
            $table->timestamp('TGL_KIRIM')->nullable();
            $table->timestamp('NEXT_ATTEMPT_AT')->nullable();
            $table->timestamps();

            $table->index(['STATUS', 'NEXT_ATTEMPT_AT'], 'idx_email_queue_status_next');
            $table->index('ID_AJUAN_SIDANG', 'idx_email_queue_ajuan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_email_queue');
    }
};
