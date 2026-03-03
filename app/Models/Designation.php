<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Designation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'department_id', 'title', 'code',
        'grade', 'min_salary', 'max_salary', 'description', 'is_active',
        // Shift timings
        'shift_start_time', 'shift_end_time',
        'late_threshold', 'early_exit_threshold', 'overtime_threshold',
        'break_duration', 'is_night_shift', 'working_days',
        'grace_period_minutes', 'overtime_rate',
        'has_flexible_timing', 'flexible_start_from', 'flexible_start_to',
        'flexible_end_from', 'flexible_end_to',
        'half_day_start_time', 'half_day_end_time',
        'is_weekend_included'
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'is_active' => 'boolean',
        'is_night_shift' => 'boolean',
        'has_flexible_timing' => 'boolean',
        'is_weekend_included' => 'boolean',
        'working_days' => 'array',
        'shift_start_time' => 'datetime',
        'shift_end_time' => 'datetime',
        'flexible_start_from' => 'datetime',
        'flexible_start_to' => 'datetime',
        'flexible_end_from' => 'datetime',
        'flexible_end_to' => 'datetime',
        'half_day_start_time' => 'datetime',
        'half_day_end_time' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get formatted shift time
     */
    public function getShiftTimeAttribute()
    {
        if (!$this->shift_start_time || !$this->shift_end_time) {
            return 'Not Set';
        }
        
        $start = Carbon::parse($this->shift_start_time)->format('h:i A');
        $end = Carbon::parse($this->shift_end_time)->format('h:i A');
        
        if ($this->is_night_shift) {
            return $start . ' - ' . $end . ' (Night Shift)';
        }
        
        return $start . ' - ' . $end;
    }

    /**
     * Get total working minutes
     */
    public function getWorkingMinutesAttribute()
    {
        if (!$this->shift_start_time || !$this->shift_end_time) {
            return 9 * 60; // Default 9 hours
        }
        
        $start = Carbon::parse($this->shift_start_time);
        $end = Carbon::parse($this->shift_end_time);
        
        if ($this->is_night_shift && $end->lessThan($start)) {
            $end->addDay();
        }
        
        return $start->diffInMinutes($end) - $this->break_duration;
    }

    /**
     * Get working hours formatted
     */
    public function getWorkingHoursAttribute()
    {
        $minutes = $this->working_minutes;
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        return $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
    }

    /**
     * Check if given time is late
     */
    public function isLate($checkInTime)
    {
        if (!$this->shift_start_time) {
            return false;
        }
        
        $checkIn = Carbon::parse($checkInTime);
        $shiftStart = Carbon::parse($this->shift_start_time);
        
        // Add grace period
        $shiftStartWithGrace = $shiftStart->copy()->addMinutes($this->grace_period_minutes);
        
        return $checkIn->gt($shiftStartWithGrace);
    }

    /**
     * Calculate late minutes
     */
    public function calculateLateMinutes($checkInTime)
    {
        if (!$this->shift_start_time) {
            return 0;
        }
        
        $checkIn = Carbon::parse($checkInTime);
        $shiftStart = Carbon::parse($this->shift_start_time);
        
        if ($checkIn->gt($shiftStart)) {
            return $checkIn->diffInMinutes($shiftStart);
        }
        
        return 0;
    }

    /**
     * Calculate early exit minutes
     */
    public function calculateEarlyExitMinutes($checkOutTime)
    {
        if (!$this->shift_end_time) {
            return 0;
        }
        
        $checkOut = Carbon::parse($checkOutTime);
        $shiftEnd = Carbon::parse($this->shift_end_time);
        
        if ($checkOut->lt($shiftEnd)) {
            return $shiftEnd->diffInMinutes($checkOut);
        }
        
        return 0;
    }

    /**
     * Calculate overtime minutes
     */
    public function calculateOvertimeMinutes($checkOutTime)
    {
        if (!$this->shift_end_time || $this->overtime_threshold <= 0) {
            return 0;
        }
        
        $checkOut = Carbon::parse($checkOutTime);
        $shiftEnd = Carbon::parse($this->shift_end_time);
        
        if ($checkOut->gt($shiftEnd)) {
            $overtime = $checkOut->diffInMinutes($shiftEnd);
            return $overtime; // Return actual overtime minutes
        }
        
        return 0;
    }

    /**
     * Check if given date is working day
     */
    public function isWorkingDay($date)
    {
        if (!$this->working_days) {
            return true; // All days working
        }
        
        $day = Carbon::parse($date)->format('l'); // Monday, Tuesday, etc.
        
        // Convert to lowercase for comparison
        $workingDays = array_map('strtolower', $this->working_days);
        
        return in_array(strtolower($day), $workingDays);
    }

    /**
     * Get attendance status for a check-in/out
     */
    public function getAttendanceStatus($checkIn, $checkOut)
    {
        if (!$checkIn || !$checkOut) {
            return 'absent';
        }
        
        $checkInTime = Carbon::parse($checkIn);
        $checkOutTime = Carbon::parse($checkOut);
        
        $totalMinutes = $checkInTime->diffInMinutes($checkOutTime);
        $requiredMinutes = $this->working_minutes;
        
        // Check if half day
        if ($totalMinutes < ($requiredMinutes / 2)) {
            return 'half_day';
        }
        
        return 'present';
    }

    /**
     * Check if flexible timing
     */
    public function isFlexible()
    {
        return $this->has_flexible_timing;
    }

    /**
     * Validate if check-in is within flexible range
     */
    public function isValidFlexibleCheckIn($checkInTime)
    {
        if (!$this->isFlexible() || !$this->flexible_start_from || !$this->flexible_start_to) {
            return true;
        }
        
        $checkIn = Carbon::parse($checkInTime)->format('H:i');
        $startFrom = Carbon::parse($this->flexible_start_from)->format('H:i');
        $startTo = Carbon::parse($this->flexible_start_to)->format('H:i');
        
        return $checkIn >= $startFrom && $checkIn <= $startTo;
    }

    /**
     * Get shift summary
     */
    public function getShiftSummaryAttribute()
    {
        $summary = [];
        
        if ($this->shift_time) {
            $summary[] = $this->shift_time;
        }
        
        if ($this->working_hours) {
            $summary[] = $this->working_hours;
        }
        
        if ($this->is_night_shift) {
            $summary[] = 'Night Shift';
        }
        
        if ($this->has_flexible_timing) {
            $summary[] = 'Flexible Timing';
        }
        
        return implode(' • ', $summary);
    }
    public function getOvertimeFormatted($minutes)
{
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    return $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
}
}