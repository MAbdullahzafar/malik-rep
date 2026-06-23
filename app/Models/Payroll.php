<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'payable_id',
        'payable_type',
        'salary_month',
        'base_amount',
        'deductions',
        'net_paid',
        'status',
        'payment_date'
    ];

    /**
     * Get the owning payable model (Can be a Teacher or a StaffMember).
     */
    public function payable()
    {
        return $this->morphTo();
    }
}
