@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>
                    Add Salary Component
                </h3>
                <p class="text-muted mb-0">Create a new salary component</p>
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
            <form action="{{ route('admin.payroll.salary-components.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Component Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="name" value="{{ old('name') }}" required>
                        <small class="text-muted">e.g., Basic Salary, HRA, PF, etc.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Component Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="code" value="{{ old('code') }}" required>
                        <small class="text-muted">e.g., BASIC, HRA, PF</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="type" required>
                            <option value="">Select Type</option>
                            <option value="earning" {{ old('type') == 'earning' ? 'selected' : '' }}>Earning</option>
                            <option value="deduction" {{ old('type') == 'deduction' ? 'selected' : '' }}>Deduction</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Value Type <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="value_type" required>
                            <option value="">Select Value Type</option>
                            <option value="fixed" {{ old('value_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            <option value="percentage" {{ old('value_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Default Value</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="default_value" value="{{ old('default_value') }}">
                        <small class="text-muted">Leave empty if not applicable</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_taxable" name="is_taxable" value="1" {{ old('is_taxable') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_taxable">Taxable Component</label>
                        </div>
                        <small class="text-muted">Check if this component is subject to tax</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-5">Save Component</button>
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
</script>
@endsection