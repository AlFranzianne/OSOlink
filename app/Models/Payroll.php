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
        'hours_worked',
        'hourly_rate',
        'gross_pay',
        'tax_deduction',
        'other_deductions',
        'net_pay',
        'pay_period_start',
        'pay_period_end',
        'status',
    ];

    protected $casts = [
        'hours_worked' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Virtual attribute to get total deductions.
     * Allows using $payroll->deductions in views/controllers.
     */
    public function getDeductionsAttribute()
    {
        return ($this->tax_deduction ?? 0) + ($this->other_deductions ?? 0);
    }
}