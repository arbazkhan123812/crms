@extends('layout.employee')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-user-circle text-primary mr-2"></i>
                    My Profile
                </h3>
                <p class="text-muted mb-0">{{ $employee->full_name }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    @if($employee->profile_image)
                        <img src="{{ asset('storage/'.$employee->profile_image) }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; font-size: 36px;">
                            {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                        </div>
                    @endif
                    <h5>{{ $employee->full_name }}</h5>
                    <p class="text-muted">{{ $employee->designation->title ?? 'N/A' }}</p>
                    <p class="small text-muted">{{ $employee->employee_code }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0">Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Full Name</label>
                            <p class="mb-0">{{ $employee->full_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Employee Code</label>
                            <p class="mb-0">{{ $employee->employee_code }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Email</label>
                            <p class="mb-0">{{ $employee->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Phone</label>
                            <p class="mb-0">{{ $employee->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Department</label>
                            <p class="mb-0">{{ $employee->department->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Designation</label>
                            <p class="mb-0">{{ $employee->designation->title ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Joining Date</label>
                            <p class="mb-0">{{ $employee->joining_date ? $employee->joining_date->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Reporting To</label>
                            <p class="mb-0">{{ $employee->reportingTo->full_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection