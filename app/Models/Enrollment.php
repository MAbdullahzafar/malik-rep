<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';

    // Whitelist database columns for mass-assignment operations safely
    protected $fillable = ['enroll_no', 'student_id', 'course_id', 'join_date', 'fee'];

    /**
     * 🌟 ADDED RELATIONSHIP: Connects each enrollment record row back to its Student profile
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * 🌟 ADDED RELATIONSHIP: Connects each enrollment record row to its Course catalog profile
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
