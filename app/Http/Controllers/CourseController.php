<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Course; 
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of courses with lightweight pagination.
     */
    public function index(Request $request) : View
    {
        $search = $request->input('search');

        $courses = Course::when($search, function($query) use ($search) {
            return $query->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('syllabus', 'LIKE', "%{$search}%");
        })
        ->latest('id')
        ->paginate(15)
        ->withQueryString();

        return view('students.course')->with('courses', $courses);
    }

    public function create() : View
    {
        return view('students.course');
    }

    /**
     * Store a newly created resource in storage with explicit fee capture whitelisting.
     */
    public function store(Request $request): RedirectResponse
    {
        // WHITELIST ENGINE: Explicitly allow the numerical fee string to pass database authorization gates
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'syllabus' => 'required|string',
            'duration' => 'required|string|max:255',
            'fee' => 'required|numeric|min:0'
        ]);

        Course::create($validated);
        return redirect('courses')->with('flash_message', 'Course Added Successfully!');
    }

    public function show(string $id): View
    {
        $course = Course::findOrFail($id);
        return view('students.course')->with('course', $course);
    }

    public function edit(string $id): View
    {
        $course = Course::findOrFail($id);
        return view('students.course')->with('course', $course);
    }

    /**
     * Update the specified resource in storage with explicit fee modifier whitelisting.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $course = Course::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'syllabus' => 'required|string',
            'duration' => 'required|string|max:255',
            'fee' => 'required|numeric|min:0'
        ]);

        $course->update($validated);
        return redirect('courses')->with('flash_message', 'Course Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        Course::destroy($id);
        return redirect('courses')->with('flash_message', 'Course Deleted Successfully!');
    }
}
