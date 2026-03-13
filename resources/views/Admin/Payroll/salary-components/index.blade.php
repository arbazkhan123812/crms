@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-coins text-primary mr-2"></i>
                    Salary Components
                </h3>
                <p class="text-muted mb-0">Manage salary earning and deduction components</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payroll.salary-components.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Component
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
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Value Type</th>
                            <th>Default Value</th>
                            <th>Taxable</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($components as $component)
                        <tr>
                            <td><span class="badge badge-primary">{{ $component->code }}</span></td>
                            <td>{{ $component->name }}</td>
                            <td>
                                <span class="badge badge-{{ $component->type == 'earning' ? 'success' : 'danger' }}">
                                    {{ ucfirst($component->type) }}
                                </span>
                            </td>
                            <td>{{ ucfirst($component->value_type) }}</td>
                            <td>
                                @if($component->default_value)
                                    @if($component->value_type == 'percentage')
                                        {{ $component->default_value }}%
                                    @else
                                        PKR {{ number_format($component->default_value, 2) }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($component->is_taxable)
                                    <span class="badge badge-warning">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $component->is_active ? 'success' : 'secondary' }}">
                                    {{ $component->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.payroll.salary-components.edit', $component->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteComponent({{ $component->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-coins fa-3x text-muted mb-3"></i>
                                <h5>No Salary Components Found</h5>
                                <p class="text-muted">Click "Add Component" to create your first salary component</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $components->links() }}
        </div>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteComponent(id) {
    if(confirm('Are you sure you want to delete this salary component?')) {
        let form = document.getElementById('delete-form');
        form.action = '/admin/payroll/salary-components/' + id;
        form.submit();
    }
}
</script>
@endsection