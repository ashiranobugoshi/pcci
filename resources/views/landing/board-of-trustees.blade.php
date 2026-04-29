@extends('layouts.admin')

@section('title', 'PCCI Board of Trustees')

@section('content')

@include('partials.api-config')

<div class="trustees-page">

    <div class="page-banner">
        <h1>BOARD OF TRUSTEES</h1>
    </div>

    {{-- Status Messages container --}}
    <div id="statusMessageContainer" class="status-container" style="display: none;"></div>

    {{-- Toolbar --}}
    <div class="toolbar">
        <div class="toolbar-left">
            <input type="text" class="search-input w-100 w-md-auto" placeholder="SEARCH TRUSTEES..." id="searchInput">
        </div>
        <button class="btn-add w-100 w-md-auto" onclick="openAddModal()">
            <i class="bi bi-person-plus-fill"></i> ADD NEW TRUSTEE
        </button>
    </div>

    {{-- Table Section --}}
    <div class="table-container">
        <div class="table-responsive">
            <table class="trustee-table" id="trusteeTable">
                <thead>
                    <tr>
                        <th width="80">IMAGE</th>
                        <th>NAME</th>
                        <th>POSITION</th>
                        <th width="150" class="text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="trusteeTableBody">
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            <i class="fa fa-spinner fa-spin me-2"></i> Loading trustees...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="pagination-container" id="paginationControls">
            {{-- Injected via JS --}}
        </div>
    </div>
</div>

{{-- =========================================
     ADD/EDIT TRUSTEE MODAL
========================================= --}}
<div class="modal-overlay" id="trusteeModalOverlay">
    <div class="edit-modal">
        <div class="modal-header">
            <h2 id="modalTitle">ADD NEW TRUSTEE</h2>
            <button class="modal-close" onclick="closeTrusteeModal()">&times;</button>
        </div>

        <div class="modal-body">
            <form id="trusteeForm" onsubmit="return false;">
                <input type="hidden" id="trusteeId">
                <input type="hidden" id="currentImageUrl">

                {{-- Image Upload & Preview --}}
                <div class="image-upload-section">
                    <div class="image-preview" id="imagePreviewContainer">
                        <img id="imagePreview" src="" alt="Trustee Image" style="display:none;">
                        <div class="image-placeholder" id="imagePlaceholder">
                            <i class="bi bi-person-bounding-box"></i>
                            <span>NO IMAGE</span>
                        </div>
                    </div>
                    <div class="upload-controls">
                        <label class="btn-upload" for="trusteeImage">
                            <i class="bi bi-upload"></i> UPLOAD PHOTO
                        </label>
                        <input type="file" id="trusteeImage" accept="image/*" style="display:none;" onchange="previewImage(this)">
                        <small class="text-muted d-block mt-2">Format: JPG, PNG. Max: 2MB.</small>
                    </div>
                </div>

                {{-- Form Fields --}}
                <div class="row g-3 mt-2">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="firstName" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">Middle Name</label>
                        <input type="text" class="form-control" id="middleName">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lastName" required>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold">Gender <span class="text-danger">*</span></label>
                        <select class="form-select" id="gender" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold">Position <span class="text-danger">*</span></label>
                        <select class="form-select" id="positionId" required>
                            <option value="" disabled selected>Loading positions...</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer d-flex flex-column flex-sm-row gap-2">
            <button class="btn-cancel w-100 w-sm-auto" onclick="closeTrusteeModal()">CANCEL</button>
            <button class="btn-save w-100 w-sm-auto" id="saveTrusteeBtn" onclick="saveTrustee()">SAVE TRUSTEE</button>
        </div>
    </div>
</div>

{{-- =========================================
     DELETE CONFIRMATION MODAL
========================================= --}}
<div class="modal-overlay" id="deleteModalOverlay">
    <div class="edit-modal" style="max-width: 400px;">
        <div class="modal-header">
            <h2 class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> CONFIRM DELETE</h2>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body text-center py-4">
            <p class="mb-0 fs-5">Are you sure you want to remove <br><strong id="deleteTrusteeName">this trustee</strong>?</p>
            <p class="text-muted small mt-2">This action cannot be undone.</p>
            <input type="hidden" id="deleteTrusteeId">
        </div>
        <div class="modal-footer d-flex flex-column flex-sm-row gap-2">
            <button class="btn-cancel w-100 w-sm-auto" onclick="closeDeleteModal()">CANCEL</button>
            <button class="btn-save bg-danger w-100 w-sm-auto" onclick="confirmDelete()">YES, DELETE</button>
        </div>
    </div>
