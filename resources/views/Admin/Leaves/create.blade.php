@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>
                    Apply Leave
                </h3>
                <p class="text-muted mb-0">Submit a new leave application</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.leaves.index') }}" class="btn btn-secondary btn-sm">
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
            <h6 class="mb-0"><i class="fas fa-info-circle text-primary mr-2"></i>Leave Application Form</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.leaves.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Employee <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="leave_type_id" required>
                            <option value="">Select Leave Type</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->days_per_year }} days/year)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="start_date" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="end_date" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Half Day</label>
                        <select class="form-control form-control-sm" name="half_day">
                            <option value="none">Full Day</option>
                            <option value="first_half">First Half</option>
                            <option value="second_half">Second Half</option>
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm" name="reason" rows="4" required></textarea>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Supporting Document (Optional)</label>
                        <input type="file" class="form-control-file" name="document" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Max 2MB (PDF, JPG, PNG)</small>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-5">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('input[name="start_date"], input[name="end_date"]').change(function() {
    let start = $('input[name="start_date"]').val();
    let end = $('input[name="end_date"]').val();
    
    if(start && end) {
        let startDate = new Date(start);
        let endDate = new Date(end);
        let diffTime = Math.abs(endDate - startDate);
        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        let weekends = 0;
        for(let i = 0; i < diffDays; i++) {
            let date = new Date(startDate);
            date.setDate(date.getDate() + i);
            if(date.getDay() === 0 || date.getDay() === 6) weekends++;
        }
        
        let workingDays = diffDays - weekends;
        $('#dayCount').remove();
        $('.form-group:last').after('<div id="dayCount" class="alert alert-info mt-2"><i class="fas fa-info-circle mr-2"></i>Selected period: ' + diffDays + ' total days, ' + workingDays + ' working days (excluding weekends)</div>');
    }
});
</script>
@endsection