@extends('layout.app')

@section('content')
    <div class="content">
        <!-- PAGE HEADER -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title text-dark">
                        <i class="fas fa-users text-primary mr-2"></i>
                        Employees
                    </h3>
                    <p class="text-muted mb-0">Manage your organization's employees</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm" ">
                                <i class=" fas fa-plus mr-1"></i> Add Employee
                    </a>
                    <div class="btn-group ml-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle"
                            data-toggle="dropdown">
                            <i class="fas fa-download mr-1"></i> Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#"><i class="fas fa-file-excel mr-2"></i>Excel</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-file-pdf mr-2"></i>PDF</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-print mr-2"></i>Print</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTERS CARD -->
        <div class="card mb-4">
            <div class="card-body py-3">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="form-label mb-1 text-muted small">Search</label>
                                <input type="text" class="form-control form-control-sm" name="search" id="search"
                                    placeholder="Name, email, code...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="form-label mb-1 text-muted small">Department</label>
                                <select class="form-control form-control-sm" name="department_id" id="department_filter">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="form-label mb-1 text-muted small">Designation</label>
                                <select class="form-control form-control-sm" name="designation_id" id="designation_filter">
                                    <option value="">All Designations</option>
                                    @foreach($designations as $designation)
                                        <option value="{{ $designation->id }}">{{ $designation->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="form-label mb-1 text-muted small">Employment Type</label>
                                <select class="form-control form-control-sm" name="employment_type" id="employment_filter">
                                    <option value="">All Types</option>
                                    @foreach($employmentTypes as $type)
                                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="form-label mb-1 text-muted small">Status</label>
                                <select class="form-control form-control-sm" name="status" id="status_filter">
                                    <option value="">All Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary btn-sm btn-block" onclick="applyFilters()">
                                <i class="fas fa-filter mr-1"></i> Apply Filters
                            </button>
                            <button type="button" class="btn btn-light btn-sm ml-2" onclick="resetFilters()">
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- EMPLOYEES TABLE -->
        <div class="card">
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
                                <th>Designation</th>
                                <th>Contact</th>
                                <th>Employment Type</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="employeesTableBody">
                            @forelse($employees as $employee)
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input employee-checkbox"
                                                id="emp{{ $employee->id }}" value="{{ $employee->id }}">
                                            <label class="custom-control-label" for="emp{{ $employee->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($employee->profile_image)
                                                <img src="{{ asset('storage/' . $employee->profile_image) }}" alt=""
                                                    class="rounded-circle mr-2" width="40" height="40" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2"
                                                    style="width: 40px; height: 40px;">
                                                    <span>{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0" style="font-weight: 500;">{{ $employee->full_name }}</h6>
                                                <small class="text-muted">{{ $employee->employee_code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                    <td>{{ $employee->designation->title ?? 'N/A' }}</td>
                                    <td>
                                        <div>{{ $employee->email }}</div>
                                        <small class="text-muted">{{ $employee->phone ?? 'No phone' }}</small>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-info badge-pill">{{ ucfirst($employee->employment_type) }}</span>
                                    </td>
                                    <td>
                                        @if($employee->status == 'active')
                                            <span class="badge badge-success badge-pill">Active</span>
                                        @elseif($employee->status == 'inactive')
                                            <span class="badge badge-secondary badge-pill">Inactive</span>
                                        @elseif($employee->status == 'suspended')
                                            <span class="badge badge-warning badge-pill">Suspended</span>
                                        @else
                                            <span class="badge badge-danger badge-pill">Terminated</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-sm btn-primary text-info"
                                            onclick="viewEmployee({{ $employee->id }})" title="View">
                                          details
                                        </button>
                                        <a href="{{ route('admin.employees.edit', $employee) }}"
                                            class="btn btn-sm btn-primary text-warning" title="Edit">
                                            edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-primary text-danger"
                                            onclick="deleteEmployee({{ $employee->id }})" title="Delete">
                                            del
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <h5>No Employees Found</h5>
                                            <p>Click "Add Employee" to add your first employee</p>
                                        </div>
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
                            Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of
                            {{ $employees->total() }} entries
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $employees->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD/EDIT EMPLOYEE MODAL -->
    <div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title" style="font-weight: 500;">
                        <i class="fas fa-user-plus mr-2"></i>
                        <span id="modalTitle">Add New Employee</span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="employeeForm" method="POST" enctype="multipart/form-data">
                    @csrf
@if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
                    <input type="hidden" id="employeeId" name="id">

                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <!-- Personal Information -->
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0" style="font-weight: 500;">
                                    <i class="fas fa-user text-primary mr-2"></i>
                                    Personal Information
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">First Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm" name="first_name"
                                                id="first_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Middle Name</label>
                                            <input type="text" class="form-control form-control-sm" name="middle_name"
                                                id="middle_name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Last Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm" name="last_name"
                                                id="last_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Gender</label>
                                            <select class="form-control form-control-sm" name="gender" id="gender">
                                                <option value="">Select Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Birth Date</label>
                                            <input type="date" class="form-control form-control-sm" name="birth_date"
                                                id="birth_date">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Profile Image</label>
                                            <input type="file" class="form-control-file" name="profile_image"
                                                id="profile_image" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Status</label>
                                            <select class="form-control form-control-sm" name="status" id="status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="suspended">Suspended</option>
                                                <option value="terminated">Terminated</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0" style="font-weight: 500;">
                                    <i class="fas fa-address-book text-primary mr-2"></i>
                                    Contact Information
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control form-control-sm" name="email" id="email"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Personal Email</label>
                                            <input type="email" class="form-control form-control-sm" name="personal_email"
                                                id="personal_email">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Phone</label>
                                            <input type="text" class="form-control form-control-sm" name="phone" id="phone"
                                                placeholder="+92 98765 43210">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Emergency Phone</label>
                                            <input type="text" class="form-control form-control-sm" name="emergency_phone"
                                                id="emergency_phone">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0" style="font-weight: 500;">
                                    <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                    Address Information
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Present Address</label>
                                            <textarea class="form-control form-control-sm" name="present_address"
                                                id="present_address" rows="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Permanent Address</label>
                                            <textarea class="form-control form-control-sm" name="permanent_address"
                                                id="permanent_address" rows="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">City</label>
                                            <input type="text" class="form-control form-control-sm" name="city" id="city">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">State</label>
                                            <input type="text" class="form-control form-control-sm" name="state" id="state">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Country</label>
                                            <input type="text" class="form-control form-control-sm" name="country"
                                                id="country" value="India">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Postal Code</label>
                                            <input type="text" class="form-control form-control-sm" name="postal_code"
                                                id="postal_code">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Employment Information -->
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0" style="font-weight: 500;">
                                    <i class="fas fa-briefcase text-primary mr-2"></i>
                                    Employment Information
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Company <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm" name="company_id" id="company_id"
                                                required>
                                                <option value="">Select Company</option>
                                                @foreach($companies as $company)
                                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Department</label>
                                            <select class="form-control form-control-sm" name="department_id"
                                                id="department_id">
                                                <option value="">Select Department</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Designation</label>
                                            <select class="form-control form-control-sm" name="designation_id"
                                                id="designation_id">
                                                <option value="">Select Designation</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Reporting To</label>
                                            <select class="form-control form-control-sm" name="reporting_to_id"
                                                id="reporting_to_id">
                                                <option value="">Select Manager</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Employment Type</label>
                                            <select class="form-control form-control-sm" name="employment_type"
                                                id="employment_type" required>
                                                <option value="permanent">Permanent</option>
                                                <option value="contract">Contract</option>
                                                <option value="intern">Intern</option>
                                                <option value="trainee">Trainee</option>
                                                <option value="consultant">Consultant</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Joining Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control form-control-sm" name="joining_date"
                                                id="joining_date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Confirmation Date</label>
                                            <input type="date" class="form-control form-control-sm" name="confirmation_date"
                                                id="confirmation_date">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Resignation Date</label>
                                            <input type="date" class="form-control form-control-sm" name="resignation_date"
                                                id="resignation_date">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Exit Date</label>
                                            <input type="date" class="form-control form-control-sm" name="exit_date"
                                                id="exit_date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="saveBtn">
                            <i class="fas fa-save mr-1"></i> Save Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- VIEW EMPLOYEE MODAL -->
    <div class="modal fade" id="viewEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title" style="font-weight: 500;">
                        <i class="fas fa-user mr-2"></i>
                        Employee Details
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row" id="employeeDetails">
                        <!-- Details will be loaded via AJAX -->
                        <div class="col-12 text-center py-5">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-2">Loading employee details...</p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- BULK ACTIONS MODAL -->
    <div class="modal fade" id="bulkActionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white py-2">
                    <h5 class="modal-title" style="font-weight: 500;">
                        <i class="fas fa-tasks mr-2"></i>
                        Bulk Actions
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p><span id="selectedCount">0</span> employee(s) selected</p>

                    <div class="form-group">
                        <label class="form-label">Select Action</label>
                        <select class="form-control form-control-sm" id="bulkAction">
                            <option value="">Choose action</option>
                            <option value="active">Set Active</option>
                            <option value="inactive">Set Inactive</option>
                            <option value="suspended">Set Suspended</option>
                            <option value="terminated">Set Terminated</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="executeBulkAction()">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TERMINATE MODAL -->
    <div class="modal fade" id="terminateModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h5 class="modal-title" style="font-weight: 500;">
                        <i class="fas fa-user-slash mr-2"></i>
                        Terminate Employee
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="terminateForm">
                    @csrf
@if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
                    <input type="hidden" id="terminate_employee_id">

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Exit Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" name="exit_date" id="exit_date_modal"
                                required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Exit Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm" name="exit_reason" id="exit_reason" rows="3"
                                required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger btn-sm">
                            Terminate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Custom styles */
        .table td {
            vertical-align: middle;
        }
    </style>

    <script>
        $(document).ready(function () {
            // Select all checkbox
            $('#selectAll').change(function () {
                $('.employee-checkbox').prop('checked', $(this).prop('checked'));
                updateSelectedCount();
            });

            // Individual checkboxes
            $(document).on('change', '.employee-checkbox', function () {
                updateSelectedCount();
            });

            // Company change - load departments
            $('#company_id').change(function () {
                let companyId = $(this).val();
                loadDepartments(companyId);
                loadDesignations(companyId);
                loadManagers(companyId);
            });

            // Department change - load designations
            $('#department_id').change(function () {
                let departmentId = $(this).val();
                loadDesignationsByDepartment(departmentId);
            });

            // Form submission
            $('#employeeForm').submit(function (e) {
                e.preventDefault();
                saveEmployee();
            });

            // Terminate form submission
            $('#terminateForm').submit(function (e) {
                e.preventDefault();
                terminateEmployee();
            });
        });

        // Update selected count
        function updateSelectedCount() {
            let count = $('.employee-checkbox:checked').length;
            $('#selectedCount').text(count);

            if (count > 0) {
                if ($('#bulkActionBtn').length == 0) {
                    $('.page-header .col-auto').append(`
                            <button type="button" class="btn btn-warning btn-sm ml-2" id="bulkActionBtn" onclick="openBulkActionModal()">
                                <i class="fas fa-tasks mr-1"></i> Bulk Actions
                            </button>
                        `);
                }
            } else {
                $('#bulkActionBtn').remove();
            }
        }

        // Open bulk action modal
        function openBulkActionModal() {
            $('#bulkActionModal').modal('show');
        }

        // Execute bulk action
        function executeBulkAction() {
            let action = $('#bulkAction').val();
            let ids = [];

            $('.employee-checkbox:checked').each(function () {
                ids.push($(this).val());
            });

            if (ids.length == 0) {
                toastr.error('No employees selected');
                return;
            }

            if (!action) {
                toastr.error('Please select an action');
                return;
            }

            if (action == 'delete') {
                if (confirm('Are you sure you want to delete selected employees?')) {
                    $.ajax({
                        url: '{{ route("admin.employees.bulk.delete") }}',
                        type: 'POST',
                        data: {
                            ids: ids,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.message);
                                location.reload();
                            }
                        }
                    });
                }
            } else {
                $.ajax({
                    url: '{{ route("admin.employees.bulk.status-update") }}',
                    type: 'POST',
                    data: {
                        ids: ids,
                        status: action,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#bulkActionModal').modal('hide');
                            location.reload();
                        }
                    }
                });
            }
        }

        // Load departments
        function loadDepartments(companyId) {
            if (!companyId) return;

            $.get('/api/departments/list?company_id=' + companyId, function (data) {
                let options = '<option value="">Select Department</option>';
                data.forEach(function (dept) {
                    options += `<option value="${dept.id}">${dept.name}</option>`;
                });
                $('#department_id').html(options);
            });
        }

        // Load designations
        function loadDesignations(companyId) {
            if (!companyId) return;

            $.get('/api/designations/list?company_id=' + companyId, function (data) {
                let options = '<option value="">Select Designation</option>';
                data.forEach(function (desig) {
                    options += `<option value="${desig.id}">${desig.title}</option>`;
                });
                $('#designation_id').html(options);
            });
        }

        // Load designations by department
        function loadDesignationsByDepartment(departmentId) {
            if (!departmentId) return;

            $.get('/api/designations/by-department/' + departmentId, function (data) {
                let options = '<option value="">Select Designation</option>';
                data.forEach(function (desig) {
                    options += `<option value="${desig.id}">${desig.title}</option>`;
                });
                $('#designation_id').html(options);
            });
        }

        // Load managers
        function loadManagers(companyId) {
            if (!companyId) return;

            $.get('/api/employees/list?company_id=' + companyId + '&status=active', function (data) {
                let options = '<option value="">Select Manager</option>';
                data.forEach(function (emp) {
                    options += `<option value="${emp.id}">${emp.name}</option>`;
                });
                $('#reporting_to_id').html(options);
            });
        }

        // Open add modal
        function openAddEmployeeModal() {
            resetForm();
            $('#modalTitle').text('Add New Employee');
            $('#employeeId').val('');
            $('#employeeModal').modal('show');
        }

        // Save employee
        function saveEmployee() {
            let formData = new FormData($('#employeeForm')[0]);
            let id = $('#employeeId').val();
            let url = id ? '/admin/employees/' + id : '{{ route("admin.employees.store") }}';

            if (id) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('#saveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
                },
                success: function (response) {
                    toastr.success('Employee saved successfully');
                    $('#employeeModal').modal('hide');
                    location.reload();
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON.errors;
                    if (errors) {
                        Object.keys(errors).forEach(function (key) {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('Error saving employee');
                    }
                },
                complete: function () {
                    $('#saveBtn').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Employee');
                }
            });
        }

        // Edit employee
        function editEmployee(id) {
            $.get('/admin/employees/' + id + '/edit', function (data) {
                $('#modalTitle').text('Edit Employee');
                $('#employeeId').val(data.id);
                $('#first_name').val(data.first_name);
                $('#middle_name').val(data.middle_name);
                $('#last_name').val(data.last_name);
                $('#gender').val(data.gender);
                $('#birth_date').val(data.birth_date);
                $('#email').val(data.email);
                $('#personal_email').val(data.personal_email);
                $('#phone').val(data.phone);
                $('#emergency_phone').val(data.emergency_phone);
                $('#present_address').val(data.present_address);
                $('#permanent_address').val(data.permanent_address);
                $('#city').val(data.city);
                $('#state').val(data.state);
                $('#country').val(data.country);
                $('#postal_code').val(data.postal_code);
                $('#company_id').val(data.company_id).trigger('change');
                $('#department_id').val(data.department_id);
                $('#designation_id').val(data.designation_id);
                $('#reporting_to_id').val(data.reporting_to_id);
                $('#employment_type').val(data.employment_type);
                $('#joining_date').val(data.joining_date);
                $('#confirmation_date').val(data.confirmation_date);
                $('#resignation_date').val(data.resignation_date);
                $('#exit_date').val(data.exit_date);
                $('#status').val(data.status);

                $('#employeeModal').modal('show');
            });
        }

        // View employee
        // View employee
        function viewEmployee(id) {
            $('#viewEmployeeModal').modal('show');

            $.get("{{ url('admin/employees') }}/" + id, function (response) {
                let employee = response.employee;
                let age = response.age;
                let totalExperience = response.totalExperience;
                let education = response.education || [];
                let experience = response.experience || [];
                let assets = response.assets || [];

                let html = generateEmployeeDetails(employee, age, totalExperience, education, experience, assets);
                $('#employeeDetails').html(html);
            }).fail(function () {
                $('#employeeDetails').html('<div class="col-12 text-center py-5 text-danger">Error loading employee details</div>');
            });
        }

        // View employee function
        function viewEmployee(id) {
            $('#viewEmployeeModal').modal('show');
            $('#employeeDetails').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading employee details...</p>
            </div>
        `);

            $.ajax({
                url: "{{ url('admin/employees') }}/" + id,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    let employee = response.employee;
                    let age = response.age;
                    let totalExperience = response.totalExperience;
                    let education = response.education || [];
                    let experience = response.experience || [];
                    let assets = response.assets || [];

                    let html = generateEmployeeProfile(employee, age, totalExperience, education, experience, assets);
                    $('#employeeDetails').html(html);
                },
                error: function () {
                    $('#employeeDetails').html(`
                    <div class="alert alert-danger m-4">
                        <i class="fas fa-exclamation-circle mr-2"></i> Error loading employee details
                    </div>
                `);
                }
            });
        }

        // Generate Clean Bootstrap 4 Profile HTML
        function generateEmployeeProfile(employee, age, totalExperience, education, experience, assets) {
            const val = (data) => data && data !== null && data !== '' ? data : '-';

            const formatDate = (dateStr) => {
                if (!dateStr) return '-';
                let date = new Date(dateStr);
                return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
            };

            const formatCurrency = (amount) => {
                if (!amount) return '-';
                return 'PKR ' + Number(amount).toLocaleString();
            };

            // Education HTML
            let educationHtml = '';
            if (education && education.length > 0) {
                educationHtml = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead class="thead-light"><tr><th>Course</th><th>Institution</th><th>Marks</th><th>Year</th></tr></thead><tbody>';
                education.forEach(edu => {
                    educationHtml += `<tr>
                    <td>${val(edu.course)}</td>
                    <td>${val(edu.institution)}</td>
                    <td>${edu.marks ? edu.marks + '%' : '-'}</td>
                    <td>${val(edu.year)}</td>
                </tr>`;
                });
                educationHtml += '</tbody></table></div>';
            } else {
                educationHtml = '<p class="text-muted text-center py-3 mb-0">No education records</p>';
            }

            // Experience HTML
            let experienceHtml = '';
            if (experience && experience.length > 0) {
                experienceHtml = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead class="thead-light"><tr><th>Company</th><th>Designation</th><th>Period</th></tr></thead><tbody>';
                experience.forEach(exp => {
                    let fromDate = exp.from_date ? new Date(exp.from_date).toLocaleDateString('en-GB', { month: 'short', year: 'numeric' }) : '-';
                    let toDate = exp.is_current ? 'Present' : (exp.to_date ? new Date(exp.to_date).toLocaleDateString('en-GB', { month: 'short', year: 'numeric' }) : '-');
                    experienceHtml += `<tr>
                    <td>${val(exp.company)} ${exp.is_current ? '<span class="badge badge-success">Current</span>' : ''}</td>
                    <td>${val(exp.designation)}</td>
                    <td>${fromDate} - ${toDate}</td>
                </tr>`;
                });
                experienceHtml += '</tbody></table></div>';
            } else {
                experienceHtml = '<p class="text-muted text-center py-3 mb-0">No experience records</p>';
            }

            // Assets HTML
            let assetsHtml = '';
            if (assets && assets.length > 0) {
                assetsHtml = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead class="thead-light"><tr><th>Asset</th><th>Type</th><th>Serial No</th><th>Status</th></tr></thead><tbody>';
                assets.forEach(asset => {
                    let statusClass = asset.status === 'assigned' ? 'success' : (asset.status === 'damaged' ? 'danger' : 'warning');
                    assetsHtml += `<tr>
                    <td>${val(asset.asset_name)}</td>
                    <td>${val(asset.asset_type)}</td>
                    <td>${val(asset.serial_no)}</td>
                    <td><span class="badge badge-${statusClass}">${val(asset.status)}</span></td>
                </tr>`;
                });
                assetsHtml += '</tbody></table></div>';
            } else {
                assetsHtml = '<p class="text-muted text-center py-3 mb-0">No assets assigned</p>';
            }

            // Main Profile HTML - SIMPLE AND CLEAN
            return `
            <div class="p-3">
                <!-- Profile Header -->
                <div class="row mb-4">
                    <div class="col-md-2 text-center">
                        ${employee.profile_image ?
                    `<img src="/storage/${employee.profile_image}" class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover;">` :
                    `<div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 36px;">
                                ${employee.first_name ? employee.first_name.charAt(0) : ''}${employee.last_name ? employee.last_name.charAt(0) : ''}
                            </div>`
                }
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-1">${employee.first_name || ''} ${employee.middle_name || ''} ${employee.last_name || ''}</h5>
                        <p class="mb-1">
                            <span class="badge badge-primary">${val(employee.employee_code)}</span>
                            <span class="badge badge-${employee.status === 'active' ? 'success' : 'secondary'}">${employee.status || '-'}</span>
                            ${employee.is_reporting_manager ? '<span class="badge badge-warning">Manager</span>' : ''}
                        </p>
                        <p class="mb-1">${employee.designation ? employee.designation.title : '-'}</p>
                        <p class="mb-1">${employee.department ? employee.department.name : '-'}</p>
                        <p class="mb-0">${employee.email || '-'}</p>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-6 mb-2">
                                <div class="border rounded p-2 text-center">
                                    <small>Joining</small><br>
                                    ${formatDate(employee.joining_date)}</strong>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="border rounded p-2 text-center">
                                    <small>Birth</small><br>
                                    ${formatDate(employee.birth_date)}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 text-center">
                                    <small>Age</small><br>
                                    ${age ? age + ' yrs' : '-'}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 text-center">
                                    <small>Experience</small><br>
                                    ${totalExperience || '-'}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Simple Navigation Tabs -->
                <ul class="nav nav-tabs nav-justified mb-3" id="profileTabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#work">Work</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#personal">Personal</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#salary">Salary</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#education">Education</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#experience">Experience</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#assets">Assets</a></li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Work Tab -->
                    <div class="tab-pane fade show active" id="work">
                        <div class="border rounded p-3">
                            <h6 class="font-weight-bold mb-3">Employment Details</h6>
                            <div class="row">
                                <div class="col-md-4 mb-2"><small>Department</small><br>${employee.department ? employee.department.name : '-'}</strong></div>
                                <div class="col-md-4 mb-2"><small>Designation</small><br>${employee.designation ? employee.designation.title : '-'}</strong></div>
                                <div class="col-md-4 mb-2"><small>Reporting To</small><br>${employee.reporting_to ? employee.reporting_to.full_name : '-'}</strong></div>
                                <div class="col-md-4 mb-2"><small>Employment Type</small><br>${val(employee.employment_type)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Level</small><br>${val(employee.employee_level)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Work Location</small><br>${val(employee.work_location)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Joining Date</small><br>${formatDate(employee.joining_date)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Confirmation</small><br>${formatDate(employee.confirmation_date)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Probation</small><br>${val(employee.probation_period)} months</strong></div>
                                <div class="col-md-4 mb-2"><small>Notice Period</small><br>${val(employee.notice_period)} days</strong></div>
                                <div class="col-md-4 mb-2"><small>CTC</small><br><strong class="">${formatCurrency(employee.ctc)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Experience Status</small><br>${val(employee.experience_status)}</strong></div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Tab -->
                    <div class="tab-pane fade" id="personal">
                        <div class="border rounded p-3">
                            <h6 class="font-weight-bold mb-3">Personal Details</h6>
                            <div class="row">
                                <div class="col-md-4 mb-2"><small>Full Name</small><br>${employee.first_name || ''} ${employee.middle_name || ''} ${employee.last_name || ''}</strong></div>
                                <div class="col-md-4 mb-2"><small>Father's Name</small><br>${val(employee.father_name)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Mother's Name</small><br>${val(employee.mother_name)}</strong></div>
                                <div class="col-md-4 mb-2"><small>CNIC</small><br>${val(employee.cnic)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Gender</small><br>${val(employee.gender)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Date of Birth</small><br>${formatDate(employee.birth_date)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Blood Group</small><br>${val(employee.blood_group)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Marital Status</small><br>${val(employee.marital_status)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Nationality</small><br>${val(employee.nationality)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Religion</small><br>${val(employee.religion)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Hobbies</small><br>${val(employee.hobbies)}</strong></div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Tab -->
                    <div class="tab-pane fade" id="contact">
                        <div class="border rounded p-3">
                            <h6 class="font-weight-bold mb-3">Contact Information</h6>
                            <div class="row mb-3">
                                <div class="col-md-4 mb-2"><small>Official Email</small><br>${val(employee.email)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Personal Email</small><br>${val(employee.personal_email)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Phone</small><br>${val(employee.phone)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Emergency Contact</small><br>${val(employee.emergency_contact_name)} (${val(employee.emergency_relation)})</strong></div>
                            </div>

                            <h6 class="font-weight-bold mt-3 mb-2">Present Address</h6>
                            <p class="border rounded p-2 bg-light">
                                ${val(employee.present_address_line1)}<br>
                                ${employee.present_address_line2 ? employee.present_address_line2 + '<br>' : ''}
                                ${val(employee.city)}, ${val(employee.state)}<br>
                                ${val(employee.country)} - ${val(employee.postal_code)}
                            </p>

                            <h6 class="font-weight-bold mt-3 mb-2">Permanent Address</h6>
                            <p class="border rounded p-2 bg-light">
                                ${val(employee.permanent_address_line1)}<br>
                                ${employee.permanent_address_line2 ? employee.permanent_address_line2 + '<br>' : ''}
                                ${val(employee.ity)}, ${val(employee.permanent_state)}<br>
                                ${val(employee.country)} - ${val(employee.postal_code)}
                            </p>
                        </div>
                    </div>

                    <!-- Salary Tab -->
                    <div class="tab-pane fade" id="salary">
                        <div class="border rounded p-3">
                            <h6 class="font-weight-bold mb-3">Salary & Bank Details</h6>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <div class="border rounded p-2">
                                        <small>Annual CTC</small><br>
                                        <strong class="text-success">${formatCurrency(employee.ctc)}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="border rounded p-2">
                                        <small>Basic Salary</small><br>
                                        ${formatCurrency(employee.basic_salary)}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3 mb-2"><small>HRA</small><br>${formatCurrency(employee.hra)}</strong></div>
                                <div class="col-md-3 mb-2"><small>DA</small><br>${formatCurrency(employee.da)}</strong></div>
                                <div class="col-md-3 mb-2"><small>Conveyance</small><br>${formatCurrency(employee.conveyance)}</strong></div>
                                <div class="col-md-3 mb-2"><small>Medical</small><br>${formatCurrency(employee.medical_allowance)}</strong></div>
                            </div>

                            <h6 class="font-weight-bold mt-3 mb-2">Bank Details</h6>
                            <div class="row">
                                <div class="col-md-4 mb-2"><small>Bank Name</small><br>${val(employee.bank_name)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Account Title</small><br>${val(employee.account_holder_name)}</strong></div>
                                <div class="col-md-4 mb-2"><small>Account Number</small><br>${val(employee.account_number)}</strong></div>
                                <div class="col-md-4 mb-2"><small>IBAN</small><br>${val(employee.iban)}</strong></div>
                            </div>
                        </div>
                    </div>

                    <!-- Education Tab -->
                    <div class="tab-pane fade" id="education">
                        <div class="border rounded p-3">
                            <h6 class="font-weight-bold mb-3">Education History</h6>
                            ${educationHtml}
                        </div>
                    </div>

                    <!-- Experience Tab -->
                    <div class="tab-pane fade" id="experience">
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="font-weight-bold mb-0">Work Experience</h6>
                                <span class="badge badge-info">Total: ${totalExperience || '-'}</span>
                            </div>
                            ${experienceHtml}
                        </div>
                    </div>

                    <!-- Assets Tab -->
                    <div class="tab-pane fade" id="assets">
                        <div class="border rounded p-3">
                            <h6 class="font-weight-bold mb-3">Assigned Assets</h6>
                            ${assetsHtml}
                        </div>
                    </div>
                </div>
            </div>
        `;
        } function deleteEmployee(id) {
            if (confirm('Are you sure you want to delete this employee?')) {
                $.ajax({
                    url: "admin/employees/delete/" + id ,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function () {
                        toastr.success('Employee deleted successfully');
                        location.reload();
                    }
                });
            }
        }

        // Open terminate modal
        function openTerminateModal(id) {
            $('#terminate_employee_id').val(id);
            $('#terminateModal').modal('show');
        }

        // Terminate employee
        function terminateEmployee() {
            let id = $('#terminate_employee_id').val();

            $.ajax({
                url: '/admin/employees/' + id + '/terminate',
                type: 'POST',
                data: {
                    exit_date: $('#exit_date_modal').val(),
                    exit_reason: $('#exit_reason').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#terminateModal').modal('hide');
                        location.reload();
                    }
                }
            });
        }

        // Reset form
        function resetForm() {
            $('#employeeForm')[0].reset();
            $('#employeeId').val('');
        }

        // Apply filters
        function applyFilters() {
            let params = new URLSearchParams(window.location.search);

            params.set('search', $('#search').val());
            params.set('department_id', $('#department_filter').val());
            params.set('designation_id', $('#designation_filter').val());
            params.set('employment_type', $('#employment_filter').val());
            params.set('status', $('#status_filter').val());

            window.location.search = params.toString();
        }

        // Reset filters
        function resetFilters() {
            window.location.search = '';
        }
    </script>
@endsection