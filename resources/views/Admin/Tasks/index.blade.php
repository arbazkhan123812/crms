@extends('layout.admin')

@section('content')
@php
    $columnLabels = $statusOptions;
    $boardTasks = $tasks->map(function ($task) use ($columnLabels) {
        $task->board_status = array_key_exists($task->status, $columnLabels) ? $task->status : \App\Models\CrmTask::STATUS_DEFERRED;
        return $task;
    });
@endphp

<div class="content task-board-page">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-7">
                <h3 class="page-title text-dark mb-2">
                    <i class="fas fa-tasks text-primary mr-2"></i>Tasks
                </h3>
                <p class="text-muted mb-0">Track work in a Kanban board, keep owners visible, and move tasks across statuses as work progresses.</p>
            </div>
            <div class="col-lg-4 col-md-5 text-md-right mt-3 mt-md-0">
                <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle mr-1"></i> Create Task
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card task-stat-card h-100">
                <div class="card-body">
                    <div class="task-stat-label">Total Tasks</div>
                    <div class="task-stat-value">{{ $tasks->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card task-stat-card h-100">
                <div class="card-body">
                    <div class="task-stat-label">Deferred</div>
                    <div class="task-stat-value">{{ $boardTasks->where('board_status', \App\Models\CrmTask::STATUS_DEFERRED)->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card task-stat-card h-100">
                <div class="card-body">
                    <div class="task-stat-label">In Progress</div>
                    <div class="task-stat-value">{{ $boardTasks->where('board_status', \App\Models\CrmTask::STATUS_IN_PROGRESS)->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card task-stat-card h-100">
                <div class="card-body">
                    <div class="task-stat-label">Completed</div>
                    <div class="task-stat-value">{{ $boardTasks->where('board_status', \App\Models\CrmTask::STATUS_COMPLETED)->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="task-board">
        @foreach($columnLabels as $statusKey => $statusLabel)
            @php
                $columnTasks = $boardTasks->where('board_status', $statusKey);
            @endphp
            <div class="task-column">
                <div class="task-column-header">
                    <div>
                        <h5 class="mb-1">{{ $statusLabel }}</h5>
                        <small>{{ $columnTasks->count() }} {{ \Illuminate\Support\Str::plural('task', $columnTasks->count()) }}</small>
                    </div>
                    <span class="task-column-chip">{{ $statusLabel }}</span>
                </div>

                <div class="task-dropzone" data-status="{{ $statusKey }}">
                    @forelse($columnTasks as $task)
                        @php
                            $relatedEntity = $resolvedEntities[$task->entity_type][$task->entity_id] ?? null;
                            $relatedName = match ($task->entity_type) {
                                'lead' => $relatedEntity?->full_name,
                                'contact' => $relatedEntity?->full_name,
                                'account' => $relatedEntity?->name,
                                default => null,
                            };
                            $relatedUrl = match ($task->entity_type) {
                                'lead' => $relatedEntity ? route('admin.lead.show', $relatedEntity->id) : null,
                                'contact' => $relatedEntity ? route('admin.contact.show', $relatedEntity->id) : null,
                                'account' => $relatedEntity ? route('admin.account.show', $relatedEntity->id) : null,
                                default => null,
                            };
                            $priorityClass = [
                                'high' => 'danger',
                                'normal' => 'primary',
                                'low' => 'secondary',
                            ][$task->priority] ?? 'secondary';
                        @endphp
                        <div class="task-card" draggable="true" data-task-id="{{ $task->id }}">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="task-card-title">{{ $task->subject }}</div>
                                    <div class="task-card-subtitle">
                                        {{ optional($task->assignedTo)->full_name ?? optional($task->assignedTo)->username ?? optional($task->assignedTo)->name ?? '-' }}
                                    </div>
                                </div>
                                <span class="badge badge-{{ $priorityClass }} px-2 py-1">{{ $priorityOptions[$task->priority] ?? ucfirst($task->priority ?? 'normal') }}</span>
                            </div>

                            <div class="task-meta-row">
                                <span class="task-meta-label">Due</span>
                                <span>{{ optional($task->due_date)->format('d M Y, h:i A') ?: 'Not set' }}</span>
                            </div>

                            <div class="task-meta-row">
                                <span class="task-meta-label">Related</span>
                                <span>
                                    @if($relatedUrl)
                                        <a href="{{ $relatedUrl }}">{{ $relatedName ?: 'View record' }}</a>
                                    @else
                                        {{ $relatedName ?: 'Not linked' }}
                                    @endif
                                </span>
                            </div>

                            <div class="task-meta-row">
                                <span class="task-meta-label">Type</span>
                                <span>{{ ucfirst($task->entity_type) }}</span>
                            </div>

                            <div class="task-meta-row border-0 pb-0 mb-0">
                                <span class="task-meta-label">Account</span>
                                <span>{{ $task->account?->name ?: '-' }}</span>
                            </div>

                            @if($task->description)
                                <div class="task-card-description mt-3">{{ $task->description }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="task-empty-state">Drop tasks here</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    (function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let draggedCard = null;

        function syncEmptyState(zone) {
            const cards = zone.querySelectorAll('.task-card');
            const emptyState = zone.querySelector('.task-empty-state');

            if (cards.length === 0) {
                if (!emptyState) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'task-empty-state';
                    placeholder.textContent = 'Drop tasks here';
                    zone.appendChild(placeholder);
                }

                return;
            }

            if (emptyState) {
                emptyState.remove();
            }
        }

        function updateColumnSummary(zone) {
            const column = zone.closest('.task-column');
            const count = zone.querySelectorAll('.task-card').length;
            column.querySelector('.task-column-header small').textContent = `${count} ${count === 1 ? 'task' : 'tasks'}`;
        }

        document.querySelectorAll('.task-card').forEach((card) => {
            card.addEventListener('dragstart', function () {
                draggedCard = this;
                this.classList.add('dragging');
            });

            card.addEventListener('dragend', function () {
                this.classList.remove('dragging');
                draggedCard = null;
            });
        });

        document.querySelectorAll('.task-dropzone').forEach((zone) => {
            zone.addEventListener('dragover', function (event) {
                event.preventDefault();
                this.classList.add('is-over');
            });

            zone.addEventListener('dragleave', function () {
                this.classList.remove('is-over');
            });

            zone.addEventListener('drop', function (event) {
                event.preventDefault();
                this.classList.remove('is-over');

                if (!draggedCard) {
                    return;
                }

                const nextStatus = this.dataset.status;
                const sourceZone = draggedCard.closest('.task-dropzone');

                if (sourceZone.dataset.status === nextStatus) {
                    return;
                }

                $.ajax({
                    url: '{{ url('admin/crm-tasks') }}/' + draggedCard.dataset.taskId + '/status',
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        _method: 'PUT',
                        status: nextStatus
                    },
                    success: (response) => {
                        if (!response.success) {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Unable to update task status.' });
                            return;
                        }

                        this.appendChild(draggedCard);
                        syncEmptyState(sourceZone);
                        syncEmptyState(this);
                        updateColumnSummary(sourceZone);
                        updateColumnSummary(this);
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Updated',
                            text: response.message,
                            timer: 1200,
                            showConfirmButton: false
                        });
                    },
                    error: (xhr) => {
                        const message = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Unable to update task status.';

                        Swal.fire({ icon: 'error', title: 'Error', text: message });
                    }
                });
            });

            syncEmptyState(zone);
            updateColumnSummary(zone);
        });
    })();
</script>

<style>
    .task-board-page {
        --task-bg: linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
        --task-border: #d6e3f0;
        --task-text: #10243e;
        --task-muted: #66768b;
        --task-accent: #2f80ed;
        --task-accent-soft: rgba(47, 128, 237, 0.12);
    }

    .task-stat-card,
    .task-column,
    .task-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
    }

    .task-stat-card {
        background: #fff;
    }

    .task-stat-label {
        font-size: .82rem;
        letter-spacing: .04em;
        color: var(--task-muted);
        text-transform: uppercase;
    }

    .task-stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--task-text);
        line-height: 1.1;
        margin-top: .4rem;
    }

    .task-board {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.5rem;
        align-items: start;
    }

    .task-column {
        background: var(--task-bg);
        border: 1px solid var(--task-border);
        padding: 1rem;
        min-height: 480px;
    }

    .task-column-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .task-column-header h5 {
        color: var(--task-text);
        font-weight: 700;
    }

    .task-column-header small {
        color: var(--task-muted);
    }

    .task-column-chip {
        background: var(--task-accent-soft);
        color: var(--task-accent);
        border-radius: 999px;
        padding: .35rem .8rem;
        font-size: .76rem;
        font-weight: 600;
    }

    .task-dropzone {
        min-height: 360px;
        border-radius: 16px;
        transition: background .2s ease, border-color .2s ease;
    }

    .task-dropzone.is-over {
        background: rgba(47, 128, 237, 0.08);
    }

    .task-card {
        background: #fff;
        padding: 1rem;
        margin-bottom: 1rem;
        cursor: grab;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12);
    }

    .task-card.dragging {
        opacity: .75;
        transform: rotate(1deg);
    }

    .task-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--task-text);
    }

    .task-card-subtitle {
        color: var(--task-muted);
        font-size: .88rem;
        margin-top: .15rem;
    }

    .task-meta-row {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        border-bottom: 1px solid #edf2f7;
        padding-bottom: .55rem;
        margin-bottom: .55rem;
        font-size: .88rem;
    }

    .task-meta-label {
        color: var(--task-muted);
        font-weight: 600;
    }

    .task-card-description {
        color: #334155;
        font-size: .9rem;
        line-height: 1.5;
    }

    .task-empty-state {
        border: 1px dashed #bfd0e2;
        border-radius: 14px;
        padding: 1.25rem;
        text-align: center;
        color: var(--task-muted);
        background: rgba(255, 255, 255, 0.72);
    }

    @media (max-width: 991.98px) {
        .task-board {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
