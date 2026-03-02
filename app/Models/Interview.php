<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $fillable = [
        'candidate_id',
        'job_id',
        'interviewer_id',
        'round',
        'scheduled_at',
        'duration_minutes',
        'mode',
        'location_or_link',
        'notes',
        'status',
        'rating',
        'feedback',
        'result'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'rating' => 'float'
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function getRoundLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->round));
    }

    public function getEndTimeAttribute()
    {
        return $this->scheduled_at->copy()->addMinutes($this->duration_minutes);
    }
}