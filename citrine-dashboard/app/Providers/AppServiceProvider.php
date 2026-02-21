<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
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
        // Force HTTPS if the APP_URL starts with https
        if (str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        if (env('FORCE_HTTPS') === true || env('FORCE_HTTPS') === 'true') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            if (config('app.url')) {
                \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
            }
        }
        
        // Disabled prefetching to prevent Mixed Content errors on production prefetch resources
        // Vite::prefetch(concurrency: 3);
    }
}




