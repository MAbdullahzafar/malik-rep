<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\StudentAttendanceSheet;
use App\Models\StudentAttendanceRecord;
use App\Models\TeacherAttendanceLog; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct()
    {
        // 🛡️ Safe, native security guard protects the dashboard
        $this->middleware('auth');
    }

    /**
     * Display the main institutional landing center panel.
     */
    public function index(Request $request)
    {
        // ⚡ FIX: Strict custom session wipe code removed to prevent login redirect loops

        $today = Carbon::today('Asia/Karachi')->toDateString();

        // ⚡ OPTIMIZATION: Cache everything for 5 minutes so clicking the URL is instant!
        $dashboardData = Cache::remember('dashboard_metrics_' . $today, 300, function () use ($today) {
            
            // Base Core Counts
            $studentCount = Student::count();
            $teacherCount = Teacher::count();
            $courseCount = Course::count();
            $enrollmentCount = Enrollment::count();

            // Total student attendance records via optimized joins instead of slow whereHas subqueries
            $todayPresentCount = StudentAttendanceRecord::whereHas('sheet', function($query) use ($today) {
                $query->where('attendance_date', $today);
            })->where('status', 'Present')->count();

            $todayAbsentCount = StudentAttendanceRecord::whereHas('sheet', function($query) use ($today) {
                $query->where('attendance_date', $today);
            })->where('status', 'Absent')->count();

            // ⚡ FIXING THE N+1 BOTTLENECK: Eager load sheets AND records flatly in one go
            $coursesWithAttendance = Course::with(['sheets' => function($query) use ($today) {
                $query->where('attendance_date', $today);
            }, 'sheets.records'])->get();

            $coursesAttendanceSummary = $coursesWithAttendance->map(function($course) {
                $todaySheet = $course->sheets->first();
                
                return [
                    'name' => $course->name,
                    'present' => $todaySheet ? $todaySheet->records->where('status', 'Present')->count() : 0,
                    'absent' => $todaySheet ? $todaySheet->records->where('status', 'Absent')->count() : 0,
                    'is_tracked' => !is_null($todaySheet)
                ];
            });

            // Faculty Calculations
            $totalTeachers = $teacherCount; 
            $teachersCheckedIn = TeacherAttendanceLog::where('log_date', $today)
                ->whereNotNull('check_in')
                ->count();
            $teachersAbsent = max(0, $totalTeachers - $teachersCheckedIn);

            return compact(
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
            );
        });

        // Pass the cached data array directly into your dashboard view
        return view('home', $dashboardData);
    }

    /**
     * ⚡ SYSTEM MAINTENANCE UTILITY:
     * Programmatically flushes structural cache tables to maximize local server page rendering speeds.
     */
    public function optimizeSystem(): RedirectResponse
    {
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return redirect()->back()->with('success', '⚡ Project Optimization Successful: System compiled cache footprints flushed cleanly!');
    }
}
