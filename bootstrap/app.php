<?php

use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\ServerTiming;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        // Health-check endpoint: point Uptime Kuma (QM-03) at /up
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Guests hitting an `auth` route are sent to route('login') by default.
        // ServerTiming -> supports OBJ-2 / QM-01 (sub-2-second load)
        // SecurityHeaders -> supports RSK-02 / QM-02 (HTTPS-only, hardening)
        $middleware->web(append: [
            ServerTiming::class,
            SecurityHeaders::class,
        ]);

        // Register named middleware aliases
        // Behind a hosting platform's HTTPS proxy (Render, Railway, Fly, ...) set TRUSTED_PROXIES=*
        // so Laravel knows the original request was https (secure cookies, correct links, HSTS).
        if ($proxies = env('TRUSTED_PROXIES')) {
            $middleware->trustProxies(at: $proxies === '*' ? '*' : array_map('trim', explode(',', $proxies)));
        }

        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);

        // If the app runs behind a cloud load balancer (multi-AZ, RSK-04),
        // uncomment so Laravel sees the original HTTPS scheme:
        // $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
