<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\SalaryComponent;
use App\Models\SalaryTemplate;
use Illuminate\Http\Request;

class SalaryTemplateController extends Controller
{
    public function index()
    {
        $templates = SalaryTemplate::with('designation')->paginate(15);
        return view('admin.payroll.salary-templates.index', compact('templates'));
    }

    public function create()
    {
        $designations = Designation::where('is_active', true)->get();
        $components = SalaryComponent::where('is_active', true)->get();
        
        $earnings = $components->where('type', 'earning');
        $deductions = $components->where('type', 'deduction');

        return view('admin.payroll.salary-templates.create', compact('designations', 'earnings', 'deductions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation_id' => 'required|exists:designations,id',
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
            'other_deductions' => 'nullable|array',
            'is_active' => 'nullable|boolean'
        ]);

        // Calculate totals
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
        $validated['is_active'] = $request->has('is_active');

        SalaryTemplate::create($validated);

        return redirect()->route('admin.payroll.salary-templates.index')
            ->with('success', 'Salary template created successfully');
    }

    public function edit($id)
    {
        $template = SalaryTemplate::findOrFail($id);
        $designations = Designation::where('is_active', true)->get();
        $components = SalaryComponent::where('is_active', true)->get();
        
        $earnings = $components->where('type', 'earning');
        $deductions = $components->where('type', 'deduction');

        return view('admin.payroll.salary-templates.edit', compact('template', 'designations', 'earnings', 'deductions'));
    }

    public function update(Request $request, $id)
    {
        $template = SalaryTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation_id' => 'required|exists:designations,id',
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
            'other_deductions' => 'nullable|array',
            'is_active' => 'nullable|boolean'
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
        $validated['is_active'] = $request->has('is_active');

        $template->update($validated);

        return redirect()->route('admin.payroll.salary-templates.index')
            ->with('success', 'Salary template updated successfully');
    }

    public function destroy($id)
    {
        $template = SalaryTemplate::findOrFail($id);
        
        if ($template->employeeSalaries()->count() > 0) {
            return back()->with('error', 'Cannot delete template assigned to employees');
        }

        $template->delete();

        return redirect()->route('admin.payroll.salary-templates.index')
            ->with('success', 'Salary template deleted successfully');
    }

    public function getByDesignation($designationId)
    {
        $templates = SalaryTemplate::where('designation_id', $designationId)
            ->where('is_active', true)
            ->get(['id', 'name', 'gross_salary', 'net_salary']);

        return response()->json($templates);
    }
}