<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController; 
use App\Http\Controllers\CourseController;   
use App\Http\Controllers\PaymentController;  
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\GradeController;       
use App\Http\Controllers\TimetableController;   
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\PayrollController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


/*
|--------------------------------------------------------------------------
| Live Serverless Environment Verification Gates (Bypasses Middleware)
|--------------------------------------------------------------------------
*/

// Diagnostic route to test if Vercel environmental configurations match your cloud database
Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        return "🎉 Serverless Database Connection verified and working perfectly on Vercel!";
    } catch (\Exception $e) {
        return response()->json([
            'status' => '❌ Live Cloud Database Connection Failed',
            'error' => $e->getMessage(),
            'driver' => config('database.default'),
            'host' => config('database.connections.' . config('database.default') . '.host'),
        ], 500);
    }
});

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
    // Unified Print Engine Engine Route Links
    Route::get('/payments/{id}/print', [PaymentController::class, 'print'])->name('payments.print');
    Route::get('/payment/print/{id}', [PaymentController::class, 'print']); // Fallback support map for legacy references

    // Async Data Filling API Connectors
    Route::get('/api/students/{id}/enrollment-details', [PaymentController::class, 'getEnrollmentDetails']);
    Route::get('/payments/enrollment-details/{id}', [PaymentController::class, 'getEnrollmentDetails']);

    // Dynamic Fee Installments & Overdue Defaulter Roster Connection Map
    Route::get('/finance/defaulters', [FinanceController::class, 'defaulterList'])->name('finance.defaulters');

    // Student Manual Roll-Call Attendance Checksheets
    Route::get('/attendance/student', [StudentAttendanceController::class, 'index'])->name('attendance.student.index');
    Route::post('/attendance/student', [StudentAttendanceController::class, 'store'])->name('attendance.student.store');

    // Biometric Teacher Attendance
    Route::get('/attendance/teacher', [TeacherAttendanceController::class, 'index'])->name('attendance.teacher.index');
    Route::post('/attendance/teacher/register', [TeacherAttendanceController::class, 'registerBiometric'])->name('attendance.teacher.register');
    Route::post('/attendance/teacher/verify', [TeacherAttendanceController::class, 'verifyBiometric'])->name('attendance.teacher.verify');

    // System Maintenance Utility Channel Dashboard Hook
    Route::post('/admin/optimize-system', [HomeController::class, 'optimizeSystem'])->name('admin.optimize');

    // Support Staff Roster Module Routes
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');

    // Master Payroll Management Module Routes
    Route::get('/payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('/payrolls/generate', [PayrollController::class, 'create'])->name('payrolls.create');
    Route::post('/payrolls/generate', [PayrollController::class, 'store'])->name('payrolls.store');
    Route::post('/payrolls/{id}/pay', [PayrollController::class, 'markAsPaid'])->name('payrolls.pay');
});

/*
|--------------------------------------------------------------------------
| System Maintenance Debug Utility Triggers & Schema Repair Mechanics
|--------------------------------------------------------------------------
*/

// EMERGENCY REPAIR PIPELINE LINK: Rebuilds grades table framework layout cleanly
Route::get('/fix-grades-database-now', function() {
    try {
        Schema::dropIfExists('grades');

        Schema::create('grades', function ($table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id');
            $table->string('exam_type')->default('Daily Test');
            $table->date('evaluation_date')->nullable();
            $table->decimal('marks_obtained', 5, 2)->default(0.00);
            $table->decimal('total_marks', 5, 2)->default(50.00);
            $table->string('grade_letter', 2)->default('F');
            $table->string('status')->default('Pass');
            $table->timestamps();
        });

        return "⚡ Success: Your grades database table has been completely rebuilt with correct serverless parameters! Visit your grades page now.";
    } catch (\Exception $e) {
        return "❌ Table Repair Exception Error: " . $e->getMessage();
    }
});

// FIXED SCHEMA FIXER: Rewritten using agnostic fluent blueprints to prevent PostgreSQL compilation exceptions
Route::get('/force-photo-fix', function() {
    try {
        if (!Schema::hasColumn('students', 'photo')) {
            Schema::table('students', function($table) { $table->string('photo')->nullable()->after('mobile'); });
        }
        if (!Schema::hasColumn('teachers', 'photo')) {
            Schema::table('teachers', function($table) { $table->string('photo')->nullable()->after('designation'); });
        }
        
        Schema::table('teachers', function($table) {
            $table->string('phone')->nullable()->change();
            $table->string('designation')->nullable()->change();
        });
        
        if (!Schema::hasColumn('courses', 'fee')) {
            Schema::table('courses', function($table) { $table->decimal('fee', 10, 2)->default(0.00)->after('duration'); });
        }
        if (!Schema::hasColumn('payments', 'paid_date')) {
            Schema::table('payments', function($table) { $table->date('paid_date')->nullable(); });
        }
        if (!Schema::hasColumn('payments', 'total_fee')) {
            Schema::table('payments', function($table) { $table->decimal('total_fee', 10, 2)->default(0.00)->nullable(); });
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
        
        return "⚡ Success: Your student, teacher, courses, and payments schemas have been verified and repaired smoothly!";
    } catch (\Exception $e) {
        return "❌ Notice Schema Exception: " . $e->getMessage();
    }
});

// DYNAMIC TRACKER DIAGNOSTIC ROUTE: Modified safely to dynamically inspect columns on any database vendor mapping
Route::get('/check-payment-columns', function() {
    try {
        $driver = config('database.default');
        $output = "<h3>Your payments table column names are [Driver: {$driver}]:</h3><ul>";
        
        if ($driver === 'pgsql') {
            $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name='payments'");
            foreach ($columns as $column) { $output .= "<li><strong>" . $column->column_name . "</strong></li>"; }
        } else {
            $columns = DB::select("SHOW COLUMNS FROM payments");
            foreach ($columns as $column) { $output .= "<li><strong>" . $column->Field . "</strong> (Type: " . $column->Type . ")</li>"; }
        }
        
        $output .= "</ul>";
        return $output;
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

// ABSOLUTE TIMETABLE SCHEMA REBUILDER PIPELINE
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
| Emergency Serverless Migration Portal (Remove after running)
|--------------------------------------------------------------------------
*/
Route::get('/run-migrations-now', function() {
    try {
        // Triggers the framework migration runner inside the Vercel instance memory
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        $output = Artisan::output();
        return "<h3>🎉 Migrations and Seeders completed successfully!</h3><pre>" . $output . "</pre>";
    } catch (\Exception $e) {
        return "<h3>❌ Migration Failed:</h3><pre>" . $e->getMessage() . "</pre>";
    }
});