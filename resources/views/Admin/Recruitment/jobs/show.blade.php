@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-briefcase text-primary mr-2"></i>
                    {{ $job->title }}
                </h3>
                <p class="text-muted mb-0">{{ $job->department->name }} • {{ $job->job_type }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.recruitment.jobs.edit', $job->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('admin.recruitment.jobs') }}" class="btn btn-outline-secondary btn-sm ml-2">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Job Details</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small">Department</label>
                            <p class="mb-0">{{ $job->department->name }}</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small">Designation</label>
                            <p class="mb-0">{{ $job->designation->title }}</p>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="text-muted small">Vacancies</label>
                            <p class="mb-0">{{ $job->vacancies }}</p>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="text-muted small">Experience</label>
                            <p class="mb-0">{{ ucfirst($job->experience_level) }}</p>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="text-muted small">Job Type</label>
                            <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $job->job_type)) }}</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small">Location</label>
                            <p class="mb-0">{{ $job->location ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small">Salary Range</label>
                            <p class="mb-0">{{ $job->salary_range }}</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small">Posted Date</label>
                            <p class="mb-0">{{ $job->posted_date->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small">Closing Date</label>
                            <p class="mb-0">
                                {{ $job->closing_date->format('d M Y') }}
                                @if($job->days_remaining < 0)
                                    <span class="badge badge-danger">Expired</span>
                                @elseif($job->days_remaining <= 7)
                                    <span class="badge badge-warning">{{ $job->days_remaining }} days left</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <h6 class="text-primary mb-2">Description</h6>
                    <p class="mb-4">{{ $job->description }}</p>

                    <h6 class="text-primary mb-2">Requirements</h6>
                    <p class="mb-4">{{ $job->requirements }}</p>

                    @if($job->responsibilities)
                    <h6 class="text-primary mb-2">Responsibilities</h6>
                    <p class="mb-4">{{ $job->responsibilities }}</p>
                    @endif

                    @if($job->qualifications)
                    <h6 class="text-primary mb-2">Qualifications</h6>
                    <p class="mb-0">{{ $job->qualifications }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Applications ({{ $job->candidates_count }})</h6>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($candidates as $candidate)
                    <div class="list-group-item px-3 py-2">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $candidate->full_name }}</strong>
                                <div class="small text-muted">{{ $candidate->email }}</div>
                            </div>
                            <span class="badge badge-{{ $candidate->status_badge }}">{{ $candidate->status_label }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">No applications yet</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection