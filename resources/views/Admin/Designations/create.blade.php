@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">

        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>
                    Add New Designation
                </h3>
                <p class="text-muted mb-0">Create a new job title with shift timings</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.designations.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light py-3">
            <ul class="nav nav-tabs card-header-tabs" id="designationTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab">
                        <i class="fas fa-info-circle mr-1"></i>Basic Info
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="shift-tab" data-toggle="tab" href="#shift" role="tab">
                        <i class="fas fa-clock mr-1"></i>Shift Timings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="advanced-tab" data-toggle="tab" href="#advanced" role="tab">
                        <i class="fas fa-cog mr-1"></i>Advanced Settings
                    </a>
                </li>
            </ul>
        </div>
        
        <form action="{{ route('admin.designations.store') }}" method="POST">
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
            <div class="card-body">
                <div class="tab-content" id="designationTabsContent">
                    <!-- Basic Info Tab -->
                    <div class="tab-pane fade show active" id="basic" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-control form-control-sm" name="department_id">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Designation Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code') }}" placeholder="e.g., SR-DEV" required>
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Designation Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title') }}" placeholder="e.g., Senior Developer" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Grade/Level</label>
                                <input type="text" class="form-control form-control-sm" name="grade" value="{{ old('grade') }}" placeholder="e.g., L3">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Min Salary (PKR)</label>
                                <input type="number" class="form-control form-control-sm" name="min_salary" value="{{ old('min_salary') }}" placeholder="50000">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Max Salary (PKR)</label>
                                <input type="number" class="form-control form-control-sm" name="max_salary" value="{{ old('max_salary') }}" placeholder="150000">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control form-control-sm" name="description" rows="3" placeholder="Job description...">{{ old('description') }}</textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="custom-control-label" for="is_active">Active Status</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shift Timings Tab -->
                    <div class="tab-pane fade" id="shift" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Shift Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control form-control-sm" name="shift_start_time" value="{{ old('shift_start_time', '09:00') }}" required>
                                <small class="text-muted">Regular check-in time</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Shift End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control form-control-sm" name="shift_end_time" value="{{ old('shift_end_time', '18:00') }}" required>
                                <small class="text-muted">Regular check-out time</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Break Duration (minutes)</label>
                                <input type="number" class="form-control form-control-sm" name="break_duration" value="{{ old('break_duration', 60) }}" min="0" max="180">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Late Threshold (minutes)</label>
                                <input type="number" class="form-control form-control-sm" name="late_threshold" value="{{ old('late_threshold', 15) }}" min="0">
                                <small class="text-muted">Minutes after start time considered late</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Early Exit Threshold (minutes)</label>
                                <input type="number" class="form-control form-control-sm" name="early_exit_threshold" value="{{ old('early_exit_threshold', 15) }}" min="0">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Overtime Threshold (minutes)</label>
                                <input type="number" class="form-control form-control-sm" name="overtime_threshold" value="{{ old('overtime_threshold', 0) }}" min="0">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Grace Period (minutes)</label>
                                <input type="number" class="form-control form-control-sm" name="grace_period_minutes" value="{{ old('grace_period_minutes', 0) }}" min="0">
                                <small class="text-muted">Extra time before marking late</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Overtime Rate (x)</label>
                                <input type="number" step="0.1" class="form-control form-control-sm" name="overtime_rate" value="{{ old('overtime_rate', 1.5) }}" min="1">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Working Days</label>
                                <div class="row">
                                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                    <div class="col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" 
                                                   id="day_{{ $day }}" 
                                                   name="working_days[]" 
                                                   value="{{ $day }}"
                                                   {{ in_array($day, old('working_days', ['monday','tuesday','wednesday','thursday','friday'])) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="day_{{ $day }}">
                                                {{ ucfirst($day) }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_night_shift" name="is_night_shift" value="1">
                                    <label class="custom-control-label" for="is_night_shift">Night Shift</label>
                                </div>
                                <small class="text-muted">Check if shift ends next day</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Settings Tab -->
                    <div class="tab-pane fade" id="advanced" role="tabpanel">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="has_flexible_timing" name="has_flexible_timing" value="1">
                                    <label class="custom-control-label" for="has_flexible_timing">Enable Flexible Timing</label>
                                </div>
                                <small class="text-muted">Allow employees to choose their check-in/out within a range</small>
                            </div>
                            
                            <div class="col-md-6 mb-3 flexible-field" style="display: none;">
                                <label class="form-label">Flexible Check-in From</label>
                                <input type="time" class="form-control form-control-sm" name="flexible_start_from" value="{{ old('flexible_start_from', '08:00') }}">
                            </div>
                            
                            <div class="col-md-6 mb-3 flexible-field" style="display: none;">
                                <label class="form-label">Flexible Check-in To</label>
                                <input type="time" class="form-control form-control-sm" name="flexible_start_to" value="{{ old('flexible_start_to', '10:00') }}">
                            </div>
                            
                            <div class="col-md-6 mb-3 flexible-field" style="display: none;">
                                <label class="form-label">Flexible Check-out From</label>
                                <input type="time" class="form-control form-control-sm" name="flexible_end_from" value="{{ old('flexible_end_from', '17:00') }}">
                            </div>
                            
                            <div class="col-md-6 mb-3 flexible-field" style="display: none;">
                                <label class="form-label">Flexible Check-out To</label>
                                <input type="time" class="form-control form-control-sm" name="flexible_end_to" value="{{ old('flexible_end_to', '19:00') }}">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <hr>
                                <h6 class="text-primary">Half Day Settings</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Half Day Start Time</label>
                                <input type="time" class="form-control form-control-sm" name="half_day_start_time" value="{{ old('half_day_start_time', '09:00') }}">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Half Day End Time</label>
                                <input type="time" class="form-control form-control-sm" name="half_day_end_time" value="{{ old('half_day_end_time', '13:00') }}">
                            </div>
                            
                            <div class="col-md-12">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_weekend_included" name="is_weekend_included" value="1">
                                    <label class="custom-control-label" for="is_weekend_included">Include Weekends in Working Days</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer text-right">
                <a href="{{ route('admin.designations.index') }}" class="btn btn-light btn-sm px-4">Cancel</a>
                <button type="submit" class="btn btn-primary btn-sm px-5">Save Designation</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle flexible timing fields
    $('#has_flexible_timing').change(function() {
        if ($(this).is(':checked')) {
            $('.flexible-field').show();
        } else {
            $('.flexible-field').hide();
        }
    });
});
</script>
@endsection