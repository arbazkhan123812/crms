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

    // Scope for upcoming calls
    public function scopeUpcoming($query)
    {
        return $query->where('call_date', '>=', now())->where('status', 'scheduled');
    }

    // Scope for today's calls
    public function scopeToday($query)
    {
        return $query->whereDate('call_date', today());
    }

    // Scope for logged calls (completed)
    public function scopeLogged($query)
    {
        return $query->where('status', 'completed');
    }

    // Mark as completed (log call)
    public function markCompleted($duration = null, $notes = null)
    {
        $this->status = 'completed';
        if ($duration) {
            $this->duration = $duration;
        }
        if ($notes) {
            $this->notes = $notes;
        }
        return $this->save();
    }

    // Mark as missed
    public function markMissed()
    {
        $this->status = 'missed';
        return $this->save();
    }

    // Mark as voicemail
    public function markVoicemail()
    {
        $this->status = 'voicemail';
        return $this->save();
    }

    // Mark as no answer
    public function markNoAnswer()
    {
        $this->status = 'no_answer';
        return $this->save();
    }
}