<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadCall extends Model
{
    protected $table = 'lead_calls';
    
    protected $fillable = [
        'lead_id', 'call_type', 'call_purpose', 'notes', 
        'duration', 'status', 'call_date', 'called_by'
    ];

    protected $casts = [
        'call_date' => 'datetime'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function calledBy()
    {
        return $this->belongsTo(User::class, 'called_by');
    }
}