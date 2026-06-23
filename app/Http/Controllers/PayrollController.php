<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Teacher;
use App\Models\StaffMember;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('payable')->orderBy('created_at', 'desc')->get();
        return view('payrolls.index', compact('payrolls'));
    }

    public function create()
    {
        return view('payrolls.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'salary_month' => 'required|string', // e.g., "July-2026"
        ]);

        $month = $request->salary_month;

        // 1. Generate for Active Teachers
        $teachers = Teacher::all();
        foreach ($teachers as $teacher) {
            // Check if entry already exists to avoid duplication
            $exists = Payroll::where('payable_id', $teacher->id)
                ->where('payable_type', Teacher::class)
                ->where('salary_month', $month)
                ->exists();

            if (!$exists) {
                Payroll::create([
                    'payable_id' => $teacher->id,
                    'payable_type' => Teacher::class,
                    'salary_month' => $month,
                    'base_amount' => 45000.00, // Standard base salary allocation model rule baseline
                    'deductions' => 0.00,
                    'net_paid' => 45000.00,
                    'status' => 'Unpaid'
                ]);
            }
        }

        // 2. Generate for Operational Support Staff
        $staffMembers = StaffMember::where('status', 'Active')->get();
        foreach ($staffMembers as $staff) {
            $exists = Payroll::where('payable_id', $staff->id)
                ->where('payable_type', StaffMember::class)
                ->where('salary_month', $month)
                ->exists();

            if (!$exists) {
                Payroll::create([
                    'payable_id' => $staff->id,
                    'payable_type' => StaffMember::class,
                    'salary_month' => $month,
                    'base_amount' => $staff->base_salary,
                    'deductions' => 0.00,
                    'net_paid' => $staff->base_salary,
                    'status' => 'Unpaid'
                ]);
            }
        }

        return redirect()->route('payrolls.index')->with('success', "Payroll logs for $month generated successfully!");
    }

    public function markAsPaid($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->update([
            'status' => 'Paid',
            'payment_date' => now()->toDateString()
        ]);

        return redirect()->route('payrolls.index')->with('success', 'Salary transaction processed successfully!');
    }
}
