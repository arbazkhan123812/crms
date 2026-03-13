<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryTemplate extends Model
{
    protected $fillable = [
        'name',
        'designation_id',
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
        'is_active'
    ];

    protected $casts = [
        'other_earnings' => 'array',
        'other_deductions' => 'array',
        'is_active' => 'boolean',
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

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function employeeSalaries()
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    public function calculateGrossSalary()
    {
        $earnings = [
            $this->basic,
            $this->hra,
            $this->da,
            $this->conveyance,
            $this->medical,
            $this->special
        ];

        if ($this->other_earnings) {
            foreach ($this->other_earnings as $earning) {
                $earnings[] = $earning['amount'];
            }
        }

        return array_sum($earnings);
    }

    public function calculateTotalDeductions()
    {
        $deductions = [
            $this->pf,
            $this->esi,
            $this->pt,
            $this->tds
        ];

        if ($this->other_deductions) {
            foreach ($this->other_deductions as $deduction) {
                $deductions[] = $deduction['amount'];
            }
        }

        return array_sum($deductions);
    }

    public function calculateNetSalary()
    {
        return $this->gross_salary - $this->total_deductions;
    }
}   