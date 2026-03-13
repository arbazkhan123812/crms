<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    protected $fillable = [
        'employee_id',
        'salary_template_id',
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
        'gross_salary',
        'total_deductions',
        'net_salary',
        'other_earnings',
        'other_deductions',
        'effective_from',
        'effective_to',
        'is_active'
    ];

    protected $casts = [
        'other_earnings' => 'array',
        'other_deductions' => 'array',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
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

    public function salaryTemplate()
    {
        return $this->belongsTo(SalaryTemplate::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}