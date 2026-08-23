<?php

namespace App\Console\Commands;

use App\Mail\PengingatSidangMail;
use App\Models\TAjuanSidang;
use App\Models\TUser;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class KirimPengingatSidang extends Command
{
    protected $signature = 'sidang:kirim-pengingat {--dry-run : Tampilkan daftar penerima tanpa mengirim email}';

    protected $description = 'Kirim email pengingat H-1 ke mahasiswa, pembimbing, penguji, dan fakultas untuk sidang yang terjadwal besok';

    private const HARI = [
        CarbonInterface::SUNDAY => 'Minggu',
        CarbonInterface::MONDAY => 'Senin',
        CarbonInterface::TUESDAY => 'Selasa',
        CarbonInterface::WEDNESDAY => 'Rabu',
        CarbonInterface::THURSDAY => 'Kamis',
        CarbonInterface::FRIDAY => 'Jumat',
        CarbonInterface::SATURDAY => 'Sabtu',
    ];

    private const BULAN = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    public function handle(): int
    {
        $besok = now()->addDay()->toDateString();

        $ajuanList = TAjuanSidang::query()
            ->with('user')
            ->whereDate('TGL_SIDANG', $besok)
            ->where(function ($query) {
                // Hanya sidang yang masih berjalan (belum ada hasil akhir)
                $query->whereNull('STATUS_LULUS')->orWhere('STATUS_LULUS', 'diajukan');
            })
            ->orderBy('TGL_SIDANG')
            ->orderBy('WAKTU_SIDANG')
            ->get();

        if ($ajuanList->isEmpty()) {
            $this->info("Tidak ada sidang terjadwal pada {$besok}. Tidak ada email dikirim.");

            return self::SUCCESS;
        }

        $emailFakultas = TUser::query()
            ->where('JENIS_USER', 'FS')
            ->pluck('EMAIL')
            ->filter()
            ->map(fn ($email) => trim($email))
            ->unique()
            ->values();

        $isDryRun = (bool) $this->option('dry-run');
        $totalTerkirim = 0;
        $totalGagal = 0;

        foreach ($ajuanList as $ajuan) {
            $this->info("================================================================");
            $this->info("Sidang: {$ajuan->TAHAPAN_SIDANG} - {$ajuan->NAMA_MHS} ({$ajuan->NIM})");

            $penerima = $this->kumpulkanPenerima($ajuan, $emailFakultas);

            if ($penerima->isEmpty()) {
                $this->warn('  Tidak ada alamat email penerima yang valid. dilewati.');
                continue;
            }

            $detail = $this->susunDetailSidang($ajuan);

            if ($isDryRun) {
                $this->table(
                    ['Email', 'Nama', 'Peran'],
                    $penerima->map(fn ($p) => [$p['email'], $p['nama'], $p['peran']])->all()
                );
                $this->line('  DRY-RUN: email tidak dikirim. (Alamat ganda dikirim sekali saat pengiriman nyata.)');
                continue;
            }

            $penerima = $penerima->unique('email')->values();

            foreach ($penerima as $p) {
                try {
                    Mail::to($p['email'])->send(new PengingatSidangMail($detail));
                    $totalTerkirim++;
                    $this->info("  Terkirim ke {$p['nama']} ({$p['peran']}): {$p['email']}");
                } catch (Throwable $e) {
                    $totalGagal++;
                    $this->error("  GAGAL ke {$p['email']}: {$e->getMessage()}");
                }
            }
        }

        $this->info('================================================================');
        if ($isDryRun) {
            $this->info('Selesai (dry-run). Tidak ada email yang dikirim.');
        } else {
            $this->info("Selesai. Terkirim: {$totalTerkirim}, Gagal: {$totalGagal}.");
        }

        return self::SUCCESS;
    }

    private function kumpulkanPenerima(TAjuanSidang $ajuan, $emailFakultas)
    {
        $penerima = collect();

        if ($ajuan->user && filled($ajuan->user->EMAIL)) {
            $penerima->push([
                'email' => trim($ajuan->user->EMAIL),
                'nama' => $ajuan->user->NAMA_LENGKAP,
                'peran' => 'Mahasiswa',
            ]);
        }

        $tim = DB::table('T_TIM_SIDANG as ts')
            ->join('T_USER as u', 'ts.ID_USER_PENILAI', '=', 'u.id')
            ->where('ts.ID_JUDUL', $ajuan->ID_JUDUL)
            ->where(function ($query) {
                $query->where('ts.STATUS_TIM_SIDANG', 'like', '%Pembimbing%')
                    ->orWhere('ts.STATUS_TIM_SIDANG', 'like', '%Penguji%');
            })
            ->orderByRaw("CASE WHEN ts.STATUS_TIM_SIDANG LIKE '%Pembimbing%' THEN 1 ELSE 2 END")
            ->orderBy('ts.URUTAN')
            ->get(['u.NAMA_LENGKAP', 'u.EMAIL', 'ts.STATUS_TIM_SIDANG']);

        foreach ($tim as $anggota) {
            if (filled($anggota->EMAIL)) {
                $penerima->push([
                    'email' => trim($anggota->EMAIL),
                    'nama' => $anggota->NAMA_LENGKAP,
                    'peran' => $anggota->STATUS_TIM_SIDANG,
                ]);
            }
        }

        $kpps = DB::table('t_kpps as tk')
            ->leftJoin('T_USER as u', function ($join) {
                $join->on('u.id', '=', 'tk.ID_USER')->orOn('u.NIP_NIM', '=', 'tk.NIP');
            })
            ->where('tk.STATUS_AKTIF', 'AKTIF')
            ->orderByDesc('tk.STATUS_TIM')
            ->get(['u.NAMA_LENGKAP', 'u.EMAIL', 'tk.NAMA', 'tk.STATUS_TIM']);

        foreach ($kpps as $anggota) {
            if (filled($anggota->EMAIL)) {
                $penerima->push([
                    'email' => trim($anggota->EMAIL),
                    'nama' => filled($anggota->NAMA_LENGKAP) ? $anggota->NAMA_LENGKAP : $anggota->NAMA,
                    'peran' => filled($anggota->STATUS_TIM) ? "KPPS ({$anggota->STATUS_TIM})" : 'KPPS',
                ]);
            }
        }

        foreach ($emailFakultas as $email) {
            $penerima->push([
                'email' => $email,
                'nama' => 'Fakultas',
                'peran' => 'Fakultas (FS)',
            ]);
        }

        return $penerima->values();
    }

    private function susunDetailSidang(TAjuanSidang $ajuan): array
    {
        $tgl = $ajuan->TGL_SIDANG instanceof Carbon ? $ajuan->TGL_SIDANG : Carbon::parse($ajuan->TGL_SIDANG);

        $waktuRaw = $ajuan->getRawOriginal('WAKTU_SIDANG');
        $waktu = $waktuRaw ? Carbon::parse($waktuRaw)->format('H:i') : '-';

        return [
            'nama_mhs' => $ajuan->NAMA_MHS,
            'nim' => $ajuan->NIM,
            'judul' => $ajuan->JUDUL,
            'tahapan' => $ajuan->TAHAPAN_SIDANG,
            'tanggal' => sprintf(
                '%s, %d %s %d',
                self::HARI[$tgl->dayOfWeek] ?? '',
                $tgl->day,
                self::BULAN[$tgl->month] ?? '',
                $tgl->year
            ),
            'waktu' => $waktu,
            'ruang' => $ajuan->RUANG_SIDANG ?: '-',
        ];
    }
}
