<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmCall extends Model
{
    protected $table = 'crm_calls';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'call_type',
        'call_purpose',
        'notes',
        'duration',
        'status',
        'call_date',
        'called_by',
        'call_owner',
    ];

    protected $casts = [
        'call_date' => 'datetime',
    ];

    public function calledBy()
    {
        return $this->belongsTo(User::class, 'called_by');
    }

    public function callOwner()
    {
        return $this->belongsTo(User::class, 'call_owner');
    }
}
