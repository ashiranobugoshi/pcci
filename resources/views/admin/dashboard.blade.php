@extends('layouts.admin')

@section('title', 'Admin Dashboard - PCCI')

@section('content')
@include('partials.api-config')

<style>
    /* ============================================== */
    /* ALDRIN'S DASHBOARD STATS CSS                   */
    /* ============================================== */
    .dashboard-header-banner {
        background-color: var(--pcci-red, #be1e38);
        color: #fff;
        padding: clamp(20px, 4vw, 36px) clamp(16px, 5vw, 40px);
        border-radius: 10px;
        font-size: clamp(1.25rem, 3.5vw, 2rem);
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: clamp(18px, 3vw, 30px);
        letter-spacing: 1px;
    }

    .dashboard-stats {
        display: flex;
        flex-wrap: wrap;
        gap: clamp(12px, 2vw, 24px);
        margin-bottom: clamp(20px, 4vw, 40px);
    }

    .dash-stat-card {
        border: 2px solid #ff0000;
        border-top: 3px solid var(--pcci-red, #be1e38);
        border-radius: 10px;
        padding: clamp(14px, 2.5vw, 20px) clamp(14px, 3vw, 24px);
        background: #f9f9f9;
        flex: 1 1 220px;
        min-height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
    }

    .dash-stat-card:hover {
        border-color: var(--pcci-red, #be1e38);
        box-shadow: 0 6px 20px rgba(190, 30, 56, 0.1);
        transform: translateY(-2px);
        text-decoration: none;
        color: inherit;
    }

    .dash-stat-card-title {
        font-size: clamp(0.9rem, 2vw, 1rem);
        font-weight: 800;
        text-transform: uppercase;
        color: #111;
        letter-spacing: 0.3px;
    }

    .dash-stat-card-value {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        margin-top: clamp(10px, 2vw, 16px);
    }

    .dash-stat-card-value i {
        color: var(--pcci-red, #be1e38);
        font-size: clamp(1rem, 2.5vw, 1.3rem);
    }

    .dash-stat-card-value .count {
        font-size: clamp(1.2rem, 3vw, 1.5rem);
        font-weight: 700;
        color: #111;
    }

    .count-loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #eee;
        border-top: 3px solid var(--pcci-red, #be1e38);
        border-radius: 50%;
        animation: countSpin 0.8s linear infinite;
    }

    @keyframes countSpin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 768px) {
        .dashboard-stats {
            flex-direction: column;
        }

        .dash-stat-card {
            flex-basis: 100%;
        }
    }

    /* ============================================== */
    /* NOTIFICATION PANEL CSS                         */
    /* ============================================== */
    .notif-card {
        border: 2px solid #eaeaea;
        border-top: 3px solid var(--pcci-red, #be1e38);
        border-radius: 10px;
        background: #ffffff;
    }

    .notif-item {
        transition: background 0.2s;
    }

    .notif-item:hover {
        background-color: #f8f9fa !important;
    }

    .notif-icon-wrapper {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f2f5;
        border: 1px solid #e5e7eb;
    }

    .notif-badge-pill {
        background-color: var(--pcci-red, #be1e38);
        color: white;
    }
</style>

<div class="container-fluid px-0">
    {{-- ======== ALDRIN'S HEADER BANNER ======== --}}
    <div class="dashboard-header-banner">
        Dashboard
    </div>

    <div class="row g-4">
        <div class="col-xl-8 col-lg-7">
            {{-- ======== ALDRIN'S STAT CARDS ======== --}}
            <div class="dashboard-stats">
                <a href="{{ route('members') }}" class="dash-stat-card">
                    <div class="dash-stat-card-title">Members</div>
                    <div class="dash-stat-card-value">
                        <i class="bi bi-people-fill"></i>
                        <span class="count" id="memberCount"><span class="count-loading"></span></span>
                    </div>
                </a>
                <a href="{{ route('applicants') }}" class="dash-stat-card">
                    <div class="dash-stat-card-title">Applicants</div>
                    <div class="dash-stat-card-value">
                        <i class="bi bi-person-fill"></i>
                        <span class="count" id="applicantCount"><span class="count-loading"></span></span>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="notif-card shadow-sm h-100 d-flex flex-column" style="max-height: 500px;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top" style="border-top-left-radius: 8px; border-top-right-radius: 8px;">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa fa-bell me-2" style="color: var(--pcci-red, #be1e38);"></i>Notification</h6>
                    <span class="badge notif-badge-pill rounded-pill px-2" id="adminNotifCount">0 New</span>
                </div>

                <div class="flex-grow-1 overflow-auto" id="adminNotifList" style="min-height: 250px;">
                    <div class="p-5 text-center text-muted">
                        <div class="spinner-border spinner-border-sm text-secondary mb-2" role="status"></div>
                        <p class="small mb-0">Syncing updates...</p>
                    </div>
                </div>

                <div class="p-2 border-top bg-light rounded-bottom d-flex justify-content-between" style="border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                    <button class="btn btn-sm btn-link text-decoration-none fw-bold" style="color: var(--pcci-red, #be1e38);" onclick="openAdminFullModal()">
                        <i class="fa fa-history me-1"></i> Full Log
                    </button>
                    <button class="btn btn-sm btn-link text-success text-decoration-none fw-bold" onclick="markAllAdminRead()">
                        <i class="fa fa-check-double me-1"></i> Mark All Read
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adminFullNotifModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-top: 4px solid var(--pcci-red, #be1e38); border-radius: 10px;">
            <div class="modal-header border-bottom bg-light py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fa fa-list-ul me-2" style="color: var(--pcci-red, #be1e38);"></i>System Activity Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="adminModalNotifList">
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ======== COMBINED JAVASCRIPT ======== --}}
<script>
    var token = localStorage.getItem('token');
    var ADMIN_DASHBOARD_AUTO_REFRESH_MS = 30000;
    var dashboardRefreshTimerId = null;
    var dashboardVisibilityListener = null;
    var baseUrl = window.API_BASE_URL || 'http://127.0.0.1:8000/api';

    // 1. Dashboard Count Engine
    async function refreshDashboardCounts() {
        await Promise.all([
            fetchCount(`${baseUrl}/v1/members`, token, 'memberCount'),
            fetchCount(`${baseUrl}/v1/applicants`, token, 'applicantCount')
        ]);
    }

    async function fetchCount(url, token, elementId) {
        const el = document.getElementById(elementId);
        if (!el) return; // Guard: Element doesn't exist on current page
        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                el.textContent = '0';
                return;
            }
            const data = await response.json();

            let count = 0;
            let items = Array.isArray(data) ? data : (Array.isArray(data.data) ? data.data : []);

            if (elementId === 'applicantCount' && items.length > 0) {
                items = items.filter(app => ['pending', 'approved', 'rejected', 'declined'].includes(String(app.status).toLowerCase()));
                count = items.length;
            } else if (elementId === 'memberCount' && items.length > 0) {
                // ADDED 'inactive' to the included statuses here
                items = items.filter(member => ['paid', 'approved', 'active', 'inactive'].includes(String(member.status).toLowerCase()));
                count = items.length;
            } else {
                count = data.total !== undefined ? data.total : (data.count !== undefined ? data.count : items.length);
            }
            animateCount(el, count);
        } catch (err) {
            el.textContent = '—';
        }
    }

    function animateCount(el, target) {
        let current = 0;
        const duration = 600;
        const steps = 30;
        const increment = target / steps;
        const stepTime = duration / steps;
        el.textContent = '0';
        if (target === 0) return;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = Math.round(current);
        }, stepTime);
    }

    // ==========================================
    // NOTIFICATION ENGINE LOGIC
    // ==========================================

    // Color Theme Helper for Dynamic Status Colors
    function getNotifColorTheme(tone) {
        const t = String(tone || '').toLowerCase();
        if (t.includes('success')) return {
            bg: '#d1e7dd',
            icon: '#0f5132',
            border: '#badbcc'
        }; // Green (Paid)
        if (t.includes('danger')) return {
            bg: '#f8d7da',
            icon: '#842029',
            border: '#f5c2c7'
        }; // Red (Rejected/Cancelled)
        if (t.includes('warning')) return {
            bg: '#fff3cd',
            icon: '#664d03',
            border: '#ffecb5'
        }; // Yellow (inactive)
        if (t.includes('primary') || t.includes('info')) return {
            bg: '#cfe2ff',
            icon: '#084298',
            border: '#b6d4fe'
        }; // Blue (Approved)
        return {
            bg: '#f8f9fa',
            icon: '#6c757d',
            border: '#e9ecef'
        }; // Default Gray
    }

    // Precise Timestamp Formatter
    function formatNotifTime(dateString) {
        const dateObj = new Date(dateString);
        if (isNaN(dateObj.getTime())) return 'Just now';

        return dateObj.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric'
            }) +
            ' at ' +
            dateObj.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
    }

    async function fetchAdminDashboardNotifications() {
        try {
            const response = await fetch(`${baseUrl}/v1/notifications`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) return;
            const data = await response.json();
            const items = data.notifications || [];
            const unreadCount = data.unread_count || 0;

            window.cachedAdminNotifications = items;
            const listContainer = document.getElementById('adminNotifList');
            const badge = document.getElementById('adminNotifCount');

            // Guard: Only update DOM if elements exist on current page
            if (!listContainer || !badge) return;

            if (items.length > 0) {
                listContainer.innerHTML = items.slice(0, 10).map(item => {
                    const payload = item.data;
                    const isUnread = item.read_at === null;
                    const timeStr = formatNotifTime(item.created_at); // Now uses the precise formatter
                    const theme = getNotifColorTheme(payload.tone);

                    return `
                    <div class="notif-item p-3 border-bottom d-flex align-items-start gap-3" 
                         style="cursor: pointer; background: ${isUnread ? '#fffcfc' : '#fff'}; border-left: 4px solid ${theme.icon};"
                         onclick="markAdminRead('${item.id}')">
                        <div class="flex-shrink-0 rounded-circle d-flex justify-content-center align-items-center" 
                             style="width: 42px; height: 42px; background-color: ${theme.bg}; color: ${theme.icon}; border: 1px solid ${theme.border};">
                            <i class="fa ${payload.icon} fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark" style="font-size: 13px;">${payload.title}</span>
                            </div>
                            <p class="text-secondary mb-1" style="font-size: 11px; line-height: 1.4;">${payload.message}</p>
                            <div class="text-muted fw-bold" style="font-size: 10px;">
                                <i class="fa fa-clock me-1"></i>${timeStr}
                            </div>
                        </div>
                    </div>`;
                }).join('');

                badge.innerText = `${unreadCount} New`;
                badge.style.display = unreadCount > 0 ? 'inline-block' : 'none';
            } else {
                listContainer.innerHTML = `<div class="p-5 text-center text-muted"><p class="small fw-bold text-dark mb-0">All caught up!</p></div>`;
                badge.style.display = 'none';
            }
        } catch (err) {
            console.error("Notif Fetch Error:", err);
        }
    }

    async function markAdminRead(id) {
        await fetch(`${baseUrl}/v1/notifications/${id}/read`, {
            method: 'PATCH',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        fetchAdminDashboardNotifications();
    }

    async function markAllAdminRead() {
        await fetch(`${baseUrl}/v1/notifications/read-all`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        fetchAdminDashboardNotifications();
    }

    function openAdminFullModal() {
        const list = document.getElementById('adminModalNotifList');
        if (!list) return; // Guard: Element doesn't exist on current page
        const items = window.cachedAdminNotifications || [];

        list.innerHTML = items.length > 0 ? items.map(item => {
            const timeStr = formatNotifTime(item.created_at);
            const theme = getNotifColorTheme(item.data.tone);

            return `
            <div class="p-4 border-bottom d-flex align-items-start gap-3" style="border-left: 5px solid ${theme.icon};">
                <div class="rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" style="width: 50px; height: 50px; background-color: ${theme.bg}; color: ${theme.icon};">
                    <i class="fa ${item.data.icon} fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 text-dark">${item.data.title}</h6>
                    <p class="text-muted small mb-2">${item.data.message}</p>
                    <span class="badge bg-light text-secondary border fw-normal"><i class="fa fa-clock me-1"></i>${timeStr}</span>
                </div>
            </div>`;
        }).join('') : '<div class="p-5 text-center text-muted">No history available.</div>';

        const modal = document.getElementById('adminFullNotifModal');
        if (modal) {
            new bootstrap.Modal(modal).show();
        }
    }

    function scheduleDashboardRefresh() {
        clearTimeout(dashboardRefreshTimerId);
        dashboardRefreshTimerId = setTimeout(async () => {
            if (document.visibilityState === 'visible') {
                await refreshDashboardCounts();
                await fetchAdminDashboardNotifications();
            }
            scheduleDashboardRefresh();
        }, ADMIN_DASHBOARD_AUTO_REFRESH_MS);
    }

    function initDashboardPage() {
        if (!token) return window.location.href = '/login';

        refreshDashboardCounts();
        fetchAdminDashboardNotifications();

        dashboardVisibilityListener = () => {
            if (document.visibilityState === 'visible') {
                refreshDashboardCounts();
                fetchAdminDashboardNotifications();
            }
        };
        document.addEventListener('visibilitychange', dashboardVisibilityListener);

        scheduleDashboardRefresh();

        window.cleanupCurrentAdminPage = function() {
            clearTimeout(dashboardRefreshTimerId);
            dashboardRefreshTimerId = null;

            if (dashboardVisibilityListener) {
                document.removeEventListener('visibilitychange', dashboardVisibilityListener);
                dashboardVisibilityListener = null;
            }
            delete window.cleanupCurrentAdminPage;
        };
    }

    if (document.readyState !== 'loading') {
        initDashboardPage();
    } else {
        document.addEventListener('DOMContentLoaded', initDashboardPage);
    }
</script>
@endsection