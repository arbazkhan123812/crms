@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>
                    Assign Salary to Employee
                </h3>
                <p class="text-muted mb-0">Set up salary structure for an employee</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payroll.employee-salaries.index') }}" class="btn btn-secondary btn-sm">
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
            <h6 class="mb-0"><i class="fas fa-info-circle text-primary mr-2"></i>Salary Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payroll.employee-salaries.store') }}" method="POST" id="salaryForm">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Employee <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="employee_id" id="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" data-designation="{{ $employee->designation_id }}">
                                    {{ $employee->full_name }} ({{ $employee->employee_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Salary Template</label>
                        <select class="form-control form-control-sm" name="salary_template_id" id="salary_template_id">
                            <option value="">Custom (Manual Entry)</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" data-designation="{{ $template->designation_id }}">
                                    {{ $template->name }} - PKR {{ number_format($template->net_salary, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select template to auto-fill values</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Effective From <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="effective_from" value="{{ old('effective_from', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-12">
                        <hr>
                        <h6 class="text-primary">Earnings</h6>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Basic Salary <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="basic" id="basic" value="{{ old('basic') }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">HRA</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="hra" id="hra" value="{{ old('hra') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">DA</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="da" id="da" value="{{ old('da') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Conveyance</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="conveyance" id="conveyance" value="{{ old('conveyance') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Medical Allowance</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="medical" id="medical" value="{{ old('medical') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Special Allowance</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="special" id="special" value="{{ old('special') }}">
                    </div>

                    <div class="col-12">
                        <hr>
                        <h6 class="text-danger">Deductions</h6>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">PF</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="pf" id="pf" value="{{ old('pf') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">ESI</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="esi" id="esi" value="{{ old('esi') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Professional Tax</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="pt" id="pt" value="{{ old('pt') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">TDS</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="tds" id="tds" value="{{ old('tds') }}">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded text-center">
                            <small class="text-muted">Gross Salary</small>
                            <h4 class="mb-0 text-primary" id="grossSalary">0.00</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded text-center">
                            <small class="text-muted">Total Deductions</small>
                            <h4 class="mb-0 text-danger" id="totalDeductions">0.00</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-success p-3 rounded text-center">
                            <small class="text-white">Net Salary</small>
                            <h4 class="mb-0 text-white" id="netSalary">0.00</h4>
                        </div>
                    </div>
                </div>

                <div class="text-right mt-4">
                    <button type="submit" class="btn btn-primary px-5">Assign Salary</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculateTotals() {
    let gross = 0;
    $('input[name="basic"], input[name="hra"], input[name="da"], input[name="conveyance"], input[name="medical"], input[name="special"]').each(function() {
        gross += parseFloat($(this).val()) || 0;
    });

    let deductions = 0;
    $('input[name="pf"], input[name="esi"], input[name="pt"], input[name="tds"]').each(function() {
        deductions += parseFloat($(this).val()) || 0;
    });

    let net = gross - deductions;

    $('#grossSalary').text(gross.toFixed(2));
    $('#totalDeductions').text(deductions.toFixed(2));
    $('#netSalary').text(net.toFixed(2));
}

$('input').on('keyup change', calculateTotals);
calculateTotals();

$('#salary_template_id').change(function() {
    let templateId = $(this).val();
    if(!templateId) return;

    $.get('/admin/payroll/salary-templates/' + templateId, function(template) {
        $('#basic').val(template.basic);
        $('#hra').val(template.hra);
        $('#da').val(template.da);
        $('#conveyance').val(template.conveyance);
        $('#medical').val(template.medical);
        $('#special').val(template.special);
        $('#pf').val(template.pf);
        $('#esi').val(template.esi);
        $('#pt').val(template.pt);
        $('#tds').val(template.tds);
        calculateTotals();
    });
});
</script>
@endsection