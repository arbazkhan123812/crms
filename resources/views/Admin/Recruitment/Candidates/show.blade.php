@extends('layout.admin')

@section('content')
    <div class="content">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title text-dark">
                        <i class="fas fa-user text-primary mr-2"></i>
                        {{ $candidate->first_name }} + {{ $candidate->last_name }}
                    </h3>
                    <p class="text-muted mb-0">{{ $candidate->job->title ?? 'N/A' }} • Applied
                        {{ $candidate->applied_date->format('d M Y') }}
                    </p>
                </div>
                <div class="col-auto">
                    <select class="form-control form-control-sm d-inline-block mr-2" style="width: auto;" id="statusSelect">
                        <option value="applied" {{ $candidate->status == 'applied' ? 'selected' : '' }}>Applied</option>
                        <option value="screening" {{ $candidate->status == 'screening' ? 'selected' : '' }}>Screening</option>
                        <option value="shortlisted" {{ $candidate->status == 'shortlisted' ? 'selected' : '' }}>Shortlisted
                        </option>
                        <option value="interview_scheduled" {{ $candidate->status == 'interview_scheduled' ? 'selected' : '' }}>Interview Scheduled</option>
                        <option value="interviewed" {{ $candidate->status == 'interviewed' ? 'selected' : '' }}>Interviewed
                        </option>
                        <option value="technical_round" {{ $candidate->status == 'technical_round' ? 'selected' : '' }}>
                            Technical Round</option>
                        <option value="hr_round" {{ $candidate->status == 'hr_round' ? 'selected' : '' }}>HR Round</option>
                        <option value="selected" {{ $candidate->status == 'selected' ? 'selected' : '' }}>Selected</option>
                        <option value="offered" {{ $candidate->status == 'offered' ? 'selected' : '' }}>Offered</option>
                        <option value="hired" {{ $candidate->status == 'hired' ? 'selected' : '' }}>Hired</option>
                        <option value="rejected" {{ $candidate->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="withdrawn" {{ $candidate->status == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                    </select>
                    <a href="{{ route('admin.recruitment.candidates') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        @if($candidate->photo_path)
                            <img src="{{ asset('storage/' . $candidate->photo_path) }}" class="img-fluid rounded-circle mb-3"
                                style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                style="width: 120px; height: 120px; font-size: 36px;">
                                {{ substr($candidate->first_name, 0, 1) }}{{ substr($candidate->last_name, 0, 1) }}
                            </div>
                        @endif
                        <h5>{{ $candidate->full_name }}</h5>
                        <p class="text-muted small">{{ $candidate->email }}</p>
                        <p class="text-muted small">{{ $candidate->phone }}</p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0">Documents</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @if($candidate->resume_path)
                                <div class="list-group-item px-3 py-2">
                                    <a href="{{ asset('storage/' . $candidate->resume_path) }}" target="_blank"
                                        class="text-primary">
                                        <i class="fas fa-file-pdf mr-2"></i> Resume
                                    </a>
                                </div>
                            @endif
                            @if($candidate->cover_letter_path)
                                <div class="list-group-item px-3 py-2">
                                    <a href="{{ asset('storage/' . $candidate->cover_letter_path) }}" target="_blank"
                                        class="text-primary">
                                        <i class="fas fa-file-alt mr-2"></i> Cover Letter
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#profile">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#interviews">Interviews</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#feedback">Feedback</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="profile">
                        <div class="card">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0">Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Current Company</label>
                                        <p class="mb-0">{{ $candidate->current_company ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Current Designation</label>
                                        <p class="mb-0">{{ $candidate->current_designation ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Experience</label>
                                        <p class="mb-0">{{ $candidate->experience_years ?? 0 }} years</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Current CTC</label>
                                        <p class="mb-0">
                                            {{ $candidate->current_ctc ? 'PKR ' . number_format($candidate->current_ctc) : 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Expected CTC</label>
                                        <p class="mb-0">
                                            {{ $candidate->expected_ctc ? 'PKR ' . number_format($candidate->expected_ctc) : 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Notice Period</label>
                                        <p class="mb-0">{{ $candidate->notice_period ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Available From</label>
                                        <p class="mb-0">
                                            {{ $candidate->available_from_date ? $candidate->available_from_date->format('d M Y') : 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Source</label>
                                        <p class="mb-0">{{ ucfirst($candidate->source) }}</p>
                                    </div>
                                </div>

                                <h6 class="text-primary mt-3 mb-2">Skills</h6>
                                <p>{{ $candidate->skills ?? 'N/A' }}</p>

                                <h6 class="text-primary mt-3 mb-2">Qualifications</h6>
                                <p>{{ $candidate->qualifications ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="interviews">
                        <div class="card">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Interview History</h6>
                                <button class="btn btn-sm btn-primary" onclick="scheduleInterview({{ $candidate->id }})">
                                    <i class="fas fa-plus mr-1"></i> Schedule Interview
                                </button>
                            </div>
                            <div class="list-group list-group-flush">
                                @forelse($candidate->interviews as $interview)
                                    <div class="list-group-item px-3 py-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>{{ $interview->round_label }}</strong>
                                                        <span
                                                            class="badge badge-{{ $interview->status == 'completed' ? 'success' : 'warning' }} ml-2">
                                                            {{ ucfirst($interview->status) }}
                                                        </span>
                                                    </div>
                                                    @if($interview->status == 'scheduled')
                                                        <button class="btn btn-sm btn-success"
                                                            onclick="addFeedback({{ $interview->id }})">
                                                            <i class="fas fa-star mr-1"></i> Add Feedback
                                                        </button>
                                                        <a href="{{ route('admin.recruitment.interviews.destroy', $interview->id) }}"
                                                            class="btn btn-sm btn-danger" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this {{ $interview->round_label }} interview?');">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    @endif
                                                </div>

                                                <div class="small text-muted mt-1">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    {{ $interview->scheduled_at->format('d M Y h:i A') }} •
                                                    <i class="fas fa-clock mr-1"></i> {{ $interview->duration_minutes }} minutes
                                                    •
                                                    <i class="fas fa-video mr-1"></i> {{ ucfirst($interview->mode) }}
                                                </div>

                                                <div class="small">
                                                    <i class="fas fa-user-tie mr-1"></i> Interviewer:
                                                    {{ $interview->interviewer->name }}
                                                    @if($interview->location_or_link)
                                                        • <i class="fas fa-map-marker-alt mr-1"></i>
                                                        {{ $interview->location_or_link }}
                                                    @endif
                                                </div>

                                                @if($interview->notes)
                                                    <div class="small text-muted mt-1">
                                                        <i class="fas fa-sticky-note mr-1"></i> {{ $interview->notes }}
                                                    </div>
                                                @endif

                                                @if($interview->feedback)
                                                    <div class="mt-3 p-3 bg-light rounded">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <strong class="text-primary">Interview Feedback</strong>
                                                            <span
                                                                class="badge badge-{{ $interview->result == 'selected' ? 'success' : ($interview->result == 'rejected' ? 'danger' : 'warning') }}">
                                                                {{ ucfirst($interview->result) }}
                                                            </span>
                                                        </div>

                                                        <div class="mb-2">
                                                            <div class="text-warning mb-1">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    @if($i <= $interview->rating)
                                                                        <i class="fas fa-star text-warning"></i>
                                                                    @else
                                                                        <i class="far fa-star text-warning"></i>
                                                                    @endif
                                                                @endfor
                                                                <span class="ml-2 text-dark">({{ $interview->rating }}/5)</span>
                                                            </div>
                                                        </div>

                                                        <p class="mb-0">{{ $interview->feedback }}</p>

                                                        @if($interview->created_at != $interview->updated_at)
                                                            <small class="text-muted d-block mt-2">
                                                                Feedback added: {{ $interview->updated_at->format('d M Y h:i A') }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">No interviews scheduled</h6>
                                        <button class="btn btn-sm btn-primary mt-2"
                                            onclick="scheduleInterview({{ $candidate->id }})">
                                            <i class="fas fa-plus mr-1"></i> Schedule First Interview
                                        </button>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="feedback">
                        <div class="card">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0">Feedback & Notes</h6>
                            </div>
                            <div class="card-body">
                                @if($candidate->interview_feedback)
                                    @foreach($candidate->interview_feedback as $feedback)
                                        <div class="border-bottom mb-3 pb-2">
                                            <strong>{{ $feedback['round'] }}</strong>
                                            <p class="mb-1">{{ $feedback['comments'] }}</p>
                                            <small class="text-muted">Rating: {{ $feedback['rating'] }}/5</small>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted text-center">No feedback yet</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title">
                        <i class="fas fa-star mr-2"></i>
                        Interview Feedback
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
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
                    <input type="hidden" id="feedback_interview_id" name="interview_id">

                    <div class="modal-body">
                        <div class="text-center mb-3" id="interviewSummary">
                            <!-- Will be filled via AJAX -->
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Rating <span class="text-danger">*</span></label>
                            <div class="rating-stars mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="far fa-star fa-lg rating-star" data-rating="{{ $i }}"
                                        style="cursor: pointer; color: #ffc107; margin-right: 5px;"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="rating_value" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Feedback <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm" name="feedback" rows="4"
                                placeholder="Write detailed feedback about the interview..." required></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Result <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" name="result" required>
                                <option value="">Select Result</option>
                                <option value="selected"> Selected - Move to next round</option>
                                <option value="rejected"> Rejected</option>
                                <option value="on_hold"> On Hold - Need further discussion</option>
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label">Strengths (Optional)</label>
                            <textarea class="form-control form-control-sm" name="strengths" rows="2"
                                placeholder="What went well?"></textarea>
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label">Areas for Improvement (Optional)</label>
                            <textarea class="form-control form-control-sm" name="improvements" rows="2"
                                placeholder="What could be better?"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="submitFeedback">
                            <i class="fas fa-save mr-1"></i> Submit Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="scheduleInterviewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Schedule Interview
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="scheduleInterviewForm">
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
                    <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                    <input type="hidden" name="job_id" value="{{ $candidate->job_id }}">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Interviewer <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" name="interviewer_id" required>
                                    <option value="">Select Interviewer</option>
                                    @foreach(App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}">{{ $user->username }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Interview Round <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" name="round" required>
                                    <option value="screening">Screening</option>
                                    <option value="technical_1">Technical Round 1</option>
                                    <option value="technical_2">Technical Round 2</option>
                                    <option value="hr">HR Round</option>
                                    <option value="manager">Manager Round</option>
                                    <option value="final">Final Round</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-sm" name="date"
                                    min="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control form-control-sm" name="time" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration (minutes)</label>
                                <select class="form-control form-control-sm" name="duration_minutes">
                                    <option value="30">30 minutes</option>
                                    <option value="45">45 minutes</option>
                                    <option value="60" selected>60 minutes</option>
                                    <option value="90">90 minutes</option>
                                    <option value="120">2 hours</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mode <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" name="mode" required>
                                    <option value="online">Online</option>
                                    <option value="offline">Offline</option>
                                    <option value="phone">Phone</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Location / Meeting Link</label>
                                <input type="text" class="form-control form-control-sm" name="location_or_link"
                                    placeholder="Google Meet link or Office address">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Notes / Instructions</label>
                                <textarea class="form-control form-control-sm" name="notes" rows="2"
                                    placeholder="Any special instructions for the interview"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-calendar-check mr-1"></i> Schedule Interview
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success/Error Message Area -->
    <div id="interviewMessage" style="display: none;"></div>

    <script>
        function scheduleInterview(candidateId) {
            $('#scheduleInterviewModal').modal('show');
        }

        $('#scheduleInterviewForm').submit(function (e) {
            e.preventDefault();

            let formData = $(this).serialize();
            let submitBtn = $(this).find('button[type="submit"]');

            // Combine date and time
            let date = $('input[name="date"]').val();
            let time = $('input[name="time"]').val();
            let scheduledAt = date + ' ' + time + ':00';

            formData += '&scheduled_at=' + encodeURIComponent(scheduledAt);

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Scheduling...');

            $.ajax({
                url: '{{ route("admin.recruitment.interviews.schedule") }}',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    $('#scheduleInterviewModal').modal('hide');
                    toastr.success('Interview scheduled successfully');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-calendar-check mr-1"></i> Schedule Interview');

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        $.each(errors, function (key, value) {
                            errorMsg += value[0] + '<br>';
                        });
                        toastr.error(errorMsg);
                    } else {
                        toastr.error('Error scheduling interview. Please try again.');
                    }
                }
            });
        });
        $('#statusSelect').change(function () {
            $.ajax({
                url: '{{ route("admin.recruitment.candidates.status", $candidate->id) }}',
                type: 'POST',
                data: {
                    status: $(this).val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function () {
                    toastr.success('Status updated');
                }
            });
        });
        $(document).on('mouseenter', '.rating-star', function () {
            let rating = $(this).data('rating');
            $('.rating-star').each(function () {
                if ($(this).data('rating') <= rating) {
                    $(this).removeClass('far').addClass('fas');
                } else {
                    $(this).removeClass('fas').addClass('far');
                }
            });
        }).on('mouseleave', '.rating-star', function () {
            let currentRating = $('#rating_value').val();
            $('.rating-star').each(function () {
                if ($(this).data('rating') <= currentRating) {
                    $(this).removeClass('far').addClass('fas');
                } else {
                    $(this).removeClass('fas').addClass('far');
                }
            });
        }).on('click', '.rating-star', function () {
            let rating = $(this).data('rating');
            $('#rating_value').val(rating);
        });

        // Add feedback function
        function addFeedback(interviewId) {
            $('#feedback_interview_id').val(interviewId);

            // Reset form
            $('#feedbackForm')[0].reset();
            $('#rating_value').val('');
            $('.rating-star').removeClass('fas').addClass('far');

            // Get interview details
            $.get('/admin/recruitment/interviews/' + interviewId + '/details', function (data) {
                let html = `
                    <div class="bg-light p-2 rounded">
                        <strong>${data.round_label}</strong><br>
                        <small class="text-muted">
                            ${data.scheduled_at} • ${data.interviewer_name}
                        </small>
                    </div>
                `;
                $('#interviewSummary').html(html);
            });

            $('#feedbackModal').modal('show');
        }

        // Submit feedback
        $('#feedbackForm').submit(function (e) {
            e.preventDefault();

            let interviewId = $('#feedback_interview_id').val();
            let formData = $(this).serialize();
            let submitBtn = $(this).find('button[type="submit"]');

            // Validate rating
            if (!$('#rating_value').val()) {
                toastr.error('Please select a rating');
                return;
            }

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Submitting...');

            $.ajax({
                url: '{{ route("admin.recruitment.interviews.feedback", "") }}/' + interviewId,
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    $('#feedbackModal').modal('hide');
                    toastr.success('Feedback submitted successfully');
                    setTimeout(() => location.reload(), 1500);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Submit Feedback');

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        $.each(errors, function (key, value) {
                            errorMsg += value[0] + '<br>';
                        });
                        toastr.error(errorMsg);
                    } else {
                        toastr.error('Error submitting feedback. Please try again.');
                    }
                }
            });
        });

    </script>
@endsection