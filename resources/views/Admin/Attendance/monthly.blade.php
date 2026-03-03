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
                    <select class="form-control form-control-sm mr-2" style="width: 120px;" id="monthSelect">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select class="form-control form-control-sm mr-2" style="width: 100px;" id="yearSelect">
                        @foreach(range($year-2, $year+2) as $y)
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

    <div class="card">
        <div class="card-header bg-light py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="mb-0"><i class="fas fa-users mr-2"></i>Employee Attendance Summary</h6>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm" onclick="exportReport()">
                        <i class="fas fa-file-excel mr-1"></i> Export
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="printReport()">
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
                            @for($d = 1; $d <= Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth; $d++)
                                <th class="text-center" style="min-width: 35px; padding: 8px 2px;">{{ $d }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td>
                                <strong>{{ $employee->full_name }}</strong>
                                <div class="small text-muted">{{ $employee->employee_code }}</div>
                            </td>
                            
                            @for($d = 1; $d <= Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth; $d++)
                                @php
                                    $date = Carbon\Carbon::createFromDate($year, $month, $d);
                                    $att = $employee->attendance->firstWhere('date', $date->format('Y-m-d'));
                                    
                                    $holiday = $holidays->firstWhere('date', $date->format('Y-m-d'));
                                    $isWeekend = $date->isWeekend();
                                    
                                    if ($holiday) {
                                        $status = 'H';
                                        $tooltip = $holiday->name;
                                        $class = 'bg-info text-white';
                                    } elseif ($isWeekend) {
                                        $status = 'W';
                                        $tooltip = 'Weekend';
                                        $class = 'bg-secondary text-white';
                                    } elseif ($att) {
                                        $status = $att->status == 'present' ? 'P' : ($att->status == 'absent' ? 'A' : 'L');
                                        $tooltip = $att->status . ' - In: ' . ($att->check_in ? Carbon::parse($att->check_in)->format('h:i A') : '—');
                                        $class = $att->status == 'present' ? 'bg-success text-white' : ($att->status == 'absent' ? 'bg-danger text-white' : 'bg-warning');
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
                                $present = $employee->attendance->where('status', 'present')->count();
                                $absent = $employee->attendance->where('status', 'absent')->count();
                                $late = $employee->attendance->where('is_late', true)->count();
                                $ot = $employee->attendance->sum('overtime_minutes');
                                $otFormatted = floor($ot/60) . 'h ' . ($ot%60) . 'm';
                            @endphp
                            <td class="text-center font-weight-bold">{{ $present }}</td>
                            <td class="text-center font-weight-bold">{{ $absent }}</td>
                            <td class="text-center font-weight-bold">{{ $late }}</td>
                            <td class="text-center font-weight-bold">{{ $otFormatted }}</td>
                        </tr>
                        @endforeach
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
.bg-warning { background-color: #ffc107 !important; }
.bg-info.text-white { background-color: #17a2b8 !important; color: white !important; }
.bg-secondary.text-white { background-color: #6c757d !important; color: white !important; }
</style>

<script>
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