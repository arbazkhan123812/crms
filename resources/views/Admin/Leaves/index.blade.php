@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-calendar-check text-primary mr-2"></i>
                    Leave Applications
                </h3>
                <p class="text-muted mb-0">Manage employee leave requests</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.leaves.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Apply Leave
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

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Pending</h6>
                            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Approved</h6>
                            <h3 class="mb-0">{{ $stats['approved'] }}</h3>
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
                            <h6 class="text-white-50">Rejected</h6>
                            <h3 class="mb-0">{{ $stats['rejected'] }}</h3>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Total</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <i class="fas fa-calendar fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" name="employee_id">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" name="leave_type_id">
                            <option value="">All Leave Types</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}" placeholder="From Date">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}" placeholder="To Date">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-filter mr-1"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Days</th>
                            <th>Applied On</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                        <tr>
                            <td>
                                <strong>{{ $leave->employee->full_name }}</strong>
                                <div class="small text-muted">{{ $leave->employee->employee_code }}</div>
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $leave->leaveType->name }}</span>
                            </td>
                            <td>
                                {{ $leave->duration }}
                                @if($leave->half_day != 'none')
                                    <br><small class="text-muted">{{ $leave->half_day_label }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $leave->total_days }}</td>
                            <td>{{ $leave->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $leave->status_badge }}">{{ ucfirst($leave->status) }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.leaves.show', $leave->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($leave->status == 'pending')
                                <button class="btn btn-sm btn-success" onclick="updateStatus({{ $leave->id }}, 'approved')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="showRejectModal({{ $leave->id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h5>No Leave Applications Found</h5>
                                <p class="text-muted">Click "Apply Leave" to create a new leave request</p>
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
                    <p class="text-muted small mb-0">
                        Showing {{ $leaves->firstItem() ?? 0 }} to {{ $leaves->lastItem() ?? 0 }} of {{ $leaves->total() }} entries
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="float-right">
                        {{ $leaves->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject Leave Application</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="rejectForm">
                @csrf
                <input type="hidden" name="status" value="rejected">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm" name="rejection_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Reject Leave</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentLeaveId = null;

function showRejectModal(id) {
    currentLeaveId = id;
    $('#rejectModal').modal('show');
}

function updateStatus(id, status) {
    if(confirm('Are you sure you want to ' + status + ' this leave application?')) {
        $.ajax({
            url: '/admin/leaves/' + id + '/status',
            type: 'POST',
            data: {
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                toastr.success('Leave ' + status + ' successfully');
                setTimeout(() => location.reload(), 1500);
            },
            error: function() {
                toastr.error('Error updating leave status');
            }
        });
    }
}

$('#rejectForm').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '/admin/leaves/' + currentLeaveId + '/status',
        type: 'POST',
        data: $(this).serialize(),
        success: function() {
            $('#rejectModal').modal('hide');
            toastr.success('Leave rejected successfully');
            setTimeout(() => location.reload(), 1500);
        },
        error: function() {
            toastr.error('Error rejecting leave');
        }
    });
});
</script>
@endsection