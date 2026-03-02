<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use SoftDeletes;



    protected $fillable = [
        'job_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'current_company',
        'current_designation',
        'experience_years',
        'current_ctc',
        'expected_ctc',
        'skills',
        'qualifications',
        'resume_path',
        'cover_letter_path',
        'photo_path',
        'source',
        'status',
        'remarks',
        'interview_feedback',
        'rating',
        'applied_date',
        'available_from_date',
        'notice_period'
    ];

    protected $casts = [
        'interview_feedback' => 'array',
        'applied_date' => 'date',
        'available_from_date' => 'date',
        'experience_years' => 'integer',
        'current_ctc' => 'decimal:2',
        'expected_ctc' => 'decimal:2',
        'rating' => 'float'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'applied' => 'secondary',
            'screening' => 'info',
            'shortlisted' => 'primary',
            'interview_scheduled' => 'warning',
            'interviewed' => 'info',
            'technical_round' => 'dark',
            'hr_round' => 'purple',
            'selected' => 'success',
            'offered' => 'success',
            'hired' => 'success',
            'rejected' => 'danger',
            'withdrawn' => 'secondary'
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }
}