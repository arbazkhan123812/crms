<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with(['employee', 'leaveType', 'approvedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $leaves = $query->latest()->paginate(20);

        $stats = [
            'pending' => Leave::where('status', 'pending')->count(),
            'approved' => Leave::where('status', 'approved')->count(),
            'rejected' => Leave::where('status', 'rejected')->count(),
            'total' => Leave::count()
        ];

        $employees = Employee::where('status', 'active')->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();

        return view('admin.leaves.index', compact('leaves', 'stats', 'employees', 'leaveTypes'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();

        return view('admin.leaves.create', compact('employees', 'leaveTypes'));
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'leave_type_id' => 'required|exists:leave_types,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'reason' => 'required|string|max:500',
        'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
    ]);

    DB::beginTransaction();

    try {
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        $totalDays = 0;
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $holiday = Holiday::whereDate('date',$date)->exists();
            $isWeekend = $date->isWeekend();

            if (!$holiday && !$isWeekend) {
                $totalDays++;
            }
        }

        if ($validated['half_day'] != 'none') {
            $totalDays = $totalDays - 0.5;
        }

        $leave = Leave::create([
            'employee_id' => $validated['employee_id'],
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'half_day' => 'none',
            'reason' => $validated['reason'],
            'status' => 'pending',
            'created_by' => auth()->id()
        ]);

        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('leave-documents', 'public');
            $leave->update(['document_path' => $path]);
        }

        $balance = LeaveBalance::where('employee_id', $validated['employee_id'])
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('year', date('Y'))
            ->first();

        if ($balance) {
            $balance->increment('pending_days', $totalDays);
            $balance->decrement('remaining_days', $totalDays);
        }

        DB::commit();

        return redirect()->route('admin.leaves.index')
            ->with('success', 'Leave application submitted successfully');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error submitting leave: ' . $e->getMessage())
            ->withInput();
    }
}
    public function show($id)
    {
        $leave = Leave::with(['employee', 'leaveType', 'approvedBy', 'createdBy'])
            ->findOrFail($id);

        return view('admin.leaves.show', compact('leave'));
    }

public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:approved,rejected',
        'rejection_reason' => 'required_if:status,rejected'
    ]);

    DB::beginTransaction();

    try {
        $leave = Leave::with('employee')->findOrFail($id);

        if ($request->status == 'approved') {
            $leave->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            $balance = LeaveBalance::where('employee_id', $leave->employee_id)
                ->where('leave_type_id', $leave->leave_type_id)
                ->where('year', date('Y'))
                ->first();

            if ($balance) {
                $balance->decrement('pending_days', $leave->total_days);
                $balance->increment('used_days', $leave->total_days);
            }

            // Mark attendance for leave period
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            
            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $holiday = Holiday::whereDate('date', $date)->exists();
                $isWeekend = $date->isWeekend();
                
                if (!$holiday && !$isWeekend) {
                    $status = $leave->half_day != 'none' ? 'half_day' : 'leave';
                    
                    Attendance::updateOrCreate(
                        [
                            'employee_id' => $leave->employee_id,
                            'date' => $date->format('Y-m-d')
                        ],
                        [
                            'status' => $status,
                            'remarks' => 'Approved ' . $leave->leaveType->name,
                            'marked_by' => auth()->id()
                        ]
                    );
                }
            }
        } else {
            $leave->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason
            ]);

            $balance = LeaveBalance::where('employee_id', $leave->employee_id)
                ->where('leave_type_id', $leave->leave_type_id)
                ->where('year', date('Y'))
                ->first();

            if ($balance) {
                $balance->decrement('pending_days', $leave->total_days);
                $balance->increment('remaining_days', $leave->total_days);
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Leave ' . $request->status . ' successfully'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error updating leave status'
        ], 500);
    }
}
    public function balances(Request $request)
    {
        $query = LeaveBalance::with(['employee', 'leaveType']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        } else {
            $query->where('year', date('Y'));
        }

        $balances = $query->paginate(20);
        $employees = Employee::where('status', 'active')->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();

        return view('admin.leaves.balances', compact('balances', 'employees', 'leaveTypes'));
    }

    public function initializeBalance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'year' => 'required|integer|min:2000|max:2100',
            'total_days' => 'required|integer|min:0',
            'carried_forward' => 'nullable|integer|min:0'
        ]);

        try {
            LeaveBalance::updateOrCreate(
                [
                    'employee_id' => $request->employee_id,
                    'leave_type_id' => $request->leave_type_id,
                    'year' => $request->year
                ],
                [
                    'total_days' => $request->total_days,
                    'remaining_days' => $request->total_days - ($request->used_days ?? 0),
                    'carried_forward' => $request->carried_forward ?? 0
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Leave balance initialized successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error initializing balance'
            ], 500);
        }
    }

    public function calendar()
    {
        $leaves = Leave::with('employee')
            ->where('status', 'approved')
            ->get()
            ->map(function ($leave) {
                return [
                    'title' => $leave->employee->full_name . ' - ' . $leave->leaveType->name,
                    'start' => $leave->start_date,
                    'end' => $leave->end_date->addDay(),
                    'color' => '#28a745',
                    'url' => route('admin.leaves.show', $leave->id)
                ];
            });

        $holidays = Holiday::where('is_active', true)
            ->get()
            ->map(function ($holiday) {
                return [
                    'title' => $holiday->name,
                    'start' => $holiday->date,
                    'color' => '#dc3545',
                    'allDay' => true
                ];
            });

        $events = $leaves->concat($holidays);

        return view('admin.leaves.calender', compact('events'));
    }
}
