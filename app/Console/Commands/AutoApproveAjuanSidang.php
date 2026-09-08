<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoApproveAjuanSidang extends Command
{
    protected $signature = 'sidang:auto-approve {--dry-run : Tampilkan data tanpa update}';

    protected $description = 'Auto-approve ajuan sidang yang sudah lebih dari 2 hari dari TGL_AJUKAN_KPPS';

    public function handle(): int
    {
        $cutoff = now()->subDays(2)->toDateString();

        $pending = DB::table('t_ajuan_sidang as a')
            ->leftJoin('t_app_ajuan_sidang as app', function ($join) {
                $join->on('app.ID_AJUAN_SIDANG', '=', 'a.id')
                    ->where('app.STATUS_APPROVE', '=', 't');
            })
            ->where('a.STATUS_AJUKAN_KPPS', 'y')
            ->whereNotNull('a.TGL_AJUKAN_KPPS')
            ->where('a.TGL_AJUKAN_KPPS', '<=', $cutoff)
            ->whereNull('app.id')
            ->select('a.id as ajuan_id', 'a.NIM', 'a.NAMA_MHS', 'a.TAHAPAN_SIDANG', 'a.TGL_AJUKAN_KPPS')
            ->get();

        if ($pending->isEmpty()) {
            $this->info('Tidak ada ajuan yang perlu di-auto-approve.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$pending->count()} ajuan baru (TGL_AJUKAN_KPPS <= {$cutoff}, belum ada di t_app_ajuan_sidang):");
        $this->newLine();

        $this->table(
            ['ID_AJUAN', 'NIM', 'Nama', 'Tahapan', 'TGL_Ajukan_KPPS'],
            $pending->map(fn($r) => [
                $r->ajuan_id,
                $r->NIM,
                $r->NAMA_MHS,
                $r->TAHAPAN_SIDANG,
                $r->TGL_AJUKAN_KPPS,
            ])->all()
        );

        if ($this->option('dry-run')) {
            $this->info('DRY-RUN: Tidak ada data yang diinsert.');
            return self::SUCCESS;
        }

        $now = now()->toDateString();
        $inserted = 0;

        foreach ($pending as $row) {
            DB::table('t_app_ajuan_sidang')->insert([
                'ID_USER' => null,
                'ID_AJUAN_SIDANG' => $row->ajuan_id,
                'STATUS_APPROVE' => 'y',
                'USULAN_PERBAIKAN' => null,
                'TGL_CREATE' => $now,
                'TGL_UPDATE' => $now,
                'TGL_APPROVE' => $now,
            ]);
            $inserted++;
        }

        $this->info("Berhasil auto-approve (insert) {$inserted} ajuan sidang.");

        return self::SUCCESS;
    }
}
