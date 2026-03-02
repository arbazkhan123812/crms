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
                    <i class="fas fa-calendar-check text-primary mr-2"></i>
                    Interviews
                </h3>
                <p class="text-muted mb-0">Manage all scheduled interviews</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm" onclick="$('#scheduleModal').modal('show')">
                    <i class="fas fa-plus mr-1"></i> Schedule Interview
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Candidate</th>
                            <th>Job</th>
                            <th>Interviewer</th>
                            <th>Round</th>
                            <th>Scheduled At</th>
                            <th>Mode</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($interviews as $interview)
                        <tr>
                            <td>{{ $interview->candidate->full_name }}</td>
                            <td>{{ $interview->job->title }}</td>
                            <td>{{ $interview->interviewer->name }}</td>
                            <td>{{ $interview->round_label }}</td>
                            <td>{{ $interview->scheduled_at->format('d M Y h:i A') }}</td>
                            <td>{{ ucfirst($interview->mode) }}</td>
                            <td>
                                <span class="badge badge-{{ $interview->status == 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($interview->status) }}
                                </span>
                            </td>
                            <td>
                                @if($interview->status == 'scheduled')
                                <button class="btn btn-sm btn-link" onclick="addFeedback({{ $interview->id }})">
                                    Add Feedback
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $interviews->links() }}
        </div>
    </div>
</div>

<!-- Schedule Interview Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title">Schedule Interview</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="scheduleForm">
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
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label>Candidate</label>
                        <select class="form-control form-control-sm" name="candidate_id" required>
                            <option value="">Select Candidate</option>
                            @foreach(App\Models\Candidate::all() as $candidate)
                            <option value="{{ $candidate->id }}">{{ $candidate->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Job</label>
                        <select class="form-control form-control-sm" name="job_id" required>
                            <option value="">Select Job</option>
                            @foreach(App\Models\Job::where('status', 'published')->get() as $job)
                            <option value="{{ $job->id }}">{{ $job->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Interviewer</label>
                        <select class="form-control form-control-sm" name="interviewer_id" required>
                            <option value="">Select Interviewer</option>
                            @foreach(App\Models\User::all() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Round</label>
                        <select class="form-control form-control-sm" name="round" required>
                            <option value="screening">Screening</option>
                            <option value="technical_1">Technical Round 1</option>
                            <option value="technical_2">Technical Round 2</option>
                            <option value="hr">HR Round</option>
                            <option value="manager">Manager Round</option>
                            <option value="final">Final Round</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Date & Time</label>
                        <input type="datetime-local" class="form-control form-control-sm" name="scheduled_at" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Duration (minutes)</label>
                        <input type="number" class="form-control form-control-sm" name="duration_minutes" value="60" min="15" max="240" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Mode</label>
                        <select class="form-control form-control-sm" name="mode" required>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                            <option value="phone">Phone</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Location/Link</label>
                        <input type="text" class="form-control form-control-sm" name="location_or_link">
                    </div>
                    <div class="form-group mb-2">
                        <label>Notes</label>
                        <textarea class="form-control form-control-sm" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title">Interview Feedback</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="feedbackForm">
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
                <input type="hidden" id="interview_id" name="interview_id">
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label>Rating (1-5)</label>
                        <select class="form-control form-control-sm" name="rating" required>
                            <option value="1">1 - Poor</option>
                            <option value="2">2 - Fair</option>
                            <option value="3">3 - Good</option>
                            <option value="4">4 - Very Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Feedback</label>
                        <textarea class="form-control form-control-sm" name="feedback" rows="4" required></textarea>
                    </div>
                    <div class="form-group mb-2">
                        <label>Result</label>
                        <select class="form-control form-control-sm" name="result" required>
                            <option value="selected">Selected</option>
                            <option value="rejected">Rejected</option>
                            <option value="on_hold">On Hold</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('#scheduleForm').submit(function(e) {
    e.preventDefault();
    $.ajax({
        url: '{{ route("admin.recruitment.interviews.schedule") }}',
        type: 'POST',
        data: $(this).serialize(),
        success: function() {
            toastr.success('Interview scheduled');
            $('#scheduleModal').modal('hide');
            location.reload();
        }
    });
});
function addFeedback(id) {
    $('#interview_id').val(id);
    $('#feedbackModal').modal('show');
}

$('#feedbackForm').submit(function(e) {
    e.preventDefault();
    let id = $('#interview_id').val();
    $.ajax({
        url: '{{ route("admin.recruitment.interviews.feedback", "") }}/' + id,
        type: 'POST',
        data: $(this).serialize(),
        success: function() {
            toastr.success('Feedback submitted');
            $('#feedbackModal').modal('hide');
            location.reload();
        }
    });
});
</script>
@endsection