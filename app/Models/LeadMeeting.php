<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadMeeting extends Model
{
    protected $table = 'lead_meetings';
    
    protected $fillable = [
        'lead_id', 'title', 'description', 'meeting_type', 'location', 
        'meeting_link', 'meeting_date', 'duration', 'status', 'notes', 
        'assigned_to', 'created_by'
    ];

    protected $casts = [
        'meeting_date' => 'datetime'
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

    // Scope for upcoming meetings
    public function scopeUpcoming($query)
    {
        return $query->where('meeting_date', '>=', now())->where('status', 'scheduled');
    }

    // Scope for today's meetings
    public function scopeToday($query)
    {
        return $query->whereDate('meeting_date', today());
    }

    // Complete meeting
    public function complete($notes = null)
    {
        $this->status = 'completed';
        if ($notes) {
            $this->notes = $notes;
        }
        return $this->save();
    }

    // Cancel meeting
    public function cancel()
    {
        $this->status = 'cancelled';
        return $this->save();
    }

    // Reschedule meeting
    public function reschedule($newDate)
    {
        $this->meeting_date = $newDate;
        $this->status = 'scheduled';
        return $this->save();
    }
    
}