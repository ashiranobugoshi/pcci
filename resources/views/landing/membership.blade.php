@extends('layouts.app')
@include('partials.api-config')

@section('content')

{{-- THIS IS THE MAGIC LINE THAT FIXES "UNDEFINED" --}}
@include('partials.api-config')

{{-- HERO SECTION --}}
<div class="w-100 mb-0 d-flex flex-column align-items-center" ... >

{{-- HERO SECTION --}}
<div class="w-100 mb-0 d-flex flex-column align-items-center" style="height: 623px; margin-top: -1px; background-color: var(--bg-hero); padding-top: 130px; transition: background-color 0.3s ease;">
    <div class="container d-flex flex-column align-items-center text-center">
        <span class="mb-3 d-block" style="color: #ffffff !important; font-family: 'DM Sans', sans-serif; font-weight: 900; font-size: 24px; text-transform: uppercase;">JOIN PCCI - VALENZUELA</span>
        <h1 class="headline-text fw-bold mb-4 text-uppercase" style="color: #ffffff !important; font-family: 'DM Sans', sans-serif; font-size: 63px;">
            Discover Local <span style="color: #EB3223;">Businesses</span>
        </h1>
        <p style="color: #ffffff !important; font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 24px; max-width: 1262px; margin-bottom: 21px;">
            Connect with our diverse community of innovative businesses and entrepreneurs driving economic growth and excellence in Valenzuela City.
        </p>
        <div style="width: 782px; max-width: 90%;">
            <div class="input-group shadow-sm rounded overflow-hidden border-0 align-items-center" style="height: 62px; background-color: var(--bg-input);">
                <span class="input-group-text border-0 ps-4" style="background-color: transparent;"><i class="bi bi-search text-secondary"></i></span>
                <input type="text" class="form-control border-0 shadow-none text-secondary" style="background-color: transparent;" placeholder="Search businesses, services..." id="searchInput">
                <button class="btn text-white px-5 fw-bold text-uppercase" style="background-color: #D40032; margin-right: 13px; height: 40px; border-radius: 6px;" onclick="handleSearch()">Search</button>
            </div>
        </div>
    </div>
</div>

{{-- Filters & Listing Section --}}
<div class="py-5" style="background-color: var(--bg-section); transition: background-color 0.3s ease;">
    <div class="w-100 mb-5" style="border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
        <div class="container px-4 px-lg-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 py-3">
                
                <div class="d-flex flex-wrap gap-2">
                    <select class="form-select form-select-sm border-0 shadow-sm" style="width: auto; background-color: var(--bg-input); color: var(--text-main);">
                        <option selected>All Categories</option>
                    </select>

                    <select class="form-select form-select-sm border-0 shadow-sm" style="width: auto; background-color: var(--bg-input); color: var(--text-main);">
                        <option selected>All Locations</option>
                    </select>

                    <select class="form-select form-select-sm border-0 shadow-sm" style="width: auto; background-color: var(--bg-input); color: var(--text-main);" id="sortSelect" onchange="handleSort()">
                        <option value="asc">Sort: A-Z</option>
                        <option value="desc">Sort: Z-A</option>
                    </select>
                </div>

                <div class="small fw-medium" style="color: var(--text-muted); font-family: 'DM Sans', sans-serif;" id="showingText">
                    Loading results...
                </div>
            </div>
        </div>
    </div>

    {{-- CARD GRID --}}
    <div class="container px-4 px-lg-5">
        <div class="row g-4" id="businessGrid">
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-arrow-repeat" style="display:inline-block; animation: spin 1s linear infinite; font-size: 2rem;"></i>
                <p class="mt-2">Fetching local businesses...</p>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-5 d-flex justify-content-center">
            <nav aria-label="Page navigation">
                <ul class="pagination" id="paginationContainer"></ul>
            </nav>
        </div>
    </div>
</div>

