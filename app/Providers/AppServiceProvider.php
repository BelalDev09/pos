<?php

namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Apple\Provider as AppleProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Socialite::extend('apple', static function ($app) {
            $config = $app['config']['services.apple'];

            return new AppleProvider(
                $app['request'],
                $config['client_id'],
                $config['client_secret'],
                $config['redirect']
            );
        });

        // qr code data route
        \Illuminate\Http\Request::setTrustedProxies(
            ['*'],
            \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
        );
        /**
         * notifications issue solve
         */
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();

                $notifications = $user->notifications()->latest()->take(10)->get();
                $unreadCount = $user->notifications()->whereNull('read_at')->count();
            } else {
                $notifications = collect();
                $unreadCount = 0;
            }

            $view->with(compact('notifications', 'unreadCount'));
        });
    }
}
