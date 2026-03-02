@extends('layout.app')

@section('content')
    <div class="content">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title text-dark">
                        <i class="fas fa-user-plus text-primary mr-2"></i>
                        Add New Employee
                    </h3>
                    <p class="text-muted mb-0">Complete the form below to add a new employee</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form id="employeeForm" action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
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

            <!-- BASIC INFORMATION -->
            <div class="card mb-4">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-user-circle text-primary mr-2"></i>
                        Basic Information
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <!-- Employee Code -->
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">Employee Code <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control form-control-sm bg-light @error('employee_code') is-invalid @enderror"
                                    id="employee_code" name="employee_code" value="EMP-{{ date('Y') }}-001" readonly>
                                @error('employee_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- First Name -->
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control form-control-sm @error('first_name') is-invalid @enderror"
                                    id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control form-control-sm @error('last_name') is-invalid @enderror"
                                    id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Full Name (Auto) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control form-control-sm bg-light" id="full_name" readonly>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm @error('gender') is-invalid @enderror"
                                    id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date"
                                    class="form-control form-control-sm @error('birth_date') is-invalid @enderror"
                                    id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Age (Auto) -->
                        <div class="col-md-1">
                            <div class="form-group mb-2">
                                <label class="form-label">Age</label>
                                <input type="text" class="form-control form-control-sm bg-light" id="age" readonly>
                            </div>
                        </div>

                        <!-- CNIC -->
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">CNIC <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm @error('cnic') is-invalid @enderror"
                                    id="cnic" name="cnic" value="{{ old('cnic') }}" placeholder="12345-1234567-1" required>
                                @error('cnic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Religion (NEW) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Religion</label>
                                <select class="form-control form-control-sm @error('religion') is-invalid @enderror"
                                    id="religion" name="religion">
                                    <option value="">Select Religion</option>
                                    <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="Christianity" {{ old('religion') == 'Christianity' ? 'selected' : '' }}>
                                        Christianity</option>
                                    <option value="Hinduism" {{ old('religion') == 'Hinduism' ? 'selected' : '' }}>Hinduism
                                    </option>
                                    <option value="Sikhism" {{ old('religion') == 'Sikhism' ? 'selected' : '' }}>Sikhism
                                    </option>
                                    <option value="Other" {{ old('religion') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('religion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Nationality (NEW) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Nationality</label>
                                <input type="text"
                                    class="form-control form-control-sm @error('nationality') is-invalid @enderror"
                                    id="nationality" name="nationality" value="{{ old('nationality', 'Pakistani') }}">
                                @error('nationality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Blood Group -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Blood Group</label>
                                <select class="form-control form-control-sm @error('blood_group') is-invalid @enderror"
                                    id="blood_group" name="blood_group">
                                    <option value="">Select</option>
                                    <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                    <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                </select>
                                @error('blood_group')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Marital Status -->
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">Marital Status</label>
                                <select class="form-control form-control-sm @error('marital_status') is-invalid @enderror"
                                    id="marital_status" name="marital_status">
                                    <option value="">Select</option>
                                    <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single
                                    </option>
                                    <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married
                                    </option>
                                    <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>
                                        Divorced</option>
                                    <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed
                                    </option>
                                </select>
                                @error('marital_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Photo Upload -->
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                <label class="form-label">Profile Photo</label>
                                <div class="d-flex align-items-center">
                                    <div class="photo-preview mr-3" style="width: 60px; height: 60px; position: relative;">
                                        <div id="defaultAvatar"
                                            class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 60px; height: 60px; font-size: 20px; cursor: pointer;"
                                            onclick="document.getElementById('photo').click();">
                                            <i class="fas fa-camera"></i>
                                        </div>
                                        <img id="photoPreview" src="" alt="Preview" class="rounded-circle d-none"
                                            style="width: 60px; height: 60px; object-fit: cover; cursor: pointer; border: 2px solid #007bff;"
                                            onclick="document.getElementById('photo').click();">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" id="photo" name="photo" accept="image/*"
                                            onchange="previewImage(this)" style="display: none;">
                                        <button type="button" class="btn btn-sm btn-primary"
                                            onclick="document.getElementById('photo').click()">
                                            <i class="fas fa-upload mr-1"></i> Choose Photo
                                        </button>
                                        <span class="text-muted small ml-2" id="photoName"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTACT INFORMATION -->
            <div class="card mb-4">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-address-book text-primary mr-2"></i>
                        Contact Information
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <!-- Official Email -->
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Official Email <span class="text-danger">*</span></label>
                                <input type="email"
                                    class="form-control form-control-sm @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" placeholder="employee@company.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Personal Email -->
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Personal Email <span class="text-danger">*</span></label>
                                <input type="email"
                                    class="form-control form-control-sm @error('personal_email') is-invalid @enderror"
                                    id="personal_email" name="personal_email" value="{{ old('personal_email') }}" required>
                                @error('personal_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Mobile Number -->
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+92</span>
                                    </div>
                                    <input type="text"
                                        class="form-control form-control-sm @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" value="{{ old('phone') }}" placeholder="300 1234567" required>
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Alternate Phone (NEW) -->
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Alternate Phone</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+92</span>
                                    </div>
                                    <input type="text"
                                        class="form-control form-control-sm @error('alternate_phone') is-invalid @enderror"
                                        id="alternate_phone" name="alternate_phone" value="{{ old('alternate_phone') }}"
                                        placeholder="300 1234567">
                                </div>
                                @error('alternate_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                       

                        <!-- Emergency Contact Person -->
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Emergency Contact Person</label>
                                <input type="text" class="form-control form-control-sm" id="emergency_contact_name"
                                    name="emergency_contact_name" value="{{ old('emergency_contact_name') }}">
                            </div>
                        </div>

                        <!-- Relationship -->
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Relationship</label>
                                <input type="text" class="form-control form-control-sm" id="emergency_relation"
                                    name="emergency_relation" value="{{ old('emergency_relation') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ADDRESS INFORMATION -->
            <div class="card mb-4">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                        Address Information
                    </h6>
                </div>
                <div class="card-body p-3">
                    <h6 class="text-muted mb-2" style="font-size: 13px;">Present Address</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Address Line 1</label>
                                <input type="text" class="form-control form-control-sm" id="present_address_line1"
                                    name="present_address_line1" value="{{ old('present_address_line1') }}"
                                    placeholder="House #, Street">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Address Line 2</label>
                                <input type="text" class="form-control form-control-sm" id="present_address_line2"
                                    name="present_address_line2" value="{{ old('present_address_line2') }}"
                                    placeholder="Area, Colony">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control form-control-sm" id="city" name="city"
                                    value="{{ old('city') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control form-control-sm" id="state" name="state"
                                    value="{{ old('state') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Country</label>
                                <select class="form-control form-control-sm" id="country" name="country">
                                    <option value="Pakistan">Pakistan</option>
                                    <option value="UAE">UAE</option>
                                    <option value="Saudi Arabia">Saudi Arabia</option>
                                    <option value="UK">United Kingdom</option>
                                    <option value="USA">United States</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control form-control-sm" id="postal_code" name="postal_code"
                                    value="{{ old('postal_code') }}">
                            </div>
                        </div>
                    </div>

                    <div class="custom-control custom-checkbox my-2">
                        <input type="checkbox" class="custom-control-input" id="same_as_present" name="same_as_present"
                            value="1">
                        <label class="custom-control-label" for="same_as_present">Permanent Address same as Present
                            Address</label>
                    </div>

                    <h6 class="text-muted mb-2 mt-3" style="font-size: 13px;">Permanent Address</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Address Line 1</label>
                                <input type="text" class="form-control form-control-sm" id="permanent_address_line1"
                                    name="permanent_address_line1" value="{{ old('permanent_address_line1') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Address Line 2</label>
                                <input type="text" class="form-control form-control-sm" id="permanent_address_line2"
                                    name="permanent_address_line2" value="{{ old('permanent_address_line2') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control form-control-sm" id="permanent_city"
                                    name="permanent_city" value="{{ old('permanent_city') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control form-control-sm" id="permanent_state"
                                    name="permanent_state" value="{{ old('permanent_state') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Country</label>
                                <select class="form-control form-control-sm" id="permanent_country"
                                    name="permanent_country">
                                    <option value="Pakistan">Pakistan</option>
                                    <option value="UAE">UAE</option>
                                    <option value="Saudi Arabia">Saudi Arabia</option>
                                    <option value="UK">UK</option>
                                    <option value="USA">USA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control form-control-sm" id="permanent_postal_code"
                                    name="permanent_postal_code" value="{{ old('permanent_postal_code') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EMPLOYMENT INFORMATION -->
            <div class="card mb-4">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-briefcase text-primary mr-2"></i>
                        Employment Information
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <!-- Department -->
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm @error('department_id') is-invalid @enderror"
                                    id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Designation -->
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">Designation <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm @error('designation_id') is-invalid @enderror"
                                    id="designation_id" name="designation_id" required>
                                    <option value="">Select Designation</option>
                                    @foreach($designations as $desig)
                                        <option value="{{ $desig->id }}" {{ old('designation_id') == $desig->id ? 'selected' : '' }}>{{ $desig->title }}</option>
                                    @endforeach
                                </select>
                                @error('designation_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Reporting To -->
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">Reporting To</label>
                                <select class="form-control form-control-sm" id="reporting_to_id" name="reporting_to_id">
                                    <option value="">Select Manager</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('reporting_to_id') == $manager->id ? 'selected' : '' }}>{{ $manager->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Employment Type -->
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm @error('employee_type') is-invalid @enderror"
                                    id="employee_type" name="employee_type" required>
                                    <option value="permanent" {{ old('employee_type') == 'permanent' ? 'selected' : '' }}>
                                        Permanent</option>
                                    <option value="contract" {{ old('employee_type') == 'contract' ? 'selected' : '' }}>
                                        Contract</option>
                                    <option value="intern" {{ old('employee_type') == 'intern' ? 'selected' : '' }}>Intern
                                    </option>
                                    <option value="trainee" {{ old('employee_type') == 'trainee' ? 'selected' : '' }}>Trainee
                                    </option>
                                    <option value="consultant" {{ old('employee_type') == 'consultant' ? 'selected' : '' }}>
                                        Consultant</option>
                                </select>
                                @error('employee_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Joining Date -->
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                                <input type="date"
                                    class="form-control form-control-sm @error('joining_date') is-invalid @enderror"
                                    id="joining_date" name="joining_date" value="{{ old('joining_date') }}" required>
                                @error('joining_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirmation Date (NEW) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Confirmation Date</label>
                                <input type="date"
                                    class="form-control form-control-sm @error('confirmation_date') is-invalid @enderror"
                                    id="confirmation_date" name="confirmation_date" value="{{ old('confirmation_date') }}">
                                @error('confirmation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Probation Period (NEW) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Probation (months)</label>
                                <input type="number"
                                    class="form-control form-control-sm @error('probation_period') is-invalid @enderror"
                                    id="probation_period" name="probation_period" value="{{ old('probation_period', 6) }}"
                                    min="1" max="24">
                                @error('probation_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notice Period (NEW) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Notice (days)</label>
                                <input type="number"
                                    class="form-control form-control-sm @error('notice_period') is-invalid @enderror"
                                    id="notice_period" name="notice_period" value="{{ old('notice_period', 30) }}" min="1"
                                    max="180">
                                @error('notice_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Work Location -->
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">Work Location</label>
                                <input type="text" class="form-control form-control-sm" id="work_location"
                                    name="work_location" value="{{ old('work_location') }}" placeholder="Karachi Office">
                            </div>
                        </div>

                        <!-- Employee Level -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Level</label>
                                <select class="form-control form-control-sm" id="employee_level" name="employee_level">
                                    <option value="">Select</option>
                                    <option value="1" {{ old('employee_level') == '1' ? 'selected' : '' }}>Level 1</option>
                                    <option value="2" {{ old('employee_level') == '2' ? 'selected' : '' }}>Level 2</option>
                                    <option value="3" {{ old('employee_level') == '3' ? 'selected' : '' }}>Level 3</option>
                                    <option value="4" {{ old('employee_level') == '4' ? 'selected' : '' }}>Level 4</option>
                                    <option value="5" {{ old('employee_level') == '5' ? 'selected' : '' }}>Level 5</option>
                                </select>
                            </div>
                        </div>

                        <!-- CTC -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">CTC (PKR)</label>
                                <input type="number" class="form-control form-control-sm" id="ctc" name="ctc"
                                    value="{{ old('ctc') }}" placeholder="500000">
                            </div>
                        </div>

                        <!-- Seat Location (NEW) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Seat Location</label>
                                <input type="text" class="form-control form-control-sm" id="seat_location"
                                    name="seat_location" value="{{ old('seat_location') }}"
                                    placeholder="Floor 2, Section A">
                            </div>
                        </div>

                        <!-- Extension (NEW) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Extension</label>
                                <input type="text" class="form-control form-control-sm" id="extension" name="extension"
                                    value="{{ old('extension') }}" placeholder="123">
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Status</label>
                                <select class="form-control form-control-sm" id="status" name="status">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="terminated">Terminated</option>
                                </select>
                            </div>
                        </div>

                        <!-- Is Reporting Manager -->
                        <div class="col-md-3">
                            <div class="form-group mb-2 mt-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_reporting_manager"
                                        name="is_reporting_manager" value="1" {{ old('is_reporting_manager') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_reporting_manager">Is Reporting
                                        Manager</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAMILY INFORMATION -->
            <div class="card mb-4">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-users text-primary mr-2"></i>
                        Family Information
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Father's Name</label>
                                <input type="text" class="form-control form-control-sm" id="father_name" name="father_name"
                                    value="{{ old('father_name') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Mother's Name</label>
                                <input type="text" class="form-control form-control-sm" id="mother_name" name="mother_name"
                                    value="{{ old('mother_name') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label">Hobbies</label>
                                <input type="text" class="form-control form-control-sm" id="hobbies" name="hobbies"
                                    value="{{ old('hobbies') }}" placeholder="Reading, Cricket">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EDUCATION (Dynamic) -->
            <div class="card mb-4">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-graduation-cap text-primary mr-2"></i>
                        Education
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary" id="add-education">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="card-body p-3">
                    <div id="education-container">
                        @if(old('education'))
                            @foreach(old('education') as $index => $edu)
                                <div class="education-entry border rounded p-2 mb-2">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm"
                                                name="education[{{ $index }}][course]" value="{{ $edu['course'] }}"
                                                placeholder="Course">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm"
                                                name="education[{{ $index }}][institution]" value="{{ $edu['institution'] }}"
                                                placeholder="Institution">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control form-control-sm"
                                                name="education[{{ $index }}][marks]" value="{{ $edu['marks'] }}"
                                                placeholder="Marks %">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="education[{{ $index }}][year]" value="{{ $edu['year'] }}"
                                                    placeholder="Year">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-sm btn-danger remove-entry">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="education-entry border rounded p-2 mb-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control form-control-sm" name="education[0][course]"
                                            placeholder="Course/Degree">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control form-control-sm" name="education[0][institution]"
                                            placeholder="Institution">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control form-control-sm" name="education[0][marks]"
                                            placeholder="Marks %">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" name="education[0][year]"
                                                placeholder="Year">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-sm btn-danger remove-entry">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- EXPERIENCE (Dynamic) -->
            <div class="card mb-4">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-history text-primary mr-2"></i>
                        Work Experience
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary" id="add-experience">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="card-body p-3">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label class="form-label">Experience Status</label>
                                <div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="fresher" name="experience_status"
                                            class="custom-control-input" value="fresher" {{ old('experience_status', 'fresher') == 'fresher' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="fresher">Fresher</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="experienced" name="experience_status"
                                            class="custom-control-input" value="experienced" {{ old('experience_status') == 'experienced' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="experienced">Experienced</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="experience-container">
                        @if(old('experience'))
                            @foreach(old('experience') as $index => $exp)
                                <div class="experience-entry border rounded p-2 mb-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control form-control-sm"
                                                name="experience[{{ $index }}][company]" value="{{ $exp['company'] }}"
                                                placeholder="Company">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control form-control-sm"
                                                name="experience[{{ $index }}][designation]" value="{{ $exp['designation'] }}"
                                                placeholder="Designation">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="date" class="form-control form-control-sm"
                                                name="experience[{{ $index }}][from]" value="{{ $exp['from'] }}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="date" class="form-control form-control-sm"
                                                name="experience[{{ $index }}][to]" value="{{ $exp['to'] }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-sm btn-danger remove-entry w-100">
                                                <i class="fas fa-trash ml-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="experience-entry border rounded p-2 mb-2">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control form-control-sm" name="experience[0][company]"
                                            placeholder="Company Name">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control form-control-sm"
                                            name="experience[0][designation]" placeholder="Designation">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control form-control-sm" name="experience[0][from]">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control form-control-sm" name="experience[0][to]">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-danger remove-entry w-100">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- BANK INFORMATION -->
            <div class="card mb-4">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-university text-primary mr-2"></i>
                        Bank Information
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">Bank Name</label>
                                <select class="form-control form-control-sm" id="bank_name" name="bank_name">
                                    <option value="">Select Bank</option>
                                    <option value="habib" {{ old('bank_name') == 'habib' ? 'selected' : '' }}>Habib Bank (HBL)
                                    </option>
                                    <option value="allied" {{ old('bank_name') == 'allied' ? 'selected' : '' }}>Allied Bank
                                    </option>
                                    <option value="meezan" {{ old('bank_name') == 'meezan' ? 'selected' : '' }}>Meezan Bank
                                    </option>
                                    <option value="ubl" {{ old('bank_name') == 'ubl' ? 'selected' : '' }}>UBL</option>
                                    <option value="alfalah" {{ old('bank_name') == 'alfalah' ? 'selected' : '' }}>Bank Alfalah
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">Account Title</label>
                                <input type="text" class="form-control form-control-sm" id="account_title"
                                    name="account_title" value="{{ old('account_title') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">Account Number</label>
                                <input type="text" class="form-control form-control-sm" id="account_number"
                                    name="account_number" value="{{ old('account_number') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="form-label">IBAN</label>
                                <input type="text" class="form-control form-control-sm" id="iban" name="iban"
                                    value="{{ old('iban') }}" placeholder="PK36SCBL0000001123456702">
                            </div>
                        </div>

                        <!-- Basic Salary -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Basic Salary (PKR)</label>
                                <input type="number" class="form-control form-control-sm" id="basic_salary"
                                    name="basic_salary" value="{{ old('basic_salary') }}" placeholder="50000">
                            </div>
                        </div>

                        <!-- HRA (NEW) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">HRA (PKR)</label>
                                <input type="number" class="form-control form-control-sm" id="hra" name="hra"
                                    value="{{ old('hra') }}" placeholder="House Rent">
                            </div>
                        </div>

                        <!-- DA (NEW) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">DA (PKR)</label>
                                <input type="number" class="form-control form-control-sm" id="da" name="da"
                                    value="{{ old('da') }}" placeholder="Dearness">
                            </div>
                        </div>

                        <!-- Conveyance (NEW) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Conveyance (PKR)</label>
                                <input type="number" class="form-control form-control-sm" id="conveyance" name="conveyance"
                                    value="{{ old('conveyance') }}" placeholder="Transport">
                            </div>
                        </div>

                        <!-- Medical Allowance (NEW) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Medical (PKR)</label>
                                <input type="number" class="form-control form-control-sm" id="medical_allowance"
                                    name="medical_allowance" value="{{ old('medical_allowance') }}" placeholder="Medical">
                            </div>
                        </div>

                        <!-- Special Allowance (NEW) -->
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label">Special (PKR)</label>
                                <input type="number" class="form-control form-control-sm" id="special_allowance"
                                    name="special_allowance" value="{{ old('special_allowance') }}" placeholder="Special">
                            </div>
                        </div>

                        <!-- Total Salary Display (NEW) -->
                        <div class="col-md-12 mt-2">
                            <div class="alert alert-info p-2 mb-0" style="font-size: 13px;">
                                <strong>Total Salary:</strong>
                                <span
                                    id="total_salary">{{ old('basic_salary', 0) + old('hra', 0) + old('da', 0) + old('conveyance', 0) + old('medical_allowance', 0) + old('special_allowance', 0) }}</span>
                                PKR
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ASSETS (Dynamic) - ADD THIS AFTER BANK INFORMATION CARD -->
            <div class="card mb-4">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0" style="font-weight: 500;">
                        <i class="fas fa-laptop text-primary mr-2"></i>
                        Company Assets
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary" id="add-asset">
                        <i class="fas fa-plus"></i> Add Asset
                    </button>
                </div>
                <div class="card-body p-3">
                    <div class="alert alert-info p-2 mb-3" style="font-size: 12px;">
                        <i class="fas fa-info-circle mr-1"></i> Add company assets assigned to this employee (laptop,
                        mobile, etc.)
                    </div>

                    <div id="assets-container">
                        @if(old('assets'))
                            @foreach(old('assets') as $index => $asset)
                                <div class="asset-entry border rounded p-2 mb-2">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <select class="form-control form-control-sm" name="assets[{{ $index }}][type]">
                                                <option value="">Asset Type</option>
                                                <option value="laptop" {{ $asset['type'] == 'laptop' ? 'selected' : '' }}>Laptop
                                                </option>
                                                <option value="mobile" {{ $asset['type'] == 'mobile' ? 'selected' : '' }}>Mobile
                                                </option>
                                                <option value="tablet" {{ $asset['type'] == 'tablet' ? 'selected' : '' }}>Tablet
                                                </option>
                                                <option value="vehicle" {{ $asset['type'] == 'vehicle' ? 'selected' : '' }}>Vehicle
                                                </option>
                                                <option value="other" {{ $asset['type'] == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control form-control-sm"
                                                name="assets[{{ $index }}][name]" value="{{ $asset['name'] }}"
                                                placeholder="Asset Name (e.g., Dell Laptop)">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control form-control-sm"
                                                name="assets[{{ $index }}][serial_no]" value="{{ $asset['serial_no'] }}"
                                                placeholder="Serial #">
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control form-control-sm" name="assets[{{ $index }}][status]">
                                                <option value="assigned" {{ $asset['status'] == 'assigned' ? 'selected' : '' }}>
                                                    Assigned</option>
                                                <option value="returned" {{ $asset['status'] == 'returned' ? 'selected' : '' }}>
                                                    Returned</option>
                                                <option value="damaged" {{ $asset['status'] == 'damaged' ? 'selected' : '' }}>Damaged
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="date" class="form-control form-control-sm"
                                                name="assets[{{ $index }}][given_on]"
                                                value="{{ $asset['given_on'] ?? date('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-sm btn-danger remove-entry w-100">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Default empty asset entry -->
                            <div class="asset-entry border rounded p-2 mb-2">
                                <div class="row">
                                    <div class="col-md-2">
                                        <select class="form-control form-control-sm" name="assets[0][type]">
                                            <option value="">Asset Type</option>
                                            <option value="laptop">Laptop</option>
                                            <option value="mobile">Mobile</option>
                                            <option value="tablet">Tablet</option>
                                            <option value="vehicle">Vehicle</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control form-control-sm" name="assets[0][name]"
                                            placeholder="Asset Name (e.g., Dell Laptop)">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control form-control-sm" name="assets[0][serial_no]"
                                            placeholder="Serial #">
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control form-control-sm" name="assets[0][status]">
                                            <option value="assigned" selected>Assigned</option>
                                            <option value="returned">Returned</option>
                                            <option value="damaged">Damaged</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control form-control-sm" name="assets[0][given_on]"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-danger remove-entry w-100">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                   
                </div>
            </div>

            <!-- FORM ACTIONS -->
            <div class="row mt-4 mb-5">
                <div class="col-12 text-right">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-light px-4">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-5 ml-2">
                        <i class="fas fa-save mr-1"></i> Save Employee
                    </button>
                </div>
            </div>
        </form>
    </div>

    <style>
        /* Form Styles */
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 2px;
            font-size: 12px;
        }

        .form-control-sm {
            font-size: 13px;
            padding: 4px 8px;
            height: 32px;
        }

        button.btn.btn-sm.btn-danger.remove-entry.w-100 {
            width: 33px !important;
        }

        .card {
            border: none;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 16px;
        }

        .card-header {
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fc;
            padding: 8px 12px;
        }

        .card-body {
            padding: 12px;
        }

        .input-group-sm .form-control,
        .input-group-sm .input-group-text {
            font-size: 13px;
            padding: 4px 8px;
            height: 32px;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        /* Validation Styles */
        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            font-size: 11px;
            color: #dc3545;
            margin-top: 2px;
        }

        /* Custom Switch */
        .custom-switch {
            padding-left: 2.25rem;
        }

        /* Border Styles */
        .border.rounded {
            border-color: #e9ecef !important;
            background-color: #fafbfc;
        }

        /* Remove Button */
        .btn-danger {
            padding: 4px 8px;
            font-size: 12px;
        }

        /* Photo Upload */
        .default-avatar {
            background: linear-gradient(135deg, #4e73df, #224abe);
            transition: opacity 0.2s;
        }

        .default-avatar:hover {
            opacity: 0.9;
        }
    </style>

    <script>
        $(document).ready(function () {
            let educationIndex = {{ old('education') ? count(old('education')) : 1 }};
            let experienceIndex = {{ old('experience') ? count(old('experience')) : 1 }};

            // Auto-generate full name
            function updateFullName() {
                let firstName = $('#first_name').val();
                let lastName = $('#last_name').val();
                if (firstName && lastName) {
                    $('#full_name').val(firstName + ' ' + lastName);
                }
            }

            $('#first_name, #last_name').on('keyup change', updateFullName);

            // Calculate age from birth date
            $('#birth_date').change(function () {
                if ($(this).val()) {
                    let birthDate = new Date($(this).val());
                    let today = new Date();
                    let age = today.getFullYear() - birthDate.getFullYear();
                    let m = today.getMonth() - birthDate.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }
                    $('#age').val(age);
                }
            });

            // CNIC Format
            $('#cnic').on('input', function () {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length > 13) value = value.substr(0, 13);

                if (value.length > 5) {
                    value = value.substr(0, 5) + '-' + value.substr(5);
                }
                if (value.length > 13) {
                    value = value.substr(0, 13) + '-' + value.substr(13, 1);
                }
                $(this).val(value);
            });

            // Phone format
            $('#phone, #emergency_phone, #whatsapp_number').on('input', function () {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length > 10) value = value.substr(0, 10);
                $(this).val(value);
            });

            // Same as present address
            $('#same_as_present').change(function () {
                if ($(this).is(':checked')) {
                    $('#permanent_address_line1').val($('#present_address_line1').val());
                    $('#permanent_address_line2').val($('#present_address_line2').val());
                    $('#permanent_city').val($('#city').val());
                    $('#permanent_state').val($('#state').val());
                    $('#permanent_postal_code').val($('#postal_code').val());
                    $('#permanent_country').val($('#country').val());
                } else {
                    $('#permanent_address_line1').val('');
                    $('#permanent_address_line2').val('');
                    $('#permanent_city').val('');
                    $('#permanent_state').val('');
                    $('#permanent_postal_code').val('');
                    $('#permanent_country').val('Pakistan');
                }
            });

            function calculateTotalSalary() {
                let basic = parseFloat($('#basic_salary').val()) || 0;
                let hra = parseFloat($('#hra').val()) || 0;
                let da = parseFloat($('#da').val()) || 0;
                let conveyance = parseFloat($('#conveyance').val()) || 0;
                let medical = parseFloat($('#medical_allowance').val()) || 0;
                let special = parseFloat($('#special_allowance').val()) || 0;

                let total = basic + hra + da + conveyance + medical + special;
                $('#total_salary').text(total.toLocaleString());
            }

            // Attach event listeners
            $('#basic_salary, #hra, #da, #conveyance, #medical_allowance, #special_allowance').on('keyup change', calculateTotalSalary);
            // Add Education
            $('#add-education').click(function () {
                let html = `
                                <div class="education-entry border rounded p-2 mb-2">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm" name="education[${educationIndex}][course]" placeholder="Course/Degree">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm" name="education[${educationIndex}][institution]" placeholder="Institution">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control form-control-sm" name="education[${educationIndex}][marks]" placeholder="Marks %">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm" name="education[${educationIndex}][year]" placeholder="Year">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-sm btn-danger remove-entry">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                $('#education-container').append(html);
                educationIndex++;
            });

            // Asset management
            let assetIndex = {{ old('assets') ? count(old('assets')) : 1 }};

            // Add Asset
            $('#add-asset').click(function () {
                addAssetEntry(assetIndex);
                assetIndex++;
            });

            function addAssetEntry(index) {
                let html = `
            <div class="asset-entry border rounded p-2 mb-2">
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" name="assets[${index}][type]">
                            <option value="">Asset Type</option>
                            <option value="laptop">Laptop</option>
                            <option value="mobile">Mobile</option>
                            <option value="tablet">Tablet</option>
                            <option value="vehicle">Vehicle</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" name="assets[${index}][name]" 
                            placeholder="Asset Name">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" name="assets[${index}][serial_no]" 
                            placeholder="Serial #">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" name="assets[${index}][status]">
                            <option value="assigned">Assigned</option>
                            <option value="returned">Returned</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control form-control-sm" name="assets[${index}][given_on]" 
                            value="${new Date().toISOString().split('T')[0]}">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger remove-entry w-100">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
                $('#assets-container').append(html);
            }

            // Quick add predefined assets
            window.addQuickAsset = function (type) {
                let index = assetIndex++;
                let assetName = '';
                let serialPlaceholder = '';

                switch (type) {
                    case 'laptop':
                        assetName = 'Dell Latitude 3420';
                        serialPlaceholder = 'LAP-';
                        break;
                    case 'mobile':
                        assetName = 'iPhone 13';
                        serialPlaceholder = 'MOB-';
                        break;
                    case 'vehicle':
                        assetName = 'Suzuki Cultus';
                        serialPlaceholder = 'CAR-';
                        break;
                    default:
                        assetName = '';
                }

                let html = `
            <div class="asset-entry border rounded p-2 mb-2">
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" name="assets[${index}][type]">
                            <option value="">Asset Type</option>
                            <option value="laptop" ${type == 'laptop' ? 'selected' : ''}>Laptop</option>
                            <option value="mobile" ${type == 'mobile' ? 'selected' : ''}>Mobile</option>
                            <option value="tablet" ${type == 'tablet' ? 'selected' : ''}>Tablet</option>
                            <option value="vehicle" ${type == 'vehicle' ? 'selected' : ''}>Vehicle</option>
                            <option value="other" ${type == 'other' ? 'selected' : ''}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" name="assets[${index}][name]" 
                            value="${assetName}" placeholder="Asset Name">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" name="assets[${index}][serial_no]" 
                            placeholder="${serialPlaceholder}XXXX">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" name="assets[${index}][status]">
                            <option value="assigned" selected>Assigned</option>
                            <option value="returned">Returned</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control form-control-sm" name="assets[${index}][given_on]" 
                            value="${new Date().toISOString().split('T')[0]}">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger remove-entry w-100">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
                $('#assets-container').append(html);
            };

            // Remove entry ko update karo (already hai, lekin ensure karo)
            $(document).on('click', '.remove-entry', function () {
                $(this).closest('.education-entry, .experience-entry, .asset-entry').remove();
            });

            // Add Experience
            $('#add-experience').click(function () {
                let html = `
                                <div class="experience-entry border rounded p-2 mb-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control form-control-sm" name="experience[${experienceIndex}][company]" placeholder="Company Name">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control form-control-sm" name="experience[${experienceIndex}][designation]" placeholder="Designation">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="date" class="form-control form-control-sm" name="experience[${experienceIndex}][from]">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="date" class="form-control form-control-sm" name="experience[${experienceIndex}][to]">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-sm btn-danger remove-entry w-100">
                                                <i class="fas fa-trash"></i> 
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                $('#experience-container').append(html);
                experienceIndex++;
            });

            // Remove entry (delegated event)
            $(document).on('click', '.remove-entry', function () {
                $(this).closest('.education-entry, .experience-entry').remove();
            });

            // Experience status toggle
            $('input[name="experience_status"]').change(function () {
                if ($(this).val() === 'fresher') {
                    $('#experience-container').find('input').val('');
                    $('#add-experience').prop('disabled', true);
                } else {
                    $('#add-experience').prop('disabled', false);
                }
            });

            // Trigger on page load
            if ($('#fresher').is(':checked')) {
                $('#add-experience').prop('disabled', true);
            }
        });

        // Image Preview Function
        function previewImage(input) {
            const preview = document.getElementById('photoPreview');
            const defaultAvatar = document.getElementById('defaultAvatar');
            const photoNameText = document.getElementById('photoName');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    // 1. Image source update karein
                    preview.src = e.target.result;

                    // 2. Logic: Avatar ko hide karein, Preview ko show karein
                    // Hum Bootstrap ki 'd-none' (hide) aur 'd-block' (show) use kar rahe hain
                    defaultAvatar.classList.add('d-none');
                    defaultAvatar.classList.remove('d-flex');

                    preview.classList.remove('d-none');
                    preview.classList.add('d-block');

                    // 3. Name show karein
                    photoNameText.textContent = input.files[0].name;
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                // Reset logic agar file remove ho jaye
                preview.classList.add('d-none');
                preview.classList.remove('d-block');

                defaultAvatar.classList.remove('d-none');
                defaultAvatar.classList.add('d-flex');

                photoNameText.textContent = '';
            }
        }
    </script>
@endsection