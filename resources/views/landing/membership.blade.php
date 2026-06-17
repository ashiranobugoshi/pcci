@extends('layouts.app')
@include('partials.api-config')

@section('content')


{{-- HERO SECTION --}}
<div class="w-100 mb-0 d-flex flex-column align-items-center" style="height: 623px; margin-top: -1px; background-color: var(--bg-hero, #1a1a2e); padding-top: 130px; transition: background-color 0.3s ease;">
    <div class="container d-flex flex-column align-items-center text-center">
        <span class="mb-3 d-block" style="color: #ffffff !important; font-family: 'DM Sans', sans-serif; font-weight: 900; font-size: 24px; text-transform: uppercase;">JOIN PCCI - VALENZUELA</span>
        <h1 class="headline-text fw-bold mb-4 text-uppercase" style="color: #ffffff !important; font-family: 'DM Sans', sans-serif; font-size: 63px;">
            Discover Local <span style="color: #EB3223;">Businesses</span>
        </h1>
        <p style="color: #574949 !important; font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 24px; max-width: 1262px; margin-bottom: 40px;">
            Explore our network of trusted local enterprises and connect with the best in Valenzuela City.
        </p>

        <div class="search-overlay bg-white p-3 rounded shadow d-flex align-items-center" style="max-width: 800px; width: 100%; margin-top: 20px;">
            <i class="bi bi-search ms-3 me-2 fs-5 text-muted"></i>
            <input type="text" id="searchInput" class="form-control border-0 shadow-none fs-5" placeholder="Search businesses, industries, or tags..." oninput="handleSearch()">
            <select id="sortSelect" class="form-select border-0 bg-light ms-3 shadow-none w-auto" onchange="handleSearch()">
                <option value="asc">A - Z</option>
                <option value="desc">Z - A</option>
            </select>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MEMBERSHIP PLANS SECTION                   --}}
{{-- ========================================== --}}
<div class="container" style="padding-top: 80px; padding-bottom: 40px;">
    <div class="text-center mb-5">
        <h2 class="fw-bold" style="font-family: 'DM Sans', sans-serif; font-size: 2.5rem; color: #111827; letter-spacing: -0.5px;">MEMBERSHIP PLANS</h2>
    </div>

    <div id="membershipPlans" class="row justify-content-center g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow text-center plan-card" style="border-radius: 12px; padding: 40px 20px;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 320px;">
                    <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-4 mb-0 fw-bold text-muted">Loading membership plans...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- BUSINESS DIRECTORY SECTION                 --}}
{{-- ========================================== --}}
<div class="container py-5" style="min-height: 500px; border-top: 1px solid #eee;">

    <div class="text-center mb-5 mt-4">
        <h2 class="fw-bold" style="font-family: 'DM Sans', sans-serif; font-size: 2.5rem; color: #111827; letter-spacing: -0.5px;">PCCI DIRECTORY</h2>
    </div>

    <div id="businessGrid" class="row g-4 mt-2">
        <div class="col-12 text-center py-5">
            <div class="spin fs-1" style="color: #be1e38;"><i class="bi bi-arrow-repeat"></i></div>
            <h4 class="mt-3 text-muted">Loading Businesses...</h4>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-5" id="paginationControls"></div>
</div>

