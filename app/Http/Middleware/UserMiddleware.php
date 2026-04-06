<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
            $user = JWTAuth::parseToken()->authenticate();
            if ($user->role === 'user') {
                return $next($request);
            }
            return response()->json([
                'status' => 'You are not authorized to access this resource'
            ], 401);
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'status' => 'Token is Invalid'
                ], 401);
            } else if ($e instanceof TokenExpiredException) {
                return response()->json([
                    'status' => 'Token is Expired'
                ], 401);
            } else {
                return response()->json([
                    'status' => 'You are not authorized to access this resource'
                ], 401);
            }
        }
    }
}
