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
        if (env('FORCE_HTTPS') === true || env('FORCE_HTTPS') === 'true') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            if (config('app.url')) {
                \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
            }
            $this->app['request']->server->set('HTTPS', 'on');
        }
        
        // Disabled prefetching to prevent Mixed Content errors on production prefetch resources
        // Vite::prefetch(concurrency: 3);
    }
}




