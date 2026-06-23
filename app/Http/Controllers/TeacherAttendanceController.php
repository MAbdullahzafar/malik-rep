<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\TeacherBiometric;
use App\Models\TeacherAttendanceLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TeacherAttendanceController extends Controller
{
    /**
     * Display the unified teacher attendance workspace containing both live console and history ledger.
     */
    public function index(Request $request)
    {
        // 🌟 TIMEZONE FIXED: Force Pakistan Standard Time during initial index loads
        $today = Carbon::today('Asia/Karachi')->toDateString();
        $teachers = Teacher::all();

        // Pull live daily roster data alongside any attendance logged today
        $liveTeachers = Teacher::with(['attendanceLogs' => function($query) use ($today) {
            $query->where('log_date', $today);
        }])->get();

        // History Ledger filters
        $selectedTeacherId = $request->input('teacher_id');
        $startDate = $request->input('start_date', Carbon::now('Asia/Karachi')->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now('Asia/Karachi')->endOfMonth()->toDateString());
        $activeTab = $request->input('tab', 'console'); 

        $historyLogs = collect();
        $analyticsSummary = [
            'total_days' => 0,
            'late_count' => 0,
            'short_hours_days' => 0,
            'total_hours_completed' => 0
        ];

        if ($selectedTeacherId) {
            $activeTab = 'history'; 
            
            $historyLogs = TeacherAttendanceLog::where('teacher_id', $selectedTeacherId)
                ->whereBetween('log_date', [$startDate, $endDate])
                ->orderBy('log_date', 'desc')
                ->get();

            $totalMinutes = 0;
            foreach ($historyLogs as $log) {
                $analyticsSummary['total_days']++;
                
                if ($log->check_in && Carbon::parse($log->check_in)->gt(Carbon::parse('08:05:00'))) {
                    $analyticsSummary['late_count']++;
                }

                if ($log->check_in && $log->check_out) {
                    $in = Carbon::parse($log->check_in);
                    $out = Carbon::parse($log->check_out);
                    $diffMinutes = $in->diffInMinutes($out);
                    
                    $totalMinutes += $diffMinutes;

                    if ($diffMinutes < 480) {
                        $analyticsSummary['short_hours_days']++;
                    }
                }
            }
            $analyticsSummary['total_hours_completed'] = round($totalMinutes / 60, 1);
        }

        return view('attendance.teacher', compact(
            'liveTeachers', 'teachers', 'historyLogs', 
            'startDate', 'endDate', 'selectedTeacherId', 
            'analyticsSummary', 'activeTab'
        ));
    }

    /**
     * REGISTRATION PHASE: Store the teacher's biometric public key mapping.
     */
    public function registerBiometric(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'credential_id' => 'required|string',
            'public_key' => 'required|string'
        ]);

        // Securely update or map a singular thumb fingerprint master key per professor
        TeacherBiometric::updateOrCreate(
            ['teacher_id' => $request->teacher_id],
            [
                'credential_id' => $request->credential_id,
                'public_key' => $request->public_key
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Biometric fingerprint signature saved to security matrix.'
        ]);
    }

    /**
     * VERIFICATION PHASE: Match hardware credential and process timestamps.
     */
    public function verifyBiometric(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'live_credential_id' => 'required|string'
        ]);

        $teacherId = $request->input('teacher_id');
        $liveCredential = $request->input('live_credential_id');

        // Fetch the professor's stored biometric record
        $storedBiometric = TeacherBiometric::where('teacher_id', $teacherId)->first();

        if (!$storedBiometric) {
            return response()->json([
                'status' => 'error',
                'message' => 'No biometric record found. Please register this teacher\'s thumb first.'
            ]);
        }

        // STRICT HARDWARE MATCHING CHECK: Rejects verification if fingerprints do not match
        if ($storedBiometric->credential_id !== $liveCredential) {
            return response()->json([
                'status' => 'error',
                'message' => 'Biometric fingerprint mismatch! Access denied.'
            ]);
        }

        // 🌟 TIMEZONE FIXED: Explicitly pull localized instance tracking keys
        $now = Carbon::now('Asia/Karachi');
        $currentDate = $now->toDateString();
        $currentDayName = $now->format('l');
        $currentTime = $now->toTimeString();

        $existingLog = TeacherAttendanceLog::where('teacher_id', $teacherId)
            ->where('log_date', $currentDate)
            ->first();

        if (!$existingLog) {
            // Action: Check-In
            TeacherAttendanceLog::create([
                'teacher_id' => $teacherId,
                'log_date' => $currentDate,
                'log_day' => $currentDayName,
                'check_in' => $currentTime,
                'status' => 'Present'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Biometric match ok. Check-in logged successfully.',
                'time' => $now->format('h:i A'),
                'day' => $currentDayName
            ]);
        } 
        
        if (is_null($existingLog->check_out)) {
            // Action: Check-Out
            $existingLog->update([
                'check_out' => $currentTime
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Biometric match ok. Check-out logged successfully.',
                'time' => $now->format('h:i A'),
                'day' => $currentDayName
            ]);
        }

        return response()->json([
            'status' => 'warning',
            'message' => 'Attendance parameters have already been fully logged for today.'
        ]);
    }
}
