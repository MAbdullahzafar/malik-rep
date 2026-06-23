<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\TeacherAttendanceLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TeacherAnalyticsController extends Controller
{
    /**
     * Display historical performance compliance metrics for all faculty staff.
     */
    public function history(Request $request)
    {
        $teachers = Teacher::all();
        
        // Default lookup parameters to current month bounds cleanly
        $selectedTeacherId = $request->input('teacher_id');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $logs = collect();
        $analyticsSummary = [
            'total_days' => 0,
            'late_count' => 0,
            'short_hours_days' => 0,
            'total_hours_completed' => 0
        ];

        if ($selectedTeacherId) {
            // Query previous history records matrix bounded by date range inputs
            $logs = TeacherAttendanceLog::where('teacher_id', $selectedTeacherId)
                ->whereBetween('log_date', [$startDate, $endDate])
                ->orderBy('log_date', 'desc')
                ->get();

            // Compute industrial analytical compliance benchmarks
            $totalMinutes = 0;
            foreach ($logs as $log) {
                $analyticsSummary['total_days']++;
                
                // 1. Check Standard Shift Time Compliance Rule (Lateness boundary 08:00 AM)
                if (Carbon::parse($log->check_in)->gt(Carbon::parse('08:05:00'))) {
                    $analyticsSummary['late_count']++;
                }

                // 2. Compute Shift Time Deficits
                if ($log->check_in && $log->check_out) {
                    $in = Carbon::parse($log->check_in);
                    $out = Carbon::parse($log->check_out);
                    $diffMinutes = $in->diffInMinutes($out);
                    
                    $totalMinutes += $diffMinutes;

                    // If worked hours fall below the standard 8-hour requirement (480 minutes)
                    if ($diffMinutes < 480) {
                        $analyticsSummary['short_hours_days']++;
                    }
                }
            }
            $analyticsSummary['total_hours_completed'] = round($totalMinutes / 60, 1);
        }

        return view('analytics.teacher_history', compact('teachers', 'logs', 'startDate', 'endDate', 'selectedTeacherId', 'analyticsSummary'));
    }
}
