<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index(Request $request)
    {
        $query = Designation::with(['department'])
            ->withCount('employees');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $designations = $query->latest()->paginate(15);
        $departments = Department::all();
        $grades = Designation::distinct('grade')->pluck('grade');

        return view('admin.designations.index', compact('designations', 'departments', 'grades'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();

        $defaultWorkingDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        return view('admin.designations.create', compact('departments', 'defaultWorkingDays'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'title' => 'required|string|max:255',
            'code' => 'required|string|unique:designations,code',
            'grade' => 'nullable|string|max:50',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gt:min_salary',
            'description' => 'nullable|string',
            'is_active' => 'boolean',

            // Shift validation
            'shift_start_time' => 'nullable|date_format:H:i',
            'shift_end_time' => 'nullable|date_format:H:i|after:shift_start_time',
            'late_threshold' => 'nullable|integer|min:0',
            'early_exit_threshold' => 'nullable|integer|min:0',
            'overtime_threshold' => 'nullable|integer|min:0',
            'break_duration' => 'nullable|integer|min:0',
            'is_night_shift' => 'nullable|boolean',
            'working_days' => 'nullable|array',
            'grace_period_minutes' => 'nullable|integer|min:0',
            'overtime_rate' => 'nullable|numeric|min:1',
            'has_flexible_timing' => 'nullable|boolean',
            'flexible_start_from' => 'nullable|date_format:H:i|required_with:has_flexible_timing',
            'flexible_start_to' => 'nullable|date_format:H:i|required_with:has_flexible_timing|after:flexible_start_from',
            'flexible_end_from' => 'nullable|date_format:H:i|required_with:has_flexible_timing',
            'flexible_end_to' => 'nullable|date_format:H:i|required_with:has_flexible_timing|after:flexible_end_from',
            'half_day_start_time' => 'nullable|date_format:H:i',
            'half_day_end_time' => 'nullable|date_format:H:i|after:half_day_start_time',
            'is_weekend_included' => 'nullable|boolean',
        ]);

        // Set defaults
        $validated['late_threshold'] = $validated['late_threshold'] ?? 15;
        $validated['early_exit_threshold'] = $validated['early_exit_threshold'] ?? 15;
        $validated['break_duration'] = $validated['break_duration'] ?? 60;
        $validated['overtime_rate'] = $validated['overtime_rate'] ?? 1.5;

        Designation::create($validated);

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation created successfully');
    }



    public function show(Designation $designation)
    {
        $designation->load(['department']);

        $employees = $designation->employees()
            ->with(['department'])
            ->paginate(15);

        return view('admin.designations.show', compact('designation', 'employees'));
    }

    public function edit(Designation $designation)
    {
        $departments = Department::where('is_active', true)->get();

        // Decode working days if stored as JSON
        if (is_string($designation->working_days)) {
            $designation->working_days = json_decode($designation->working_days);
        }

        return view('admin.designations.edit', compact('designation', 'departments'));
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'title' => 'required|string|max:255',
            'code' => 'required|string|unique:designations,code,' . $designation->id,
            'grade' => 'nullable|string|max:50',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gt:min_salary',
            'description' => 'nullable|string',
            'is_active' => 'boolean',

            // Shift validation
            'shift_start_time' => 'nullable|date_format:H:i',
            'shift_end_time' => 'nullable|date_format:H:i|after:shift_start_time',
            'late_threshold' => 'nullable|integer|min:0',
            'early_exit_threshold' => 'nullable|integer|min:0',
            'overtime_threshold' => 'nullable|integer|min:0',
            'break_duration' => 'nullable|integer|min:0',
            'is_night_shift' => 'nullable|boolean',
            'working_days' => 'nullable|array',
            'grace_period_minutes' => 'nullable|integer|min:0',
            'overtime_rate' => 'nullable|numeric|min:1',
            'has_flexible_timing' => 'nullable|boolean',
            'flexible_start_from' => 'nullable|date_format:H:i|required_with:has_flexible_timing',
            'flexible_start_to' => 'nullable|date_format:H:i|required_with:has_flexible_timing|after:flexible_start_from',
            'flexible_end_from' => 'nullable|date_format:H:i|required_with:has_flexible_timing',
            'flexible_end_to' => 'nullable|date_format:H:i|required_with:has_flexible_timing|after:flexible_end_from',
            'half_day_start_time' => 'nullable|date_format:H:i',
            'half_day_end_time' => 'nullable|date_format:H:i|after:half_day_start_time',
            'is_weekend_included' => 'nullable|boolean',
        ]);

        // Set defaults for shift fields if not provided
        $validated['late_threshold'] = $validated['late_threshold'] ?? 15;
        $validated['early_exit_threshold'] = $validated['early_exit_threshold'] ?? 15;
        $validated['break_duration'] = $validated['break_duration'] ?? 60;
        $validated['overtime_rate'] = $validated['overtime_rate'] ?? 1.5;

        $designation->update($validated);

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation updated successfully');
    }

    public function destroy(Designation $designation)
    {
        if ($designation->employees()->count() > 0) {
            return back()->with('error', 'Cannot delete designation with assigned employees');
        }

        $designation->delete();

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation deleted successfully');
    }

    public function updateSalaryRange(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'min_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|min:0|gt:min_salary'
        ]);

        $designation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Salary range updated successfully'
        ]);
    }

    public function getByDepartment(Request $request, $departmentId = null)
    {
        if ($departmentId) {
            $designations = Designation::where('department_id', $departmentId)
                ->where('is_active', true)
                ->select('id', 'title', 'code', 'grade')
                ->get();
        } else {
            $designations = Designation::where('is_active', true)
                ->select('id', 'title', 'code', 'grade')
                ->get();
        }

        return response()->json($designations);
    }

    public function getList(Request $request)
    {
        $query = Designation::query();

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('active_only')) {
            $query->where('is_active', true);
        }

        $designations = $query->select('id', 'title', 'code', 'grade')->get();

        return response()->json($designations);
    }
}
