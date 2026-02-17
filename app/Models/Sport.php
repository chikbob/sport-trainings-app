<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'coach_id', 'location'];

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }

    public function registrations()
    {
        return $this->hasManyThrough(
            Registration::class,
            Training::class,
            'sport_id',     // FK в таблице trainings, ссылается на sport
            'training_id',  // FK в таблице registrations, ссылается на training
            'id',           // Локальный ключ в таблице sports
            'id'            // Локальный ключ в таблице trainings
        );
    }

    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }
}
