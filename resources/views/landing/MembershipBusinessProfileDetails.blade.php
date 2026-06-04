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
    <div class="w-100" style="background:#1f2330; min-height: 420px; transition: all 0.3s ease;">
        <div class="container">
            <div class="row align-items-center g-4" style="padding-top:120px; padding-bottom:80px;">

                {{-- LOGO / INITIALS --}}
                <div class="col-auto">
                    <div class="rounded-4 bg-light d-flex align-items-center justify-content-center overflow-hidden shadow-lg"
                        style="width:130px; height:130px; border: 4px solid rgba(255,255,255,0.1);" id="biz-avatar-container">
                        <span class="fw-bold text-danger" style="font-size: 2.5rem;" id="biz-initials">...</span>
                    </div>
                </div>

                {{-- HERO CONTENT --}}
                <div class="col">
                    <h1 class="fw-bold text-white mb-2" style="font-family: 'DM Sans', sans-serif; font-size: 2.5rem;" id="biz-name-main">
                        Loading...
                    </h1>

                    <span class="badge rounded-pill mb-3 px-3 py-2 fw-bold text-uppercase" style="background:#2e5aac; font-size: 0.85rem;" id="biz-industry">
                        Industry
                    </span>

                    <p class="text-light mb-4 font-italic" style="max-width:750px; opacity:.9; font-size: 1.1rem; line-height: 1.6;" id="biz-tagline">
                        Loading tagline...
                    </p>

                    <div class="d-flex gap-3 flex-wrap">
                        <a href="#" id="biz-phone-btn" class="btn btn-danger px-4 py-2 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-telephone-fill me-2"></i> CONTACT US
                        </a>
                        <a href="#" id="biz-email-btn" class="btn btn-outline-light px-4 py-2 fw-bold rounded-pill">
                            <i class="bi bi-envelope-fill me-2"></i> EMAIL NOW
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="container mt-5 mb-5" style="font-family: 'DM Sans', sans-serif;">
        <div class="row g-5">

            {{-- LEFT CONTENT --}}
            <div class="col-lg-8">

                {{-- ABOUT --}}
                <div class="card border border-danger shadow-sm p-4 p-md-5 rounded-4 mb-4" style="background: var(--bg-card);">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-buildings text-danger fs-4"></i>
                        </div>
                        <h4 class="fw-bold text-danger mb-0" style="font-family: 'Poppins', sans-serif;">About Our Company</h4>
                    </div>
                    <p id="biz-about-side" style="line-height: 1.8; color: var(--text-main); font-size: 1.05rem;">Loading...</p>
                </div>

                {{-- PRODUCTS & SERVICES (TAGS) --}}
                <div class="card border border-danger shadow-sm p-4 p-md-5 rounded-4 mb-4" style="background: var(--bg-card);">
                    <h4 class="fw-bold text-danger mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-briefcase"></i>
                        <span>Products & Services</span>
                    </h4>
                    <div class="row g-3" id="biz-services">
                        {{-- Services injected here via JS --}}
                    </div>
                </div>

                {{-- MAP --}}
                <div class="card border border-danger shadow-sm p-4 p-md-5 rounded-4 mb-5" style="background: var(--bg-card);">
                    <h4 class="fw-bold text-danger mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Our Location</span>
                    </h4>
                    <p class="mb-4 fw-bold" style="color: var(--text-main); font-size: 1.1rem;">
                        <span id="biz-address-map">Loading...</span>
                    </p>
                    <div class="rounded-4 overflow-hidden shadow-sm border">
                        <iframe id="biz-map-frame"
                            src=""
                            width="100%" height="350" style="border:0;" loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDEBAR --}}
            <div class="col-lg-4">

                {{-- CONTACT INFO --}}
                <div class="card border border-danger shadow-sm p-4 rounded-4 mb-4" style="background: var(--bg-card);">
                    <h5 class="fw-bold text-danger mb-4" style="font-family: 'Poppins', sans-serif;">Contact Information</h5>
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:45px;height:45px;">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div>
                                <small class="fw-bold text-uppercase text-danger" style="letter-spacing: 1px; font-size: 0.75rem;">Phone</small><br>
                                <span id="biz-phone" class="fw-medium" style="color: var(--text-main); font-size: 1.05rem;">Loading...</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:45px;height:45px;">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div style="overflow: hidden; text-overflow: ellipsis;">
                                <small class="fw-bold text-uppercase text-danger" style="letter-spacing: 1px; font-size: 0.75rem;">Email</small><br>
                                <span id="biz-email" class="fw-medium" style="color: var(--text-main); font-size: 1.05rem;">Loading...</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:45px;height:45px;">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <small class="fw-bold text-uppercase text-danger" style="letter-spacing: 1px; font-size: 0.75rem;">Address</small><br>
                                <span id="biz-address" class="fw-medium" style="color: var(--text-main); font-size: 1.05rem;">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BUSINESS HOURS --}}
                <div class="card border border-danger shadow-sm p-4 rounded-4 mb-4" style="background: var(--bg-card);">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-clock-history text-danger fs-4"></i>
                        <h5 class="fw-bold text-danger mb-0" style="font-family: 'Poppins', sans-serif;">Business Hours</h5>
                    </div>
                    <div class="d-flex flex-column gap-2" id="biz-hours-container">
                        {{-- Hours injected here --}}
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="card border border-danger shadow-sm p-4 rounded-4 position-sticky" style="top: 100px; background: var(--bg-card);">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-gear text-danger fs-4"></i>
                        <h5 class="fw-bold text-danger mb-0">Quick Actions</h5>
                    </div>
                    <div class="d-grid gap-3">
                        <button class="btn btn-outline-danger fw-bold rounded-pill">Request Quote</button>
                        <button class="btn btn-outline-danger fw-bold rounded-pill">Schedule Call</button>
                        <hr class="my-2 text-muted">
                        <a href="{{ route('membership') }}" class="btn btn-outline-danger fw-bold rounded-pill py-2">
                            <i class="bi bi-arrow-left me-2"></i> Browse Directory
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }

    .service-box:hover {
        transform: translateY(-3px);
        transition: all 0.3s ease;
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }
</style>

