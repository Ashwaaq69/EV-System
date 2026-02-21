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
        // UNCONDITIONAL Brute-force HTTPS for production proxy
        \Illuminate\Support\Facades\URL::forceScheme('https');
        if (config('app.url')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
        }

        // Force server variables for proxy-to-container HTTPS termination
        $this->app['request']->server->set('HTTPS', 'on');
        $this->app['request']->server->set('SERVER_PORT', 443);
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = 443;
        
        // Disabled prefetching to prevent Mixed Content errors
        // Vite::prefetch(concurrency: 3);
    }
}




