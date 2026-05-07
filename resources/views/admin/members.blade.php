@extends('layouts.admin')
@include('partials.api-config')

@section('title', 'Members - PCCI')

@section('content')
<style>
    /* ============================================== */
    /* MEMBERS LISTING PAGE                           */
    /* ============================================== */

    .members-header-banner {
        background-color: var(--pcci-red);
        color: #fff;
        padding: 30px 40px; 
        border-radius: 10px;
        font-size: 1.8rem;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 25px;
        letter-spacing: 1px;
    }

    /* --- Search & Add Row --- */
    .members-toolbar {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding: 10px 15px;
        background: #fdfdfd;
        border-radius: 8px;
        border: 1px solid #eee;
    }

    .search-box {
        flex: 1;
        position: relative;
    }

    .search-box input {
        width: 100%;
        padding: 11px 4px 11px 40px; 
        border: 1.5px solid #ddd;
        border-radius: 8px;
        font-size: 0.92rem;
        color: #333;
        font-family: 'Inter', sans-serif;
        background: #fff;
        outline: none;
        transition: all 0.25s ease;
    }

    .search-box input::placeholder {
        color: #aaa;
        font-size: 0.9rem;
        font-style: italic;
    }

    .search-box input:focus {
        border-color: var(--pcci-red);
        box-shadow: 0 0 0 3px rgba(190, 30, 56, 0.08);
    }

    .search-box .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 1.05rem;
        pointer-events: none;
    }

    .btn-add-new {
        background-color: var(--pcci-red);
        color: #fff;
        border: none;
        padding: 11px 26px;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.25s ease;
        letter-spacing: 0.3px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(190, 30, 56, 0.25);
    }

    .btn-add-new:hover {
        background-color: #9a0a28;
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(190, 30, 56, 0.35);
    }

    /* --- Table Container (UPDATED FOR HORIZONTAL SCROLLING) --- */
    .members-table-wrapper {
        border: 2px solid var(--pcci-red);
        border-radius: 12px;
        overflow-x: auto; /* Allows horizontal scrolling */
        -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
    }

    .members-table {
        width: 100%;
        min-width: 1100px; /* Forces scrolling on screens smaller than 1100px */
        border-collapse: collapse;
        font-size: 0.88rem;
        margin-bottom: 0;
    }

    /* --- Table Header --- */
    .members-table thead th {
        background-color: var(--pcci-red);
        color: #fff;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 14px 12px;
        white-space: nowrap;
        border-right: 1px solid rgba(255,255,255,0.15);
        text-align: center;
    }

    .members-table thead th:last-child {
        border-right: none;
    }

    .sort-icon {
        font-size: 0.7rem;
        margin-left: 4px;
        opacity: 0.8;
        vertical-align: middle;
    }

    /* --- Table Body --- */
    .members-table tbody tr {
        border-bottom: 1px solid #e8e8e8;
        transition: background-color 0.15s;
    }

    .members-table tbody tr:last-child {
        border-bottom: none;
    }

    .members-table tbody tr:hover {
        background-color: #fff5f6;
    }

    .members-table tbody td {
        padding: 14px 12px;
        color: #333;
        vertical-align: middle;
        border-right: 1px solid #f0f0f0;
        text-align: center; 
    }

    .members-table tbody td:last-child {
        border-right: none;
    }

    /* --- NEW UI CSS ADDITIONS --- */
    .status-inactive {
        background-color: #ff9800 !important;
        color: white !important;
        font-weight: bold;
    }
    
    .icon-add {
        font-size: 0.85rem;
        cursor: pointer;
        opacity: 0.6;
        transition: opacity 0.2s;
        color: #0d6efd;
    }
    
    .icon-add:hover {
        opacity: 1;
    }

    /* --- Pagination Bar --- */
    .members-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-top: 2px solid var(--pcci-red);
        background: #fff;
        font-size: 0.85rem;
        color: #555;
    }

    .pagination-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pagination-left label {
        font-weight: 500;
        color: #555;
    }

    .pagination-left select {
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 0.85rem;
        color: #333;
        outline: none;
        cursor: pointer;
    }

    .pagination-center {
        font-weight: 500;
        color: #555;
    }

    .pagination-right {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .pagination-btn {
        width: 32px;
        height: 32px;
        border: 1px solid #ccc;
        border-radius: 6px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #555;
        font-size: 0.8rem;
        transition: all 0.15s;
    }

    .pagination-btn:hover {
        background: #f5f5f5;
        border-color: var(--pcci-red);
        color: var(--pcci-red);
    }

    .pagination-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* MODAL CSS */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 15, 20, 0.55); z-index: 9999; align-items: center; justify-content: center; }
    .modal-overlay.active { display: flex; }
    .modal-card { background: #fff; border-radius: 16px; width: 100%; max-width: 620px; margin: 20px; box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3); display: flex; flex-direction: column; overflow: hidden; max-height: 90vh; }
    .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 28px 16px; border-bottom: 1px solid #eee; }
    .modal-body { padding: 24px 32px; overflow-y: auto; flex: 1; }
    .modal-footer { padding: 16px 28px; border-top: 1px solid #f0f0f0; background: #fafafa; display: flex; justify-content: flex-end; gap: 12px; }
    .modal-field { margin-bottom: 20px; }
    .modal-field label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
    .modal-field input, .modal-field select { width: 100%; padding: 10px 14px; border: 1.5px solid #ddd; border-radius: 8px; }
</style>

{{-- ======== RED HEADER BANNER ======== --}}
<div class="members-header-banner">
    <i class="fa fa-users me-3"></i> MEMBERS
</div>

{{-- ======== SEARCH BAR + FILTERS + ADD NEW ======== --}}
<div class="members-toolbar">
    <div class="search-box">
        <i class="bi bi-search search-icon"></i>
        <input type="text" placeholder="Search members by name, email, or ID . . ." id="memberSearchInput">
    </div>

    <div class="d-flex align-items-center">
        <span class="fw-bold text-muted small me-2 text-uppercase">Status:</span>
        <select class="form-select border fw-bold text-dark fs-sm" id="memberStatusFilter" style="width: 140px; cursor: pointer;">
            <option value="all" selected>All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <button class="btn-add-new" type="button"><i class="bi bi-plus-lg"></i> Add New Member</button>
</div>

{{-- ======== TABLE ======== --}}
<div class="members-table-wrapper">
    <table class="members-table">
        <thead>
            <tr>
                <th class="text-start">Company Name <i class="bi bi-arrow-down-up sort-icon"></i></th>
                <th>Member Type</th>
                <th>Current Status</th>
                <th>Business Address</th>
                <th>Email</th>
                <th>Contact No. <i class="bi bi-arrow-down-up sort-icon"></i></th>
                <th>Registered Member</th>
                <th>Registration date <i class="bi bi-arrow-down sort-icon"></i></th>
            </tr>
        </thead>
        <tbody id="membersTableBody">
            <tr>
                <td colspan="8" style="text-align: center; padding: 30px; color: #888;">
                    <i class="bi bi-arrow-repeat" style="display:inline-block; animation: spin 1s linear infinite;"></i> Loading members...
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ======== PAGINATION ======== --}}
    <div class="members-pagination">
        <div class="pagination-left">
            <label>Rows per page</label>
            <select id="rowsPerPageSelect">
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
        <div class="pagination-center">
            Page 1 of 1
        </div>
        <div class="pagination-right">
            <button class="pagination-btn disabled" id="firstPageBtn" title="First" type="button"><i class="bi bi-chevron-double-left"></i></button>
            <button class="pagination-btn disabled" id="prevPageBtn" title="Previous" type="button"><i class="bi bi-chevron-left"></i></button>
            <button class="pagination-btn disabled" id="nextPageBtn" title="Next" type="button"><i class="bi bi-chevron-right"></i></button>
            <button class="pagination-btn disabled" id="lastPageBtn" title="Last" type="button"><i class="bi bi-chevron-double-right"></i></button>
        </div>
    </div>
</div>

{{-- ======== ADD NEW MEMBER MODAL ======== --}}
<div class="modal-overlay" id="addMemberModal">
    <div class="modal-card">
        <div class="modal-header">
            <div class="modal-header-left">
                <h3 class="m-0 fw-bold"><i class="bi bi-person-plus-fill text-danger me-2"></i> Create New Member</h3>
            </div>
            <button class="modal-close-btn" id="closeModal" type="button">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-field">
                <label>Eligible Applicant <span class="text-danger">*</span></label>
                <select id="addMemberCompanySelect">
                    <option value="">Loading eligible applicants . . .</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 modal-field">
                    <label>Member Type</label>
                    <input type="text" id="addMemberType" value="Member" readonly style="background: #e9ecef;">
                </div>
                <div class="col-md-6 modal-field">
                    <label>Company Name</label>
                    <input type="text" id="addMemberCompanyNameReadOnly" readonly style="background: #e9ecef;">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 modal-field">
                    <label>Email Address</label>
                    <input type="email" id="addMemberEmail" readonly style="background: #e9ecef;">
                </div>
                <div class="col-md-6 modal-field">
                    <label>Induction Date <span class="text-danger">*</span></label>
                    <input type="date" id="addMemberInductionDate">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary px-4" id="cancelModal" type="button">Cancel</button>
            <button class="btn btn-danger px-4 fw-bold" id="saveMemberBtn" type="button" onclick="saveMemberFromModal()">
                <i class="bi bi-check-lg"></i> Add Member
            </button>
        </div>
    </div>
</div>

<script>
    // ==============================================
    // MODAL UI LOGIC 
    // ==============================================
    document.querySelector('.btn-add-new').addEventListener('click', function() {
        document.getElementById('addMemberModal').classList.add('active');
    });

    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('addMemberModal').classList.remove('active');
    });

    document.getElementById('cancelModal').addEventListener('click', function() {
        document.getElementById('addMemberModal').classList.remove('active');
    });

    document.getElementById('addMemberModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });

    // ==============================================
    // API AND DATA LOGIC
    // ==============================================
    let allMembersData = [];
    let approvedApplicantsForModal = [];
    let currentPage = 1;
    let rowsPerPage = 10;
    let currentSearchTerm = '';
    const ADMIN_MEMBERS_AUTO_REFRESH_MS = 15000;

    document.addEventListener('DOMContentLoaded', function() {
        fetchMembers();

        setInterval(() => {
            if (document.visibilityState !== 'visible') return;
            fetchMembers();
        }, ADMIN_MEMBERS_AUTO_REFRESH_MS);

        // --- Event Listeners for Filters & Pagination ---
        document.getElementById('rowsPerPageSelect').addEventListener('change', function() {
            rowsPerPage = Number(this.value) || 10;
            currentPage = 1;
            renderMembers(currentSearchTerm);
        });

        document.getElementById('memberSearchInput').addEventListener('input', function() {
            currentSearchTerm = this.value.trim().toLowerCase();
            currentPage = 1;
            renderMembers(currentSearchTerm);
        });

        document.getElementById('memberStatusFilter').addEventListener('change', function() {
            currentPage = 1;
            renderMembers(currentSearchTerm);
        });

        document.getElementById('firstPageBtn').addEventListener('click', function() {
            if (this.classList.contains('disabled')) return;
            currentPage = 1;
            renderMembers(currentSearchTerm);
        });

        document.getElementById('prevPageBtn').addEventListener('click', function() {
            if (this.classList.contains('disabled')) return;
            currentPage -= 1;
            renderMembers(currentSearchTerm);
        });

        document.getElementById('nextPageBtn').addEventListener('click', function() {
            if (this.classList.contains('disabled')) return;
            currentPage += 1;
            renderMembers(currentSearchTerm);
        });

        document.getElementById('lastPageBtn').addEventListener('click', function() {
            if (this.classList.contains('disabled')) return;
            const filtered = getFilteredMembers(currentSearchTerm);
            currentPage = Math.max(1, Math.ceil(filtered.length / rowsPerPage));
            renderMembers(currentSearchTerm);
        });

        // Modal Dropdown listener
        const companySelect = document.getElementById('addMemberCompanySelect');
        if (companySelect) {
            companySelect.addEventListener('change', function () {
                const selectedApplicant = approvedApplicantsForModal.find((item) => String(item.id) === String(this.value));
                if(selectedApplicant) {
                    document.getElementById('addMemberCompanyNameReadOnly').value = selectedApplicant.basic_profile?.registered_business_name || '';
                    document.getElementById('addMemberEmail').value = selectedApplicant.basic_profile?.email || '';
                }
            });
        }
    });

    // --- FETCH MEMBERS CORE LOGIC ---
    async function fetchMembers() {
        const tbody = document.getElementById('membersTableBody');
        const token = localStorage.getItem('token');

        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/members`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) throw new Error("Failed to fetch members");

            const data = await response.json();

            allMembersData = data.data || [];
            
            if(allMembersData.length > 0 && document.getElementById('membersTableBody').innerHTML.includes('Loading')) {
                currentPage = 1;
                currentSearchTerm = '';
                document.getElementById('memberSearchInput').value = '';
            }

            fetchTreasurerApprovedApplicantsForModal();
            renderMembers(currentSearchTerm);

        } catch (error) {
            console.error("Error loading members:", error);
            tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; color: red; padding: 20px;">Failed to load members from database.</td></tr>`;
        }
    }

    // --- RENDER TABLE & INJECT NEW UI ---
    function renderMembers(searchTerm) {
        const tbody = document.getElementById('membersTableBody');
        tbody.innerHTML = '';

        const filtered = getFilteredMembers(searchTerm);
        const totalPages = Math.max(1, Math.ceil(filtered.length / rowsPerPage));

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        const startIndex = (currentPage - 1) * rowsPerPage;
        const pageItems = filtered.slice(startIndex, startIndex + rowsPerPage);

        updatePaginationUI(filtered.length, totalPages);

        if (filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 20px;">No members found matching your criteria.</td></tr>`;
            return;
        }

        pageItems.forEach(member => {
            const applicant = member.applicant || {};
            const profile = applicant.basic_profile || {};
            const loc = profile.business_location || {};
            const rep = applicant.official_representative || {};

            const companyName = profile.registered_business_name || 'N/A';
            const email = profile.email || 'N/A';
            
            // Grab raw status and uppercase it for display
            const rawStatus = String(member.status || 'Active').toLowerCase();
            const statusDisplay = rawStatus.toUpperCase();
            
            const memberType = member.membership_type_id === 1 ? 'Directory Member' : 'Regular Member';

            // Missing Data Checks for Plus Icons
            const addressRaw = [loc.business_address, loc.city_municipality, loc.province].filter(Boolean).join(', ');
            const addressContent = addressRaw ? addressRaw : '<i class="fa fa-plus icon-add" title="Add Address"></i>';
            
            const contactRaw = profile.telephone_no || rep.contact_no;
            const contactContent = contactRaw ? contactRaw : '<i class="fa fa-plus icon-add" title="Add Contact"></i>';
            
            const repRaw = [rep.first_name, rep.mid_name, rep.surname].filter(Boolean).join(' ');
            const registeredMemberContent = repRaw ? repRaw : '<i class="fa fa-plus icon-add" title="Add Member"></i>';

            const regDateContent = member.created_at ? new Date(member.created_at).toLocaleDateString('en-US') : '<i class="fa fa-plus icon-add" title="Add Date"></i>';

            // FIX: Check against the raw lowercase status for the color coding
            let statusBadge = `<span class="badge bg-success text-uppercase rounded-pill shadow-sm py-1 px-3">${statusDisplay}</span>`;
            
            if (rawStatus === 'inactive' || rawStatus === 'expired') {
                statusBadge = `<span class="badge text-uppercase rounded-pill shadow-sm py-1 px-3 status-inactive">${statusDisplay}</span>`;
            } else if (rawStatus === 'pending') {
                statusBadge = `<span class="badge bg-warning text-dark text-uppercase rounded-pill shadow-sm py-1 px-3">${statusDisplay}</span>`;
            }

            tbody.innerHTML += `
                <tr class="align-middle">
                    <td class="fw-bold text-start text-dark">${companyName}</td>
                    <td class="text-secondary text-capitalize">${memberType}</td>
                    <td>${statusBadge}</td>
                    <td class="text-secondary">${addressContent}</td>
                    <td class="text-primary">${email}</td>
                    <td class="text-secondary">${contactContent}</td>
                    <td class="text-secondary text-capitalize">${registeredMemberContent}</td>
                    <td class="text-secondary">${regDateContent}</td>
                </tr>
            `;
        });
    }

    // --- FILTER LOGIC ---
    function getFilteredMembers(searchTerm) {
        const statusFilter = document.getElementById('memberStatusFilter').value.toLowerCase();

        return allMembersData.filter(member => {
            const status = (member.status || '').toLowerCase();

            if (!['paid', 'approved', 'active', 'inactive'].includes(status)) {
                return false;
            }

            if (statusFilter !== 'all' && status !== statusFilter) {
                return false;
            }

            if (!searchTerm) return true;

            const applicant = member.applicant || {};
            const profile = applicant.basic_profile || {};
            const rep = applicant.official_representative || {};
            
            const companyName = (profile.registered_business_name || '').toLowerCase();
            const email = (profile.email || '').toLowerCase();
            const repName = [rep.first_name, rep.surname].filter(Boolean).join(' ').toLowerCase();

            return companyName.includes(searchTerm) || email.includes(searchTerm) || repName.includes(searchTerm);
        });
    }

    // --- PAGINATION UI CONTROLLER ---
    function updatePaginationUI(totalItems, totalPages) {
        const firstBtn = document.getElementById('firstPageBtn');
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');
        const lastBtn = document.getElementById('lastPageBtn');
        const center = document.querySelector('.pagination-center');

        if (center) {
            if (totalItems === 0) {
                center.textContent = 'Page 0 of 0';
            } else {
                center.textContent = `Page ${currentPage} of ${totalPages}`;
            }
        }

        const isFirst = currentPage <= 1 || totalItems === 0;
        const isLast = currentPage >= totalPages || totalItems === 0;

        [firstBtn, prevBtn].forEach(btn => btn?.classList.toggle('disabled', isFirst));
        [nextBtn, lastBtn].forEach(btn => btn?.classList.toggle('disabled', isLast));
    }

    // --- FETCH MODAL DROPDOWN ---
    async function fetchTreasurerApprovedApplicantsForModal() {
        const companySelect = document.getElementById('addMemberCompanySelect');
        const token = localStorage.getItem('token');
        if (!companySelect) return;

        try {
            const [membersRes, paidRes, approvedRes] = await Promise.all([
                fetch(`${window.API_BASE_URL}/v1/members`, { headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` } }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=paid`, { headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` } }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=approved`, { headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` } })
            ]);

            const membersData = await membersRes.json();
            const paidData = await paidRes.json();
            const approvedData = await approvedRes.json();

            const freshMembers = Array.isArray(membersData.data) ? membersData.data : [];
            const combinedApplicants = [...(paidData.data || []), ...(approvedData.data || [])];

            const existingMemberEmails = new Set(freshMembers.map((m) => String(m?.applicant?.basic_profile?.email || '').trim().toLowerCase()).filter(Boolean));

            approvedApplicantsForModal = combinedApplicants.filter((app) => {
                const email = String(app?.basic_profile?.email || '').trim().toLowerCase();
                return !(email && existingMemberEmails.has(email));
            });

            companySelect.innerHTML = '<option value="">Select eligible company . . .</option>';
            approvedApplicantsForModal.forEach((app) => {
                const name = app?.basic_profile?.registered_business_name || `Applicant #${app.id}`;
                companySelect.insertAdjacentHTML('beforeend', `<option value="${app.id}">${name}</option>`);
            });

            if (approvedApplicantsForModal.length === 0) {
                companySelect.innerHTML = '<option value="">No eligible companies available</option>';
            }
        } catch (error) {
            console.error('Modal fetch error:', error);
        }
    }

    // --- SAVE NEW MEMBER API CALL ---
    async function saveMemberFromModal() {
        const companySelect = document.getElementById('addMemberCompanySelect');
        const inductionDateInput = document.getElementById('addMemberInductionDate');
        const saveBtn = document.getElementById('saveMemberBtn');
        const token = localStorage.getItem('token');

        const selectedApplicant = approvedApplicantsForModal.find((item) => String(item.id) === String(companySelect?.value));
        
        if (!selectedApplicant) return alert('Please select an eligible company first.');
        if (!inductionDateInput.value) return alert('Please select an induction date.');

        const companyName = selectedApplicant?.basic_profile?.registered_business_name || '';
        const email = selectedApplicant?.basic_profile?.email || '';

        try {
            saveBtn.disabled = true;
            saveBtn.innerHTML = 'Adding ...';

            const response = await fetch(`${window.API_BASE_URL}/v1/members`, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                body: JSON.stringify({
                    applicant_id: selectedApplicant.id,
                    company_name: companyName,
                    email: email,
                    induction_date: inductionDateInput.value,
                }),
            });

            if (!response.ok) throw new Error('Failed to create member.');

            alert('Member created successfully.');
            document.getElementById('addMemberModal').classList.remove('active');
            inductionDateInput.value = '';
            companySelect.value = '';
            fetchMembers();

        } catch (error) {
            console.error(error);
            alert('Failed to create member.');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check-lg"></i> Add Member';
        }
    }
</script>
@endsection