{{-- ========================================= --}}
{{-- API FETCH LOGIC MAPS TO YOUR EXACT JSON --}}
{{-- ========================================= --}}
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const targetId = "{{ $id ?? '' }}";

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
                if (!payload || typeof payload !== 'object') {
                    return {};
                }

                if (Array.isArray(payload)) {
                    return payload.find(item => String(item.id) === String(targetId) || String(item._idx) === String(targetId)) || {};
                }

                if (payload.data) {
                    if (Array.isArray(payload.data)) {
                        return payload.data.find(item => String(item.id) === String(targetId) || String(item._idx) === String(targetId)) || {};
                    }
                    return payload.data;
                }

                if (payload.business_profile && typeof payload.business_profile === 'object') {
                    return payload.business_profile;
                }

                if (payload.profile && typeof payload.profile === 'object') {
                    return payload.profile;
                }

                return payload;
            }

            biz = resolveBusinessPayload(result);

            document.getElementById('loading-spinner').style.display = 'none';

            if (!biz || Object.keys(biz).length === 0) {
                document.getElementById('error-message').innerText = "The business profile you are looking for does not exist.";
                document.getElementById('error-state').style.display = 'block';
                return;
            }

            // === MAPPING YOUR API DATA ===
            const name = biz.registered_business_name || 'Business Name';
            const email = biz.email || 'N/A';
            const phone = biz.telephone_no || 'N/A';
            const industry = biz.industry || 'Business';
            const tagline = biz.business_tagline || '';
            const description = biz.description || tagline || 'No detailed description provided.';

            // Handle Nested Location Object
            // Handle Nested Location Object
            let address = 'Valenzuela City';
            let mapQuery = name;

            if (biz.business_location) {
                const loc = biz.business_location;

                // If location_link exists, use it for BOTH the visible text AND the Google Map!
                if (loc.location_link && loc.location_link !== 'N/A') {
                    address = loc.location_link;
                    mapQuery = loc.location_link; // <-- This tells the map to point exactly here!
                } else {
                    // Fallback just in case location_link is empty
                    const addressParts = [loc.business_address, loc.city_municipality, loc.province].filter(p => p && p !== 'N/A');
                    if (addressParts.length > 0) {
                        address = addressParts.join(', ');
                        mapQuery = address;
                    }
                }
            }
            // Update Basic Text Elements
            document.getElementById('biz-name-main').innerText = name;
            document.getElementById('biz-industry').innerText = industry;
            document.getElementById('biz-tagline').innerText = tagline ? `"${tagline}"` : '';
            document.getElementById('biz-about-side').innerText = description;
            document.getElementById('biz-phone').innerText = phone;
            document.getElementById('biz-email').innerText = email;
            document.getElementById('biz-address').innerText = address;
            document.getElementById('biz-address-map').innerText = address;

            // Update Contact Buttons
            document.getElementById('biz-phone-btn').href = phone !== 'N/A' ? `tel:${phone}` : '#';
            document.getElementById('biz-email-btn').href = email !== 'N/A' ? `mailto:${email}` : '#';

            // 3. DYNAMIC PHOTO URL
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
                document.getElementById('biz-initials').innerText = initials;
            }

            function extractProductItems(source) {
                if (!source || typeof source !== 'object') return [];

                const candidates = [
                    source.products,
                    source.product_items,
                    source.product_list,
                    source.items,
                    source.business_products,
                    source.business_services,
                    source.services,
                    source.offerings,
                    source.service_items,
                    source.services_offered,
                    source.catalog,
                    source.items_offered,
                    source.records,
                ];

                for (const candidate of candidates) {
                    if (Array.isArray(candidate) && candidate.length > 0) {
                        const items = candidate.map(item => {
                            if (typeof item === 'string') {
                                const title = item.trim();
                                return title ? {
                                    title,
                                    description: '',
                                    url: ''
                                } : null;
                            }
                            if (item && typeof item === 'object') {
                                const title = item.name || item.product_name || item.title || item.service_name || item.label || item.product_title || item.service_title;
                                if (!title) return null;
                                return {
                                    title: String(title).trim(),
                                    description: String(item.description || item.details || item.summary || item.long_description || item.product_description || item.service_description || '').trim(),
                                    url: String(item.url || item.service_url || item.website || item.link || item.product_url || item.website_url || '').trim(),
                                };
                            }
                            return null;
                        }).filter(Boolean);

                        if (items.length > 0) {
                            return items;
                        }
                    }

                    if (typeof candidate === 'string' && candidate.trim() !== '') {
                        return candidate.split(/[,;|]/).map(s => {
                            const title = s.trim();
                            return title ? {
                                title,
                                description: '',
                                url: ''
                            } : null;
                        }).filter(Boolean);
                    }
                }

                return [];
            }

            function renderProductCards(items, container) {
                if (!container) return;
                container.innerHTML = '';
                if (!Array.isArray(items) || items.length === 0) {
                    return;
                }

                container.innerHTML = items.map(item => `
                    <div class="col-md-6 col-lg-4">
                        <div class="service-box bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 p-3 h-100">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <i class="bi bi-box-seam text-danger fs-4 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 text-danger text-capitalize" style="font-family: 'Poppins', sans-serif;">${item.title}</h6>
                                    <p class="small mb-0 text-muted">${item.description || ''}</p>
                                </div>
                            </div>
                            ${item.url ? `<a href="${item.url}" target="_blank" rel="noopener noreferrer" class="small fw-bold text-danger">View product</a>` : ''}
                        </div>
                    </div>
                `).join('');
            }

            const servicesContainer = document.getElementById('biz-services');

            function resolveProductOwnerId(source) {
                if (!source || typeof source !== 'object') {
                    return null;
                }

                const keys = ['user_id', 'owner_id', 'created_by', 'member_id', 'business_user_id', 'seller_id'];
                for (const key of keys) {
                    if (source[key] !== undefined && source[key] !== null && String(source[key]).trim() !== '') {
                        return String(source[key]);
                    }
                }

                if (source.user && typeof source.user === 'object' && source.user.id) {
                    return String(source.user.id);
                }

                return null;
            }

            async function fetchProductsForBusiness(biz, targetId) {
                const ownerId = resolveProductOwnerId(biz);

                if (!ownerId) {
                    return [];
                }

                try {
                    const res = await fetch(`${window.API_BASE_URL}/v1/products/active?user_id=${encodeURIComponent(ownerId)}`);
                    if (!res.ok) return [];
                    const data = await res.json();
                    const list = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);

                    return list.map(item => ({
                        title: String(item.name || item.title || item.product_name || item.service_name || item.label || item.product_title || item.service_title || item.id || '').trim(),
                        description: String(item.description || item.details || item.summary || item.product_description || '').trim(),
                        url: String(item.url || item.service_url || item.product_url || item.website || '').trim(),
                    })).filter(Boolean);
                } catch (e) {
                    return [];
                }
            }

            // Prefer dedicated products API; fall back to embedded product lists in business object
            (async () => {
                let products = await fetchProductsForBusiness(biz, targetId);
                if (!products || products.length === 0) {
                    products = extractProductItems(biz);
                }
                renderProductCards(products, servicesContainer);
            })();

            // Map Business Hours
            const hoursContainer = document.getElementById('biz-hours-container');
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

            // 4. FIX GOOGLE MAPS URL (Added the missing $ and fixed the base URL)
            const encodedMapQuery = encodeURIComponent(mapQuery + ', Philippines');
            document.getElementById('biz-map-frame').src = `https://maps.google.com/maps?q=${encodedMapQuery}&t=m&z=15&output=embed`;

            // Reveal the UI
            document.getElementById('main-content').style.display = 'block';

        } catch (error) {
            console.error("Error fetching business details:", error);
            document.getElementById('loading-spinner').style.display = 'none';
            document.getElementById('error-message').innerText = "Unable to connect to the server. Please check your connection.";
            document.getElementById('error-state').style.display = 'block';
        }
    });
</script>

@endsection