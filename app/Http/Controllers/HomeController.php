<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\StudentAttendanceSheet;
use App\Models\StudentAttendanceRecord;
use App\Models\TeacherAttendanceLog; // 👈 Integrated your biometric model mapping
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct()
    {
        // 🛡️ SECURITY SHIELD LOCKDOWN ACTIVE
        $this->middleware('auth');
    }

    /**
     * Display the main institutional landing center panel.
     */
    public function index(Request $request)
    {
        // 🛡️ BACKEND WATCHDOG GATEWAY HANDSHAKE CHECK
        // If the sessionStorage verification token is missing on page load, log out instantly!
        if ($request->has('verify_tab_state') && !$request->session()->has('tab_session_active')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login');
        }

        // Fetch all counts directly from your models now
        $studentCount = Student::count();
        $teacherCount = Teacher::count();
        $courseCount = Course::count();
        $enrollmentCount = Enrollment::count();

        // INTEGRATED: Real-time date lookup index
        $today = Carbon::today('Asia/Karachi')->toDateString(); // 🌟 Matched your localized timezone parameters

        // Calculate total students marked present across the institution today
        $todayPresentCount = StudentAttendanceRecord::whereHas('sheet', function($query) use ($today) {
            $query->where('attendance_date', $today);
        })->where('status', 'Present')->count();

        // Calculate total students marked absent across the institution today
        $todayAbsentCount = StudentAttendanceRecord::whereHas('sheet', function($query) use ($today) {
            $query->where('attendance_date', $today);
        })->where('status', 'Absent')->count();// Compile course-by-course real-time summary collection matrix
        $coursesAttendanceSummary = Course::with(['sheets' => function($query) use ($today) {
            $query->where('attendance_date', $today)->with('records');
        }])->get()->map(function($course) {
            $todaySheet = $course->sheets->first();
            
            return [
                'name' => $course->name,
                'present' => $todaySheet ? $todaySheet->records->where('status', 'Present')->count() : 0,
                'absent' => $todaySheet ? $todaySheet->records->where('status', 'Absent')->count() : 0,
                'is_tracked' => !is_null($todaySheet)
            ];
        });

        // =======================================================
        // ✨ DYNAMIC RESOLUTION: LIVE FACULTY CALCULATIONS CONNECTED
        // =======================================================
        
        // 1. Total Faculty Assigned
        $totalTeachers = $teacherCount; 

        // 2. Count actual checked-in rows for today from your TeacherAttendanceLog table
        $teachersCheckedIn = TeacherAttendanceLog::where('log_date', $today)
            ->whereNotNull('check_in')
            ->count();

        // 3. Compute true absentees dynamically
        $teachersAbsent = max(0, $totalTeachers - $teachersCheckedIn);

        // Pass everything safely to your updated home.blade.php
        return view('home', compact(
            'studentCount', 
            'teacherCount', 
            'courseCount', 
            'enrollmentCount',
            'todayPresentCount',
            'todayAbsentCount',
            'coursesAttendanceSummary',
            'totalTeachers',       
            'teachersCheckedIn',   
            'teachersAbsent'       
        ));
    }

    /**
     * ⚡ SYSTEM MAINTENANCE UTILITY:
     * Programmatically flushes structural cache tables to maximize local server page rendering speeds.
     */
    public function optimizeSystem(): RedirectResponse
    {
        // Execute structural internal core application flushing via clean native console hooks
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return redirect()->back()->with('success', '⚡ Project Optimization Successful: System compiled cache footprints flushed cleanly!');
    }
} // 👈 Class completely and securely closed here within active file boundaries!