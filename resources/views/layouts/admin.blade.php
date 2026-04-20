<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PCCI Admin')</title>
    @include('partials.api-config')
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --pcci-red: #be1e38;
            --pcci-light-red: #e35d5d;
            --sidebar-width: 280px;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            display: flex;
            height: 100vh;
            background-color: #f4f6f9; /* Soft background for the whole admin area */
            overflow: hidden;
        }

        /* ============================================== */
        /* SIDEBAR                                        */
        /* ============================================== */

        .sidebar {
            width: var(--sidebar-width);
            background-color: #ffffff;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
            flex-shrink: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1050;
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }

        .admin-profile {
            padding: 0 25px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #f0f0f0;
            text-decoration: none; 
            cursor: pointer; 
            transition: background 0.2s;
        }
        
        .admin-profile:hover { background-color: #fff1f3; }

        .avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: 2px solid var(--pcci-red);
            object-fit: cover;
        }

        .admin-info span { display: block; }
        .role { font-size: 11px; color: #888; text-transform: uppercase; font-weight: 600; }
        .name { font-size: 1.1rem; font-weight: 700; color: var(--pcci-red); }

        .menu-label {
            padding: 25px 25px 10px;
            font-size: 11px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
        }

        /* --- Nav Links --- */
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
            gap: 12px;
            transition: 0.2s;
            border-radius: 0 25px 25px 0;
            margin-right: 15px;
        }

        .nav-link.active {
            background-color: var(--pcci-red);
            color: white;
        }

        .nav-link:hover:not(.active) {
            background-color: #fff1f3;
            color: var(--pcci-red);
        }

        /* --- Content Dropdown --- */
        .nav-dropdown { position: relative; }

        .nav-dropdown-toggle {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
            gap: 12px;
            transition: 0.2s;
            cursor: pointer;
            border: none;
            background: none;
            width: calc(100% - 15px);
            text-align: left;
            border-radius: 0 25px 25px 0;
        }

        .nav-dropdown-toggle.active {
            background-color: var(--pcci-red);
            color: white;
        }

        .nav-dropdown-toggle:hover:not(.active) {
            background-color: #fff1f3;
            color: var(--pcci-red);
        }

        .nav-dropdown-toggle .chevron {
            margin-left: auto;
            font-size: 0.75rem;
            transition: transform 0.3s ease;
        }

        .nav-dropdown-toggle.open .chevron { transform: rotate(180deg); }

        /* Sub-menu */
        .nav-dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
            background: #fafafa;
        }

        .nav-dropdown-menu.open { max-height: 300px; }

        .nav-dropdown-menu a {
            display: flex;
            align-items: center;
            padding: 10px 25px 10px 62px;
            text-decoration: none;
            color: #555;
            font-weight: 500;
            font-size: 0.88rem;
            gap: 10px;
            transition: 0.2s;
            position: relative;
        }

        .nav-dropdown-menu a::before {
            content: ''; width: 6px; height: 6px; border-radius: 50%;
            background: #ccc; position: absolute; left: 42px; transition: background 0.2s;
        }

        .nav-dropdown-menu a:hover { background-color: #fff1f3; color: var(--pcci-red); }
        .nav-dropdown-menu a:hover::before { background: var(--pcci-red); }
        .nav-dropdown-menu a.active { color: var(--pcci-red); font-weight: 700; background: #fff1f3; }
        .nav-dropdown-menu a.active::before { background: var(--pcci-red); }

        /* --- Logout --- */
        .logout-box { padding: 20px; margin-top: auto; }
        .btn-logout {
            width: 100%;
            background-color: var(--pcci-light-red);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            text-transform: uppercase;
        }

        .btn-logout:hover { background-color: var(--pcci-red); }

        /* ============================================== */
        /* MAIN CONTENT                                   */
        /* ============================================== */

        .main { 
            flex: 1; 
            overflow-y: auto; 
            overflow-x: hidden;
            background-color: #f4f6f9; 
            position: relative;
        }

        .admin-content-shell {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px clamp(16px, 2.5vw, 28px) 32px;
            box-sizing: border-box;
        }

        /* ============================================== */
        /* MOBILE HAMBURGER BUTTON & OVERLAY              */
        /* ============================================== */

        .hamburger-btn {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: var(--pcci-red);
            color: #fff;
            border: none;
            border-radius: 8px;
            width: 45px;
            height: 45px;
            font-size: 1.5rem;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(190, 30, 56, 0.3);
            transition: background 0.2s;
        }

        .hamburger-btn:hover { background: #9a182d; }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active { display: block; opacity: 1; }

        .back-to-top-btn {
            position: fixed;
            right: 22px;
            bottom: 22px;
            width: 46px;
            height: 46px;
            border: none;
            border-radius: 999px;
            background: var(--pcci-red);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(190, 30, 56, 0.3);
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transform: translateY(8px);
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease, background-color 0.2s ease;
            z-index: 1200;
        }

        .back-to-top-btn.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .back-to-top-btn:hover {
            background: #9a182d;
        }

        /* ============================================== */
        /* RESPONSIVE BREAKPOINTS                         */
        /* ============================================== */

        @media (max-width: 991.98px) {
            .hamburger-btn { display: flex; }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                box-shadow: none;
            }

            .sidebar.open {
                transform: translateX(0);
                box-shadow: 5px 0 25px rgba(0, 0, 0, 0.15);
            }

            .main { width: 100%; }
            
            /* Add top padding to body content so it isn't hidden under hamburger */
            .admin-content-shell > :first-child { padding-top: 75px !important; }
        }

        @media (max-width: 575.98px) {
            .sidebar { width: 280px; }
            .hamburger-btn { top: 10px; left: 10px; width: 40px; height: 40px; font-size: 1.25rem; }
            .back-to-top-btn { right: 14px; bottom: 14px; width: 42px; height: 42px; }
        }
    </style>
    
    @include('layouts.admin-title-style')
</head>
<body>

    {{-- Mobile hamburger button --}}
    <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle sidebar">
        <i class="bi bi-list" id="hamburgerIcon"></i>
    </button>

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
        </nav>

        <div class="logout-box mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout d-flex justify-content-center align-items-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> LOG OUT
                </button>
            </form>
        </div>
    </aside>

    <main class="main" id="mainContent">
        <div class="admin-content-shell">
            @yield('content')
        </div>
    </main>

    <button class="back-to-top-btn" id="adminBackToTop" aria-label="Back to top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Content dropdown toggle
        const toggle = document.getElementById('contentDropdownToggle');
        const menu = document.getElementById('contentDropdownMenu');

        toggle.addEventListener('click', function() {
            this.classList.toggle('open');
            menu.classList.toggle('open');
        });

        // Mobile sidebar toggle
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const hamburgerIcon = document.getElementById('hamburgerIcon');
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            hamburgerIcon.classList.replace('bi-list', 'bi-x-lg');
            document.body.style.overflow = 'hidden'; // Prevent scrolling bg
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            hamburgerIcon.classList.replace('bi-x-lg', 'bi-list');
            document.body.style.overflow = ''; // Restore scrolling
        }

        hamburgerBtn.addEventListener('click', function() {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        overlay.addEventListener('click', closeSidebar);

        // Close sidebar when a nav link is clicked (mobile)
        sidebar.querySelectorAll('.nav-link, .nav-dropdown-menu a, .btn-logout').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 991.98) {
                    closeSidebar();
                }
            });
        });

        // Back to top button for all admin pages (scroll container is .main)
        const adminMainContent = document.getElementById('mainContent');
        const adminBackToTop = document.getElementById('adminBackToTop');

        function toggleAdminBackToTop() {
            if (!adminMainContent || !adminBackToTop) return;
            adminBackToTop.classList.toggle('show', adminMainContent.scrollTop > 220);
        }

        if (adminMainContent && adminBackToTop) {
            adminMainContent.addEventListener('scroll', toggleAdminBackToTop);
            adminBackToTop.addEventListener('click', function() {
                adminMainContent.scrollTo({ top: 0, behavior: 'smooth' });
            });
            toggleAdminBackToTop();
        }
    </script>
</body>
</html>