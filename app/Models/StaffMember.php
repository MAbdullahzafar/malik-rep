<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'contact',
        'base_salary',
        'status'
    ];

    /**
     * Polymorphic Relationship: A staff member has many monthly payroll entries.
     */
    public function payrolls()
    {
        return $this->morphMany(Payroll::class, 'payable');
    }
}
