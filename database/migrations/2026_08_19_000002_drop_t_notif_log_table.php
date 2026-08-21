<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('t_notif_log')) {
            Schema::drop('t_notif_log');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('t_notif_log')) {
            DB::statement(
                "CREATE TABLE `t_notif_log` (
                    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                    `ID_NOTIF` bigint unsigned DEFAULT NULL,
                    `ID_USER` bigint unsigned DEFAULT NULL,
                    `CHANNEL` varchar(255) DEFAULT NULL,
                    `STATUS` varchar(255) DEFAULT NULL,
                    `ERROR_MSG` text,
                    `SENT_AT` timestamp NULL DEFAULT NULL,
                    `created_at` timestamp NULL DEFAULT NULL,
                    `updated_at` timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        }
    }
};