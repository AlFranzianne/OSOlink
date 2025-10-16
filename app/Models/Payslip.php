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
        'file_path',
        'status',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'total_earnings' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function payroll()
    {
        return $this->belongsTo(\App\Models\Payroll::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}