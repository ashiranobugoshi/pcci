@extends('layouts.admin')

@section('title', 'Applicants - PCCI')

@section('content')
@include('partials.api-config')

<style>
    /* ============================================== */
    /* APPLICANTS LISTING PAGE                        */
    /* ============================================== */

    /* --- Standard Header Banner --- */
    .applicant-header-banner {
        background-color: var(--pcci-red, #be1e38);
        color: #fff;
        padding: 36px 40px;
        border-radius: 10px;
        font-size: 2rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 30px;
        letter-spacing: 1px;
    }

    .applicant-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 16px;
    }

    .applicant-card {
        border: 1.5px solid #eee;
        border-top: 3px solid var(--pcci-red, #be1e38);
        border-radius: 8px;
        padding: 20px 16px;
        background: #fff;
        text-align: center;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .applicant-card:hover {
        border-color: var(--pcci-red, #be1e38);
        box-shadow: 0 6px 16px rgba(190, 30, 56, 0.12);
        transform: translateY(-3px);
    }

    .applicant-card-name {
        font-size: 1.05rem;
        font-weight: 800;
        color: #111;
        text-transform: uppercase;
        margin-bottom: 6px;
        width: 100%;
        word-wrap: break-word;
        line-height: 1.3;
    }

    .applicant-card-industry {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 12px;
        width: 100%;
        word-wrap: break-word;
        line-height: 1.4;
    }

    .applicant-card-id {
        font-size: 0.75rem;
        color: #888;
        font-family: monospace;
        background: #f8f9fa;
        padding: 4px 10px;
        border-radius: 6px;
        margin-bottom: 12px;
    }

    .grid-message {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px;
        color: #666;
    }

    /* --- Toolbar & Search Bar --- */
    .applicant-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
        align-items: center;
    }

    .applicant-search-wrapper {
        position: relative;
        max-width: 480px;
        width: 100%;
        flex: 1;
    }

    .applicant-search-wrapper i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 0.95rem;
        pointer-events: none;
    }

    .applicant-search {
        width: 100%;
        padding: 10px 14px 10px 40px;
        border: 1.5px solid #ddd;
        border-radius: 50rem;
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        background: #fff;
        box-sizing: border-box;
    }

    .applicant-search:focus {
        border-color: var(--pcci-red, #be1e38);
        box-shadow: 0 0 0 3px rgba(190, 30, 56, 0.1);
    }
</style>

<div class="applicant-header-banner">Applicants</div>

<div class="applicant-toolbar">
    <div class="applicant-search-wrapper">
        <i class="fa fa-search"></i>
        <input type="text" class="applicant-search" id="applicantSearch" placeholder="Search businesses..." oninput="filterApplicants()">
    </div>

    <select id="applicantStatusFilter" class="form-select form-select-sm text-muted fw-bold" style="height: 44px; border-radius: 50rem; border-color: #ddd; font-size: 14px; box-shadow: none; cursor:pointer; width: 140px; background-color: #fff;" onchange="filterApplicants()">
        <option value="pending" selected>Pending</option>
        <option value="rejected">Rejected</option>
    </select>
</div>

<div class="applicant-grid" id="applicantGrid">
    <div class="grid-message"><i class="fa fa-spinner fa-spin me-2"></i> Loading applicants...</div>
</div>

<script>
    // Changed to 'var' to prevent SPA routing crashes when switching tabs!
    var allApplicants = [];

    function initApplicantsPage() {
        var token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '/login';
            return;
        }
        fetchApplicantsList(token);
    }

    async function fetchApplicantsList(token) {
        const grid = document.getElementById('applicantGrid');
        if (grid) grid.innerHTML = '<div class="grid-message w-100 text-center py-5"><i class="fa fa-spinner fa-spin me-2"></i> Loading applicants...</div>';

        try {
            const apiBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
            const response = await fetch(`${apiBase}/v1/applicants`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (response.ok) {
                // Safely assign data to the master array
                allApplicants = result.data || [];
                // Run the filter immediately to render the default view
                filterApplicants();
            } else {
                if (grid) grid.innerHTML = '<div class="grid-message text-danger w-100 text-center py-5">Error loading data.</div>';
            }
        } catch (error) {
            if (grid) grid.innerHTML = '<div class="grid-message text-danger w-100 text-center py-5">Network error. Failed to load.</div>';
        }
    }

    // Unified filtering function that respects both the search bar and the dropdown
    function filterApplicants() {
        const query = document.getElementById('applicantSearch')?.value.toLowerCase().trim() || '';
        const statusFilter = document.getElementById('applicantStatusFilter')?.value.toLowerCase() || 'pending';

        const filtered = allApplicants.filter(app => {
            const profile = app.basic_profile || {};
            const org = app.organization_membership || {};
            const status = String(app.status || 'pending').toLowerCase();

            const name = (profile.registered_business_name || '').toLowerCase();
            const industry = (org.type_of_company || '').toLowerCase();

            // Match exact status (pending vs rejected)
            const matchesStatus = status === statusFilter;
            // Match search text against business name or industry
            const matchesSearch = name.includes(query) || industry.includes(query);

            return matchesStatus && matchesSearch;
        });

        renderApplicantCards(filtered);
    }

    function renderApplicantCards(applicants) {
        const grid = document.getElementById('applicantGrid');
        if (!grid) return;

        grid.innerHTML = '';
        if (applicants.length === 0) {
            const statusFilter = document.getElementById('applicantStatusFilter')?.value || 'pending';
            grid.innerHTML = `<div class="grid-message w-100 text-center py-5 text-muted">No ${statusFilter} applicants found.</div>`;
            return;
        }

        applicants.forEach(app => {
            const profile = app.basic_profile || {};
            const org = app.organization_membership || {};
            const status = String(app.status || 'pending').toLowerCase();

            // Dynamic Badges based on status
            let statusBadge = `<span style="background:#fef3c7; color:#b45309; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">PENDING</span>`;
            if (status === 'rejected') {
                statusBadge = `<span style="background:#fee2e2; color:#b91c1c; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">REJECTED</span>`;
            }

            // Saves the clicked ID to localStorage so the profile page loads it instantly
            grid.insertAdjacentHTML('beforeend', `
                <a href="/applicant/${app.id}" onclick="localStorage.setItem('viewingApplicantId', '${app.id}')" class="applicant-card" style="border: 1.5px solid #eee; border-top: 3px solid var(--pcci-red, #be1e38); border-radius: 8px; padding: 20px 16px; background: #fff; text-align: center; text-decoration: none; color: inherit; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <div class="applicant-card-name" style="font-weight: 700; font-size: 15px; color: #111; margin-bottom: 4px;">${profile.registered_business_name || 'N/A'}</div>
                    <div class="applicant-card-industry" style="font-size: 13px; color: #6b7280; margin-bottom: 12px;">${org.type_of_company || 'N/A'}</div>
                    <div class="applicant-card-id" style="font-size: 11px; color: #9ca3af; font-family: monospace; margin-bottom: 12px;">ID-${String(app.id).padStart(4, '0')}</div>
                    ${statusBadge}
                </a>
            `);
        });
    }

    // Initialize fetching when the page loads
    if (document.readyState !== 'loading') {
        initApplicantsPage();
    } else {
        document.addEventListener('DOMContentLoaded', initApplicantsPage);
    }
</script>
@endsection