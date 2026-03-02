@extends('layout.app')

@section('content')
    <div class="content">
        <div class="page-header mb-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title text-dark">
                        <i class="fas fa-users text-primary mr-2"></i>
                        Candidates
                    </h3>
                    <p class="text-muted mb-0">Manage all job applicants</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.recruitment.candidates.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Add Candidate
                    </a>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body py-3">
                <form method="GET" id="filterForm">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <input type="text" class="form-control form-control-sm" name="search"
                                value="{{ request('search') }}" placeholder="Search name, email...">
                        </div>
                        <div class="col-md-2 mb-2">
                            <select class="form-control form-control-sm" name="job_id">
                                <option value="">All Jobs</option>
                                @foreach($jobs as $job)
                                    <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>
                                        {{ $job->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select class="form-control form-control-sm" name="status">
                                <option value="">All Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-3 text-right">
                            <span class="text-muted small">Total: {{ $candidates->total() }} candidates</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Candidate</th>
                                <th>Applied For</th>
                                <th>Applied On</th>
                                <th>Experience</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($candidates as $candidate)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($candidate->photo_path)
                                                <img src="{{ asset('storage/' . $candidate->photo_path) }}"
                                                    class="rounded-circle mr-2" width="32" height="32" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2"
                                                    style="width: 32px; height: 32px;">
                                                    {{ substr($candidate->first_name, 0, 1) }}{{ substr($candidate->last_name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $candidate->full_name }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $candidate->job->title ?? 'N/A' }}</td>
                                    <td>{{ $candidate->applied_date->format('d M Y') }}</td>
                                    <td>{{ $candidate->experience_years ?? 0 }} years</td>
                                    <td>
                                        <small>{{ $candidate->email }}<br>{{ $candidate->phone }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $candidate->status_badge }}">
                                            {{ $candidate->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.recruitment.candidates.show', $candidate->id) }}"
                                            class="btn btn-sm btn-link">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.recruitment.candidates.destroy', $candidate->id) }}"
                                        class="btn btn-sm btn-link text-danger" title="Delete"
                                        onclick="return confirm('Are you sure you want to delete candidate: {{ $candidate->full_name }}? This action cannot be undone.');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    </td>
                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <h5>No Candidates Found</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $candidates->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection