<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScopeTenant
{
    /**
     * Ensures the authenticated user belongs to the current tenant.
     * Prevents cross-tenant data access after login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user   = auth()->user();
        $tenant = app('tenant');

        if ($tenant && $user->tenant_id !== $tenant->id && !$user->is_super_admin) {
            auth()->logout();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access denied.'], 403);
            }

            return redirect()->route('login')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
