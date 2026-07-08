<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudentProfileController extends Controller
{
    /**
     * Display the specified student's complete ledger data matrix.
     */
    public function show(string $id) : View
    {
        // 1. Fetch the master student profile record safely using the row ID
        $student = DB::table('students')->where('id', $id)->first();
        if (!$student) {
            abort(404, 'Student profile record not found.');
        }

        // CHECK ENROLLMENT: Locate this student's true numerical row entry inside the enrollments table
        $activeEnrollment = DB::table('enrollments')->where('student_id', $id)->first();

        // Initialize empty timetable slots collections matrix payload
        $studentTimetableMatrix = collect();
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        if (!$activeEnrollment) {
            $enrollments = collect(); 
            $totalCourseFees = 0.00;
            $totalRemitted = 0.00;
            $outstandingBalance = 0.00;
            $totalPaidToDate = 0.00;
            $receipts = collect();
        } else {
            $enrollmentTablePrimaryKeyId = $activeEnrollment->id;
            $studentAssociatedCourseId = $activeEnrollment->course_id;

            // 🌟 DYNAMIC TIMETABLE LINK MATRIX ENGINE
            // Pull all institutional timetable sessions matching this student's specific enrolled course ID
            $rawStudentSchedules = DB::table('timetables')
                ->where('course_id', $studentAssociatedCourseId)
                ->orderBy('start_time', 'asc')
                ->get();

            foreach ($rawStudentSchedules as $raw) {
                $studentTimetableMatrix->push((object)[
                    'id'          => $raw->id,
                    'day_of_week' => $raw->day_of_week,
                    'start_time'  => $raw->start_time,
                    'end_time'    => $raw->end_time,
                    'room_number' => $raw->room_number,
                    'course'      => DB::table('courses')->where('id', $raw->course_id)->first(),
                    'teacher'     => DB::table('teachers')->where('id', $raw->teacher_id)->first()
                ]);
            }

            // 2. Locate the very first transaction matching this active enrollment map pointer entry
            $paymentRecord = DB::table('payments')
                ->where('enrollment_id', '=', $enrollmentTablePrimaryKeyId)
                ->whereNotNull('total_fee')
                ->where('total_fee', '>', 0)
                ->orderBy('id', 'asc')
                ->first();

            // 3. FORCE STRICT SINGLE ENROLLMENT MAP
            if ($paymentRecord) {
                $feeVal = floatval($paymentRecord->total_fee);
                $pDate = $paymentRecord->payment_date;
                $rNo = $paymentRecord->receipt_no;

                $enrollments = DB::table('courses')
                    ->where('id', $activeEnrollment->course_id)
                    ->select(
                        'courses.id as course_id',
                        'courses.name as course_name',
                        'courses.duration',
                        DB::raw($feeVal . " as total_course_fee"),
                        DB::raw("'" . $pDate . "' as registration_date"),
                        DB::raw("'" . $rNo . "' as receipt_no")
                    )
                    ->limit(1)
                    ->get();
            } else {
                $regDate = $activeEnrollment->join_date ?? $activeEnrollment->created_at ?? now()->toDateString();
                
                $enrollments = DB::table('courses')
                    ->where('id', $activeEnrollment->course_id)
                    ->select(
                        'courses.id as course_id',
                        'courses.name as course_name',
                        'courses.duration',
                        DB::raw('courses.fee as total_course_fee'),
                        DB::raw("'" . $regDate . "' as registration_date"),
                        // DB::raw('"—" as receipt_no')
                        DB::raw("'—' as receipt_no")

                    )
                    ->limit(1) 
                    ->get();
            }
            // 4. Calculate True Single-Course Ledger Analytics Totals
            $totalCourseFees = $enrollments->sum('total_course_fee');
            
            $totalRemitted = DB::table('payments')
                ->where('enrollment_id', '=', $enrollmentTablePrimaryKeyId)
                ->sum('amount');

            $outstandingBalance = $totalCourseFees - $totalRemitted;

            $totalPaidToDate = DB::table('payments')
                ->where('enrollment_id', '=', $enrollmentTablePrimaryKeyId)
                ->sum('amount');

            // 5. Fetch full chronological receipts ledger historical database records
            $receipts = DB::table('payments')
                ->where('enrollment_id', '=', $enrollmentTablePrimaryKeyId)
                ->orderBy('id', 'desc')
                ->get();
        }
        // 📊 INTEGRATED EVALUATIONS RETRIEVAL MATRIX
        $grades = DB::table('grades')->where('student_id', $id)->get();
        $dailyTests = $grades->where('exam_type', 'Daily Test');
        $midterms = $grades->where('exam_type', 'Midterm');
        $finals = $grades->where('exam_type', 'Final Term');

        $totalObtainedMarks = $grades->sum('marks_obtained');
        $totalPossibleMarks = $grades->sum('total_marks');
        $overallPercentage = $totalPossibleMarks > 0 ? round(($totalObtainedMarks / $totalPossibleMarks) * 100, 1) : 0;

        // 🌟 AUTOMATED DYNAMIC ATTENDANCE MULTI-TABLE SCANNER ENGINE
        $recordsTable = Schema::hasTable('student_attendance_records') ? 'student_attendance_records' : (Schema::hasTable('attendance_records') ? 'attendance_records' : null);
        $sheetsTable = Schema::hasTable('student_attendance_sheets') ? 'student_attendance_sheets' : (Schema::hasTable('attendance_sheets') ? 'attendance_sheets' : null);

        $attendanceHistoryList = collect();
        $monthlyAttendanceSummary = collect();

        if ($recordsTable && $sheetsTable) {
            $sheetForeignKey = Schema::hasColumn($recordsTable, 'attendance_sheet_id') ? 'attendance_sheet_id' : 'sheet_id';
            $dateColumnName = Schema::hasColumn($sheetsTable, 'attendance_date') ? 'attendance_date' : 'date';

            $attendanceHistoryList = DB::table($recordsTable)
                ->join($sheetsTable, $recordsTable . '.' . $sheetForeignKey, '=', $sheetsTable . '.id')
                ->where($recordsTable . '.student_id', '=', $id)
                ->select(
                    $recordsTable . '.status',
                    $sheetsTable . '.' . $dateColumnName . ' as date'
                )
                ->orderBy($sheetsTable . '.' . $dateColumnName, 'desc')
                ->get();

            $totalClasses = $attendanceHistoryList->count();
            $presentClasses = $attendanceHistoryList->whereIn('status', ['Present', 'Late', 'Excused'])->count();
            $attendancePercentage = $totalClasses > 0 ? round(($presentClasses / $totalClasses) * 100, 1) : 100;

            $groupedByMonth = $attendanceHistoryList->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->date)->format('Y-m'); 
            });

            foreach ($groupedByMonth as $monthKey => $monthRecords) {
                $mTotal = $monthRecords->count();
                $mPresent = $monthRecords->whereIn('status', ['Present', 'Late', 'Excused'])->count();
                $mPercentage = $mTotal > 0 ? round(($mPresent / $mTotal) * 100, 1) : 100;
                $displayMonthName = \Carbon\Carbon::parse($monthKey . '-01')->format('F Y'); 

                $monthlyAttendanceSummary->push([
                    'month_name' => $displayMonthName,
                    'total_sessions' => $mTotal,
                    'present_sessions' => $mPresent,
                    'percentage' => $mPercentage
                ]);
            }
        }

        // 🌟 LIVE FEES CONFIGURATION TIMELINE LEDGER: Pulls configured dynamic payment plans
        $cleanStudentId = intval($id);
        $installments = DB::table('payment_installments')
            ->where('student_id', $cleanStudentId)
            ->orderBy('installment_number', 'asc')
            ->get();

        // 🌟 COUPLING MATRIX PAYLOAD BINDING
        return view('students.profile', compact(
            'student', 'enrollments', 'totalCourseFees', 'totalRemitted', 'outstandingBalance', 
            'totalPaidToDate', 'receipts', 'dailyTests', 'midterms', 'finals', 
            'overallPercentage', 'attendancePercentage', 'totalClasses', 'presentClasses',
            'attendanceHistoryList', 'monthlyAttendanceSummary',
            'studentTimetableMatrix', 'daysOfWeek', 'installments'
        ));
    }
}
