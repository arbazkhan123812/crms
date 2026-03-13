@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-file-invoice-dollar text-primary mr-2"></i>
                    Payroll Management
                </h3>
                <p class="text-muted mb-0">{{ Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</p>
            </div>
            <div class="col-auto">
                <div class="d-flex">
                    <select class="form-control form-control-sm mr-2" style="width: 120px;" id="monthSelect">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select class="form-control form-control-sm mr-2" style="width: 100px;" id="yearSelect">
                        @foreach(range(date('Y')-2, date('Y')+1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('admin.payroll.payrolls.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-play mr-1"></i> Process Payroll
                    </a>
                </div>
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
                    <h6 class="text-white-50">Total Employees</h6>
                    <h3 class="mb-0">{{ $stats['total_employees'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-3">
                    <h6 class="text-white-50">Processed</h6>
                    <h3 class="mb-0">{{ $stats['processed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <h6 class="text-white-50">Paid</h6>
                    <h3 class="mb-0">{{ $stats['paid'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body py-3">
                    <h6 class="text-white-50">Total Salary</h6>
                    <h3 class="mb-0">PKR {{ number_format($stats['total_salary'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-list mr-2"></i>Payroll List</h6>
            <div>
                <button class="btn btn-sm btn-success mr-2" onclick="bulkAction('paid')">
                    <i class="fas fa-check-circle mr-1"></i> Mark as Paid
                </button>
                <button class="btn btn-sm btn-danger" onclick="bulkAction('cancelled')">
                    <i class="fas fa-times-circle mr-1"></i> Cancel Selected
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="40">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="selectAll">
                                    <label class="custom-control-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Basic</th>
                            <th>Gross</th>
                            <th>Deductions</th>
                            <th>Net Salary</th>
                            <th>Present Days</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $payroll)
                        <tr>
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input payroll-check" id="payroll{{ $payroll->id }}" value="{{ $payroll->id }}">
                                    <label class="custom-control-label" for="payroll{{ $payroll->id }}"></label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($payroll->employee->profile_image)
                                        <img src="{{ asset('storage/'.$payroll->employee->profile_image) }}" class="rounded-circle mr-2" width="32" height="32">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px;">
                                            {{ substr($payroll->employee->first_name, 0, 1) }}{{ substr($payroll->employee->last_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $payroll->employee->full_name }}</strong>
                                        <div class="small text-muted">{{ $payroll->employee->employee_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $payroll->employee->department->name ?? 'N/A' }}</td>
                            <td>PKR {{ number_format($payroll->basic, 2) }}</td>
                            <td>PKR {{ number_format($payroll->gross_salary, 2) }}</td>
                            <td class="text-danger">PKR {{ number_format($payroll->total_deductions, 2) }}</td>
                            <td class="text-success font-weight-bold">PKR {{ number_format($payroll->net_salary, 2) }}</td>
                            <td>{{ $payroll->present_days }}/{{ $payroll->working_days }}</td>
                            <td>
                                <span class="badge badge-{{ $payroll->status_badge }}">{{ ucfirst($payroll->status) }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.payroll.payrolls.show', $payroll->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.payroll.payrolls.payslip', $payroll->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                                <h5>No Payroll Records Found</h5>
                                <p class="text-muted">Click "Process Payroll" to generate payroll for this month</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $payrolls->links() }}
        </div>
    </div>
</div>

<script>
$('#monthSelect, #yearSelect').change(function() {
    let month = $('#monthSelect').val();
    let year = $('#yearSelect').val();
    window.location.href = '{{ route("admin.payroll.payrolls.index") }}?month=' + month + '&year=' + year;
});

$('#selectAll').change(function() {
    $('.payroll-check').prop('checked', $(this).prop('checked'));
});

function bulkAction(status) {
    let ids = [];
    $('.payroll-check:checked').each(function() {
        ids.push($(this).val());
    });

    if(ids.length == 0) {
        toastr.error('Please select at least one payroll');
        return;
    }

    $.ajax({
        url: '{{ route("admin.payroll.payrolls.bulk-status") }}',
        type: 'POST',
        data: {
            ids: ids,
            status: status,
            _token: '{{ csrf_token() }}'
        },
        success: function() {
            toastr.success('Payroll status updated');
            setTimeout(() => location.reload(), 1500);
        }
    });
}
</script>
@endsection