@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-file-invoice text-primary mr-2"></i>
                    Salary Templates
                </h3>
                <p class="text-muted mb-0">Manage designation-wise salary structures</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payroll.salary-templates.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Template
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
                            <th>Template Name</th>
                            <th>Designation</th>
                            <th>Basic</th>
                            <th>Gross Salary</th>
                            <th>Deductions</th>
                            <th>Net Salary</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                        <tr>
                            <td><strong>{{ $template->name }}</strong></td>
                            <td>{{ $template->designation->title }}</td>
                            <td>PKR {{ number_format($template->basic, 2) }}</td>
                            <td>PKR {{ number_format($template->gross_salary, 2) }}</td>
                            <td>PKR {{ number_format($template->total_deductions, 2) }}</td>
                            <td class="text-success font-weight-bold">PKR {{ number_format($template->net_salary, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $template->is_active ? 'success' : 'secondary' }}">
                                    {{ $template->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.payroll.salary-templates.edit', $template->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteTemplate({{ $template->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                <h5>No Salary Templates Found</h5>
                                <p class="text-muted">Click "Add Template" to create your first salary template</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $templates->links() }}
        </div>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteTemplate(id) {
    if(confirm('Are you sure you want to delete this salary template?')) {
        let form = document.getElementById('delete-form');
        form.action = '/admin/payroll/salary-templates/' + id;
        form.submit();
    }
}
</script>
@endsection