<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreAccess
{
    /**
     * Verifies the user has access to the store they're trying to operate on.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user    = auth()->user();
        $storeId = $request->route('store')
            ?? $request->input('store_id')
            ?? session('active_store_id');

        if (!$storeId) {
            return $next($request);
        }

        // Super admins and tenant owners can access all stores
        if ($user->is_super_admin || $user->hasRole(['super_admin', 'tenant_owner'])) {
            return $next($request);
        }

        // Other users must be assigned to the store
        if ((int) $user->store_id !== (int) $storeId) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You do not have access to this store.'], 403);
            }
            abort(403, 'You do not have access to this store.');
        }

        return $next($request);
    }
}
