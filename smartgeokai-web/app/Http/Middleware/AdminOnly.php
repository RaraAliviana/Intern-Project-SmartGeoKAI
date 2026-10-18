<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = auth()->user()?->role;

        // Superadmin dan admin sama-sama boleh akses
        if (!in_array($role, ['superadmin', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}