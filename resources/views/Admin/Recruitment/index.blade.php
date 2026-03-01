@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-bullhorn text-primary mr-2"></i>
                    Recruitment
                </h3>
                <p class="text-muted mb-0">Manage job postings and candidates</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.recruitment.jobs.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Post New Job
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <h6>Active Jobs</h6>
                    <h3>{{ $stats['active_jobs'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <h6>Total Candidates</h6>
                    <h3>{{ $stats['total_candidates'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-3">
                    <h6>In Process</h6>
                    <h3>{{ $stats['in_process'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body py-3">
                    <h6>Hired</h6>
                    <h3>{{ $stats['hired'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Open Positions</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Job Title</th>
                                <th>Department</th>
                                <th>Vacancies</th>
                                <th>Posted</th>
                                <th>Closing</th>
                                <th>Candidates</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                            <tr>
                                <td><strong>{{ $job->title }}</strong></td>
                                <td>{{ $job->department->name }}</td>
                                <td>{{ $job->vacancies }}</td>
                                <td>{{ $job->posted_date->format('d M') }}</td>
                                <td>{{ $job->closing_date->format('d M') }}</td>
                                <td><span class="badge badge-info">{{ $job->candidates_count ?? 0 }}</span></td>
                                <td>
                                    <a href="{{ route('admin.recruitment.candidates', $job->id) }}" class="btn btn-sm btn-link">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Recent Candidates</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($candidates as $candidate)
                    <div class="list-group-item px-3 py-2">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $candidate->name }}</strong>
                                <div class="small text-muted">{{ $candidate->job->title }}</div>
                            </div>
                            <span class="badge badge-{{ $candidate->status == 'applied' ? 'secondary' : ($candidate->status == 'shortlisted' ? 'info' : ($candidate->status == 'hired' ? 'success' : 'danger')) }}">
                                {{ ucfirst($candidate->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection