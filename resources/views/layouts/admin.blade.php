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

    <script>
        const savedTheme = localStorage.getItem('admin-theme') || 'light';
        if (savedTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>

    <style>
        /* === GLOBAL THEME VARIABLES === */
        :root {
            --pcci-red: #be1e38;
            --pcci-light-red: #e35d5d;
            --sidebar-width: 280px;

            /* Light Theme Colors */
            --bg-main: #f4f6f9;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --text-main: #333333;
            --text-muted: #888888;
            --border-color: #e0e0e0;
            --hover-bg: #fff1f3;
            --input-bg: #ffffff;
            --input-border: #ddd;
            --table-header: #f8f8f8;
            --table-hover: #fdf2f4;
        }

        /* Dark Theme Colors */
        [data-theme="dark"] {
            --bg-main: #1a1c23;
            --bg-sidebar: #252836;
            --bg-card: #2b2d3c;
            --text-main: #e2e8f0;
            --text-muted: #a0aec0;
            --border-color: #3f4252;
            --hover-bg: #323545;
            --input-bg: #1f222e;
            --input-border: #4a4d61;
            --table-header: #323545;
            --table-hover: #3a3f50;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            display: flex;
            height: 100vh;
            background-color: var(--bg-main);
            color: var(--text-main);
            overflow: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 20px 0;
            flex-shrink: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1050;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 10px;
        }

        .admin-profile {
            padding: 0 25px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid var(--border-color);
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s;
        }

        .admin-profile:hover {
            background-color: var(--hover-bg);
        }

        .avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: 2px solid var(--pcci-red);
            object-fit: cover;
        }

        .admin-info span {
            display: block;
        }

        .role {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
        }

        .name {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--pcci-red);
        }

        .menu-label {
            padding: 25px 25px 10px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        /* Nav Links */
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            text-decoration: none;
            color: var(--text-main);
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
            background-color: var(--hover-bg);
            color: var(--pcci-red);
        }

        /* Content Dropdown */
        .nav-dropdown {
            position: relative;
        }

        .nav-dropdown-toggle {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            text-decoration: none;
            color: var(--text-main);
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
            background-color: var(--hover-bg);
            color: var(--pcci-red);
        }

        .nav-dropdown-toggle .chevron {
            margin-left: auto;
            font-size: 0.75rem;
            transition: transform 0.3s ease;
        }

        .nav-dropdown-toggle.open .chevron {
            transform: rotate(180deg);
        }

        .nav-dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
            background: var(--bg-sidebar);
        }

        .nav-dropdown-menu.open {
            max-height: 300px;
        }

        .nav-dropdown-menu a {
            display: flex;
            align-items: center;
            padding: 10px 25px 10px 62px;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 500;
            font-size: 0.88rem;
            gap: 10px;
            transition: 0.2s;
            position: relative;
        }

        .nav-dropdown-menu a::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--border-color);
            position: absolute;
            left: 42px;
            transition: background 0.2s;
        }

        .nav-dropdown-menu a:hover {
            background-color: var(--hover-bg);
            color: var(--pcci-red);
        }

        .nav-dropdown-menu a:hover::before {
            background: var(--pcci-red);
        }

        .nav-dropdown-menu a.active {
            color: var(--pcci-red);
            font-weight: 700;
            background: var(--hover-bg);
        }

        .nav-dropdown-menu a.active::before {
            background: var(--pcci-red);
        }

        /* Logout */
        .logout-box {
            padding: 20px;
            margin-top: auto;
        }

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

        .btn-logout:hover {
            background-color: var(--pcci-red);
        }

        /* MAIN CONTENT */
        .main {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            background-color: var(--bg-main);
            position: relative;
            transition: background-color 0.3s ease;
        }

        .admin-content-shell {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px clamp(16px, 2.5vw, 28px) 32px;
            box-sizing: border-box;
        }

        /* =========================================================
           AGGRESSIVE GLOBAL DARK MODE OVERRIDES FOR ALL CHILD PAGES 
           ========================================================= */
        [data-theme="dark"] body,
        [data-theme="dark"] .main,
        [data-theme="dark"] .admin-content-shell {
            background-color: var(--bg-main) !important;
            color: var(--text-main) !important;
        }

        [data-theme="dark"] .card,
        [data-theme="dark"] .modal-box,
        [data-theme="dark"] .modal-content,
        [data-theme="dark"] .settings-card,
        [data-theme="dark"] .toolbar,
        [data-theme="dark"] .users-table-wrapper,
        [data-theme="dark"] .box,
        [data-theme="dark"] .panel {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            color: var(--text-main) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
        }

        /* Force inputs to turn dark */
        [data-theme="dark"] input,
        [data-theme="dark"] select,
        [data-theme="dark"] textarea,
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select,
        [data-theme="dark"] .search-box input {
            background-color: var(--input-bg) !important;
            color: var(--text-main) !important;
            border-color: var(--input-border) !important;
        }

        /* === TABLE OVERRIDES (FIXED FOR READABILITY) === */
        [data-theme="dark"] table,
        [data-theme="dark"] .users-table,
        [data-theme="dark"] .table {
            color: var(--text-main) !important;
            border-color: var(--border-color) !important;
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-main);
            --bs-table-striped-color: var(--text-main);
            --bs-table-hover-color: var(--text-main);
        }

        [data-theme="dark"] th,
        [data-theme="dark"] thead,
        [data-theme="dark"] .table th {
            background-color: var(--table-header) !important;
            color: var(--text-main) !important;
            border-bottom: 2px solid var(--border-color) !important;
        }

        /* Forces text inside table cells to be white/light grey */
        [data-theme="dark"] td,
        [data-theme="dark"] .table td,
        [data-theme="dark"] table tbody tr td {
            border-bottom: 1px solid var(--border-color) !important;
            background-color: transparent !important;
            color: var(--text-main) !important;
        }

        [data-theme="dark"] tr:hover td,
        [data-theme="dark"] .table-hover tbody tr:hover td {
            background-color: var(--table-hover) !important;
            color: var(--text-main) !important;
        }

        /* Force Pagination links to turn dark */
        [data-theme="dark"] .pagination .page-link {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            color: var(--text-main) !important;
        }

        [data-theme="dark"] .pagination .page-item.active .page-link {
            background-color: var(--pcci-red) !important;
            border-color: var(--pcci-red) !important;
            color: #fff !important;
        }

        [data-theme="dark"] .pagination .page-item.disabled .page-link {
            background-color: var(--table-header) !important;
            color: var(--text-muted) !important;
        }

        /* Force text to stay readable */
        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6 {
            color: var(--text-main) !important;
        }

        [data-theme="dark"] .text-muted,
        [data-theme="dark"] p {
            color: var(--text-muted) !important;
        }

        [data-theme="dark"] .page-header {
            color: #fff !important;
            /* Keep the red header text white */
        }


        /* MOBILE HAMBURGER BUTTON & OVERLAY */
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

        .hamburger-btn:hover {
            background: #9a182d;
        }

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

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

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

        @media (max-width: 991.98px) {
            .hamburger-btn {
                display: flex;
            }

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
                box-shadow: 5px 0 25px rgba(0, 0, 0, 0.5);
            }

            .main {
                width: 100%;
            }

            .admin-content-shell> :first-child {
                padding-top: 75px !important;
            }
        }

        @media (max-width: 575.98px) {
            .sidebar {
                width: 280px;
            }

            .hamburger-btn {
                top: 10px;
                left: 10px;
                width: 40px;
                height: 40px;
                font-size: 1.25rem;
            }

            .back-to-top-btn {
                right: 14px;
                bottom: 14px;
                width: 42px;
                height: 42px;
            }
        }
    </style>

    @include('layouts.admin-title-style')
