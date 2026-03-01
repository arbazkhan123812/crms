<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with(['parent', 'manager'])->withCount('employees');
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $departments = $query->latest()->paginate(15);
        
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $parentDepartments = Department::where('is_active', true)->get();
        $managers = Employee::where('status', 'active')->get();
        
        return view('admin.departments.create', compact('parentDepartments', 'managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:departments,code',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'is_active' => 'boolean'
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully');
    }

    public function show(Department $department)
    {
        $department->load(['parent', 'manager', 'children']);
        
        $employees = Employee::where('department_id', $department->id)
                            ->with(['designation'])
                            ->paginate(15);
        
        return view('admin.departments.show', compact('department', 'employees'));
    }

    public function edit(Department $department)
    {
        $parentDepartments = Department::where('id', '!=', $department->id)
                                      ->where('is_active', true)
                                      ->get();
        
        $managers = Employee::where('status', 'active')->where('is_reporting_manager' , 1)->get();
        
        return view('admin.departments.edit', compact('department', 'parentDepartments', 'managers'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'is_active' => 'boolean'
        ]);

        if ($request->filled('parent_id') && $request->parent_id == $department->id) {
            return back()->withErrors(['parent_id' => 'Department cannot be its own parent'])->withInput();
        }

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully');
    }

    public function destroy(Department $department)
    {
        if ($department->employees()->count() > 0) {
            return back()->with('error', 'Cannot delete department with assigned employees');
        }

        if ($department->children()->count() > 0) {
            return back()->with('error', 'Cannot delete department with child departments');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully');
    }

    public function hierarchy()
    {
        $departments = Department::with(['children', 'manager'])
                                ->whereNull('parent_id')
                                ->get();
        
        return view('admin.departments.hierarchy', compact('departments'));
    }

    public function assignManager(Request $request, Department $department)
    {
        $validated = $request->validate([
            'manager_id' => 'required|exists:employees,id'
        ]);

        $department->update(['manager_id' => $request->manager_id]);

        return response()->json([
        'success' => true,
            'message' => 'Manager assigned successfully'
        ]);
    }

    public function getList(Request $request)
    {
        $query = Department::query();
        
        if ($request->filled('active_only')) {
            $query->where('is_active', true);
        }
        
        $departments = $query->select('id', 'name', 'code')->get();
        
        return response()->json($departments);
    }

    public function employees(Department $department)
    {
        $employees = $department->employees()
                               ->with(['designation'])
                               ->paginate(20);
        
        return view('admin.departments.employees', compact('department', 'employees'));
    }
}