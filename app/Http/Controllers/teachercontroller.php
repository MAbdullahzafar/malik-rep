<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Teacher; 
use Illuminate\View\View;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers with lightweight pagination.
     */
    public function index(Request $request) : View
    {
        $search = $request->input('search');

        $teachers = Teacher::when($search, function($query) use ($search) {
            return $query->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%")
                         ->orWhere('designation', 'LIKE', "%{$search}%");
        })
        ->latest('id')
        ->paginate(15)
        ->withQueryString();

        return view('teachers')->with('teachers', $teachers);
    }

    public function create() : View
    {
        return view('teachers');
    }

    /**
     * Store a newly created resource in storage with safe optional parameters.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/teachers'), $filename);
            $validated['photo'] = 'uploads/teachers/' . $filename;
        }

        Teacher::create($validated);
        return redirect('teachers')->with('flash_message', 'Teacher Added!');
    }

    public function show(string $id): View
    {
        $teacher = Teacher::findOrFail($id);
        return view('teachers')->with('teacher', $teacher);
    }

    public function edit(string $id): View
    {
        $teacher = Teacher::findOrFail($id);
        return view('teachers')->with('teacher', $teacher);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $teacher = Teacher::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email,' . $id,
            'phone' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            if ($teacher->photo && file_exists(public_path($teacher->photo))) {
                @unlink(public_path($teacher->photo));
            }

            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/teachers'), $filename);
            $validated['photo'] = 'uploads/teachers/' . $filename;
        }

        $teacher->update($validated);
        return redirect('teachers')->with('flash_message', 'Teacher Updated!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $teacher = Teacher::findOrFail($id);

        if ($teacher->photo && file_exists(public_path($teacher->photo))) {
            @unlink(public_path($teacher->photo));
        }

        $teacher->delete();
        return redirect('teachers')->with('flash_message', 'Teacher Deleted!');
    }
}
