@extends('layout.app')

@section('content')
<div class="content">
    <!-- PAGE HEADER -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-building text-primary mr-2"></i>
                    Company Profile
                </h3>
                <p class="text-muted mb-0">Manage your company information and settings</p>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-primary btn-sm" onclick="openEditModal()">
                    <i class="fas fa-edit mr-1"></i> Edit Profile
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="company-logo-wrapper mb-3 position-relative" id="logoContainer">
                        @if($company->logo)
                            <img src="{{ asset('storage/'.$company->logo) }}" alt="Company Logo" class="img-fluid rounded-circle" id="companyLogo" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #4e73df;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px; border: 3px solid #4e73df;">
                                <span style="font-size: 48px;">{{ substr($company->name, 0, 2) }}</span>
                            </div>
                        @endif
                        
                        <div class="upload-overlay" onclick="document.getElementById('logoUpload').click()">
                            <i class="fas fa-camera"></i>
                        </div>
                        
                        <form id="logoUploadForm" enctype="multipart/form-data" style="display: none;">
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
                            <input type="file" id="logoUpload" name="logo" accept="image/*" onchange="uploadLogo(this)">
                        </form>
                    </div>
                    
                    <h4 class="mb-1" id="companyName">{{ $company->name }}</h4>
                    <p class="text-muted mb-2" id="companyEmail">{{ $company->email }}</p>
                    
                    <span class="badge badge-{{ $company->status == 'active' ? 'success' : 'secondary' }} badge-pill px-3 py-1" id="companyStatus">
                        {{ ucfirst($company->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-info-circle text-primary mr-2"></i>
                        Company Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6 class="text-primary mb-3" style="font-weight: 500;">
                                <i class="fas fa-phone-alt mr-2"></i>Contact Information
                            </h6>
                            
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Phone Number</label>
                                <p class="font-weight-medium mb-0" id="companyPhone">{{ $company->phone ?? 'Not provided' }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Website</label>
                                <p class="font-weight-medium mb-0" id="companyWebsite">
                                    @if($company->website)
                                        <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                                    @else
                                        Not provided
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <h6 class="text-primary mb-3" style="font-weight: 500;">
                                <i class="fas fa-file-invoice mr-2"></i>Tax & Registration
                            </h6>
                            
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Tax ID / GST</label>
                                <p class="font-weight-medium mb-0" id="companyTaxId">{{ $company->tax_id ?? 'Not provided' }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Registration Number</label>
                                <p class="font-weight-medium mb-0" id="companyRegNo">{{ $company->registration_number ?? 'Not provided' }}</p>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="col-12">
                            <h6 class="text-primary mb-3" style="font-weight: 500;">
                                <i class="fas fa-map-marker-alt mr-2"></i>Address
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Address</label>
                                    <p class="font-weight-medium mb-0" id="companyAddress">{{ $company->address ?? 'Not provided' }}</p>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">City</label>
                                    <p class="font-weight-medium mb-0" id="companyCity">{{ $company->city ?? 'Not provided' }}</p>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small mb-1">State</label>
                                    <p class="font-weight-medium mb-0" id="companyState">{{ $company->state ?? 'Not provided' }}</p>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small mb-1">Country</label>
                                    <p class="font-weight-medium mb-0" id="companyCountry">{{ $company->country ?? 'Not provided' }}</p>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small mb-1">Postal Code</label>
                                    <p class="font-weight-medium mb-0" id="companyPostalCode">{{ $company->postal_code ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-bg-primary rounded-circle mr-3">
                                    <i class="fas fa-users fa-lg text-white"></i>
                                </div>
                                <div>
                                    <p class="card-text mb-1">Total Employees</p>
                                    <h4 class="card-title mb-0">{{ $totalEmployees ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-bg-success rounded-circle mr-3">
                                    <i class="fas fa-sitemap fa-lg text-white"></i>
                                </div>
                                <div>
                                    <p class="card-text mb-1">Departments</p>
                                    <h4 class="card-title mb-0">{{ $totalDepartments ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-bg-warning rounded-circle mr-3">
                                    <i class="fas fa-briefcase fa-lg text-white"></i>
                                </div>
                                <div>
                                    <p class="card-text mb-1">Designations</p>
                                    <h4 class="card-title mb-0">{{ $totalDesignations ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- EDIT COMPANY MODAL -->
<div class="modal fade" id="editCompanyModal" tabindex="-1" aria-labelledby="editCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title" style="font-weight: 500;">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Company Profile
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="companyEditForm" enctype="multipart/form-data">
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
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Basic Information -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0" style="font-weight: 500;">
                                <i class="fas fa-info-circle text-primary mr-2"></i>
                                Basic Information
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" name="name" id="edit_name" placeholder="e.g., Tech Solutions Pvt Ltd" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control form-control-sm" name="email" id="edit_email" placeholder="info@company.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Phone</label>
                                        <input type="text" class="form-control form-control-sm" name="phone" id="edit_phone" placeholder="+91 9876543210">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Website</label>
                                        <input type="url" class="form-control form-control-sm" name="website" id="edit_website" placeholder="https://company.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Status</label>
                                        <select class="form-control form-control-sm" name="status" id="edit_status">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Details -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0" style="font-weight: 500;">
                                <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                Address Details
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Address</label>
                                        <textarea class="form-control form-control-sm" name="address" id="edit_address" rows="2" placeholder="Street address"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">City</label>
                                        <input type="text" class="form-control form-control-sm" name="city" id="edit_city" placeholder="City">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">State</label>
                                        <input type="text" class="form-control form-control-sm" name="state" id="edit_state" placeholder="State">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Country</label>
                                        <input type="text" class="form-control form-control-sm" name="country" id="edit_country" placeholder="Country">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Postal Code</label>
                                        <input type="text" class="form-control form-control-sm" name="postal_code" id="edit_postal_code" placeholder="Postal code">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tax & Registration -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0" style="font-weight: 500;">
                                <i class="fas fa-file-invoice text-primary mr-2"></i>
                                Tax & Registration
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Tax ID / GST</label>
                                        <input type="text" class="form-control form-control-sm" name="tax_id" id="edit_tax_id" placeholder="e.g., 27ABCDE1234F1Z5">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Registration Number</label>
                                        <input type="text" class="form-control form-control-sm" name="registration_number" id="edit_registration_number" placeholder="Registration no.">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" id="saveBtn">
                        <i class="fas fa-save mr-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Custom Styles */
.icon-bg-primary {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #4e73df, #224abe);
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-bg-success {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #28a745, #1e7e34);
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-bg-warning {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #ffc107, #d39e00);
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-statistics {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.company-logo-wrapper {
    position: relative;
    display: inline-block;
}

.upload-overlay {
    position: absolute;
    bottom: 10px;
    right: 10px;
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #4e73df, #224abe);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.3s;
    border: 2px solid white;
}

.company-logo-wrapper:hover .upload-overlay {
    opacity: 1;
}
</style>

<script>
$(document).ready(function() {
    // Load company data when modal opens
    $('#editCompanyModal').on('show.bs.modal', function() {
        loadCompanyData();
    });
    
    // Handle form submission
    $('#companyEditForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("admin.companies.update") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#saveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
            },
            success: function(response) {
                if(response.success) {
                    // Update displayed data
                    $('#companyName').text(response.company.name);
                    $('#companyEmail').text(response.company.email);
                    $('#companyPhone').text(response.company.phone || 'Not provided');
                    $('#companyWebsite').html(response.company.website ? `<a href="${response.company.website}" target="_blank">${response.company.website}</a>` : 'Not provided');
                    $('#companyAddress').text(response.company.address || 'Not provided');
                    $('#companyCity').text(response.company.city || 'Not provided');
                    $('#companyState').text(response.company.state || 'Not provided');
                    $('#companyCountry').text(response.company.country || 'Not provided');
                    $('#companyPostalCode').text(response.company.postal_code || 'Not provided');
                    $('#companyTaxId').text(response.company.tax_id || 'Not provided');
                    $('#companyRegNo').text(response.company.registration_number || 'Not provided');
                    
                    // Update status badge
                    var statusBadge = response.company.status == 'active' ? 
                        '<span class="badge badge-success badge-pill px-3 py-1">Active</span>' : 
                        '<span class="badge badge-secondary badge-pill px-3 py-1">Inactive</span>';
                    $('#companyStatus').replaceWith(statusBadge);
                    
                    $('#editCompanyModal').modal('hide');
                    
                    // Show success message
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                if(errors) {
                    Object.keys(errors).forEach(function(key) {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error('An error occurred while saving');
                }
            },
            complete: function() {
                $('#saveBtn').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Changes');
            }
        });
    });
});

// Load company data for editing
function loadCompanyData() {
    $.get('{{ route("admin.companies.edit") }}', function(response) {
        if(response.success) {
            $('#edit_name').val(response.company.name);
            $('#edit_email').val(response.company.email);
            $('#edit_phone').val(response.company.phone);
            $('#edit_website').val(response.company.website);
            $('#edit_address').val(response.company.address);
            $('#edit_city').val(response.company.city);
            $('#edit_state').val(response.company.state);
            $('#edit_country').val(response.company.country);
            $('#edit_postal_code').val(response.company.postal_code);
            $('#edit_tax_id').val(response.company.tax_id);
            $('#edit_registration_number').val(response.company.registration_number);
            $('#edit_status').val(response.company.status);
        }
    });
}

// Open edit modal
function openEditModal() {
    $('#editCompanyModal').modal('show');
}

// Upload logo
function uploadLogo(input) {
    if (input.files && input.files[0]) {
        var formData = new FormData();
        formData.append('logo', input.files[0]);
        formData.append('_token', '{{ csrf_token() }}');
        
        $.ajax({
            url: '{{ route("admin.companies.upload-logo") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                // Show loading
            },
            success: function(response) {
                if(response.success) {
                    // Update logo image
                    var logoHtml = `<img src="${response.logo_url}" alt="Company Logo" class="img-fluid rounded-circle" id="companyLogo" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #4e73df;">`;
                    $('#logoContainer').html(logoHtml + $('#logoContainer .upload-overlay').prop('outerHTML'));
                    
                    toastr.success('Logo uploaded successfully');
                }
            },
            error: function() {
                toastr.error('Error uploading logo');
            }
        });
    }
}
</script>
@endsection