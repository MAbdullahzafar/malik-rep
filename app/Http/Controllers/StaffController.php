<?php

namespace App\Http\Controllers;

use App\Models\StaffMember;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = StaffMember::orderBy('name', 'asc')->get();
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:Sweeper,Guard,Peon',
            'contact' => 'nullable|string|max:20',
            'base_salary' => 'required|numeric|min:0',
        ]);

        StaffMember::create($validated);

        return redirect()->route('staff.index')->with('success', 'Support staff member registered successfully!');
    }
}
