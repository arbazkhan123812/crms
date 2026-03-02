@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-user-plus text-primary mr-2"></i>
                    Add New Candidate
                </h3>
                <p class="text-muted mb-0">Add a candidate manually</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.recruitment.candidates') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.recruitment.candidates.store') }}" method="POST" enctype="multipart/form-data">
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

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="first_name" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="last_name" value="{{ old('last_name') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-sm" name="email" value="{{ old('email') }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="phone" value="{{ old('phone') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Job <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="job_id" required>
                            <option value="">Select Job</option>
                            @foreach($jobs as $job)
                                <option value="{{ $job->id }}" {{ old('job_id') == $job->id ? 'selected' : '' }}>{{ $job->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Source</label>
                        <select class="form-control form-control-sm" name="source">
                            <option value="website">Website</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="referral">Referral</option>
                            <option value="job_portal">Job Portal</option>
                            <option value="walk_in">Walk In</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Current Company</label>
                        <input type="text" class="form-control form-control-sm" name="current_company" value="{{ old('current_company') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Current Designation</label>
                        <input type="text" class="form-control form-control-sm" name="current_designation" value="{{ old('current_designation') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Experience (Years)</label>
                        <input type="number" class="form-control form-control-sm" name="experience_years" value="{{ old('experience_years') }}" min="0" max="50">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Current CTC</label>
                        <input type="number" class="form-control form-control-sm" name="current_ctc" value="{{ old('current_ctc') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Expected CTC</label>
                        <input type="number" class="form-control form-control-sm" name="expected_ctc" value="{{ old('expected_ctc') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Notice Period</label>
                        <input type="text" class="form-control form-control-sm" name="notice_period" value="{{ old('notice_period') }}" placeholder="30 days">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Available From</label>
                        <input type="date" class="form-control form-control-sm" name="available_from_date" value="{{ old('available_from_date') }}">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Skills</label>
                        <input type="text" class="form-control form-control-sm" name="skills" value="{{ old('skills') }}" placeholder="PHP, Laravel, MySQL">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Qualifications</label>
                        <textarea class="form-control form-control-sm" name="qualifications" rows="2">{{ old('qualifications') }}</textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Resume (PDF, DOC)</label>
                        <input type="file" class="form-control-file" name="resume" accept=".pdf,.doc,.docx">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Cover Letter</label>
                        <input type="file" class="form-control-file" name="cover_letter" accept=".pdf,.doc,.docx">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Photo</label>
                        <input type="file" class="form-control-file" name="photo" accept="image/*">
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary btn-sm px-5">Save Candidate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection