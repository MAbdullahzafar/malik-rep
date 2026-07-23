<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\StudentAttendanceSheet;
use App\Models\StudentAttendanceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Twilio\Rest\Client;


class StudentAttendanceController extends Controller


{
    /**
     * Display the attendance tracking matrix sheet filtered by selected course context.
     */
    public function index(Request $request)
    {
        $courses = Course::all();
        
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $selectedCourseId = $request->input('course_id');
        
        $students = collect();
        $existingSheet = null;

        if ($selectedCourseId) {
            // Check if attendance has already been logged for this date and course context
            $existingSheet = StudentAttendanceSheet::with('records.student')
                ->where('course_id', $selectedCourseId)
                ->where('attendance_date', $selectedDate)
                ->first();

            if ($existingSheet) {
                // Load existing records matrix directly for editing profiles
                $students = $existingSheet->records;
            } else {
                /*
                 * BULLETPROOF DIRECT FIELD LOOKUP:
                 * 1. Search the enrollments table for your exact 'course_id' selection.
                 * 2. Pluck only the matching 'student_id' column array values.
                 * 3. Fetch only those specific students from the database.
                 */
                $enrolledStudentIds = Enrollment::where('course_id', $selectedCourseId)
                    ->pluck('student_id')
                    ->toArray();

                $students = Student::whereIn('id', $enrolledStudentIds)
                    ->orderBy('name', 'asc')
                    ->get();
            }
        }

        return view('attendance.student', compact('courses', 'students', 'selectedDate', 'selectedCourseId', 'existingSheet'));
    }

    /**
     * Store or mass-update the bulk collection dataset into the database safely.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:Present,Absent,Late,Excused',
            'attendance.*.remarks' => 'nullable|string|max:255'
        ]);

        $courseId = $request->input('course_id');
        $date = $request->input('attendance_date');
        $attendanceData = $request->input('attendance');

        // Locate an existing sheet registry session or instantiate a new log record
        $sheet = StudentAttendanceSheet::firstOrCreate(
            ['course_id' => $courseId, 'attendance_date' => $date],
            ['teacher_id' => null]
        );

        // Process bulk row values safely inside an optimized model wrapper map
        foreach ($attendanceData as $studentId => $data) {
            StudentAttendanceRecord::updateOrCreate(
                [
                    'attendance_sheet_id' => $sheet->id,
                    'student_id' => $studentId
                ],
                [
                    'status' => $data['status'],
                    'remarks' => $data['remarks'] ?? null
                ]
            );
        }

        return redirect()->route('attendance.student.index', [
            'course_id' => $courseId,
            'date' => $date
        ])->with('success', 'Student attendance registry records updated inside system index.');
    }

    /**
     * Automated WhatsApp Attendance Alert Engine
     */
    public function markAbsent(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
        
        if (!$student->mobile) {
            return back()->with('error', 'Parent phone number is missing.');
        }

        try {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $twilio = new Client($sid, $token);

            // Clean special characters out of the phone string to ensure strict global routing compatibility
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

            return back()->with('success', 'WhatsApp attendance notification successfully delivered!');

        } catch (\Exception $e) {
            return back()->with('error', 'Message dispatch failed: ' . $e->getMessage());
        }
    }
}
