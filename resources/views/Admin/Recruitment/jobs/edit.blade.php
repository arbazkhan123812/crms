@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-edit text-primary mr-2"></i>
                    Edit Job
                </h3>
                <p class="text-muted mb-0">{{ $job->title }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.recruitment.jobs') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.recruitment.jobs.update', $job->id) }}" method="POST">
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
                @method('PUT')

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Job Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm @error('title') is-invalid @enderror" 
                               name="title" value="{{ old('title', $job->title) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Vacancies <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" name="vacancies" value="{{ old('vacancies', $job->vacancies) }}" min="1" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Department</label>
                        <select class="form-control form-control-sm" name="department_id" required>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $job->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Designation</label>
                        <select class="form-control form-control-sm" name="designation_id" required>
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}" {{ old('designation_id', $job->designation_id) == $desig->id ? 'selected' : '' }}>
                                    {{ $desig->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Job Type</label>
                        <select class="form-control form-control-sm" name="job_type">
                            <option value="full_time" {{ old('job_type', $job->job_type) == 'full_time' ? 'selected' : '' }}>Full Time</option>
                            <option value="part_time" {{ old('job_type', $job->job_type) == 'part_time' ? 'selected' : '' }}>Part Time</option>
                            <option value="contract" {{ old('job_type', $job->job_type) == 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="internship" {{ old('job_type', $job->job_type) == 'internship' ? 'selected' : '' }}>Internship</option>
                            <option value="remote" {{ old('job_type', $job->job_type) == 'remote' ? 'selected' : '' }}>Remote</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Experience Level</label>
                        <select class="form-control form-control-sm" name="experience_level">
                            <option value="entry" {{ old('experience_level', $job->experience_level) == 'entry' ? 'selected' : '' }}>Entry Level</option>
                            <option value="mid" {{ old('experience_level', $job->experience_level) == 'mid' ? 'selected' : '' }}>Mid Level</option>
                            <option value="senior" {{ old('experience_level', $job->experience_level) == 'senior' ? 'selected' : '' }}>Senior Level</option>
                            <option value="lead" {{ old('experience_level', $job->experience_level) == 'lead' ? 'selected' : '' }}>Lead</option>
                            <option value="manager" {{ old('experience_level', $job->experience_level) == 'manager' ? 'selected' : '' }}>Manager</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control form-control-sm" name="location" value="{{ old('location', $job->location) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Min Salary</label>
                        <input type="number" class="form-control form-control-sm" name="min_salary" value="{{ old('min_salary', $job->min_salary) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Salary</label>
                        <input type="number" class="form-control form-control-sm" name="max_salary" value="{{ old('max_salary', $job->max_salary) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Closing Date</label>
                        <input type="date" class="form-control form-control-sm" name="closing_date" value="{{ old('closing_date', $job->closing_date->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control form-control-sm" name="status">
                            <option value="draft" {{ old('status', $job->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $job->status) == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="closed" {{ old('status', $job->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="on_hold" {{ old('status', $job->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control form-control-sm" name="description" rows="4" required>{{ old('description', $job->description) }}</textarea>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Requirements</label>
                        <textarea class="form-control form-control-sm" name="requirements" rows="4" required>{{ old('requirements', $job->requirements) }}</textarea>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Responsibilities</label>
                        <textarea class="form-control form-control-sm" name="responsibilities" rows="3">{{ old('responsibilities', $job->responsibilities) }}</textarea>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Qualifications</label>
                        <textarea class="form-control form-control-sm" name="qualifications" rows="3">{{ old('qualifications', $job->qualifications) }}</textarea>
                    </div>
                </div>

                <div class="text-right">
                    <a href="{{ route('admin.recruitment.jobs') }}" class="btn btn-light btn-sm px-4 mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm px-5">Update Job</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection