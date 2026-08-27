<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    private const ABSOLUTE_TIMEOUT = 28800; // 8 hours max absolute session
    private const MAX_RENEWALS = 3;

    public function check(Request $request)
    {
        $user = session('auth_user');
        if (!$user) {
            return response()->json(['expired' => true], 401);
        }

        $createdAt = $user['session_created_at'] ?? 0;
        $duration = $user['session_duration'] ?? 21600;
        $now = now()->timestamp;
        $remaining = $duration - ($now - $createdAt);
        $absoluteRemaining = self::ABSOLUTE_TIMEOUT - ($now - ($user['session_login_at'] ?? $createdAt));

        if ($remaining <= 0 || $absoluteRemaining <= 0) {
            return response()->json(['expired' => true, 'remaining' => 0]);
        }

        return response()->json([
            'expired' => false,
            'remaining' => min($remaining, $absoluteRemaining),
            'show_warning' => $remaining <= 300,
            'absolute_remaining' => $absoluteRemaining,
        ]);
    }

    public function renew(Request $request)
    {
        $user = session('auth_user');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $now = now()->timestamp;
        $loginAt = $user['session_login_at'] ?? $user['session_created_at'] ?? $now;
        $absoluteRemaining = self::ABSOLUTE_TIMEOUT - ($now - $loginAt);

        if ($absoluteRemaining <= 0) {
            session()->invalidate();
            session()->regenerateToken();
            return response()->json(['expired' => true, 'message' => 'Session absolut telah habis. Silakan login kembali.'], 401);
        }

        $renewalCount = ($user['session_renewal_count'] ?? 0) + 1;
        if ($renewalCount > self::MAX_RENEWALS) {
            session()->invalidate();
            session()->regenerateToken();
            return response()->json(['expired' => true, 'message' => 'Batas perpanjangan session tercapai. Silakan login kembali.'], 401);
        }

        $user['session_created_at'] = $now;
        $user['session_last_activity'] = $now;
        $user['session_duration'] = 21600;
        $user['session_renewal_count'] = $renewalCount;
        $user['session_login_at'] = $loginAt;
        session(['auth_user' => $user]);

        return response()->json([
            'success' => true,
            'remaining' => min(21600, $absoluteRemaining),
            'renewal_count' => $renewalCount,
            'max_renewals' => self::MAX_RENEWALS,
        ]);
    }
}
