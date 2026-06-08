@extends('layouts.app')

@section('content')

@php
$business = $business ?? [
'name' => isset($id) ? ('Member Business #' . $id) : 'Member Business',
'about' => 'Business details are currently unavailable. Please check back later for updated profile information.',
'services' => ['Business Service 1', 'Business Service 2', 'Business Service 3'],
'phone' => 'Not available',
'email' => 'Not available',
'address' => 'Address not available',
'hours' => [
'Monday - Friday' => 'Not available',
'Saturday' => 'Not available',
'Sunday' => 'Not available',
],
'map' => 'Valenzuela+City',
];
@endphp

{{-- HERO SECTION --}}
<div class="w-100" style="background:#1f2330; min-height: 350px;">
    <div class="container">
        <div class="row align-items-center justify-content-center justify-content-md-start g-4"
            style="padding-top:60px; padding-bottom:60px; @media (min-width: 768px) { padding-top:120px; padding-bottom:80px; }">

            {{-- LOGO --}}
            <div class="col-12 col-md-auto d-flex justify-content-center">
                <div class="rounded-4 bg-light d-flex align-items-center justify-content-center shadow-lg"
                    style="width:120px;height:120px;">
                    <span class="fw-bold fs-2 text-danger">
                        {{ strtoupper(substr($business['name'], 0, 3)) }}
                    </span>
                </div>
            </div>

            {{-- CONTENT --}}
            <div class="col-12 col-md text-center text-md-start">
                <h1 class="fw-bold text-white mb-2 fs-2 fs-md-1" id="bizDisplayName">
                    {{ $business['name'] }}
                </h1>
                <span class="badge rounded-pill mb-3" id="bizIndustryText" style="background:#2e5aac;">
                    Manufacturing
                </span>

                <div class="mt-2 d-flex align-items-center gap-2" style="justify-content:center; justify-content-md:start;">
                    <span id="bizMembershipTypeText" class="badge rounded-pill" style="background:rgba(255,255,255,0.9); color:#111827; font-weight:600;">Member: N/A</span>
                    <small id="bizExpiryText" class="text-light" style="opacity:.9; margin-left:6px;">Expires: N/A</small>
                </div>

                <p class="text-light mb-4 mx-auto mx-md-0" id="bizAboutText" style="max-width:720px; opacity:.9;">
                </p>

                <div class="d-flex gap-3 flex-wrap justify-content-center justify-content-md-start">
                    <a href="tel:{{ $business['phone'] }}" class="btn btn-danger px-4 fw-bold">
                        CONTACT US
                    </a>
                    <a href="mailto:{{ $business['email'] }}" class="btn btn-outline-light px-4 fw-bold">
                        CALL NOW
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4 mt-md-5">
    <div class="row g-4 g-lg-5">

        {{-- LEFT CONTENT --}}
        <div class="col-lg-8">

            {{-- ABOUT --}}
            <div class="card border border-danger shadow-sm p-3 p-md-4 rounded-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-buildings text-danger fs-3"></i>
                    <h4 class="fw-bold text-danger mb-0">About Our Company</h4>
                </div>
                <p class="mb-0">{{ $business['about'] }}</p>
            </div>

            {{-- SERVICES --}}
            <div class="card border border-danger shadow-sm p-3 p-md-4 rounded-4 mb-4">
                <h4 class="fw-bold text-danger mb-4 d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase"></i>
                    <span>Products & Services</span>
                </h4>

                <div class="row g-3" id="biz-services">
                    @foreach ($business['services'] as $service)
                    <div class="col-12 col-sm-6 col-xl-4">
                        <div class="service-box bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 p-3 h-100 shadow-sm">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-check-circle-fill text-danger"></i>
                                <h6 class="fw-bold mb-0 text-danger">{{ $service }}</h6>
                            </div>
                            <p class="small mb-0 opacity-75">
                                Reliable solutions tailored to meet your business goals.
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- MAP --}}
            <div class="card border border-danger shadow-sm p-3 p-md-4 rounded-4 mb-4">
                <h4 class="fw-bold text-danger mb-3">Our Location</h4>
                <p class="mb-3">
                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                    <span id="bizAddressText">{{ $business['address'] }}</span>
                </p>
                <div class="rounded-3 overflow-hidden">
                    <iframe
                        src="https://maps.google.com/maps?q={{ $business['map'] }}&t=m&z=15&output=embed"
                        width="100%"
                        height="300"
                        style="border:0;"
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="col-lg-4">

            {{-- CONTACT INFO --}}
            <div class="card border border-danger shadow-sm p-4 rounded-4 mb-4">
                <h5 class="fw-bold text-danger mb-4">Contact Information</h5>
                <div class="d-flex flex-column gap-4">
                    {{-- Phone --}}
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px; flex-shrink:0;">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div class="text-truncate">
                            <small class="fw-bold text-uppercase opacity-75">Phone</small><br>
                            <span class="text-break" id="bizPhoneText">{{ $business['phone'] }}</span>
                        </div>
                    </div>
                    {{-- Email --}}
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px; flex-shrink:0;">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="text-truncate">
                            <small class="fw-bold text-uppercase opacity-75">Email</small><br>
                            <span class="text-break" id="bizEmailText">{{ $business['email'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HOURS --}}
            <div class="card border border-danger shadow-sm p-4 rounded-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-clock text-danger fs-4"></i>
                    <h5 class="fw-bold text-danger mb-0">Business Hours</h5>
                </div>
                @foreach ($business['hours'] as $day => $time)
                <div class="d-flex justify-content-between py-1 border-bottom border-light">
                    <span>{{ $day }}</span>
                    <span class="fw-bold">{{ $time }}</span>
                </div>
                @endforeach
            </div>

            {{-- DOCUMENTS --}}
            <div class="card border border-danger shadow-sm p-4 rounded-4 mb-4">
                <h5 class="fw-bold text-danger mb-4">Business Documents</h5>
                <div class="d-grid gap-2">
                    <button id="bizMayorBtn" class="btn btn-outline-danger fw-bold" type="button" onclick="viewBusinessDocument('mayors')">View Mayor Permit</button>
                    <button id="bizDtiBtn" class="btn btn-outline-danger fw-bold" type="button" onclick="viewBusinessDocument('dti')">View DTI / SEC</button>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="card border border-danger shadow-sm p-4 rounded-4 mb-5">
                <div class="d-grid gap-2">
                    <button class="btn btn-danger fw-bold">Request Quote</button>
                    <a href="{{ url('/membership') }}" class="btn btn-outline-danger fw-bold">
                        Browse Other Members
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal-overlay" id="bizDocModal" onclick="handleBizDocOverlay(event)" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:2000; justify-content:center; align-items:center; padding:20px;">
    <div style="background:#fff; width:100%; max-width:900px; border-radius:12px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25); max-height:90vh; display:flex; flex-direction:column;">
        <div style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e5e7eb;">
            <h5 id="bizDocModalTitle" style="margin:0; font-size:1rem;">Document Preview</h5>
            <button type="button" onclick="closeBizDocModal()" style="border:none; background:none; font-size:22px; cursor:pointer;">&times;</button>
        </div>
        <div style="padding:0; flex:1; min-height:220px; display:flex; align-items:center; justify-content:center; background:#f8fafc; position:relative;">
            <img id="bizDocPreviewImage" style="display:none; width:100%; height:auto; max-height:calc(90vh - 140px); object-fit:contain;" alt="Document preview">
            <iframe id="bizDocPreviewFrame" style="display:none; width:100%; height:100%; border:none;"></iframe>
            <div id="bizDocPreviewEmpty" style="display:none; padding:40px; text-align:center; color:#6b7280;">
                <i class="fa fa-file-circle-xmark" style="font-size:32px;"></i>
                <p style="margin-top:12px;">No preview available for this document.</p>
            </div>
        </div>
        <div style="padding:16px 20px; display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e5e7eb; background:#fafafa;">
            <button type="button" class="btn btn-light" onclick="closeBizDocModal()">Close</button>
            <a id="bizDocOpenLink" class="btn btn-danger" target="_blank" rel="noopener noreferrer" href="#">Open Full File</a>
        </div>
    </div>
</div>

<script>
    function setTextIfExists(id, value) {
        const el = document.getElementById(id);
        if (el && typeof value !== 'undefined' && value !== null) {
            el.innerText = value;
        }
    }

    function getNestedValue(source, path) {
        if (!source || typeof source !== 'object' || !path) return null;
        return path.split('.').reduce((current, key) => {
            if (current && typeof current === 'object' && key in current) {
                return current[key];
            }
            return null;
        }, source);
    }

    function normalizeDocumentUrl(raw) {
        if (!raw) return null;
        const trimmed = String(raw).trim();
        if (!trimmed) return null;

        if (/^(https?:|data:|blob:)/i.test(trimmed)) return trimmed;
        if (trimmed.startsWith('/')) return trimmed;
        if (trimmed.startsWith('storage/')) return '/' + trimmed;
        if (/^b2:\/\//i.test(trimmed)) {
            return trimmed.replace(/^b2:\/\//i, 'https://');
        }
        if (/^www\./i.test(trimmed)) {
            return `https://${trimmed}`;
        }
        return '/storage/' + trimmed;
    }

    function findBusinessDocumentUrl(biz, type) {
        const candidatePaths = {
            mayors: [
                'documents.mayors_permit',
                'documents.mayor_permit',
                'uploaded_documents.mayors_permit',
                'uploaded_documents.mayor_permit',
                'requirements.mayors_permit',
                'attachments.mayors_permit',
                'basic_profile.mayors_permit',
                'mayors_permit',
                'mayor_permit',
                'business_permit',
                'mayors_permit_url',
                'business_permit_url'
            ],
            dti: [
                'documents.dti_sec',
                'documents.dti_or_sec',
                'uploaded_documents.dti_sec',
                'uploaded_documents.dti_or_sec',
                'requirements.dti_sec',
                'attachments.dti_sec',
                'basic_profile.dti_sec',
                'dti_sec',
                'dti_or_sec',
                'dti',
                'sec',
                'dti_sec_url',
                'dti_url',
                'sec_url'
            ]
        };

        const paths = candidatePaths[type] || [];
        for (const path of paths) {
            const rawValue = getNestedValue(biz, path);
            const normalized = normalizeDocumentUrl(rawValue);
            if (normalized) return normalized;
        }
        return null;
    }

    function openBizDocModal(title, url) {
        const modal = document.getElementById('bizDocModal');
        const titleEl = document.getElementById('bizDocModalTitle');
        const imgEl = document.getElementById('bizDocPreviewImage');
        const frameEl = document.getElementById('bizDocPreviewFrame');
        const emptyEl = document.getElementById('bizDocPreviewEmpty');
        const openLink = document.getElementById('bizDocOpenLink');

        titleEl.innerText = title;
        imgEl.style.display = 'none';
        frameEl.style.display = 'none';
        emptyEl.style.display = 'none';
        imgEl.src = '';
        frameEl.src = '';

        if (!url) {
            emptyEl.style.display = 'block';
            openLink.style.display = 'none';
        } else {
            openLink.style.display = 'inline-flex';
            openLink.href = url;

            const isImage = /\.(png|jpe?g|gif|webp|bmp|svg)$/i.test(url);
            if (isImage) {
                imgEl.src = url;
                imgEl.style.display = 'block';
            } else {
                frameEl.src = url;
                frameEl.style.display = 'block';
            }
        }

        modal.style.display = 'flex';
    }

    function closeBizDocModal() {
        const modal = document.getElementById('bizDocModal');
        const imgEl = document.getElementById('bizDocPreviewImage');
        const frameEl = document.getElementById('bizDocPreviewFrame');
        modal.style.display = 'none';
        imgEl.src = '';
        frameEl.src = '';
    }

    function handleBizDocOverlay(event) {
        if (event.target.id === 'bizDocModal') {
            closeBizDocModal();
        }
    }

    function viewBusinessDocument(type) {
        const title = type === 'mayors' ? "Mayor's Permit" : 'DTI / SEC Document';
        const businessId = "{{ $id ?? '' }}";
        if (!businessId) {
            alert('Invalid business profile.');
            return;
        }

        const profile = window.currentBusinessProfile || {};
        const fileUrl = findBusinessDocumentUrl(profile, type);
        if (!fileUrl) {
            alert('No document available for this business profile.');
            return;
        }

        openBizDocModal(title, fileUrl);
    }

    async function loadBusinessProfile() {
        const businessId = "{{ $id ?? '' }}";
        if (!businessId) return;

        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/business/${businessId}`);
            if (!response.ok) return;

            const result = await response.json();
            const biz = result.data || result || {};
            window.currentBusinessProfile = biz;

            // === NORMALISE NESTED OBJECTS — safe JSON-string guard ===
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

            // === LOCATION — BusinessResource exposes at top-level AND inside basic_profile ===
            const locRaw = biz.business_location ?? basic.business_location;
            const loc = safeObj(locRaw);
            const address = [loc.business_address, loc.city_municipality, loc.province, loc.zip_code].filter(Boolean).join(', ');

            // === PHONE — two Applicant columns: telephone_no (landline) and rep_contact_no (mobile)
            // BusinessResource now exposes BOTH at top-level; nested paths are fallbacks.
            const phone =
                biz.telephone_no // top-level PRIMARY — BusinessResource fix
                ||
                biz.applicant?.telephone_no // flat on applicant object
                ||
                basic.telephone_no // inside basic_profile
                ||
                biz.rep_contact_no // top-level rep mobile
                ||
                biz.applicant?.rep_contact_no ||
                rep.contact_no // inside official_representative
                ||
                biz.user?.contact_number ||
                biz.contact_number ||
                biz.phone ||
                null;

            // === EMAIL ===
            const email =
                biz.email // top-level PRIMARY
                ||
                biz.applicant?.email ||
                basic.email ||
                biz.user?.email ||
                null;

            // === INDUSTRY ===
            const industry =
                bizAdditional.industry ||
                biz.industry ||
                org.type_of_company ||
                biz.type_of_company ||
                biz.business_type ||
                null;

            // === ABOUT ===
            const about =
                bizAdditional.about_description ||
                biz.about_description ||
                biz.about ||
                biz.description ||
                null;

            setTextIfExists('bizDisplayName', biz.registered_business_name || biz.name || biz.business_name || document.getElementById('bizDisplayName')?.innerText);
            setTextIfExists('bizIndustryText', industry || document.getElementById('bizIndustryText')?.innerText || 'Industry not specified');
            setTextIfExists('bizAboutText', about || document.getElementById('bizAboutText')?.innerText);
            setTextIfExists('bizEmailText', email || document.getElementById('bizEmailText')?.innerText);
            setTextIfExists('bizPhoneText', phone || document.getElementById('bizPhoneText')?.innerText);
            setTextIfExists('bizAddressText', address || document.getElementById('bizAddressText')?.innerText);

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
                    <div class="col-12 col-sm-6 col-xl-4">
                        <div class="service-box bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 p-3 h-100 shadow-sm">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <i class="bi bi-box-seam text-danger fs-4 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 text-danger">${item.title}</h6>
                                    <p class="small mb-0 opacity-75">${item.description || ''}</p>
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

            if (servicesContainer) {
                (async () => {
                    const businessId = "{{ $id ?? '' }}";
                    let products = await fetchProductsForBusiness(biz, businessId);
                    if (!products || products.length === 0) {
                        products = extractProductItems(biz);
                    }
                    renderProductCards(products, servicesContainer);
                })();
            }

            // --- Membership type & expiry (robust extractor similar to settings/dashboard) ---
            (function() {
                const memberObj = biz.member || biz.data?.member || biz;
                const typeCandidates = [
                    memberObj.membershipType?.name,
                    memberObj.membership_type?.name,
                    biz.membershipType?.name,
                    biz.membership_type?.name,
                    memberObj.applicant?.membershipType?.name,
                    memberObj.applicant?.membership_type?.name,
                    typeof biz.membership_type === 'string' ? biz.membership_type : null,
                    typeof memberObj.membership_type === 'string' ? memberObj.membership_type : null
                ];

                let memType = 'N/A';
                for (let candidate of typeCandidates) {
                    if (candidate && typeof candidate === 'string') {
                        const clean = candidate.trim();
                        const lower = clean.toLowerCase();
                        if (clean !== '' && lower !== 'n/a' && lower !== 'initial_registration' && lower !== 'renewal') {
                            memType = clean;
                            break;
                        }
                    }
                }

                const baseDate = memberObj.induction_date || memberObj.applicant?.induction_date || memberObj.created_at || biz.date_approved;
                let expiryDate = 'Pending Approval';
                if (baseDate) {
                    const d = new Date(baseDate);
                    if (!Number.isNaN(d.getTime())) {
                        d.setFullYear(d.getFullYear() + 1);
                        expiryDate = d.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    }
                }

                setTextIfExists('bizMembershipTypeText', `Member: ${memType}`);
                setTextIfExists('bizExpiryText', `Expires: ${expiryDate}`);
            })();

            const mayorUrl = findBusinessDocumentUrl(biz, 'mayors');
            const dtiUrl = findBusinessDocumentUrl(biz, 'dti');
            const mayorBtn = document.getElementById('bizMayorBtn');
            const dtiBtn = document.getElementById('bizDtiBtn');

            if (mayorBtn) {
                mayorBtn.disabled = !mayorUrl;
                mayorBtn.classList.toggle('disabled', !mayorUrl);
                mayorBtn.innerText = mayorUrl ? 'View Mayor Permit' : 'Mayor Permit unavailable';
            }
            if (dtiBtn) {
                dtiBtn.disabled = !dtiUrl;
                dtiBtn.classList.toggle('disabled', !dtiUrl);
                dtiBtn.innerText = dtiUrl ? 'View DTI / SEC' : 'DTI / SEC unavailable';
            }
        } catch (e) {
            console.error("Could not fetch business profile:", e);
        }
    }

    document.addEventListener('DOMContentLoaded', loadBusinessProfile);
</script>
@endsection