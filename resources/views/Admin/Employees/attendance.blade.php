@extends('layout.employee')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-clock text-primary mr-2"></i>
                    My Attendance
                </h3>
                <p class="text-muted mb-0">{{ now()->format('F Y') }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <h6 class="text-white-50">Present</h6>
                    <h3 class="mb-0">{{ $summary['present'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-3">
                    <h6 class="text-white-50">Absent</h6>
                    <h3 class="mb-0">{{ $summary['absent'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body py-3">
                    <h6 class="text-white-50">Late</h6>
                    <h3 class="mb-0">{{ $summary['late'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-3">
                    <h6 class="text-white-50">On Leave</h6>
                    <h3 class="mb-0">{{ $summary['leave'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Total Hours</th>
                            <th>Status</th>
                            <th>Late</th>
                            <th>Overtime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $att)
                        <tr>
                            <td>{{ $att->date->format('d M Y') }}</td>
                            <td>{{ $att->date->format('l') }}</td>
                            <td>{{ $att->check_in ? Carbon\Carbon::parse($att->check_in)->format('h:i A') : '—' }}</td>
                            <td>{{ $att->check_out ? Carbon\Carbon::parse($att->check_out)->format('h:i A') : '—' }}</td>
                            <td>{{ $att->total_hours_formatted }}</td>
                            <td>
                                @php
                                    $statusClass = [
                                        'present' => 'success',
                                        'absent' => 'danger',
                                        'half_day' => 'warning',
                                        'leave' => 'info',
                                        'wfh' => 'primary'
                                    ][$att->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">{{ ucfirst($att->status) }}</span>
                            </td>
                            <td>
                                @if($att->is_late)
                                    <span class="text-danger">{{ $att->late_minutes }} min</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($att->is_overtime)
                                    <span class="text-success">{{ $att->overtime_minutes }} min</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h5>No Attendance Records</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection