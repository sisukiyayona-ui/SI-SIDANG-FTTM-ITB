<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.dummy' => \App\Http\Middleware\DummyAuthMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'ownership' => \App\Http\Middleware\OwnershipMiddleware::class,
        ]);

        $middleware->trustProxies(at: '*');

        $middleware->prepend(\App\Http\Middleware\SecurityHeadersMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Upload melebihi batas PHP (post_max_size) biasanya menghasilkan halaman
        // HTML 413 sehingga frontend hanya menampilkan "Server error (HTTP 413)".
        // Kembalikan JSON agar pesan batas ukuran jelas untuk request AJAX/fetch.
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ukuran file terlalu besar untuk diproses server. Maksimal 10 MB per file — simpan file secara bertahap.',
                ], 413);
            }

            return null;
        });
    })->create();
