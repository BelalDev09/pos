<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if (!$user->tenant_id && !$user->is_super_admin) {
            abort(403, 'Tenant not assigned');
        }

        if ($user->tenant_id) {
            app()->instance('tenant_id', $user->tenant_id);
            config(['app.tenant_id' => $user->tenant_id]);
            $request->attributes->set('tenant_id', $user->tenant_id);
        }

        return $next($request);
    }
}
