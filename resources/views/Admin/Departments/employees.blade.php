@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-users text-primary mr-2"></i>
                    {{ $department->name }} - Employees
                </h3>
                <p class="text-muted mb-0">List of all employees in this department</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Department
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Employee Code</th>
                            <th>Employee Name</th>
                            <th>Designation</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joining Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td><span class="badge badge-light">{{ $employee->employee_code }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($employee->profile_image)
                                        <img src="{{ asset('storage/'.$employee->profile_image) }}" alt="" class="rounded-circle mr-2" width="32" height="32" style="object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px; font-size: 12px;">
                                            {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0" style="font-weight: 500;">{{ $employee->full_name }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $employee->designation->title ?? 'N/A' }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->phone ?? 'N/A' }}</td>
                            <td>{{ $employee->joining_date->format('d M, Y') }}</td>
                            <td>
                                @if($employee->status == 'active')
                                    <span class="badge badge-success">Active</span>
                                @elseif($employee->status == 'inactive')
                                    <span class="badge badge-secondary">Inactive</span>
                                @else
                                    <span class="badge badge-danger">{{ ucfirst($employee->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <h5>No Employees Found</h5>
                                    <p>This department has no employees assigned yet</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($employees->hasPages())
        <div class="card-footer bg-white">
            {{ $employees->links() }}
        </div>
        @endif
    </div>
</div>
@endsection