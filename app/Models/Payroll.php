<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'base_pay'     => 'float',
        'hourly_rate'  => 'float',
        'hours_worked' => 'float',
        'gross_pay'    => 'float',
        'deductions'   => 'float',
        'net_pay'      => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}