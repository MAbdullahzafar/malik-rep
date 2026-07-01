<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

// Dynamically handle the base class fallback cleanly without wrapping the class definition
if (class_exists(\Laravel\Telescope\TelescopeApplicationServiceProvider::class)) {
    class BaseTelescopeProvider extends \Laravel\Telescope\TelescopeApplicationServiceProvider {}
} else {
    class BaseTelescopeProvider extends ServiceProvider {}
}

class TelescopeServiceProvider extends BaseTelescopeProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (!class_exists(\Laravel\Telescope\Telescope::class)) {
            return;
        }

        $isLocal = $this->app->environment('local');

        \Laravel\Telescope\Telescope::filter(function ($entry) use ($isLocal) {
            return $isLocal ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Register the Telescope gate.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function (User $user) {
            return in_array($user->email, []);
        });
    }
}
