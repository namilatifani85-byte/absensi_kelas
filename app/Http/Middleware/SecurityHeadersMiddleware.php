<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware Keamanan Header HTTP (SEC-09 Safe).
 * Menerapkan Content-Security-Policy (CSP), nosniff, X-Frame-Options DENY,
 * dan Referrer-Policy untuk mitigasi serangan Stored/Reflected XSS, Clickjacking, dan MIME sniffing.
 */
class SecurityHeadersMiddleware
{
    /**
     * Menambahkan header keamanan pada setiap respons HTTP aplikasi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; object-src 'none'; frame-ancestors 'none';"
        );

        return $response;
    }
}
