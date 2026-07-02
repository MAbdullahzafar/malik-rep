<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // FORCE BOOTSTRAP: Tells Laravel to stop using Tailwind CSS links
        Paginator::useBootstrapFive();

        // Force cookie state persistence configuration mappings across serverless hosting gates
        config(['session.driver' => 'cookie']);
        config(['session.domain' => '.vercel.app']);
        config(['session.secure' => true]);
        config(['session.same_site' => 'lax']);
    }
}
