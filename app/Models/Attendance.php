<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'check_in_ip',
        'check_out_ip',
        'check_in_location',
        'check_out_location',
        'check_in_method',
        'total_hours',
        'overtime_minutes',
        'late_minutes',
        'early_exit_minutes',
        'is_late',
        'is_early_exit',
        'is_overtime',
        'status',
        'remarks',
        'marked_by'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'is_late' => 'boolean',
        'is_early_exit' => 'boolean',
        'is_overtime' => 'boolean'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function regularization()
    {
        return $this->hasOne(AttendanceRegularization::class);
    }

    public function getTotalHoursFormattedAttribute()
    {
        if (!$this->total_hours) return '0h';
        $hours = floor($this->total_hours / 60);
        $minutes = $this->total_hours % 60;
        return $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
    }

    public function getLateMinutesFormattedAttribute()
    {
        if (!$this->late_minutes) return '0m';
        $hours = floor($this->late_minutes / 60);
        $minutes = $this->late_minutes % 60;
        return $hours > 0 ? $hours . 'h ' . $minutes . 'm' : $minutes . 'm';
    }

    public function getOvertimeFormattedAttribute()
    {
        $minutes = $this->overtime_minutes;

        if (!$minutes || $minutes <= 0) return '0h';

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
    }


    public function getRequiredHoursForDate($date)
    {
        if (!$this->isWorkingDay($date)) {
            return 0;
        }

        return $this->working_minutes;
    }

    /**
     * Check if check-in time is within flexible range
     */
    public function isWithinFlexibleRange($time)
    {
        if (!$this->has_flexible_timing) {
            return true;
        }

        $timeStr = Carbon::parse($time)->format('H:i');
        $startFrom = Carbon::parse($this->flexible_start_from)->format('H:i');
        $startTo = Carbon::parse($this->flexible_start_to)->format('H:i');

        return $timeStr >= $startFrom && $timeStr <= $startTo;
    }

    public function getShiftStatus($time)
    {
        $timeCarbon = Carbon::parse($time);
        $shiftStart = Carbon::parse($this->shift_start_time);
        $shiftEnd = Carbon::parse($this->shift_end_time);

        if ($timeCarbon->lt($shiftStart)) {
            return 'early';
        } elseif ($timeCarbon->gt($shiftEnd)) {
            return 'late';
        } else {
            return 'on_time';
        }
    }
    public function isComplete()
    {
        return $this->check_in && $this->check_out;
    }

    /**
     * Get attendance duration in hours
     */
    public function getDurationInHoursAttribute()
    {
        if (!$this->check_in || !$this->check_out) {
            return 0;
        }

        return round($this->total_hours / 60, 2);
    }

    /**
     * Get attendance status with shift context
     */
    public function getShiftBasedStatusAttribute()
    {
        if ($this->status == 'absent') {
            return 'Absent';
        }

        if ($this->is_late && $this->is_early_exit) {
            return 'Late & Early Exit';
        }

        if ($this->is_late) {
            return 'Late';
        }

        if ($this->is_early_exit) {
            return 'Early Exit';
        }

        if ($this->is_overtime) {
            return 'Overtime';
        }

        return ucfirst($this->status);
    }
}
