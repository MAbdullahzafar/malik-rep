<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateLateFines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:update-fines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Background maintenance task to automatically evaluate overdue installments past the 10th and apply fines daily.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today('Asia/Karachi');

        // Locate all non-compliant installment milestones past their due dates
        $overdueInstallments = DB::table('payment_installments')
            ->where('status', '!=', 'Paid')
            ->where('due_date', '<', $today->toDateString())
            ->get();

        $updatedCount = 0;

        foreach ($overdueInstallments as $installment) {
            $dueDate = Carbon::parse($installment->due_date);
            $daysLate = $today->diffInDays($dueDate);

            if ($daysLate > 0) {
                // Multiplies the daily rate threshold past the deadline cleanly
                $calculatedFine = $daysLate * 50.00;

                DB::table('payment_installments')
                    ->where('id', $installment->id)
                    ->update([
                        'fine_charged' => $calculatedFine,
                        'updated_at'   => now()
                    ]);
                
                $updatedCount++;
            }
        }

        $this->info("⚡ Success: Dynamic Background Fine Engine completed. Updated {$updatedCount} overdue accounts.");
    }
}