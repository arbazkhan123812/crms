<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $payrolls = Payroll::with(['employee', 'processedBy'])
            ->where('month', $month)
            ->where('year', $year)
            ->paginate(15);

        $stats = [
            'total_employees' => Payroll::where('month', $month)->where('year', $year)->count(),
            'processed' => Payroll::where('month', $month)->where('year', $year)->where('status', 'processed')->count(),
            'paid' => Payroll::where('month', $month)->where('year', $year)->where('status', 'paid')->count(),
            'total_salary' => Payroll::where('month', $month)->where('year', $year)->sum('net_salary')
        ];

        return view('admin.payroll.payrolls.index', compact('payrolls', 'stats', 'month', 'year'));
    }

    public function create()
    {
        $month = request('month', Carbon::now()->month);
        $year = request('year', Carbon::now()->year);

        // Get employees with active salary
        $employees = Employee::where('status', 'active')
            ->whereHas('salary')
            ->get();

        return view('admin.payroll.payrolls.create', compact('employees', 'month', 'year'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        DB::beginTransaction();

        try {
            $processed = 0;
            $month = $request->month;
            $year = $request->year;

            $startDate = Carbon::createFromDate($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();

            foreach ($request->employee_ids as $employeeId) {
                // Check if payroll already exists
                $exists = Payroll::where('employee_id', $employeeId)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $employee = Employee::with('salary')->find($employeeId);
                $salary = $employee->salary;

                if (!$salary) {
                    continue;
                }

                // Calculate attendance
                $workingDays = $this->calculateWorkingDays($month, $year);
                $attendance = Attendance::where('employee_id', $employeeId)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->get();

                $presentDays = $attendance->whereIn('status', ['present', 'wfh'])->count();
                $absentDays = $attendance->where('status', 'absent')->count();
                $leaveDays = $attendance->where('status', 'leave')->count();

                // Calculate salary based on present days
                $perDaySalary = $salary->basic / $workingDays;
                $basicPaid = $perDaySalary * $presentDays;

                // Calculate pro-rata for other components
                $componentRatio = $presentDays / $workingDays;

                $payrollData = [
                    'employee_id' => $employeeId,
                    'month' => $month,
                    'year' => $year,
                    'basic' => $basicPaid,
                    'hra' => $salary->hra ? $salary->hra * $componentRatio : null,
                    'da' => $salary->da ? $salary->da * $componentRatio : null,
                    'conveyance' => $salary->conveyance ? $salary->conveyance * $componentRatio : null,
                    'medical' => $salary->medical ? $salary->medical * $componentRatio : null,
                    'special' => $salary->special ? $salary->special * $componentRatio : null,
                    'pf' => $salary->pf,
                    'esi' => $salary->esi,
                    'pt' => $salary->pt,
                    'tds' => $salary->tds,
                    'other_earnings' => $salary->other_earnings,
                    'other_deductions' => $salary->other_deductions,
                    'working_days' => $workingDays,
                    'present_days' => $presentDays,
                    'absent_days' => $absentDays,
                    'leave_days' => $leaveDays,
                    'gross_salary' => $basicPaid + ($salary->hra ? $salary->hra * $componentRatio : 0) + 
                                     ($salary->da ? $salary->da * $componentRatio : 0) + 
                                     ($salary->conveyance ? $salary->conveyance * $componentRatio : 0) + 
                                     ($salary->medical ? $salary->medical * $componentRatio : 0) + 
                                     ($salary->special ? $salary->special * $componentRatio : 0),
                    'total_deductions' => $salary->pf + $salary->esi + $salary->pt + $salary->tds,
                    'status' => 'draft',
                    'processed_by' => auth()->id(),
                    'processed_date' => now()
                ];

                // Add other earnings if any
                if ($salary->other_earnings) {
                    foreach ($salary->other_earnings as $earning) {
                        $payrollData['gross_salary'] += $earning['amount'] * $componentRatio;
                    }
                }

                $payrollData['net_salary'] = $payrollData['gross_salary'] - $payrollData['total_deductions'];

                Payroll::create($payrollData);
                $processed++;
            }

            DB::commit();

            return redirect()->route('admin.payroll.payrolls.index', ['month' => $month, 'year' => $year])
                ->with('success', "Payroll processed for {$processed} employees");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing payroll: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $payroll = Payroll::with(['employee', 'processedBy'])->findOrFail($id);
        return view('admin.payroll.payrolls.show', compact('payroll'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,processed,paid,cancelled',
            'payment_date' => 'required_if:status,paid|nullable|date',
            'payment_mode' => 'required_if:status,paid|nullable|string',
            'remarks' => 'nullable|string'
        ]);

        $payroll = Payroll::findOrFail($id);
        
        $updateData = ['status' => $request->status];
        
        if ($request->status == 'paid') {
            $updateData['payment_date'] = $request->payment_date;
            $updateData['payment_mode'] = $request->payment_mode;
        }
        
        if ($request->remarks) {
            $updateData['remarks'] = $request->remarks;
        }

        $payroll->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Payroll status updated successfully'
        ]);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:payrolls,id',
            'status' => 'required|in:draft,processed,paid,cancelled'
        ]);

        Payroll::whereIn('id', $request->ids)->update([
            'status' => $request->status,
            'processed_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payroll status updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);
        
        if ($payroll->status == 'paid') {
            return back()->with('error', 'Cannot delete paid payroll');
        }

        $payroll->delete();

        return redirect()->route('admin.payroll.payrolls.index')
            ->with('success', 'Payroll deleted successfully');
    }

    public function generatePayslip($id)
    {
        $payroll = Payroll::with(['employee.department', 'employee.designation'])->findOrFail($id);
        
        // You can use a PDF package like barryvdh/laravel-dompdf
        // $pdf = PDF::loadView('admin.payroll.payslip', compact('payroll'));
        // return $pdf->download('payslip-'.$payroll->employee->employee_code.'-'.$payroll->month.'-'.$payroll->year.'.pdf');
        
        // For now, return HTML view
        return view('admin.payroll.payrolls.payslip', compact('payroll'));
    }

    private function calculateWorkingDays($month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $workingDays = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            if (!$currentDate->isWeekend()) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        return $workingDays;
    }

    public function report(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $payrolls = Payroll::where('month', $month)
                ->where('year', $year)
                ->where('status', 'paid')
                ->get();

            $monthlyData[] = [
                'month' => Carbon::create()->month($month)->format('F'),
                'employees' => $payrolls->count(),
                'total_salary' => $payrolls->sum('net_salary')
            ];
        }

        $departmentWise = Payroll::with('employee.department')
            ->where('year', $year)
            ->where('status', 'paid')
            ->get()
            ->groupBy('employee.department.name')
            ->map(function ($payrolls) {
                return [
                    'count' => $payrolls->count(),
                    'total' => $payrolls->sum('net_salary')
                ];
            });

        return view('admin.payroll.reports.index', compact('monthlyData', 'departmentWise', 'year'));
    }
}