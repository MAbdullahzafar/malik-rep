<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendanceLog extends Model
{
    use HasFactory;

    protected $table = 'teacher_attendance_logs';
    protected $fillable = ['teacher_id', 'log_date', 'log_day', 'check_in', 'check_out', 'status'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
