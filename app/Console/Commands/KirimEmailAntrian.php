<?php

namespace App\Console\Commands;

use App\Mail\NotifikasiApproveAjuanMail;
use App\Models\TEmailQueue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class KirimEmailAntrian extends Command
{
    protected $signature = 'email:kirim-antrian
        {--limit=50 : Maksimal email per run}
        {--dry-run : Tampilkan antrean tanpa mengirim}';

    protected $description = 'Kirim email pending dari t_email_queue (dipanggil cron)';

    public function handle(): int
    {
        $lock = Cache::lock('sidang:email-kirim-antrian', 300);

        if (!$lock->get()) {
            $this->warn('Proses email:kirim-antrian lain sedang berjalan. Dilewati.');

            return self::SUCCESS;
        }

        try {
            return $this->proses();
        } finally {
            $lock->release();
        }
    }

    private function proses(): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $dryRun = (bool) $this->option('dry-run');

        $now = now();
        $query = TEmailQueue::query()
            ->where('STATUS', 'pending')
            ->where(function ($q) use ($now) {
                $q->whereNull('NEXT_ATTEMPT_AT')->orWhere('NEXT_ATTEMPT_AT', '<=', $now);
            })
            ->orderBy('id')
            ->limit($limit);

        $items = $query->get();

        if ($items->isEmpty()) {
            $this->info('Tidak ada email pending.');

            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($items as $row) {
            $email = $row->EMAIL;
            $nama = $row->NAMA_PENERIMA ?? '-';

            if ($dryRun) {
                $this->line("[DRY-RUN] #{$row->id} {$row->TIPE} -> {$email} ({$nama})");
                continue;
            }

            try {
                $payload = $row->PAYLOAD;
                if (!is_array($payload)) {
                    $payload = json_decode((string) $row->PAYLOAD, true) ?: [];
                }

                $mail = match ($row->TIPE) {
                    'notifikasi_approve' => new NotifikasiApproveAjuanMail($payload),
                    default => throw new \RuntimeException('Tipe email tidak dikenal: ' . $row->TIPE),
                };

                Mail::to($email)->send($mail);

                if ($row->TIPE === 'notifikasi_approve' && $row->ID_AJUAN_SIDANG) {
                    $alreadyLogged = DB::table('t_notif_approve_log')
                        ->where('ID_AJUAN_SIDANG', $row->ID_AJUAN_SIDANG)
                        ->where('EMAIL', $email)
                        ->exists();

                    if (!$alreadyLogged) {
                        DB::table('t_notif_approve_log')->insert([
                            'ID_AJUAN_SIDANG' => $row->ID_AJUAN_SIDANG,
                            'ID_USER_KPPS' => $row->ID_USER_PENERIMA ?? 0,
                            'EMAIL' => $email,
                            'TGL_KIRIM' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                $row->update([
                    'STATUS' => 'sent',
                    'ATTEMPTS' => $row->ATTEMPTS + 1,
                    'LAST_ERROR' => null,
                    'TGL_KIRIM' => now(),
                ]);

                $sent++;
                $this->info("Terkirim #{$row->id} -> {$email}");
            } catch (Throwable $e) {
                $attempts = $row->ATTEMPTS + 1;
                $status = $attempts >= 3 ? 'failed' : 'pending';

                $row->update([
                    'STATUS' => $status,
                    'ATTEMPTS' => $attempts,
                    'LAST_ERROR' => $e->getMessage(),
                    'NEXT_ATTEMPT_AT' => $status === 'pending' ? now()->addMinutes(5 * $attempts) : null,
                ]);

                $failed++;
                $this->error("Gagal #{$row->id} -> {$email}: {$e->getMessage()}");
            }
        }

        $this->info('Selesai. Terkirim: ' . $sent . ', Gagal: ' . $failed . '.');

        return self::SUCCESS;
    }
}
