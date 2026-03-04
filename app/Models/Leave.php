<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'total_days',
        'half_day',
        'reason',
        'remarks',
        'document_path',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'total_days' => 'integer'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDurationAttribute()
    {
        return $this->start_date->format('d M') . ' - ' . $this->end_date->format('d M Y');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary'
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getHalfDayLabelAttribute()
    {
        $labels = [
            'none' => 'Full Day',
            'first_half' => 'First Half',
            'second_half' => 'Second Half'
        ];
        return $labels[$this->half_day] ?? 'Full Day';
    }
}