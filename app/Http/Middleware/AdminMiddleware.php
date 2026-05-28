<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('user') || session('user')->role != 'admin') {

            return response('403 | Akses ditolak', 403);
        }

        return $next($request);
    }
}