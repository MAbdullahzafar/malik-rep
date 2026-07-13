<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Teacher; 
use Illuminate\View\View;
// Cloudinary SDK classes added here
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

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
            
            // Initialize Cloudinary (Automatically reads CLOUDINARY_URL environment variable)
            Configuration::instance();
            $uploadApi = new UploadApi();
            
            // Upload the temporary file straight to Cloudinary
            $response = $uploadApi->upload($file->getRealPath(), [
                'folder' => 'teachers'
            ]);
            
            // Store the full secure URL in the database array
            $validated['photo'] = $response['secure_url'];
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
            // Note: Cloudinary asset replacement happens automatically by overwriting 
            // the URL in your database. Old assets can be managed directly on your Cloudinary dashboard.
            
            $file = $request->file('photo');
            
            // Initialize Cloudinary
            Configuration::instance();
            $uploadApi = new UploadApi();
            
            // Upload the new image file
            $response = $uploadApi->upload($file->getRealPath(), [
                'folder' => 'teachers'
            ]);
            
            // Update the array with the new web image URL
            $validated['photo'] = $response['secure_url'];
        }

        $teacher->update($validated);
        return redirect('teachers')->with('flash_message', 'Teacher Updated!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $teacher = Teacher::findOrFail($id);

        // Local filesystem checking logic removed as files now safely live on the cloud
        $teacher->delete();
        return redirect('teachers')->with('flash_message', 'Teacher Deleted!');
    }
}
