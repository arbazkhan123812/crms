@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-edit text-primary mr-2"></i>
                    Edit Salary Component
                </h3>
                <p class="text-muted mb-0">{{ $component->name }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payroll.salary-components.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-light py-3">
            <h6 class="mb-0"><i class="fas fa-info-circle text-primary mr-2"></i>Component Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payroll.salary-components.update', $component->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Component Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="name" value="{{ old('name', $component->name) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Component Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="code" value="{{ old('code', $component->code) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="type" required>
                            <option value="earning" {{ old('type', $component->type) == 'earning' ? 'selected' : '' }}>Earning</option>
                            <option value="deduction" {{ old('type', $component->type) == 'deduction' ? 'selected' : '' }}>Deduction</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Value Type <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="value_type" required>
                            <option value="fixed" {{ old('value_type', $component->value_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            <option value="percentage" {{ old('value_type', $component->value_type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Default Value</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="default_value" value="{{ old('default_value', $component->default_value) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_taxable" name="is_taxable" value="1" {{ old('is_taxable', $component->is_taxable) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_taxable">Taxable Component</label>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $component->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-5">Update Component</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('select[name="type"]').change(function() {
    if($(this).val() == 'deduction') {
        $('#is_taxable').prop('checked', false).prop('disabled', true);
    } else {
        $('#is_taxable').prop('disabled', false);
    }
});

if($('select[name="type"]').val() == 'deduction') {
    $('#is_taxable').prop('disabled', true);
}
</script>
@endsection