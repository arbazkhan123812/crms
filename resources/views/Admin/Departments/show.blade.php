@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-info-circle text-primary mr-2"></i>
                    Department Details
                </h3>
                <p class="text-muted mb-0">View complete department information</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary btn-sm ml-2">
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
                        <i class="fas fa-sitemap fa-3x text-white"></i>
                    </div>
                    <h4>{{ $department->name }}</h4>
                    <p class="text-muted mb-2">{{ $department->code }}</p>
                    
                    <span class="badge badge-{{ $department->is_active ? 'success' : 'secondary' }} badge-pill px-3 py-1">
                        {{ $department->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    
                    <hr>
                    
                    <div class="text-left">
                        <div class="mb-3">
                            <label class="text-muted small mb-0">Company</label>
                            <p class="font-weight-medium">{{ $department->company->name ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted small mb-0">Parent Department</label>
                            <p class="font-weight-medium">{{ $department->parent->name ?? 'Root Department' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted small mb-0">Department Manager</label>
                            <p class="font-weight-medium">{{ $department->manager->full_name ?? 'Not Assigned' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted small mb-0">Total Employees</label>
                            <p class="font-weight-medium">{{ $department->employees->count() }}</p>
                        </div>
                        
                        @if($department->description)
                        <div class="mb-3">
                            <label class="text-muted small mb-0">Description</label>
                            <p class="font-weight-medium">{{ $department->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-users text-primary mr-2"></i>
                        Department Employees
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Designation</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                <tr>
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
                                                <small class="text-muted">{{ $employee->employee_code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee->designation->title ?? 'N/A' }}</td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->phone ?? 'N/A' }}</td>
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
                                        <p class="text-muted mb-0">No employees in this department</p>
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
            
            @if($department->children->count() > 0)
            <div class="card">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-sitemap text-primary mr-2"></i>
                        Sub-Departments
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Department Name</th>
                                    <th>Manager</th>
                                    <th>Employees</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->children as $child)
                                <tr>
                                    <td><span class="badge badge-light">{{ $child->code }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.departments.show', $child) }}" class="text-primary">
                                            {{ $child->name }}
                                        </a>
                                    </td>
                                    <td>{{ $child->manager->full_name ?? 'Not Assigned' }}</td>
                                    <td>{{ $child->employees_count ?? 0 }}</td>
                                    <td>
                                        @if($child->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
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