<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveType::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $leaveTypes = $query->latest()->paginate(15);

        return view('admin.leave-types.index', compact('leaveTypes'));
    }

    public function create()
    {
        return view('admin.leave-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code',
            'description' => 'nullable|string',
            'days_per_year' => 'required|integer|min:0',
            'is_paid' => 'nullable|boolean',
            'carry_forward' => 'nullable|boolean',
            'max_carry_forward' => 'nullable|integer|min:0|required_if:carry_forward,1',
            'requires_approval' => 'nullable|boolean',
            'requires_document' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_paid'] = $request->has('is_paid');
        $validated['carry_forward'] = $request->has('carry_forward');
        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['requires_document'] = $request->has('requires_document');
        $validated['is_active'] = $request->has('is_active');

        LeaveType::create($validated);

        return redirect()->route('admin.leave-types.index')
            ->with('success', 'Leave type created successfully');
    }

    public function edit($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        return view('admin.leave-types.edit', compact('leaveType'));
    }

    public function update(Request $request, $id)
    {
        $leaveType = LeaveType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code,' . $id,
            'description' => 'nullable|string',
            'days_per_year' => 'required|integer|min:0',
            'is_paid' => 'nullable|boolean',
            'carry_forward' => 'nullable|boolean',
            'max_carry_forward' => 'nullable|integer|min:0|required_if:carry_forward,1',
            'requires_approval' => 'nullable|boolean',
            'requires_document' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_paid'] = $request->has('is_paid');
        $validated['carry_forward'] = $request->has('carry_forward');
        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['requires_document'] = $request->has('requires_document');
        $validated['is_active'] = $request->has('is_active');

        $leaveType->update($validated);

        return redirect()->route('admin.leave-types.index')
            ->with('success', 'Leave type updated successfully');
    }

    public function destroy($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        
        if ($leaveType->leaves()->count() > 0) {
            return back()->with('error', 'Cannot delete leave type with associated leave records');
        }

        if ($leaveType->balances()->count() > 0) {
            return back()->with('error', 'Cannot delete leave type with associated leave balances');
        }

        $leaveType->delete();

        return redirect()->route('admin.leave-types.index')
            ->with('success', 'Leave type deleted successfully');
    }

    public function toggleStatus($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        $leaveType->update(['is_active' => !$leaveType->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'status' => $leaveType->is_active
        ]);
    }
}