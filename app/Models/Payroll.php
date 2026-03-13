<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'basic',
        'hra',
        'da',
        'conveyance',
        'medical',
        'special',
        'pf',
        'esi',
        'pt',
        'tds',
        'other_earnings',
        'other_deductions',
        'working_days',
        'present_days',
        'absent_days',
        'leave_days',
        'gross_salary',
        'total_deductions',
        'net_salary',
        'status',
        'processed_date',
        'processed_by',
        'payment_date',
        'payment_mode',
        'remarks'
    ];

    protected $casts = [
        'other_earnings' => 'array',
        'other_deductions' => 'array',
        'processed_date' => 'date',
        'payment_date' => 'date',
        'basic' => 'decimal:2',
        'hra' => 'decimal:2',
        'da' => 'decimal:2',
        'conveyance' => 'decimal:2',
        'medical' => 'decimal:2',
        'special' => 'decimal:2',
        'pf' => 'decimal:2',
        'esi' => 'decimal:2',
        'pt' => 'decimal:2',
        'tds' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::create()->month($this->month)->format('F');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'processed' => 'info',
            'paid' => 'success',
            'cancelled' => 'danger'
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getNetSalaryFormattedAttribute()
    {
        return 'PKR ' . number_format($this->net_salary, 2);
    }
}