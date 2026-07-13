<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of recorded transactions with rapid pagination and search filters.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = DB::table('payments')
            ->join('enrollments', 'payments.enrollment_id', '=', 'enrollments.id')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->select('payments.*', 'enrollments.fee as total_fee', 'students.name as student_name', 'students.id as student_id');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                // 🛡️ SCHEMA SAFETY CHECK: Fallback safely if receipt_no doesn't exist yet
                if (Schema::hasColumn('payments', 'receipt_no')) {
                    $q->where('payments.receipt_no', 'LIKE', "%{$search}%")
                      ->orWhere('students.name', 'LIKE', "%{$search}%");
                } else {
                    $q->where('students.name', 'LIKE', "%{$search}%");
                }
            });
        }

        $payments = $query->orderBy('payments.id', 'desc')->paginate(10);
        $enrollments = \App\Models\Student::with('enrollments')->get();
        $courses = DB::table('courses')->orderBy('name', 'asc')->get();

        return view('students.payment', compact('payments', 'enrollments', 'courses'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(): View
    {
        return view('students.payment');
    }

    /**
     * Store a newly created payment resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount'     => 'required|numeric|min:1',
            'paid_date'  => 'required|date'
        ]);

        $studentId     = $request->input('student_id');
        $paymentAmount = floatval($request->input('amount'));
        $paymentDate   = $request->input('paid_date');

        $enrollment = DB::table('enrollments')->where('student_id', $studentId)->first();
        if (!$enrollment) {
            return redirect()->back()->withErrors(['error' => 'Critical Error: No active student enrollment track discovered.']);
        }

        // 🛡️ CRITICAL SECURITY CHECKPOINT WALL: Is the baseline Admission Fee paid?
        $unpaidAdmissionRow = DB::table('payment_installments')
            ->where('student_id', $studentId)
            ->where(function($query) {
                $query->where('installment_number', 0)
                      ->orWhere('installment_number', 'LIKE', '%Admission%')
                      ->orWhere('status', 'SETTLED');
            })
            ->where('status', '!=', 'Paid')
            ->first();

        // 🛑 COMPULSORY REJECTION GATE: Block tuition processing if admission is unpaid
        if ($unpaidAdmissionRow) {
            $admissionDueRemaining = floatval($unpaidAdmissionRow->base_amount) - floatval($unpaidAdmissionRow->amount_paid);
            
            if ($paymentAmount < $admissionDueRemaining) {
                return redirect()->back()->withInput()->withErrors([
                    'error' => "❌ CRITICAL SECURITY BLOCK: Monthly course fee installments are locked! This student has an unpaid separate Admission Fee. You must pay at least Rs. " . number_format($admissionDueRemaining, 2) . " to settle the Admission Fee first before any course fees can be processed."
                ]);
            }
        }

        $courseFee = DB::table('courses')->where('id', $enrollment->course_id)->value('fee') ?? 0.00;
        $receiptNo = 'REC-' . time() . '-' . $studentId;

        DB::beginTransaction();
        try {
            $paymentData = [
                'enrollment_id' => $enrollment->id,
                'amount'        => $paymentAmount,
                'payment_date'  => $paymentDate,
                'total_fee'     => $courseFee,
                'created_at'    => now(),
                'updated_at'    => now()
            ];

            // 🛡️ SCHEMA SAFETY CHECK: Inject key attribute fields conditionally
            if (Schema::hasColumn('payments', 'receipt_no')) {
                $paymentData['receipt_no'] = $receiptNo;
            }

            // Write to master history payment transaction logs safely
            DB::table('payments')->insert($paymentData);

            $remainingCash = $paymentAmount;

            // Step A: Clear the Admission Fee if it hasn't been closed out yet
            if ($unpaidAdmissionRow) {
                $admissionDueRemaining = floatval($unpaidAdmissionRow->base_amount) - floatval($unpaidAdmissionRow->amount_paid);
                
                DB::table('payment_installments')->where('id', $unpaidAdmissionRow->id)->update([
                    'amount_paid' => floatval($unpaidAdmissionRow->base_amount),
                    'status'      => 'Paid',
                    'paid_date'   => $paymentDate,
                    'updated_at'  => now()
                ]);
                $remainingCash = round($remainingCash - $admissionDueRemaining, 2);
            }
            // Step B: Only apply left-over funds to standard monthly installments if any remain
            if ($remainingCash > 0) {
                $pendingInstallments = DB::table('payment_installments')
                    ->where('student_id', $studentId)
                    ->where('status', '!=', 'Paid')
                    ->where('installment_number', '>', 0)
                    ->orderBy('installment_number', 'asc')
                    ->lockForUpdate()
                    ->get();

                $collectionDate = Carbon::parse($paymentDate);

                foreach ($pendingInstallments as $inst) {
                    if ($remainingCash <= 0) break;

                    $dueDate = Carbon::parse($inst->due_date);
                    $fineCharged = 0.00;

                    if ($collectionDate->greaterThan($dueDate)) {
                        $daysLate = $collectionDate->diffInDays($dueDate);
                        $fineCharged = $daysLate * 50.00;
                    }

                    $currentBaseDue = floatval($inst->base_amount) - floatval($inst->amount_paid);
                    $totalMilestonePayable = $currentBaseDue + $fineCharged;

                    if ($remainingCash >= $totalMilestonePayable) {
                        DB::table('payment_installments')->where('id', $inst->id)->update([
                            'amount_paid'  => floatval($inst->base_amount),
                            'fine_charged' => $fineCharged,
                            'status'       => 'Paid',
                            'paid_date'    => $paymentDate,
                            'updated_at'   => now()
                        ]);
                        $remainingCash = round($remainingCash - $totalMilestonePayable, 2);
                    } else {
                        $newPaidAmount = floatval($inst->amount_paid);
                        if ($remainingCash > $fineCharged) {
                            $remainderForBase = $remainingCash - $fineCharged;
                            $newPaidAmount += $remainderForBase;
                        }

                        DB::table('payment_installments')->where('id', $inst->id)->update([
                            'amount_paid'  => $newPaidAmount,
                            'fine_charged' => $fineCharged,
                            'status'       => 'Partially Paid',
                            'updated_at'   => now()
                        ]);
                        $remainingCash = 0.00;
                    }
                }
            }

            DB::commit();
            return redirect()->route('payments.index')->with('flash_message', '🎉 Transaction processed cleanly! Separate baseline admission requirements have been successfully verified.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Processing Matrix Failure: ' . $e->getMessage()]);
        }
    }

    /**
     * Handles edited form records safely to prevent route crashes.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'enrollment_id' => 'required',
            'payment_date'  => 'required|date',
            'amount'        => 'required|numeric|min:1'
        ]);

        DB::table('payments')->where('id', $id)->update([
            'enrollment_id' => $request->input('enrollment_id'),
            'payment_date'  => $request->input('payment_date'),
            'amount'        => $request->input('amount'),
            'updated_at'    => now()
        ]);

        return redirect()->route('payments.index')->with('flash_message', 'Payment updated successfully.');
    }

    /**
     * Cleans up single transaction logs from database records cleanly.
     */
    public function destroy(string $id): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $payment = DB::table('payments')->where('id', $id)->first();
            if (!$payment) {
                return redirect()->back()->withErrors(['error' => 'Payment log entry not found.']);
            }

            $enrollment = DB::table('enrollments')->where('id', $payment->enrollment_id)->first();
            if ($enrollment) {
                DB::table('payment_installments')
                    ->where('student_id', $enrollment->student_id)
                    ->update([
                        'amount_paid'  => 0.00,
                        'fine_charged' => 0.00,
                        'status'       => 'Unpaid',
                        'paid_date'    => null,
                        'updated_at'   => now()
                    ]);
            }

            DB::table('payments')->where('id', $id)->delete();
            DB::commit();
            return redirect()->route('payments.index')->with('flash_message', '🗑️ Payment transaction wiped out cleanly and balances reset successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Rollback Exception Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Async API endpoint called by JavaScript to populate dynamic modal elements in real time.
     */
    public function getEnrollmentDetails($studentId)
    {
        $enrollment = DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->where('enrollments.student_id', $studentId)
            ->select('enrollments.id as enrollment_id', 'courses.name as course_name', 'courses.fee as total_course_fee')
            ->first();

        if ($enrollment) {
            return response()->json([
                'success'       => true,
                'enrollment_id' => $enrollment->enrollment_id,
                'course_name'   => $enrollment->course_name,
                'total_course_fee' => $enrollment->total_course_fee
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No registration matrix found.']);
    }

    /**
     * Enforces late grace periods and prints the 3-Column Bank Fee Voucher Layout.
     */
    public function print(string $id): View
    {
        $payment = DB::table('payments')
            ->join('enrollments', 'payments.enrollment_id', '=', 'enrollments.id')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->where('payments.id', $id)
            ->select('payments.*', 'students.name as student_name', 'students.mobile as student_mobile', 'students.reg_no as student_reg_no', 'students.id as student_id', 'courses.name as course_name')
            ->first();

        if (!$payment) {
            abort(404, 'Payment transaction receipt registry footprint absent.');
        }

        $transactionDate = Carbon::parse($payment->payment_date);
        $payment->flat_late_fine = 0.00;

        if ($transactionDate->day > 10) {
            $payment->flat_late_fine = 500.00;
            $payment->calculated_due_date = Carbon::parse($payment->payment_date)->addMonth()->day(10)->format('d-M-Y');
        } else {
            $payment->calculated_due_date = Carbon::parse($payment->payment_date)->day(10)->format('d-M-Y');
        }

        return view('students.voucher-print', compact('payment'));
    }
}
