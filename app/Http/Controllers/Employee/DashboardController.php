<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $employee = $user->employee;

        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', Carbon::today())
            ->first();

        $monthlyAttendance = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->get();

        $presentCount = $monthlyAttendance->where('status', 'present')->count();
        $absentCount = $monthlyAttendance->where('status', 'absent')->count();
        $leaveCount = $monthlyAttendance->where('status', 'leave')->count();
        $lateCount = $monthlyAttendance->where('is_late', true)->count();

        $pendingLeaves = Leave::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->count();

        $approvedLeaves = Leave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereMonth('start_date', Carbon::now()->month)
            ->sum('total_days');

        // $recentAnnouncements = Announcement::where('is_active', true)
        //     ->whereDate('publish_date', '<=', Carbon::today())
        //     ->latest()
        //     ->limit(5)
        //     ->get();

        return view('employee.dashboard', compact(
            'employee',
            'todayAttendance',
            'presentCount',
            'absentCount',
            'leaveCount',
            'lateCount',
            'pendingLeaves',
            'approvedLeaves',
            'recentAnnouncements'
        ));
    }

    public function profile()
    {
        $employee = auth()->user()->employee->load(['department', 'designation', 'reportingTo']);
        return view('employee.profile', compact('employee'));
    }

    public function attendance()
    {
        $employee = auth()->user()->employee;

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', Carbon::now()->month)
            ->orderBy('date', 'desc')
            ->paginate(15);

        $summary = [
            'present' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', Carbon::now()->month)
                ->where('status', 'present')
                ->count(),
            'absent' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', Carbon::now()->month)
                ->where('status', 'absent')
                ->count(),
            'late' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', Carbon::now()->month)
                ->where('is_late', true)
                ->count(),
            'leave' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', Carbon::now()->month)
                ->where('status', 'leave')
                ->count()
        ];

        return view('employee.attendance', compact('attendances', 'summary'));
    }

    public function leaves()
    {
        $employee = auth()->user()->employee;

        $leaves = Leave::where('employee_id', $employee->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $balances = \App\Models\LeaveBalance::where('employee_id', $employee->id)
            ->with('leaveType')
            ->where('year', Carbon::now()->year)
            ->get();

        return view('employee.leaves', compact('leaves', 'balances'));
    }

    public function applyLeave(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500'
        ]);

        $employee = auth()->user()->employee;

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        Leave::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'status' => 'pending',
            'created_by' => $user->id ?? 1
        ]);

        return redirect()->back()->with('success', 'Leave application submitted successfully');
    }

    public function cancelLeave($id)
    {
        $leave = Leave::where('id', $id)
            ->where('employee_id', auth()->user()->employee->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $leave->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Leave cancelled successfully');
    }
}