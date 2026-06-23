<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $selectedCourseId = $request->get('course_id');
        
        // Fetch baseline drop-down dependencies synced with your project
        $courses = Course::all();
        
        // Loads student course tracks natively to auto-fill the frontend form
        $students = Student::with('course')->get();

        // Query performance reports filtered by active course selections
        $query = Grade::with(['student', 'course']);
        if ($selectedCourseId) {
            $query->where('course_id', $selectedCourseId);
        }
        $grades = $query->latest()->paginate(15);
        // 📊 ADVANCED STATISTICAL CALCULATOR MATRIX
        $totalGraded = Grade::when($selectedCourseId, function($q) use($selectedCourseId) {
            return $q->where('course_id', $selectedCourseId);
        })->count();

        $totalPassed = Grade::when($selectedCourseId, function($q) use($selectedCourseId) {
            return $q->where('course_id', $selectedCourseId);
        })->where('status', '=', 'Pass')->count();

        $totalFailed = Grade::when($selectedCourseId, function($q) use($selectedCourseId) {
            return $q->where('course_id', $selectedCourseId);
        })->where('status', '=', 'Fail')->count();

        $classPassingPercentage = $totalGraded > 0 ? round(($totalPassed / $totalGraded) * 100, 1) : 0;

        return view('grades.index', compact(
            'grades', 'courses', 'students', 'selectedCourseId',
            'totalPassed', 'totalFailed', 'classPassingPercentage'
        ));
    }
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'exam_type' => 'required|in:Daily Test,Midterm,Final Term',
            'evaluation_date' => 'required|date',
            'marks_obtained' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:1',
        ]);

        if ($request->marks_obtained > $request->total_marks) {
            return redirect()->back()->withErrors(['marks_obtained' => 'Obtained marks cannot exceed total marks boundaries.']);
        }

        $percentage = ($request->marks_obtained / $request->total_marks) * 100;
        
        // Calculate Pass/Fail bounds based on professional 50% benchmarks
        $status = $percentage >= 50 ? 'Pass' : 'Fail';
        
        $gradeLetter = 'F';
        if ($percentage >= 85) $gradeLetter = 'A';
        elseif ($percentage >= 70) $gradeLetter = 'B';
        elseif ($percentage >= 50) $gradeLetter = 'C';

        // 🌟 FIXED DATA INJECTION: Cleaned assignment typo so student entries map flawlessly
        Grade::create([
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'exam_type' => $request->exam_type,
            'evaluation_date' => $request->evaluation_date,
            'marks_obtained' => $request->marks_obtained,
            'total_marks' => $request->total_marks,
            'grade_letter' => $gradeLetter,
            'status' => $status,
        ]);

        return redirect()->route('grades.index')->with('success', 'Performance mark logged into system ledger.');
    }

    public function destroy($id)
    {
        Grade::findOrFail($id)->delete();
        return redirect()->route('grades.index')->with('success', 'Record purged from log successfully.');
    }
}
