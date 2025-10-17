<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $table = 'payrolls';

    protected $fillable = [
        'user_id',
        'employment_status',
        'base_pay',
        'hourly_rate',
        'hours_worked',
        'gross_pay',
        'deductions',
        'net_pay',
        'status',
        'period_from',
        'period_to',
        'tax_deduction',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // If you use payslips as fallback for period dates
    public function payslip()
    {
        return $this->hasOne(\App\Models\Payslip::class, 'payroll_id');
    }
}