<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee_id = $user->employee_id;
        $employee = Employee::find($employee_id);

        $todayAttendance = Attendance::where('employee_id', $employee_id)
            ->whereDate('date', Carbon::today())
            ->first();

        $monthlyAttendance = Attendance::where('employee_id', $employee_id)
            ->whereMonth('date', Carbon::now('Asia/Karachi')->month)
            ->whereYear('date', Carbon::now('Asia/Karachi')->year)
            ->get();

        $presentCount = $monthlyAttendance->where('status', 'present')->count();
        $absentCount = $monthlyAttendance->where('status', 'absent')->count();
        $leaveCount = $monthlyAttendance->where('status', 'leave')->count();
        $lateCount = $monthlyAttendance->where('is_late', true)->count();

        $pendingLeaves = Leave::where('employee_id', $employee_id)
            ->where('status', 'pending')
            ->count();

        $approvedLeaves = Leave::where('employee_id', $employee_id)
            ->where('status', 'approved')
            ->whereMonth('start_date', Carbon::now('Asia/Karachi')->month)
            ->sum('total_days');

        // Add these two lines
        $balances = \App\Models\LeaveBalance::where('employee_id', $employee_id)
            ->with('leaveType')
            ->where('year', Carbon::now('Asia/Karachi')->year)
            ->get();

        $leaves = Leave::where('employee_id', $employee_id)
            ->with('leaveType')
            ->latest()
            ->limit(5)
            ->get();

        return view('employees.dashboard', compact(
            'employee',
            'todayAttendance',
            'presentCount',
            'absentCount',
            'leaveCount',
            'lateCount',
            'pendingLeaves',
            'approvedLeaves',
            'balances',
            'leaves'
        ));
    }
    public function profile()
    {
        $employee = auth()->user()->employee->load(['department', 'designation', 'reportingTo']);
        return view('employee.profile', compact('employee'));
    }

    public function checkIn(Request $request)
    {
        $user = auth()->user();
        $employee = Employee::find($user->employee_id);

        if (!$employee) {
            return response()->json(['error' => 'No employee record found'], 404);
        }

        $designation = $employee->designation;

        if (!$designation || !$designation->shift_start_time) {
            return response()->json(['error' => 'No shift timing assigned for your designation'], 404);
        }

        $today = Carbon::today();

        // Check if today is working day
        if (!$designation->isWorkingDay($today)) {
            return response()->json(['error' => 'Today is not a working day for your designation'], 400);
        }

         $is_holiday = Holiday::whereDate('date',Carbon::now()->toDate())->exists();
        if($is_holiday){
            return response()->json(['error' => 'Today is holiday'], 400);
        }

        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'date' => $today
        ]);

        if ($attendance->check_in) {
            return response()->json(['error' => 'Already checked in today'], 400);
        }

        $checkInTime = Carbon::now('Asia/Karachi');

        // Check if flexible timing is enabled
        if ($designation->has_flexible_timing) {
            $checkInTimeStr = $checkInTime->format('H:i');
            $flexStart = Carbon::parse($designation->flexible_start_from)->format('H:i');
            $flexEnd = Carbon::parse($designation->flexible_start_to)->format('H:i');

            if ($checkInTimeStr < $flexStart || $checkInTimeStr > $flexEnd) {
                return response()->json([
                    'warning' => true,
                    'message' => 'Check-in time is outside your flexible timing range. This will be marked as irregular.'
                ]);
            }
        }

        $attendance->check_in = $checkInTime;
        $attendance->check_in_ip = $request->ip();
        $attendance->check_in_location = $request->location ?? null;
        $attendance->check_in_method = 'web';
        $attendance->status = 'present';
        $attendance->save();

        // Check if late
        $isLate = $designation->isLate($checkInTime);

        $message = 'Check-in successful';
        if ($isLate) {
            $lateMinutes = $designation->calculateLateMinutes($checkInTime);
            $message .= ". You are {$lateMinutes} minutes late.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_late' => $isLate
        ]);
    }

    public function checkOut(Request $request)
    {
        $user = auth()->user();
        $employee = Employee::find($user->employee_id);

        if (!$employee) {
            return response()->json(['error' => 'No employee record found'], 404);
        }

        $designation = $employee->designation;

        if (!$designation || !$designation->shift_end_time) {
            return response()->json(['error' => 'No shift timing assigned for your designation'], 404);
        }

        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json(['error' => 'No check-in record found for today'], 404);
        }

        if ($attendance->check_out) {
            return response()->json(['error' => 'Already checked out today'], 400);
        }

        $checkOutTime = Carbon::now('Asia/Karachi');

        // Check if flexible timing is enabled
        if ($designation->has_flexible_timing) {
            $checkOutTimeStr = $checkOutTime->format('H:i');
            $flexEndFrom = Carbon::parse($designation->flexible_end_from)->format('H:i');
            $flexEndTo = Carbon::parse($designation->flexible_end_to)->format('H:i');

            if ($checkOutTimeStr < $flexEndFrom || $checkOutTimeStr > $flexEndTo) {
                return response()->json([
                    'warning' => true,
                    'message' => 'Check-out time is outside your flexible timing range. This will be marked as irregular.'
                ]);
            }
        }

        $attendance->check_out = $checkOutTime;
        $attendance->check_out_ip = $request->ip();
        $attendance->check_out_location = $request->location ?? null;

        // Calculate total hours
        $totalHours = Carbon::parse($attendance->check_in)->diffInMinutes($attendance->check_out);
        $attendance->total_hours = $totalHours;

        // Calculate overtime
        $overtimeMinutes = $designation->calculateOvertimeMinutes($checkOutTime);
        $attendance->overtime_minutes = $overtimeMinutes;
        $attendance->is_overtime = $overtimeMinutes > 0;

        // Calculate early exit
        $earlyExitMinutes = $designation->calculateEarlyExitMinutes($checkOutTime);
        $attendance->early_exit_minutes = $earlyExitMinutes;
        $attendance->is_early_exit = $earlyExitMinutes > $designation->early_exit_threshold;

        $attendance->save();

        $message = 'Check-out successful';
        if ($overtimeMinutes > 0) {
            $message .= ". You worked " . floor($overtimeMinutes / 60) . "h " . ($overtimeMinutes % 60) . "m overtime.";
        }
        if ($earlyExitMinutes > 0) {
            $message .= ". You left {$earlyExitMinutes} minutes early.";
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function attendance()
    {
        $employee = auth()->user()->employee;

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', Carbon::now('Asia/Karachi')->month)
            ->orderBy('date', 'desc')
            ->paginate(15);

        $summary = [
            'present' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', Carbon::now('Asia/Karachi')->month)
                ->where('status', 'present')
                ->count(),
            'absent' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', Carbon::now('Asia/Karachi')->month)
                ->where('status', 'absent')
                ->count(),
            'late' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', Carbon::now('Asia/Karachi')->month)
                ->where('is_late', true)
                ->count(),
            'leave' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', Carbon::now('Asia/Karachi')->month)
                ->where('status', 'leave')
                ->count()
        ];

        return view('employee.attendance', compact('attendances', 'summary'));
    }

    public function leaves()
    {
        $employee = auth()->user()->employee_id;

        $leaves = Leave::where('employee_id', $employee)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

            $leavetypes = LeaveType::where('is_active', true)->get();
            
            $balances = $leavetypes->map(function ($type) use ($employee) {
                $usedDays = Leave::where('employee_id', $employee)
                    ->where('leave_type_id', $type->id)
                    ->where('status', 'approved')
                    ->sum('total_days');
    
                return (object) [
                    'leave_type_name' => $type->name,
                    'total_allowed'   => $type->days_per_year,
                    'used_days'       => $usedDays,
                    'available_days'  => $type->days_per_year - $usedDays,
                ];
            });

          

        return view('employees.leaves', compact('leaves', 'balances', 'leavetypes'));
    }

    public function applyLeave(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500'
        ]);

        $employee_id = auth()->user()->employee_id;
        $user_id = auth()->user()->id;

        // Check if attendance already marked for any date in range
        $dates = [];
        $currentDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        while ($currentDate <= $endDate) {
            $attendanceExists = Attendance::where('employee_id', $employee_id)
                ->whereDate('date', $currentDate->format('Y-m-d'))
                ->exists();

            if ($attendanceExists) {
                $dates[] = $currentDate->format('d M Y');
            }
            $currentDate->addDay();
        }

        if (!empty($dates)) {
            $dateList = implode(', ', $dates);
            return redirect()->back()->with('error', "Cannot apply for leave. Attendance already marked on: {$dateList}");
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $requestedDays = $startDate->diffInDays($endDate) + 1;

        $leaveType = LeaveType::find($request->leave_type_id);

        if (!$leaveType) {
            return redirect()->back()->with('error', 'Invalid leave type selected');
        }

        $currentYear = Carbon::now('Asia/Karachi')->year;

        // Approved leaves count
        $approvedLeaves = Leave::where('employee_id', $employee_id)
            ->where('leave_type_id', $request->leave_type_id)
            ->where('status', 'approved')
            ->whereYear('start_date', $currentYear)
            ->orWhereYear('end_date', $currentYear)
            ->get()
            ->sum('total_days');

        $pendingLeaves = Leave::where('employee_id', $employee_id)
            ->where('leave_type_id', $request->leave_type_id)
            ->where('status', 'pending')
            ->whereYear('start_date', $currentYear)
            ->orWhereYear('end_date', $currentYear)
            ->get()
            ->sum('total_days');

        $totalUsedLeaves = $approvedLeaves + $pendingLeaves;

        $availableLeaves = $leaveType->days_per_year - $totalUsedLeaves;

        if ($requestedDays > $availableLeaves) {
            return redirect()->back()->with('error', "Only {$availableLeaves} days are available for {$leaveType->name} this year. You requested {$requestedDays} days.");
        }

        $totalDays = $requestedDays;

        try {
            DB::beginTransaction();

            $leave = Leave::create([
                'employee_id' => $employee_id,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_days' => $totalDays,
                'reason' => $request->reason,
                'status' => 'pending',
                'created_by' => $user_id
            ]);

            // Update or create leave balance record
            $balance = \App\Models\LeaveBalance::firstOrNew([
                'employee_id' => $employee_id,
                'leave_type_id' => $request->leave_type_id,
                'year' => $currentYear
            ]);

            if (!$balance->exists) {
                $balance->total_days = $leaveType->days_per_year;
                $balance->used_days = 0;
                $balance->pending_days = 0;
                $balance->remaining_days = $leaveType->days_per_year;
                $balance->carried_forward = 0;
            }

            $balance->pending_days += $totalDays;
            $balance->remaining_days = $balance->total_days - ($balance->used_days + $balance->pending_days);
            $balance->save();

            DB::commit();

            return redirect()->back()->with('success', 'Leave application submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error submitting leave: ' . $e->getMessage());
        }
    }
    public function cancelLeave($id)
    {
        $leave = Leave::where('id', $id)
            ->where('employee_id', auth()->user()->employee_id)
            ->where('status', 'pending')
            ->firstOrFail();

        $leave->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Leave cancelled successfully');
    }
}
