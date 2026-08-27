<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DummyAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('auth_user')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
