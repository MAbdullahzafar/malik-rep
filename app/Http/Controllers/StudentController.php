<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Student;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource with rapid pagination and search filters.
     */
    public function index(Request $request) : View
    {
        $search = $request->input('search');

        // OPTIMIZED: Pulls only 15 rows, eager loads course, and adds direct search capabilities for registration columns
        $students = Student::with('course')
        ->when($search, function($query) use ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('reg_no', 'LIKE', "%{$search}%")
                  ->orWhere('contact', 'LIKE', "%{$search}%")
                  ->orWhere('mobile', 'LIKE', "%{$search}%");
            });
        })
        ->latest('id') 
        ->paginate(15)
        ->withQueryString(); 

        // 🛡️ FIXED BINDING MATRIX: Appends flat course_name attributes right inside data collections objects to match index.blade layout lookups
        $students->getCollection()->transform(function ($item) {
            $item->course_name = $item->course ? $item->course->name : null;
            return $item;
        });

        // FETCHED: Collect all courses to populate form select elements smoothly
        $courses = Course::orderBy('name', 'asc')->get();

        return view('students.index')->with([
            'students' => $students,
            'courses' => $courses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() : View
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */  
    public function store(Request $request): RedirectResponse
    {
        // Validate incoming registration properties safely including your course identifier tracking
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'course_id' => 'required|exists:courses,id',
            'requested_installments' => 'required|integer|min:1|max:12', // INJECTED VALIDATION FOR THE PICKER
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // 2MB Upload Limit
        ]);

        $input = $request->all();
        
        if (isset($input['contact'])) {
            $input['mobile'] = $input['contact'];
        }

        // INTEGRATED: Process and save new profile photo uploads securely
        // if ($request->hasFile('photo')) {
        //     $file = $request->file('photo');
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     $file->move(public_path('storage/student_photos'), $filename);
        //     $input['photo'] = 'storage/student_photos/' . $filename;
        // }

if ($request->hasFile('photo')) {

    $file = $request->file('photo');

    $filename = time() . '_' . $file->getClientOriginalName();

    Storage::disk('s3')->putFileAs(
        '',
        $file,
        $filename,
        'public'
    );

    $input['photo'] = Storage::disk('s3')->url($filename);
}
$student->update($input);





        // The reg_no is generated cleanly via the Model hook built inside the Student model schema
        $student = Student::create($input);

        // AUTOMATED ENROLLMENT GENERATOR: Computes a safe string identifier
        $enrollmentNumber = 'ENR-' . date('Y') . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT);

        // FETCH ACTIVE COURSE FEE: Resolves the missing fee column constraint dynamically
        $coursePrice = Course::where('id', $request->input('course_id'))->value('fee') ?? 0.00;

        // EXTRA RELATION LOG: Generates the explicit matching enrollment row tracking record safely
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id'  => $request->input('course_id'),
            'enroll_no'  => $enrollmentNumber,
            'join_date'  => now()->toDateString(),
            'fee'        => $coursePrice
        ]);

        // Instantiate your FinanceController to distribute customized milestone splits
        $totalSplitsRequested = intval($request->input('requested_installments', 3));
        
        $financeEngine = new \App\Http\Controllers\FinanceController();
        $financeEngine->generateInstallments($enrollment->id, $totalSplitsRequested);

    //     return redirect('students')->with('flash_message', 'Student Added and Custom Fee Installment Plan Generated!');
    // }/**

    return redirect('students')->with('flash_message', 'Student Added...');
}

 /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $student = Student::findOrFail($id);
        return view('students.show')->with('students', $student);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $student = Student::findOrFail($id);
        return view('students.edit')->with('students', $student);
    }

    /**
     * Update the specified resource in data storage banks safely.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        // Validate updates safely including your course tracking checks
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'course_id' => 'required|exists:courses,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $student = Student::findOrFail($id);
        $input = $request->all();
        
        if (isset($input['contact'])) {
            $input['mobile'] = $input['contact'];
        }

        // INTEGRATED: Process and replace old file pathways with new image entries securely
        // if ($request->hasFile('photo')) {
        //     // Delete old file if it exists to clean up server memory storage space
        //     if ($student->photo && file_exists(public_path($student->photo))) {
        //         @unlink(public_path($student->photo));
        //     }

        //     $file = $request->file('photo');
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     $file->move(public_path('storage/student_photos'), $filename);
        //     $input['photo'] = 'storage/student_photos/' . $filename;
        // }

        // $student->update($input);





        if ($request->hasFile('photo')) {

    $file = $request->file('photo');

    $filename = time() . '_' . $file->getClientOriginalName();

    Storage::disk('s3')->putFileAs(
        '',
        $file,
        $filename,
        'public'
    );

    $input['photo'] = Storage::disk('s3')->url($filename);
}

        // UPDATED ENROLLMENT LOGIC: Builds tracking key if missing during update routines
        $enrollmentNumber = 'ENR-' . date('Y') . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT);

        // FETCH ACTIVE COURSE FEE FOR UPDATE
        $coursePrice = Course::where('id', $request->input('course_id'))->value('fee') ?? 0.00;

        // EXTRA RELATION LOG: Keep enrollment mapping log unified with chosen input
        Enrollment::updateOrCreate(
            ['student_id' => $student->id],
            [
                'course_id' => $request->input('course_id'),
                'enroll_no' => $enrollmentNumber,
                'fee'        => $coursePrice
            ]
        );

        return redirect('students')->with('flash_message', 'student Updated!');
    }

    /**
     * Remove the specified resource from storage registers cleanly.
     */
    public function destroy(string $id): RedirectResponse
    {
        $student = Student::findOrFail($id);

        // INTEGRATED: Cleans up file assets upon hard deletion commands
        // if ($student->photo && file_exists(public_path($student->photo))) {
        //     @unlink(public_path($student->photo));
        // }
        

        $student->delete();
        return redirect('students')->with('flash_message', 'student deleted!');
    }

    /**
     * Generates a comprehensive printable fee installment schedule guide overview for students.
     */
    public function printSchedule(string $id): View
    {
        // 🛡️ TYPE SAFE GUARD: Force conversion of the string identifier into a solid integer number
        $cleanStudentId = intval($id);

        $student = DB::table('students')->where('id', $cleanStudentId)->first();
        
        if (!$student) {
            abort(404, 'Student target registry footprint absent.');
        }

        $enrollment = DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->where('enrollments.student_id', $cleanStudentId)
            ->select('enrollments.*', 'courses.name as course_name', 'courses.duration')
            ->first();

        $installments = DB::table('payment_installments')
            ->where('student_id', $cleanStudentId)
            ->orderBy('installment_number', 'asc')
            ->get();

        // Points directly to your payment-print.blade.php layout file name safely
        return view('students.payment-print', compact('student', 'enrollment', 'installments'));
    }
}