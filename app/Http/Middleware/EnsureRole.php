<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();

        if (! $user || ! $user->is_active || ($user->role !== $role && $user->role !== 'superadmin')) {
            abort(403);
        }

        return $next($request);
    }
}
