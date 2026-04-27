@extends('layouts.admin')

@section('title', 'Applicant Profile - PCCI')

@section('content')

@include('partials.api-config')

<style>
    /* ============================================== */
    /* APPLICANT PROFILE PAGE STYLES                  */
    /* ============================================== */

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

    /* --- Detail Card Container --- */
    .applicant-detail-card {
        border: 1px solid #ff0000;
        border-radius: 12px;
        padding: 0;
        position: relative;
        background: #fff;
        margin-bottom: 24px;
        display: none; /* Hidden until data loads */
    }

    .loading-container {
        text-align: center;
        padding: 50px;
        font-size: 1.2rem;
        color: #666;
        background: #fff;
        border-radius: 12px;
        border: 1px dashed #ccc;
        margin-bottom: 24px;
    }

    .applicant-detail-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 28px 12px;
        border-bottom: 1px solid #eee;
    }

    .applicant-detail-card-header h3 { font-size: 1.25rem; font-weight: 700; color: #111; margin: 0; }

    .btn-close-card {
        width: 32px; height: 32px; border: 1px solid #ff0000; border-radius: 6px;
        background: #fff; display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: #666; font-size: 1.1rem; transition: all 0.2s;
    }
    .btn-close-card:hover { background: #ffffff; border-color: #ff0000; }

    /* --- Scrollable Content Area --- */
    .applicant-detail-body { padding: 20px 28px 28px; max-height: 480px; overflow-y: auto; }
    .applicant-detail-body::-webkit-scrollbar { width: 6px; }
    .applicant-detail-body::-webkit-scrollbar-track { background: #f0f0f0; border-radius: 3px; }
    .applicant-detail-body::-webkit-scrollbar-thumb { background: #c0c0c0; border-radius: 3px; }
    .applicant-detail-body::-webkit-scrollbar-thumb:hover { background: #999; }

    /* --- Section Headings --- */
    .detail-section-title { font-size: 0.95rem; font-weight: 700; color: #222; margin-top: 20px; margin-bottom: 12px; padding-bottom: 4px; }
    .detail-section-title:first-child { margin-top: 0; }

    /* --- Field Rows --- */
    .detail-row { display: flex; flex-wrap: wrap; gap: 8px 40px; margin-bottom: 6px; }
    .detail-field { flex: 1 1 45%; min-width: 220px; font-size: 0.9rem; color: #333; padding: 3px 0; line-height: 1.5; }
    .detail-field strong { color: #555; font-weight: 600; }
    
    .detail-row-inline { display: flex; flex-wrap: wrap; gap: 8px 32px; margin-bottom: 6px; }
    .detail-row-inline .detail-field { flex: 0 1 auto; min-width: auto; }

    /* --- Action Buttons --- */
    .applicant-actions { display: none; gap: 16px; margin-top: 8px; } /* Hidden until data loads */
    .btn-approve { background-color: #1a2744; color: #fff; border: none; padding: 12px 32px; border-radius: 8px; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; letter-spacing: 0.5px; }
    .btn-approve:hover { background-color: #0f1a30; transform: translateY(-1px); }
    .btn-approve:disabled { opacity: 0.5; cursor: not-allowed; }
    .btn-reject { background-color: #7a1a2e; color: #fff; border: none; padding: 12px 32px; border-radius: 8px; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; letter-spacing: 0.5px; }
    .btn-reject:hover { background-color: #5c1020; transform: translateY(-1px); }

    /* --- Modal Styles (From Paul's branch) --- */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; justify-content: center; align-items: center; }
    .modal-content { background: #fff; padding: 30px; border-radius: 12px; border: 1px solid #ccc; width: 100%; max-width: 400px; color: #333; }
    .modal-content h3 { margin-top: 0; color: var(--pcci-red, #be1e38); font-family: 'Poppins', sans-serif; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; color: #555; font-size: 0.9rem; font-weight: bold;}
    .form-group select { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; background: #f9f9f9; color: #333; }
    .btn-modal { padding: 10px 15px; border-radius: 6px; border: none; cursor: pointer; font-weight: bold; width: 100%; margin-top: 10px; }
    .btn-modal-primary { background: #28a745; color: white; transition: 0.3s; }
    .btn-modal-secondary { background: #ccc; color: #333; transition: 0.3s; }
    .alert-error { background: rgba(255,0,0,0.1); color: #ff6b6b; border: 1px solid #ff6b6b; padding: 10px; border-radius: 6px; margin-bottom: 15px; display: none; }

    /* --- Responsive --- */
    @media (max-width: 768px) {
        .applicant-header-banner { padding: 36px 24px; font-size: 1.5rem; }
        .applicant-detail-card-header { padding: 16px 20px 10px; }
        .applicant-detail-body { padding: 16px 20px 20px; max-height: 400px; }
        .detail-field { flex: 1 1 100%; min-width: unset; }
        .detail-row-inline .detail-field { flex: 1 1 45%; min-width: 140px; }
        .applicant-actions { flex-direction: column; }
    }

    @media (max-width: 576px) {
        .applicant-header-banner {
            padding: 24px 16px;
            font-size: 1.25rem;
        }

        .applicant-detail-card-header h3 {
            font-size: 1rem;
        }

        .applicant-detail-body {
            padding: 14px 14px 16px;
            max-height: none;
        }

        .detail-row,
        .detail-row-inline {
            gap: 6px 12px;
        }

        .btn-approve,
        .btn-reject {
            width: 100%;
            padding: 11px 14px;
        }

        .modal-content {
            padding: 20px 14px;
        }
    }
</style>

{{-- ======== RED HEADER BANNER ======== --}}
<div class="applicant-header-banner">
    Applicant Profile
</div>

{{-- ======== LOADING STATE ======== --}}
<div id="loadingState" class="loading-container">
    Fetching applicant details...
</div>

{{-- ======== DETAIL CARD ======== --}}
<div id="detailCard" class="applicant-detail-card">
    <div class="applicant-detail-card-header">
        <h3 id="headerTitle">Applicant Details</h3>
        <a href="{{ route('applicants') }}" class="btn-close-card" title="Close">
            <i class="bi bi-x-lg"></i>
        </a>
    </div>

    <div class="applicant-detail-body">
        {{-- BASIC PROFILE & LOCATION --}}
        <div class="detail-section-title">Business Profile & Location</div>
        <div class="detail-row">
            <div class="detail-field"><strong>Registered Name:</strong> <span id="val-registered-name">-</span></div>
            <div class="detail-field"><strong>Address:</strong> <span id="val-address">-</span></div>
        </div>
        <div class="detail-row">
            <div class="detail-field"><strong>Trade Name:</strong> <span id="val-trade-name">-</span></div>
            <div class="detail-field"><strong>City:</strong> <span id="val-city">-</span></div>
        </div>
        <div class="detail-row">
            <div class="detail-field"><strong>Membership Type:</strong> <span id="val-membership-type">-</span></div>
            <div class="detail-field"><strong>Province:</strong> <span id="val-province">-</span></div>
        </div>
        <div class="detail-row">
            <div class="detail-field"><strong>Contact No:</strong> <span id="val-contact-no">-</span></div>
            <div class="detail-field"><strong>Region:</strong> <span id="val-region">-</span></div>
        </div>
        <div class="detail-row">
            <div class="detail-field"><strong>Email:</strong> <span id="val-email">-</span></div>
            <div class="detail-field"><strong>Zipcode:</strong> <span id="val-zip">-</span></div>
        </div>

        {{-- REPRESENTATIVE --}}
        <div class="detail-section-title">Official Representative</div>
        <div class="detail-row-inline">
            <div class="detail-field"><strong>First Name:</strong> <span id="val-rep-first">-</span></div>
            <div class="detail-field"><strong>Last Name:</strong> <span id="val-rep-last">-</span></div>
            <div class="detail-field"><strong>Designation:</strong> <span id="val-rep-designation">-</span></div>
            <div class="detail-field"><strong>Contact:</strong> <span id="val-rep-contact">-</span></div>
        </div>

        {{-- FORM OF ORGANIZATION --}}
        <div class="detail-section-title">Organization Information</div>
        <div class="detail-row">
            <div class="detail-field"><strong>Business Type:</strong> <span id="val-org-type">-</span></div>
            <div class="detail-field"><strong>Year Established:</strong> <span id="val-org-year">-</span></div>
        </div>
        <div class="detail-row">
            <div class="detail-field"><strong>SEC/DTI No:</strong> <span id="val-org-reg-no">-</span></div>
            <div class="detail-field"><strong>No. of Employees:</strong> <span id="val-org-employees">-</span></div>
        </div>
        <div class="detail-row">
            <div class="detail-field"><strong>Registration Date:</strong> <span id="val-org-date">-</span></div>
            <div class="detail-field"><strong>Current Status:</strong> <span id="val-status" style="font-weight:bold; text-transform:uppercase;">-</span></div>
        </div>
    </div>
</div>

{{-- ======== ACTION BUTTONS ======== --}}
<div id="actionButtons" class="applicant-actions">
    <button class="btn-approve" id="btnApprove" type="button" onclick="openApproveModal()">Approve</button>
    <button class="btn-reject" id="btnReject" type="button" onclick="handleReject()">Reject</button>
</div>

{{-- ======== APPROVE MODAL ======== --}}
<div id="approveModal" class="modal-overlay">
    <div class="modal-content">
        <h3>Approve Applicant</h3>
        <p style="color: #666; font-size: 0.9rem;">Assign a membership type to finalize the approval.</p>
        
        <div id="approveError" class="alert-error"></div>
        
        <form id="approveForm" onsubmit="submitApprove(event)">
            <div class="form-group">
                <label>Membership Type</label>
                {{-- Values reverted to exact strings as expected by the Admin route --}}
                <select id="approveMembershipType" required>
                    <option value="Regular">Regular</option>
                    <option value="Life">Life</option>
                    <option value="Associate">Associate</option>
                    <option value="Chapter">Chapter</option>
                </select>
            </div>
            <button type="submit" id="approveSubmitBtn" class="btn-modal btn-modal-primary">Confirm Approval</button>
            <button type="button" class="btn-modal btn-modal-secondary" onclick="closeApproveModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
    const token = localStorage.getItem('token');
    // Extract ID from the URL (e.g., /applicant/66 -> 66)
    const applicantId = window.location.pathname.split('/').pop();

    document.addEventListener('DOMContentLoaded', function() {
        if (!token) {
            window.location.href = '/login';
            return;
        }
        fetchApplicantData();
    });

    // Helper function to force HTTPS for API requests
    function getSecureApiUrl() {
        let secureApiUrl = window.API_BASE_URL || 'https://pcciv-api.onrender.com/api';
        if (secureApiUrl.includes('onrender.com') && secureApiUrl.startsWith('http://')) {
            secureApiUrl = secureApiUrl.replace('http://', 'https://');
        }
        return secureApiUrl;
    }

    async function fetchApplicantData() {
        try {
            const response = await fetch(`${getSecureApiUrl()}/v1/applicants`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });

            if (response.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
                return;
            }

            const result = await response.json();
            if (response.ok && result.data) {
                const applicant = result.data.find(app => app.id == applicantId);
                
                if (applicant) {
                    populateUI(applicant);
                } else {
                    showError('Applicant not found.');
                }
            } else {
                showError('Failed to load data.');
            }
        } catch (error) {
            console.error(error);
            showError('Network error. Please try again.');
        }
    }

    function populateUI(app) {
        const safe = (val) => val || 'N/A';
        const profile = app.basic_profile || {};
        const loc = profile.business_location || {};
        const rep = app.official_representative || {};
        const org = app.organization_membership || {};

        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('detailCard').style.display = 'block';
        document.getElementById('actionButtons').style.display = 'flex';

        document.getElementById('headerTitle').innerText = `Applicant Details: ${safe(profile.registered_business_name).toUpperCase()}`;

        document.getElementById('val-registered-name').innerText = safe(profile.registered_business_name);
        document.getElementById('val-trade-name').innerText = safe(profile.trade_name);
        document.getElementById('val-email').innerText = safe(profile.email);
        document.getElementById('val-contact-no').innerText = safe(profile.telephone_no);
        document.getElementById('val-membership-type').innerText = safe(app.membership_type);
        
        document.getElementById('val-address').innerText = safe(loc.business_address);
        document.getElementById('val-city').innerText = safe(loc.city_municipality);
        document.getElementById('val-province').innerText = safe(loc.province);
        document.getElementById('val-region').innerText = safe(loc.region);
        document.getElementById('val-zip').innerText = safe(loc.zip_code);

        document.getElementById('val-rep-first').innerText = safe(rep.first_name);
        document.getElementById('val-rep-last').innerText = safe(rep.surname);
        document.getElementById('val-rep-designation').innerText = safe(rep.designation);
        document.getElementById('val-rep-contact').innerText = safe(rep.contact_no);

        document.getElementById('val-org-type').innerText = safe(org.type_of_company);
        document.getElementById('val-org-reg-no').innerText = safe(org.registration_number);
        document.getElementById('val-org-date').innerText = safe(org.date_of_registration);
        document.getElementById('val-org-employees').innerText = safe(org.number_of_employees);
        document.getElementById('val-org-year').innerText = safe(org.year_established);

        // Status Management
        const status = safe(app.status).toLowerCase();
        const statusEl = document.getElementById('val-status');
        statusEl.innerText = safe(app.status);
        
        if (status === 'approved' || status === 'paid') {
            statusEl.style.color = '#15803d'; 
            document.getElementById('btnApprove').style.display = 'none';
        } else if (status === 'rejected' || status === 'declined') {
            statusEl.style.color = '#b91c1c'; 
            document.getElementById('btnReject').style.display = 'none';
        } else {
            statusEl.style.color = '#c2410c'; 
        }

        if (app.membership_type && app.membership_type !== 'N/A') {
            const select = document.getElementById('approveMembershipType');
            for(let i=0; i < select.options.length; i++) {
                if(select.options[i].value.toLowerCase() === app.membership_type.toLowerCase()) {
                    select.selectedIndex = i;
                    break;
                }
            }
        }
    }

    function showError(msg) {
        document.getElementById('loadingState').innerText = msg;
        document.getElementById('loadingState').style.color = '#b91c1c';
    }

    function openApproveModal() {
        document.getElementById('approveModal').style.display = 'flex';
        document.getElementById('approveError').style.display = 'none';
    }

    function closeApproveModal() {
        document.getElementById('approveModal').style.display = 'none';
    }

    // --- DIRECT ADMIN APPROVE API CALL (PUT /v1/applicants/{id}) ---
    async function submitApprove(e) {
        e.preventDefault();
        
        const membershipTypeString = document.getElementById('approveMembershipType').value;
        const btnApprove = document.getElementById('approveSubmitBtn');
        const errorDiv = document.getElementById('approveError');
        
        btnApprove.disabled = true; 
        btnApprove.innerText = 'Approving...';
        errorDiv.style.display = 'none';

        try {
            const targetUrl = `${getSecureApiUrl()}/v1/applicants/${applicantId}`;
            
            // Log exactly what we are sending for debugging
            console.log("Submitting Admin Approval to:", targetUrl);
            
            const response = await fetch(targetUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    status: "approved",
                    membership_type: membershipTypeString
                })
            });

            // Capture raw response to expose backend server errors
            const responseText = await response.text();
            let data = {};
            try { data = JSON.parse(responseText); } catch(e) { console.error("Non-JSON API Response"); }

            if (response.ok) {
                closeApproveModal();
                fetchApplicantData(); // Refresh UI instantly
            } else {
                console.error("Backend Error:", response.status, data);
                // Expose the exact API failure directly to the user
                errorDiv.innerHTML = `<strong>Backend API Error (${response.status}):</strong><br> ${data.message || responseText || 'Unknown backend failure.'}`;
                errorDiv.style.display = 'block';
            }
        } catch (err) {
            console.error("Network Catch:", err);
            errorDiv.innerText = 'Network error: ' + err.message;
            errorDiv.style.display = 'block';
        } finally {
            btnApprove.disabled = false; 
            btnApprove.innerText = 'Confirm Approval';
        }
    }

    // --- DIRECT ADMIN REJECT API CALL (PUT /v1/applicants/{id}) ---
    async function handleReject() {
        if (!confirm("Are you sure you want to reject this applicant?")) {
            return;
        }

        const btnReject = document.getElementById('btnReject');
        btnReject.disabled = true; 
        btnReject.innerText = 'Rejecting...';

        try {
            const targetUrl = `${getSecureApiUrl()}/v1/applicants/${applicantId}`;
            
            console.log("Submitting Admin Rejection to:", targetUrl);
            
            const response = await fetch(targetUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    status: "rejected"
                })
            });

            const responseText = await response.text();
            let data = {};
            try { data = JSON.parse(responseText); } catch(e) {}

            if (response.ok) {
                fetchApplicantData(); 
            } else {
                console.error("Backend Error:", response.status, data);
                alert(`Backend API Error (${response.status}): \n\n${data.message || responseText || 'Unknown backend failure.'}`);
            }
        } catch (err) {
            console.error("Network Catch:", err);
            alert('Network error: ' + err.message);
        } finally {
            btnReject.disabled = false; 
            btnReject.innerText = 'Reject';
        }
    }
</script>   

@endsection