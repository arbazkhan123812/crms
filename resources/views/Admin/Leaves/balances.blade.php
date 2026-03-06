@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-balance-scale text-primary mr-2"></i>
                    Leave Balances
                </h3>
                <p class="text-muted mb-0">Track employee leave balances</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm" onclick="showInitializeModal()">
                    <i class="fas fa-plus mr-1"></i> Initialize Balance
                </button>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-5">
                        <select class="form-control form-control-sm" name="employee_id">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" name="year">
                            <option value="">Select Year</option>
                            @for($y = date('Y')-2; $y <= date('Y')+1; $y++)
                                <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-filter mr-1"></i> Apply
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
                            <th>Year</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Used</th>
                            <th class="text-center">Pending</th>
                            <th class="text-center">Remaining</th>
                            <th class="text-center">Carried</th>
                            <th class="text-center">Utilization</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($balances as $balance)
                        <tr>
                            <td>
                                <strong>{{ $balance->employee->full_name }}</strong>
                                <div class="small text-muted">{{ $balance->employee->employee_code }}</div>
                            </td>
                            <td>{{ $balance->leaveType->name }}</td>
                            <td class="text-center">{{ $balance->year }}</td>
                            <td class="text-center">{{ $balance->total_days }}</td>
                            <td class="text-center">{{ $balance->used_days }}</td>
                            <td class="text-center">{{ $balance->pending_days }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $balance->remaining_days > 0 ? 'success' : 'danger' }}">
                                    {{ $balance->remaining_days }}
                                </span>
                            </td>
                            <td class="text-center">{{ $balance->carried_forward }}</td>
                            <td class="text-center">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $balance->utilization_percentage > 80 ? 'danger' : 'success' }}" 
                                         style="width: {{ $balance->utilization_percentage }}%">
                                        {{ $balance->utilization_percentage }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                                <h5>No Leave Balances Found</h5>
                                <p class="text-muted">Click "Initialize Balance" to set up leave balances</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $balances->links() }}
        </div>
    </div>
</div>

<!-- Initialize Balance Modal -->
<div class="modal fade" id="initializeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Initialize Leave Balance</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="initializeForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Employee <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="leave_type_id" required>
                            <option value="">Select Leave Type</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Year <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" name="year" value="{{ date('Y') }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Total Days <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" name="total_days" min="0" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Carried Forward</label>
                        <input type="number" class="form-control form-control-sm" name="carried_forward" min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Initialize Balance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showInitializeModal() {
    $('#initializeModal').modal('show');
}

$('#initializeForm').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '{{ route("admin.leaves.balances.init") }}',
        type: 'POST',
        data: $(this).serialize(),
        success: function() {
            $('#initializeModal').modal('hide');
            toastr.success('Leave balance initialized successfully');
            setTimeout(() => location.reload(), 1500);
        },
        error: function() {
            toastr.error('Error initializing balance');
        }
    });
});
</script>
@endsection