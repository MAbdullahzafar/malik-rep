<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'id';

    // Whitelist database parameters for mass assignment operations safely
    protected $fillable = [
        'receipt_no', 
        'student_id', 
        'enrollment_id', 
        'amount', 
        'paid_amount', 
        'payment_date', 
        'paid_date', 
        'total_fee'
    ];

    /**
     * 🌟 ADDED RELATIONSHIP: Connects each payment row directly back to its Student profile
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * 🌟 ADDED RELATIONSHIP: Connects each payment row directly to its Enrollment log record
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }
}
