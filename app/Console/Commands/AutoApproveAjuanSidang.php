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
        // Ambil ajuan yang butuh auto-approve voting KPPS
        $ajuans = DB::table('t_ajuan_sidang as a')
            ->where(function ($q) {
                // Kasus 1: sudah di-submit ke KPPS
                $q->where('a.STATUS_AJUKAN_KPPS', 'y');

                // Kasus 2: sidang sudah selesai (STATUS_LULUS terisi, bukan 'diajukan', bukan kosong)
                $q->orWhere(function ($q2) {
                    $q2->whereNotNull('a.STATUS_LULUS')
                        ->whereNotIn('a.STATUS_LULUS', ['diajukan', '']);
                });
            })
            ->select(
                'a.id as ajuan_id',
                'a.NIM',
                'a.NAMA_MHS',
                'a.TAHAPAN_SIDANG',
                'a.KODE_PRODI',
                'a.STATUS_LULUS',
                'a.STATUS_AJUKAN_KPPS'
            )
            ->orderBy('a.id')
            ->get();

        if ($ajuans->isEmpty()) {
            $this->info('Tidak ada ajuan yang perlu di-auto-approve.');
            return self::SUCCESS;
        }

        // Hitung anggota yang benar-benar masih butuh approval, sekaligus simpan untuk insert
        $pending = []; // [ajuan_id => collection anggota]
        $rows    = []; // data untuk ditampilkan di tabel

        foreach ($ajuans as $ajuan) {
            $members = $this->getPendingMembers($ajuan->ajuan_id, $ajuan->KODE_PRODI);

            if ($members->isEmpty()) {
                continue; // semua anggota sudah approve, skip
            }

            $pending[$ajuan->ajuan_id] = $members;

            foreach ($members as $member) {
                $rows[] = [
                    $ajuan->ajuan_id,
                    $ajuan->NIM,
                    $ajuan->NAMA_MHS,
                    $ajuan->TAHAPAN_SIDANG,
                    $member->NAMA . ' (' . $member->ID_USER . ')',
                    $member->STATUS_TIM,
                ];
            }
        }

        if (empty($rows)) {
            $this->info('Semua anggota KPPS sudah approve untuk seluruh ajuan terkait.');
            return self::SUCCESS;
        }

        $jumlahAjuanPending = count($pending);
        $this->info("Ditemukan {$jumlahAjuanPending} ajuan yang masih butuh approval KPPS:");
        $this->newLine();

        $this->table(
            ['ID_AJUAN', 'NIM', 'Nama Mhs', 'Tahapan', 'Anggota KPPS', 'Status Tim'],
            $rows
        );

        if ($this->option('dry-run')) {
            $this->info('DRY-RUN: Tidak ada data yang diinsert.');
            return self::SUCCESS;
        }

        $now      = now()->toDateString();
        $inserted = 0;

        DB::transaction(function () use ($pending, $now, &$inserted) {
            foreach ($pending as $ajuanId => $members) {
                $data = [];
                foreach ($members as $member) {
                    $data[] = [
                        'ID_USER'          => $member->ID_USER,
                        'ID_AJUAN_SIDANG'  => $ajuanId,
                        'STATUS_APPROVE'   => 't',
                        'USULAN_PERBAIKAN' => null,
                        'ALASAN_REJECT'    => null,
                        'TGL_CREATE'       => $now,
                        'TGL_UPDATE'       => $now,
                        'TGL_APPROVE'      => $now,
                    ];
                }
                DB::table('t_app_ajuan_sidang')->insert($data);
                $inserted += count($data);
            }
        });

        $this->info("Berhasil auto-approve (insert) {$inserted} voting anggota KPPS untuk {$jumlahAjuanPending} ajuan.");

        return self::SUCCESS;
    }

    /**
     * Ambil anggota KPPS aktif di prodi tsb yang BELUM punya record approval
     * untuk ajuan ini.
     */
    private function getPendingMembers(int $ajuanId, string $kodeProdi)
    {
        // Satu baris per ID_USER (hindari duplikat Ketua+Anggota untuk orang yang sama)
        return DB::table('t_kpps as k')
            ->leftJoin('t_app_ajuan_sidang as app', function ($join) use ($ajuanId) {
                $join->on('app.ID_USER', '=', 'k.ID_USER')
                    ->where('app.ID_AJUAN_SIDANG', '=', $ajuanId);
            })
            ->where('k.KODE_PRODI', $kodeProdi)
            ->where('k.STATUS_AKTIF', 'AKTIF')
            ->whereNull('app.id')
            ->select(
                'k.ID_USER',
                DB::raw('MAX(k.NAMA) as NAMA'),
                DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(k.STATUS_TIM ORDER BY CASE WHEN k.STATUS_TIM = 'Ketua' THEN 1 WHEN k.STATUS_TIM = 'Sekretaris' THEN 2 ELSE 3 END SEPARATOR ','), ',', 1) as STATUS_TIM")
            )
            ->groupBy('k.ID_USER')
            ->orderByRaw("CASE WHEN STATUS_TIM = 'Ketua' THEN 1 WHEN STATUS_TIM = 'Sekretaris' THEN 2 ELSE 3 END")
            ->get();
    }
}