<script>
    let masterBusinesses = [];
    let allBusinesses = [];
    let currentPage = 1;
    const itemsPerPage = 9;

    document.addEventListener("DOMContentLoaded", function() {
        fetchMembershipPlans();
        fetchBusinesses();
    });

    async function fetchMembershipPlans() {
        const container = document.getElementById('membershipPlans');
        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/membership-types`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to load membership plans');
            }

            const data = await response.json();
            const plans = data.data || data || [];

            if (!Array.isArray(plans) || plans.length === 0) {
                container.innerHTML = '<div class="col-12 text-center text-muted"><p>No membership plan information is available at this time.</p></div>';
                return;
            }

            container.innerHTML = plans.map((plan, index) => {
                const price = parseFloat(plan.price || 0).toLocaleString('en-PH', {
                    style: 'currency',
                    currency: 'PHP',
                    minimumFractionDigits: 2
                });
                const renewalText = plan.renewal_price ? `Renewal: ₱${parseFloat(plan.renewal_price).toLocaleString('en-PH', { minimumFractionDigits: 2 })}` : '';
                const noteText = plan.notes ? plan.notes : `Membership duration: ${plan.duration_in_months || 12} months.`;
                const isPrimary = index === 0;
                const cardBg = isPrimary ? '#be1e38' : '#ffffff';
                const cardColor = isPrimary ? 'white' : '#111827';
                const btnClass = isPrimary ? 'btn btn-light text-danger' : 'btn btn-outline-dark';

                return `
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow text-center plan-card" style="border-radius: 12px; padding: 40px 20px; background-color: ${cardBg}; color: ${cardColor};">
                            <div class="card-body d-flex flex-column align-items-center">
                                <div class="mb-3" style="width: 70px; height: 70px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi ${isPrimary ? 'bi-person-badge-fill' : 'bi-journal-bookmark-fill'}" style="font-size: 1.8rem; color: ${isPrimary ? 'white' : '#be1e38'};"></i>
                                </div>
                                <h5 class="fw-bold mb-3" style="font-family: 'DM Sans', sans-serif; letter-spacing: 1px;">${plan.name || 'Membership Plan'}</h5>
                                <div class="mb-4">
                                    <span class="d-block fw-bold mb-1" style="font-size: 0.8rem; opacity: 0.9; letter-spacing: 1px;">STARTS AT</span>
                                    <span style="font-family: 'DM Sans', sans-serif; font-size: 2rem; font-weight: 800;">${price}</span>
                                    <span class="fw-bold d-block mt-1" style="font-size: 0.8rem; letter-spacing: 2px; opacity: 0.8;">/ ANNUALLY</span>
                                </div>
                                <p class="small mb-3" style="opacity: 0.85;">${noteText}</p>
                                <a href="{{ route('signup') }}" class="${btnClass} fw-bold px-4 py-2 mt-auto" style="border-radius: 6px; letter-spacing: 1px; border-width: 2px; width: 80%;">APPLY NOW</a>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } catch (error) {
            console.error('Membership plans load failed:', error);
            container.innerHTML = '<div class="col-12 text-center text-danger"><p>Unable to load membership plans. Please try again later.</p></div>';
        }
    }

    async function fetchBusinesses() {
        try {
            // FIXED API ROUTE -> /v1/business
            const response = await fetch(`${window.API_BASE_URL}/v1/business`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                masterBusinesses = (data.data || data || []).map((biz, idx) => {
                    const businessId = biz.id || biz.user_id || biz.member_id || biz.applicant_id || (biz.applicant && biz.applicant.id) || idx;
                    return {
                        ...biz,
                        _idx: idx,
                        id: businessId
                    };
                });
                allBusinesses = [...masterBusinesses];
                sortData(document.getElementById('sortSelect').value);
                renderPage(1);
            } else {
                document.getElementById('businessGrid').innerHTML = '<div class="col-12 text-center text-danger"><h5>Failed to load businesses.</h5></div>';
            }
        } catch (error) {
            document.getElementById('businessGrid').innerHTML = '<div class="col-12 text-center text-muted"><h5>Network error. Please try again.</h5></div>';
        }
    }

    function renderPage(page) {
        currentPage = page;
        const grid = document.getElementById('businessGrid');
        grid.innerHTML = '';

        if (allBusinesses.length === 0) {
            grid.innerHTML = '<div class="col-12 text-center py-5 text-muted"><h4>No businesses found matching your criteria.</h4></div>';
            renderPagination(0);
            return;
        }

        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedData = allBusinesses.slice(startIndex, endIndex);

        let html = '';
        paginatedData.forEach(biz => {
            const name = biz.registered_business_name || biz.applicant?.registered_business_name || 'Unnamed Business';

            // SMART PHOTO FINDER
            let rawPhoto = biz.photo_url ||
                biz.applicant?.basic_profile?.photo_url ||
                biz.applicant?.photo_url ||
                biz.user?.photo_url ||
                biz.applicant?.user?.photo_url ||
                biz.member?.user?.photo_url ||
                biz.profile_photo_url ||
                null;

            let finalPhotoUrl = null;
            if (rawPhoto && rawPhoto !== 'N/A' && String(rawPhoto).trim() !== 'null' && String(rawPhoto).trim() !== '') {
                if (rawPhoto.startsWith('http')) {
                    finalPhotoUrl = rawPhoto;
                } else {
                    const baseUrl = (window.API_BASE_URL || '').replace('/api', '');
                    finalPhotoUrl = rawPhoto.startsWith('/storage') ? `${baseUrl}${rawPhoto}` : `${baseUrl}/storage/${rawPhoto}`;
                }
            }

            // Fallback to Initials
            let imageContent = '';
            if (finalPhotoUrl) {
                imageContent = `<img src="${finalPhotoUrl}" class="w-100 h-100" style="object-fit: cover;" alt="${name}">`;
            } else {
                let initials = name.substring(0, 2).toUpperCase();
                const words = name.split(' ');
                if (words.length > 1 && words[1].length > 0) {
                    initials = (words[0][0] + words[1][0]).toUpperCase();
                }
                imageContent = `<div class="w-100 h-100 d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger fw-bold" style="font-size: 4.5rem; font-family: 'DM Sans', sans-serif;">${initials}</div>`;
            }

            // UPDATED: Outer div changed to <a> tag
            html += `
                <div class="col-md-6 col-lg-4">
                    <a href="/business/${biz.id}" class="card h-100 shadow-sm border-0 business-card text-decoration-none text-dark" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s; display: block;">
                        <div style="height: 180px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid #eee;">
                             ${imageContent}
                        </div>
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-1 text-truncate">${name}</h5>
                            <p class="small mb-3 text-uppercase fw-bold" style="color: #be1e38;">${biz.industry || 'General Industry'}</p>
                            <p class="text-muted small text-truncate" style="font-family: 'Poppins', sans-serif;">${biz.business_tagline || biz.email || 'No additional details available.'}</p>
                            <div class="btn btn-sm btn-outline-danger w-100 fw-bold mt-2" style="border-radius: 6px; pointer-events: none;">View Profile</div>
                        </div>
                    </a>
                </div>
            `;
        });

        grid.innerHTML = html;
        renderPagination(Math.ceil(allBusinesses.length / itemsPerPage));
    }

    function renderPagination(totalPages) {
        const controls = document.getElementById('paginationControls');
        if (totalPages <= 1) {
            controls.innerHTML = '';
            return;
        }

        let paginationHtml = '<ul class="pagination shadow-sm">';
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <button class="page-link" onclick="renderPage(${i})" style="${i === currentPage ? 'background-color: #be1e38; border-color: #be1e38; color: white;' : 'color: #333;'}">${i}</button>
                </li>
            `;
        }
        paginationHtml += '</ul>';
        controls.innerHTML = paginationHtml;
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
        100% {
            transform: rotate(360deg);
        }
    }

    .spin {
        display: inline-block;
        animation: spin 1s linear infinite;
    }

    .business-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .plan-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 15px 30px rgba(190, 30, 56, 0.15) !important;
        transition: all 0.3s ease;
    }
</style>

@endsection