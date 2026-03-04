@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-calendar-alt text-primary mr-2"></i>
                    Monthly Attendance Report
                </h3>
                <p class="text-muted mb-0">{{ Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</p>
            </div>
            <div class="col-auto">
                <div class="d-flex">
                    <select class="form-control select2 form-control-sm mr-2" style="width: 120px;" id="monthSelect">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                            </option>   
                        @endforeach
                    </select>
                    <select class="form-control select2 form-control-sm mr-2" style="width: 100px;" id="yearSelect">
                       @foreach(range(date('Y') - 10, date('Y') + 5) as $y)
    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
@endforeach
                    </select>
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Daily
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($employees->isEmpty())
    <div class="alert alert-info">
        <i class="fas fa-info-circle mr-2"></i> No employees found.
    </div>
    @endif

    <div class="card">
        <div class="card-header bg-light py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="mb-0"><i class="fas fa-users mr-2"></i>Employee Attendance Summary</h6>
                </div>
                <div class="col-auto">
                    <button class="btn btn-success btn-sm" onclick="exportReport()">
                        <i class="fas fa-file-excel mr-1"></i> Export
                    </button>
                    <button class="btn btn-info btn-sm" onclick="printReport()">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="attendanceTable">
                    <thead class="thead-light">
                        <tr>
                            <th rowspan="2" class="align-middle text-center" style="min-width: 200px;">Employee</th>
                            <th colspan="{{ Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth }}" class="text-center">Days of Month</th>
                            <th rowspan="2" class="align-middle text-center" style="min-width: 80px;">Present</th>
                            <th rowspan="2" class="align-middle text-center" style="min-width: 80px;">Absent</th>
                            <th rowspan="2" class="align-middle text-center" style="min-width: 80px;">Late</th>
                            <th rowspan="2" class="align-middle text-center" style="min-width: 80px;">OT</th>
                        </tr>
                        <tr>
                            @php $daysInMonth = Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth; @endphp
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                <th class="text-center" style="min-width: 35px; padding: 8px 2px;">{{ $d }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td>
                                <strong>{{ $employee->full_name }}</strong>
                                <div class="small text-muted">{{ $employee->employee_code }}</div>
                                <div class="small text-info">{{ $employee->designation->title ?? 'No Designation' }}</div>
                            </td>
                            
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $date = Carbon\Carbon::createFromDate($year, $month, $d)->format('Y-m-d');
                                    
                                    // Find attendance for this date
                                    $att = null;
                                    foreach($employee->attendance as $a) {
                                        // Handle both string and Carbon instances
                                        $attDate = $a->date instanceof Carbon\Carbon 
                                            ? $a->date->format('Y-m-d') 
                                            : (is_string($a->date) ? $a->date : date('Y-m-d', strtotime($a->date)));
                                        
                                        if ($attDate == $date) {
                                            $att = $a;
                                            break;
                                        }
                                    }
                                    
                                    $holiday = $holidays->firstWhere('date', $date);
                                    $isWeekend = Carbon\Carbon::parse($date)->isWeekend();
                                    
                                    if ($holiday) {
                                        $status = 'H';
                                        $tooltip = $holiday->name;
                                        $class = 'bg-info text-white';
                                    } elseif ($isWeekend) {
                                        $status = 'W';
                                        $tooltip = 'Weekend';
                                        $class = 'bg-secondary text-white';
                                    } elseif ($att) {
                                        switch($att->status) {
                                            case 'present':
                                                $status = 'P';
                                                $class = 'bg-success text-white';
                                                $tooltip = 'Present';
                                                break;
                                            case 'wfh':
                                                $status = 'WFH';
                                                $class = 'bg-primary text-white';
                                                $tooltip = 'Work From Home';
                                                break;
                                            case 'absent':
                                                $status = 'A';
                                                $class = 'bg-danger text-white';
                                                $tooltip = 'Absent';
                                                break;
                                            case 'half_day':
                                                $status = 'HD';
                                                $class = 'bg-warning';
                                                $tooltip = 'Half Day';
                                                break;
                                            case 'leave':
                                                $status = 'L';
                                                $class = 'bg-secondary text-white';
                                                $tooltip = 'On Leave';
                                                break;
                                            default:
                                                $status = '?';
                                                $class = '';
                                                $tooltip = 'Unknown';
                                        }
                                        
                                        if ($att->check_in) {
                                            $checkInTime = $att->check_in instanceof Carbon\Carbon 
                                                ? $att->check_in->format('h:i A')
                                                : date('h:i A', strtotime($att->check_in));
                                            $tooltip .= ' - In: ' . $checkInTime;
                                        }
                                    } else {
                                        $status = '—';
                                        $tooltip = 'No record';
                                        $class = '';
                                    }
                                @endphp
                                <td class="text-center {{ $class }}" style="padding: 8px 2px;" title="{{ $tooltip }}">
                                    {{ $status }}
                                </td>
                            @endfor
                            
                            @php
                                $present = 0;
                                $absent = 0;
                                $late = 0;
                                $ot = 0;
                                
                                foreach($employee->attendance as $att) {
                                    if (in_array($att->status, ['present', 'wfh'])) {
                                        $present++;
                                        if ($att->is_late) $late++;
                                    } elseif ($att->status == 'absent') {
                                        $absent++;
                                    }
                                    $ot += $att->overtime_minutes;
                                }
                                
                                $otFormatted = floor($ot/60) . 'h ' . ($ot%60) . 'm';
                                if ($ot == 0) $otFormatted = '0h';
                            @endphp
                            <td class="text-center font-weight-bold">{{ $present }}</td>
                            <td class="text-center font-weight-bold">{{ $absent }}</td>
                            <td class="text-center font-weight-bold">{{ $late }}</td>
                            <td class="text-center font-weight-bold">{{ $otFormatted }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ 6 + $daysInMonth }}" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5>No Employees Found</h5>
                                <p class="text-muted">There are no active employees in the system</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.table td, .table th { white-space: nowrap; }
.bg-success.text-white { background-color: #28a745 !important; color: white !important; }
.bg-danger.text-white { background-color: #dc3545 !important; color: white !important; }
.bg-warning { background-color: #ffc107 !important; color: #212529 !important; }
.bg-info.text-white { background-color: #17a2b8 !important; color: white !important; }
.bg-primary.text-white { background-color: #007bff !important; color: white !important; }
.bg-secondary.text-white { background-color: #6c757d !important; color: white !important; }
</style>

<script>
$(document).ready(function() {
    console.log('Monthly report loaded');
});

$('#monthSelect, #yearSelect').change(function() {
    let month = $('#monthSelect').val();
    let year = $('#yearSelect').val();
    window.location.href = '{{ route("admin.attendance.monthly") }}?month=' + month + '&year=' + year;
});

function exportReport() {
    let month = $('#monthSelect').val();
    let year = $('#yearSelect').val();
    window.location.href = '{{ route("admin.attendance.export") }}?month=' + month + '&year=' + year;
}

function printReport() {
    window.print();
}
</script>
@endsection