<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'value_type',
        'default_value',
        'is_taxable',
        'is_active'
    ];

    protected $casts = [
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
        'default_value' => 'decimal:2'
    ];
}