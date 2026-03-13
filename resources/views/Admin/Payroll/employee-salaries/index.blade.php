@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-user-tie text-primary mr-2"></i>
                    Employee Salaries
                </h3>
                <p class="text-muted mb-0">Manage employee salary assignments</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payroll.employee-salaries.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Assign Salary
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

    <div class="card">
        <div class="card-header bg-light py-2">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control form-control-sm" id="departmentFilter">
                        <option value="">All Departments</option>
                        @foreach(App\Models\Department::all() as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control form-control-sm" id="designationFilter">
                        <option value="">All Designations</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search employee...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Template</th>
                            <th>Basic</th>
                            <th>Gross</th>
                            <th>Net</th>
                            <th>Effective From</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $salary)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($salary->employee->profile_image)
                                        <img src="{{ asset('storage/'.$salary->employee->profile_image) }}" class="rounded-circle mr-2" width="32" height="32">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px;">
                                            {{ substr($salary->employee->first_name, 0, 1) }}{{ substr($salary->employee->last_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $salary->employee->full_name }}</strong>
                                        <div class="small text-muted">{{ $salary->employee->employee_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $salary->employee->department->name ?? 'N/A' }}</td>
                            <td>{{ $salary->employee->designation->title ?? 'N/A' }}</td>
                            <td>{{ $salary->salaryTemplate->name ?? 'Custom' }}</td>
                            <td>PKR {{ number_format($salary->basic, 2) }}</td>
                            <td>PKR {{ number_format($salary->gross_salary, 2) }}</td>
                            <td class="text-success font-weight-bold">PKR {{ number_format($salary->net_salary, 2) }}</td>
                            <td>{{ $salary->effective_from->format('d M Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $salary->is_active ? 'success' : 'secondary' }}">
                                    {{ $salary->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.payroll.employee-salaries.edit', $salary->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.payroll.employee-salaries.history', $salary->employee_id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-history"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                                <h5>No Employee Salaries Found</h5>
                                <p class="text-muted">Click "Assign Salary" to assign salary to employees</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $salaries->links() }}
        </div>
    </div>
</div>

<script>
$('#departmentFilter').change(function() {
    let deptId = $(this).val();
    if(deptId) {
        $.get('/admin/designations/by-department/' + deptId, function(data) {
            let options = '<option value="">All Designations</option>';
            data.forEach(function(desig) {
                options += `<option value="${desig.id}">${desig.title}</option>`;
            });
            $('#designationFilter').html(options);
        });
    }
});
</script>
@endsection