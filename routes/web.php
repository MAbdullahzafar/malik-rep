<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController; 
use App\Http\Controllers\CourseController;   
use App\Http\Controllers\PaymentController;  
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\GradeController;       
use App\Http\Controllers\TimetableController;   
use App\Http\Controllers\FinanceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Public Authentication Gates (High-Security Mode)
|--------------------------------------------------------------------------
*/

// Enforces login endpoints while allowing core registrations to run natively
Auth::routes(['register' => true]);

// Automatically redirect any root hits straight to the secure login wall
Route::get('/', function () {
    return redirect()->route('login');
});
/*
|--------------------------------------------------------------------------
| Secure Shielded Portal Workspace (Requires Secure Session Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Fast Dashboard Core Workspace Home Redirections
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Multi-Module Resource Controllers Matrix Layouts
    Route::resource('students', StudentController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('courses', CourseController::class);
    Route::resource('grades', GradeController::class);          
    Route::resource('timetables', TimetableController::class);  

    // Singular Alternative Route Mappings for Course Crud Framework Actions
    Route::post('/course', [CourseController::class, 'store']);
    Route::put('/course/{id}', [CourseController::class, 'update']);
    Route::patch('/course/{id}', [CourseController::class, 'update']);
    Route::delete('/course/{id}', [CourseController::class, 'destroy']);

    // Individualized Profiling Analytics Ledgers & Printing Engines
    Route::get('/students/{id}/profile', [StudentProfileController::class, 'show']);
    Route::get('/students/{id}/schedule-pdf', [StudentController::class, 'printSchedule'])->name('students.schedule');
    
    // 🌟 UNIFIED PRINT ENGINE ROUTE: Connects your isolated 3-copy vouchers flawlessly!
    Route::get('/payments/{id}/print', [PaymentController::class, 'print'])->name('payments.print');
    Route::get('/payment/print/{id}', [PaymentController::class, 'print']); // Fallback support map for legacy references

    // Async Data Filling API Connectors
    Route::get('/api/students/{id}/enrollment-details', [PaymentController::class, 'getEnrollmentDetails']);
    Route::get('/payments/enrollment-details/{id}', [PaymentController::class, 'getEnrollmentDetails']);

    // ⚠️ NEW DYNAMIC FEE INSTALLMENTS & OVERDUE DEFAULTER ROSTER CONNECTION MAP
    Route::get('/finance/defaulters', [FinanceController::class, 'defaulterList'])->name('finance.defaulters');

    // Student Manual Roll-Call Attendance Checksheets
    Route::get('/attendance/student', [StudentAttendanceController::class, 'index'])->name('attendance.student.index');
    Route::post('/attendance/student', [StudentAttendanceController::class, 'store'])->name('attendance.student.store');

    // Biometric Teacher Attendance
    Route::get('/attendance/teacher', [TeacherAttendanceController::class, 'index'])->name('attendance.teacher.index');
    Route::post('/attendance/teacher/register', [TeacherAttendanceController::class, 'registerBiometric'])->name('attendance.teacher.register');
    Route::post('/attendance/teacher/verify', [TeacherAttendanceController::class, 'verifyBiometric'])->name('attendance.teacher.verify');
});
/*
|--------------------------------------------------------------------------
| System Maintenance Debug Utility Triggers (Public Diagnostics)
|--------------------------------------------------------------------------
*/

