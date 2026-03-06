<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    // Заполняемые поля, если нужно
    protected $fillable = [
        'sport_id',
        'date',
        'time',
        'place',
        'notes',
        'is_cancelled',
        'cancelled_at',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_cancelled' => 'boolean',
        'cancelled_at' => 'datetime',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    // Отношение к спорту
    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    // Отношение к регистрациям — добавь это!
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
