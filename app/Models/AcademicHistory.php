<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'from_class',
        'to_class',
        'academic_year',
        'promotion_date'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
