<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'days_per_year',
        'is_paid',
        'carry_forward',
        'max_carry_forward',
        'requires_approval',
        'requires_document',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'carry_forward' => 'boolean',
        'requires_approval' => 'boolean',
        'requires_document' => 'boolean',
        'is_active' => 'boolean',
        'days_per_year' => 'integer',
        'max_carry_forward' => 'integer'
    ];

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function balances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}