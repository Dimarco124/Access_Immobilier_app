<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Forcer HTTPS en production, sur Render ou si le proxy est en HTTPS
        if (
            config('app.env') === 'production' ||
            str_contains(config('app.url'), 'https://') ||
            request()->header('X-Forwarded-Proto') === 'https' ||
            str_contains(request()->getHost(), 'onrender.com')
        ) {
            URL::forceScheme('https');
        }
    }
}
