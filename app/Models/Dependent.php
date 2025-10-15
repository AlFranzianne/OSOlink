<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'relationship',
        'date_of_birth',
    ];

    // Relationship: Dependent belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
