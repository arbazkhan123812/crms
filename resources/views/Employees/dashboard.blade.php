@extends('layout.employee')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-tachometer-alt text-primary mr-2"></i>
                    Employee Dashboard
                </h3>
                <p class="text-muted mb-0">Welcome back, {{ $employee->full_name }}</p>
            </div>
            <div class="col-auto">
                <span class="badge badge-primary p-2">{{ now()->format('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Present This Month</h6>
                            <h3 class="mb-0">{{ $presentCount }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Absent</h6>
                            <h3 class="mb-0">{{ $absentCount }}</h3>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Late Arrivals</h6>
                            <h3 class="mb-0">{{ $lateCount }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Leaves Taken</h6>
                            <h3 class="mb-0">{{ $leaveCount }}</h3>
                        </div>
                        <i class="fas fa-umbrella-beach fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0"><i class="fas fa-clock mr-2"></i>Today's Attendance</h6>
                </div>
                <div class="card-body">
                    @if($todayAttendance)
                        <div class="row">
                            <div class="col-6">
                                <div class="border rounded p-3 text-center">
                                    <small class="text-muted d-block">Check In</small>
                                    <strong>{{ $todayAttendance->check_in ? Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') : '—' }}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3 text-center">
                                    <small class="text-muted d-block">Check Out</small>
                                    <strong>{{ $todayAttendance->check_out ? Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') : '—' }}</strong>
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle mr-2"></i> Status: 
                                    <span class="badge badge-{{ $todayAttendance->status == 'present' ? 'success' : 'warning' }}">
                                        {{ ucfirst($todayAttendance->status) }}
                                    </span>
                                    @if($todayAttendance->is_late)
                                        <span class="badge badge-danger ml-2">Late by {{ $todayAttendance->late_minutes }} min</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No attendance marked for today</p>
                            <button class="btn btn-primary btn-sm" onclick="checkIn()">
                                <i class="fas fa-sign-in-alt mr-1"></i> Check In
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-umbrella-beach mr-2"></i>Leave Balances</h6>
                    <a href="{{ route('employee.leaves') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($balances ?? [] as $balance)
                        <div class="col-6 mb-3">
                            <div class="border rounded p-2">
                                <div class="d-flex justify-content-between">
                                    <span>{{ $balance->leaveType->name }}</span>
                                    <span class="badge badge-primary">{{ $balance->remaining_days }}/{{ $balance->total_days }}</span>
                                </div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-success" style="width: {{ $balance->utilization_percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        

            <div class="card">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-history mr-2"></i>Recent Leaves</h6>
                    <a href="{{ route('employee.leaves') }}" class="btn btn-sm btn-link">Apply Leave</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($leaves ?? [] as $leave)
                    <div class="list-group-item px-3 py-2">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $leave->leaveType->name }}</strong>
                                <p class="small text-muted mb-0">{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M') }}</p>
                            </div>
                            <span class="badge badge-{{ $leave->status_badge }}">{{ ucfirst($leave->status) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        No leave applications
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.opacity-50 { opacity: 0.5; }
.text-white-50 { color: rgba(255,255,255,0.7); }
</style>

<script>
function checkIn() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            $.ajax({
                url: '{{ route("attendance.checkin") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    location: position.coords.latitude + ',' + position.coords.longitude
                },
                success: function(response) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.error || 'Error checking in');
                }
            });
        });
    } else {
        $.ajax({
            url: '{{ route("attendance.checkin") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                toastr.success(response.message);
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.error || 'Error checking in');
            }
        });
    }
}
</script>
@endsection