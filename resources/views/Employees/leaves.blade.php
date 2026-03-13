@extends('layout.employee')

@section('content')
    <div class="content">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title text-dark">
                        <i class="fas fa-umbrella-beach text-primary mr-2"></i>
                        My Leaves
                    </h3>
                    <p class="text-muted mb-0">Apply and track your leave requests</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#applyLeaveModal">
                        <i class="fas fa-plus mr-1"></i> Apply Leave
                    </button>
                    <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary btn-sm ml-2">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="row mb-4">
            @foreach($balances as $balance)
                <div class="col-md-3 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="font-weight-bold mb-0">{{ $balance->leave_type_name }}</h6>
                                <span class="badge badge-success px-2 py-1">
                                    {{ $balance->available_days }} / {{ $balance->total_allowed }} Left
                                </span>
                            </div>

                            <div class="progress mb-2" style="height: 10px; border-radius: 5px;">
                                @php
                                    $percentUsed = ($balance->total_allowed > 0)
                                        ? ($balance->used_days / $balance->total_allowed) * 100
                                        : 0;
                                @endphp
                                <div class="progress-bar bg-info" style="width: {{ $percentUsed }}%"></div>
                            </div>

                            <div class="d-flex justify-content-between small text-muted">
                                <span>Used: {{ $balance->used_days }}</span>
                                <span>Total: {{ $balance->total_allowed }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Leave History Table -->
        <div class="card">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0"><i class="fas fa-history mr-2"></i>Leave History</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Leave Type</th>
                                <th>Duration</th>
                                <th>Days</th>
                                <th>Applied On</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                                <tr>
                                    <td>{{ $leave->leaveType->name }}</td>
                                    <td>{{ Carbon\Carbon::parse($leave->start_date)->format('d M') }} -
                                        {{ Carbon\Carbon::parse($leave->end_date)->format('d M') }}</td>
                                    <td>{{ $leave->total_days }}</td>
                                    <td>{{ $leave->created_at->format('d M Y') }}</td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'cancelled' => 'secondary'
                                            ][$leave->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $statusClass }}">{{ ucfirst($leave->status) }}</span>
                                    </td>
                                    <td>
                                        @if($leave->status == 'pending')
                                            <button class="btn btn-sm btn-danger" onclick="cancelLeave({{ $leave->id }})">
                                                <i class="fas fa-times mr-2"></i> Cancel
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-umbrella-beach fa-3x text-muted mb-3"></i>
                                        <h5>No Leave Applications</h5>
                                        <p class="text-muted">You haven't applied for any leave yet</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $leaves->links() }}
            </div>
        </div>
    </div>

    <!-- Apply Leave Modal -->
    <div class="modal fade" id="applyLeaveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Apply Leave</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('employee.leaves.apply') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Leave Type</label>
                            <select class="form-control form-control-sm" name="leave_type_id" id="leave_type_id" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leavetypes as $type)
                                    @php
                                        $balance = $balances->firstWhere('leave_type_id', $type->id);
                                        $available = $balance ? $balance->remaining_days : $type->days_per_year;
                                    @endphp
                                    <option value="{{ $type->id }}" data-available="{{ $available }}">
                                        {{ $type->name }} ({{ $available }} days available)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control form-control-sm" name="start_date"
                                        id="start_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control form-control-sm" name="end_date" id="end_date"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Reason</label>
                            <textarea class="form-control form-control-sm" name="reason" rows="3" required></textarea>
                        </div>

                        <!-- Live calculation display -->
                        <div class="alert alert-info" id="liveCalculation" style="display: none;">
                            <i class="fas fa-calculator mr-2"></i>
                            <span id="calculationMessage"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="submitBtn">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function cancelLeave(id) {
            if (confirm('Are you sure you want to cancel this leave?')) {
                $.ajax({
                    url: '/employee/leaves/' + id + '/cancel',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function () {
                        toastr.success('Leave cancelled successfully');
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function () {
                        toastr.error('Error cancelling leave');
                    }
                });
            }
        }

        // Live calculation of leave days
        $('#start_date, #end_date, #leave_type_id').on('change', function () {
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            let leaveTypeId = $('#leave_type_id').val();

            if (startDate && endDate && leaveTypeId) {
                let start = new Date(startDate);
                let end = new Date(endDate);
                let diffTime = Math.abs(end - start);
                let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                let selectedOption = $('#leave_type_id option:selected');
                let availableDays = parseInt(selectedOption.data('available'));

                $('#liveCalculation').show();

                if (diffDays > availableDays) {
                    $('#calculationMessage').html(`
                    <span class="text-danger">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        You are requesting ${diffDays} days but only ${availableDays} days are available!
                    </span>
                `);
                    $('#submitBtn').prop('disabled', true);
                } else {
                    $('#calculationMessage').html(`
                    <span class="text-primary">
                        <i class="fas fa-check-circle mr-1"></i>
                        Requesting ${diffDays} days. ${availableDays} days available.
                    </span>
                `);
                    $('#submitBtn').prop('disabled', false);
                }
            } else {
                $('#liveCalculation').hide();
            }
        });
    </script>
@endsection