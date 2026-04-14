@extends('layout.admin')

@section('content')
<div class="content meetings-index-page">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-7">
                <h3 class="page-title text-dark mb-2">
                    <i class="fas fa-calendar-alt text-primary mr-2"></i>Meetings
                </h3>
                <p class="text-muted mb-0">Manage all CRM meetings in one place and keep hosts aligned across the system.</p>
            </div>
            <div class="col-lg-4 col-md-5 text-md-right mt-3 mt-md-0">
                <a href="{{ route('admin.meetings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle mr-1"></i> Create Meeting
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card meeting-table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Related To</th>
                            <th>Meeting Location</th>
                            <th>Location</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Host</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($meetings as $meeting)
                            @php
                                $relatedEntity = $resolvedEntities[$meeting->entity_type][$meeting->entity_id] ?? null;
                                $relatedLabel = match ($meeting->entity_type) {
                                    'lead' => $relatedEntity?->full_name ?: 'Lead #' . $meeting->entity_id,
                                    'account' => $relatedEntity?->name ?: 'Account #' . $meeting->entity_id,
                                    'deal' => $relatedEntity ? 'Deal #' . $relatedEntity->id : 'Deal #' . $meeting->entity_id,
                                    'other' => 'Other',
                                    default => '-',
                                };
                            @endphp
                            <tr>
                                <td class="font-weight-semibold">{{ $meeting->title }}</td>
                                <td>{{ ucfirst($meeting->entity_type) }}{{ $meeting->entity_type !== 'other' ? ' - ' . $relatedLabel : '' }}</td>
                                <td>{{ $locationTypeOptions[$meeting->location_type] ?? ucfirst(str_replace('_', ' ', $meeting->location_type ?? '')) }}</td>
                                <td>{{ $meeting->location ?: '-' }}</td>
                                <td>{{ optional($meeting->starts_at ?: $meeting->meeting_date)->format('d M Y, h:i A') ?: '-' }}</td>
                                <td>{{ optional($meeting->ends_at)->format('d M Y, h:i A') ?: '-' }}</td>
                                <td>{{ optional($meeting->assignedTo)->full_name ?? optional($meeting->assignedTo)->username ?? optional($meeting->assignedTo)->name ?? '-' }}</td>
                                <td><span class="badge badge-primary">{{ ucfirst($meeting->status) }}</span></td>
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.meetings.edit', $meeting) }}" class="btn btn-light">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.meetings.destroy', $meeting) }}" onsubmit="return confirm('Delete this meeting?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light text-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">No meetings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
   
</style>
@endsection
