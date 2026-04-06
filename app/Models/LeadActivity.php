<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadActivity extends Model
{
    protected $table = 'lead_activities';
    
    protected $fillable = [
        'lead_id', 'type', 'subject', 'description', 'status', 
        'due_date', 'completed_at', 'assigned_to'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}