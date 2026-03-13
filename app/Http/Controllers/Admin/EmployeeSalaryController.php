<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\SalaryTemplate;
use Illuminate\Http\Request;

class EmployeeSalaryController extends Controller
{
    public function index()
    {
        $salaries = EmployeeSalary::with(['employee', 'salaryTemplate'])
            ->where('is_active', true)
            ->paginate(15);

        return view('admin.payroll.employee-salaries.index', compact('salaries'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        $templates = SalaryTemplate::where('is_active', true)->get();

        return view('admin.payroll.employee-salaries.create', compact('employees', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id|unique:employee_salaries,employee_id',
            'salary_template_id' => 'required|exists:salary_templates,id',
            'effective_from' => 'required|date',
            'basic' => 'required|numeric|min:0',
            'hra' => 'nullable|numeric|min:0',
            'da' => 'nullable|numeric|min:0',
            'conveyance' => 'nullable|numeric|min:0',
            'medical' => 'nullable|numeric|min:0',
            'special' => 'nullable|numeric|min:0',
            'pf' => 'nullable|numeric|min:0',
            'esi' => 'nullable|numeric|min:0',
            'pt' => 'nullable|numeric|min:0',
            'tds' => 'nullable|numeric|min:0',
            'other_earnings' => 'nullable|array',
            'other_deductions' => 'nullable|array'
        ]);

        $grossSalary = $validated['basic'] + ($validated['hra'] ?? 0) + ($validated['da'] ?? 0) + 
                      ($validated['conveyance'] ?? 0) + ($validated['medical'] ?? 0) + ($validated['special'] ?? 0);

        if (!empty($validated['other_earnings'])) {
            foreach ($validated['other_earnings'] as $earning) {
                $grossSalary += $earning['amount'];
            }
        }

        $totalDeductions = ($validated['pf'] ?? 0) + ($validated['esi'] ?? 0) + 
                          ($validated['pt'] ?? 0) + ($validated['tds'] ?? 0);

        if (!empty($validated['other_deductions'])) {
            foreach ($validated['other_deductions'] as $deduction) {
                $totalDeductions += $deduction['amount'];
            }
        }

        $validated['gross_salary'] = $grossSalary;
        $validated['total_deductions'] = $totalDeductions;
        $validated['net_salary'] = $grossSalary - $totalDeductions;
        $validated['is_active'] = true;

        EmployeeSalary::create($validated);

        return redirect()->route('admin.payroll.employee-salaries.index')
            ->with('success', 'Employee salary assigned successfully');
    }

    public function edit($id)
    {
        $salary = EmployeeSalary::with('employee')->findOrFail($id);
        $employees = Employee::where('status', 'active')->get();
        $templates = SalaryTemplate::where('is_active', true)->get();

        return view('admin.payroll.employee-salaries.edit', compact('salary', 'employees', 'templates'));
    }

    public function update(Request $request, $id)
    {
        $salary = EmployeeSalary::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id|unique:employee_salaries,employee_id,' . $id,
            'salary_template_id' => 'required|exists:salary_templates,id',
            'effective_from' => 'required|date',
            'basic' => 'required|numeric|min:0',
            'hra' => 'nullable|numeric|min:0',
            'da' => 'nullable|numeric|min:0',
            'conveyance' => 'nullable|numeric|min:0',
            'medical' => 'nullable|numeric|min:0',
            'special' => 'nullable|numeric|min:0',
            'pf' => 'nullable|numeric|min:0',
            'esi' => 'nullable|numeric|min:0',
            'pt' => 'nullable|numeric|min:0',
            'tds' => 'nullable|numeric|min:0',
            'other_earnings' => 'nullable|array',
            'other_deductions' => 'nullable|array'
        ]);

        $grossSalary = $validated['basic'] + ($validated['hra'] ?? 0) + ($validated['da'] ?? 0) + 
                      ($validated['conveyance'] ?? 0) + ($validated['medical'] ?? 0) + ($validated['special'] ?? 0);

        if (!empty($validated['other_earnings'])) {
            foreach ($validated['other_earnings'] as $earning) {
                $grossSalary += $earning['amount'];
            }
        }

        $totalDeductions = ($validated['pf'] ?? 0) + ($validated['esi'] ?? 0) + 
                          ($validated['pt'] ?? 0) + ($validated['tds'] ?? 0);

        if (!empty($validated['other_deductions'])) {
            foreach ($validated['other_deductions'] as $deduction) {
                $totalDeductions += $deduction['amount'];
            }
        }

        $validated['gross_salary'] = $grossSalary;
        $validated['total_deductions'] = $totalDeductions;
        $validated['net_salary'] = $grossSalary - $totalDeductions;

        $salary->update($validated);

        return redirect()->route('admin.payroll.employee-salaries.index')
            ->with('success', 'Employee salary updated successfully');
    }

    public function destroy($id)
    {
        $salary = EmployeeSalary::findOrFail($id);
        $salary->delete();

        return redirect()->route('admin.payroll.employee-salaries.index')
            ->with('success', 'Employee salary deleted successfully');
    }

    public function history($employeeId)
    {
        $salaries = EmployeeSalary::with('salaryTemplate')
            ->where('employee_id', $employeeId)
            ->orderBy('effective_from', 'desc')
            ->paginate(15);

        $employee = Employee::findOrFail($employeeId);

        return view('admin.payroll.employee-salaries.history', compact('salaries', 'employee'));
    }
}