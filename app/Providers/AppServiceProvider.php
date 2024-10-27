<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        RateLimiter::for('articles', function ($request) {
            return [
                Limit::perSecond(5)->by(auth()->id()),
                Limit::perMinute(50)->by(auth()->id()),
            ];
        });

        RateLimiter::for('preferences', function ($request) {
            return [
                Limit::perSecond(20)->by(auth()->id()),
                Limit::perMinute(100)->by(auth()->id()),
            ];
        });
    }
}
