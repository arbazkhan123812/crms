<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryComponent;
use Illuminate\Http\Request;

class SalaryComponentController extends Controller
{
    public function index()
    {
        $components = SalaryComponent::orderBy('type')->orderBy('name')->paginate(15);
        return view('admin.payroll.salary-components.index', compact('components'));
    }

    public function create()
    {
        return view('admin.payroll.salary-components.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:salary_components',
            'type' => 'required|in:earning,deduction',
            'value_type' => 'required|in:fixed,percentage',
            'default_value' => 'nullable|numeric|min:0',
            'is_taxable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_taxable'] = $request->has('is_taxable');
        $validated['is_active'] = $request->has('is_active');

        SalaryComponent::create($validated);

        return redirect()->route('admin.payroll.salary-components.index')
            ->with('success', 'Salary component created successfully');
    }

    public function edit($id)
    {
        $component = SalaryComponent::findOrFail($id);
        return view('admin.payroll.salary-components.edit', compact('component'));
    }

    public function update(Request $request, $id)
    {
        $component = SalaryComponent::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:salary_components,code,' . $id,
            'type' => 'required|in:earning,deduction',
            'value_type' => 'required|in:fixed,percentage',
            'default_value' => 'nullable|numeric|min:0',
            'is_taxable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_taxable'] = $request->has('is_taxable');
        $validated['is_active'] = $request->has('is_active');

        $component->update($validated);

        return redirect()->route('admin.payroll.salary-components.index')
            ->with('success', 'Salary component updated successfully');
    }

    public function destroy($id)
    {
        $component = SalaryComponent::findOrFail($id);
        $component->delete();

        return redirect()->route('admin.payroll.salary-components.index')
            ->with('success', 'Salary component deleted successfully');
    }

    public function toggleStatus($id)
    {
        $component = SalaryComponent::findOrFail($id);
        $component->update(['is_active' => !$component->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
}