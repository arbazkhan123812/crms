@extends('layout.employee')

@section('content')
    <style>
        <style>.content {
            padding: 20px;
            background: #f8f9fa;
        }

        .page-header {
            padding-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }

        
        .card-statistics {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .card-statistics:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .icon-bg-primary {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4e73df, #224abe);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .icon-bg-danger {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #dc3545, #bd2130);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .icon-bg-warning {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ffc107, #d39e00);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .icon-bg-info {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #17a2b8, #117a8b);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        
        .btn-primary {
            background: linear-gradient(135deg, #4e73df, #224abe);
            border: none;
            border-radius: 4px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #224abe, #4e73df);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #bd2130);
            border: none;
            border-radius: 4px;
            font-weight: 500;
        }

        .btn-primary {
            border: 1px solid #4e73df;
            color: #4e73df;
            border-radius: 4px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4e73df, #224abe);
            border-color: transparent;
            color: white;
        }

        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background-color: #f8f9fa;
        }

        
        .badge.bg-primary {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: white;
        }

        
        .progress {
            background-color: #e9ecef;
            border-radius: 4px;
        }

        .progress-bar {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            border-radius: 4px;
        }

        
        .list-group-item {
            border-left: none;
            border-right: none;
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        
        .form-control-sm {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 13px;
            padding: 6px 12px;
        }

        
        .table thead th {
            border-top: none;
            font-weight: 500;
            color: #6c757d;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        
        .border {
            border: 1px solid #e9ecef !important;
            border-radius: 4px;
        }

        
        .font-weight-medium {
            font-weight: 500;
        }

        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
    </style>
    <div class="content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- PAGE HEADER -->
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
                    <span class="badge bg-primary text-white px-3 py-2">
                        <i class="fas fa-calendar-alt mr-1"></i>{{ now()->format('l, d F Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- STATISTICS CARDS -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="card card-statistics h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-bg-primary rounded-circle mr-3">
                                <i class="fas fa-check-circle fa-2x text-white"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1">Present This Month</p>
                                <h4 class="card-title mb-0">{{ $presentCount }}</h4>
                                <span
                                    class="text-muted small">{{ round(($presentCount / max($presentCount + $absentCount, 1)) * 100) }}%
                                    attendance</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="card card-statistics h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-bg-danger rounded-circle mr-3">
                                <i class="fas fa-times-circle fa-2x text-white"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1">Absent</p>
                                <h4 class="card-title mb-0">{{ $absentCount }}</h4>
                                <span class="text-muted small">This month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="card card-statistics h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-bg-warning rounded-circle mr-3">
                                <i class="fas fa-clock fa-2x text-white"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1">Late Arrivals</p>
                                <h4 class="card-title mb-0">{{ $lateCount }}</h4>
                                <span
                                    class="text-muted small">{{ $lateCount > 0 ? 'Needs improvement' : 'Perfect punctuality' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="card card-statistics h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-bg-info rounded-circle mr-3">
                                <i class="fas fa-umbrella-beach fa-2x text-white"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1">Leaves Taken</p>
                                <h4 class="card-title mb-0">{{ $leaveCount }}</h4>
                                <span class="text-muted small">This month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- LEFT COLUMN -->
            <div class="col-lg-6">
                <!-- TODAY'S ATTENDANCE CARD -->
                <div class="card mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0" style="font-weight: 500;">
                            <i class="fas fa-clock text-primary mr-2"></i>
                            Today's Attendance
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($todayAttendance)
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="border rounded p-3 text-center">
                                        <small class="text-muted d-block mb-1">Check In</small>
                                        <h5 class="mb-0 font-weight-normal">
                                            {{ $todayAttendance->check_in ? Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') : '—' }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3 text-center">
                                        <small class="text-muted d-block mb-1">Check Out</small>
                                        <h5 class="mb-0 font-weight-normal">
                                            {{ $todayAttendance->check_out ? Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') : '—' }}
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div>
                                    <span
                                        class="badge badge-{{ $todayAttendance->status == 'present' ? 'success' : 'warning' }} px-3 py-1">
                                        <i
                                            class="fas fa-{{ $todayAttendance->status == 'present' ? 'check-circle' : 'exclamation-circle' }} mr-1"></i>
                                        {{ ucfirst($todayAttendance->status) }}
                                    </span>
                                    @if($todayAttendance->is_late)
                                        <span class="badge badge-danger ml-2 px-3 py-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Late by
                                            {{ $todayAttendance->late_minutes }} min
                                        </span>
                                    @endif
                                </div>

                                @if($todayAttendance->check_in && !$todayAttendance->check_out)
                                    <button class="btn btn-danger px-4" onclick="checkOut()">
                                        <i class="fas fa-sign-out-alt mr-1"></i> Check Out
                                    </button>
                                @endif
                            </div>

                            @if($todayAttendance->check_in && $todayAttendance->check_out)
                                <div class="alert alert-success bg-light border-0 text-center py-2 mb-0">
                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                    <span>You have successfully completed your day. Checked out at
                                        <strong>{{ Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}</strong></span>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-clock fa-3x text-light"></i>
                                </div>
                                <h5 class="text-muted mb-2">No Attendance Marked</h5>
                                <p class="text-muted small mb-4">You haven't checked in today</p>
                                <button class="btn btn-primary px-5" onclick="checkIn()">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Check In Now
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- LEAVE BALANCES CARD -->
                <div class="card mb-4">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0" style="font-weight: 500;">
                            <i class="fas fa-umbrella-beach text-primary mr-2"></i>
                            Leave Balances
                        </h6>
                        <a href="{{ route('employee.leaves') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($balances ?? [] as $balance)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="font-weight-medium">{{ $balance->leaveType->name }}</span>
                                            <span
                                                class="badge bg-primary text-white">{{ $balance->remaining_days }}/{{ $balance->total_days }}</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success"
                                                style="width: {{ $balance->utilization_percentage }}%"></div>
                                        </div>
                                        <small class="text-muted d-block mt-1">{{ $balance->utilization_percentage }}%
                                            utilized</small>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4">
                                    <p class="text-muted mb-0">No leave balances found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-6">
                <!-- RECENT LEAVES CARD -->
                <div class="card mb-4">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0" style="font-weight: 500;">
                            <i class="fas fa-history text-primary mr-2"></i>
                            Recent Leaves
                        </h6>
                        <a href="{{ route('employee.leaves') }}" class="btn btn-sm btn-primary">Apply Leave</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($leaves ?? [] as $leave)
                                                <div class="list-group-item px-4 py-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1 font-weight-medium">{{ $leave->leaveType->name }}</h6>
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                                {{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M') }}
                                                            </small>
                                                        </div>
                                                        <span class="badge badge-{{ 
                                                                $leave->status == 'approved' ? 'success' :
                                ($leave->status == 'pending' ? 'warning' : 'secondary') 
                                                            }} px-3 py-1">
                                                            {{ ucfirst($leave->status) }}
                                                        </span>
                                                    </div>
                                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="fas fa-umbrella-beach fa-3x text-light mb-3"></i>
                                    <h5 class="text-muted">No Leave Applications</h5>
                                    <p class="text-muted small">You haven't applied for any leave yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            
            $('[data-toggle="tooltip"]').tooltip();
        });

        function checkIn() {
            if (!confirm('Are you ready to check in?')) return;

            function doCheckIn(location) {
                $.ajax({
                    url: '{{ route("employee.attendance.checkin") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        location: location,
                    },
                    beforeSend: function () {
                        toastr.info('Processing check-in...');
                    },
                    success: function (response) {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON?.error || 'Error checking in');
                    }
                });
            }

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => doCheckIn(position.coords.latitude + ',' + position.coords.longitude),
                    () => doCheckIn('')
                );
            } else {
                doCheckIn('');
            }
        }

        function checkOut() {
            if (!confirm('Are you sure you want to check out?')) return;

            function doCheckOut(location) {
                $.ajax({
                    url: '{{ route("employee.attendance.checkout") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        location: location
                    },
                    beforeSend: function () {
                        toastr.info('Processing check-out...');
                    },
                    success: function (response) {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 1500);
                        location.reload();
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON?.error || 'Error checking out');
                    }
                });
            }

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => doCheckOut(position.coords.latitude + ',' + position.coords.longitude),
                    () => doCheckOut('')
                );
            } else {
                doCheckOut('');
            }
        }
    </script>

@endsection