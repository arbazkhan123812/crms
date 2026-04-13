@extends('layout.admin')

@section('content')
<div class="content call-index-page">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-7">
                <h3 class="page-title text-dark mb-2">
                    <i class="fas fa-phone-alt text-primary mr-2"></i>Calls
                </h3>
                <p class="text-muted mb-0">See scheduled and logged calls together across leads, contacts, and accounts.</p>
            </div>
            <div class="col-lg-4 col-md-5 text-md-right mt-3 mt-md-0">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus-circle mr-1"></i> Create Call
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="{{ route('admin.calls.create', ['mode' => 'schedule']) }}">Schedule Call</a>
                        <a class="dropdown-item" href="{{ route('admin.calls.create', ['mode' => 'log']) }}">Log Call</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card call-stat-card h-100">
                <div class="card-body">
                    <div class="call-stat-label">Total Calls</div>
                    <div class="call-stat-value">{{ $calls->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card call-stat-card h-100">
                <div class="card-body">
                    <div class="call-stat-label">Scheduled Calls</div>
                    <div class="call-stat-value">{{ $scheduleCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card call-stat-card h-100">
                <div class="card-body">
                    <div class="call-stat-label">Logged Calls</div>
                    <div class="call-stat-value">{{ $loggedCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card call-table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" id="myTable">
                    <thead>
                        <tr>
                            <th>Call For</th>
                            <th>Owner</th>
                            <th>Mode</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Subject</th>
                            <th>Related To</th>
                            <th>Start Time</th>
                            <th>Duration</th>
                            <th>Outgoing Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calls as $call)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ $call['call_for_type'] }}</div>
                                    <div class="small text-muted">
                                        @if($call['call_for_url'])
                                            <a href="{{ $call['call_for_url'] }}">{{ $call['call_for_name'] }}</a>
                                        @else
                                            {{ $call['call_for_name'] }}
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $call['owner_name'] }}</td>
                                <td>
                                    <span class="badge badge-{{ $call['mode'] === 'schedule' ? 'primary' : 'success' }}">
                                        {{ ucfirst($call['mode']) }}
                                    </span>
                                </td>
                                <td>{{ $call['type_label'] }}</td>
                                <td>{{ $call['status_label'] }}</td>
                                <td>{{ $call['subject'] }}</td>
                                <td>{{ $call['related_to_name'] }}</td>
                                <td>{{ optional($call['start_time'])->format('d M Y, h:i A') ?: '-' }}</td>
                                <td>{{ $call['duration'] }}</td>
                                <td>{{ $call['outgoing_call_status'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="call-empty-state">No calls found yet.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .call-index-page {
        --call-bg: linear-gradient(180deg, #fffdf7 0%, #f5f7fb 100%);
        --call-border: #dde4ee;
        --call-text: #172033;
        --call-muted: #6b7687;
    }

    .call-stat-card,
    .call-table-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 16px 38px rgba(15, 23, 42, 0.08);
        background: #fff;
    }

    .call-stat-label {
        font-size: .82rem;
        letter-spacing: .04em;
        color: var(--call-muted);
        text-transform: uppercase;
    }

    .call-stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--call-text);
        margin-top: .45rem;
        line-height: 1.1;
    }

    .call-empty-state {
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        padding: 1.5rem;
        background: #f8fafc;
        color: var(--call-muted);
    }
</style>
@endsection
