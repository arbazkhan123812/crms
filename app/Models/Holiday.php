<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'name',
        'date',
        'year',
        'type',
        'is_recurring',
        'description',
        'is_active'
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'year' => 'integer'
    ];

    public function getFormattedDateAttribute()
    {
        return $this->date->format('d M, Y');
    }

    public function getDayNameAttribute()
    {
        return $this->date->format('l');
    }
}