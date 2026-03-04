@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>
                    Add Leave Type
                </h3>
                <p class="text-muted mb-0">Create a new leave type policy</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.leave-types.index') }}" class="btn btn-secondary btn-sm">
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
            <h6 class="mb-0"><i class="fas fa-info-circle text-primary mr-2"></i>Leave Type Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.leave-types.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="code" value="{{ old('code') }}" required>
                        <small class="text-muted">e.g., AL, SL, CL</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Days Per Year <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" name="days_per_year" value="{{ old('days_per_year', 0) }}" min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Carry Forward</label>
                        <input type="number" class="form-control form-control-sm" name="max_carry_forward" value="{{ old('max_carry_forward') }}" min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="carry_forward" id="carry_forward" value="1" {{ old('carry_forward') ? 'checked' : '' }}>
                            <label class="form-check-label" for="carry_forward">
                                Allow Carry Forward
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_paid" id="is_paid" value="1" checked>
                            <label class="form-check-label" for="is_paid">
                                Paid Leave
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="requires_approval" id="requires_approval" value="1" checked>
                            <label class="form-check-label" for="requires_approval">
                                Requires Approval
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="requires_document" id="requires_document" value="1">
                            <label class="form-check-label" for="requires_document">
                                Requires Document
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control form-control-sm" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-5">Save Leave Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('#carry_forward').change(function() {
    if($(this).is(':checked')) {
        $('input[name="max_carry_forward"]').prop('disabled', false);
    } else {
        $('input[name="max_carry_forward"]').prop('disabled', true).val('');
    }
});
</script>
@endsection