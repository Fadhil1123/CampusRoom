<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // =========================
        // CEK SUDAH LOGIN
        // =========================

        if (!session()->has('user')) {

            return redirect('/login');
        }

        // =========================
        // CEK ROLE ADMIN
        // =========================

        if (session('user')->role != 'admin') {

            abort(403, 'Akses ditolak');
        }

        return $next($request);
    }
}