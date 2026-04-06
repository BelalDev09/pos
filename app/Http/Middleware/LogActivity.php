<?php

// File: app/Http/Middleware/LogActivity.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    private const LOGGABLE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ✅ শুধু authenticated user থাকলে এবং write methods এ log করব
        if (
            Auth::check()
            && in_array($request->method(), self::LOGGABLE_METHODS, true)
            && $response->isSuccessful()
        ) {
            try {
                $this->log($request);
            } catch (\Throwable $e) {
                // Log error হলেও request block করব না
                // silent fail
            }
        }

        return $response;
    }

    private function log(Request $request): void
    {
        $user = Auth::user();

        DB::table('activity_logs')->insert([
            'tenant_id'   => $user->tenant_id ?? null,
            'user_id'     => $user->id,
            'action'      => $request->method(),
            'module'      => $this->resolveModule($request),
            'description' => $request->method() . ' ' . $request->path(),
            'ip_address'  => $request->ip(),
            'user_agent'  => substr($request->userAgent() ?? '', 0, 255),
            'created_at'  => now(),
        ]);
    }

    private function resolveModule(Request $request): string
    {
        $segments = explode('/', trim($request->path(), '/'));
        return ucfirst($segments[0] ?? 'general');
    }
}
