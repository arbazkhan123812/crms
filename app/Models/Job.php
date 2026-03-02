<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;

    protected $table = 'jobs';

    protected $fillable = [
        'title',
        'department_id',
        'designation_id',
        'vacancies',
        'description',
        'requirements',
        'responsibilities',
        'qualifications',
        'experience_level',
        'job_type',
        'status',
        'posted_date',
        'closing_date',
        'min_salary',
        'max_salary',
        'location',
        'skills_required',
        'benefits',
        'views_count',
        'applications_count',
        'created_by'
    ];

    protected $casts = [
        'skills_required' => 'array',
        'benefits' => 'array',
        'posted_date' => 'date',
        'closing_date' => 'date',
        'vacancies' => 'integer',
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'published')
            ->where('closing_date', '>=', now());
    }

    public function getTitleWithCodeAttribute()
    {
        return $this->title . ' (' . $this->department->name . ')';
    }

    public function getDaysRemainingAttribute()
    {
        return now()->diffInDays($this->closing_date, false);
    }

    public function getSalaryRangeAttribute()
    {
        if ($this->min_salary && $this->max_salary) {
            return 'PKR ' . number_format($this->min_salary) . ' - ' . number_format($this->max_salary);
        }
        return 'Market Competitive';
    }
}