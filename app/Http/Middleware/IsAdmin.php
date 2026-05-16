<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Hanya izinkan akses untuk user dengan is_admin = true.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->is_admin) {
            abort(403, 'Akses ditolak — hanya untuk admin.');
        }

        return $next($request);
    }
}