</div>


<style>
    .trustees-page {
        font-family: 'Inter', sans-serif;
        padding: 0 0 60px 0;
        background: #f4f6f9;
        min-height: 100vh;
    }

    /* Banner */
    .page-banner {
        background: #be1e38;
        padding: 36px 40px;
        margin-bottom: 28px;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 4px 12px rgba(190, 30, 56, 0.15);
    }
    .page-banner h1 {
        color: #fff;
        font-size: clamp(1.5rem, 4vw, 2rem);
        font-weight: 900;
        margin: 0;
        letter-spacing: 1px;
    }

    /* Toolbar */
    .toolbar {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        justify-content: space-between;
        padding: 0 40px;
        margin-bottom: 24px;
        gap: 16px;
    }
    @media (min-width: 768px) {
        .toolbar { flex-direction: row; align-items: center; }
        .w-md-auto { width: auto !important; }
    }
    @media (max-width: 767.98px) {
        .page-banner { padding: 25px 20px; }
        .toolbar { padding: 0 20px; }
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        flex: 1;
    }
    .search-input {
        flex: 1;
        max-width: 400px;
        height: 46px;
        border: 1.5px solid #ddd;
        border-radius: 8px;
        padding: 0 20px;
        font-size: 0.95rem;
        font-weight: 600;
        color: #444;
        background: #fff;
        transition: all 0.2s;
    }
    .search-input:focus { border-color: #be1e38; outline: none; box-shadow: 0 0 0 3px rgba(190, 30, 56, 0.1); }

    .btn-add {
        height: 46px;
        padding: 0 28px;
        background: #be1e38;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.2s;
        box-shadow: 0 4px 6px rgba(190, 30, 56, 0.2);
    }
    .btn-add:hover { background: #9a182d; transform: translateY(-1px); box-shadow: 0 6px 12px rgba(190, 30, 56, 0.3); }

    /* Table Container */
    .table-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        margin: 0 20px;
        overflow: hidden;
    }
    @media (min-width: 768px) {
        .table-container { margin: 0 40px; }
    }

    .trustee-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 600px; /* Forces scroll on very small screens to prevent squishing */
    }
    .trustee-table th {
        background: #f8f9fa;
        color: #555;
        font-weight: 800;
        font-size: 0.85rem;
        letter-spacing: 1px;
        padding: 16px 24px;
        text-transform: uppercase;
        border-bottom: 2px solid #eaeaea;
    }
    .trustee-table td {
        padding: 16px 24px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
        font-weight: 500;
        font-size: 0.95rem;
    }
    .trustee-table tbody tr:hover { background-color: #fafbfc; }
    .trustee-table tbody tr:last-child td { border-bottom: none; }

    /* Avatar Thumbnail */
    .avatar-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #eee;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .avatar-wrapper i {
        color: #aaa;
        font-size: 1.5rem;
    }

    /* Badges */
    .badge-position {
        background: #eef2f7;
        color: #4a5568;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        border: 1px solid #e2e8f0;
    }

    /* Action Buttons */
    .action-btns {
        display: flex;
        gap: 8px;
        justify-content: center;
    }
    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 1rem;
    }
    .btn-edit-icon { background: #e3f2fd; color: #0d6efd; }
    .btn-edit-icon:hover { background: #0d6efd; color: #fff; }
    .btn-delete-icon { background: #fde8e8; color: #dc3545; }
    .btn-delete-icon:hover { background: #dc3545; color: #fff; }

    /* Pagination */
    .pagination-container {
        padding: 16px 24px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
    }
    .page-btn {
        min-width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
        background: #fff;
        color: #495057;
        margin: 0 4px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }
    .page-btn:hover { background: #e9ecef; }
    .page-btn.active { background: #be1e38; color: #fff; border-color: #be1e38; }
    .page-btn:disabled { opacity: 0.5; cursor: not-allowed; }

    /* Status Messages */
    .status-container {
        margin: 0 20px 20px;
        padding: 16px 20px;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    @media (min-width: 768px) {
        .status-container { margin: 0 40px 20px; }
    }
    .status-success { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
    .status-error { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }

    /* =====================
       MODALS
    ===================== */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(3px);
        padding: 15px; /* Prevent touching edges on mobile */
    }
    .modal-overlay.active { display: flex; }

    .edit-modal {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 700px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        overflow: hidden;
        animation: modalFadeIn 0.3s ease;
        display: flex;
        flex-direction: column;
        max-height: 90vh; /* Prevents modal from being taller than screen */
    }
    @keyframes modalFadeIn {
        from { transform: scale(0.95); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }

    .edit-modal .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 24px 32px;
        border-bottom: 1px solid #eee;
        background: #fafafa;
    }
    .edit-modal .modal-header h2 {
        font-size: 1.25rem;
        font-weight: 800;
        color: #222;
        margin: 0;
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 1.75rem;
        color: #aaa;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        transition: color 0.2s;
    }
    .modal-close:hover { color: #dc3545; }

    .edit-modal .modal-body {
        padding: 32px;
        overflow-y: auto; /* Makes form scrollable if screen is small */
    }

    /* Image Upload Section inside Modal */
    .image-upload-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px dashed #eee;
    }
    @media (min-width: 576px) {
        .image-upload-section { flex-direction: row; align-items: center; gap: 24px; }
        .w-sm-auto { width: auto !important; }
    }

    .image-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 3px solid #eee;
        background: #f8f9fa;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        position: relative;
    }
    .image-preview img { width: 100%; height: 100%; object-fit: cover; }
    .image-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #aaa;
    }
    .image-placeholder i { font-size: 2.5rem; margin-bottom: 4px; }
    .image-placeholder span { font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; }

    .btn-upload {
        background: #fff;
        color: #be1e38;
        border: 2px solid #be1e38;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-upload:hover { background: #be1e38; color: #fff; }

    .edit-modal .modal-footer {
        padding: 24px 32px;
        border-top: 1px solid #eee;
        background: #fafafa;
        justify-content: flex-end;
    }
    .btn-cancel, .btn-save {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    .btn-cancel { background: #e2e8f0; color: #4a5568; }
    .btn-cancel:hover { background: #cbd5e1; }
    .btn-save { background: #be1e38; color: #fff; box-shadow: 0 4px 6px rgba(190, 30, 56, 0.2); }
    .btn-save:hover { background: #9a182d; transform: translateY(-1px); }
    .btn-save:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
</style>

<script>
    // State variables
    let trusteesData = [];
    let positionsData = [];
    let currentPage = 1;
    let itemsPerPage = 10;
    const baseUrl = window.API_BASE_URL;

    document.addEventListener('DOMContentLoaded', () => {
        loadPositions();
        loadTrustees();

        // Search listener
        document.getElementById('searchInput').addEventListener('input', function() {
            currentPage = 1;
            renderTable();
        });
    });

    function showStatus(message, type = 'success') {
        const container = document.getElementById('statusMessageContainer');
        container.className = `status-container status-${type}`;
        container.innerHTML = type === 'success' 
            ? `<i class="bi bi-check-circle-fill fs-5"></i> <span>${message}</span>`
            : `<i class="bi bi-exclamation-circle-fill fs-5"></i> <span>${message}</span>`;
        container.style.display = 'flex';
        
        setTimeout(() => { container.style.display = 'none'; }, 4000);
    }

    // --- FETCH DATA ---
    async function loadPositions() {
        try {
            const token = localStorage.getItem('token');
            const res = await fetch(`${baseUrl}/v1/board-positions`, {
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
            });
            if(res.ok) {
                const json = await res.json();
                positionsData = json.data || json || [];
                populatePositionDropdown();
            }
        } catch(e) { console.error("Error loading positions:", e); }
    }

    function populatePositionDropdown() {
        const select = document.getElementById('positionId');
        select.innerHTML = '<option value="" disabled selected>Select Position</option>';
        positionsData.forEach(pos => {
            const opt = document.createElement('option');
            opt.value = pos.id;
            opt.textContent = pos.position;
            select.appendChild(opt);
        });
    }

    async function loadTrustees() {
        try {
            const token = localStorage.getItem('token');
            const res = await fetch(`${baseUrl}/v1/trustees`, {
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
            });
            if(res.ok) {
                const json = await res.json();
                trusteesData = json.data || json || [];
                
                // Sort by position importance
                trusteesData.sort((a, b) => {
                    const aId = a.board_position_id || (a.position && a.position.id) || 999;
                    const bId = b.board_position_id || (b.position && b.position.id) || 999;
                    return Number(aId) - Number(bId);
                });
                
                renderTable();
            } else {
                showStatus("Failed to load trustees.", "error");
            }
        } catch(e) {
            console.error(e);
            showStatus("Network error while loading data.", "error");
        }
    }

    // --- RENDER TABLE ---
    function renderTable() {
        const tbody = document.getElementById('trusteeTableBody');
        const query = document.getElementById('searchInput').value.toLowerCase().trim();
        
        // Filter
        let filtered = trusteesData;
        if(query) {
            filtered = trusteesData.filter(t => {
                const name = `${t.firstname} ${t.lastname}`.toLowerCase();
                const pos = (t.position && t.position.position ? t.position.position : '').toLowerCase();
                return name.includes(query) || pos.includes(query);
            });
        }

        // Pagination calculations
        const totalItems = filtered.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        if(currentPage > totalPages && totalPages > 0) currentPage = totalPages;
        
        const startIdx = (currentPage - 1) * itemsPerPage;
        const pagedData = filtered.slice(startIdx, startIdx + itemsPerPage);

        tbody.innerHTML = '';

        if(pagedData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-5 text-muted">No trustees found.</td></tr>`;
            renderPagination(0, 1);
            return;
        }

        pagedData.forEach(t => {
            const tr = document.createElement('tr');
            
            const fullName = `${t.firstname} ${t.middlename ? t.middlename + ' ' : ''}${t.lastname}`;
            const posName = t.position && t.position.position ? t.position.position : 'No Position';
            
            let avatarHtml = `<i class="bi bi-person-fill"></i>`;
            if(t.image_url) {
                avatarHtml = `<img src="${t.image_url}" alt="${fullName}" onerror="this.onerror=null; this.parentNode.innerHTML='<i class=\\'bi bi-person-fill\\'></i>';">`;
            }

            // Storing full object in JSON for easy edit modal population
            const dataStr = encodeURIComponent(JSON.stringify(t));

            tr.innerHTML = `
                <td><div class="avatar-wrapper shadow-sm">${avatarHtml}</div></td>
                <td>
                    <div class="fw-bold text-dark">${fullName}</div>
                    <div class="small text-muted">${t.gender || ''}</div>
                </td>
                <td><span class="badge-position shadow-sm">${posName}</span></td>
                <td>
                    <div class="action-btns">
                        <button class="btn-icon btn-edit-icon" onclick="openEditModal('${dataStr}')" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn-icon btn-delete-icon" onclick="openDeleteModal(${t.id}, '${fullName.replace(/'/g, "\\'")}')" title="Delete">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

        renderPagination(totalPages, currentPage);
    }

    function renderPagination(totalPages, current) {
        const container = document.getElementById('paginationControls');
        container.innerHTML = '';
        
        if(totalPages <= 1) return;

        // Prev
        const prev = document.createElement('button');
        prev.className = 'page-btn';
        prev.innerHTML = '<i class="bi bi-chevron-left"></i>';
        prev.disabled = (current === 1);
        prev.onclick = () => { currentPage--; renderTable(); };
        container.appendChild(prev);

        // Numbers
        for(let i=1; i<=totalPages; i++) {
            const btn = document.createElement('button');
            btn.className = `page-btn ${i === current ? 'active' : ''}`;
            btn.innerText = i;
            btn.onclick = () => { currentPage = i; renderTable(); };
            container.appendChild(btn);
        }

        // Next
        const next = document.createElement('button');
        next.className = 'page-btn';
        next.innerHTML = '<i class="bi bi-chevron-right"></i>';
        next.disabled = (current === totalPages);
        next.onclick = () => { currentPage++; renderTable(); };
        container.appendChild(next);
    }

    // --- MODAL HANDLING ---
    function previewImage(input) {
        if(input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
                document.getElementById('imagePlaceholder').style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearModal() {
        document.getElementById('trusteeForm').reset();
        document.getElementById('trusteeId').value = '';
        document.getElementById('currentImageUrl').value = '';
        document.getElementById('trusteeImage').value = '';
        
        document.getElementById('imagePreview').src = '';
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('imagePlaceholder').style.display = 'flex';
    }

    function openAddModal() {
        clearModal();
        document.getElementById('modalTitle').innerText = 'ADD NEW TRUSTEE';
        document.getElementById('saveTrusteeBtn').innerText = 'SAVE TRUSTEE';
        document.getElementById('trusteeModalOverlay').classList.add('active');
    }

    function openEditModal(encodedData) {
        clearModal();
        const t = JSON.parse(decodeURIComponent(encodedData));
        
        document.getElementById('modalTitle').innerText = 'EDIT TRUSTEE';
        document.getElementById('saveTrusteeBtn').innerText = 'UPDATE TRUSTEE';
        
        document.getElementById('trusteeId').value = t.id;
        document.getElementById('firstName').value = t.firstname || '';
        document.getElementById('middleName').value = t.middlename || '';
        document.getElementById('lastName').value = t.lastname || '';
        document.getElementById('gender').value = t.gender || '';
        
        if(t.position && t.position.id) {
            document.getElementById('positionId').value = t.position.id;
        }

        if(t.image_url) {
            document.getElementById('currentImageUrl').value = t.image_url;
            document.getElementById('imagePreview').src = t.image_url;
            document.getElementById('imagePreview').style.display = 'block';
            document.getElementById('imagePlaceholder').style.display = 'none';
        }

        document.getElementById('trusteeModalOverlay').classList.add('active');
    }

    function closeTrusteeModal() {
        document.getElementById('trusteeModalOverlay').classList.remove('active');
    }

    function openDeleteModal(id, name) {
        document.getElementById('deleteTrusteeId').value = id;
        document.getElementById('deleteTrusteeName').innerText = name;
        document.getElementById('deleteModalOverlay').classList.add('active');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModalOverlay').classList.remove('active');
    }

    // --- CRUD OPERATIONS ---
    async function saveTrustee() {
        const form = document.getElementById('trusteeForm');
        if(!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const id = document.getElementById('trusteeId').value;
        const btn = document.getElementById('saveTrusteeBtn');
        const token = localStorage.getItem('token');
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> SAVING...';

        const formData = new FormData();
        formData.append('firstname', document.getElementById('firstName').value);
        formData.append('middlename', document.getElementById('middleName').value);
        formData.append('lastname', document.getElementById('lastName').value);
        formData.append('gender', document.getElementById('gender').value);
        formData.append('board_position_id', document.getElementById('positionId').value);
        
        const imageFile = document.getElementById('trusteeImage').files[0];
        if(imageFile) {
            formData.append('image', imageFile);
        }

        const url = id ? `${baseUrl}/v1/trustees/${id}` : `${baseUrl}/v1/trustees`;
        
        // If updating via POST with FormData in Laravel, need _method spoofing
        if(id) {
            formData.append('_method', 'PUT');
        }

        try {
            const res = await fetch(url, {
                method: 'POST', // Always POST for FormData, let Laravel spoof PUT
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` },
                body: formData
            });

            if(res.ok) {
                closeTrusteeModal();
                showStatus(id ? "Trustee updated successfully!" : "Trustee added successfully!");
                loadTrustees();
            } else {
                const errData = await res.json();
                console.error(errData);
                alert("Failed to save: " + (errData.message || "Validation error"));
            }
        } catch(e) {
            console.error(e);
            alert("Network error.");
        } finally {
            btn.disabled = false;
            btn.innerText = id ? 'UPDATE TRUSTEE' : 'SAVE TRUSTEE';
        }
    }

    async function confirmDelete() {
        const id = document.getElementById('deleteTrusteeId').value;
        const token = localStorage.getItem('token');
        
        try {
            const res = await fetch(`${baseUrl}/v1/trustees/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
            });

            if(res.ok) {
                closeDeleteModal();
                showStatus("Trustee removed successfully!");
                loadTrustees();
            } else {
                alert("Failed to delete trustee.");
            }
        } catch(e) {
            console.error(e);
            alert("Network error.");
        }
    }

</script>

@endsection