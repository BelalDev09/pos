<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleOrAdmin
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // Superadmin / Admin automatic access
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return $next($request);
        }

        // Normal role check
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access.');
    }
}
