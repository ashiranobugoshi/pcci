{{-- Overlay for mobile sidebar --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="adminSidebar">
    <a href="{{ route('admin.profile') }}" class="admin-profile">
        <img src="https://i.pravatar.cc/150?u=default" class="avatar" id="sidebarAvatar" alt="Admin">
        <div class="admin-info text-truncate w-100">
            <span class="role">Admin</span>
            <span class="name text-truncate" id="sidebarAdminName">ADMIN</span>
        </div>
    </a>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const name = localStorage.getItem('userName');
            if (name) {
                const el = document.getElementById('sidebarAdminName');
                if (el) el.textContent = name.toUpperCase();
            }
            const avatar = localStorage.getItem('adminAvatar');
            if (avatar) {
                const img = document.getElementById('sidebarAvatar');
                if (img) img.src = avatar;
            }
        });
    </script>

    <div class="menu-label">Admin Panel</div>


    
    <nav>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> DASHBOARD
        </a>
        
        <a href="{{ route('members') }}" class="nav-link {{ request()->routeIs('members') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> MEMBERS
        </a>
        
        <a href="{{ route('applicants') }}" class="nav-link {{ request()->routeIs('applicants') || request()->routeIs('applicant.profile') ? 'active' : '' }}">
            <i class="bi bi-person-fill"></i> APPLICANT
        </a>

        <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i> ADMIN USERS
        </a>
        <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i> SETTINGS
    </a>

        {{-- CONTENT DROPDOWN --}}
        <div class="nav-dropdown">
            <button class="nav-dropdown-toggle {{ request()->routeIs('content.*') ? 'active open' : '' }}" id="contentDropdownToggle">
                <i class="bi bi-collection-play"></i> CONTENT
                <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="nav-dropdown-menu {{ request()->routeIs('content.*') ? 'open' : '' }}" id="contentDropdownMenu">
                <a href="{{ route('content.trustees-admin') }}" class="{{ request()->routeIs('content.trustees-admin') ? 'active' : '' }}">Board of Trustees</a>
                <a href="{{ route('content.activities') }}" class="{{ request()->routeIs('content.activities') ? 'active' : '' }}"> PCCI Activities</a>
                <a href="{{ route('content.event-admin') }}" class="{{ request()->routeIs('content.event-admin') ? 'active' : '' }}">Event</a>
            </div>
        </div>

        {{-- NEW SETTINGS TAB (BELOW CONTENT) --}}
        <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i> SETTINGS
        </a>
    </nav>

    <div class="logout-box mt-auto">
    <button type="button" class="btn-logout d-flex justify-content-center align-items-center gap-2" onclick="handleLogout(event)">
        <i class="bi bi-box-arrow-right"></i> LOG OUT
    </button>
</div>
</aside>