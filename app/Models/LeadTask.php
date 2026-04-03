<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadTask extends Model
{
    protected $table = 'lead_tasks';
    
    protected $fillable = [
        'lead_id', 'subject', 'description', 'assigned_to', 
        'due_date', 'status', 'completed_at', 'created_by'
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function complete()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        return $this->save();
    }
}