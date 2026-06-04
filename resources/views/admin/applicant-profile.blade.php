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
        display: none;
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

    .applicant-detail-card-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111;
        margin: 0;
    }

    .btn-close-card {
        width: 32px;
        height: 32px;
        border: 1px solid #ff0000;
        border-radius: 6px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #666;
        font-size: 1.1rem;
        transition: all 0.2s;
    }

    .btn-close-card:hover {
        background: #ffffff;
        border-color: #ff0000;
    }

    /* --- Scrollable Content Area --- */
    .applicant-detail-body {
        padding: 20px 28px 28px;
        max-height: 480px;
        overflow-y: auto;
    }

    .applicant-detail-body::-webkit-scrollbar {
        width: 6px;
    }

    .applicant-detail-body::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 3px;
    }

    .applicant-detail-body::-webkit-scrollbar-thumb {
        background: #c0c0c0;
        border-radius: 3px;
    }

    .applicant-detail-body::-webkit-scrollbar-thumb:hover {
        background: #999;
    }

    /* --- Section Headings --- */
    .detail-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #222;
        margin-top: 20px;
        margin-bottom: 12px;
        padding-bottom: 4px;
    }

    .detail-section-title:first-child {
        margin-top: 0;
    }

    /* --- Field Rows --- */
    .detail-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 40px;
        margin-bottom: 6px;
    }

    .detail-field {
        flex: 1 1 45%;
        min-width: 220px;
        font-size: 0.9rem;
        color: #333;
        padding: 3px 0;
        line-height: 1.5;
    }

    .detail-field strong {
        color: #555;
        font-weight: 600;
    }

    .detail-row-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 32px;
        margin-bottom: 6px;
    }

    .detail-row-inline .detail-field {
        flex: 0 1 auto;
        min-width: auto;
    }

    /* --- Action Buttons --- */
    .applicant-actions {
        display: none;
        gap: 16px;
        margin-top: 8px;
    }

    .btn-approve {
        background-color: #1a2744;
        color: #fff;
        border: none;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        letter-spacing: 0.5px;
    }

    .btn-approve:hover {
        background-color: #0f1a30;
        transform: translateY(-1px);
    }

    .btn-approve:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-reject {
        background-color: #7a1a2e;
        color: #fff;
        border: none;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        letter-spacing: 0.5px;
    }

    .btn-reject:hover {
        background-color: #5c1020;
        transform: translateY(-1px);
    }

    /* --- Modal Styles --- */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        border: 1px solid #ccc;
        width: 100%;
        max-width: 400px;
        color: #333;
    }

    .modal-content h3 {
        margin-top: 0;
        color: var(--pcci-red, #be1e38);
        font-family: 'Poppins', sans-serif;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #555;
        font-size: 0.9rem;
        font-weight: bold;
    }

    .form-group select {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        background: #f9f9f9;
        color: #333;
    }

    .btn-modal {
        padding: 10px 15px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: bold;
        width: 100%;
        margin-top: 10px;
    }

    .btn-modal-primary {
        background: #28a745;
        color: white;
        transition: 0.3s;
    }

    .btn-modal-secondary {
        background: #ccc;
        color: #333;
        transition: 0.3s;
    }

    .alert-error {
        background: rgba(255, 0, 0, 0.1);
        color: #ff6b6b;
        border: 1px solid #ff6b6b;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 15px;
        display: none;
    }

    /* --- Responsive --- */
    @media (max-width: 768px) {
        .applicant-header-banner {
            padding: 36px 24px;
            font-size: 1.5rem;
        }

        .applicant-detail-card-header {
            padding: 16px 20px 10px;
        }

        .applicant-detail-body {
            padding: 16px 20px 20px;
            max-height: 400px;
        }

        .detail-field {
            flex: 1 1 100%;
            min-width: unset;
        }

        .detail-row-inline .detail-field {
            flex: 1 1 45%;
            min-width: 140px;
        }

        .applicant-actions {
            flex-direction: column;
        }
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
    <i class="fa fa-spinner fa-spin mb-3" style="font-size: 2rem; color: #be1e38;"></i>
    <br>Fetching applicant details...
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
    <button id="btnReject" class="btn btn-danger" onclick="openApplicantRejectModal()">Reject</button>
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

{{-- ======== REJECT MODAL ======== --}}
<div class="modal-overlay" id="applicantRejectModal" style="display: none; background: rgba(0, 0, 0, 0.6); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; justify-content: center; align-items: center;">
    <div class="modal-content-box" style="background: #fff; width: 90%; max-width: 500px; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">

        <div class="modal-header" style="background: #be1e38; color: #fff; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h5 style="margin: 0; font-weight: 600; font-size: 16px;"><i class="fa fa-times-circle me-2"></i> Reject Applicant</h5>
            <button type="button" style="background: none; border: none; color: #fff; font-size: 20px; cursor: pointer;" onclick="closeApplicantRejectModal()">&times;</button>
        </div>

        <div class="modal-body" style="padding: 24px;">
            <p class="text-muted mb-3" style="font-size: 14px;">Please provide a reason for rejecting this application. This reason will be included in the email sent to the applicant.</p>
            <textarea id="applicantRejectionReason" class="form-control" rows="4" placeholder="Enter specific rejection reason here... (Required)" style="resize: none; border-radius: 8px; border: 1px solid #d1d5db; padding: 12px; width: 100%; font-size: 14px;"></textarea>
        </div>

        <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 10px; background: #fdfdfd;">
            <button type="button" class="btn btn-light fw-bold shadow-sm" style="border: 1px solid #d1d5db;" onclick="closeApplicantRejectModal()">Cancel</button>
            <button type="button" class="btn btn-danger fw-bold shadow-sm" id="submitApplicantRejectBtn" onclick="submitApplicantRejection()">Confirm Rejection</button>
        </div>

    </div>
</div>

<script>
    const token = localStorage.getItem('token');

    // SAFE ID EXTRACTION
    const pathParts = window.location.pathname.split('/').filter(Boolean);
    const applicantId = pathParts[pathParts.length - 1];

    function getSecureApiUrl() {
        let url = window.API_BASE_URL || '/api';
        return url.endsWith('/') ? url.slice(0, -1) : url;
    }

    // INSTANT FETCH LOGIC (Removed the 2-second retry loop)
    async function fetchApplicantData() {
        if (!applicantId || isNaN(applicantId)) {
            console.warn("Skipping fetch: Not a valid applicant ID profile view.");
            return;
        }

        try {
            // Reset to loading state immediately
            document.getElementById('loadingState').style.display = 'block';
            document.getElementById('loadingState').innerHTML = '<i class="fa fa-spinner fa-spin mb-3" style="font-size: 2rem; color: #be1e38;"></i><br>Fetching applicant details...';
            document.getElementById('loadingState').style.color = '#666';
            document.getElementById('detailCard').style.display = 'none';
            document.getElementById('actionButtons').style.display = 'none';

            let response = await fetch(`${getSecureApiUrl()}/v1/applicants/${applicantId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.status === 401) {
                localStorage.removeItem('token');
                return window.location.href = '/login';
            }

            let applicant = null;

            if (response.ok) {
                const result = await response.json();
                applicant = result.data || result;
            } else if (response.status === 404) {
                console.warn("Direct fetch missing, falling back to full list...");
                // Added ?all=true to bypass pagination issues causing the data to be hidden
                const fallbackResponse = await fetch(`${getSecureApiUrl()}/v1/applicants?all=true`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (fallbackResponse.ok) {
                    const result = await fallbackResponse.json();
                    const list = Array.isArray(result.data) ? result.data : (Array.isArray(result) ? result : []);
                    applicant = list.find(app => String(app.id) === String(applicantId));
                }
            }

            if (applicant && applicant.id) {
                populateUI(applicant);
            } else {
                showError('Applicant record could not be found. It may have been rejected and filtered from the active list.');
            }
        } catch (error) {
            console.error("Fetch Error:", error);
            showError('Network error while fetching data.');
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

        // Ensure buttons are reset to visible before applying logic
        const btnApprove = document.getElementById('btnApprove');
        const btnReject = document.getElementById('btnReject');
        if (btnApprove) btnApprove.style.display = 'inline-block';
        if (btnReject) btnReject.style.display = 'inline-block';

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
            if (btnApprove) btnApprove.style.display = 'none';
        } else if (status === 'rejected' || status === 'declined') {
            statusEl.style.color = '#b91c1c';
            if (btnReject) btnReject.style.display = 'none';
        } else {
            statusEl.style.color = '#c2410c';
        }

        if (app.membership_type && app.membership_type !== 'N/A') {
            const select = document.getElementById('approveMembershipType');
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value.toLowerCase() === app.membership_type.toLowerCase()) {
                    select.selectedIndex = i;
                    break;
                }
            }
        }
    }

    function showError(msg) {
        // Displays error cleanly while hiding the empty card
        const loadingState = document.getElementById('loadingState');
        loadingState.innerText = msg;
        loadingState.style.color = '#b91c1c';
        loadingState.style.display = 'block';

        document.getElementById('detailCard').style.display = 'none';
        document.getElementById('actionButtons').style.display = 'none';
    }

    // Modal Control Functions
    function openApproveModal() {
        document.getElementById('approveModal').style.display = 'flex';
        document.getElementById('approveError').style.display = 'none';
    }

    function closeApproveModal() {
        document.getElementById('approveModal').style.display = 'none';
    }

    function openApplicantRejectModal() {
        document.getElementById('applicantRejectionReason').value = '';
        document.getElementById('applicantRejectModal').style.display = 'flex';
    }

    function closeApplicantRejectModal() {
        document.getElementById('applicantRejectModal').style.display = 'none';
    }

    // --- DIRECT ADMIN APPROVE API CALL ---
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

            const responseText = await response.text();
            let data = {};
            try {
                data = JSON.parse(responseText);
            } catch (e) {}

            if (response.ok) {
                closeApproveModal();
                fetchApplicantData(); // Refresh UI instantly
            } else {
                console.error("Backend Error:", response.status, data);
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

    // --- DIRECT ADMIN REJECT API CALL ---
    async function submitApplicantRejection() {
        const reason = document.getElementById('applicantRejectionReason').value.trim();

        if (!reason) {
            alert('Please enter a rejection reason.');
            return;
        }

        if (!confirm('Are you sure you want to reject this applicant? An email will be sent to them automatically.')) {
            return;
        }

        const btnReject = document.getElementById('submitApplicantRejectBtn');
        const originalText = btnReject.innerText;
        btnReject.disabled = true;
        btnReject.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Rejecting...';

        try {
            const targetUrl = `${getSecureApiUrl()}/v1/applicants/${applicantId}/reject`;

            const response = await fetch(targetUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    rejection_reason: reason
                })
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                alert('Success: Applicant rejected and notification email sent.');
                closeApplicantRejectModal();
                fetchApplicantData(); // Instant Refresh
            } else {
                console.error("Backend Error:", response.status, data);
                alert(`Error: ${data.message || 'Unknown backend failure.'}`);
            }
        } catch (err) {
            console.error("Network Catch:", err);
            alert('Network error: ' + err.message);
        } finally {
            btnReject.disabled = false;
            btnReject.innerText = originalText;
        }
    }

    // Initialize page
    if (document.readyState !== 'loading') {
        fetchApplicantData();
    } else {
        document.addEventListener('DOMContentLoaded', fetchApplicantData);
    }
</script>

@endsection