<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoApproveAjuanSidang extends Command
{
    protected $signature = 'sidang:auto-approve {--dry-run : Tampilkan data tanpa update}';

    protected $description = 'Auto-approve voting KPPS: ajuan sudah di-submit ke KPPS atau STATUS_LULUS terisi';

    public function handle(): int
    {
        // Ajuan yang butuh auto-approve voting KPPS
        $ajuans = DB::table('t_ajuan_sidang as a')
            ->where(function ($q) {
                // Kasus 1: sudah di-submit ke KPPS (STATUS_AJUKAN_KPPS='y')
                $q->where('a.STATUS_AJUKAN_KPPS', 'y');
                // Kasus 2: sidang sudah selesai (STATUS_LULUS terisi selain "diajukan")
                $q->orWhere(function ($q2) {
                    $q2->whereNotNull('a.STATUS_LULUS')
                        ->where('a.STATUS_LULUS', '!=', 'diajukan');
                });
            })
            ->select('a.id as ajuan_id', 'a.NIM', 'a.NAMA_MHS', 'a.TAHAPAN_SIDANG', 'a.KODE_PRODI', 'a.STATUS_LULUS', 'a.STATUS_AJUKAN_KPPS', 'a.TGL_AJUKAN_KPPS')
            ->orderBy('a.id')
            ->get();

        if ($ajuans->isEmpty()) {
            $this->info('Tidak ada ajuan yang perlu di-auto-approve.');
            return self::SUCCESS;
        }

        $rows = [];
        foreach ($ajuans as $ajuan) {
            // Anggota KPPS untuk prodi yang sama, belum punya record approval di ajuan ini
            $members = DB::table('t_kpps as k')
                ->leftJoin('t_app_ajuan_sidang as app', function ($join) use ($ajuan) {
                    $join->on('app.ID_USER', '=', 'k.ID_USER')
                        ->where('app.ID_AJUAN_SIDANG', '=', $ajuan->ajuan_id);
                })
                ->where('k.KODE_PRODI', $ajuan->KODE_PRODI)
                ->where('k.STATUS_AKTIF', 'AKTIF')
                ->whereNull('app.id')
                ->select('k.ID_USER', 'k.NAMA', 'k.STATUS_TIM')
                ->orderBy('k.STATUS_TIM')
                ->get();

            foreach ($members as $member) {
                $rows[] = [
                    'ID_AJUAN' => $ajuan->ajuan_id,
                    'NIM' => $ajuan->NIM,
                    'NAMA_MHS' => $ajuan->NAMA_MHS,
                    'TAHAPAN' => $ajuan->TAHAPAN_SIDANG,
                    'NIP/KPPS' => $member->NAMA,
                    'STATUS_TIM' => $member->STATUS_TIM,
                    'ID_USER' => $member->ID_USER,
                ];
            }
        }

        if (empty($rows)) {
            $this->info('Semua anggota KPPS sudah approve.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$ajuans->count()} ajuan, {$this->describe($ajuans)}:");
        $this->newLine();

        $this->table(
            ['ID_AJUAN', 'NIM', 'Nama Mhs', 'Tahapan', 'Anggota KPPS (ID_USER)', 'Status Tim'],
            $rows
        );

        if ($this->option('dry-run')) {
            $this->info('DRY-RUN: Tidak ada data yang diinsert.');
            return self::SUCCESS;
        }

        $now = now()->toDateString();
        $inserted = 0;

        foreach ($ajuans as $ajuan) {
            $members = DB::table('t_kpps as k')
                ->leftJoin('t_app_ajuan_sidang as app', function ($join) use ($ajuan) {
                    $join->on('app.ID_USER', '=', 'k.ID_USER')
                        ->where('app.ID_AJUAN_SIDANG', '=', $ajuan->ajuan_id);
                })
                ->where('k.KODE_PRODI', $ajuan->KODE_PRODI)
                ->where('k.STATUS_AKTIF', 'AKTIF')
                ->whereNull('app.id')
                ->select('k.ID_USER')
                ->get();

            foreach ($members as $member) {
                DB::table('t_app_ajuan_sidang')->insert([
                    'ID_USER' => $member->ID_USER,
                    'ID_AJUAN_SIDANG' => $ajuan->ajuan_id,
                    'STATUS_APPROVE' => 't',
                    'USULAN_PERBAIKAN' => null,
                    'TGL_CREATE' => $now,
                    'TGL_UPDATE' => $now,
                    'TGL_APPROVE' => $now,
                ]);
                $inserted++;
            }
        }

        $this->info("Berhasil auto-approve (insert) {$inserted} voting anggota KPPS.");

        return self::SUCCESS;
    }

    private function describe($ajuans): string
    {
        $ids = $ajuans->pluck('ajuan_id')->implode(', ');
        return "ID_AJUAN: {$ids}";
    }
}