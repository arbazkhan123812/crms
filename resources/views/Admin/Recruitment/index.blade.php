@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-bullhorn text-primary mr-2"></i>
                    Recruitment Dashboard
                </h3>
                <p class="text-muted mb-0">Manage job postings, candidates, and interviews</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.recruitment.jobs.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Post New Job
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-2 col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <h6>Active Jobs</h6>
                    <h3>{{ $stats['active_jobs'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <h6>Total Candidates</h6>
                    <h3>{{ $stats['total_candidates'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body py-3">
                    <h6>In Process</h6>
                    <h3>{{ $stats['in_process'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body py-3">
                    <h6>Interviews Today</h6>
                    <h3>{{ $stats['interviews_today'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body py-3">
                    <h6>Pending Review</h6>
                    <h3>{{ $stats['pending_review'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body py-3">
                    <h6>Hired</h6>
                    <h3>{{ $stats['hired'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-briefcase text-primary mr-2"></i>Recent Job Posts</h6>
                    <a href="{{ route('admin.recruitment.jobs') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($recent_jobs as $job)
                    <div class="list-group-item px-3 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $job->title }}</strong>
                                <div class="small text-muted">
                                    {{ $job->department->name }} • {{ $job->vacancies }} vacancies
                                </div>
                            </div>
                            <span class="badge badge-{{ $job->status == 'published' ? 'success' : 'secondary' }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-users text-primary mr-2"></i>Recent Candidates</h6>
                    <a href="{{ route('admin.recruitment.candidates') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($recent_candidates as $candidate)
                    <div class="list-group-item px-3 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $candidate->full_name }}</strong>
                                <div class="small text-muted">
                                    {{ $candidate->job->title ?? 'N/A' }}
                                </div>
                            </div>
                            <span class="badge badge-{{ $candidate->status_badge }}">
                                {{ $candidate->status_label }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="fas fa-calendar text-primary mr-2"></i>Upcoming Interviews</h6>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($upcoming_interviews as $interview)
                    <div class="list-group-item px-3 py-2">
                        <div class="d-flex">
                            <div class="bg-light rounded p-2 text-center mr-3" style="width: 50px;">
                                <div class="small">{{ $interview->scheduled_at->format('d M') }}</div>
                                <div class="font-weight-bold">{{ $interview->scheduled_at->format('H:i') }}</div>
                            </div>
                            <div>
                                <strong>{{ $interview->candidate->full_name }}</strong>
                                <div class="small text-muted">
                                    {{ $interview->job->title }} • {{ $interview->round_label }}
                                </div>
                                <div class="small">
                                    <i class="fas fa-user-tie mr-1"></i>{{ $interview->interviewer->name }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        No upcoming interviews
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card { border: none; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.card-header { background-color: #f8f9fc; border-bottom: 1px solid #e9ecef; }
</style>
@endsection