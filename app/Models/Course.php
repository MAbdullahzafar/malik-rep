<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // Forces the model to point explicitly to the correct plural database table name
    protected $table = 'courses';

    // Core primary identification key mapping sequence anchor property
    protected $primaryKey = 'id';

    // ALLOWS MASS ASSIGNMENT: Gives Laravel explicit permission to save your form fields
    protected $fillable = ['name', 'syllabus', 'duration','fee'];

    /**
     * INTEGRATED: Establishes a link to the student attendance sheets registry.
     * This allows the system to look up real-time analytics for the dashboard.
     */
    public function sheets()
    {
        return $this->hasMany(StudentAttendanceSheet::class, 'course_id');
    }
}
