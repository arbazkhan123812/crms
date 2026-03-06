<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $departmentId = $request->get('department_id');

        $query = Attendance::with('employee.department', 'employee.designation')
            ->whereDate('date', $date);

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $attendances = $query->paginate(20);

        // Get employees who are on leave but not yet marked in attendance
        $approvedLeaves = Leave::with('employee')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get();

        foreach ($approvedLeaves as $leave) {
            $attendanceExists = Attendance::where('employee_id', $leave->employee_id)
                ->whereDate('date', $date)
                ->exists();

            if (!$attendanceExists) {
                Attendance::create([
                    'employee_id' => $leave->employee_id,
                    'date' => $date,
                    'status' => 'leave',
                    'remarks' => 'Approved ' . $leave->leaveType->name,
                    'marked_by' => auth()->id()
                ]);
            }
        }

        $stats = [
            'present' => Attendance::whereDate('date', $date)->where('status', 'present')->count(),
            'absent' => Attendance::whereDate('date', $date)->where('status', 'absent')->count(),
            'late' => Attendance::whereDate('date', $date)->where('is_late', true)->count(),
            'wfh' => Attendance::whereDate('date', $date)->where('status', 'wfh')->count(),
            'leave' => Attendance::whereDate('date', $date)->where('status', 'leave')->count()
        ];

        return view('admin.attendance.index', compact('attendances', 'date', 'stats'));
    }

    public function markAttendance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'required',
            'check_out' => 'required',
            'status' => 'required|in:present,absent,half_day,wfh'
        ]);

        $employee = Employee::with('designation')->find($request->employee_id);

        $isattendance_available = Attendance::where('employee_id',$request->employee_id)
                                  ->whereDate('created_at', '=', $request->date)
                                  ->exists();


         if ($isattendance_available) {
            return redirect()->back()->with('error', 'Attendance already marked.');
        }

        $isOnLeave = Leave::where('employee_id', $request->employee_id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $request->date)
            ->whereDate('end_date', '>=', $request->date)
            ->exists();

        if ($isOnLeave) {
            return redirect()->back()->with('error', 'Attendance cannot be marked because the employee is on approved leave.');
        }

        // Get designation shift timings
        $designation = $employee->designation;

        if (!$designation || !$designation->shift_start_time || !$designation->shift_end_time) {
            return redirect()->back()->with('error', 'No shift timing assigned for this employee\'s designation');
        }

        $date = $request->date;
        $checkIn = Carbon::parse($date . ' ' . $request->check_in);
        $checkOut = Carbon::parse($date . ' ' . $request->check_out);
        $totalHours = $checkIn->diffInMinutes($checkOut);

        // Calculate using designation's shift rules
        $lateMinutes = $designation->calculateLateMinutes($checkIn);
        $earlyExitMinutes = $designation->calculateEarlyExitMinutes($checkOut);
        $overtimeMinutes = $designation->calculateOvertimeMinutes($checkOut);

        // Check if working day
        $isWorkingDay = $designation->isWorkingDay($date);

        // Determine final status
        if (!$isWorkingDay) {
            $status = 'weekend';
        } else {
            $status = $request->status;
        }

        // Check if late based on threshold
        $isLate = $designation->isLate($checkIn);

        // Check if early exit based on threshold
        $isEarlyExit = $earlyExitMinutes > $designation->early_exit_threshold;

        // Check if overtime
        $isOvertime = $overtimeMinutes > 0;

        Attendance::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date' => $date
            ],
            [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'check_in_ip' => $request->ip(),
                'total_hours' => $totalHours,
                'late_minutes' => $lateMinutes,
                'early_exit_minutes' => $earlyExitMinutes,
                'overtime_minutes' => $overtimeMinutes,
                'is_late' => $isLate,
                'is_early_exit' => $isEarlyExit,
                'is_overtime' => $isOvertime,
                'status' => $status,
                'marked_by' => auth()->id()
            ]
        );

        return redirect()->back()->with('success', 'Attendance marked successfully');
    }

    public function markBulkAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array'
        ]);

        $count = 0;

        foreach ($request->attendances as $att) {
            if (isset($att['present']) && $att['present']) {
                $employee = Employee::with('designation')->find($att['employee_id']);

                if ($employee && $employee->designation) {
                    $designation = $employee->designation;
                    $isWorkingDay = $designation->isWorkingDay($request->date);

                    $status = $isWorkingDay ? 'present' : 'weekend';

                    Attendance::updateOrCreate(
                        [
                            'employee_id' => $att['employee_id'],
                            'date' => $request->date
                        ],
                        [
                            'status' => $status,
                            'marked_by' => auth()->id()
                        ]
                    );

                    $count++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Attendance marked for {$count} employees"
        ]);
    }

    public function monthlyReport(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        $employees = Employee::with(['designation', 'leave', 'attendance' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('date', 'asc');
        }])->where('status', 'active')->get();

        

        foreach ($employees as $employee) {
            $requiredMinutes = 0;
            $workingDays = 0;

            if ($employee->designation) {
                for ($d = 1; $d <= $startDate->daysInMonth; $d++) {
                    $currentDate = Carbon::createFromDate($year, $month, $d);
                    if ($employee->designation->isWorkingDay($currentDate)) {
                        $requiredMinutes += $employee->designation->working_minutes;
                        $workingDays++;
                    }
                }
            }

            $attendedMinutes = 0;
            $attendedDays = 0;
            $lateDays = 0;
            $overtimeMinutes = 0;
            $leaveDays = 0;

            foreach ($employee->leave as $att) {
                
                    $leaveDays++;
                
            }

            $employee->required_hours = $requiredMinutes;
            $employee->required_days = $workingDays;
            $employee->attended_minutes = $attendedMinutes;
            $employee->attended_days = $attendedDays;
            $employee->late_days = $lateDays;
            $employee->total_overtime = $overtimeMinutes;
            $employee->leave_days = $leaveDays;
        }

        $holidays = Holiday::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        return view('admin.attendance.monthly', compact('employees', 'month', 'year', 'holidays'));
    }

    public function delete_attendance($id){
        $delete = Attendance::where('id',$id)->delete();
        return redirect()->back()->with('success', 'Attendance deleted successfully');
    }
    public function checkIn(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

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

        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'date' => $today
        ]);

        if ($attendance->check_in) {
            return response()->json(['error' => 'Already checked in today'], 400);
        }

        $checkInTime = Carbon::now();

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
        $employee = $user->employee;

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
            return response()->json(['error' => 'No check-in record found'], 404);
        }

        if ($attendance->check_out) {
            return response()->json(['error' => 'Already checked out today'], 400);
        }

        $checkOutTime = Carbon::now();

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
            $message .= ". You worked {$designation->getOvertimeFormatted($overtimeMinutes)} overtime.";
        }
        if ($earlyExitMinutes > 0) {
            $message .= ". You left {$earlyExitMinutes} minutes early.";
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendance,id',
            'check_in' => 'required',
            'check_out' => 'required',
            'status' => 'required|in:present,absent,half_day,wfh,leave,holiday',
            'is_late' => 'nullable|boolean',
            'is_overtime' => 'nullable|boolean',
            'remarks' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $attendance = Attendance::with('employee.designation')->findOrFail($request->attendance_id);

            $date = Carbon::parse($attendance->date)->format('Y-m-d');
            $checkIn = Carbon::parse($date . ' ' . $request->check_in);
            $checkOut = Carbon::parse($date . ' ' . $request->check_out);
            $totalHours = $checkIn->diffInMinutes($checkOut);

            // Get employee's designation shift timings
            $employee = $attendance->employee;
            $designation = $employee->designation;

            $lateMinutes = 0;
            $earlyExitMinutes = 0;
            $overtimeMinutes = 0;
            $isLate = false;
            $isEarlyExit = false;
            $isOvertime = false;

            if ($designation && $request->status == 'present') {
                // Recalculate based on designation rules
                $lateMinutes = $designation->calculateLateMinutes($checkIn);
                $earlyExitMinutes = $designation->calculateEarlyExitMinutes($checkOut);
                $overtimeMinutes = $designation->calculateOvertimeMinutes($checkOut);

                $isLate = $designation->isLate($checkIn);
                $isEarlyExit = $earlyExitMinutes > $designation->early_exit_threshold;
                $isOvertime = $overtimeMinutes > 0;
            }

            $attendance->update([
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'total_hours' => $totalHours,
                'late_minutes' => $lateMinutes,
                'early_exit_minutes' => $earlyExitMinutes,
                'overtime_minutes' => $overtimeMinutes,
                'is_late' => $request->has('is_late') ? true : $isLate,
                'is_early_exit' => $request->has('is_early_exit') ? true : $isEarlyExit,
                'is_overtime' => $request->has('is_overtime') ? true : $isOvertime,
                'status' => $request->status,
                'remarks' => $request->remarks,
                'marked_by' => auth()->id()
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance updated successfully',
                    'attendance' => $attendance
                ]);
            }

            return redirect()->back()->with('success', 'Attendance updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating attendance',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error updating attendance: ' . $e->getMessage());
        }
    }

    public function getEmployeeShift($id)
    {
        $employee = Employee::with('designation')->findOrFail($id);
        $designation = $employee->designation;

        if (!$designation) {
            return response()->json(['error' => 'No designation found'], 404);
        }

        return response()->json([
            'shift_start' => $designation->shift_start_time ? Carbon::parse($designation->shift_start_time)->format('H:i') : null,
            'shift_end' => $designation->shift_end_time ? Carbon::parse($designation->shift_end_time)->format('H:i') : null,
            'late_threshold' => $designation->late_threshold,
            'grace_period' => $designation->grace_period_minutes,
            'is_flexible' => $designation->has_flexible_timing,
            'working_days' => $designation->working_days
        ]);
    }

    public function getTodayShiftStatus(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['error' => 'No employee found'], 404);
        }

        $designation = $employee->designation;

        if (!$designation) {
            return response()->json(['error' => 'No designation found'], 404);
        }

        $today = Carbon::today();
        $isWorkingDay = $designation->isWorkingDay($today);
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $status = [
            'is_working_day' => $isWorkingDay,
            'checked_in' => $attendance && $attendance->check_in ? true : false,
            'checked_out' => $attendance && $attendance->check_out ? true : false,
            'check_in_time' => $attendance && $attendance->check_in ? Carbon::parse($attendance->check_in)->format('h:i A') : null,
            'check_out_time' => $attendance && $attendance->check_out ? Carbon::parse($attendance->check_out)->format('h:i A') : null,
            'shift_start' => $designation->shift_start_time ? Carbon::parse($designation->shift_start_time)->format('h:i A') : null,
            'shift_end' => $designation->shift_end_time ? Carbon::parse($designation->shift_end_time)->format('h:i A') : null,
            'is_late' => $attendance ? $attendance->is_late : false,
            'late_minutes' => $attendance ? $attendance->late_minutes : 0,
            'is_flexible' => $designation->has_flexible_timing
        ];

        return response()->json($status);
    }

    public function destroy($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attendance deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attendance'
            ], 500);
        }
    }
}
