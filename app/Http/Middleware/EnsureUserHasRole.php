<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     * Membatasi akses rute berdasarkan peran pengguna (lecturer / student).
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak otorisasi untuk melakukan tindakan ini.');
        }

        return $next($request);
    }
}
