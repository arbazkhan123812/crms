@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-briefcase text-primary mr-2"></i>
                    Designations
                </h3>
                <p class="text-muted mb-0">Manage job titles and positions in your organization</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.designations.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Designation
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.designations.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="form-label mb-1 text-muted small">Search</label>
                            <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Designation title...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="form-label mb-1 text-muted small">Department</label>
                            <select class="form-control form-control-sm" name="department_id">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="form-label mb-1 text-muted small">Grade</label>
                            <select class="form-control form-control-sm" name="grade">
                                <option value="">All Grades</option>
                                @foreach($grades as $grade)
                                    @if($grade)
                                        <option value="{{ $grade }}" {{ request('grade') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="form-label mb-1 text-muted small">Status</label>
                            <select class="form-control form-control-sm" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-filter mr-1"></i> Apply Filters
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
                            <th>Code</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Grade</th>
                            <th>Salary Range</th>
                            <th>Employees</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($designations as $designation)
                        <tr>
                            <td><span class="badge badge-light">{{ $designation->code }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-tag text-info mr-2"></i>
                                    <div>
                                        <h6 class="mb-0" style="font-weight: 500;">{{ $designation->title }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $designation->department->name ?? 'N/A' }}</td>
                            <td>{{ $designation->grade ?? 'N/A' }}</td>
                            <td>
                                @if($designation->min_salary && $designation->max_salary)
                                    <span class="text-muted">PKR {{ number_format($designation->min_salary) }} - {{ number_format($designation->max_salary) }}</span>
                                @else
                                    <span class="text-muted">Not Set</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-light badge-pill">{{ $designation->employees_count ?? 0 }}</span>
                            </td>
                            <td>
                                @if($designation->is_active)
                                    <span class="badge badge-success badge-pill">Active</span>
                                @else
                                    <span class="badge badge-secondary badge-pill">Inactive</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.designations.show', $designation) }}" class="btn btn-sm btn-primary btn-link text-info">
                                    view
                                </a>
                                <a href="{{ route('admin.designations.edit', $designation) }}" class="btn btn-sm btn-primary btn-link text-warning">
                                    edit
                                </a>
                                <button type="button" class="btn btn-sm btn-primary text-danger" onclick="deleteDesignation({{ $designation->id }})">
                                    del
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-briefcase fa-3x mb-3"></i>
                                    <h5>No Designations Found</h5>
                                    <p>Click "Add Designation" to create your first designation</p>
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
                        Showing {{ $designations->firstItem() ?? 0 }} to {{ $designations->lastItem() ?? 0 }} of {{ $designations->total() }} entries
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="float-right">
                        {{ $designations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
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
    @method('DELETE')
</form>

<script>
function deleteDesignation(id) {
    if(confirm('Are you sure you want to delete this designation?')) {
        var form = document.getElementById('delete-form');
        form.action = '/admin/designations/' + id;
        form.submit();
    }
}
</script>
@endsection