@extends('layout.admin')

@section('content')
    <div class="content gogi-page">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="page-title text-dark mb-2">
                        <i data-feather="bell" class="mr-2 text-primary"></i>Notifications
                    </h3>
                    <p class="text-muted mb-0">Review your latest assignments and activity alerts in one place.</p>
                </div>
                <div class="col-md-4 text-md-right mt-3 mt-md-0">
                    @if(auth()->user()->unreadNotifications()->count())
                        <form action="{{ route('admin.notifications.read-all') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Mark All as Read
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @forelse($notifications as $notification)
                    <a href="{{ route('admin.notifications.show', $notification->id) }}"
                        class="notification-page-item d-flex align-items-start justify-content-between {{ is_null($notification->read_at) ? 'is-unread' : '' }}">
                        <div class="pr-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="notification-page-icon">
                                    <i data-feather="bell"></i>
                                </span>
                                <div>
                                    <h6 class="mb-0">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                    <small class="text-muted text-uppercase">
                                        {{ $notification->data['record_type'] ?? 'Record' }}
                                    </small>
                                </div>
                            </div>
                            <p class="mb-1 text-dark">{{ $notification->data['message'] ?? 'You have a new notification.' }}</p>
                            <small class="text-muted">{{ $notification->created_at?->diffForHumans() }}</small>
                        </div>
                        @if(is_null($notification->read_at))
                            <span class="badge badge-primary badge-pill">Unread</span>
                        @endif
                    </a>
                @empty
                    <div class="text-center py-5 px-4">
                        <div class="mb-3 text-muted">
                            <i data-feather="bell-off"></i>
                        </div>
                        <h5 class="mb-1">No notifications yet</h5>
                        <p class="text-muted mb-0">Assignments for your leads and accounts will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <style>
        .notification-page-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #eef2f7;
            text-decoration: none;
            color: inherit;
            transition: background .2s ease, transform .2s ease;
        }

        .notification-page-item:hover {
            background: #f8fafc;
            color: inherit;
            transform: translateY(-1px);
        }

        .notification-page-item:last-child {
            border-bottom: 0;
        }

        .notification-page-item.is-unread {
            background: rgba(59, 130, 246, .06);
        }

        .notification-page-icon {
            width: 42px;
            height: 42px;
            min-width: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: .75rem;
            background: #eef4ff;
            color: #3b82f6;
        }
    </style>
@endsection
