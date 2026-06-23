<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = ['attendance_sheet_id', 'student_id', 'status', 'remarks'];

    // Get the student details for this specific log row
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * INTEGRATED FIX: Connects this individual record back to its parent sheet session.
     * This allows the system to look up dates and fixes the undefined method error.
     */
    public function sheet()
    {
        return $this->belongsTo(StudentAttendanceSheet::class, 'attendance_sheet_id');
    }
}
