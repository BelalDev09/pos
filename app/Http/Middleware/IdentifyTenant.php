<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        /**
         * Local development support
         * If running on localhost or 127.0.0.1, load first active tenant
         */
        if (in_array($host, ['127.0.0.1', 'localhost'])) {

            $tenant = Tenant::where('is_active', true)->first();

            if (!$tenant) {
                abort(500, 'No active tenant found for local environment.');
            }

            // Share tenant globally
            $request->attributes->set('tenant', $tenant);
            app()->instance('tenant', $tenant);
            view()->share('currentTenant', $tenant);

            return $next($request);
        }

        /**
         * Extract subdomain
         * Example:
         * shop1.example.com → shop1
         */
        $subdomain = explode('.', $host)[0];

        $tenant = Tenant::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->first();

        if (!$tenant) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tenant not found.'
                ], 404);
            }

            abort(404, 'Tenant not found.');
        }

        /**
         * Make tenant available globally
         */
        $request->attributes->set('tenant', $tenant);
        app()->instance('tenant', $tenant);
        view()->share('currentTenant', $tenant);

        return $next($request);
    }
}
