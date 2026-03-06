@extends('layout.admin')

@section('content')
    <div class="content">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title text-dark">
                        <i class="fas fa-plus-circle text-primary mr-2"></i>
                        Post New Job
                    </h3>
                    <p class="text-muted mb-0">Create a new job opening</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.recruitment.jobs') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <form action="{{ route('admin.recruitment.jobs.store') }}" method="POST">
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
                        <div class="col-md-6 mb-3">
                            <label>Job Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="title" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Department <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" name="department_id" required>
                                <option value="">Select</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Designation <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" name="designation_id" required>
                                <option value="">Select</option>
                                @foreach($designations as $desig)
                                    <option value="{{ $desig->id }}">{{ $desig->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label>Vacancies <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" name="vacancies" min="1" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Experience Level</label>
                            <select class="form-control form-control-sm" name="experience_level">
                                <option value="entry">Entry Level</option>
                                <option value="mid" selected>Mid Level</option>
                                <option value="senior">Senior Level</option>
                                <option value="lead">Lead</option>
                                <option value="manager">Manager</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Job Type</label>
                            <select class="form-control form-control-sm" name="job_type">
                                <option value="full_time">Full Time</option>
                                <option value="part_time">Part Time</option>
                                <option value="contract">Contract</option>
                                <option value="internship">Internship</option>
                                <option value="remote">Remote</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Location</label>
                            <input type="text" class="form-control form-control-sm" name="location" placeholder="Karachi">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Closing Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" name="closing_date" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Min Salary (PKR)</label>
                            <input type="number" class="form-control form-control-sm" name="min_salary">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Max Salary (PKR)</label>
                            <input type="number" class="form-control form-control-sm" name="max_salary">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Status</label>
                            <select class="form-control form-control-sm" name="status">
                                <option value="draft">Save as Draft</option>
                                <option value="published">Publish Now</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Job Description <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm" name="description" rows="4" required></textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Requirements <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm" name="requirements" rows="4" required></textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Responsibilities</label>
                            <textarea class="form-control form-control-sm" name="responsibilities" rows="3"></textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Qualifications</label>
                            <textarea class="form-control form-control-sm" name="qualifications" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary px-4">Post Job</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection