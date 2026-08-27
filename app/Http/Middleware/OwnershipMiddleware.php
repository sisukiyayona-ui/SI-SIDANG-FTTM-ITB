<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnershipMiddleware
{
    private const PRIVILEGED_ROLES = ['Admin', 'TU Prodi', 'TU FS', 'FS', 'KPPS', 'Monev'];

    public function handle(Request $request, Closure $next)
    {
        $user = session('auth_user');
        if (!$user) {
            return redirect()->route('login');
        }

        $activeRole = $user['role'] ?? null;
        $userRoles = $user['roles'] ?? [];

        if ($activeRole === 'Admin' || in_array('Admin', $userRoles)) {
            return $next($request);
        }

        if (in_array($activeRole, self::PRIVILEGED_ROLES) || array_intersect(self::PRIVILEGED_ROLES, $userRoles)) {
            return $next($request);
        }

        $idJudul = $request->route('idJudul') ?? $request->input('id_judul') ?? session('active_judul_id');
        if ($idJudul) {
            $isOwner = DB::table('t_judul')
                ->where('id', $idJudul)
                ->where('id_user_mhs', $user['id'])
                ->exists();

            if ($isOwner) {
                return $next($request);
            }

            if (in_array($activeRole, ['Pembimbing', 'Penguji']) || in_array('Pembimbing', $userRoles) || in_array('Penguji', $userRoles)) {
                $isAssigned = DB::table('t_tim_sidang')
                    ->where('ID_JUDUL', $idJudul)
                    ->where('ID_USER_PENILAI', $user['id'])
                    ->exists();

                if ($isAssigned) {
                    return $next($request);
                }
            }

            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $next($request);
    }
}
