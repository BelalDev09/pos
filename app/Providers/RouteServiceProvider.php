<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // ...
        });
    }

    protected function configureRateLimiting()
    {
        // General API limit
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

        // Authentication endpoints stricter limit
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // POS cart (cashier) highest limit
        RateLimiter::for('pos-cart', function (Request $request) {
            return Limit::perMinute(300)->by($request->user()->id);
        });
    }
}
