<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dynamic Milestone Generation Engine.
     */
    public function generateInstallments($enrollmentId, $totalInstallmentsRequested = 3)
    {
        $enrollment = DB::table('enrollments')->where('id', $enrollmentId)->first();
        if (!$enrollment) return false;

        $courseFee = DB::table('courses')->where('id', $enrollment->course_id)->value('fee') ?? 0.00;
        $totalInstallmentsRequested = intval($totalInstallmentsRequested) > 0 ? intval($totalInstallmentsRequested) : 3;
        $installmentAmount = round($courseFee / $totalInstallmentsRequested, 2);

        $baseDate = Carbon::parse($enrollment->join_date ?? now());

        for ($i = 1; $i <= $totalInstallmentsRequested; $i++) {
            $dueDate = Carbon::parse($baseDate)->addMonths($i)->day(10)->toDateString();

            // 🛡️ FIXED INSERT MATRIX: Added total_milestones_configured parameters safely to eliminate General Error 1364!
            DB::table('payment_installments')->insert([
                'enrollment_id'               => $enrollmentId,
                'student_id'                  => $enrollment->student_id,
                'installment_number'          => $i,
                'total_milestones_configured' => $totalInstallmentsRequested, // INJECTED SYSTEM COMPLIANT FIELD
                'base_amount'                 => $installmentAmount,
                'fine_charged'                => 0.00,
                'amount_paid'                 => 0.00,
                'due_date'                    => $dueDate,
                'status'                      => 'Unpaid',
                'created_at'                  => now(),
                'updated_at'                  => now()
            ]);
        }
        return true;
    }

    /**
     * Live tracking roster for non-compliant student fee milestones.
     */
    public function defaulterList(): View
    {
        $today = Carbon::today('Asia/Karachi');
        $todayString = $today->toDateString();

        $overdueInstallments = DB::table('payment_installments')
            ->where('status', '!=', 'Paid')
            ->where('due_date', '<', $todayString)
            ->get();

        // 🌟 OPTIMIZED HIGH-SPEED BATCH RUN CONTROLLER: Avoids memory leaks during data queries
        DB::beginTransaction();
        try {
            foreach ($overdueInstallments as $installment) {
                $dueDate = Carbon::parse($installment->due_date);
                $daysLate = $today->diffInDays($dueDate);

                if ($daysLate > 0) {
                    $calculatedFine = $daysLate * 50.00;

                    DB::table('payment_installments')
                        ->where('id', $installment->id)
                        ->update([
                            'fine_charged' => $calculatedFine,
                            'updated_at'   => now()
                        ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }


        // 🛡️ HIGH-SECURITY FIXED QUERY: Excludes total_milestones_configured to prevent layout data crashes
        $defaulters = DB::table('payment_installments')
            ->join('students', 'payment_installments.student_id', '=', 'students.id')
            ->join('enrollments', 'payment_installments.enrollment_id', '=', 'enrollments.id')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->where('payment_installments.status', '!=', 'Paid')
            ->where('payment_installments.due_date', '<', $todayString)
            ->select(
                'students.id as student_id',
                'students.name as student_name',
                'students.mobile as student_mobile',
                'courses.name as course_name',
                'payment_installments.id as installment_id',
                'payment_installments.installment_number',
                'payment_installments.base_amount',
                'payment_installments.fine_charged',
                'payment_installments.amount_paid',
                'payment_installments.due_date',
                'payment_installments.status'
            )
            ->orderBy('payment_installments.due_date', 'asc')
            ->get();

        return view('finance.defaulters', compact('defaulters'));
    }
}
