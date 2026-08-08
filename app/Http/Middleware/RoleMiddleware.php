<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = session('auth_user');

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $roles = is_array($roles) ? $roles : [$roles];

        $userRoles = $user['roles'] ?? [$user['role'] ?? null];

        if (empty(array_intersect($roles, $userRoles))) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
