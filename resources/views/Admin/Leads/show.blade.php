@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-user-tie text-primary mr-2"></i>{{ $lead->full_name ?: 'Lead Profile' }}
                </h3>
                <p class="text-muted mb-0">Lead profile with notes, activities, calls, meetings, emails, and full contact context.</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Leads
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card lead-profile-card h-100">
                <div class="card-body text-center">
                    @if($lead->lead_image)
                        <img src="{{ asset($lead->lead_image) }}" class="rounded-circle shadow-sm mb-3" width="90" height="90" style="object-fit: cover;">
                    @else
                        <div class="lead-avatar mx-auto mb-3">
                            {{ strtoupper(substr($lead->first_name ?? 'L', 0, 1)) }}{{ strtoupper(substr($lead->last_name ?? 'D', 0, 1)) }}
                        </div>
                    @endif

                    <h4 class="mb-1">{{ $lead->full_name ?: 'Unnamed Lead' }}</h4>
                    <p class="text-muted mb-2">{{ $lead->title ?: 'No title added' }}</p>
                    <span class="badge badge-pill badge-primary px-3 py-2">{{ $lead->lead_status ?: 'No Status' }}</span>

                    <hr>

                    <div class="text-left lead-meta">
                        <div class="lead-meta-item">
                            <span class="text-muted">Company</span>
                            <strong>{{ $lead->company ?: '-' }}</strong>
                        </div>
                        <div class="lead-meta-item">
                            <span class="text-muted">Owner</span>
                            <strong>{{ $lead->owner?->full_name ?? $lead->owner?->username ?? 'Not Assigned' }}</strong>
                        </div>
                        <div class="lead-meta-item">
                            <span class="text-muted">Source</span>
                            <strong>{{ $lead->lead_source ?: '-' }}</strong>
                        </div>
                        <div class="lead-meta-item">
                            <span class="text-muted">Created By</span>
                            <strong>{{ $lead->user?->full_name ?? $lead->user?->username ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="stat-label">Notes</div>
                            <div class="stat-value">{{ $lead->notes->count() }}</div>
                            <small class="text-muted">All lead notes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="stat-label">Activities</div>
                            <div class="stat-value">{{ $lead->activities->count() }}</div>
                            <small class="text-muted">{{ $pendingActivities->count() }} pending, {{ $completedActivities->count() }} completed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="stat-label">Emails</div>
                            <div class="stat-value">{{ $lead->emails->count() }}</div>
                            <small class="text-muted">Sent and logged emails</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="stat-label">Scheduled Calls</div>
                            <div class="stat-value">{{ $scheduledCalls->count() }}</div>
                            <small class="text-muted">Upcoming planned calls</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="stat-label">Logged Calls</div>
                            <div class="stat-value">{{ $loggedCalls->count() }}</div>
                            <small class="text-muted">Completed or logged calls</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="stat-label">Meetings</div>
                            <div class="stat-value">{{ $lead->meetings->count() }}</div>
                            <small class="text-muted">{{ $upcomingMeetings->count() }} upcoming, {{ $completedMeetings->count() }} closed</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card info-card">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0">Lead Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <span class="text-muted">Email</span>
                                <div>{{ $lead->email ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <span class="text-muted">Phone</span>
                                <div>{{ $lead->phone ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <span class="text-muted">Mobile</span>
                                <div>{{ $lead->mobile ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <span class="text-muted">Website</span>
                                <div>{{ $lead->website ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <span class="text-muted">Annual Revenue</span>
                                <div>{{ $lead->annual_revenue ? number_format((float) $lead->annual_revenue, 2) : '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <span class="text-muted">Employees</span>
                                <div>{{ $lead->no_of_employees ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="info-item">
                                <span class="text-muted">Address</span>
                                <div>
                                    {{ $lead->street ?: '-' }}
                                    @if($lead->city || $lead->state || $lead->country || $lead->zip_code)
                                        <br>{{ collect([$lead->city, $lead->state, $lead->country, $lead->zip_code])->filter()->implode(', ') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-item">
                                <span class="text-muted">Description</span>
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
            <ul class="nav nav-pills lead-detail-tabs mb-4" id="leadDetailTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="notes-tab" data-toggle="pill" href="#notes-pane" role="tab">Notes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="activities-tab" data-toggle="pill" href="#activities-pane" role="tab">Activities</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="calls-tab" data-toggle="pill" href="#calls-pane" role="tab">Calls</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="meetings-tab" data-toggle="pill" href="#meetings-pane" role="tab">Meetings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="emails-tab" data-toggle="pill" href="#emails-pane" role="tab">Emails</a>
                </li>
            </ul>

            <div class="tab-content" id="leadDetailTabContent">
                <div class="tab-pane fade show active" id="notes-pane" role="tabpanel">
                    @if($lead->notes->isEmpty())
                        <div class="empty-state">No notes available for this lead.</div>
                    @else
                        @foreach($lead->notes as $note)
                            <div class="timeline-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Note by {{ optional($note->createdBy)->username ?? optional($note->createdBy)->name ?? 'User' }}</h6>
                                        <p class="mb-1">{{ $note->note }}</p>
                                    </div>
                                    <small class="text-muted">{{ optional($note->created_at)->format('d M Y, h:i A') }}</small>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="tab-pane fade" id="activities-pane" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <h5 class="section-title">Open Activities</h5>
                            @forelse($pendingActivities as $activity)
                                <div class="timeline-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $activity->subject }}</h6>
                                            <div class="text-muted small mb-2">{{ ucfirst($activity->type) }}</div>
                                            <p class="mb-0">{{ $activity->description ?: 'No description' }}</p>
                                        </div>
                                        <span class="badge badge-warning">{{ $activity->status }}</span>
                                    </div>
                                    <div class="mt-2 small text-muted">
                                        Due: {{ optional($activity->due_date)->format('d M Y, h:i A') ?: '-' }}
                                        | Assigned: {{ optional($activity->assignedTo)->username ?? optional($activity->assignedTo)->name ?? '-' }}
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">No open activities.</div>
                            @endforelse
                        </div>
                        <div class="col-lg-6 mb-4">
                            <h5 class="section-title">Completed Activities</h5>
                            @forelse($completedActivities as $activity)
                                <div class="timeline-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $activity->subject }}</h6>
                                            <div class="text-muted small mb-2">{{ ucfirst($activity->type) }}</div>
                                            <p class="mb-0">{{ $activity->description ?: 'No description' }}</p>
                                        </div>
                                        <span class="badge badge-success">{{ $activity->status }}</span>
                                    </div>
                                    <div class="mt-2 small text-muted">
                                        Completed: {{ optional($activity->completed_at)->format('d M Y, h:i A') ?: optional($activity->created_at)->format('d M Y, h:i A') }}
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">No completed activities.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="calls-pane" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <h5 class="section-title">Scheduled Calls</h5>
                            @forelse($scheduledCalls as $call)
                                <div class="timeline-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $call->call_purpose ?: 'Scheduled Call' }}</h6>
                                            <div class="text-muted small mb-2">{{ ucfirst($call->call_type) }} call</div>
                                            <p class="mb-0">{{ $call->notes ?: 'No notes' }}</p>
                                        </div>
                                        <span class="badge badge-info">{{ $call->status }}</span>
                                    </div>
                                    <div class="mt-2 small text-muted">
                                        {{ optional($call->call_date)->format('d M Y, h:i A') ?: '-' }}
                                        | By {{ optional($call->calledBy)->username ?? optional($call->calledBy)->name ?? '-' }}
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">No scheduled calls.</div>
                            @endforelse
                        </div>
                        <div class="col-lg-6 mb-4">
                            <h5 class="section-title">Logged Calls</h5>
                            @forelse($loggedCalls as $call)
                                <div class="timeline-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $call->call_purpose ?: 'Logged Call' }}</h6>
                                            <div class="text-muted small mb-2">{{ ucfirst($call->call_type) }} call</div>
                                            <p class="mb-0">{{ $call->notes ?: 'No notes' }}</p>
                                        </div>
                                        <span class="badge badge-secondary">{{ $call->status }}</span>
                                    </div>
                                    <div class="mt-2 small text-muted">
                                        {{ optional($call->call_date)->format('d M Y, h:i A') ?: '-' }}
                                        | Duration: {{ $call->duration ?: '-' }}
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">No logged calls.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="meetings-pane" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <h5 class="section-title">Scheduled Meetings</h5>
                            @forelse($upcomingMeetings as $meeting)
                                <div class="timeline-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $meeting->title }}</h6>
                                            <div class="text-muted small mb-2">{{ ucfirst($meeting->meeting_type) }} meeting</div>
                                            <p class="mb-0">{{ $meeting->description ?: 'No agenda added' }}</p>
                                        </div>
                                        <span class="badge badge-info">{{ $meeting->status }}</span>
                                    </div>
                                    <div class="mt-2 small text-muted">
                                        {{ optional($meeting->meeting_date)->format('d M Y, h:i A') ?: '-' }}
                                        | Duration: {{ $meeting->duration ?: '-' }}
                                        | Assigned: {{ optional($meeting->assignedTo)->username ?? optional($meeting->assignedTo)->name ?? '-' }}
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">No scheduled meetings.</div>
                            @endforelse
                        </div>
                        <div class="col-lg-6 mb-4">
                            <h5 class="section-title">Closed Meetings</h5>
                            @forelse($completedMeetings as $meeting)
                                <div class="timeline-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $meeting->title }}</h6>
                                            <div class="text-muted small mb-2">{{ ucfirst($meeting->meeting_type) }} meeting</div>
                                            <p class="mb-0">{{ $meeting->notes ?: ($meeting->description ?: 'No meeting notes') }}</p>
                                        </div>
                                        <span class="badge badge-success">{{ $meeting->status }}</span>
                                    </div>
                                    <div class="mt-2 small text-muted">
                                        {{ optional($meeting->meeting_date)->format('d M Y, h:i A') ?: '-' }}
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">No completed meetings.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="emails-pane" role="tabpanel">
                    @if($lead->emails->isEmpty())
                        <div class="empty-state">No emails logged for this lead.</div>
                    @else
                        @foreach($lead->emails as $email)
                            <div class="timeline-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $email->subject ?: 'No subject' }}</h6>
                                        <div class="text-muted small mb-2">
                                            From: {{ $email->from_email ?: '-' }}
                                            | To: {{ $email->to_email ?: '-' }}
                                            @if($email->cc)
                                                | Cc: {{ $email->cc }}
                                            @endif
                                        </div>
                                        <p class="mb-0">{!! nl2br(e(\Illuminate\Support\Str::limit($email->body, 400))) !!}</p>
                                    </div>
                                    <span class="badge badge-primary">{{ $email->status ?: 'sent' }}</span>
                                </div>
                                <div class="mt-2 small text-muted">
                                    {{ optional($email->created_at)->format('d M Y, h:i A') ?: '-' }}
                                    | Sent by {{ optional($email->sentBy)->username ?? optional($email->sentBy)->name ?? '-' }}
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-title {
        font-size: 1.35rem;
        font-weight: 600;
    }

    .lead-profile-card,
    .info-card,
    .detail-tabs-card,
    .stat-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.08);
    }

    .lead-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: #35394F;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        font-weight: 600;
    }

    .lead-meta-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.55rem 0;
        border-bottom: 1px solid #eef2f7;
        font-size: 0.9rem;
    }

    .lead-meta-item:last-child {
        border-bottom: none;
    }

    .stat-label {
        color: #64748b;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.1;
        margin: 0.35rem 0;
    }

    .info-item span {
        display: block;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.3rem;
    }

    .lead-detail-tabs .nav-link {
        border-radius: 999px;
        color: #475569;
        font-weight: 500;
        margin-right: 0.5rem;
        padding: 0.55rem 1rem;
        background: #f8fafc;
    }

    .lead-detail-tabs .nav-link.active {
        background: #35394F;
        color: #fff;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #1e293b;
    }

    .timeline-card {
        background: #fff;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 0.9rem;
    }

    .empty-state {
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 2rem 1rem;
        text-align: center;
        color: #64748b;
        background: #f8fafc;
    }
</style>
@endsection
