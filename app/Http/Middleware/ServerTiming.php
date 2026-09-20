<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds a `Server-Timing: app;dur=<ms>` header and logs any request slower than the
 * 2-second target (OBJ-2 / QM-01 / AT-03).
 */
class ServerTiming
{
    public function handle(Request $request, Closure $next): Response
    {
        $start    = microtime(true);
        $response = $next($request);
        $ms       = (microtime(true) - $start) * 1000;

        $response->headers->set('Server-Timing', sprintf('app;dur=%.1f', $ms));

        if ($ms > 2000) {
            Log::warning(sprintf('Slow response (%.0f ms): %s %s', $ms, $request->method(), $request->path()));
        }

        return $response;
    }
}
