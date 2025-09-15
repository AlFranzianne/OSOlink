<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'content',
        'parent_id',
    ];

    protected $with = ['user', 'replies']; // Auto eager-load

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Parent comment
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // Direct replies
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('replies.user');
    }
}