<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    /**
     * Display a comprehensive grid calendar matrix of all class schedules.
     */
    public function index(Request $request)
    {
        $selectedCourseId = $request->get('course_id');
        
        // 🌟 FIXED STABLE COUPLING: Fetches variables as standard collection elements matching view structures
        $courses = DB::table('courses')->orderBy('name', 'asc')->get();
        $teachers = DB::table('teachers')->orderBy('name', 'asc')->get();
        $rawSchedules = DB::table('timetables')->orderBy('start_time', 'asc')->get();
        
        $schedules = collect();
        foreach ($rawSchedules as $raw) {
            $schedules->push((object)[
                'id'          => $raw->id,
                'day_of_week' => $raw->day_of_week,
                'start_time'  => $raw->start_time,
                'end_time'    => $raw->end_time,
                'room_number' => $raw->room_number,
                'course'      => DB::table('courses')->where('id', $raw->course_id)->first(),
                'teacher'     => DB::table('teachers')->where('id', $raw->teacher_id)->first()
            ]);
        }

        if (!empty($selectedCourseId)) {
            $schedules = $schedules->filter(function($slot) use ($selectedCourseId) {
                return isset($slot->course->id) && (string)$slot->course->id === (string)$selectedCourseId;
            });
        }

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('timetables.index', compact('schedules', 'courses', 'teachers', 'selectedCourseId', 'daysOfWeek'));
    }

    /**
     * Store a newly generated timetable schedule entry into records registers safely.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required',
            'teacher_id' => 'required',
            'day_of_week' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_number' => 'required|string|max:50'
        ]);

        $startTime  = $request->input('start_time');
        $endTime    = $request->input('end_time');
        $dayOfWeek  = $request->input('day_of_week');
        $roomNumber = trim($request->input('room_number'));
        $teacherId  = $request->input('teacher_id');
        $courseId   = $request->input('course_id');

        if ($startTime >= $endTime) {
            return redirect()->back()->withErrors(['start_time' => 'Start time cannot be later than or equal to end time limits.']);
        }

        // Overlap Protection Shield Verification Check
        $hasConflict = DB::table('timetables')->where('day_of_week', $dayOfWeek)
            ->where(function($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })
            ->where(function($query) use ($roomNumber, $teacherId, $courseId) {
                $query->where('room_number', '=', $roomNumber)
                      ->orWhere('teacher_id', '=', $teacherId)
                      ->orWhere('course_id', '=', $courseId);
            })
            ->exists();

        if ($hasConflict) {
            return redirect()->back()->withErrors(['conflict' => 'Schedule collision error detected! Slot already allocated.']);
        }

        DB::table('timetables')->insert([
            'course_id'   => $courseId,
            'teacher_id'  => $teacherId,
            'day_of_week' => $dayOfWeek,
            'start_time'  => $startTime,
            'end_time'    => $endTime,
            'room_number' => $roomNumber,
            'created_at'  => now(),
            'updated_at'  => now()
        ]);

        return redirect()->route('timetables.index')->with('success', 'Class Schedule slot logged successfully!');
    }

    /**
     * Remove the specified timetable resource entry from registers.
     */
    public function destroy($id)
    {
        DB::table('timetables')->where('id', $id)->delete();
        return redirect()->route('timetables.index')->with('success', 'Class timetable slot dropped.');
    }
}
