@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-info-circle text-primary mr-2"></i>
                    Designation Details
                </h3>
                <p class="text-muted mb-0">Complete information about this designation</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.designations.edit', $designation) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('admin.designations.index') }}" class="btn btn-secondary btn-sm ml-2">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="icon-bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-user-tag fa-3x text-white"></i>
                    </div>
                    <h4>{{ $designation->title }}</h4>
                    <p class="text-muted mb-2">{{ $designation->code }}</p>
                    
                    <span class="badge badge-{{ $designation->is_active ? 'success' : 'secondary' }} badge-pill px-3 py-1">
                        {{ $designation->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    
                    <hr>
                    
                    <div class="text-left">
                        <div class="mb-3">
                            <label class="text-muted small mb-0">Department</label>
                            <p class="font-weight-medium">{{ $designation->department->name ?? 'Not Assigned' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted small mb-0">Grade/Level</label>
                            <p class="font-weight-medium">{{ $designation->grade ?? 'Not Set' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted small mb-0">Salary Range</label>
                            <p class="font-weight-medium">
                                @if($designation->min_salary && $designation->max_salary)
                                    PKR {{ number_format($designation->min_salary) }} - {{ number_format($designation->max_salary) }}
                                @else
                                    Not Set
                                @endif
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted small mb-0">Total Employees</label>
                            <p class="font-weight-medium">{{ $designation->employees->count() }}</p>
                        </div>
                        
                        @if($designation->description)
                        <div class="mb-3">
                            <label class="text-muted small mb-0">Description</label>
                            <p class="font-weight-medium">{{ $designation->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-users text-primary mr-2"></i>
                        Employees with this Designation
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Employee Code</th>
                                    <th>Employee Name</th>
                                    <th>Department</th>
                                    <th>Email</th>
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
                                    <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                    <td>{{ $employee->email }}</td>
                                    <td>
                                        @if($employee->status == 'active')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($employee->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted mb-0">No employees with this designation</p>
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
    </div>
</div>

<style>
.icon-bg-primary {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #4e73df, #224abe);
}
</style>
@endsection