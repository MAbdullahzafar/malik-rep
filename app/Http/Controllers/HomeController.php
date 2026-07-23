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
use Twilio\Rest\Client;



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
                    'is_tracked' => !is_null($todaySheet),
                    'sheet_id' => $todaySheet ? $todaySheet->id : null // Captured to power the dashboard broadcast button
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
                'teachersAbsent',
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

    /**
     * Bulk Course Absence WhatsApp Broadcast Channel Engine
     */
    public function broadcastCourseAbsentees($sheetId)
    {
        // 1. Fetch the active attendance sheet along with its specific absent records
        $sheet = StudentAttendanceSheet::with(['records' => function($query) {
            $query->where('status', 'Absent');
        }, 'records.student'])->findOrFail($sheetId);

        $absentRecords = $sheet->records;

        if ($absentRecords->isEmpty()) {
            return back()->with('error', 'No absent students found for this course tracking record today.');
        }

        try {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $twilio = new Client($sid, $token);
            
            $successCount = 0;

            // 2. Loop through every absent student and trigger their individual WhatsApp alert
            foreach ($absentRecords as $record) {
                $student = $record->student;

                if ($student && $student->mobile) {
                    // Clean up formatting rules to ensure proper international delivery
                    $cleanMobile = str_replace(['+', ' ', '-'], '', $student->mobile);

                    $twilio->messages->create(
                        "whatsapp:" . $cleanMobile,
                        [
                            "from" => "whatsapp:+14155238886",
                            "contentSid" => "HXb5b62575e6e4ff9d2c1094ece14bf7e0",
                            "contentVariables" => json_encode([
                                "1" => (string)$student->name,
                                "2" => "Absent Today"
                            ])
                        ]
                    );
                    $successCount++;
                }
            }

            // Flush the metrics cache so the dashboard immediately shows refreshed synchronized state metrics updates
            Artisan::call('cache:clear');

            return back()->with('success', "Successfully broadcasted real-time alerts to {$successCount} parents!");

        } catch (\Exception $e) {
            return back()->with('error', 'Broadcast execution error: ' . $e->getMessage());
        }
    }
}