{{-- ======== DYNAMIC FETCH LOGIC ======== --}}
<script>
    let allBusinesses = [];
    let masterBusinesses = []; // unfiltered copy
    let currentPage = 1;
    const perPage = 12;

    document.addEventListener('DOMContentLoaded', function() {
        fetchBusinesses();

        // Allow pressing Enter in the search input
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') handleSearch();
        });
    });

    async function fetchBusinesses() {
        try {
            const token = localStorage.getItem('token');
            const headers = { 'Accept': 'application/json' };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const response = await fetch(`${window.API_BASE_URL}/v1/business`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) throw new Error("Failed to fetch");

            const result = await response.json();
            
            let rawData = result.data || result || [];

            // === FIX 1: Attach the permanent original database index ===
            masterBusinesses = rawData.map((biz, index) => {
                return { ...biz, original_index: index };
            });

            allBusinesses = [...masterBusinesses];

            sortData('asc');
            renderPage(1);

        } catch (error) {
            console.error('Error fetching businesses:', error);
            document.getElementById('businessGrid').innerHTML = `
                <div class="col-12 text-center py-5" style="color: #D40032;">
                    <h5><i class="bi bi-exclamation-triangle"></i> Failed to load businesses.</h5>
                    <p>Make sure your server is running and connected to the internet!</p>
                </div>
            `;
            document.getElementById('showingText').innerText = "0 results";
        }
    }

    function renderPage(page) {
        currentPage = page;
        const totalResults = allBusinesses.length;
        const totalPages = Math.ceil(totalResults / perPage);
        
        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const offset = (currentPage - 1) * perPage;
        const pagedData = allBusinesses.slice(offset, offset + perPage);

        const showingStart = totalResults === 0 ? 0 : offset + 1;
        const showingEnd = Math.min(offset + perPage, totalResults);
        document.getElementById('showingText').innerText = `Showing ${showingStart}-${showingEnd} of ${totalResults} results`;

        const grid = document.getElementById('businessGrid');
        grid.innerHTML = '';

        if (totalResults === 0) {
            grid.innerHTML = '<div class="col-12 text-center text-muted py-5">No businesses found.</div>';
            renderPagination(0);
            return;
        }

        pagedData.forEach((biz) => {
            const name = biz.registered_business_name || 'Unknown Business';
            const email = biz.email || 'N/A';
            const phone = biz.telephone_no || 'N/A';
            const industry = biz.industry || 'Business';
            const tagline = biz.business_tagline || '';
            const tags = (Array.isArray(biz.tags)) ? biz.tags : [];
            
            // === FIX 2: Use the permanent index for the URL instead of the sorted position ===
            const profileUrl = `/business/${biz.original_index}`;

            let avatarHTML = '';
            if (biz.photo_url && !biz.photo_url.includes('N/A') && !biz.photo_url.includes('null')) {
                avatarHTML = `<img src="${biz.photo_url}" alt="${name}" style="width: 56px; height: 56px; object-fit: cover;" class="rounded-circle shadow-sm">`;
            } else {
                const words = name.split(' ');
                let initials = name.substring(0, 2).toUpperCase();
                if (words.length > 1) initials = (words[0][0] + words[1][0]).toUpperCase();
                const colors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-info', 'bg-danger'];
                // Use the permanent index to keep colors consistent
                const colorIndex = biz.original_index % colors.length;
                avatarHTML = `<div class="rounded-circle ${colors[colorIndex]} d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 56px; height: 56px; font-size: 1.2rem;">${initials}</div>`;
            }

            const cardHTML = `
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow p-3" style="border-radius: 12px; background-color: var(--bg-card);">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            ${avatarHTML}
                            <div style="width: calc(100% - 70px);">
                                <span class="d-inline-block rounded px-2 py-1 mb-1 fw-bold text-uppercase text-truncate" style="font-size: 0.65rem; background-color: #fdf2f2; color: #be1e38; max-width: 100%;">
                                    ${industry}
                                </span>
                                <h5 class="fw-bold mb-1 text-truncate" style="color: var(--text-main);" title="${name}">${name}</h5>
                                ${tagline ? `<small class="text-truncate d-block" style="color: #888; font-style: italic; font-size: 0.8rem;">"${tagline}"</small>` : ''}
                            </div>
                        </div>
                        <div class="card-body p-0 d-flex flex-column flex-grow-1">
                            <div class="mb-3">
                                <div class="d-flex gap-2 small flex-wrap">
                                    ${tags.map(tag => `<span class="bg-body-secondary px-2 py-1 rounded text-capitalize" style="color: #1a1a2e !important; font-weight: 600;">${tag}</span>`).join('')}
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top" style="border-color: var(--border-color) !important;">
                                <div class="small text-truncate" style="color: var(--text-muted); max-width: 60%;">
                                    <i class="bi bi-envelope"></i> <span title="${email}">${email}</span><br>
                                    <i class="bi bi-telephone"></i> ${phone}
                                </div>
                                <a href="${profileUrl}"
                                   class="btn py-1 px-3 text-white fw-bold"
                                   style="background-color: #D40032; border-radius: 6px; font-size: 0.8rem;">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            grid.insertAdjacentHTML('beforeend', cardHTML);
        });

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        const pagContainer = document.getElementById('paginationContainer');
        pagContainer.innerHTML = '';
        if (totalPages <= 1) return;

        pagContainer.innerHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link border-0" style="background-color: transparent; color: var(--text-muted); cursor: pointer;" onclick="renderPage(${currentPage - 1})">Previous</a>
            </li>
        `;

        for (let p = 1; p <= totalPages; p++) {
            const isActive = currentPage === p;
            pagContainer.innerHTML += `
                <li class="page-item ${isActive ? 'active' : ''}">
                    <a class="page-link border-0 rounded mx-1 ${isActive ? 'bg-danger text-white shadow-sm' : ''}" 
                       style="${!isActive ? 'background-color: transparent; color: var(--text-muted); cursor: pointer;' : 'cursor: default;'}"
                       onclick="renderPage(${p})">
                        ${p}
                    </a>
                </li>
            `;
        }

        pagContainer.innerHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link border-0" style="background-color: transparent; color: var(--text-muted); cursor: pointer;" onclick="renderPage(${currentPage + 1})">Next</a>
            </li>
        `;
    }

    function handleSort() {
        const order = document.getElementById('sortSelect').value;
        sortData(order);
        renderPage(1); 
    }

    function sortData(order) {
        allBusinesses.sort((a, b) => {
            const nameA = (a.registered_business_name || '').toLowerCase();
            const nameB = (b.registered_business_name || '').toLowerCase();
            if (order === 'asc') return nameA.localeCompare(nameB);
            return nameB.localeCompare(nameA);
        });
    }

    function handleSearch() {
        const term = document.getElementById('searchInput').value.trim().toLowerCase();

        if (!term) {
            allBusinesses = [...masterBusinesses];
        } else {
            allBusinesses = masterBusinesses.filter(biz => {
                const name = (biz.registered_business_name || '').toLowerCase();
                const industry = (biz.industry || '').toLowerCase();
                const tagline = (biz.business_tagline || '').toLowerCase();
                const email = (biz.email || '').toLowerCase();
                const tags = (Array.isArray(biz.tags) ? biz.tags.join(' ') : '').toLowerCase();
                return name.includes(term) || industry.includes(term) || tagline.includes(term) || email.includes(term) || tags.includes(term);
            });
        }

        // Re-apply current sort
        const order = document.getElementById('sortSelect').value;
        sortData(order);
        renderPage(1);
    }
</script>

<style>
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    @keyframes spin { 
        100% { transform: rotate(360deg); } 
    }
</style>

@endsection