<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeExperience extends Model
{
    use HasFactory;

    protected $table = 'employee_experience';

    protected $fillable = [
        'employee_id',
        'company',
        'designation',
        'from_date',
        'to_date',
        'is_current',
        'job_description',
        'achievements',
        'skills_used',
        'reason_for_leaving',
        'salary',
        'location',
        'reporting_manager',
        'manager_contact',
        'document_path'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'is_current' => 'boolean',
        'salary' => 'decimal:2'
    ];

    /**
     * Get the employee that owns the experience record.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get formatted duration.
     */
    public function getDurationAttribute()
    {
        $from = $this->from_date ? $this->from_date->format('M Y') : 'Unknown';
        $to = $this->is_current ? 'Present' : ($this->to_date ? $this->to_date->format('M Y') : 'Unknown');
        
        return $from . ' - ' . $to;
    }

    /**
     * Calculate total experience in months.
     */
    public function getDurationInMonthsAttribute()
    {
        $start = $this->from_date;
        $end = $this->is_current ? now() : $this->to_date;
        
        if (!$start || !$end) {
            return 0;
        }
        
        return $start->diffInMonths($end);
    }

    /**
     * Get formatted duration in years and months.
     */
    public function getFormattedDurationAttribute()
    {
        $months = $this->duration_in_months;
        $years = floor($months / 12);
        $remainingMonths = $months % 12;
        
        $duration = [];
        if ($years > 0) {
            $duration[] = $years . ' year' . ($years > 1 ? 's' : '');
        }
        if ($remainingMonths > 0) {
            $duration[] = $remainingMonths . ' month' . ($remainingMonths > 1 ? 's' : '');
        }
        
        return !empty($duration) ? implode(' ', $duration) : 'Less than a month';
    }

    /**
     * Scope a query to get current experience.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope a query to get previous experience.
     */
    public function scopePrevious($query)
    {
        return $query->where('is_current', false);
    }

    /**
     * Scope a query to filter by company.
     */
    public function scopeByCompany($query, $company)
    {
        return $query->where('company', 'like', "%{$company}%");
    }

    /**
     * Scope a query to filter by designation.
     */
    public function scopeByDesignation($query, $designation)
    {
        return $query->where('designation', 'like', "%{$designation}%");
    }

    /**
     * Scope a query to order by most recent.
     */
    public function scopeMostRecent($query)
    {
        return $query->orderBy('from_date', 'desc');
    }

    /**
     * Check if this is the most recent experience.
     */
    public function isMostRecent()
    {
        $mostRecent = $this->employee->experience()
                        ->orderBy('from_date', 'desc')
                        ->first();
        
        return $mostRecent && $mostRecent->id === $this->id;
    }

    /**
     * Get total years of experience for an employee.
     */
    public static function getTotalExperience($employeeId)
    {
        $experiences = self::where('employee_id', $employeeId)->get();
        $totalMonths = 0;
        
        foreach ($experiences as $exp) {
            $totalMonths += $exp->duration_in_months;
        }
        
        $years = floor($totalMonths / 12);
        $months = $totalMonths % 12;
        
        return [
            'years' => $years,
            'months' => $months,
            'formatted' => ($years > 0 ? $years . ' years ' : '') . ($months > 0 ? $months . ' months' : '')
        ];
    }
}