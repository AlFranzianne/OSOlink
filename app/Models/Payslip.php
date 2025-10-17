<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'user_id',
        'period_from',
        'period_to',
        'total_earnings',
        'tax_deduction',
        'other_deductions',
        'total_deductions',
        'net_pay',
        'status',
        'issued_at',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to'   => 'date',
        'issued_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}