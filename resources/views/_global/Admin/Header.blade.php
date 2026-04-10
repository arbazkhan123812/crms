<div class="header d-print-none">
    <div class="header-container">
        <div class="header-left">
            <div class="navigation-toggler">
                <a href="#" data-action="navigation-toggler">
                    <i data-feather="menu"></i>
                </a>
            </div>

            <div class="header-logo">
                {{-- Laravel mein route ya url helper use karein --}}
                <a href="{{ url('admin/main/index') }}">
                    <img class="logo" src="{{ asset('assets/logos/inventory_white_logo.png') }}" style="height: 70px; margin-top:1rem;" alt="logo">  
                </a>
            </div>
        </div>

        <div class="header-body">
            <div class="header-body-left">
                <ul class="navbar-nav">
                    <li class="nav-item mr-3">
                        <div class="header-search-form">
                            <form>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button class="btn">
                                            <i data-feather="search"></i>
                                        </button>
                                    </div>
                                    <input type="text" class="form-control" id="searchInput" onkeyup="filterNavigation()" placeholder="Search">
                                    <div class="input-group-append">
                                        <button class="btn header-search-close-btn">
                                            <i data-feather="x"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="header-body-right">
                <ul class="navbar-nav">
                    <li class="nav-item d-none">
                        <a href="#" class="nav-link mobile-header-search-btn" title="Search">
                            <i data-feather="search"></i>
                        </a>
                    </li>

                    <li class="nav-item dropdown d-none d-md-block">
                        <a href="#" class="nav-link" title="Fullscreen" data-toggle="fullscreen">
                            <i class="maximize" data-feather="maximize"></i>
                            <i class="minimize" data-feather="minimize"></i>
                        </a>
                    </li>
                    
                    @can('notifications_viewnotifications')
                    <li class="nav-item dropdown">
                        <a href="#"
                            class="nav-link header-notification-link"
                            data-toggle="dropdown"
                            data-placement="bottom"
                            data-trigger="hover"
                            data-toggle-tooltip="tooltip"
                            title="Notifications"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i data-feather="bell"></i>
                            @if(($headerUnreadNotificationsCount ?? 0) > 0)
                                <span class="badge badge-danger">{{ $headerUnreadNotificationsCount > 99 ? '99+' : $headerUnreadNotificationsCount }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right notification-dropdown">
                            <div class="notification-dropdown-header d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-0">Notifications</h6>
                                    <small class="text-muted">Latest 5 updates for you</small>
                                </div>
                                @if(($headerUnreadNotificationsCount ?? 0) > 0)
                                    <span class="badge badge-primary badge-pill px-3 py-2">{{ $headerUnreadNotificationsCount }} unread</span>
                                @endif
                            </div>

                            <div class="notification-dropdown-list">
                                @forelse($headerNotifications ?? [] as $notification)
                                    <a href="{{ route('admin.notifications.show', $notification->id) }}"
                                        class="notification-dropdown-item {{ is_null($notification->read_at) ? 'is-unread' : '' }}">
                                        <span class="notification-dropdown-item-icon">
                                            <i data-feather="bell"></i>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-block font-weight-semibold text-dark">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </span>
                                            <span class="d-block text-muted small">
                                                {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                            </span>
                                            <span class="d-block text-muted small mt-1">
                                                {{ optional($notification->created_at)->diffForHumans() }}
                                            </span>
                                        </span>
                                    </a>
                                @empty
                                    <div class="notification-dropdown-empty">
                                        <div class="mb-2">
                                            <i data-feather="bell-off"></i>
                                        </div>
                                        <div class="font-weight-semibold text-dark">No new notifications</div>
                                        <small>Assigned leads and accounts will appear here.</small>
                                    </div>
                                @endforelse
                            </div>

                            @can('notifications_viewallnotifications')
                            <div class="notification-dropdown-footer text-center"> <a href="{{ route('admin.notifications.index') }}" class="btn btn-link ">
                                                View All
                                            </a>
                                        </div>
                                        @endcan
                        </div>
                    </li>
                    @endcan

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle user_manu" title="User menu" data-toggle="dropdown">
                            <figure class="avatar avatar-sm">
                                <img src="{{ asset('assets/img/user.png') }}" class="rounded-circle" alt="avatar" />
                            </figure>
                            <span class="ml-2 d-sm-inline d-none">
                                {{-- Laravel Auth Helper use karein --}}
                                {{ Auth::user()->username ?? 'Guest' }}
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-big">
                            <div class="text-center py-4">
                                <figure class="avatar avatar-lg mb-3 border-0">
                                    <img src="{{ asset('assets/img/user.png') }}" class="rounded-circle" alt="image" />
                                </figure>
                                <h5 class="text-center">
                                    {{ Auth::user()->email ?? '' }}
                                </h5>
                                <div class="mb-2 small text-center text-muted">
                                    {{-- Agar role session mein rakha hai to session() helper use karein --}}
                                    {{ session('name') }}
                                </div>
                            </div>
                            <div class="list-group">
                                <a href="{{ url('admin/profile') }}" class="list-group-item">View Profile</a>
                                
                                {{-- Logout ke liye Laravel mein POST request standard hai, lekin abhi url use kar lein --}}
                                <a href="{{ route('auth.logout') }}" class="list-group-item text-danger">Sign Out!</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item header-toggler">
                <a href="#" class="nav-link">
                    <i data-feather="arrow-down"></i>
                </a>
            </li>
        </ul>
    </div>
</div>
