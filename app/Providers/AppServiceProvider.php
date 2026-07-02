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

        // Let the application dynamically pull drivers from the vercel.json environment setup
        config(['session.secure' => true]);
        config(['session.same_site' => 'lax']);
    }
}
