<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;

// Check if Telescope package exists before extending it to prevent production crashes
if (class_exists(\Laravel\Telescope\TelescopeApplicationServiceProvider::class)) {

    class TelescopeServiceProvider extends \Laravel\Telescope\TelescopeApplicationServiceProvider
    {
        /**
         * Register any application services.
         */
        public function register(): void
        {
            // Telescope::night();

            $this->hideSensitiveRequestDetails();

            $isLocal = $this->app->environment('local');

            Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
                return $isLocal ||
                       $entry->isReportableException() ||
                       $entry->isFailedRequest() ||
                       $entry->isFailedJob() ||
                       $entry->isScheduledTask() ||
                       $entry->hasMonitoredTag();
            });
        }

        /**
         * Prevent sensitive request details from being logged by Telescope.
         */
        protected function hideSensitiveRequestDetails(): void
        {
            if ($this->app->environment('local')) {
                return;
            }

            Telescope::hideRequestParameters(['_token']);

            Telescope::hideRequestHeaders([
                'cookie',
                'x-csrf-token',
                'x-xsrf-token',
            ]);
        }

        /**
         * Register the Telescope gate.
         *
         * This gate determines who can access Telescope in non-local environments.
         */
        protected function gate(): void
        {
            Gate::define('viewTelescope', function (User $user) {
                return in_array($user->email, [
                    //
                ]);
            });
        }
    }

} else {

    // Fallback dummy provider so Laravel's bootstrapper doesn't crash when it looks for this class
    class TelescopeServiceProvider extends \Illuminate\Support\ServiceProvider 
    {
        public function register(): void {}
        public function boot(): void {}
    }

}
