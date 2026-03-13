@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-play-circle text-primary mr-2"></i>
                    Process Payroll
                </h3>
                <p class="text-muted mb-0">{{ Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payroll.payrolls.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="mb-0"><i class="fas fa-users mr-2"></i>Select Employees for Payroll Processing</h6>
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-primary" onclick="selectAll()">Select All</button>
                    <button class="btn btn-sm btn-secondary" onclick="deselectAll()">Deselect All</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payroll.payrolls.process') }}" method="POST">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">

                <div class="table-responsive">
                    <table class="table table-hover">
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
                                <th>Designation</th>
                                <th>Current Salary</th>
                                <th>Last Processed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                            @php
                                $lastPayroll = App\Models\Payroll::where('employee_id', $employee->id)
                                    ->latest()
                                    ->first();
                                $exists = App\Models\Payroll::where('employee_id', $employee->id)
                                    ->where('month', $month)
                                    ->where('year', $year)
                                    ->exists();
                            @endphp
                            @if(!$exists)
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input employee-check" 
                                               name="employee_ids[]" value="{{ $employee->id }}" 
                                               id="emp{{ $employee->id }}">
                                        <label class="custom-control-label" for="emp{{ $employee->id }}"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($employee->profile_image)
                                            <img src="{{ asset('storage/'.$employee->profile_image) }}" class="rounded-circle mr-2" width="32" height="32">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px;">
                                                {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $employee->full_name }}</strong>
                                            <div class="small text-muted">{{ $employee->employee_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                <td>{{ $employee->designation->title ?? 'N/A' }}</td>
                                <td>PKR {{ number_format($employee->salary->net_salary ?? 0, 2) }}</td>
                                <td>
                                    @if($lastPayroll)
                                        {{ $lastPayroll->month_name }} {{ $lastPayroll->year }}
                                    @else
                                        Never
                                    @endif
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(count($employees) == 0)
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>All employees have been processed for this month!</h5>
                </div>
                @else
                <div class="text-right mt-4">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-play mr-1"></i> Process Selected
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

<script>
$('#selectAll').change(function() {
    $('.employee-check').prop('checked', $(this).prop('checked'));
});

function selectAll() {
    $('.employee-check').prop('checked', true);
    $('#selectAll').prop('checked', true);
}

function deselectAll() {
    $('.employee-check').prop('checked', false);
    $('#selectAll').prop('checked', false);
}
</script>
@endsection