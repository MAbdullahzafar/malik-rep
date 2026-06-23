<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendanceSheet extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'teacher_id', 'attendance_date'];

    // Get the course/class information
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Get all student records attached to this sheet
    public function records()
    {
        return $this->hasMany(StudentAttendanceRecord::class, 'attendance_sheet_id');
    }
}
