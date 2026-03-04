@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-file-alt text-primary mr-2"></i>
                    Leave Application Details
                </h3>
                <p class="text-muted mb-0">{{ $leave->employee->full_name }} - {{ $leave->leaveType->name }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.leaves.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0">Application Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Employee</label>
                            <p class="mb-0"><strong>{{ $leave->employee->full_name }}</strong></p>
                            <small class="text-muted">{{ $leave->employee->employee_code }}</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Leave Type</label>
                            <p class="mb-0"><span class="badge badge-primary">{{ $leave->leaveType->name }}</span></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Status</label>
                            <p class="mb-0"><span class="badge badge-{{ $leave->status_badge }}">{{ ucfirst($leave->status) }}</span></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Start Date</label>
                            <p class="mb-0">{{ $leave->start_date->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">End Date</label>
                            <p class="mb-0">{{ $leave->end_date->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Total Days</label>
                            <p class="mb-0">{{ $leave->total_days }} {{ $leave->half_day != 'none' ? '(' . $leave->half_day_label . ')' : '' }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted small">Reason</label>
                            <p class="mb-0">{{ $leave->reason }}</p>
                        </div>
                        @if($leave->document_path)
                        <div class="col-12 mb-3">
                            <label class="text-muted small">Document</label>
                            <div>
                                <a href="{{ asset('storage/'.$leave->document_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download mr-1"></i> View Document
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0">Application Timeline</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-3 py-2">
                            <div class="d-flex">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 32px; height: 32px;">
                                    <i class="fas fa-pen"></i>
                                </div>
                                <div>
                                    <strong>Applied</strong>
                                    <div class="small text-muted">{{ $leave->created_at->format('d M Y h:i A') }}</div>
                                    <div class="small">by {{ $leave->createdBy->name }}</div>
                                </div>
                            </div>
                        </div>
                        
                        @if($leave->status != 'pending')
                        <div class="list-group-item px-3 py-2">
                            <div class="d-flex">
                                <div class="bg-{{ $leave->status == 'approved' ? 'success' : 'danger' }} text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 32px; height: 32px;">
                                    <i class="fas fa-{{ $leave->status == 'approved' ? 'check' : 'times' }}"></i>
                                </div>
                                <div>
                                    <strong>{{ ucfirst($leave->status) }}</strong>
                                    <div class="small text-muted">{{ $leave->approved_at->format('d M Y h:i A') }}</div>
                                    <div class="small">by {{ $leave->approvedBy->name }}</div>
                                    @if($leave->rejection_reason)
                                        <div class="small text-danger mt-1">Reason: {{ $leave->rejection_reason }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($leave->status == 'pending')
            <div class="card">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-success btn-block mb-2" onclick="updateStatus({{ $leave->id }}, 'approved')">
                        <i class="fas fa-check mr-1"></i> Approve
                    </button>
                    <button class="btn btn-danger btn-block" onclick="showRejectModal({{ $leave->id }})">
                        <i class="fas fa-times mr-1"></i> Reject
                    </button>
                </div>
            </div>
            @endif
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
let currentLeaveId = {{ $leave->id }};

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

function showRejectModal(id) {
    $('#rejectModal').modal('show');
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