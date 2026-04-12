@extends('layout.admin')

@section('content')
    @php
        $canViewNotes = auth()->user()->can('leads_notes') || auth()->user()->can('leads_addnote') || auth()->user()->can('leads_addnotes');
        $canManageNotes = auth()->user()->can('leads_addnote') || auth()->user()->can('leads_addnotes');
        $canViewActivities = auth()->user()->can('leads_details');
        $canViewTasks = auth()->user()->can('leads_task') || auth()->user()->can('leads_addtasks');
        $canManageTasks = auth()->user()->can('leads_addtasks');
        $canViewCalls = auth()->user()->can('leads_call') || auth()->user()->can('leads_viewlogcall') || auth()->user()->can('leads_schedulecalls') || auth()->user()->can('leads_createcall') || auth()->user()->can('leads_logcall') || auth()->user()->can('leads_addlogcall');
        $canCreateScheduledCalls = auth()->user()->can('leads_schedulecalls') || auth()->user()->can('leads_createcall');
        $canLogCalls = auth()->user()->can('leads_logcall') || auth()->user()->can('leads_addlogcall');
        $canEditCalls = auth()->user()->can('leads_editscheduledcalls') || $canLogCalls || $canCreateScheduledCalls;
        $canCompleteCalls = auth()->user()->can('leads_markcompleteupcommingcall');
        $canDeleteCalls = auth()->user()->can('leads_deletecall');
        $canViewMeetings = auth()->user()->can('leads_viewschedulemeeting') || auth()->user()->can('leads_viewupcomingmeeting') || auth()->user()->can('leads_viewpastmeeting') || auth()->user()->can('leads_schedulemeeting');
        $canScheduleMeetings = auth()->user()->can('leads_schedulemeeting');
        $canEditMeetings = auth()->user()->can('leads_schedulemeeting');
        $canCompleteMeetings = auth()->user()->can('leads_markcompleteupcommingmeeting');
        $canDeleteUpcomingMeetings = auth()->user()->can('leads_deleteupcomingmeeting');
        $canDeletePastMeetings = auth()->user()->can('leads_deletepastmeeting');
        $canViewEmails = auth()->user()->can('leads_details') || auth()->user()->can('leads_sendemail');
        $canSendEmails = auth()->user()->can('leads_sendemail');
        $defaultTab = $canViewNotes ? 'notes-pane' : ($canViewActivities ? 'activities-pane' : ($canViewTasks ? 'tasks-pane' : ($canViewCalls ? 'calls-pane' : ($canViewMeetings ? 'meetings-pane' : 'emails-pane'))));
    @endphp
    <div class="content lead-show-page">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="page-title text-dark mb-1"><i
                            class="fas fa-user-tie text-primary mr-2"></i>{{ $lead->full_name ?: 'Lead Profile' }}</h3>
                    <p class="text-muted mb-0">Manage this lead in one place. Double-click any note, task, call, or meeting
                        card to edit it fast.</p>
                </div>
                <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-primary mr-2"><i
                            class="fas fa-arrow-left mr-1"></i>Back</a>
                    @can('leads_email')
                        <button type="button" class="btn btn-primary" onclick="openComposeEmailModal()"><i
                                class="fas fa-paper-plane mr-1"></i>Compose Email</button>
                    @endcan
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="card lead-profile-card h-100">
                    <div class="card-body text-center">
                        @if($lead->lead_image)
                            <img src="{{ asset($lead->lead_image) }}" class="rounded-circle shadow-sm mb-3" width="96"
                                height="96" style="object-fit: cover;">
                        @else
                            <div class="lead-avatar mx-auto mb-3">
                                {{ strtoupper(substr($lead->first_name ?? 'L', 0, 1)) }}{{ strtoupper(substr($lead->last_name ?? 'D', 0, 1)) }}
                            </div>
                        @endif
                        <h4 class="mb-1">{{ $lead->full_name ?: 'Unnamed Lead' }}</h4>
                        <p class="text-muted mb-2">{{ $lead->title ?: 'No title added' }}</p>
                        <span
                            class="badge badge-pill badge-primary px-3 py-2">{{ $lead->lead_status ?: 'No Status' }}</span>
                        <hr>
                        <div class="text-left lead-meta">
                            <div class="lead-meta-item"><span
                                    class="text-muted">Company</span><strong>{{ $lead->company ?: '-' }}</strong></div>
                            <div class="lead-meta-item"><span
                                    class="text-muted">Owner</span><strong>{{ $lead->owner?->full_name ?? $lead->owner?->username ?? 'Not Assigned' }}</strong>
                            </div>
                            <div class="lead-meta-item"><span
                                    class="text-muted">Source</span><strong>{{ $lead->lead_source ?: '-' }}</strong></div>
                            <div class="lead-meta-item"><span class="text-muted">Created
                                    By</span><strong>{{ $lead->user?->full_name ?? $lead->user?->username ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row mb-3">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-label">Notes</div>
                                <div class="stat-value">{{ $lead->notes->count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-label">Tasks</div>
                                <div class="stat-value">{{ $lead->tasks->count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-label">Calls</div>
                                <div class="stat-value">{{ $lead->calls->count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-label">Meetings</div>
                                <div class="stat-value">{{ $lead->meetings->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card info-card">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="mb-0">Contact Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="info-item"><span>Email</span>
                                    <div>{{ $lead->email ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-item"><span>Phone</span>
                                    <div>{{ $lead->phone ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-item"><span>Mobile</span>
                                    <div>{{ $lead->mobile ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-item"><span>Website</span>
                                    <div>{{ $lead->website ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="info-item"><span>Address</span>
                                    <div>{{ $lead->street ?: '-' }}
                                        @if($lead->city || $lead->state || $lead->country || $lead->zip_code)<br>{{ collect([$lead->city, $lead->state, $lead->country, $lead->zip_code])->filter()->implode(', ') }}@endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item"><span>Description</span>
                                    <div>{{ $lead->description ?: 'No description added.' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card detail-tabs-card">
            <div class="card-body">
                <ul class="nav nav-pills lead-detail-tabs mb-4">
                    @can('leads_notes')
                        <li class="nav-item"><a class="nav-link {{ $defaultTab === 'notes-pane' ? 'active' : '' }}"
                                data-toggle="pill" href="#notes-pane">Notes</a></li>
                    @endcan
                    @can('leads_activities')
                        <li class="nav-item"><a class="nav-link {{ $defaultTab === 'activities-pane' ? 'active' : '' }}"
                                data-toggle="pill" href="#activities-pane">Activities</a></li>
                    @endcan
                    @can('leads_task')
                        <li class="nav-item"><a class="nav-link {{ $defaultTab === 'tasks-pane' ? 'active' : '' }}"
                                data-toggle="pill" href="#tasks-pane">Tasks</a></li>
                    @endcan


                    @can('leads_call')
                        <li class="nav-item"><a class="nav-link {{ $defaultTab === 'calls-pane' ? 'active' : '' }}"
                                data-toggle="pill" href="#calls-pane">Calls</a></li>
                    @endcan
                    @can('leads_meeting')
                        <li class="nav-item"><a class="nav-link {{ $defaultTab === 'meetings-pane' ? 'active' : '' }}"
                                data-toggle="pill" href="#meetings-pane">Meetings</a></li>
                    @endcan
                    @can('leads_email')
                        <li class="nav-item"><a class="nav-link {{ $defaultTab === 'emails-pane' ? 'active' : '' }}"
                                data-toggle="pill" href="#emails-pane">Emails</a></li>
                    @endcan
                </ul>
                <div class="tab-content">
                    @can('leads_notes')
                        <div class="tab-pane fade {{ $defaultTab === 'notes-pane' ? 'show active' : '' }}" id="notes-pane">
                            <div class="section-toolbar mb-3">
                                <h5 class="section-title mb-0">Notes</h5>
                                @can('leads_addnotes')
                                    <button class="btn btn-sm btn-primary" type="button" onclick="openNoteModal()">Add Note</button>
                                @endcan
                            </div>
                            @forelse($lead->notes as $note)
                                <div class="timeline-card editable-record" data-kind="note" data-id="{{ $note->id }}"
                                    data-note="{{ e($note->note) }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                {{ optional($note->createdBy)->username ?? optional($note->createdBy)->name ?? 'User' }}
                                            </h6>
                                            <p class="mb-1">{{ $note->note }}</p>
                                        </div>
                                        <div class="text-right">
                                            <small
                                                class="text-muted d-block">{{ optional($note->created_at)->format('d M Y, h:i A') }}</small>
                                            @canany(['leads_editnotes', 'leads_deletenotes'])
                                                <div class="mt-2 btn-group btn-group-sm">
                                                    @can('leads_editnotes')
                                                        <button class="btn btn-light" type="button"
                                                            onclick="event.stopPropagation(); editNote({{ $note->id }}, @js($note->note))">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endcan
                                                    @can('leads_deletenotes')
                                                        <button class="btn btn-light text-danger" type="button"
                                                            onclick="event.stopPropagation(); deleteNote({{ $note->id }})">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            @endcanany
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">No notes available.</div>
                            @endforelse
                        </div>
                    @endcan

                    @can('leads_activities')
                        <div class="tab-pane fade {{ $defaultTab === 'activities-pane' ? 'show active' : '' }}"
                            id="activities-pane">
                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <h5 class="section-title">Open Activities</h5>@forelse($pendingActivities as $activity)<div
                                        class="timeline-card">
                                        <h6 class="mb-1">{{ $activity->subject }}</h6>
                                        <div class="text-muted small mb-2">{{ ucfirst($activity->type) }}</div>
                                        <p class="mb-1">{{ $activity->description ?: 'No description' }}</p><small
                                            class="text-muted">Due
                                            {{ optional($activity->due_date)->format('d M Y, h:i A') ?: '-' }}</small>
                                    </div>@empty<div class="empty-state">No open activities.</div>@endforelse
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <h5 class="section-title">Completed Activities</h5>
                                    @forelse($completedActivities as $activity)<div class="timeline-card">
                                        <h6 class="mb-1">{{ $activity->subject }}</h6>
                                        <div class="text-muted small mb-2">{{ ucfirst($activity->type) }}</div>
                                        <p class="mb-1">{{ $activity->description ?: 'No description' }}</p><small
                                            class="text-muted">Completed
                                            {{ optional($activity->completed_at)->format('d M Y, h:i A') ?: optional($activity->created_at)->format('d M Y, h:i A') }}</small>
                                    </div>@empty<div class="empty-state">No completed activities.</div>@endforelse
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('leads_task')
                        <div class="tab-pane fade {{ $defaultTab === 'tasks-pane' ? 'show active' : '' }}" id="tasks-pane">
                            <div class="section-toolbar mb-3">
                                <h5 class="section-title mb-0">Tasks</h5>
                                @can('leads_addtasks')
                                    <button class="btn btn-sm btn-primary" type="button" onclick="openTaskModal()">Add Task</button>
                                @endcan
                            </div>
                            @forelse($lead->tasks as $task)
                                <div class="timeline-card editable-record" data-kind="task" data-id="{{ $task->id }}"
                                    data-subject="{{ e($task->subject) }}" data-description="{{ e($task->description) }}"
                                    data-assigned="{{ $task->assigned_to }}"
                                    data-due="{{ optional($task->due_date)->format('Y-m-d\TH:i') }}"
                                    data-status="{{ $task->status }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $task->subject }}</h6>
                                            <div class="text-muted small mb-2">
                                                Assigned to
                                                {{ optional($task->assignedTo)->username ?? optional($task->assignedTo)->name ?? '-' }}
                                            </div>
                                            <p class="mb-1">{{ $task->description ?: 'No task description' }}</p>
                                            <small class="text-muted">Due
                                                {{ optional($task->due_date)->format('d M Y, h:i A') ?: '-' }}</small>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="badge badge-{{ $task->status === 'completed' ? 'success' : 'warning' }} d-block mb-2">{{ ucfirst($task->status) }}</span>
                                            @canany(['leads_edittasks', 'leads_markcompleteupcommingtask', 'leads_deletetasks'])
                                                <div class="btn-group btn-group-sm">
                                                    @can('leads_edittasks')
                                                        <button class="btn btn-light" type="button"
                                                            onclick="event.stopPropagation(); editTaskFromCard(this)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endcan
                                                    @if($task->status !== 'completed')
                                                        @can('leads_markcompleteupcommingtask')
                                                            <button class="btn btn-light text-success" type="button"
                                                                onclick="event.stopPropagation(); markTaskCompleted({{ $task->id }})">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @endcan
                                                    @endif
                                                    @can('leads_deletetasks')
                                                        <button class="btn btn-light text-danger" type="button"
                                                            onclick="event.stopPropagation(); deleteTask({{ $task->id }})">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            @endcanany
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">No tasks created for this lead.</div>
                            @endforelse
                        </div>
                    @endcan

                    @can('leads_call')
                        <div class="tab-pane fade {{ $defaultTab === 'calls-pane' ? 'show active' : '' }}" id="calls-pane">
                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <div class="section-toolbar mb-3">
                                        <h5 class="section-title mb-0">Scheduled Calls</h5>
                                        @can('leads_createcall')
                                            <button class="btn btn-sm btn-primary" type="button"
                                                onclick="openCallModal('scheduled')">Create Call</button>
                                        @endcan
                                    </div>
                                    @forelse($scheduledCalls as $call)
                                        <div class="timeline-card editable-record" data-kind="call" data-mode="scheduled"
                                            data-id="{{ $call->id }}" data-call-type="{{ $call->call_type }}"
                                            data-purpose="{{ e($call->call_purpose) }}" data-notes="{{ e($call->notes) }}"
                                            data-duration="{{ e($call->duration) }}" data-status="{{ $call->status }}"
                                            data-call-owner="{{ $call->call_owner ?: $call->called_by }}"
                                            data-date="{{ optional($call->call_date)->format('Y-m-d\TH:i') }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $call->call_purpose ?: 'Scheduled Call' }}</h6>
                                                    <div class="text-muted small mb-2">{{ ucfirst($call->call_type) }} call | Owner
                                                        {{ optional($call->callOwner)->username ?? optional($call->callOwner)->name ?? optional($call->calledBy)->username ?? optional($call->calledBy)->name ?? '-' }}
                                                    </div>
                                                    <p class="mb-1">{{ $call->notes ?: 'No notes' }}</p><small
                                                        class="text-muted">{{ optional($call->call_date)->format('d M Y, h:i A') ?: '-' }}</small>
                                                </div>
                                                <div class="text-right"><span
                                                        class="badge badge-info d-block mb-2">{{ ucfirst($call->status) }}</span>
                                                    <div class="btn-group btn-group-sm">
                                                        @can('leads_editscheduledcalls')
                                                            <button class="btn btn-light" type="button"
                                                                onclick="event.stopPropagation(); editCallFromCard(this)"><i
                                                                    class="fas fa-edit"></i></button>
                                                        @endcan
                                                        @can('leads_markcompletescheduledcall')
                                                            <button class="btn btn-light text-success" type="button"
                                                                onclick="event.stopPropagation(); openCompleteCallModal(this)"><i
                                                                    class="fas fa-check-circle"></i></button>
                                                        @endcan
                                                        @can('leads_deletescheduledcalls')
                                                            <button class="btn btn-light text-danger" type="button"
                                                                onclick="event.stopPropagation(); deleteCall({{ $call->id }})"><i
                                                                    class="fas fa-trash-alt"></i></button>
                                                        @endcan
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">No scheduled calls.</div>
                                    @endforelse
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="section-toolbar mb-3">
                                        <h5 class="section-title mb-0">Logged Calls</h5>
                                        @can('leads_logcall')
                                            <button class="btn btn-sm btn-primary" type="button"
                                                onclick="openCallModal('logged')">Log Call</button>
                                        @endcan
                                    </div>
                                    @forelse($loggedCalls as $call)
                                        <div class="timeline-card editable-record" data-kind="call" data-mode="logged"
                                            data-id="{{ $call->id }}" data-call-type="{{ $call->call_type }}"
                                            data-purpose="{{ e($call->call_purpose) }}" data-notes="{{ e($call->notes) }}"
                                            data-duration="{{ e($call->duration) }}" data-status="{{ $call->status }}"
                                            data-call-owner="{{ $call->call_owner ?: $call->called_by }}"
                                            data-date="{{ optional($call->call_date)->format('Y-m-d\TH:i') }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $call->call_purpose ?: 'Logged Call' }}</h6>
                                                    <div class="text-muted small mb-2">{{ ucfirst($call->call_type) }} call | Owner
                                                        {{ optional($call->callOwner)->username ?? optional($call->callOwner)->name ?? optional($call->calledBy)->username ?? optional($call->calledBy)->name ?? '-' }}
                                                    </div>
                                                    <p class="mb-1">{{ $call->notes ?: 'No notes' }}</p><small
                                                        class="text-muted">{{ optional($call->call_date)->format('d M Y, h:i A') ?: '-' }}
                                                        | Duration {{ $call->duration ?: '-' }}</small>
                                                </div>
                                                <div class="text-right"><span
                                                        class="badge badge-secondary d-block mb-2">{{ ucfirst($call->status) }}</span>

                                                    <div class="btn-group btn-group-sm">
                                                        @can('leads_editlogcall')
                                                            <button class="btn btn-light" type="button"
                                                                onclick="event.stopPropagation(); editCallFromCard(this)"><i
                                                                    class="fas fa-edit"></i></button>
                                                        @endcan
                                                        @can('leads_deletelogcall')
                                                            <button class="btn btn-light text-danger" type="button"
                                                                onclick="event.stopPropagation(); deleteCall({{ $call->id }})"><i
                                                                    class="fas fa-trash-alt"></i></button>
                                                        @endcan
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">No logged calls.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endcan


                    <div class="tab-pane fade {{ $defaultTab === 'meetings-pane' ? 'show active' : '' }}"
                        id="meetings-pane">
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <div class="section-toolbar mb-3">
                                    <h5 class="section-title mb-0">Scheduled Meetings</h5>
                                    @can('leads_schedulemeeting')
                                        <button class="btn btn-sm btn-primary" type="button"
                                            onclick="openMeetingModal()">Schedule
                                            Meeting</button>
                                    @endcan
                                </div>
                                @if(auth()->user()->can('leads_viewupcomingmeeting') || $canScheduleMeetings)
                                    @forelse($upcomingMeetings as $meeting)
                                        <div class="timeline-card editable-record" data-kind="meeting" data-id="{{ $meeting->id }}"
                                            data-title="{{ e($meeting->title) }}" data-description="{{ e($meeting->description) }}"
                                            data-type="{{ $meeting->meeting_type }}" data-location="{{ e($meeting->location) }}"
                                            data-link="{{ e($meeting->meeting_link) }}"
                                            data-date="{{ optional($meeting->meeting_date)->format('Y-m-d\TH:i') }}"
                                            data-duration="{{ e($meeting->duration) }}" data-assigned="{{ $meeting->assigned_to }}"
                                            data-status="{{ $meeting->status }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $meeting->title }}</h6>
                                                    <div class="text-muted small mb-2">{{ ucfirst($meeting->meeting_type) }} meeting
                                                    </div>
                                                    <p class="mb-1">{{ $meeting->description ?: 'No agenda added' }}</p><small
                                                        class="text-muted">{{ optional($meeting->meeting_date)->format('d M Y, h:i A') ?: '-' }}
                                                        | Duration {{ $meeting->duration ?: '-' }}</small>
                                                </div>
                                                <div class="text-right"><span
                                                        class="badge badge-info d-block mb-2">{{ ucfirst($meeting->status) }}</span>

                                                    <div class="btn-group btn-group-sm">
                                                        @can('leads_editupcomingmeeting')
                                                            <button class="btn btn-light" type="button"
                                                                onclick="event.stopPropagation(); editMeetingFromCard(this)"><i
                                                                    class="fas fa-edit"></i></button>
                                                        @endcan
                                                        @can('leads_markcompleteupcomingmeeting')
                                                            <button class="btn btn-light text-success" type="button"
                                                                onclick="event.stopPropagation(); openCompleteMeetingModal({{ $meeting->id }})"><i
                                                                    class="fas fa-check-circle"></i></button>
                                                        @endcan
                                                        @can('leads_deleteupcomingmeeting')
                                                            <button class="btn btn-light text-danger" type="button"
                                                                onclick="event.stopPropagation(); deleteMeeting({{ $meeting->id }})"><i
                                                                    class="fas fa-trash-alt"></i></button>
                                                        @endcan
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">No scheduled meetings.</div>
                                    @endforelse
                                @endif
                            </div>
                            <div class="col-lg-6 mb-4">
                                <h5 class="section-title">Closed Meetings</h5>
                                @if(auth()->user()->can('leads_viewpastmeeting'))
                                    @forelse($completedMeetings as $meeting)
                                        <div class="timeline-card editable-record" data-kind="meeting" data-id="{{ $meeting->id }}"
                                            data-title="{{ e($meeting->title) }}" data-description="{{ e($meeting->description) }}"
                                            data-type="{{ $meeting->meeting_type }}" data-location="{{ e($meeting->location) }}"
                                            data-link="{{ e($meeting->meeting_link) }}"
                                            data-date="{{ optional($meeting->meeting_date)->format('Y-m-d\TH:i') }}"
                                            data-duration="{{ e($meeting->duration) }}" data-assigned="{{ $meeting->assigned_to }}"
                                            data-status="{{ $meeting->status }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $meeting->title }}</h6>
                                                    <div class="text-muted small mb-2">{{ ucfirst($meeting->meeting_type) }} meeting
                                                    </div>
                                                    <p class="mb-1">
                                                        {{ $meeting->notes ?: ($meeting->description ?: 'No meeting notes') }}
                                                    </p>
                                                    <small
                                                        class="text-muted">{{ optional($meeting->meeting_date)->format('d M Y, h:i A') ?: '-' }}</small>
                                                </div>
                                                <div class="text-right"><span
                                                        class="badge badge-success d-block mb-2">{{ ucfirst($meeting->status) }}</span>

                                                    <div class="btn-group btn-group-sm">
                                                        @can('leads_editcompletedmeeting')
                                                            <button class="btn btn-light" type="button"
                                                                onclick="event.stopPropagation(); editMeetingFromCard(this)"><i
                                                                    class="fas fa-edit"></i></button>
                                                        @endcan
                                                        @can('leads_deletecompletemeeting')
                                                            <button class="btn btn-light text-danger" type="button"
                                                                onclick="event.stopPropagation(); deleteMeeting({{ $meeting->id }})"><i
                                                                    class="fas fa-trash-alt"></i></button>
                                                        @endcan
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">No completed meetings.</div>
                                    @endforelse
                                @endif
                            </div>
                        </div>
                    </div>



                    <div class="tab-pane fade {{ $defaultTab === 'emails-pane' ? 'show active' : '' }}" id="emails-pane">
                        <div class="section-toolbar mb-3">
                            <h5 class="section-title mb-0">Emails</h5>
                            @can('leads_sendemail')
                                <button class="btn btn-primary" type="button" onclick="openComposeEmailModal()">Compose
                                    Email</button>
                            @endcan
                        </div>
                        @if($lead->emails->isEmpty())
                            <div class="empty-state">No emails logged for this lead.</div>
                        @else
                            @foreach($lead->emails as $email)
                                <div class="timeline-card">
                                    <h6 class="mb-1">{{ $email->subject ?: 'No subject' }}</h6>
                                    <div class="text-muted small mb-2">From {{ $email->from_email ?: '-' }} | To
                                        {{ $email->to_email ?: '-' }} @if($email->cc)| Cc {{ $email->cc }}@endif
                                    </div>
                                    <p class="mb-0">{!! nl2br(e(\Illuminate\Support\Str::limit($email->body, 350))) !!}</p>
                                    <div class="mt-2 small text-muted">
                                        {{ optional($email->created_at)->format('d M Y, h:i A') ?: '-' }} | Sent by
                                        {{ optional($email->sentBy)->username ?? optional($email->sentBy)->name ?? '-' }}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="noteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="noteForm">@csrf<div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><span id="noteModalTitle">Add Note</span></h5><button type="button"
                            class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body"><input type="hidden" id="note_id">
                        <div class="form-group mb-0"><label>Note</label><textarea id="note_text" rows="5"
                                class="form-control" required></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light"
                            data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="taskModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="taskForm">@csrf<div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><span id="taskModalTitle">Add Task</span></h5><button type="button"
                            class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body"><input type="hidden" id="task_id">
                        <div class="form-group"><label>Subject</label><input type="text" class="form-control"
                                id="task_subject" required></div>
                        <div class="form-group"><label>Assign To</label><select class="form-control" id="task_assigned_to"
                                required>
                                <option value="">Select User</option>@foreach($users as $user)<option
                                value="{{ $user->id }}">{{ $user->username ?? $user->name }}</option>@endforeach
                            </select></div>
                        <div class="form-group"><label>Due Date</label><input type="datetime-local" class="form-control"
                                id="task_due_date"></div>
                        <div class="form-group"><label>Status</label><select class="form-control" id="task_status">
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                            </select></div>
                        <div class="form-group mb-0"><label>Description</label><textarea class="form-control"
                                id="task_description" rows="4"></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light"
                            data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="callModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="callForm">@csrf<div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><span id="callModalTitle">Log Call</span></h5><button type="button"
                            class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body"><input type="hidden" id="call_id"><input type="hidden" id="call_mode"
                            value="logged">
                        <div class="form-group"><label>Call Type</label><select class="form-control" id="call_type">
                                <option value="outbound">Outbound</option>
                                <option value="inbound">Inbound</option>
                            </select></div>
                        <div class="form-group"><label>Call Owner</label><select class="form-control" id="call_owner"
                                required>
                                <option value="">Select User</option>@foreach($users as $user)<option
                                value="{{ $user->id }}">{{ $user->username ?? $user->name }}</option>@endforeach
                            </select></div>
                        <div class="form-group"><label>Purpose</label><input type="text" class="form-control"
                                id="call_purpose"></div>
                        <div class="form-group"><label>Date & Time</label><input type="datetime-local" class="form-control"
                                id="call_date" required></div>
                        <div class="form-group"><label>Duration</label><input type="text" class="form-control"
                                id="call_duration"></div>
                        <div class="form-group"><label>Status</label><select class="form-control" id="call_status">
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="missed">Missed</option>
                                <option value="voicemail">Voicemail</option>
                                <option value="no_answer">No Answer</option>
                            </select></div>
                        <div class="form-group mb-0"><label>Notes</label><textarea class="form-control" id="call_notes"
                                rows="4"></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light"
                            data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="completeCallModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="completeCallForm">@csrf<div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Mark Call Complete</h5><button type="button" class="close text-white"
                            data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body"><input type="hidden" id="complete_call_id">
                        <div class="form-group"><label>Duration</label><input type="text" class="form-control"
                                id="complete_call_duration"></div>
                        <div class="form-group"><label>Status</label><select class="form-control" id="complete_call_status">
                                <option value="completed">Completed</option>
                                <option value="missed">Missed</option>
                                <option value="voicemail">Voicemail</option>
                                <option value="no_answer">No Answer</option>
                            </select></div>
                        <div class="form-group mb-0"><label>Notes</label><textarea class="form-control"
                                id="complete_call_notes" rows="4"></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light"
                            data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="meetingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="meetingForm">@csrf<div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><span id="meetingModalTitle">Schedule Meeting</span></h5><button
                            type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body"><input type="hidden" id="meeting_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label>Title</label><input type="text" class="form-control"
                                        id="meeting_title" required></div>
                            </div>
                            <input type="hidden" name="meeting_type" value="scheduled">
                            <div class="col-md-6">
                                <div class="form-group"><label>Date & Time</label><input type="datetime-local"
                                        class="form-control" id="meeting_date" required></div>
                            </div>
                            <div class="col-md-6 d-none">
                                <div class="form-group"><label>Duration</label><input type="text" class="form-control"
                                        id="meeting_duration"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label>Assigned To</label><select class="form-control"
                                        id="meeting_assigned_to" required>
                                        <option value="">Select User</option>@foreach($users as $user)<option
                                        value="{{ $user->id }}">{{ $user->username ?? $user->name }}</option>@endforeach
                                    </select></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label>Status</label><select class="form-control"
                                        id="meeting_status">
                                        <option value="scheduled">Scheduled</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="rescheduled">Rescheduled</option>
                                    </select></div>
                            </div>
                            <div class="col-md-6 d-none">
                                <div class="form-group"><label>Location</label><input type="text" class="form-control"
                                        id="meeting_location"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label>Meeting Link</label><input type="url" class="form-control"
                                        id="meeting_link"></div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-0"><label>Description</label><textarea class="form-control"
                                        id="meeting_description" rows="4"></textarea></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light"
                            data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="completeMeetingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="completeMeetingForm">@csrf<div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Mark Meeting Complete</h5><button type="button" class="close text-white"
                            data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body"><input type="hidden" id="complete_meeting_id">
                        <div class="form-group mb-0"><label>Notes</label><textarea class="form-control"
                                id="complete_meeting_notes" rows="4"></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light"
                            data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="composeEmailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="composeEmailForm">@csrf<input type="hidden" name="lead_id" value="{{ $lead->id }}">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Compose Email</h5><button type="button" class="close text-white"
                            data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group"><label>To</label><input type="email" class="form-control" name="to_email"
                                id="email_to" value="{{ $lead->email }}" required></div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label>Cc</label><input type="text" class="form-control" name="cc"
                                        id="email_cc" placeholder="comma separated emails"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label>Bcc</label><input type="text" class="form-control" name="bcc"
                                        id="email_bcc" placeholder="comma separated emails"></div>
                            </div>
                        </div>
                        <div class="form-group"><label>Template</label><select class="form-control" id="email_template"
                                name="template_id">
                                <option value="">Select Template</option>@foreach($emailTemplates as $template)<option
                                    value="{{ $template->id }}" data-subject="{{ e($template->subject) }}"
                                data-body="{{ e($template->body) }}">{{ $template->name }}</option>@endforeach
                            </select></div>
                        <div class="form-group"><label>Subject</label><input type="text" class="form-control" name="subject"
                                id="email_subject" required></div>
                        <div class="form-group mb-0"><label>Body</label><textarea class="form-control" name="body"
                                id="email_body" rows="8" required></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light"
                            data-dismiss="modal">Discard</button><button type="submit" class="btn btn-primary">Send
                            Email</button></div>
                </form>
            </div>
        </div>
    </div>
    <style>
        .page-title {
            font-size: 1.35rem;
            font-weight: 600
        }

        .lead-profile-card,
        .info-card,
        .detail-tabs-card,
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(15, 23, 42, .08)
        }

        .lead-avatar {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: #35394F;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            font-weight: 600
        }

        .lead-meta-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: .55rem 0;
            border-bottom: 1px solid #eef2f7;
            font-size: .9rem
        }

        .lead-meta-item:last-child {
            border-bottom: none
        }

        .stat-label {
            color: #64748b;
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .04em
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.1;
            margin: .35rem 0
        }

        .info-item span {
            display: block;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: .3rem
        }

        .lead-detail-tabs .nav-link {
            border-radius: 999px;
            color: #475569;
            font-weight: 500;
            margin-right: .5rem;
            padding: .55rem 1rem;
            background: #f8fafc
        }

        .lead-detail-tabs .nav-link.active {
            background: #35394F;
            color: #fff
        }

        .section-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b
        }

        .timeline-card {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: .9rem;
            transition: all .2s ease
        }

        .timeline-card.editable-record {
            cursor: pointer
        }

        .timeline-card.editable-record:hover {
            border-color: #cbd5e1;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06)
        }

        .empty-state {
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 2rem 1rem;
            text-align: center;
            color: #64748b;
            background: #f8fafc
        }
    </style>
    <script>

        function formatDateLocal(date) {
            if (!date) return '';
            const d = new Date(date);
            const offset = d.getTimezoneOffset();
            return new Date(d.getTime() - offset * 60000).toISOString().slice(0, 16);
        }

        function getRecordCard(src) {
            if (src && src.jquery) return src.closest('.editable-record');
            return $(src).closest('.editable-record');
        }

        function handleReloadResponse(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Something went wrong.');
            }
        }

        function openNoteModal() {
            $('#noteModalTitle').text('Add Note');
            $('#note_id').val('');
            $('#note_text').val('');
            $('#noteModal').modal('show');
        }

        function editNote(id, note) {
            $('#noteModalTitle').text('Edit Note');
            $('#note_id').val(id);
            $('#note_text').val(note || '');
            $('#noteModal').modal('show');
        }

        function openTaskModal() {
            $('#taskModalTitle').text('Add Task');
            $('#task_id').val('');
            $('#task_subject,#task_description,#task_due_date').val('');
            $('#task_status').val('pending');
            $('#task_assigned_to').val('');
            $('#taskModal').modal('show');
        }

        function editTaskFromCard(src) {
            const card = getRecordCard(src);
            $('#taskModalTitle').text('Edit Task');
            $('#task_id').val(card.data('id'));
            $('#task_subject').val(card.data('subject') || '');
            $('#task_description').val(card.data('description') || '');
            $('#task_assigned_to').val(card.data('assigned') || '');
            $('#task_due_date').val(card.data('due') || '');
            $('#task_status').val(card.data('status') || 'pending');
            $('#taskModal').modal('show');
        }

        function openCallModal(mode) {
            $('#call_id').val('');
            $('#call_mode').val(mode || 'logged');
            $('#callModalTitle').text(mode === 'scheduled' ? 'Create Call' : 'Log Call');
            $('#call_type').val('outbound');
            $('#call_owner').val('{{ auth()->id() }}');
            $('#call_purpose').val('');
            $('#call_notes').val('');
            $('#call_duration').val('');
            $('#call_status').val(mode === 'scheduled' ? 'scheduled' : 'completed');
            $('#call_date').val(formatDateLocal(new Date()));
            $('#callModal').modal('show');
        }

        function editCallFromCard(src) {
            const card = getRecordCard(src);
            $('#call_id').val(card.data('id'));
            $('#call_mode').val(card.data('mode') || 'logged');
            $('#callModalTitle').text('Edit Call');
            $('#call_type').val(card.data('call-type') || 'outbound');
            $('#call_owner').val(card.data('call-owner') || '');
            $('#call_purpose').val(card.data('purpose') || '');
            $('#call_notes').val(card.data('notes') || '');
            $('#call_duration').val(card.data('duration') || '');
            $('#call_status').val(card.data('status') || 'completed');
            $('#call_date').val(card.data('date') || '');
            $('#callModal').modal('show');
        }

        function openCompleteCallModal(src) {
            const card = getRecordCard(src);
            $('#complete_call_id').val(card.data('id'));
            $('#complete_call_duration').val(card.data('duration') || '');
            $('#complete_call_status').val('completed');
            $('#complete_call_notes').val(card.data('notes') || '');
            $('#completeCallModal').modal('show');
        }

        function openMeetingModal() {
            $('#meetingModalTitle').text('Schedule Meeting');
            $('#meeting_id').val('');
            $('#meeting_title,#meeting_description,#meeting_location,#meeting_link,#meeting_duration').val('');
            $('#meeting_type').val('virtual');
            $('#meeting_status').val('scheduled');
            $('#meeting_assigned_to').val('');
            $('#meeting_date').val(formatDateLocal(new Date()));
            $('#meetingModal').modal('show');
        }

        function editMeetingFromCard(src) {
            const card = getRecordCard(src);
            $('#meetingModalTitle').text('Edit Meeting');
            $('#meeting_id').val(card.data('id'));
            $('#meeting_title').val(card.data('title') || '');
            $('#meeting_description').val(card.data('description') || '');
            $('#meeting_type').val(card.data('type') || 'virtual');
            $('#meeting_location').val(card.data('location') || '');
            $('#meeting_link').val(card.data('link') || '');
            $('#meeting_date').val(card.data('date') || '');
            $('#meeting_duration').val(card.data('duration') || '');
            $('#meeting_assigned_to').val(card.data('assigned') || '');
            $('#meeting_status').val(card.data('status') || 'scheduled');
            $('#meetingModal').modal('show');
        }

        function openCompleteMeetingModal(id) {
            $('#complete_meeting_id').val(id);
            $('#complete_meeting_notes').val('');
            $('#completeMeetingModal').modal('show');
        }

        function openComposeEmailModal() {
            $('#composeEmailForm')[0].reset();
            $('#email_to').val('{{ $lead->email }}');
            $('#composeEmailModal').modal('show');
        }

        function deleteNote(id) {
            if (!confirm('Delete this note?')) return;
            $.post('{{ url("admin/leads/notes") }}/' + id, {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            }, handleReloadResponse);
        }

        function deleteTask(id) {
            if (!confirm('Delete this task?')) return;
            $.post('{{ url("admin/leads/tasks") }}/' + id, {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            }, handleReloadResponse);
        }

        function deleteCall(id) {
            if (!confirm('Delete this call?')) return;
            $.post('{{ url("admin/leads/calls") }}/' + id, {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            }, handleReloadResponse);
        }

        function deleteMeeting(id) {
            if (!confirm('Delete this meeting?')) return;
            $.post('{{ url("admin/leads/meetings") }}/' + id, {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            }, handleReloadResponse);
        }

        function markTaskCompleted(id) {
            $.post('{{ url("admin/tasks") }}/' + id + '/status', {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                status: 'completed'
            }, handleReloadResponse);
        }

        $(function () {

            $('.editable-record').on('dblclick', function () {
                const kind = $(this).data('kind');
                if (kind === 'note') {
                    editNote($(this).data('id'), $(this).data('note'));
                } else if (kind === 'task') {
                    editTaskFromCard($(this));
                } else if (kind === 'call') {
                    editCallFromCard($(this));
                } else if (kind === 'meeting') {
                    editMeetingFromCard($(this));
                }
            });

            $('#noteForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#note_id').val();
                const payload = {
                    _token: '{{ csrf_token() }}',
                    note: $('#note_text').val(),
                    lead_id: '{{ $lead->id }}'
                };
                if (id) {
                    payload._method = 'PUT';
                    $.post('{{ url("admin/leads/notes") }}/' + id, payload, handleReloadResponse);
                } else {
                    $.post('{{ route("admin.lead.addNote") }}', payload, handleReloadResponse);
                }
            });

            $('#taskForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#task_id').val();
                const payload = {
                    _token: '{{ csrf_token() }}',
                    lead_id: '{{ $lead->id }}',
                    subject: $('#task_subject').val(),
                    description: $('#task_description').val(),
                    assigned_to: $('#task_assigned_to').val(),
                    due_date: $('#task_due_date').val(),
                    status: $('#task_status').val()
                };
                if (id) {
                    payload._method = 'PUT';
                    $.post('{{ url("admin/leads/tasks") }}/' + id, payload, handleReloadResponse);
                } else {
                    $.post('{{ route("admin.lead.addTask") }}', payload, handleReloadResponse);
                }
            });

            $('#callForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#call_id').val();
                const payload = {
                    _token: '{{ csrf_token() }}',
                    lead_id: '{{ $lead->id }}',
                    call_type: $('#call_type').val(),
                    call_owner: $('#call_owner').val(),
                    call_purpose: $('#call_purpose').val(),
                    notes: $('#call_notes').val(),
                    duration: $('#call_duration').val(),
                    status: $('#call_status').val(),
                    call_date: $('#call_date').val()
                };
                if (id) {
                    payload._method = 'PUT';
                    payload.called_by = '{{ auth()->id() }}';
                    $.post('{{ url("admin/leads/calls") }}/' + id + '/full', payload, handleReloadResponse);
                } else if ($('#call_mode').val() === 'scheduled') {
                    payload.status = 'scheduled';
                    $.post('{{ route("admin.lead.scheduleCall") }}', payload, handleReloadResponse);
                } else {
                    $.post('{{ route("admin.lead.addCall") }}', payload, handleReloadResponse);
                }
            });

            $('#completeCallForm').on('submit', function (e) {
                e.preventDefault();
                $.post('{{ url("admin/leads/calls") }}/' + $('#complete_call_id').val(), {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    duration: $('#complete_call_duration').val(),
                    status: $('#complete_call_status').val(),
                    notes: $('#complete_call_notes').val()
                }, handleReloadResponse);
            });

            $('#meetingForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#meeting_id').val();
                const payload = {
                    _token: '{{ csrf_token() }}',
                    lead_id: '{{ $lead->id }}',
                    title: $('#meeting_title').val(),
                    description: $('#meeting_description').val(),
                    meeting_type: $('#meeting_type').val(),
                    location: $('#meeting_location').val(),
                    meeting_link: $('#meeting_link').val(),
                    meeting_date: $('#meeting_date').val(),
                    duration: $('#meeting_duration').val(),
                    assigned_to: $('#meeting_assigned_to').val(),
                    status: $('#meeting_status').val()
                };
                if (id) {
                    payload._method = 'PUT';
                    $.post('{{ url("admin/leads/meetings") }}/' + id, payload, handleReloadResponse);
                } else {
                    delete payload.status;
                    $.post('{{ route("admin.lead.scheduleMeeting") }}', payload, handleReloadResponse);
                }
            });

            $('#completeMeetingForm').on('submit', function (e) {
                e.preventDefault();
                $.post('{{ url("admin/leads/meetings") }}/' + $('#complete_meeting_id').val() + '/complete', {
                    _token: '{{ csrf_token() }}',
                    notes: $('#complete_meeting_notes').val()
                }, handleReloadResponse);
            });

            $('#email_template').on('change', function () {
                const o = $(this).find(':selected');
                $('#email_subject').val(o.data('subject') || '');
                $('#email_body').val(o.data('body') || '');
            });

            $('#composeEmailForm').on('submit', function (e) {
                e.preventDefault();
                $.post('{{ route("admin.lead.sendEmail") }}', $(this).serialize(), handleReloadResponse);
            });

        });
</script>@endsection