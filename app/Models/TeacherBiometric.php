<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherBiometric extends Model
{
    use HasFactory;

    protected $table = 'teacher_biometrics';
    protected $fillable = ['teacher_id', 'credential_id', 'public_key'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
