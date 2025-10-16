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
        'issued_at',
        'total_earnings',
        'total_deductions',
        'net_pay',
        'status',
        // add other columns if you have them
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'total_earnings' => 'float',
        'total_deductions' => 'float',
        'net_pay' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function payroll()
    {
        return $this->belongsTo(\App\Models\Payroll::class);
    }
}