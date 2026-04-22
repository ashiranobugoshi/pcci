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
        border: 2px solid #ff0000; border-top: 3px solid var(--pcci-red, #be1e38);
        border-radius: 10px;
        padding: clamp(14px, 2.5vw, 20px) clamp(14px, 3vw, 24px);
        background: #f9f9f9;
        flex: 1 1 220px;
        min-height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: space-between; text-decoration: none; color: inherit; transition: all 0.2s ease;
    }
    .dash-stat-card:hover { border-color: var(--pcci-red, #be1e38); box-shadow: 0 6px 20px rgba(190, 30, 56, 0.1); transform: translateY(-2px); text-decoration: none; color: inherit;}
    .dash-stat-card-title { font-size: clamp(0.9rem, 2vw, 1rem); font-weight: 800; text-transform: uppercase; color: #111; letter-spacing: 0.3px; }
    .dash-stat-card-value { display: flex; align-items: center; justify-content: flex-end; gap: 8px; margin-top: clamp(10px, 2vw, 16px); }
    .dash-stat-card-value i { color: var(--pcci-red, #be1e38); font-size: clamp(1rem, 2.5vw, 1.3rem); }
    .dash-stat-card-value .count { font-size: clamp(1.2rem, 3vw, 1.5rem); font-weight: 700; color: #111; }
    .count-loading {
        display: inline-block; width: 20px; height: 20px; border: 3px solid #eee;
        border-top: 3px solid var(--pcci-red, #be1e38); border-radius: 50%; animation: countSpin 0.8s linear infinite;
    }
    @keyframes countSpin { to { transform: rotate(360deg); } }

    @media (max-width: 768px) {
        .dashboard-stats {
            flex-direction: column;
        }

        .dash-stat-card {
            flex-basis: 100%;
        }
    }

</style>

{{-- ======== ALDRIN'S HEADER BANNER ======== --}}
<div class="dashboard-header-banner">
    Dashboard
</div>

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

{{-- ======== COMBINED JAVASCRIPT ======== --}}
<script>
    const token = localStorage.getItem('token');
    const ADMIN_DASHBOARD_AUTO_REFRESH_MS = 15000;

    function refreshDashboardCounts() {
        fetchCount('/api/v1/members', token, 'memberCount');
        fetchCount(`${window.API_BASE_URL}/v1/applicants`, token, 'applicantCount');
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (!token) {
            window.location.href = '/login';
            return;
        }

        refreshDashboardCounts();

        setInterval(() => {
            if (document.visibilityState !== 'visible') return;
            refreshDashboardCounts();
        }, ADMIN_DASHBOARD_AUTO_REFRESH_MS);
    });

    async function fetchCount(url, token, elementId) {
        const el = document.getElementById(elementId);
        try {
            const headers = { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` };
            const response = await fetch(url, { headers });
            const data = await response.json();

            if (response.ok) {
                let count = 0;
                if (Array.isArray(data)) count = data.length;
                else if (data.data && Array.isArray(data.data)) count = data.data.length;
                else if (data.total !== undefined) count = data.total;
                else if (data.count !== undefined) count = data.count;
                animateCount(el, count);
            } else { el.textContent = '0'; }
        } catch (err) { el.textContent = '—'; }
    }

    function animateCount(el, target) {
        let current = 0; const duration = 600; const steps = 30;
        const increment = target / steps; const stepTime = duration / steps;
        el.textContent = '0';
        if (target === 0) return;
        const timer = setInterval(function() {
            current += increment;
            if (current >= target) { current = target; clearInterval(timer); }
            el.textContent = Math.round(current);
        }, stepTime);
    }

    function logout() { localStorage.removeItem('token'); window.location.href = '/login'; }
</script>
@endsection