// EMERGENCY REPAIR PIPELINE LINK: Rebuilds grades table structure to eliminate the status error instantly
Route::get('/fix-grades-database-now', function() {
    try {
        try {
            DB::statement("DROP TABLE IF EXISTS grades");
        } catch (\Exception $e) {}

        DB::statement("CREATE TABLE grades (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            student_id BIGINT UNSIGNED NOT NULL,
            course_id BIGINT UNSIGNED NOT NULL,
            exam_type ENUM('Daily Test', 'Midterm', 'Final Term') NOT NULL DEFAULT 'Daily Test',
            evaluation_date DATE NULL,
            marks_obtained DECIMAL(5,2) NOT NULL DEFAULT 0.00,
            total_marks DECIMAL(5,2) NOT NULL DEFAULT 50.00,
            grade_letter VARCHAR(2) NOT NULL DEFAULT 'F',
            status VARCHAR(255) NOT NULL DEFAULT 'Pass',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        return "⚡ Success: Your grades database table has been completely rebuilt with the missing 'status' column! Visit your grades page now.";
    } catch (\Exception $e) {
        return "❌ Table Repair Exception Error: " . $e->getMessage();
    }
});

Route::get('/force-photo-fix', function() {
    try {
        try {
            DB::statement("ALTER TABLE students ADD COLUMN photo VARCHAR(255) NULL AFTER mobile");
        } catch (\Exception $e) {}

        try {
            DB::statement("ALTER TABLE teachers ADD COLUMN photo VARCHAR(255) NULL AFTER designation");
        } catch (\Exception $e) {}

        DB::statement("ALTER TABLE teachers MODIFY COLUMN phone VARCHAR(255) NULL");
        DB::statement("ALTER TABLE teachers MODIFY COLUMN designation VARCHAR(255) NULL");
        
        $columnsCourses = DB::select("SHOW COLUMNS FROM courses LIKE 'fee'");
        if (empty($columnsCourses)) {
            DB::statement("ALTER TABLE courses ADD COLUMN fee DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER duration");
        } else {
            DB::statement("ALTER TABLE courses MODIFY COLUMN fee DECIMAL(10,2) NOT NULL DEFAULT 0.00");
        }

        $columnsPaidDate = DB::select("SHOW COLUMNS FROM payments LIKE 'paid_date'");
        if (empty($columnsPaidDate)) {
            DB::statement("ALTER TABLE payments ADD COLUMN paid_date DATE NULL");
        }

        $columnsTotalFee = DB::select("SHOW COLUMNS FROM payments LIKE 'total_fee'");
        if (empty($columnsTotalFee)) {
            DB::statement("ALTER TABLE payments ADD COLUMN total_fee DECIMAL(10,2) NULL DEFAULT 0.00");
        }

        $pastPayments = DB::table('payments')->get();
        foreach($pastPayments as $payment) {
            $enrollment = DB::table('enrollments')->where('student_id', $payment->student_id)->first();
            if (!empty($enrollment)) {
                $courseFee = DB::table('courses')->where('id', $enrollment->course_id)->value('fee') ?? 0.00;
                DB::table('payments')
                    ->where('id', $payment->id)
                    ->where(function($query) {
                        $query->whereNull('total_fee')->orWhere('total_fee', 0);
                    })
                    ->update(['total_fee' => $courseFee]);
            } else {
                DB::table('payments')->where('id', $payment->id)->update(['total_fee' => 0.00]);
            }
        }
        
        return "⚡ Success: Your student, teacher, courses, and payments database schemas have been fully verified and repaired!";
    } catch (\Exception $e) {
        return "❌ Notice Schema Exception: " . $e->getMessage();
    }
});

// 🔍 EMERGENCY PAYMENT TABLE COLUMN TRACKER DIAGNOSTIC ROUTE
Route::get('/check-payment-columns', function() {
    try {
        $columns = DB::select("SHOW COLUMNS FROM payments");
        $output = "<h3>Your payments table column names are:</h3><ul>";
        foreach ($columns as $column) {
            $output .= "<li><strong>" . $column->Field . "</strong> (Type: " . $column->Type . ")</li>";
        }
        $output .= "</ul>";
        return $output;
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

// 🌟 ABSOLUTE TIMETABLE SCHEMA REBUILDER PIPELINE
Route::get('/rebuild-timetable-table-now', function() {
    try {
        Schema::dropIfExists('timetables');

        Schema::create('timetables', function ($table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_number');
            $table->timestamps();
        });

        return "⚡ Success: Your timetables table has been completely rebuilt with correct columns! Refresh your scheduler page now.";
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

/*
|--------------------------------------------------------------------------
| Performance Booster & Maintenance Utility Bindings
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // ⚡ SYSTEM MAINTENANCE UTILITY CHANNEL: Maps the performance flush action trigger straight to your dashboard console widget
    Route::post('/admin/optimize-system', [HomeController::class, 'optimizeSystem'])->name('admin.optimize');
});


Route::middleware(['auth'])->group(function () {
    // 🧹 SUPPORT STAFF ROSTER MODULE ROUTES
    Route::get('/staff', [App\Http\Controllers\StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [App\Http\Controllers\StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [App\Http\Controllers\StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/{id}/edit', [App\Http\Controllers\StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{id}', [App\Http\Controllers\StaffController::class, 'update'])->name('staff.update');

    // 💼 MASTER PAYROLL MANAGEMENT MODULE ROUTES
    Route::get('/payrolls', [App\Http\Controllers\PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('/payrolls/generate', [App\Http\Controllers\PayrollController::class, 'create'])->name('payrolls.create');
    Route::post('/payrolls/generate', [App\Http\Controllers\PayrollController::class, 'store'])->name('payrolls.store');
    Route::post('/payrolls/{id}/pay', [App\Http\Controllers\PayrollController::class, 'markAsPaid'])->name('payrolls.pay');
});
