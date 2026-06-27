<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Enforce secure HTTPS link generation globally across all live Vercel domains
        if (config('app.env') === 'production' || isset($_ENV['VERCEL_ENV'])) {
            URL::forceScheme('https');
        }
    }
}
