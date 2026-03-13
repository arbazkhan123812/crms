@extends('layout.admin')

@section('content')
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
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title text-dark">
                        <i class="fas fa-clock text-primary mr-2"></i>
                        Daily Attendance
                    </h3>
                    <p class="text-muted mb-0">Track employee attendance for
                        {{ Carbon\Carbon::parse($date)->format('d F, Y') }}
                    </p>
                </div>
                <div class="col-auto">
                    <div class="d-flex">
                        <div class="btn-group mr-2">
                            <a href="{{ route('admin.attendance.index', ['date' => Carbon\Carbon::parse($date)->subDay()->format('Y-m-d')]) }}"
                                class="btn btn-primary btn-sm">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <input type="date" class="form-control form-control-sm" style="width: 150px;"
                                value="{{ $date }}" id="datePicker">
                            <a href="{{ route('admin.attendance.index', ['date' => Carbon\Carbon::parse($date)->addDay()->format('Y-m-d')]) }}"
                                class="btn btn-primary btn-sm">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                        <a href="{{ route('admin.attendance.monthly') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-calendar-alt mr-1"></i> Monthly Report
                        </a>
                        <button class="btn btn-primary btn-sm ml-2" data-toggle="modal" data-target="#markAttendanceModal">
                            <i class="fas fa-plus mr-1"></i> Mark Attendance
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Present</h6>
                                <h3 class="mb-0">{{ $stats['present'] }}</h3>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Absent</h6>
                                <h3 class="mb-0">{{ $stats['absent'] }}</h3>
                            </div>
                            <i class="fas fa-times-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-primary text-white">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Late</h6>
                                <h3 class="mb-0">{{ $stats['late'] }}</h3>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-primary text-white">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">WFH</h6>
                                <h3 class="mb-0">{{ $stats['wfh'] }}</h3>
                            </div>
                            <i class="fas fa-home fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-primary text-white">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">On Leave</h6>
                                <h3 class="mb-0">{{ $stats['leave'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-umbrella-beach fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="myTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Total Hours</th>
                                <th>Status</th>
                                <th>Late</th>
                                <th>Overtime</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $att)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($att->employee->profile_image)
                                                <img src="{{ asset('storage/' . $att->employee->profile_image) }}"
                                                    class="rounded-circle mr-2" width="32" height="32">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2"
                                                    style="width: 32px; height: 32px;">
                                                    {{ substr($att->employee->first_name, 0, 1) }}{{ substr($att->employee->last_name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $att->employee->full_name }}</strong>
                                                <div class="small text-muted">{{ $att->employee->employee_code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $att->employee->department->name ?? 'N/A' }}</td>
                                    <td>{{ $att->check_in ? Carbon\Carbon::parse($att->check_in)->format('h:i A') : '—' }}</td>
                                    <td>{{ $att->check_out ? Carbon\Carbon::parse($att->check_out)->format('h:i A') : '—' }}
                                    </td>
                                    <td><span class="badge badge-light">{{ $att->total_hours_formatted }}</span></td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'present' => 'success',
                                                'absent' => 'danger',
                                                'half_day' => 'warning',
                                                'holiday' => 'info',
                                                'leave' => 'secondary',
                                                'wfh' => 'primary'
                                            ][$att->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $statusClass }}">{{ ucfirst($att->status) }}</span>
                                    </td>
                                    <td>
                                        @if($att->is_late)
                                            <span class="text-danger">{{ $att->late_minutes_formatted }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($att->is_overtime)
                                            <span class="text-success">{{ $att->overtime_formatted }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary " onclick="editAttendance({{ $att }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ url('admin/attendance/delete/' . $att->id) }}"
                                            class="btn btn-sm btn-primary ">
                                            <i class="fa fa-close"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <h5>No Attendance Records</h5>
                                        <p class="text-muted">No attendance found for this date</p>
                                        <button class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#markAttendanceModal">
                                            <i class="fas fa-plus mr-1"></i> Mark Attendance
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="text-muted small mb-0">Showing {{ $attendances->firstItem() ?? 0 }} to
                            {{ $attendances->lastItem() ?? 0 }} of {{ $attendances->total() }} entries
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">{{ $attendances->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Attendance Modal -->
    <div class="modal fade" id="editAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Attendance</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('admin.attendance.update') }}" method="POST" id="editAttendanceForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="attendance_id" id="edit_attendance_id">

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Employee</label>
                            <input type="text" class="form-control form-control-sm bg-light" id="edit_employee_name"
                                readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control form-control-sm" name="date" id="edit_date" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Check In Time</label>
                                    <input type="time" class="form-control form-control-sm" name="check_in"
                                        id="edit_check_in" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Check Out Time</label>
                                    <input type="time" class="form-control form-control-sm" name="check_out"
                                        id="edit_check_out" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control form-control-sm" name="status" id="edit_status" required>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="half_day">Half Day</option>
                                <option value="wfh">Work From Home</option>
                                <option value="leave">On Leave</option>
                                <option value="holiday">Holiday</option>
                            </select>
                        </div>

                        <div class="alert alert-info alert-dismissible fade show" id="autoCalcInfo" style="display: none;">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span id="autoCalcMessage">Late and overtime will be calculated automatically based on
                                designation shift timings.</span>
                        </div>

                        <div class="row" id="calculatedValues" style="display: none;">
                            <div class="col-md-6">
                                <div class="border rounded p-2 mb-2">
                                    <small class="text-muted d-block">Late Minutes</small>
                                    <strong id="displayLateMinutes">0</strong> <span class="text-muted">min</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-2 mb-2">
                                    <small class="text-muted d-block">Overtime Minutes</small>
                                    <strong id="displayOvertimeMinutes">0</strong> <span class="text-muted">min</span>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="calculated_late_minutes" id="calculated_late_minutes">
                        <input type="hidden" name="calculated_overtime_minutes" id="calculated_overtime_minutes">

                        <div class="form-group mb-3" id="edit_remarks_field" style="display: none;">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control form-control-sm" name="remarks" id="edit_remarks" rows="2"
                                placeholder="Reason for half day/leave..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Update Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Mark Attendance Modal -->
    <div class="modal fade" id="markAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title"><i class="fas fa-pen mr-2"></i>Mark Attendance</h5>
                    <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('admin.attendance.mark') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Employee</label>
                            <select class="form-control form-control-sm" name="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach(App\Models\Employee::where('status', 'active')->get() as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control form-control-sm" name="date" value="{{ $date }}"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Check In</label>
                                    <input type="time" class="form-control form-control-sm" name="check_in" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Check Out</label>
                                    <input type="time" class="form-control form-control-sm" name="check_out" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control form-control-sm" name="status" required>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="half_day">Half Day</option>
                                <option value="wfh">Work From Home</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .opacity-50 {
            opacity: 0.5;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.7);
        }
    </style>

    <script>
        $('#datePicker').change(function () {
            window.location.href = '{{ route("admin.attendance.index") }}?date=' + $(this).val();
        });

        $('#departmentFilter').change(function () {
            let url = new URL(window.location.href);
            url.searchParams.set('department_id', $(this).val());
            window.location.href = url.toString();
        });

        // Show/hide remarks field based on status
        $('#edit_status').change(function () {
            if ($(this).val() == 'half_day' || $(this).val() == 'leave') {
                $('#edit_remarks_field').show();
            } else {
                $('#edit_remarks_field').hide();
            }
        });

        // Edit attendance function
        function editAttendance(att) {
            // Parse the attendance object
            let attendance = typeof att === 'string' ? JSON.parse(att) : att;

            // Set form values
            $('#edit_attendance_id').val(attendance.id);
            $('#edit_employee_name').val(attendance.employee.full_name + ' (' + attendance.employee.employee_code + ')');
            $('#edit_date').val(attendance.date);

            // Format time values
            if (attendance.check_in) {
                let checkIn = new Date(attendance.check_in);
                let hours = checkIn.getHours().toString().padStart(2, '0');
                let minutes = checkIn.getMinutes().toString().padStart(2, '0');
                $('#edit_check_in').val(hours + ':' + minutes);
            }

            if (attendance.check_out) {
                let checkOut = new Date(attendance.check_out);
                let hours = checkOut.getHours().toString().padStart(2, '0');
                let minutes = checkOut.getMinutes().toString().padStart(2, '0');
                $('#edit_check_out').val(hours + ':' + minutes);
            }

            // Set status
            $('#edit_status').val(attendance.status);

            // Hide/show auto-calc info based on status
            if (attendance.status === 'present' || attendance.status === 'wfh') {
                $('#autoCalcInfo').show();

                // Trigger calculation based on current times
                calculateLateAndOvertime(attendance.employee.designation);
            } else {
                $('#autoCalcInfo').hide();
                $('#calculatedValues').hide();
            }

            // Set remarks if any
            if (attendance.remarks) {
                $('#edit_remarks').val(attendance.remarks);
            } else {
                $('#edit_remarks').val('');
            }

            // Show/hide remarks field based on status
            if (attendance.status === 'half_day' || attendance.status === 'leave') {
                $('#edit_remarks_field').show();
            } else {
                $('#edit_remarks_field').hide();
            }

            // Open modal
            $('#editAttendanceModal').modal('show');
        }

        // Function to calculate late and overtime based on designation
        function calculateLateAndOvertime(designation) {
            let checkIn = $('#edit_check_in').val();
            let checkOut = $('#edit_check_out').val();
            let date = $('#edit_date').val();

            if (!checkIn || !checkOut || !date) return;


            $('#autoCalcMessage').html('Late and overtime will be calculated automatically based on designation shift timings when you save.');
        }

        $('#edit_check_in, #edit_check_out').on('change', function () {
            if ($('#edit_status').val() === 'present' || $('#edit_status').val() === 'wfh') {
                calculateLateAndOvertime();
            }
        });

        $('#edit_status').on('change', function () {
            if ($(this).val() === 'present' || $(this).val() === 'wfh') {
                $('#autoCalcInfo').show();
                calculateLateAndOvertime();
            } else {
                $('#autoCalcInfo').hide();
                $('#calculatedValues').hide();
            }

            if ($(this).val() === 'half_day' || $(this).val() === 'leave') {
                $('#edit_remarks_field').show();
            } else {
                $('#edit_remarks_field').hide();
            }
        });

        $('#editAttendanceForm').submit(function (e) {
            e.preventDefault();

            let formData = $(this).serialize();
            let submitBtn = $(this).find('button[type="submit"]');

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...');

            $.ajax({
                url: '{{ route("admin.attendance.update") }}',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    $('#editAttendanceModal').modal('hide');
                    toastr.success('Attendance updated successfully');
                    setTimeout(() => location.reload(), 1500);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html('Update Attendance');

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        $.each(errors, function (key, value) {
                            errorMsg += value[0] + '<br>';
                        });
                        toastr.error(errorMsg);
                    } else {
                        toastr.error('Error updating attendance');
                    }
                }
            });
        });
    </script>

@endsection