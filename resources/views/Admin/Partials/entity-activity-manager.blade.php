@php
    $activityEntity = $entity;
    $activityEntityType = $entityType;
    $activityLabel = ucfirst($entityType);
@endphp

<div class="card border mt-4 entity-activity-card">
    <div class="card-header bg-light py-2">
        <h6 class="mb-0">
            <i class="fas fa-layer-group text-primary mr-2"></i>{{ $activityLabel }} Activities
        </h6>
    </div>
    <div class="card-body">
        <ul class="nav nav-pills lead-detail-tabs mb-4">
            <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#{{ $activityEntityType }}-notes-pane">Notes</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#{{ $activityEntityType }}-tasks-pane">Tasks</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#{{ $activityEntityType }}-calls-pane">Calls</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#{{ $activityEntityType }}-meetings-pane">Meetings</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="{{ $activityEntityType }}-notes-pane">
                <div class="section-toolbar mb-3">
                    <h5 class="section-title mb-0">Notes</h5>
                    <button class="btn btn-sm btn-primary" type="button" onclick="openEntityNoteModal()">Add Note</button>
                </div>
                @forelse($activityEntity->notes as $note)
                    <div class="timeline-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ optional($note->createdBy)->username ?? optional($note->createdBy)->name ?? 'User' }}</h6>
                                <p class="mb-1">{{ $note->note }}</p>
                                <small class="text-muted">{{ optional($note->created_at)->format('d M Y, h:i A') }}</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-light" type="button" onclick="editEntityNote({{ $note->id }}, @js($note->note), @js($note->type))">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-light text-danger" type="button" onclick="deleteEntityNote({{ $note->id }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No notes available.</div>
                @endforelse
            </div>

            <div class="tab-pane fade" id="{{ $activityEntityType }}-tasks-pane">
                <div class="section-toolbar mb-3">
                    <h5 class="section-title mb-0">Tasks</h5>
                    <button class="btn btn-sm btn-primary" type="button" onclick="openEntityTaskModal()">Add Task</button>
                </div>
                @forelse($activityEntity->tasks as $task)
                    <div class="timeline-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $task->subject }}</h6>
                                <div class="text-muted small mb-2">
                                    Assigned to {{ optional($task->assignedTo)->username ?? optional($task->assignedTo)->name ?? '-' }}
                                </div>
                                <p class="mb-1">{{ $task->description ?: 'No task description' }}</p>
                                <small class="text-muted">Due {{ optional($task->due_date)->format('d M Y, h:i A') ?: '-' }}</small>
                            </div>
                            <div class="text-right">
                                @php
                                    $taskBadgeClass = $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'warning');
                                @endphp
                                <span class="badge badge-{{ $taskBadgeClass }} d-block mb-2">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-light" type="button" onclick="editEntityTask({{ $task->id }}, @js($task->subject), @js($task->description), '{{ $task->assigned_to }}', '{{ optional($task->due_date)->format('Y-m-d\\TH:i') }}', '{{ $task->status }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($task->status !== 'completed')
                                        <button class="btn btn-light text-success" type="button" onclick="markEntityTaskCompleted({{ $task->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-light text-danger" type="button" onclick="deleteEntityTask({{ $task->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No tasks available.</div>
                @endforelse
            </div>

            <div class="tab-pane fade" id="{{ $activityEntityType }}-calls-pane">
                <div class="section-toolbar mb-3">
                    <h5 class="section-title mb-0">Calls</h5>
                    <button class="btn btn-sm btn-primary" type="button" onclick="openEntityCallModal()">Log / Schedule Call</button>
                </div>
                @forelse($activityEntity->calls as $call)
                    <div class="timeline-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ ucfirst($call->call_type) }} Call</h6>
                                <div class="text-muted small mb-2">
                                    Owner {{ optional($call->callOwner)->username ?? optional($call->callOwner)->name ?? '-' }}
                                </div>
                                <p class="mb-1">{{ $call->call_purpose ?: 'No purpose added' }}</p>
                                <small class="text-muted">{{ optional($call->call_date)->format('d M Y, h:i A') ?: '-' }}</small>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-{{ $call->status === 'completed' ? 'success' : ($call->status === 'scheduled' ? 'primary' : 'secondary') }} d-block mb-2">{{ ucfirst(str_replace('_', ' ', $call->status)) }}</span>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-light" type="button" onclick="editEntityCall({{ $call->id }}, '{{ $call->call_type }}', @js($call->call_purpose), @js($call->notes), @js($call->duration), '{{ $call->status }}', '{{ optional($call->call_date)->format('Y-m-d\\TH:i') }}', '{{ $call->call_owner }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-light text-danger" type="button" onclick="deleteEntityCall({{ $call->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No calls available.</div>
                @endforelse
            </div>

            <div class="tab-pane fade" id="{{ $activityEntityType }}-meetings-pane">
                <div class="section-toolbar mb-3">
                    <h5 class="section-title mb-0">Meetings</h5>
                    <button class="btn btn-sm btn-primary" type="button" onclick="openEntityMeetingModal()">Schedule Meeting</button>
                </div>
                @forelse($activityEntity->meetings as $meeting)
                    <div class="timeline-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $meeting->title }}</h6>
                                <div class="text-muted small mb-2">
                                    Assigned to {{ optional($meeting->assignedTo)->username ?? optional($meeting->assignedTo)->name ?? '-' }}
                                </div>
                                <p class="mb-1">{{ $meeting->description ?: 'No meeting description' }}</p>
                                <small class="text-muted">{{ optional($meeting->meeting_date)->format('d M Y, h:i A') ?: '-' }}</small>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-{{ $meeting->status === 'completed' ? 'success' : ($meeting->status === 'scheduled' ? 'primary' : 'secondary') }} d-block mb-2">{{ ucfirst($meeting->status) }}</span>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-light" type="button" onclick="editEntityMeeting({{ $meeting->id }}, @js($meeting->title), @js($meeting->description), @js($meeting->meeting_type), @js($meeting->location), @js($meeting->meeting_link), '{{ optional($meeting->meeting_date)->format('Y-m-d\\TH:i') }}', @js($meeting->duration), '{{ $meeting->assigned_to }}', '{{ $meeting->status }}', @js($meeting->notes))">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($meeting->status !== 'completed')
                                        <button class="btn btn-light text-success" type="button" onclick="openEntityCompleteMeetingModal({{ $meeting->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-light text-danger" type="button" onclick="deleteEntityMeeting({{ $meeting->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No meetings available.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="entityNoteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="entityNoteForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="entityNoteModalTitle">Add Note</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="entity_note_id">
                  
                    <div class="form-group mb-0">
                        <label>Note</label>
                        <textarea class="form-control" id="entity_note_text" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="entityTaskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="entityTaskForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="entityTaskModalTitle">Add Task</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="entity_task_id">
                    <div class="form-group"><label>Subject</label><input type="text" class="form-control" id="entity_task_subject" required></div>
                    <div class="form-group"><label>Description</label><textarea class="form-control" id="entity_task_description" rows="3"></textarea></div>
                    <div class="form-group"><label>Assign To</label>
                        <select class="form-control" id="entity_task_assigned_to" required>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username ?? $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Due Date</label><input type="datetime-local" class="form-control" id="entity_task_due_date"></div>
                    <div class="form-group mb-0"><label>Status</label>
                        <select class="form-control" id="entity_task_status">
                            <option value="deferred">Deferred</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending (Legacy)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="entityCallModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="entityCallForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="entityCallModalTitle">Log / Schedule Call</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="entity_call_id">
                    <div class="form-group"><label>Call Type</label>
                        <select class="form-control" id="entity_call_type" required>
                            <option value="outbound">Outbound</option>
                            <option value="inbound">Inbound</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Owner</label>
                        <select class="form-control" id="entity_call_owner" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username ?? $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Purpose</label><input type="text" class="form-control" id="entity_call_purpose"></div>
                    <div class="form-group"><label>Status</label>
                        <select class="form-control" id="entity_call_status" required>
                            <option value="completed">Completed</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="missed">Missed</option>
                            <option value="voicemail">Voicemail</option>
                            <option value="no_answer">No Answer</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Call Date</label><input type="datetime-local" class="form-control" id="entity_call_date" required></div>
                    <div class="form-group"><label>Duration</label><input type="text" class="form-control" id="entity_call_duration" placeholder="e.g. 15 min"></div>
                    <div class="form-group mb-0"><label>Notes</label><textarea class="form-control" id="entity_call_notes" rows="4"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Call</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="entityMeetingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="entityMeetingForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="entityMeetingModalTitle">Schedule Meeting</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="entity_meeting_id">
                    <div class="form-group"><label>Title</label><input type="text" class="form-control" id="entity_meeting_title" required></div>
                    <div class="form-group"><label>Description</label><textarea class="form-control" id="entity_meeting_description" rows="3"></textarea></div>
                    <div class="form-group"><label>Meeting Type</label>
                        <select class="form-control" id="entity_meeting_type">
                            <option value="virtual">Virtual</option>
                            <option value="in-person">In Person</option>
                            <option value="phone">Phone</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Location</label><input type="text" class="form-control" id="entity_meeting_location"></div>
                    <div class="form-group"><label>Meeting Link</label><input type="url" class="form-control" id="entity_meeting_link"></div>
                    <div class="form-group"><label>Meeting Date</label><input type="datetime-local" class="form-control" id="entity_meeting_date" required></div>
                    <div class="form-group"><label>Duration</label><input type="text" class="form-control" id="entity_meeting_duration"></div>
                    <div class="form-group"><label>Assign To</label>
                        <select class="form-control" id="entity_meeting_assigned_to" required>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username ?? $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Status</label>
                        <select class="form-control" id="entity_meeting_status">
                            <option value="scheduled">Scheduled</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="rescheduled">Rescheduled</option>
                        </select>
                    </div>
                    <div class="form-group mb-0"><label>Notes</label><textarea class="form-control" id="entity_meeting_notes" rows="3"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Meeting</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="entityCompleteMeetingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="entityCompleteMeetingForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Complete Meeting</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="entity_complete_meeting_id">
                    <div class="form-group mb-0">
                        <label>Completion Notes</label>
                        <textarea class="form-control" id="entity_complete_meeting_notes" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Complete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const entityActivityType = @js($activityEntityType);
    const entityActivityId = {{ $activityEntity->id }};
    const entityActivityBaseUrl = '{{ url("admin/crm") }}';

    function entityActivityUrl(section) {
        return entityActivityBaseUrl + '/' + entityActivityType + '/' + entityActivityId + '/' + section;
    }

    function reloadEntityActivityPage() {
        window.location.reload();
    }

    function formatEntityLocalDate(date) {
        if (!date) return '';
        const d = new Date(date);
        const offset = d.getTimezoneOffset();
        return new Date(d.getTime() - offset * 60000).toISOString().slice(0, 16);
    }

    function handleEntityActivityResponse(response) {
        if (response.success) {
            reloadEntityActivityPage();
        } else {
            alert(response.message || 'Something went wrong.');
        }
    }

    function confirmEntityDelete(message, url) {
        if (!confirm(message)) return;

        $.post(url, {
            _token: '{{ csrf_token() }}',
            _method: 'DELETE'
        }, handleEntityActivityResponse);
    }

    function openEntityNoteModal() {
        $('#entityNoteModalTitle').text('Add Note');
        $('#entity_note_id').val('');
        $('#entity_note_text').val('');
        $('#entityNoteModal').modal('show');
    }

    function editEntityNote(id, note, type) {
        $('#entityNoteModalTitle').text('Edit Note');
        $('#entity_note_id').val(id);
        $('#entity_note_text').val(note || '');
        $('#entityNoteModal').modal('show');
    }

    function openEntityTaskModal() {
        $('#entityTaskModalTitle').text('Add Task');
        $('#entity_task_id').val('');
        $('#entity_task_subject').val('');
        $('#entity_task_description').val('');
        $('#entity_task_assigned_to').val('');
        $('#entity_task_due_date').val('');
        $('#entity_task_status').val('deferred');
        $('#entityTaskModal').modal('show');
    }

    function editEntityTask(id, subject, description, assignedTo, dueDate, status) {
        $('#entityTaskModalTitle').text('Edit Task');
        $('#entity_task_id').val(id);
        $('#entity_task_subject').val(subject || '');
        $('#entity_task_description').val(description || '');
        $('#entity_task_assigned_to').val(assignedTo || '');
        $('#entity_task_due_date').val(dueDate || '');
        $('#entity_task_status').val(status || 'deferred');
        $('#entityTaskModal').modal('show');
    }

    function openEntityCallModal() {
        $('#entityCallModalTitle').text('Log / Schedule Call');
        $('#entity_call_id').val('');
        $('#entity_call_type').val('outbound');
        $('#entity_call_owner').val('{{ auth()->id() }}');
        $('#entity_call_purpose').val('');
        $('#entity_call_status').val('completed');
        $('#entity_call_date').val(formatEntityLocalDate(new Date()));
        $('#entity_call_duration').val('');
        $('#entity_call_notes').val('');
        $('#entityCallModal').modal('show');
    }

    function editEntityCall(id, callType, purpose, notes, duration, status, callDate, callOwner) {
        $('#entityCallModalTitle').text('Edit Call');
        $('#entity_call_id').val(id);
        $('#entity_call_type').val(callType || 'outbound');
        $('#entity_call_owner').val(callOwner || '{{ auth()->id() }}');
        $('#entity_call_purpose').val(purpose || '');
        $('#entity_call_status').val(status || 'completed');
        $('#entity_call_date').val(callDate || formatEntityLocalDate(new Date()));
        $('#entity_call_duration').val(duration || '');
        $('#entity_call_notes').val(notes || '');
        $('#entityCallModal').modal('show');
    }

    function openEntityMeetingModal() {
        $('#entityMeetingModalTitle').text('Schedule Meeting');
        $('#entity_meeting_id').val('');
        $('#entity_meeting_title').val('');
        $('#entity_meeting_description').val('');
        $('#entity_meeting_type').val('virtual');
        $('#entity_meeting_location').val('');
        $('#entity_meeting_link').val('');
        $('#entity_meeting_date').val(formatEntityLocalDate(new Date()));
        $('#entity_meeting_duration').val('');
        $('#entity_meeting_assigned_to').val('');
        $('#entity_meeting_status').val('scheduled');
        $('#entity_meeting_notes').val('');
        $('#entityMeetingModal').modal('show');
    }

    function editEntityMeeting(id, title, description, meetingType, location, meetingLink, meetingDate, duration, assignedTo, status, notes) {
        $('#entityMeetingModalTitle').text('Edit Meeting');
        $('#entity_meeting_id').val(id);
        $('#entity_meeting_title').val(title || '');
        $('#entity_meeting_description').val(description || '');
        $('#entity_meeting_type').val(meetingType || 'virtual');
        $('#entity_meeting_location').val(location || '');
        $('#entity_meeting_link').val(meetingLink || '');
        $('#entity_meeting_date').val(meetingDate || formatEntityLocalDate(new Date()));
        $('#entity_meeting_duration').val(duration || '');
        $('#entity_meeting_assigned_to').val(assignedTo || '');
        $('#entity_meeting_status').val(status || 'scheduled');
        $('#entity_meeting_notes').val(notes || '');
        $('#entityMeetingModal').modal('show');
    }

    function openEntityCompleteMeetingModal(id) {
        $('#entity_complete_meeting_id').val(id);
        $('#entity_complete_meeting_notes').val('');
        $('#entityCompleteMeetingModal').modal('show');
    }

    function deleteEntityNote(id) {
        confirmEntityDelete('Delete this note?', entityActivityBaseUrl + '/notes/' + id);
    }

    function deleteEntityTask(id) {
        confirmEntityDelete('Delete this task?', entityActivityBaseUrl + '/tasks/' + id);
    }

    function deleteEntityCall(id) {
        confirmEntityDelete('Delete this call?', entityActivityBaseUrl + '/calls/' + id);
    }

    function deleteEntityMeeting(id) {
        confirmEntityDelete('Delete this meeting?', entityActivityBaseUrl + '/meetings/' + id);
    }

    function markEntityTaskCompleted(id) {
        $.post(entityActivityBaseUrl + '/tasks/' + id + '/status', {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            status: 'completed'
        }, handleEntityActivityResponse);
    }

    $(function () {
        $('#entityNoteForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#entity_note_id').val();
            const payload = {
                _token: '{{ csrf_token() }}',
                note: $('#entity_note_text').val(),
                type: $('#entity_note_type').val()
            };

            if (id) {
                payload._method = 'PUT';
                $.post(entityActivityBaseUrl + '/notes/' + id, payload, handleEntityActivityResponse);
            } else {
                $.post(entityActivityUrl('notes'), payload, handleEntityActivityResponse);
            }
        });

        $('#entityTaskForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#entity_task_id').val();
            const payload = {
                _token: '{{ csrf_token() }}',
                subject: $('#entity_task_subject').val(),
                description: $('#entity_task_description').val(),
                assigned_to: $('#entity_task_assigned_to').val(),
                due_date: $('#entity_task_due_date').val(),
                status: $('#entity_task_status').val()
            };

            if (id) {
                payload._method = 'PUT';
                $.post(entityActivityBaseUrl + '/tasks/' + id, payload, handleEntityActivityResponse);
            } else {
                delete payload.status;
                $.post(entityActivityUrl('tasks'), payload, handleEntityActivityResponse);
            }
        });

        $('#entityCallForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#entity_call_id').val();
            const payload = {
                _token: '{{ csrf_token() }}',
                call_type: $('#entity_call_type').val(),
                call_owner: $('#entity_call_owner').val(),
                call_purpose: $('#entity_call_purpose').val(),
                status: $('#entity_call_status').val(),
                call_date: $('#entity_call_date').val(),
                duration: $('#entity_call_duration').val(),
                notes: $('#entity_call_notes').val()
            };

            if (id) {
                payload._method = 'PUT';
                $.post(entityActivityBaseUrl + '/calls/' + id, payload, handleEntityActivityResponse);
            } else {
                $.post(entityActivityUrl('calls'), payload, handleEntityActivityResponse);
            }
        });

        $('#entityMeetingForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#entity_meeting_id').val();
            const payload = {
                _token: '{{ csrf_token() }}',
                title: $('#entity_meeting_title').val(),
                description: $('#entity_meeting_description').val(),
                meeting_type: $('#entity_meeting_type').val(),
                location: $('#entity_meeting_location').val(),
                meeting_link: $('#entity_meeting_link').val(),
                meeting_date: $('#entity_meeting_date').val(),
                duration: $('#entity_meeting_duration').val(),
                assigned_to: $('#entity_meeting_assigned_to').val(),
                status: $('#entity_meeting_status').val(),
                notes: $('#entity_meeting_notes').val()
            };

            if (id) {
                payload._method = 'PUT';
                $.post(entityActivityBaseUrl + '/meetings/' + id, payload, handleEntityActivityResponse);
            } else {
                delete payload.status;
                delete payload.notes;
                $.post(entityActivityUrl('meetings'), payload, handleEntityActivityResponse);
            }
        });

        $('#entityCompleteMeetingForm').on('submit', function (e) {
            e.preventDefault();
            $.post(entityActivityBaseUrl + '/meetings/' + $('#entity_complete_meeting_id').val() + '/complete', {
                _token: '{{ csrf_token() }}',
                notes: $('#entity_complete_meeting_notes').val()
            }, handleEntityActivityResponse);
        });
    });
</script>

<style>
    .entity-activity-card .lead-detail-tabs .nav-link {
        border-radius: 999px;
        color: #475569;
        font-weight: 500;
        margin-right: .5rem;
        padding: .55rem 1rem;
        background: #f8fafc;
    }

    .entity-activity-card .lead-detail-tabs .nav-link.active {
        background: #35394F;
        color: #fff;
    }

    .entity-activity-card .section-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .entity-activity-card .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
    }

    .entity-activity-card .timeline-card {
        background: #fff;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: .9rem;
        transition: all .2s ease;
    }

    .entity-activity-card .timeline-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
    }

    .entity-activity-card .empty-state {
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 2rem 1rem;
        text-align: center;
        color: #64748b;
        background: #f8fafc;
    }
</style>