</head>

<body>

    <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle sidebar">
        <i class="bi bi-list" id="hamburgerIcon"></i>
    </button>

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
        const pageCache = new Map();
        const pageDomCache = new Map();
        const pageTitleCache = new Map();
        const cacheStorageKey = 'adminPageCache';
        const cacheVersionKey = 'adminPageCacheVersion';
        const pageCacheVersion = 2;
        const adminMainContent = document.getElementById('mainContent');
        const adminBackToTop = document.getElementById('adminBackToTop');
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const hamburgerIcon = document.getElementById('hamburgerIcon');
        const contentDropdownToggle = document.getElementById('contentDropdownToggle');
        const contentDropdownMenu = document.getElementById('contentDropdownMenu');
        const toggle = contentDropdownToggle;
        const menu = contentDropdownMenu;

        function parsePageResponse(html) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const titleTag = doc.querySelector('title');
            const contentShell = doc.querySelector('.admin-content-shell');
            return {
                content: contentShell ? contentShell.innerHTML : doc.body.innerHTML,
                title: titleTag ? titleTag.textContent : document.title
            };
        }

        function loadCacheFromStorage() {
            try {
                const storedVersion = Number(sessionStorage.getItem(cacheVersionKey));
                if (storedVersion !== pageCacheVersion) {
                    sessionStorage.removeItem(cacheStorageKey);
                    sessionStorage.setItem(cacheVersionKey, String(pageCacheVersion));
                    return;
                }

                const cached = sessionStorage.getItem(cacheStorageKey);
                if (cached) {
                    const parsed = JSON.parse(cached);
                    Object.entries(parsed).forEach(([key, html]) => pageCache.set(key, html));
                }
            } catch (error) {
                console.warn('Unable to restore admin page cache:', error);
            }
        }

        function preserveCurrentPageDom(pageKey) {
            if (!pageKey || !adminMainContent || pageDomCache.has(pageKey)) return;

            const pageSnapshot = document.createElement('div');
            while (adminMainContent.firstChild) {
                pageSnapshot.appendChild(adminMainContent.firstChild);
            }

            if (pageSnapshot.childNodes.length > 0) {
                pageDomCache.set(pageKey, pageSnapshot);
                pageTitleCache.set(pageKey, document.title);
            }
        }

        function restoreCachedPage(pageKey) {
            if (!pageKey || !adminMainContent || !pageDomCache.has(pageKey)) return false;
            const cachedPage = pageDomCache.get(pageKey);
            if (!cachedPage) return false;

            adminMainContent.innerHTML = '';
            adminMainContent.appendChild(cachedPage);
            document.title = pageTitleCache.get(pageKey) || document.title;
            currentPageKey = pageKey;
            return true;
        }

        let executedScriptKeys = new Set();
        let currentPageKey = null;

        function saveCacheToStorage() {
            try {
                sessionStorage.setItem(cacheVersionKey, String(pageCacheVersion));
                sessionStorage.setItem(cacheStorageKey, JSON.stringify(Object.fromEntries(pageCache)));
            } catch (error) {
                console.warn('Unable to persist admin page cache:', error);
            }
        }

        function runScripts(scriptNodes, pageKey) {
            if (pageKey !== currentPageKey) {
                executedScriptKeys.clear();
                currentPageKey = pageKey;
            }

            scriptNodes.forEach((oldScript) => {
                try {
                    const scriptKey = oldScript.src || `inline:${oldScript.textContent}`;
                    if (executedScriptKeys.has(scriptKey)) {
                        return;
                    }

                    if (oldScript.src) {
                        // Avoid loading the same external script twice
                        if (document.querySelector(`script[src="${oldScript.src}"]`)) {
                            return;
                        }
                    }

                    const newScript = document.createElement('script');
                    if (oldScript.src) {
                        newScript.src = oldScript.src;
                        newScript.async = false;
                    } else {
                        let scriptContent = oldScript.textContent;

                        // Replace const and let declarations with var to allow redeclaration
                        // This prevents "already declared" errors on page reloads
                        scriptContent = scriptContent.replace(/\b(const|let)\s+([a-zA-Z_$][a-zA-Z0-9_$]*)\s*=/g, (match, keyword, varName) => {
                            return `var ${varName} =`;
                        });
                        newScript.textContent = scriptContent;
                    }

                    executedScriptKeys.add(scriptKey);
                    adminMainContent.appendChild(newScript);
                } catch (err) {
                    console.error('Error processing script:', err);
                }
            });
        }

        function setPageContent(contentHTML, pageTitle, pageKey) {
            if (typeof window.cleanupCurrentAdminPage === 'function') {
                try {
                    window.cleanupCurrentAdminPage();
                } catch (err) {
                    console.error('Error cleaning up admin page:', err);
                }
                delete window.cleanupCurrentAdminPage;
            }

            if (!adminMainContent) return;
            const temp = document.createElement('div');
            temp.innerHTML = '<div class="admin-content-shell">' + contentHTML + '</div>';

            const scripts = Array.from(temp.querySelectorAll('script'));
            scripts.forEach(script => script.remove());

            adminMainContent.innerHTML = temp.innerHTML;
            document.title = pageTitle;
            runScripts(scripts, pageKey);
            adminMainContent.scrollTo({
                top: 0,
                behavior: 'auto'
            });
            toggleAdminBackToTop();
        }

        function getCacheKey(url) {
            return new URL(url, window.location.origin).href;
        }

        function updateActiveLinkStates(url) {
            if (!sidebar) return;
            const absoluteUrl = new URL(url, window.location.origin).href;
            const currentPath = new URL(absoluteUrl).pathname;
            const links = sidebar.querySelectorAll('a.nav-link, .nav-dropdown-menu a');
            let dropdownActive = false;

            links.forEach(link => {
                const linkPath = new URL(link.href, window.location.origin).pathname;
                const isActive = linkPath === currentPath;
                link.classList.toggle('active', isActive);
                if (isActive && link.closest('.nav-dropdown-menu')) {
                    dropdownActive = true;
                }
            });

            if (contentDropdownToggle) {
                contentDropdownToggle.classList.toggle('active', dropdownActive);
                contentDropdownToggle.classList.toggle('open', dropdownActive);
            }
            if (contentDropdownMenu) {
                contentDropdownMenu.classList.toggle('open', dropdownActive);
            }
        }

        async function loadPage(url, pushState = true) {
            if (!url || url.startsWith('mailto:') || url.startsWith('tel:')) return;
            const absoluteUrl = new URL(url, window.location.origin).href;
            if (absoluteUrl === window.location.href) return;

            const cacheKey = getCacheKey(absoluteUrl);
            const currentUrl = getCacheKey(window.location.href);

            if (currentUrl !== cacheKey) {
                preserveCurrentPageDom(currentUrl);
            }

            if (pageDomCache.has(cacheKey)) {
                restoreCachedPage(cacheKey);
                updateActiveLinkStates(absoluteUrl);
                if (pushState) {
                    history.pushState({
                        url: absoluteUrl
                    }, document.title, absoluteUrl);
                }
                return;
            }

            let pageHTML = pageCache.get(cacheKey);

            if (!pageHTML) {
                try {
                    const response = await fetch(absoluteUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        },
                        cache: 'force-cache'
                    });

                    if (!response.ok) throw new Error(`Failed to fetch ${absoluteUrl}: ${response.status}`);
                    pageHTML = await response.text();
                    pageCache.set(cacheKey, pageHTML);
                    saveCacheToStorage();
                } catch (error) {
                    console.error('Seamless page load failed, falling back to full navigation:', error);
                    window.location.href = absoluteUrl;
                    return;
                }
            }

            const {
                content,
                title
            } = parsePageResponse(pageHTML);
            setPageContent(content, title, cacheKey);
            updateActiveLinkStates(absoluteUrl);

            if (pushState) {
                history.pushState({
                    url: absoluteUrl
                }, title, absoluteUrl);
            }
        }

        function closeSidebar() {
            if (!sidebar || !overlay || !hamburgerIcon) return;
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            hamburgerIcon.classList.replace('bi-x-lg', 'bi-list');
            document.body.style.overflow = '';
        }

        function openSidebar() {
            if (!sidebar || !overlay || !hamburgerIcon) return;
            sidebar.classList.add('open');
            overlay.classList.add('active');
            hamburgerIcon.classList.replace('bi-list', 'bi-x-lg');
            document.body.style.overflow = 'hidden';
        }

        function handleLinkClick(event) {
            const link = event.target.closest('a[href]');
            if (!link) return;
            if (link.target && link.target !== '_self') return;
            if (link.closest('.logout-box')) return;
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0) return;
            const href = link.href;
            if (!href || link.origin !== window.location.origin) return;
            event.preventDefault();
            loadPage(href);
            if (window.innerWidth <= 991.98 && sidebar && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        }

        function toggleAdminBackToTop() {
            if (!adminMainContent || !adminBackToTop) return;
            adminBackToTop.classList.toggle('show', adminMainContent.scrollTop > 220);
        }

        function handleLogout(event) {
            if (event) event.preventDefault();

            try {
                const token = localStorage.getItem('token');

                let secureApiUrl = window.API_BASE_URL;
                if (secureApiUrl.includes('onrender.com') && secureApiUrl.startsWith('http://')) {
                    secureApiUrl = secureApiUrl.replace('http://', 'https://');
                }

                if (token) {
                    fetch(`${secureApiUrl}/v1/logout`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    }).catch(error => console.error('Error during API logout:', error));
                }
            } catch (error) {
                console.error('Error during API logout:', error);
            } finally {
                localStorage.removeItem('token');
                localStorage.removeItem('role');
                localStorage.removeItem('userName');
                localStorage.removeItem('userEmail');

                window.location.href = '/login';
            }
        }

        if (toggle && menu) {
            toggle.addEventListener('click', function() {
                this.classList.toggle('open');
                menu.classList.toggle('open');
            });
        }

        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', function() {
                if (sidebar && sidebar.classList.contains('open')) closeSidebar();
                else openSidebar();
            });
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        if (sidebar) {
            sidebar.querySelectorAll('.nav-link, .nav-dropdown-menu a').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 991.98) closeSidebar();
                });
            });
        }

        if (adminMainContent && adminBackToTop) {
            adminMainContent.addEventListener('scroll', toggleAdminBackToTop);
            adminBackToTop.addEventListener('click', function() {
                adminMainContent.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            toggleAdminBackToTop();
        }

        loadCacheFromStorage();
        if (adminMainContent) {
            const currentUrl = getCacheKey(window.location.href);
            pageCache.set(currentUrl, '<div class="admin-content-shell">' + adminMainContent.innerHTML + '</div>');
            saveCacheToStorage();
        }

        updateActiveLinkStates(window.location.href);
        document.body.addEventListener('click', handleLinkClick);

        window.addEventListener('popstate', function(event) {
            const url = event.state && event.state.url ? event.state.url : window.location.href;
            loadPage(url, false);
        });
    </script>
</body>

</html>