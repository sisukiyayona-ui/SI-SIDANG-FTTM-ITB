<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $this->syncRequestScheme($request);

        $nonce = bin2hex(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);

        $response = $next($request);

        $isHttps = $request->isSecure();

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        if ($isHttps) {
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        }

        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        if (app()->environment('production') && ($request->isMethod('GET') || $request->isMethod('POST'))) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline'; " .
               "style-src 'self' 'unsafe-inline'; " .
               "img-src 'self' data: https:; " .
               "font-src 'self'; " .
               "connect-src 'self'; " .
               "frame-ancestors 'none'; " .
               "form-action 'self'; " .
               "base-uri 'self';";

        if ($isHttps) {
            $csp .= " upgrade-insecure-requests;";
        }

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }

    private function syncRequestScheme(Request $request): void
    {
        $forwardedProto = strtolower((string) $request->headers->get('X-Forwarded-Proto'));
        $configured = parse_url((string) config('app.url'), PHP_URL_SCHEME);

        if ($forwardedProto === 'https' || $configured === 'https') {
            $request->server->set('HTTPS', 'on');
            $request->headers->set('X-Forwarded-Proto', 'https');

            return;
        }

        if ($configured === 'http') {
            $request->server->set('HTTPS', 'off');
        }
    }
}
