<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    // 🌟 UNLOCK MASS ASSIGNMENT FIELDS FOR SECURE DATABASE INJECTIONS
    protected $fillable = [
        'student_id',
        'course_id',
        'exam_type',
        'evaluation_date',
        'marks_obtained',
        'total_marks',
        'grade_letter',
        'status'
    ];

    /**
     * Relationship back to the primary Student profile model index
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship back to the primary Course curriculum track model index
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
