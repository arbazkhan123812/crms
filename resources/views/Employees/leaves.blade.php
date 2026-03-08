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

    <div class="row mb-4">
        @foreach($balances as $balance)
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">{{ $balance->leaveType->name }}</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{ $balance->remaining_days }}</h3>
                        <small class="text-muted">/ {{ $balance->total_days }}</small>
                    </div>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-success" style="width: {{ $balance->utilization_percentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header bg-light py-3">
            <h6 class="mb-0">Leave History</h6>
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
                            <td>{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M') }}</td>
                            <td>{{ $leave->total_days }}</td>
                            <td>{{ $leave->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $leave->status_badge }}">{{ ucfirst($leave->status) }}</span>
                            </td>
                            <td>
                                @if($leave->status == 'pending')
                                <button class="btn btn-sm btn-danger" onclick="cancelLeave({{ $leave->id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-umbrella-beach fa-3x text-muted mb-3"></i>
                                <h5>No Leave Applications</h5>
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
                        <select class="form-control form-control-sm" name="leave_type_id" required>
                            <option value="">Select</option>
                            @foreach($balances as $balance)
                                <option value="{{ $balance->leaveType->id }}">{{ $balance->leaveType->name }} ({{ $balance->remaining_days }} days left)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control form-control-sm" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control form-control-sm" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control form-control-sm" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cancelLeave(id) {
    if(confirm('Are you sure you want to cancel this leave?')) {
        $.ajax({
            url: '/employee/leaves/' + id + '/cancel',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function() {
                toastr.success('Leave cancelled');
                setTimeout(() => location.reload(), 1500);
            }
        });
    }
}
</script>
@endsection