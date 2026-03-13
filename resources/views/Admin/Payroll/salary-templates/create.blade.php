@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>
                    Create Salary Template
                </h3>
                <p class="text-muted mb-0">Create a new salary structure for a designation</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payroll.salary-templates.index') }}" class="btn btn-secondary btn-sm">
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
            <ul class="nav nav-tabs card-header-tabs" id="salaryTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab">
                        <i class="fas fa-info-circle mr-1"></i>Basic Info
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="earnings-tab" data-toggle="tab" href="#earnings" role="tab">
                        <i class="fas fa-plus-circle mr-1"></i>Earnings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="deductions-tab" data-toggle="tab" href="#deductions" role="tab">
                        <i class="fas fa-minus-circle mr-1"></i>Deductions
                    </a>
                </li>
            </ul>
        </div>
        
        <form action="{{ route('admin.payroll.salary-templates.store') }}" method="POST" id="salaryForm">
            @csrf
            <div class="card-body">
                <div class="tab-content" id="salaryTabsContent">
                    
                    <!-- Basic Info Tab -->
                    <div class="tab-pane fade show active" id="basic" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Template Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="name" value="{{ old('name') }}" required>
                                <small class="text-muted">e.g., Software Engineer Level 1</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Designation <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" name="designation_id" required>
                                    <option value="">Select Designation</option>
                                    @foreach($designations as $designation)
                                        <option value="{{ $designation->id }}">{{ $designation->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Earnings Tab -->
                    <div class="tab-pane fade" id="earnings" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Basic Salary <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="basic" value="{{ old('basic') }}" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">HRA</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="hra" value="{{ old('hra') }}">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">DA</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="da" value="{{ old('da') }}">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Conveyance</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="conveyance" value="{{ old('conveyance') }}">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Medical Allowance</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="medical" value="{{ old('medical') }}">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Special Allowance</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="special" value="{{ old('special') }}">
                            </div>
                        </div>

                        <div class="card border mt-3">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Other Earnings</h6>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addEarning()">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                            <div class="card-body" id="earnings-container">
                                <!-- Dynamic earnings will be added here -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Deductions Tab -->
                    <div class="tab-pane fade" id="deductions" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">PF</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="pf" value="{{ old('pf') }}">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ESI</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="esi" value="{{ old('esi') }}">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Professional Tax</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="pt" value="{{ old('pt') }}">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">TDS</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="tds" value="{{ old('tds') }}">
                            </div>
                        </div>

                        <div class="card border mt-3">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Other Deductions</h6>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addDeduction()">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                            <div class="card-body" id="deductions-container">
                                <!-- Dynamic deductions will be added here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer text-right">
                <div class="row">
                    <div class="col-md-4">
                        <div class="bg-light p-2 rounded">
                            <small class="text-muted">Gross Salary</small>
                            <h5 class="mb-0" id="gross-salary">0.00</h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light p-2 rounded">
                            <small class="text-muted">Total Deductions</small>
                            <h5 class="mb-0" id="total-deductions">0.00</h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-primary text-white p-2 rounded">
                            <small>Net Salary</small>
                            <h5 class="mb-0" id="net-salary">0.00</h5>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary px-5 mt-3">Save Template</button>
            </div>
        </form>
    </div>
</div>

<script>
let earningIndex = 0;
let deductionIndex = 0;

function addEarning() {
    let html = `
        <div class="row mb-2 earning-item">
            <div class="col-md-5">
                <input type="text" class="form-control form-control-sm" name="other_earnings[${earningIndex}][name]" placeholder="Earning Name">
            </div>
            <div class="col-md-4">
                <input type="number" step="0.01" class="form-control form-control-sm" name="other_earnings[${earningIndex}][amount]" placeholder="Amount">
            </div>
            <div class="col-md-2">
                <select class="form-control form-control-sm" name="other_earnings[${earningIndex}][type]">
                    <option value="fixed">Fixed</option>
                    <option value="percentage">Percentage</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.row').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    $('#earnings-container').append(html);
    earningIndex++;
}

function addDeduction() {
    let html = `
        <div class="row mb-2 deduction-item">
            <div class="col-md-5">
                <input type="text" class="form-control form-control-sm" name="other_deductions[${deductionIndex}][name]" placeholder="Deduction Name">
            </div>
            <div class="col-md-4">
                <input type="number" step="0.01" class="form-control form-control-sm" name="other_deductions[${deductionIndex}][amount]" placeholder="Amount">
            </div>
            <div class="col-md-2">
                <select class="form-control form-control-sm" name="other_deductions[${deductionIndex}][type]">
                    <option value="fixed">Fixed</option>
                    <option value="percentage">Percentage</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.row').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    $('#deductions-container').append(html);
    deductionIndex++;
}

function calculateTotals() {
    let gross = parseFloat($('input[name="basic"]').val()) || 0;
    gross += parseFloat($('input[name="hra"]').val()) || 0;
    gross += parseFloat($('input[name="da"]').val()) || 0;
    gross += parseFloat($('input[name="conveyance"]').val()) || 0;
    gross += parseFloat($('input[name="medical"]').val()) || 0;
    gross += parseFloat($('input[name="special"]').val()) || 0;

    $('.earning-item').each(function() {
        gross += parseFloat($(this).find('input[name*="[amount]"]').val()) || 0;
    });

    let deductions = parseFloat($('input[name="pf"]').val()) || 0;
    deductions += parseFloat($('input[name="esi"]').val()) || 0;
    deductions += parseFloat($('input[name="pt"]').val()) || 0;
    deductions += parseFloat($('input[name="tds"]').val()) || 0;

    $('.deduction-item').each(function() {
        deductions += parseFloat($(this).find('input[name*="[amount]"]').val()) || 0;
    });

    let net = gross - deductions;

    $('#gross-salary').text(gross.toFixed(2));
    $('#total-deductions').text(deductions.toFixed(2));
    $('#net-salary').text(net.toFixed(2));
}

$('input').on('keyup change', calculateTotals);
</script>
@endsection