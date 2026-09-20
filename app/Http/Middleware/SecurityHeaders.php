<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RSK-02 / QM-02: HTTPS-only traffic and baseline hardening headers.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isProduction() && ! $request->isSecure()) {
            return redirect()->secure($request->getRequestUri());
        }

        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
