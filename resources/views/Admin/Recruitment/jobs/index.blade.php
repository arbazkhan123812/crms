@extends('layout.admin')

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
                    <h3 class="page-title font-weight-bold">
                        <i class="fas fa-briefcase text-primary mr-2"></i> Job Openings
                    </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent p-0">
                            <li class="breadcrumb-item"><a href="#">Recruitment</a></li>
                            <li class="breadcrumb-item active">Jobs</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.recruitment.jobs.create') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus-circle mr-1"></i> Post New Job
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="myTable">
                        <thead class="bg-light text-uppercase font-weight-bold" style="font-size: 0.85rem;">
                            <tr>
                                <th class="px-4">Job Title</th>
                                <th>Department</th>
                                <th>Vacancies</th>
                                <th>Posted / Closing</th>
                                <th class="text-center">Apps</th>
                                <th>Status</th>
                                <th class="text-right px-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                                <tr>
                                    <td class="px-4">
                                        <div class="font-weight-bold text-dark">{{ $job->title }}</div>
                                        <small class="text-muted">{{ $job->designation->title }}</small>
                                    </td>
                                    <td><span class="badge badge-soft-secondary p-2">{{ $job->department->name }}</span></td>
                                    <td class="text-center">{{ $job->vacancies }}</td>
                                    <td>
                                        <div style="font-size: 0.9rem;">{{ $job->posted_date->format('d M') }}</div>
                                        @if($job->days_remaining < 0)
                                            <span class="text-danger small font-weight-bold"><i
                                                    class="fas fa-clock mr-1"></i>Expired</span>
                                        @elseif($job->days_remaining <= 7)
                                            <span class="text-warning small font-weight-bold"><i
                                                    class="fas fa-exclamation-triangle mr-1"></i>{{ $job->days_remaining }}d
                                                left</span>
                                        @else
                                            <small class="text-muted">{{ $job->closing_date->format('d M Y') }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="#"
                                            class="badge badge-pill badge-info py-1 px-2">{{ $job->candidates_count }}</a>
                                    </td>
                                    <td>
                                        <span>{{ $job->status }}</span>
                                    </td>
                                    <td class="text-right px-4">
                                        <div class="btn-group border rounded shadow-sm bg-white">
                                            <a href="{{ route('admin.recruitment.jobs.show', $job->id) }}"
                                                class="btn btn-sm text-primary border-right" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.recruitment.jobs.edit', $job->id) }}"
                                                class="btn btn-sm text-info" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.recruitment.jobs.destroy', $job->id) }}"
                                                class="btn btn-sm text-danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete job: {{ $job->title }}? This action cannot be undone.');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if($jobs->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $jobs->links() }}
                </div>
            @endif
        </div>
    </div>


    <style>
        /* Modern Styling Fixes */
        .badge-soft-secondary {
            background-color: #f1f3f5;
            color: #495057;
        }

        .table td {
            vertical-align: middle !important;
        }

        .custom-select {
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-group .btn:hover {
            background: #f8f9fa;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Status Update AJAX
            $('.status-select').on('change', function () {
                let jobId = $(this).data('id');
                let status = $(this).val();
                let $select = $(this);

                // UI feedback start
                $select.addClass('opacity-50');

                $.ajax({
                    url: "{{ url('admin/recruitment/jobs') }}/" + jobId + "/status",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status
                    },
                    success: function (response) {
                        // Success feedback
                        $select.removeClass('opacity-50 border-success');
                        if (status === 'published') $select.addClass('border-success');

                        // Toastr ya alert de saktay hain
                        console.log("Status updated to: " + status);
                    },
                    error: function (xhr) {
                        $select.removeClass('opacity-50');
                        alert("Error updating status. Please check route parameter.");
                    }
                });
            });
        });
    </script>
@endsection