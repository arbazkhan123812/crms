@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-edit text-primary mr-2"></i>
                    Edit Designation
                </h3>
                <p class="text-muted mb-0">Update designation information and shift timings</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.designations.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

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
        
        <form action="{{ route('admin.designations.update', $designation) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card-body">
                <div class="tab-content" id="designationTabsContent">
                    
                    <!-- Basic Info Tab -->
                    <div class="tab-pane fade show active" id="basic" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Designation Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code', $designation->code) }}" required>
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Designation Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title', $designation->title) }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-control form-control-sm" name="department_id">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id', $designation->department_id) == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Grade/Level</label>
                                <input type="text" class="form-control form-control-sm" name="grade" 
                                       value="{{ old('grade', $designation->grade) }}" placeholder="e.g., L3">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Min Salary (PKR)</label>
                                <input type="number" class="form-control form-control-sm" name="min_salary" 
                                       value="{{ old('min_salary', $designation->min_salary) }}" placeholder="50000">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Salary (PKR)</label>
                                <input type="number" class="form-control form-control-sm" name="max_salary" 
                                       value="{{ old('max_salary', $designation->max_salary) }}" placeholder="150000">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control form-control-sm" name="description" rows="3">{{ old('description', $designation->description) }}</textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                        {{ old('is_active', $designation->is_active) ? 'checked' : '' }}>
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
                                <input type="time" class="form-control form-control-sm" name="shift_start_time" 
                                       value="{{ old('shift_start_time', $designation->shift_start_time ? \Carbon\Carbon::parse($designation->shift_start_time)->format('H:i') : '09:00') }}" required>
                                <small class="text-muted">Regular check-in time</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Shift End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control form-control-sm" name="shift_end_time" 
                                       value="{{ old('shift_end_time', $designation->shift_end_time ? \Carbon\Carbon::parse($designation->shift_end_time)->format('H:i') : '18:00') }}" required>
                                <small class="text-muted">Regular check-out time</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Break Duration (minutes)</label>
                                <input type="number" class="form-control form-control-sm" name="break_duration" 
                                       value="{{ old('break_duration', $designation->break_duration ?? 60) }}" min="0" max="180">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Late Threshold (minutes)</label>
                                <input type="number" class="form-control form-control-sm" name="late_threshold" 
                                       value="{{ old('late_threshold', $designation->late_threshold ?? 15) }}" min="0">
                                <small class="text-muted">Minutes after start time considered late</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Early Exit Threshold (minutes)</label>
                                <input type="number" class="form-control form-control-sm" name="early_exit_threshold" 
                                       value="{{ old('early_exit_threshold', $designation->early_exit_threshold ?? 15) }}" min="0">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Overtime Threshold (minutes)</label>
                                <input type="number" class="form-control form-control-sm" name="overtime_threshold" 
                                       value="{{ old('overtime_threshold', $designation->overtime_threshold ?? 0) }}" min="0">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Grace Period (minutes)</label>
                                <input type="number" class="form-control form-control-sm" name="grace_period_minutes" 
                                       value="{{ old('grace_period_minutes', $designation->grace_period_minutes ?? 0) }}" min="0">
                                <small class="text-muted">Extra time before marking late</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Overtime Rate (x)</label>
                                <input type="number" step="0.1" class="form-control form-control-sm" name="overtime_rate" 
                                       value="{{ old('overtime_rate', $designation->overtime_rate ?? 1.5) }}" min="1">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Working Days</label>
                                <div class="row">
                                    @php
                                        $workingDays = old('working_days', $designation->working_days ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
                                        if (is_string($workingDays)) {
                                            $workingDays = json_decode($workingDays);
                                        }
                                    @endphp
                                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                    <div class="col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" 
                                                   id="day_{{ $day }}" 
                                                   name="working_days[]" 
                                                   value="{{ $day }}"
                                                   {{ is_array($workingDays) && in_array($day, $workingDays) ? 'checked' : '' }}>
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
                                    <input type="checkbox" class="custom-control-input" id="is_night_shift" name="is_night_shift" value="1"
                                        {{ old('is_night_shift', $designation->is_night_shift) ? 'checked' : '' }}>
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
                                    <input type="checkbox" class="custom-control-input" id="has_flexible_timing" name="has_flexible_timing" value="1"
                                        {{ old('has_flexible_timing', $designation->has_flexible_timing) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="has_flexible_timing">Enable Flexible Timing</label>
                                </div>
                                <small class="text-muted">Allow employees to choose their check-in/out within a range</small>
                            </div>
                            
                            <div class="col-md-6 mb-3 flexible-field" style="{{ old('has_flexible_timing', $designation->has_flexible_timing) ? '' : 'display: none;' }}">
                                <label class="form-label">Flexible Check-in From</label>
                                <input type="time" class="form-control form-control-sm" name="flexible_start_from" 
                                       value="{{ old('flexible_start_from', $designation->flexible_start_from ? \Carbon\Carbon::parse($designation->flexible_start_from)->format('H:i') : '08:00') }}">
                            </div>
                            
                            <div class="col-md-6 mb-3 flexible-field" style="{{ old('has_flexible_timing', $designation->has_flexible_timing) ? '' : 'display: none;' }}">
                                <label class="form-label">Flexible Check-in To</label>
                                <input type="time" class="form-control form-control-sm" name="flexible_start_to" 
                                       value="{{ old('flexible_start_to', $designation->flexible_start_to ? \Carbon\Carbon::parse($designation->flexible_start_to)->format('H:i') : '10:00') }}">
                            </div>
                            
                            <div class="col-md-6 mb-3 flexible-field" style="{{ old('has_flexible_timing', $designation->has_flexible_timing) ? '' : 'display: none;' }}">
                                <label class="form-label">Flexible Check-out From</label>
                                <input type="time" class="form-control form-control-sm" name="flexible_end_from" 
                                       value="{{ old('flexible_end_from', $designation->flexible_end_from ? \Carbon\Carbon::parse($designation->flexible_end_from)->format('H:i') : '17:00') }}">
                            </div>
                            
                            <div class="col-md-6 mb-3 flexible-field" style="{{ old('has_flexible_timing', $designation->has_flexible_timing) ? '' : 'display: none;' }}">
                                <label class="form-label">Flexible Check-out To</label>
                                <input type="time" class="form-control form-control-sm" name="flexible_end_to" 
                                       value="{{ old('flexible_end_to', $designation->flexible_end_to ? \Carbon\Carbon::parse($designation->flexible_end_to)->format('H:i') : '19:00') }}">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <hr>
                                <h6 class="text-primary">Half Day Settings</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Half Day Start Time</label>
                                <input type="time" class="form-control form-control-sm" name="half_day_start_time" 
                                       value="{{ old('half_day_start_time', $designation->half_day_start_time ? \Carbon\Carbon::parse($designation->half_day_start_time)->format('H:i') : '09:00') }}">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Half Day End Time</label>
                                <input type="time" class="form-control form-control-sm" name="half_day_end_time" 
                                       value="{{ old('half_day_end_time', $designation->half_day_end_time ? \Carbon\Carbon::parse($designation->half_day_end_time)->format('H:i') : '13:00') }}">
                            </div>
                            
                            <div class="col-md-12">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_weekend_included" name="is_weekend_included" value="1"
                                        {{ old('is_weekend_included', $designation->is_weekend_included) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_weekend_included">Include Weekends in Working Days</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer text-right">
                <a href="{{ route('admin.designations.index') }}" class="btn btn-light btn-sm px-4">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary btn-sm px-5 ml-2">
                    <i class="fas fa-save mr-1"></i> Update Designation
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.nav-tabs .nav-link {
    color: #495057;
    font-weight: 500;
    border: none;
    padding: 8px 16px;
}

.nav-tabs .nav-link.active {
    color: #4e73df;
    border-bottom: 2px solid #4e73df;
    background: none;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 4px;
    font-size: 13px;
}

.form-control-sm {
    font-size: 13px;
    padding: 4px 8px;
    height: 32px;
}

.card {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.card-header {
    border-bottom: 1px solid #e9ecef;
    background-color: #f8f9fc;
    padding: 12px 16px;
}
</style>

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
    
    // Auto-calculate overtime threshold from grace period
    $('#grace_period_minutes').on('input', function() {
        let grace = parseInt($(this).val()) || 0;
        let overtime = $('#overtime_threshold').val();
        
        if (overtime < grace) {
            $('#overtime_threshold').val(grace);
        }
    });
    
    // Validate shift end time is after start time
    $('input[name="shift_start_time"], input[name="shift_end_time"]').on('change', function() {
        let start = $('input[name="shift_start_time"]').val();
        let end = $('input[name="shift_end_time"]').val();
        
        if (start && end && end <= start && !$('#is_night_shift').is(':checked')) {
            toastr.warning('End time should be after start time. For night shifts, check the "Night Shift" option.');
        }
    });
});
</script>
@endsection