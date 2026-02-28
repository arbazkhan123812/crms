@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="page-title text-dark mb-0">
                    <i class="fas fa-user-edit text-primary mr-2"></i>
                    Edit Employee
                </h5>
                <p class="text-muted small mb-0">Update employee information - {{ $employee->employee_code }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
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

    <form id="employeeForm" action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- BASIC INFORMATION -->
        <div class="card mb-3">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 ">
                    <i class="fas fa-user-circle text-primary mr-2"></i>Basic Information
                </h6>
            </div>
            <div class="card-body p-2">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Employee Code</label>
                            <input type="text" class="form-control form-control-sm bg-light" value="{{ $employee->employee_code }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('first_name') is-invalid @enderror" 
                                name="first_name" value="{{ old('first_name', $employee->first_name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Middle Name</label>
                            <input type="text" class="form-control form-control-sm" name="middle_name" 
                                value="{{ old('middle_name', $employee->middle_name) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('last_name') is-invalid @enderror" 
                                name="last_name" value="{{ old('last_name', $employee->last_name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Gender <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" name="gender" required>
                                <option value="">Select</option>
                                <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" name="birth_date" 
                                value="{{ old('birth_date', $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">CNIC <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm cnic-mask" name="cnic" 
                                value="{{ old('cnic', $employee->cnic) }}" placeholder="12345-1234567-1" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Blood Group</label>
                            <select class="form-control form-control-sm" name="blood_group">
                                <option value="">Select</option>
                                @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group', $employee->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Marital Status</label>
                            <select class="form-control form-control-sm" name="marital_status">
                                <option value="">Select</option>
                                @foreach(['single','married','divorced','widowed'] as $ms)
                                <option value="{{ $ms }}" {{ old('marital_status', $employee->marital_status) == $ms ? 'selected' : '' }}>{{ ucfirst($ms) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Religion</label>
                            <select class="form-control form-control-sm" name="religion">
                                <option value="">Select</option>
                                @foreach(['Islam','Christianity','Hinduism','Sikhism','Other'] as $rel)
                                <option value="{{ $rel }}" {{ old('religion', $employee->religion) == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Nationality</label>
                            <input type="text" class="form-control form-control-sm" name="nationality" 
                                value="{{ old('nationality', $employee->nationality ?? 'Pakistani') }}">
                        </div>
                    </div>
                </div>

                <!-- Photo Upload -->
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="form-group mb-0">
                            <label class="small mb-0 ">Profile Photo</label>
                            <div class="d-flex align-items-center">
                                <div class="photo-preview mr-3" style="width: 60px; height: 60px;">
                                    @if($employee->profile_image)
                                    <img src="/storage/{{ $employee->profile_image }}" class="rounded-circle border" 
                                        style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                        style="width: 60px; height: 60px; font-size: 20px;">
                                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" id="photo" name="photo" accept="image/*" class="d-none">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="document.getElementById('photo').click()">
                                        <i class="fas fa-upload mr-1"></i> Change Photo
                                    </button>
                                    <small class="text-muted ml-2">Leave empty to keep current</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTACT INFORMATION -->
        <div class="card mb-3">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 ">
                    <i class="fas fa-address-book text-primary mr-2"></i>Contact Information
                </h6>
            </div>
            <div class="card-body p-2">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Official Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control form-control-sm" name="email" 
                                value="{{ old('email', $employee->email) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Personal Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control form-control-sm" name="personal_email" 
                                value="{{ old('personal_email', $employee->personal_email) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Mobile <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm phone-mask" name="phone" 
                                value="{{ old('phone', $employee->phone) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Alternate Phone</label>
                            <input type="text" class="form-control form-control-sm phone-mask" name="alternate_phone" 
                                value="{{ old('alternate_phone', $employee->alternate_phone) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">WhatsApp</label>
                            <input type="text" class="form-control form-control-sm phone-mask" name="whatsapp_number" 
                                value="{{ old('whatsapp_number', $employee->whatsapp_number) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Emergency Phone</label>
                            <input type="text" class="form-control form-control-sm phone-mask" name="emergency_phone" 
                                value="{{ old('emergency_phone', $employee->emergency_phone) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Emergency Contact</label>
                            <input type="text" class="form-control form-control-sm" name="emergency_contact_name" 
                                value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Relationship</label>
                            <input type="text" class="form-control form-control-sm" name="emergency_relation" 
                                value="{{ old('emergency_relation', $employee->emergency_relation) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADDRESS INFORMATION -->
        <div class="card mb-3">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 ">
                    <i class="fas fa-map-marker-alt text-primary mr-2"></i>Address Information
                </h6>
            </div>
            <div class="card-body p-2">
                <div class="row">
                    <div class="col-md-6">
                        <label class="small mb-0 ">Present Address</label>
                        <div class="border rounded p-2 bg-light">
                            <input type="text" class="form-control form-control-sm mb-1" name="present_address_line1" 
                                value="{{ old('present_address_line1', $employee->present_address_line1) }}" placeholder="Address Line 1">
                            <input type="text" class="form-control form-control-sm mb-1" name="present_address_line2" 
                                value="{{ old('present_address_line2', $employee->present_address_line2) }}" placeholder="Address Line 2">
                            <div class="row">
                                <div class="col-4"><input type="text" class="form-control form-control-sm" name="city" 
                                    value="{{ old('city', $employee->city) }}" placeholder="City"></div>
                                <div class="col-4"><input type="text" class="form-control form-control-sm" name="state" 
                                    value="{{ old('state', $employee->state) }}" placeholder="State"></div>
                                <div class="col-4"><input type="text" class="form-control form-control-sm" name="postal_code" 
                                    value="{{ old('postal_code', $employee->postal_code) }}" placeholder="Postal"></div>
                            </div>
                                <input type="text" class="form-control form-control-sm mb-1" name="country" 
                                value="{{ old('country', $employee->country) }}" placeholder="country">

                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="small mb-0 ">Permanent Address</label>
                        <div class="border rounded p-2 bg-light">
                            <input type="text" class="form-control form-control-sm mb-1" name="permanent_address_line1" 
                                value="{{ old('permanent_address_line1', $employee->permanent_address_line1) }}" placeholder="Address Line 1">
                            <input type="text" class="form-control form-control-sm mb-1" name="permanent_address_line2" 
                                value="{{ old('permanent_address_line2', $employee->permanent_address_line2) }}" placeholder="Address Line 2">
                            <div class="row">
                                <div class="col-4"><input type="text" class="form-control form-control-sm" name="permanent_city" 
                                    value="{{ old('permanent_city', $employee->permanent_city) }}" placeholder="City"></div>
                                <div class="col-4"><input type="text" class="form-control form-control-sm" name="permanent_state" 
                                    value="{{ old('permanent_state', $employee->permanent_state) }}" placeholder="State"></div>
                                <div class="col-4"><input type="text" class="form-control form-control-sm" name="permanent_postal_code" 
                                    value="{{ old('permanent_postal_code', $employee->permanent_postal_code) }}" placeholder="Postal"></div>
                            </div>
                             <input type="text" class="form-control form-control-sm mb-1" name="permanent_country" 
                                value="{{ old('country', $employee->country) }}" placeholder="Address Line 2">
                            
                        </div>
                    </div>
                </div>
                <div class="custom-control custom-checkbox mt-2">
                    <input type="checkbox" class="custom-control-input" id="same_as_present" name="same_as_present" value="1">
                    <label class="custom-control-label small" for="same_as_present">Same as present address</label>
                </div>
            </div>
        </div>

        <!-- EMPLOYMENT INFORMATION -->
        <div class="card mb-3">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 ">
                    <i class="fas fa-briefcase text-primary mr-2"></i>Employment Information
                </h6>
            </div>
            <div class="card-body p-2">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Department <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" name="department_id" required>
                                <option value="">Select</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Designation <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" name="designation_id" required>
                                <option value="">Select</option>
                                @foreach($designations as $desig)
                                <option value="{{ $desig->id }}" {{ old('designation_id', $employee->designation_id) == $desig->id ? 'selected' : '' }}>{{ $desig->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Reporting To</label>
                            <select class="form-control form-control-sm" name="reporting_to_id">
                                <option value="">Select</option>
                                @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('reporting_to_id', $employee->reporting_to_id) == $manager->id ? 'selected' : '' }}>{{ $manager->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Employment Type <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" name="employee_type" required>
                                @foreach(['permanent','contract','intern','trainee','consultant'] as $type)
                                <option value="{{ $type }}" {{ old('employee_type', $employee->employment_type) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Joining Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" name="joining_date" 
                                value="{{ old('joining_date', $employee->joining_date ? $employee->joining_date->format('Y-m-d') : '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Confirmation Date</label>
                            <input type="date" class="form-control form-control-sm" name="confirmation_date" 
                                value="{{ old('confirmation_date', $employee->confirmation_date ? $employee->confirmation_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Probation (months)</label>
                            <input type="number" class="form-control form-control-sm" name="probation_period" 
                                value="{{ old('probation_period', $employee->probation_period ?? 6) }}" min="1" max="24">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Notice (days)</label>
                            <input type="number" class="form-control form-control-sm" name="notice_period" 
                                value="{{ old('notice_period', $employee->notice_period ?? 30) }}" min="1" max="180">
                        </div>
                    </div>
                   
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Level</label>
                            <select class="form-control form-control-sm" name="employee_level">
                                <option value="">Select</option>
                                @for($i=1; $i<=5; $i++)
                                <option value="{{ $i }}" {{ old('employee_level', $employee->employee_level) == $i ? 'selected' : '' }}>Level {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">CTC (PKR)</label>
                            <input type="number" class="form-control form-control-sm" name="ctc" 
                                value="{{ old('ctc', $employee->ctc) }}">
                        </div>
                    </div>
                    
                    
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Status</label>
                            <select class="form-control form-control-sm" name="status">
                                @foreach(['active','inactive','suspended','terminated'] as $st)
                                <option value="{{ $st }}" {{ old('status', $employee->status) == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2 mt-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_reporting_manager" name="is_reporting_manager" value="1" 
                                    {{ old('is_reporting_manager', $employee->is_reporting_manager) ? 'checked' : '' }}>
                                <label class="custom-control-label small" for="is_reporting_manager">Is Reporting Manager</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAMILY INFORMATION -->
        <div class="card mb-3">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 ">
                    <i class="fas fa-users text-primary mr-2"></i>Family Information
                </h6>
            </div>
            <div class="card-body p-2">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Father's Name</label>
                            <input type="text" class="form-control form-control-sm" name="father_name" 
                                value="{{ old('father_name', $employee->father_name) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Mother's Name</label>
                            <input type="text" class="form-control form-control-sm" name="mother_name" 
                                value="{{ old('mother_name', $employee->mother_name) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Hobbies</label>
                            <input type="text" class="form-control form-control-sm" name="hobbies" 
                                value="{{ old('hobbies', $employee->hobbies) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- EDUCATION (Dynamic) -->
        <div class="card mb-3">
            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 ">
                    <i class="fas fa-graduation-cap text-primary mr-2"></i>Education
                </h6>
                <button type="button" class="btn btn-sm btn-primary" id="add-education">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="card-body p-2" id="education-container">
                @forelse($employee->education as $index => $edu)
                <div class="education-entry border rounded p-2 mb-2">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="education[{{ $index }}][course]" 
                                value="{{ $edu->course }}" placeholder="Course">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="education[{{ $index }}][institution]" 
                                value="{{ $edu->institution }}" placeholder="Institution">
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control form-control-sm" name="education[{{ $index }}][marks]" 
                                value="{{ $edu->marks }}" placeholder="Marks %">
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" name="education[{{ $index }}][year]" 
                                    value="{{ $edu->year }}" placeholder="Year">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-sm btn-danger remove-entry">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="education-entry border rounded p-2 mb-2">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="education[0][course]" placeholder="Course">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="education[0][institution]" placeholder="Institution">
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control form-control-sm" name="education[0][marks]" placeholder="Marks %">
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" name="education[0][year]" placeholder="Year">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-sm btn-danger remove-entry">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- EXPERIENCE (Dynamic) -->
        <div class="card mb-3">
            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 ">
                    <i class="fas fa-history text-primary mr-2"></i>Work Experience
                </h6>
                <button type="button" class="btn btn-sm btn-primary" id="add-experience">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="card-body p-2">
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="form-group mb-0">
                            <label class="small mb-0 ">Experience Status</label>
                            <div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="fresher" name="experience_status" class="custom-control-input" value="fresher" 
                                        {{ old('experience_status', $employee->experience_status) == 'fresher' ? 'checked' : '' }}>
                                    <label class="custom-control-label small" for="fresher">Fresher</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="experienced" name="experience_status" class="custom-control-input" value="experienced" 
                                        {{ old('experience_status', $employee->experience_status) == 'experienced' ? 'checked' : '' }}>
                                    <label class="custom-control-label small" for="experienced">Experienced</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="experience-container">
                    @forelse($employee->experience as $index => $exp)
                    <div class="experience-entry border rounded p-2 mb-2">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" name="experience[{{ $index }}][company]" 
                                    value="{{ $exp->company }}" placeholder="Company">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" name="experience[{{ $index }}][designation]" 
                                    value="{{ $exp->designation }}" placeholder="Designation">
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control form-control-sm" name="experience[{{ $index }}][from]" 
                                    value="{{ $exp->from_date ? $exp->from_date->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control form-control-sm" name="experience[{{ $index }}][to]" 
                                    value="{{ $exp->to_date ? $exp->to_date->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-danger remove-entry w-100">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="experience-entry border rounded p-2 mb-2">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" name="experience[0][company]" placeholder="Company">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" name="experience[0][designation]" placeholder="Designation">
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
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ASSETS (Dynamic) -->
        <div class="card mb-3">
            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 ">
                    <i class="fas fa-laptop text-primary mr-2"></i>Company Assets
                </h6>
                <button type="button" class="btn btn-sm btn-primary" id="add-asset">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="card-body p-2" id="assets-container">
                @forelse($employee->assets as $index => $asset)
                <div class="asset-entry border rounded p-2 mb-2">
                    <div class="row">
                        <div class="col-md-2">
                            <select class="form-control form-control-sm" name="assets[{{ $index }}][type]">
                                <option value="">Type</option>
                                @foreach(['laptop','mobile','tablet','vehicle','other'] as $type)
                                <option value="{{ $type }}" {{ $asset->asset_type == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control form-control-sm" name="assets[{{ $index }}][name]" 
                                value="{{ $asset->asset_name }}" placeholder="Asset Name">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control form-control-sm" name="assets[{{ $index }}][serial_no]" 
                                value="{{ $asset->serial_no }}" placeholder="Serial #">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control form-control-sm" name="assets[{{ $index }}][status]">
                                @foreach(['assigned','returned','damaged'] as $st)
                                <option value="{{ $st }}" {{ $asset->status == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control form-control-sm" name="assets[{{ $index }}][given_on]" 
                                value="{{ $asset->given_on ? $asset->given_on->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-danger remove-entry w-100">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="asset-entry border rounded p-2 mb-2">
                    <div class="row">
                        <div class="col-md-2">
                            <select class="form-control form-control-sm" name="assets[0][type]">
                                <option value="">Type</option>
                                <option value="laptop">Laptop</option>
                                <option value="mobile">Mobile</option>
                                <option value="tablet">Tablet</option>
                                <option value="vehicle">Vehicle</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control form-control-sm" name="assets[0][name]" placeholder="Asset Name">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control form-control-sm" name="assets[0][serial_no]" placeholder="Serial #">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control form-control-sm" name="assets[0][status]">
                                <option value="assigned">Assigned</option>
                                <option value="returned">Returned</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control form-control-sm" name="assets[0][given_on]" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-danger remove-entry w-100">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- BANK INFORMATION -->
        <div class="card mb-3">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 ">
                    <i class="fas fa-university text-primary mr-2"></i>Bank & Salary Information
                </h6>
            </div>
            <div class="card-body p-2">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Bank Name</label>
                            <select class="form-control form-control-sm" name="bank_name">
                                <option value="">Select</option>
                                @foreach(['habib'=>'Habib Bank','allied'=>'Allied Bank','meezan'=>'Meezan Bank','ubl'=>'UBL','alfalah'=>'Bank Alfalah'] as $val=>$label)
                                <option value="{{ $val }}" {{ old('bank_name', $employee->bank_name) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Account Title</label>
                            <input type="text" class="form-control form-control-sm" name="account_title" 
                                value="{{ old('account_title', $employee->account_holder_name) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Account Number</label>
                            <input type="text" class="form-control form-control-sm" name="account_number" 
                                value="{{ old('account_number', $employee->account_number) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">IBAN</label>
                            <input type="text" class="form-control form-control-sm" name="iban" 
                                value="{{ old('iban', $employee->iban) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Basic Salary</label>
                            <input type="number" class="form-control form-control-sm" name="basic_salary" 
                                value="{{ old('basic_salary', $employee->basic_salary) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">HRA</label>
                            <input type="number" class="form-control form-control-sm" name="hra" 
                                value="{{ old('hra', $employee->hra) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">DA</label>
                            <input type="number" class="form-control form-control-sm" name="da" 
                                value="{{ old('da', $employee->da) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Conveyance</label>
                            <input type="number" class="form-control form-control-sm" name="conveyance" 
                                value="{{ old('conveyance', $employee->conveyance) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Medical</label>
                            <input type="number" class="form-control form-control-sm" name="medical_allowance" 
                                value="{{ old('medical_allowance', $employee->medical_allowance) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label class="small mb-0 ">Special</label>
                            <input type="number" class="form-control form-control-sm" name="special_allowance" 
                                value="{{ old('special_allowance', $employee->special_allowance) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM ACTIONS -->
        <div class="row mt-3 mb-4">
            <div class="col-12 text-right">
                <a href="{{ route('admin.employees.index') }}" class="btn btn-light btn-sm px-4">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary btn-sm px-5 ml-2">
                    <i class="fas fa-save mr-1"></i> Update Employee
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.card { border: 1px solid rgba(0,0,0,.125); border-radius: .25rem; }
.card-header { border-bottom: 1px solid rgba(0,0,0,.125); }
.form-control-sm { font-size: .875rem; padding: .25rem .5rem; }
.small { font-size: .875rem; }
. { font-weight: 600 !important; }
.card-body.p-2 {
    padding: 20px !important;
}
.badge-sm { font-size: 10px; padding: 2px 4px; }
</style>

<script>
$(document).ready(function() {
    let educationIndex = {{ $employee->education->count() }};
    let experienceIndex = {{ $employee->experience->count() }};
    let assetIndex = {{ $employee->assets->count() }};

    // Same as present address
    $('#same_as_present').change(function() {
        if ($(this).is(':checked')) {
            $('input[name="permanent_address_line1"]').val($('input[name="present_address_line1"]').val());
            $('input[name="permanent_address_line2"]').val($('input[name="present_address_line2"]').val());
            $('input[name="permanent_city"]').val($('input[name="city"]').val());
            $('input[name="permanent_state"]').val($('input[name="state"]').val());
            $('input[name="permanent_postal_code"]').val($('input[name="postal_code"]').val());
            $('input[name="permanent_country"]').val($('input[name="country"]').val());
        }
    });

    // Add Education
    $('#add-education').click(function() {
        let html = `
            <div class="education-entry border rounded p-2 mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" name="education[${educationIndex}][course]" placeholder="Course">
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

    // Add Experience
    $('#add-experience').click(function() {
        let html = `
            <div class="experience-entry border rounded p-2 mb-2">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" name="experience[${experienceIndex}][company]" placeholder="Company">
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

    // Add Asset
    $('#add-asset').click(function() {
        let html = `
            <div class="asset-entry border rounded p-2 mb-2">
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" name="assets[${assetIndex}][type]">
                            <option value="">Type</option>
                            <option value="laptop">Laptop</option>
                            <option value="mobile">Mobile</option>
                            <option value="tablet">Tablet</option>
                            <option value="vehicle">Vehicle</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" name="assets[${assetIndex}][name]" placeholder="Asset Name">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" name="assets[${assetIndex}][serial_no]" placeholder="Serial #">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" name="assets[${assetIndex}][status]">
                            <option value="assigned">Assigned</option>
                            <option value="returned">Returned</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control form-control-sm" name="assets[${assetIndex}][given_on]" value="${new Date().toISOString().split('T')[0]}">
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
        assetIndex++;
    });

    // Remove entry
    $(document).on('click', '.remove-entry', function() {
        $(this).closest('.education-entry, .experience-entry, .asset-entry').remove();
    });

    // Experience status toggle
    $('input[name="experience_status"]').change(function() {
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

// Phone mask
$('.phone-mask').on('input', function() {
    let value = $(this).val().replace(/\D/g, '');
    if (value.length > 11) value = value.substr(0, 11);
    $(this).val(value);
});

// CNIC mask
$('.cnic-mask').on('input', function() {
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
</script>
@endsection