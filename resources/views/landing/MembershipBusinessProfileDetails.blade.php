@extends('layouts.app')
@include('partials.api-config')
@section('content')

{{-- LOADING SPINNER --}}
<div id="loading-spinner" class="text-center py-5" style="margin-top: 150px; min-height: 60vh;">
    <i class="bi bi-arrow-repeat text-danger" style="display:inline-block; animation: spin 1s linear infinite; font-size: 3rem;"></i>
    <p class="mt-3 fw-bold text-muted" style="font-family: 'Poppins', sans-serif;">Loading Business Profile...</p>
</div>

{{-- ERROR STATE --}}
<div id="error-state" class="container text-center py-5" style="display: none; margin-top: 100px; min-height: 50vh;">
    <h1 class="text-danger fw-bold" style="font-size: 4rem;"><i class="bi bi-exclamation-circle"></i></h1>
    <h2 class="fw-bold mt-3">Profile Not Found</h2>
    <p class="text-muted" id="error-message">The business profile you are looking for does not exist or couldn't be loaded.</p>
    <a href="{{ route('membership') }}" class="btn btn-danger mt-4 px-4 py-2 fw-bold rounded-pill">Return to Directory</a>
</div>

{{-- MAIN CONTENT WRAPPER --}}
<div id="main-content" style="display: none;">

    {{-- HERO SECTION --}}
    <div class="w-100" style="background:#1f2328; padding-top: 130px; padding-bottom: 60px;">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                <div class="rounded-circle overflow-hidden bg-white d-flex align-items-center justify-content-center shadow" style="width: 150px; height: 150px; border: 5px solid rgba(255,255,255,0.1);">
                    <div id="biz-avatar-container" class="w-100 h-100 d-flex align-items-center justify-content-center">
                        <span id="biz-initials" class="fw-bold text-danger" style="font-size: 3.5rem;"></span>
                    </div>
                </div>
                <div class="text-center text-md-start text-white">
                    <div class="mb-2">
                        <span id="biz-industry" class="badge bg-danger rounded-pill px-3 py-2 fw-bold" style="font-size: 0.85rem;">Loading...</span>
                    </div>
                    <h1 id="biz-name-main" class="fw-bold mb-2" style="font-family: 'DM Sans', sans-serif; font-size: 2.5rem;">Loading...</h1>
                    <p id="biz-tagline" class="mb-0 text-white-50 fs-5" style="font-family: 'Poppins', sans-serif;"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN DETAILS SECTION --}}
    <div class="container py-5" style="margin-top: -30px; position: relative; z-index: 10;">
        <div class="row g-4">

            {{-- LEFT COLUMN: About & Products --}}
            <div class="col-lg-8">

                {{-- ABOUT --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                    <h4 class="fw-bold mb-4" style="color: var(--text-main); font-family: 'DM Sans', sans-serif;">About the Business</h4>
                    <p id="biz-about-side" class="text-muted" style="line-height: 1.8; font-family: 'Poppins', sans-serif;">Loading description...</p>
                </div>

                {{-- PRODUCTS & SERVICES --}}
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h4 class="fw-bold mb-4" style="color: var(--text-main); font-family: 'DM Sans', sans-serif;">Products & Services</h4>
                    <div id="biz-services" class="row">
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="bi bi-arrow-repeat spin fs-2"></i><br>Loading products...
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: Contact, Hours, Map --}}
            <div class="col-lg-4">

                {{-- CONTACT INFO --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                    <h5 class="fw-bold mb-4" style="color: var(--text-main); font-family: 'DM Sans', sans-serif;">Contact Information</h5>

                    <div class="d-flex align-items-start mb-3">
                        <div class="icon-wrapper bg-danger bg-opacity-10 text-danger me-3 d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 45px; height: 45px;">
                            <i class="bi bi-geo-alt-fill fs-5"></i>
                        </div>
                        <div class="pt-1">
                            <span class="d-block text-muted small text-uppercase fw-bold mb-1">Address</span>
                            <span style="color: var(--text-main); font-size: 0.95rem; font-weight: 500; line-height: 1.4; display: block;" id="biz-address">Loading...</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-danger bg-opacity-10 text-danger me-3 d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 45px; height: 45px;">
                            <i class="bi bi-telephone-fill fs-5"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted small text-uppercase fw-bold mb-1">Phone</span>
                            <span id="biz-phone" class="fw-bold" style="color: var(--text-main);">Loading...</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-wrapper bg-danger bg-opacity-10 text-danger me-3 d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 45px; height: 45px;">
                            <i class="bi bi-envelope-fill fs-5"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted small text-uppercase fw-bold mb-1">Email</span>
                            <span id="biz-email" class="fw-bold" style="color: var(--text-main); word-break: break-all;">Loading...</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="#" id="biz-phone-btn" class="btn btn-danger py-2 fw-bold" style="border-radius: 8px;"><i class="bi bi-telephone me-2"></i> Call Now</a>
                        <a href="#" id="biz-email-btn" class="btn btn-outline-danger py-2 fw-bold" style="border-radius: 8px;"><i class="bi bi-envelope me-2"></i> Send Email</a>
                    </div>
                </div>

                {{-- BUSINESS HOURS --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                    <h5 class="fw-bold mb-4" style="color: var(--text-main); font-family: 'DM Sans', sans-serif;">Business Hours</h5>
                    <div id="biz-hours-container" style="font-family: 'Poppins', sans-serif;">
                        <div class="text-center text-muted py-3">Loading hours...</div>
                    </div>
                </div>

                {{-- MAP --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <iframe id="biz-map-frame" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div class="p-3 bg-white d-flex align-items-start gap-3">
                        <div class="bg-danger bg-opacity-10 text-danger p-2 rounded flex-shrink-0">
                            <i class="bi bi-geo-alt fs-4"></i>
                        </div>
                        <div class="pt-1">
                            <p class="mb-0" style="font-size: 0.95rem; font-weight: 500; color: var(--text-main); line-height: 1.4;" id="biz-address-map">Loading...</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const targetId = "{{ $id ?? '' }}";

        // ==========================================
        // 1. STRICTLY FILTERED PRODUCT FETCH
        // ==========================================
        async function loadBusinessProducts(memberId) {
            const productsGrid = document.getElementById('biz-services');
            if (!productsGrid) return;

            productsGrid.innerHTML = '<div class="col-12 text-center py-5 text-muted"><i class="bi bi-arrow-repeat spin fs-2"></i><br>Loading products...</div>';

            try {
                const apiBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');

                // STRICTLY FILTER BY member_id
                const response = await fetch(`${apiBase}/v1/public/products?member_id=${memberId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch products');

                const result = await response.json();
                const products = result.data || result || [];

                if (products.length > 0) {
                    productsGrid.innerHTML = products.map(prod => {
                        const prodName = prod.name || 'Unnamed Product';
                        const prodDesc = prod.description || '';
                        const prodUrl = prod.url || prod.product_url || '';

                        return `
                            <div class="col-md-6 col-lg-6 mb-4">
                                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; background: #fff; border: 1px solid rgba(0,0,0,0.05) !important;">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="bg-danger bg-opacity-10 text-danger rounded p-3 d-flex align-items-center justify-content-center flex-shrink-0">
                                                <i class="bi bi-box-seam fs-3"></i>
                                            </div>
                                            <div>
                                                <h5 class="card-title fw-bold mb-2" style="color: #111; font-size: 1.1rem;">${prodName}</h5>
                                                <p class="card-text text-muted small mb-0" style="line-height: 1.5;">${prodDesc}</p>
                                                ${prodUrl ? `<a href="${prodUrl}" target="_blank" class="small fw-bold text-danger mt-2 d-inline-block">View Details <i class="bi bi-arrow-right"></i></a>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    productsGrid.innerHTML = '<div class="col-12 text-center text-muted py-4">No products or services listed yet.</div>';
                }
            } catch (error) {
                console.error("Product Fetch Error:", error);
                productsGrid.innerHTML = '<div class="col-12 text-center text-muted py-4">Products are currently unavailable.</div>';
            }
        }

        // ==========================================
        // 2. MAIN PROFILE FETCH LOGIC
        // ==========================================
        try {
            const headers = {
                'Accept': 'application/json'
            };
            const response = await fetch(`${window.API_BASE_URL}/v1/business/${encodeURIComponent(targetId)}`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error("Failed to connect to the business directory API.");
            }

            const result = await response.json();
            let biz = {};

            function resolveBusinessPayload(payload) {
                if (!payload || typeof payload !== 'object') return {};
                if (Array.isArray(payload)) return payload.find(item => String(item.id) === String(targetId) || String(item._idx) === String(targetId)) || {};
                if (payload.data) {
                    if (Array.isArray(payload.data)) return payload.data.find(item => String(item.id) === String(targetId) || String(item._idx) === String(targetId)) || {};
                    return payload.data;
                }
                if (payload.business_profile && typeof payload.business_profile === 'object') return payload.business_profile;
                if (payload.profile && typeof payload.profile === 'object') return payload.profile;
                return payload;
            }

            biz = resolveBusinessPayload(result);

            document.getElementById('loading-spinner').style.display = 'none';

            if (!biz || Object.keys(biz).length === 0) {
                document.getElementById('error-message').innerText = "The business profile you are looking for does not exist.";
                document.getElementById('error-state').style.display = 'block';
                return;
            }

            // === DATA EXTRACTION — matched to the exact BusinessResource JSON shape ===
            // BusinessResource (public endpoint, no auth) exposes fields at BOTH top-level
            // AND nested inside biz.applicant.* for backwards-compat with older blade code.
            // Priority: top-level first (most direct), then nested fallbacks.

            function safeObj(val) {
                if (!val) return {};
                if (typeof val === 'string') {
                    try {
                        return JSON.parse(val);
                    } catch (e) {
                        return {};
                    }
                }
                return (typeof val === 'object' && !Array.isArray(val)) ? val : {};
            }

            const basic = safeObj(biz.applicant?.basic_profile ?? biz.basic_profile);
            const rep = safeObj(biz.applicant?.official_representative ?? biz.official_representative);
            const org = safeObj(biz.applicant?.organization_membership ?? biz.organization_membership);
            const bizAdditional = safeObj(biz.applicant?.business_additional_data ?? biz.business_additional_data);

            const name = biz.registered_business_name || basic.registered_business_name || 'Business Name';

            // 1. EMAIL
            // BusinessResource exposes: biz.email (top-level) + biz.applicant.email + biz.applicant.basic_profile.email
            const email =
                biz.email // top-level — primary path from BusinessResource
                ||
                biz.applicant?.email // flat on nested applicant object
                ||
                basic.email // inside basic_profile
                ||
                biz.user?.email ||
                'N/A';

            // 2. PHONE — two DB columns on Applicant:
            //    telephone_no   → business landline (Applicant::$fillable)
            //    rep_contact_no → representative mobile (Applicant::$fillable)
            // BusinessResource exposes BOTH at top-level AND nested.
            const phone =
                biz.telephone_no // top-level — PRIMARY (BusinessResource fix)
                ||
                biz.applicant?.telephone_no // flat on nested applicant object
                ||
                basic.telephone_no // inside basic_profile
                ||
                biz.rep_contact_no // top-level rep phone fallback
                ||
                biz.applicant?.rep_contact_no ||
                rep.contact_no // inside official_representative
                ||
                biz.user?.contact_number ||
                biz.contact_number ||
                'N/A';

            // 3. INDUSTRY
            const industry =
                bizAdditional.industry ||
                biz.industry ||
                org.type_of_company ||
                biz.type_of_company ||
                'Business';

            // 4. ABOUT / TAGLINE
            const tagline =
                bizAdditional.business_tagline ||
                biz.business_tagline ||
                '';
            const description =
                bizAdditional.about_description ||
                biz.about_description ||
                biz.about ||
                biz.description ||
                tagline ||
                'No detailed description provided.';

            // 5. LOCATION
            let address = 'Valenzuela City';
            let mapQuery = name;

            // BusinessResource exposes business_location at top-level AND inside basic_profile
            const loc = safeObj(biz.business_location ?? basic.business_location);

            if (loc && Object.keys(loc).length > 0) {
                if (loc.location_link && loc.location_link !== 'N/A') {
                    address = loc.location_link;
                    mapQuery = loc.location_link;
                } else {
                    const addressParts = [loc.business_address, loc.city_municipality, loc.province].filter(p => p && p !== 'N/A');
                    if (addressParts.length > 0) {
                        address = addressParts.join(', ');
                        mapQuery = address;
                    }
                }
            }

            // Populate UI Elements
            document.getElementById('biz-name-main').innerText = name;
            document.getElementById('biz-industry').innerText = industry;
            document.getElementById('biz-tagline').innerText = tagline ? `"${tagline}"` : '';
            document.getElementById('biz-about-side').innerText = description;
            document.getElementById('biz-phone').innerText = phone;
            document.getElementById('biz-email').innerText = email;
            document.getElementById('biz-address').innerText = address;
            document.getElementById('biz-address-map').innerText = address;

            document.getElementById('biz-phone-btn').href = phone !== 'N/A' ? `tel:${phone}` : '#';
            document.getElementById('biz-email-btn').href = email !== 'N/A' ? `mailto:${email}` : '#';

            // DYNAMIC PHOTO URL
            if (biz.photo_url && biz.photo_url !== 'N/A' && biz.photo_url !== 'null') {
                const activeOrigin = new URL(window.API_BASE_URL || window.location.origin).origin;
                let finalPhotoUrl = biz.photo_url
                    .replace('http://127.0.0.1:8000', activeOrigin)
                    .replace('http://localhost:8000', activeOrigin);

                document.getElementById('biz-avatar-container').innerHTML = `<img src="${finalPhotoUrl}" alt="${name}" class="w-100 h-100" style="object-fit: cover;">`;
            } else {
                let initials = name.substring(0, 2).toUpperCase();
                const words = name.split(' ');
                if (words.length > 1 && words[1].length > 0) {
                    initials = (words[0][0] + words[1][0]).toUpperCase();
                }
                const initialsEl = document.getElementById('biz-initials');
                if (initialsEl) initialsEl.innerText = initials;
            }

            // Map Business Hours
            const hoursContainer = document.getElementById('biz-hours-container');
            if (hoursContainer) {
                hoursContainer.innerHTML = '';
                if (biz.business_hours && Object.keys(biz.business_hours).length > 0) {
                    for (const [day, time] of Object.entries(biz.business_hours)) {
                        hoursContainer.innerHTML += `
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-capitalize" style="color: var(--text-main); font-weight: 500;">${day}</span>
                                <span class="fw-bold" style="color: var(--text-main);">${time}</span>
                            </div>
                        `;
                    }
                } else {
                    hoursContainer.innerHTML = '<span style="color: var(--text-muted);">Business hours not provided.</span>';
                }
            }

            // FIX GOOGLE MAPS URL
            const encodedMapQuery = encodeURIComponent(mapQuery + ', Philippines');
            const mapFrame = document.getElementById('biz-map-frame');
            if (mapFrame) {
                mapFrame.src = `https://maps.google.com/maps?q=${encodedMapQuery}&t=m&z=15&output=embed`;
            }

            // Reveal the UI
            document.getElementById('main-content').style.display = 'block';

            // TRIGGER THE STRICTLY FILTERED FETCH NOW THAT PROFILE IS LOADED!
            loadBusinessProducts(targetId);

        } catch (error) {
            console.error("Error fetching business details:", error);
            document.getElementById('loading-spinner').style.display = 'none';
            document.getElementById('error-message').innerText = "Unable to connect to the server. Please check your connection.";
            document.getElementById('error-state').style.display = 'block';
        }
    });
</script>

@endsection