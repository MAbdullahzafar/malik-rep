<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 🌟 AUTOMATED MIDNIGHT BALANCING GUARD:
        // Automatically updates overdue installment milestones balances and late fines at exactly 12:00 AM every night [INDEX].
        $schedule->command('finance:update-fines')->dailyAt('00:00')->timezone('Asia/Karachi');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}