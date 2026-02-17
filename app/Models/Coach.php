<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coach extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'phone',
        'specialization'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sports()
    {
        return $this->hasMany(Sport::class);
    }
}
