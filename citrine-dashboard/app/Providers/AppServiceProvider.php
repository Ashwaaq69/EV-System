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
        if (config('app.env') === 'production' && env('FORCE_HTTPS') === true) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        
        // Disabled prefetching to prevent Mixed Content errors on production prefetch resources
        // Vite::prefetch(concurrency: 3);
    }
}




