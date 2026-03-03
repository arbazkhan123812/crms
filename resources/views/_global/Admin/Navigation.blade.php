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
            <li>
                <a href="{{ url('admin/dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <span class="nav-link-icon">
                        <i class="fas fa-home"></i>
                    </span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i data-feather="user"></i>
                    </span>
                    <span>User Management</span>
                </a>
                <ul>

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

                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/roles/index') }}"
                            class="{{ request()->is('admin/roles*') ? 'active' : '' }}">
                            <span class="nav-link-icon"><i data-feather="star"></i></span>
                            <span>Roles</span>
                        </a>
                    </li>
                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/permissions') }}"
                            class="{{ request()->is('admin/permissions*') ? 'active' : '' }}">
                            <span class="nav-link-icon"><i data-feather="key"></i></span>
                            <span>Permissions</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i data-feather="user"></i>
                    </span>
                    <span>Employees</span>
                </a>
                <ul>

                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/employees/create') }}"
                            class="{{ request()->is('admin/users/create') ? 'active' : '' }}">
                            <span class="nav-link-icon">
                                <i data-feather="users"></i>
                            </span>
                            <span>Add Employees</span>
                        </a>
                    </li>

                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/employees') }}"
                            class="{{ request()->is('admin/employees') ? 'active' : '' }}">
                            <span class="nav-link-icon"><i data-feather="eye"></i></span>
                            <span>View Employees</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i data-feather="user"></i>
                    </span>
                    <span>Designations</span>
                </a>
                <ul>

                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/designations/create') }}"
                            class="{{ request()->is('admin/designation/create') ? 'active' : '' }}">
                            <span class="nav-link-icon">
                                <i data-feather="users"></i>
                            </span>
                            <span>Add Designation</span>
                        </a>
                    </li>

                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/designations') }}"
                            class="{{ request()->is('admin/designation') ? 'active' : '' }}">
                            <span class="nav-link-icon"><i data-feather="eye"></i></span>
                            <span>View Designation</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i data-feather="user"></i>
                    </span>
                    <span>Departments</span>
                </a>
                <ul>

                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/departments/create') }}"
                            class="{{ request()->is('admin/designation/create') ? 'active' : '' }}">
                            <span class="nav-link-icon">
                                <i data-feather="users"></i>
                            </span>
                            <span>Add Departments</span>
                        </a>
                    </li>

                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/departments') }}"
                            class="{{ request()->is('admin/designation') ? 'active' : '' }}">
                            <span class="nav-link-icon"><i data-feather="eye"></i></span>
                            <span>View Departments</span>
                        </a>
                    </li>

                </ul>
            </li>
           
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i data-feather="user"></i>
                    </span>
                    <span>Recruitment</span>
                </a>
                <ul>

                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/recruitment/jobs/create') }}"
                            class="{{ request()->is('admin/recruitment/jobs/create') ? 'active' : '' }}">
                            <span class="nav-link-icon">
                                <i data-feather="users"></i>
                            </span>
                            <span>Add Recruitment</span>
                        </a>
                    </li>
                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/recruitment') }}"
                            class="{{ request()->is('admin/recruitment/jobs/') ? 'active' : '' }}">
                            <span class="nav-link-icon">
                                <i data-feather="eye"></i>
                            </span>
                            <span>View Recruitments</span>
                        </a>
                    </li>
                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/recruitment/candidates/create') }}"
                            class="{{ request()->is('admin/candidates/create') ? 'active' : '' }}">
                            <span class="nav-link-icon">
                                <i data-feather="eye"></i>
                            </span>
                            <span>Add Candidates</span>
                        </a>
                    </li>
                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/recruitment/candidates') }}"
                            class="{{ request()->is('admin/recruitment/candidates') ? 'active' : '' }}">
                            <span class="nav-link-icon">
                                <i data-feather="eye"></i>
                            </span>
                            <span>View Candidates</span>
                        </a>
                    </li>
                    <li>
                        {{-- Laravel mein active class ke liye request()->is() best hai --}}
                        <a href="{{ url('admin/recruitment/jobs') }}"
                            class="{{ request()->is('admin/recruitment/jobs') ? 'active' : '' }}">
                            <span class="nav-link-icon">
                                <i data-feather="eye"></i>
                            </span>
                            <span>View Jobs</span>
                        </a>
                    </li>


                </ul>
            </li>
           
            
            
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