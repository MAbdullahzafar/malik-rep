<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teachers';
    
    // Updated fillable array to handle substitution parameters seamlessly
    protected $fillable = [
        'name', 
        'is_substitute',
        'specialized_subject',
        'email', 
        'phone', 
        'designation',
        'photo'
    ];

    /**
     * INTEGRATED RELATION: Connects the teacher to their saved laptop biometric hardware keys.
     */
    public function biometrics()
    {
        return $this->hasMany(TeacherBiometric::class, 'teacher_id');
    }

    /**
     * INTEGRATED RELATION: Links the teacher to their history of check-in and check-out logs.
     */
    public function attendanceLogs()
    {
        return $this->hasMany(TeacherAttendanceLog::class, 'teacher_id');
    }

    /**
     * POLYMORPHIC RELATION: Links the teacher to their historical monthly payroll salary ledger entries.
     */
    public function payrolls()
    {
        return $this->morphMany(Payroll::class, 'payable');
    }
}
