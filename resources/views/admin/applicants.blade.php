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

    .applicant-status {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 50rem;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        margin-top: auto;
        background-color: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    .applicant-search-wrapper {
        position: relative;
        max-width: 480px;
        width: 100%;
        margin-bottom: 20px;
    }

    .applicant-search {
        width: 100%;
        padding: 10px 14px 10px 40px;
        border: 1.5px solid #ddd;
        border-radius: 50rem;
    }

    .grid-message {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px;
        color: #666;
    }

    /* --- Sort / Filter Toolbar --- */
    .applicant-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
        align-items: center;
    }

    .applicant-toolbar .toolbar-group {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .applicant-toolbar .toolbar-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #555;
        letter-spacing: 0.5px;
        margin-right: 4px;
    }

    .applicant-toolbar .sort-btn {
        padding: 6px 16px;
        border: 1.5px solid #ddd;
        border-radius: 50rem;
        background: #fff;
        font-size: 0.8rem;
        font-weight: 600;
        color: #555;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .applicant-toolbar .sort-btn:hover {
        border-color: var(--pcci-red, #be1e38);
        color: var(--pcci-red, #be1e38);
    }

    .applicant-toolbar .sort-btn.active {
        background: var(--pcci-red, #be1e38);
        color: #fff;
        border-color: var(--pcci-red, #be1e38);
    }

    .applicant-toolbar .sort-btn i {
        margin-right: 4px;
    }

    /* --- Search Bar --- */
    .applicant-search-wrapper {
        position: relative;
        max-width: 480px;
        width: 100%;
        margin-bottom: 20px;
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

    .applicant-search::placeholder {
        color: #aaa;
    }

    /* --- Responsive --- */
    @media (max-width: 768px) {
        .applicant-header-banner {
            padding: 36px 24px;
            font-size: 1.5rem;
        }

        .applicant-search-wrapper {
            max-width: 100%;
        }

        .applicant-toolbar {
            gap: 8px;
        }

        .applicant-toolbar .toolbar-group:last-child {
            margin-left: 0;
        }
    }

    @media (max-width: 576px) {
        .applicant-header-banner {
            padding: 24px 20px;
            font-size: 1.3rem;
        }

        .applicant-toolbar {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .applicant-toolbar .toolbar-group {
            flex-wrap: wrap;
        }

        .applicant-toolbar .toolbar-group:last-child {
            margin-left: 0;
        }

        .applicant-search {
            font-size: 0.85rem;
            padding: 9px 12px 9px 36px;
        }
    }
</style>

<div class="applicant-header-banner">Applicants</div>

<div class="applicant-search-wrapper">
    <i class="bi bi-search"></i>
    <input type="text" class="applicant-search" id="applicantSearch" placeholder="Search by name, industry, or ID..." oninput="applyFiltersAndSort()">
</div>

{{-- Fixed: Removed Status filter, set to Pending only --}}
<div class="applicant-toolbar">
    <div class="toolbar-group">
        <button class="sort-btn active" disabled>Pending</button>
    </div>
</div>

<div class="applicant-grid" id="applicantGrid">
    <div class="grid-message"><i class="fa fa-spinner fa-spin me-2"></i> Loading applicants...</div>
</div>

{{-- ======== DYNAMIC FETCH LOGIC ======== --}}
<script>
    let allApplicants = [];
    let nameSortAsc = null;

    function initApplicantsPage() {
        var token = localStorage.getItem('token');

        if (!token) {
            window.location.href = '/login';
            return;
        }

        fetchApplicantsList(token);
    }

    if (document.readyState !== 'loading') {
        initApplicantsPage();
    } else {
        document.addEventListener('DOMContentLoaded', initApplicantsPage);
    }

    async function fetchApplicantsList() {
        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/applicants`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            if (response.ok) {
                allApplicants = result.data || [];
                applyFiltersAndSort();
            }
        } catch (error) {
            document.getElementById('applicantGrid').innerHTML = '<div class="grid-message">Error loading data.</div>';
        }
    }

    function toggleSortName() {
        const btn = document.getElementById('sortNameBtn');

        if (nameSortAsc === null || nameSortAsc === false) {
            nameSortAsc = true;
            btn.classList.add('active');
            btn.innerHTML = '<i class="bi bi-sort-alpha-down"></i> Name A-Z';
        } else {
            nameSortAsc = false;
            btn.innerHTML = '<i class="bi bi-sort-alpha-up"></i> Name Z-A';
        }

        applyFiltersAndSort();
    }

    function applyFiltersAndSort() {
        const query = document.getElementById('applicantSearch').value.toLowerCase().trim();

        // Filter strictly for status 'pending'
        let filtered = allApplicants.filter(app => {
            const status = (app.status || '').toLowerCase();
            const profile = app.basic_profile || {};
            const org = app.organization_membership || {};
            const name = (profile.registered_business_name || '').toLowerCase();
            const industry = (org.type_of_company || '').toLowerCase();

            const matchesStatus = status === 'pending';
            const matchesSearch = name.includes(query) || industry.includes(query);

            return matchesStatus && matchesSearch;
        });

        renderApplicantCards(filtered);
    }

    function renderApplicantCards(applicants) {
        const grid = document.getElementById('applicantGrid');
        grid.innerHTML = '';
        if (applicants.length === 0) {
            grid.innerHTML = '<div class="grid-message">No pending applicants found.</div>';
            return;
        }

        applicants.forEach(app => {
            const profile = app.basic_profile || {};
            const org = app.organization_membership || {};
            grid.insertAdjacentHTML('beforeend', `
                <a href="/applicant/${app.id}" class="applicant-card">
                    <div class="applicant-card-name">${profile.registered_business_name || 'N/A'}</div>
                    <div class="applicant-card-industry">${org.type_of_company || 'N/A'}</div>
                    <div class="applicant-card-id">ID-${String(app.id).padStart(4, '0')}</div>
                    <span class="applicant-status status-pending">PENDING</span>
                </a>
            `);
        });
    }

    document.addEventListener('DOMContentLoaded', fetchApplicantsList);
</script>
@endsection