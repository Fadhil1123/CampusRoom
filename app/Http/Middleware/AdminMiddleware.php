<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user_id')) {
            return redirect('/login');
        }

        if (session('role') != 'admin') {
            abort(403, 'Akses ditolak');
        }

        return $next($request);
    }
}