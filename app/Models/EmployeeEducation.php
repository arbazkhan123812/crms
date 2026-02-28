<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    use HasFactory;

    protected $table = 'employee_education';

    protected $fillable = [
        'employee_id',
        'course',
        'institution',
        'marks',
        'year',
        'grade',
        'specialization',
        'board_university',
        'start_date',
        'end_date',
        'is_highest_qualification',
        'document_path'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_highest_qualification' => 'boolean',
        'marks' => 'decimal:2'
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


    public function getFormattedMarksAttribute()
    {
        return $this->marks ? $this->marks . '%' : 'N/A';
    }

    public function getDurationAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->format('Y') . ' - ' . $this->end_date->format('Y');
        }
        return $this->year ?? 'N/A';
    }

   
    public function scopeHighestQualification($query)
    {
        return $query->where('is_highest_qualification', true);
    }

    public function scopeByCourse($query, $course)
    {
        return $query->where('course', 'like', "%{$course}%");
    }

    public function scopeByInstitution($query, $institution)
    {
        return $query->where('institution', 'like', "%{$institution}%");
    }

  
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('end_date', $year)
                    ->orWhere('year', $year);
    }
}