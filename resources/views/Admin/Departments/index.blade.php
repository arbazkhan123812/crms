@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-sitemap text-primary mr-2"></i>
                    Departments
                </h3>
                <p class="text-muted mb-0">Manage your organization departments and hierarchy</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Department
                </a>
                <a href="{{ route('admin.departments.hierarchy') }}" class="btn btn-outline-secondary btn-sm ml-2">
                    <i class="fas fa-project-diagram mr-1"></i> View Hierarchy
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.departments.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label class="form-label mb-1 text-muted small">Search</label>
                            <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Department name...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="form-label mb-1 text-muted small">Status</label>
                            <select class="form-control form-control-sm" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-filter mr-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-light btn-sm ml-2">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                    <div class="col-md-2 d-flex align-items-end justify-content-end">
                        <span class="text-muted small">
                            <i class="fas fa-info-circle mr-1"></i>
                            Total: {{ $departments->total() }} departments
                        </span>
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
                            <th>Department Name</th>
                            <th>Parent Department</th>
                            <th>Manager</th>
                            <th>Employees</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                        <tr>
                            <td><span class="badge badge-light">{{ $department->code }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-folder text-warning mr-2"></i>
                                    <div>
                                        <h6 class="mb-0" style="font-weight: 500;">{{ $department->name }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($department->parent)
                                    <span class="text-muted">{{ $department->parent->name }}</span>
                                @else
                                    <span class="badge badge-light">Root</span>
                                @endif
                            </td>
                            <td>
                                @if($department->manager)
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center mr-2" style="width: 24px; height: 24px; font-size: 11px;">
                                            {{ substr($department->manager->first_name, 0, 1) }}{{ substr($department->manager->last_name, 0, 1) }}
                                        </div>
                                        <span>{{ $department->manager->full_name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-light badge-pill">{{ $department->employees_count ?? 0 }}</span>
                            </td>
                            <td>
                                @if($department->is_active)
                                    <span class="badge badge-success badge-pill">Active</span>
                                @else
                                    <span class="badge badge-secondary badge-pill">Inactive</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-sm btn-link text-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-sm btn-link text-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-link text-danger" onclick="deleteDepartment({{ $department->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-sitemap fa-3x mb-3"></i>
                                    <h5>No Departments Found</h5>
                                    <p>Click "Add Department" to create your first department</p>
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
                        Showing {{ $departments->firstItem() ?? 0 }} to {{ $departments->lastItem() ?? 0 }} of {{ $departments->total() }} entries
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="float-right">
                        {{ $departments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteDepartment(id) {
    if(confirm('Are you sure you want to delete this department?')) {
        var form = document.getElementById('delete-form');
        form.action = '/admin/departments/' + id;
        form.submit();
    }
}
</script>
@endsection