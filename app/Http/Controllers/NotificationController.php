<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\TAjuanSidang;
use App\Services\DummyAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    protected DummyAuthService $auth;

    public function __construct(DummyAuthService $auth)
    {
        $this->auth = $auth;
    }

    public function index()
    {
        $user = $this->auth->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $pending = $this->pendingAjuanNotifications($user);

        $stored = Notification::forUser($user['id'])
            ->latest()
            ->limit(20)
            ->get();

        $notifications = $pending
            ->concat($stored)
            ->sortByDesc('created_at')
            ->take(20)
            ->values();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $pending->count() + Notification::unreadCountForUser($user['id']),
        ]);
    }

    public function unreadCount()
    {
        $user = $this->auth->user();
        if (!$user) {
            return response()->json(['count' => 0]);
        }

        return response()->json([
            'count' => $this->pendingAjuanNotifications($user)->count() + Notification::unreadCountForUser($user['id']),
        ]);
    }

    public function markAsRead($id)
    {
        $user = $this->auth->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ((int) $id < 0) {
            $ajuanId = abs((int) $id);
            $this->markAjuanAsRead($user['id'], $ajuanId);
            return response()->json(['success' => true]);
        }

        $notification = Notification::forUser($user['id'])->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = $this->auth->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Notification::forUser($user['id'])->unread()->update(['is_read' => true]);

        $pendingAjuanIds = $this->pendingAjuanNotifications($user)
            ->pluck('id')
            ->map(fn ($id) => abs((int) $id));

        foreach ($pendingAjuanIds as $ajuanId) {
            $this->markAjuanAsRead($user['id'], $ajuanId);
        }

        return response()->json(['success' => true]);
    }

    protected function markAjuanAsRead(int $userId, int $ajuanId): void
    {
        $exists = DB::table('t_notif_read')
            ->where('ID_USER', $userId)
            ->where('ID_AJUAN', $ajuanId)
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('t_notif_read')->insert([
            'ID_USER' => $userId,
            'ID_AJUAN' => $ajuanId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function ajuanLink(TAjuanSidang $a): string
    {
        $strata = strtoupper((string) ($a->STRATA ?? ''));
        $route = match ($strata) {
            'S1' => 'sidang.s1',
            'S2' => 'sidang.s2',
            default => 'sidang.s3',
        };

        return route($route);
    }

    protected function ajuanLinkForRole(TAjuanSidang $a, string $role): string
    {
        if ($role === 'KPPS') {
            $strata = strtoupper((string) ($a->STRATA ?? ''));
            return route('sidang.approve-ajuan.index', ['strata' => $strata]);
        }

        return $this->ajuanLink($a);
    }

    /**
     * Notifikasi dinamis dari pengajuan sidang yang menunggu aksi.
     *
     * - TU Prodi: ajuan yang sudah disubmit mahasiswa (STATUS_AJUKAN_MHS='t')
     *   tapi belum disetujui prodi (STATUS_AJUKAN_PRODI IS NULL), khusus prodi sendiri.
     * - FS: ajuan yang sudah disetujui prodi (STATUS_AJUKAN_PRODI='t'),
     *   semua prodi.
     */
    protected function pendingAjuanNotifications(array $user): \Illuminate\Support\Collection
    {
        $role = $user['role'] ?? null;

        if (!in_array($role, ['TU Prodi', 'FS', 'KPPS'], true)) {
            return collect();
        }

        $query = TAjuanSidang::query()
            ->whereNotNull('JUDUL');

        if ($role === 'TU Prodi') {
            $query->where('STATUS_AJUKAN_MHS', 'y')
                ->where(function ($q) {
                    $q->whereNull('STATUS_AJUKAN_PRODI')
                        ->orWhere('STATUS_AJUKAN_PRODI', '');
                })
                ->where('KODE_PRODI', $user['kode_prodi'] ?? null);
        } elseif ($role === 'FS') {
            $query->where('STATUS_AJUKAN_MHS', 'y')
                ->where('STATUS_AJUKAN_PRODI', 'y');
        } elseif ($role === 'KPPS') {
            $query->where('STATUS_AJUKAN_KPPS', 'y');
        }

        $readAjuanIds = DB::table('t_notif_read')
            ->where('ID_USER', $user['id'])
            ->pluck('ID_AJUAN');

        if ($readAjuanIds->isNotEmpty()) {
            $query->whereNotIn('id', $readAjuanIds);
        }

        return $query
            ->orderByDesc('TGL_CREATE')
            ->get()
            ->map(function (TAjuanSidang $a) use ($role) {
                $message = trim($a->NAMA_MHS . ' (' . $a->NIM . ') - ' . $a->TAHAPAN_SIDANG . ' - ' . ($a->NAMA_PRODI ?? ''));
                if ($role === 'KPPS' && $a->TGL_AJUKAN_KPPS) {
                    $message .= ' - diajukan KPPS ' . $a->TGL_AJUKAN_KPPS->format('d M Y');
                }

                return [
                    'id' => -$a->id,
                    'type' => 'ajuan_sidang',
                    'title' => 'Pengajuan Sidang Baru',
                    'message' => $message,
                    'link' => $this->ajuanLinkForRole($a, $role),
                    'is_read' => false,
                    'created_at' => $a->TGL_CREATE?->toDateTimeString() ?? now()->toDateTimeString(),
                ];
            });
    }
}
