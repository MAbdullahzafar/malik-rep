<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// IMPORT: Pulls in the core pagination style controller
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
    }
}