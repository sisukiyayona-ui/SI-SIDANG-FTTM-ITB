<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DummyAuthMiddleware
{
    private const ABSOLUTE_TIMEOUT = 28800; // 8 hours max absolute session

    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('auth_user')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = session('auth_user');
        $createdAt = $user['session_created_at'] ?? 0;
        $duration = $user['session_duration'] ?? 21600;
        $now = now()->timestamp;
        $loginAt = $user['session_login_at'] ?? $createdAt;
        $absoluteRemaining = self::ABSOLUTE_TIMEOUT - ($now - $loginAt);

        if (($now - $createdAt) > $duration || $absoluteRemaining <= 0) {
            session()->invalidate();
            session()->regenerateToken();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Session expired', 'expired' => true], 401);
            }
            return redirect()->route('login')
                ->with('error', 'Session telah habis. Silakan login kembali.');
        }

        $user['session_last_activity'] = $now;
        session(['auth_user' => $user]);

        return $next($request);
    }
}
