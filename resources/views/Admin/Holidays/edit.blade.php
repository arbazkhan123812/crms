@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-edit text-primary mr-2"></i>
                    Edit Holiday
                </h3>
                <p class="text-muted mb-0">{{ $holiday->name }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.holidays.index') }}" class="btn btn-secondary btn-sm">
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
            <h6 class="mb-0"><i class="fas fa-info-circle text-primary mr-2"></i>Holiday Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.holidays.update', $holiday->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Holiday Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="name" value="{{ old('name', $holiday->name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="date" value="{{ old('date', $holiday->date->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="type" required>
                            <option value="national" {{ old('type', $holiday->type) == 'national' ? 'selected' : '' }}>National</option>
                            <option value="religious" {{ old('type', $holiday->type) == 'religious' ? 'selected' : '' }}>Religious</option>
                            <option value="company" {{ old('type', $holiday->type) == 'company' ? 'selected' : '' }}>Company</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_recurring" id="is_recurring" value="1" {{ old('is_recurring', $holiday->is_recurring) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_recurring">
                                Recurring Yearly
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $holiday->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea class="form-control form-control-sm" name="description" rows="3">{{ old('description', $holiday->description) }}</textarea>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-5">Update Holiday</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection