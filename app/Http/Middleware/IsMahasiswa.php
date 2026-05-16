<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsMahasiswa
{
    /**
     * Hanya izinkan akses untuk mahasiswa (is_admin = false).
     * Admin yang mengakses route mahasiswa akan diarahkan ke admin dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect('/');
        }

        if (auth()->user()->is_admin) {
            return redirect('/admin-dashboard');
        }

        return $next($request);
    }
}
