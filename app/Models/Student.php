<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Course; // 🌟 FIXED: Added missing class reference import to prevent database crashes!
use App\Models\Enrollment; // 🌟 FIXED: Added missing class reference import for relationship mapping integrity

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    protected $primaryKey = 'id';
    
    // FIXED: Added 'photo' alongside your existing columns to whitelist it for database saving
    protected $fillable = ['reg_no', 'name', 'address', 'mobile', 'contact', 'photo'];

    /**
     * AUTOMATED LIFECYCLE HOOK: Instantly generates unique registration codes sequentially 
     * across the entire system database directory regardless of chosen course tags.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            // Only execute code if a registration number hasn't been set manually
            if (empty($student->reg_no)) {
                // Find the course linked via request input data parameter
                $courseId = request()->input('course_id');
                $coursePrefix = 'ST'; // Fallback prefix if no course is detected

                if ($courseId) {
                    $course = Course::find($courseId);
                    if ($course && !empty($course->name)) {
                        $courseName = trim($course->name);
                        
                        // Detects if the course title is already a short code abbreviation
                        if (preg_match('/^[A-Z]{2,4}$/i', $courseName)) {
                            $coursePrefix = strtoupper($courseName);
                        } else {
                            $words = explode(' ', $courseName);
                            $prefix = '';
                            foreach ($words as $word) {
                                $prefix .= strtoupper(substr(trim($word), 0, 1));
                            }
                            $coursePrefix = !empty($prefix) ? $prefix : 'ST';
                        }
                    }
                }

                // // 🌟 FIXED GLOBAL LOOKUP LOGIC LAYER
                // // Query database to find the absolute highest sequential number used by ANY student across the whole system
                // $lastStudentWithAnyNumber = self::where('reg_no', 'REGEXP', '^[A-Z]+-[0-9]+$')
                //     ->get()
                //     ->sortByDesc(function($item) {
                //         // Extract just the numerical digits from the registration code string (e.g. "WD-2001" -> 2001)
                //         $parts = explode('-', $item->reg_no);
                //         return isset($parts[1]) ? intval($parts[1]) : 0;
                //     })
                //     ->first();





                                  $lastStudentWithAnyNumber = self::all()
                           ->filter(function ($student) {
                          return preg_match('/^[A-Z]+-\d+$/', $student->reg_no);
                  })
                             ->sortByDesc(function ($student) {
                          $parts = explode('-', $student->reg_no);
                           return isset($parts[1]) ? intval($parts[1]) : 0;
                            })
                              ->first();



                if ($lastStudentWithAnyNumber) {
                    $parts = explode('-', $lastStudentWithAnyNumber->reg_no);
                    $lastNumber = isset($parts[1]) ? intval($parts[1]) : 1999;
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 2000; // Baseline starting point for your school's directory sequence
                }

                // Map the final sequential code string index to model attribute parameters
                $student->reg_no = $coursePrefix . '-' . $nextNumber;
            }
        });
    }

    /**
     * INTEGRATED RELATION: Connects the student to their course enrollments index registry.
     * This allows the attendance sheet matrix to isolate and load only course-specific students.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * ADDED RELATIONSHIP: Direct path to fetch the course name through the enrollments table
     */
    public function course()
    {
        return $this->hasOneThrough(
            Course::class,
            Enrollment::class,
            'student_id', 
            'id',         
            'id',         
            'course_id'   
        );
    }
}