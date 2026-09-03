<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AdminReadOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->hasRole('admin') && ! in_array($request->method(), ['GET', 'HEAD'])) {
            abort(403, 'Admin hanya memiliki akses baca pada modul ini.');
        }

        if ($request->user()?->hasRole('admin') && (Str::is('*/create', $request->path()) || Str::is('*/edit', $request->path()))) {
            abort(403, 'Admin hanya memiliki akses baca pada modul ini.');
        }

        return $next($request);
    }
}
