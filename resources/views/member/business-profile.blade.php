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
                {{-- NEW: Tagline Display --}}
                <p id="bizTaglineText" class="text-white-50 fst-italic mb-2" style="font-size: 1.1rem;"></p>
                
                <span class="badge rounded-pill mb-3" id="bizIndustryText" style="background:#2e5aac;">
                    Loading...
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
                    @auth
                    <button type="button" class="btn btn-outline-warning fw-bold px-4" onclick="openEditBizModal()">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profile
                    </button>
                    @endauth
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
                <p class="mb-0" id="bizAboutTextContainer">{{ $business['about'] }}</p>
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

            {{-- MAP (Updated with ID) --}}
            <div class="card border border-danger shadow-sm p-3 p-md-4 rounded-4 mb-4">
                <h4 class="fw-bold text-danger mb-3">Our Location</h4>
                <p class="mb-3">
                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                    <span id="bizAddressText">{{ $business['address'] }}</span>
                </p>
                <div class="rounded-3 overflow-hidden">
                    <iframe id="bizMapFrame" width="100%" height="300" style="border:0;" loading="lazy"></iframe>
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
                    {{-- Website --}}
                    <div class="d-flex align-items-center gap-3" id="bizWebsiteBlock" style="display: none !important;">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px; flex-shrink:0;">
                            <i class="bi bi-globe"></i>
                        </div>
                        <div class="text-truncate">
                            <small class="fw-bold text-uppercase opacity-75">Website / Social</small><br>
                            <a href="#" target="_blank" class="text-break text-decoration-none fw-bold" id="bizWebsiteText" style="color: #111827;">Loading...</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HOURS (Updated to dynamic container) --}}
            <div class="card border border-danger shadow-sm p-4 rounded-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-clock text-danger fs-4"></i>
                    <h5 class="fw-bold text-danger mb-0">Business Hours</h5>
                </div>
                <div id="bizHoursContainer">
                    <span class="text-muted">Loading hours...</span>
                </div>
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
                    <a href="{{ url('/membership') }}" class="btn btn-outline-danger fw-bold">
                        Browse Other Members
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- EDIT BUSINESS PROFILE MODAL (Member only — auth required)  --}}
{{-- ============================================================ --}}
@auth
<div id="editBizModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:3000; justify-content:center; align-items:center; padding:20px;" onclick="if(event.target===this) closeEditBizModal()">
    <div style="background:#fff; width:100%; max-width:640px; border-radius:14px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25); max-height:90vh; display:flex; flex-direction:column;">
        {{-- Header --}}
        <div style="background:#be1e38; padding:18px 24px; display:flex; justify-content:space-between; align-items:center;">
            <h5 style="margin:0; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:1.1rem;">
                <i class="bi bi-pencil-square me-2"></i>Edit Business Profile
            </h5>
            <button type="button" onclick="closeEditBizModal()" style="border:none; background:none; color:#fff; font-size:22px; cursor:pointer; line-height:1;">×</button>
        </div>

        {{-- Body --}}
        <div style="padding:24px; overflow-y:auto; flex:1;">
            <div id="editBizAlert" style="display:none;" class="alert alert-danger mb-3"></div>

            <p class="text-muted small mb-4">Changes here will be reflected on the public business directory.</p>

            {{-- Business Name & Trade Name --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Registered Business Name</label>
                    <input type="text" id="editBizName" class="form-control" placeholder="Registered business name">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Trade Name <small class="text-muted">(Optional)</small></label>
                    <input type="text" id="editBizTrade" class="form-control" placeholder="Store or brand name">
                </div>
            </div>

            {{-- Industry & Ownership --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Business Type (Industry)</label>
                    <input type="text" id="editBizIndustry" class="form-control" placeholder="e.g. Retail, Food & Beverage">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ownership Type</label>
                    <select id="editBizOwnership" class="form-select">
                        <option value="">Select Ownership</option>
                        <option value="Corporation">Corporation</option>
                        <option value="Partnership">Partnership</option>
                        <option value="Single Proprietorship">Single Proprietorship</option>
                    </select>
                </div>
            </div>

            {{-- Tagline & Designation --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Your Designation</label>
                    <input type="text" id="editBizDesignation" class="form-control" placeholder="e.g. CEO, Manager">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Business Tagline</label>
                    <input type="text" id="editBizTagline" class="form-control" placeholder="Short tagline">
                </div>
            </div>

            {{-- About --}}
            <div class="mb-3">
                <label class="form-label fw-bold">About / Description</label>
                <textarea id="editBizAbout" class="form-control" rows="4" placeholder="Describe your business..."></textarea>
            </div>

            {{-- Phone & Website --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Business Phone</label>
                    <input type="text" id="editBizPhone" class="form-control" placeholder="02-XXXX-XXXX">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Website / Social URL</label>
                    <input type="text" id="editBizWebsite" class="form-control" placeholder="www.yourwebsite.com">
                </div>
            </div>

            {{-- Address --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Business Address</label>
                <input type="text" id="editBizAddress" class="form-control" placeholder="Street address">
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label fw-bold">City / Municipality</label>
                    <input type="text" id="editBizCity" class="form-control" placeholder="City">
                </div>
                <div class="col-6">
                    <label class="form-label fw-bold">Province</label>
                    <input type="text" id="editBizProvince" class="form-control" placeholder="Province">
                </div>
            </div>

            {{-- Business Hours --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Business Hours</label>
                <div class="input-group mb-2">
                    <span class="input-group-text" style="width:100px;">Mon - Fri</span>
                    <input type="text" id="editBizHoursMF" class="form-control" placeholder="8:00 AM - 5:00 PM">
                </div>
                <div class="input-group mb-2">
                    <span class="input-group-text" style="width:100px;">Saturday</span>
                    <input type="text" id="editBizHoursSat" class="form-control" placeholder="9:00 AM - 1:00 PM">
                </div>
                <div class="input-group">
                    <span class="input-group-text" style="width:100px;">Sunday</span>
                    <input type="text" id="editBizHoursSun" class="form-control" placeholder="Closed">
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div style="padding:16px 24px; border-top:1px solid #e5e7eb; background:#fafafa; display:flex; justify-content:flex-end; gap:10px;">
            <button type="button" class="btn btn-light fw-bold" onclick="closeEditBizModal()">Cancel</button>
            <button type="button" class="btn btn-danger fw-bold px-4" id="saveBizBtn" onclick="saveBusinessProfile()">
                <span id="saveBizBtnText">Save Changes</span>
                <span id="saveBizSpinner" class="spinner-border spinner-border-sm ms-2" style="display:none;"></span>
            </button>
        </div>
    </div>
</div>
@endauth

<div class="modal-overlay" id="bizDocModal" onclick="handleBizDocOverlay(event)" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:2000; justify-content:center; align-items:center; padding:20px;">
    <div style="background:#fff; width:100%; max-width:900px; border-radius:12px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25); max-height:90vh; display:flex; flex-direction:column;">
        <div style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e5e7eb;">
            <h5 id="bizDocModalTitle" style="margin:0; font-size:1rem;">Document Preview</h5>
            <button type="button" onclick="closeBizDocModal()" style="border:none; background:none; font-size:22px; cursor:pointer;">×</button>
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
        if (el && value && String(value).trim() !== '' && value !== 'N/A') el.innerText = String(value).trim();
    }

    function safeProfileObj(val) {
        if (!val) return {};
        if (typeof val === 'string') {
            try { return JSON.parse(val); } catch (e) { return {}; }
        }
        return (typeof val === 'object' && !Array.isArray(val)) ? val : {};
    }

    function resolveApplicantFields(raw) {
        const root = raw?.applicant || raw || {};
        const basic = safeProfileObj(root.basic_profile);
        const addData = safeProfileObj(root.business_additional_data);
        const org = safeProfileObj(root.organization_membership);
        const rep = safeProfileObj(root.official_representative);
        const loc = safeProfileObj(basic.business_location);

        return {
            registered_business_name: root.registered_business_name || basic.registered_business_name || '',
            trade_name: root.trade_name || basic.trade_name || '',
            industry: root.industry || addData.industry || '',
            about_description: root.about_description || addData.about_description || '',
            business_tagline: root.business_tagline || addData.business_tagline || '',
            website_socmed: root.website_socmed || basic.website || basic.website_socmed || '',
            type_of_company: root.type_of_company || org.type_of_company || '',
            rep_designation: root.rep_designation || rep.designation || '',
            telephone_no: root.telephone_no || basic.telephone_no || '',
            rep_contact_no: root.rep_contact_no || rep.contact_no || '',
            email: root.email || basic.email || '',
            business_address: root.business_address || loc.business_address || '',
            city_municipality: root.city_municipality || loc.city_municipality || '',
            province: root.province || loc.province || '',
            zip_code: root.zip_code || loc.zip_code || '',
            business_hours: root.business_hours || addData.business_hours || {},
        };
    }

    async function loadBusinessProfile() {
        const businessId = "{{ $id ?? '' }}";
        const token = localStorage.getItem('token');

        let fetchUrl = `${window.API_BASE_URL}/v1/business/${businessId}`;
        let options = {
            headers: {
                'Accept': 'application/json'
            }
        };

        if (!businessId) {
            if (!token) return;
            fetchUrl = `${window.API_BASE_URL}/v1/application`;
            options.headers['Authorization'] = `Bearer ${token}`;
        }

        try {
            const response = await fetch(fetchUrl, options);
            if (!response.ok) throw new Error("Failed to load profile");

            const result = await response.json();
            let rawBiz = result.data || result || {};
            const biz = rawBiz.applicant || rawBiz; // Extract flat data

            window.currentBusinessProfile = biz;

            const address = [biz.business_address, biz.city_municipality, biz.province, biz.zip_code]
                .filter(v => v && v !== 'N/A').join(', ');

            let displayName = biz.registered_business_name || null;
            if (biz.trade_name && biz.trade_name !== 'N/A') {
                displayName = displayName ? `${displayName} (${biz.trade_name})` : biz.trade_name;
            }

            setTextIfExists('bizDisplayName', displayName);
            setTextIfExists('bizIndustryText', biz.industry);
            setTextIfExists('bizAboutText', biz.about_description);
            setTextIfExists('bizAboutTextContainer', biz.about_description);
            setTextIfExists('bizEmailText', biz.email);
            setTextIfExists('bizPhoneText', biz.telephone_no || biz.rep_contact_no);
            setTextIfExists('bizAddressText', address);

            // Handle URL display
            const website = biz.website_socmed || biz.website || null;
            const webBlock = document.getElementById('bizWebsiteBlock');
            const webText = document.getElementById('bizWebsiteText');
            if (website && website !== 'N/A') {
                if (webBlock) webBlock.style.setProperty('display', 'flex', 'important');
                if (webText) {
                    webText.href = website.startsWith('http') ? website : `https://${website}`;
                    webText.innerText = website;
                }
            }

            // 1. Tagline Update
            setTextIfExists('bizTaglineText', biz.business_tagline ? `"${biz.business_tagline}"` : '');

            // 2. Map Update
            const mapQuery = biz.location_link && biz.location_link !== 'N/A' ? biz.location_link : address;
            const encodedMapQuery = encodeURIComponent(mapQuery + ', Philippines');
            const mapFrame = document.getElementById('bizMapFrame');
            if (mapFrame) {
                mapFrame.src = `http://googleusercontent.com/maps.google.com/maps?q=${encodedMapQuery}&t=m&z=15&output=embed`;
            }

            // 3. Dynamic Hours Loop
            const hoursContainer = document.getElementById('bizHoursContainer');
            if (hoursContainer) {
                hoursContainer.innerHTML = '';
                let hoursData = biz.business_hours || {};
                
                if (typeof hoursData === 'string') {
                    try { hoursData = JSON.parse(hoursData); } catch (e) { hoursData = {}; }
                }

                if (hoursData && Object.keys(hoursData).length > 0) {
                    for (const [day, time] of Object.entries(hoursData)) {
                        hoursContainer.innerHTML += `
                            <div class="d-flex justify-content-between py-1 border-bottom border-light">
                                <span>${day}</span>
                                <span class="fw-bold">${time}</span>
                            </div>
                        `;
                    }
                } else {
                    hoursContainer.innerHTML = '<span class="text-muted">Business hours not provided.</span>';
                }
            }

        } catch (e) {
            console.error("Could not fetch business profile:", e);
        }
    }

    function openEditBizModal() {
        const token = localStorage.getItem('token');
        if (!token) return;

        const biz = window.currentBusinessProfile || {};
        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.value = (val && val !== 'N/A' && val !== 'null') ? val : '';
        };

        setVal('editBizName', biz.registered_business_name);
        setVal('editBizTrade', biz.trade_name);
        setVal('editBizTagline', biz.business_tagline);
        setVal('editBizAbout', biz.about_description);
        setVal('editBizIndustry', biz.industry);
        setVal('editBizWebsite', biz.website_socmed);
        setVal('editBizOwnership', biz.type_of_company);
        setVal('editBizDesignation', biz.rep_designation);

        let hours = biz.business_hours || {};
        if (typeof hours === 'string') {
            try {
                hours = JSON.parse(hours);
            } catch (e) {
                hours = {};
            }
        }

        setVal('editBizHoursMF', hours['Monday - Friday']);
        setVal('editBizHoursSat', hours['Saturday']);
        setVal('editBizHoursSun', hours['Sunday']);

        setVal('editBizPhone', biz.telephone_no || biz.rep_contact_no);
        setVal('editBizAddress', biz.business_address);
        setVal('editBizCity', biz.city_municipality);
        setVal('editBizProvince', biz.province);

        document.getElementById('editBizAlert').style.display = 'none';
        document.getElementById('editBizModal').style.display = 'flex';
    }

    function closeEditBizModal() {
        document.getElementById('editBizModal').style.display = 'none';
    }

    async function saveBusinessProfile() {
        const token = localStorage.getItem('token');
        const btn = document.getElementById('saveBizBtn');
        const alert = document.getElementById('editBizAlert');
        btn.disabled = true;
        alert.style.display = 'none';

        const getVal = id => document.getElementById(id)?.value.trim() || '';

        const payload = {
            registered_business_name: getVal('editBizName'),
            trade_name: getVal('editBizTrade'),
            business_tagline: getVal('editBizTagline'),
            about_description: getVal('editBizAbout'),
            industry: getVal('editBizIndustry'),
            website_socmed: getVal('editBizWebsite'),
            type_of_company: getVal('editBizOwnership'),
            rep_designation: getVal('editBizDesignation'),
            telephone_no: getVal('editBizPhone'),
            business_address: getVal('editBizAddress'),
            city_municipality: getVal('editBizCity'),
            province: getVal('editBizProvince'),
            business_hours: {
                "Monday - Friday": getVal('editBizHoursMF') || "Closed",
                "Saturday": getVal('editBizHoursSat') || "Closed",
                "Sunday": getVal('editBizHoursSun') || "Closed"
            }
        };

        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/application`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload),
            });
            const result = await response.json();

            if (response.ok) {
                window.location.reload();
            } else {
                alert.textContent = result.message || 'Error saving.';
                if (result.errors) alert.textContent += ' ' + Object.values(result.errors).flat().join(' ');
                alert.style.display = 'block';
            }
        } catch (e) {
            alert.textContent = 'Network error while saving.';
            alert.style.display = 'block';
        } finally {
            btn.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', loadBusinessProfile);
</script>
@endsection 