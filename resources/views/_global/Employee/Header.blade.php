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
                <a href="{{ url('employee/main/index') }}">
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
                                <a href="{{ url('employee/profile') }}" class="list-group-item">View Profile</a>
                                
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