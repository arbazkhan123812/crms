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