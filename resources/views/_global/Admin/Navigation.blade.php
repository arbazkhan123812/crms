<div class="navigation mt-4">
    <div class="navigation-header">
        <span>Navigation</span>
        <a href="#">
            <i class="ti-close"></i>
        </a>
    </div>
    <div class="navigation-menu-body">
        <ul>

            {{-- Example for another link --}}
            @can('dashboard_view')
                <li>
                    <a href="{{ url('admin/dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <i class="fas fa-home"></i>
                        </span>
                        <span>Dashboard</span>
                    </a>
                </li>
            @endcan

            @can('users_management')
                <li>

                    <a href="#">
                        <span class="nav-link-icon">
                            <i data-feather="user"></i>
                        </span>
                        <span>User Management</span>
                    </a>
                    <ul>
                        @can('users_view')
                            <li>
                                {{-- Laravel mein active class ke liye request()->is() best hai --}}
                                <a href="{{ url('admin/users/index') }}"
                                    class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                                    <span class="nav-link-icon">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <span>Users</span>
                                </a>
                            </li>
                        @endcan


                        @can('roles_view')
                            <li>
                                {{-- Laravel mein active class ke liye request()->is() best hai --}}
                                <a href="{{ url('admin/roles/index') }}"
                                    class="{{ request()->is('admin/roles*') ? 'active' : '' }}">
                                    <span class="nav-link-icon"><i data-feather="star"></i></span>
                                    <span>Roles</span>
                                </a>
                            </li>
                        @endcan
                        @can('permissions_view')
                            <li>
                                {{-- Laravel mein active class ke liye request()->is() best hai --}}
                                <a href="{{ url('admin/permissions') }}"
                                    class="{{ request()->is('admin/permissions*') ? 'active' : '' }}">
                                    <span class="nav-link-icon"><i data-feather="key"></i></span>
                                    <span>Permissions</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @auth

                    <ul>
                        @can('leads_view')
                            <li>
                                {{-- Laravel mein active class ke liye request()->is() best hai --}}
                                <a href="{{ url('admin/leads') }}"
                                    class="{{ request()->is('admin/leads*') ? 'active' : '' }}">
                                    <span class="nav-link-icon">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <span>Leads</span>
                                </a>
                            </li>
                        @endcan
                        @can('accounts_view')
                            <li>
                                <a href="{{ url('admin/accounts') }}"
                                    class="{{ request()->is('admin/accounts') ? 'active' : '' }}">
                                    <span class="nav-link-icon">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <span>Accounts</span>
                                </a>
                            </li>
                        @endcan
                        <li>
                            <a href="{{ url('admin/deals') }}"
                                class="{{ request()->is('admin/deals*') ? 'active' : '' }}">
                                <span class="nav-link-icon">
                                    <i class="fas fa-handshake"></i>
                                </span>
                                <span>Deals</span>
                            </a>
                        </li>
                        @can('contacts_view')
                            <li>
                                <a href="{{ url('admin/contacts') }}"
                                    class="{{ request()->is('admin/contacts*') ? 'active' : '' }}">
                                    <span class="nav-link-icon">
                                        <i class="fas fa-address-book"></i>
                                    </span>
                                    <span>Contacts</span>
                                </a>
                            </li>
                        @endcan


                     
                    </ul>
                
            @endauth






        </ul>
    </div>
</div>
<script>
    function filterNavigation() {
        var input = document.getElementById("searchInput").value.toLowerCase();
        var navItems = document.querySelectorAll(".navigation-menu-body > ul > li");

        navItems.forEach(function (item) {
            var text = item.querySelector('a').textContent.toLowerCase();
            var subItems = item.querySelectorAll('ul li a');
            var matchedSubItem = false;

            if (text.includes(input)) {
                item.style.display = "";
            } else {
                subItems.forEach(function (subItem) {
                    if (subItem.textContent.toLowerCase().includes(input)) {
                        matchedSubItem = true;
                    }
                });
                item.style.display = matchedSubItem ? "" : "none";
            }
        });
    }
</script>
