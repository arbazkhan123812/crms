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
        'subject',
        'notes',
        'duration',
        'status',
        'call_date',
        'start_time',
        'end_time',
        'called_by',
        'call_owner',
        'related_to_type',
        'related_to_id',
        'outgoing_call_status',
    ];

    protected $casts = [
        'call_date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
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
