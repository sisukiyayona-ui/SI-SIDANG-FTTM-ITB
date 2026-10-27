<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE t_ajuan_sidang MODIFY IP DECIMAL(4,2) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE t_ajuan_sidang MODIFY IP DECIMAL(4,0) NULL');
    }
};
