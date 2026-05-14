@extends('layouts.admin')

@section('title', 'PCCI Activities')

@section('content')

@include('partials.api-config')
<div class="activities-page">

    {{-- Red Banner Header --}}
    <div class="page-banner">
        <h1>PCCI ACTIVITIES</h1>
    </div>

    {{-- Search / Filter / Add Bar --}}
    <div class="toolbar">
        <div class="toolbar-left">
            <input type="text" class="search-input" placeholder="SEARCH" id="searchInput">
            <button class="btn-filter" id="filterToggle">
                <i class="bi bi-funnel"></i> FILTERED
            </button>
        </div>
        <button class="btn-add-activity" onclick="openAddModal()">
            <i class="bi bi-plus-lg"></i> ADD
        </button>
    </div>

    {{-- Activity Grid --}}
    <div class="activity-grid" id="activityGrid">
        <div class="text-center py-5 w-100 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading activities...</div>
    </div>
</div>

{{-- ===========================
     VIEW ACTIVITY MODAL
=========================== --}}
<div class="modal-overlay" id="viewModalOverlay">
    <div class="view-modal">

        <button class="view-modal-close" id="closeViewModal">&times;</button>

        <div class="view-modal-inner">

            <h1 class="view-title" id="viewTitle">Event Details</h1>
            <span class="view-category-badge" id="viewCategory">General</span>

            <div class="view-content-row">

                {{-- Left: Photo --}}
                <div class="view-photo-wrap">
                    <img src="" alt="" id="viewImage">
                </div>

                {{-- Right: Details + Description --}}
                <div class="view-details">

                    <div class="view-meta">
                        <div class="view-meta-item">
                            <i class="bi bi-calendar3"></i>
                            <span id="viewDate">Tuesday, December 9, 2025</span>
                        </div>
                        <div class="view-meta-item">
                            <i class="bi bi-clock"></i>
                            <span id="viewTime">9:00 AM</span>
                        </div>
                        <div class="view-meta-item">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span id="viewLocation">Valenzuela City</span>
                        </div>
                    </div>

                    <div class="view-description-wrap">
                        <p class="view-description" id="viewDescription"></p>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

{{-- ===========================
     ADD ACTIVITY MODAL
=========================== --}}
<div class="modal-overlay" id="addModalOverlay">
    <div class="edit-modal">

        <div class="modal-header">
            <h2>ADD ACTIVITY</h2>
            <button class="modal-close" id="closeAddModal">&times;</button>
        </div>

        <div class="modal-body">

            {{-- Image Preview placeholder --}}
            <div class="image-preview-wrap" id="addImagePreviewWrap" style="display:none;">
                <img src="" alt="Activity Preview" id="addImagePreview">
                <button class="btn-crop" title="Change Image" onclick="document.getElementById('addImageFile').click()">
                    <i class="bi bi-crop"></i>
                </button>
            </div>

            {{-- Description --}}
            <textarea
                class="edit-description"
                id="addDescription"
                placeholder="DESCRIPTION"
                rows="4"></textarea>

            {{-- File Upload --}}
            <div class="file-upload-section">
                <label class="file-upload-label">ACTIVITY IMAGE</label>
                <div class="file-upload-row">
                    <label class="btn-choose-file" for="addImageFile">CHOOSE FILE</label>
                    <input type="file" id="addImageFile" accept="image/*" style="display:none;">
                    <span class="file-name-display" id="addFileNameDisplay">NO FILE CHOSEN</span>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button class="btn-cancel" id="cancelAddModal">CANCEL</button>
            <button class="btn-save" id="saveNewActivity">SAVE</button>
        </div>

    </div>
</div>

{{-- ===========================
     EDIT ACTIVITY MODAL
=========================== --}}
<div class="modal-overlay" id="editModalOverlay">
    <div class="edit-modal">

        <div class="modal-header">
            <h2>EDIT ACTIVITY</h2>
            <button class="modal-close" id="closeEditModal">&times;</button>
        </div>

        <div class="modal-body">

            {{-- Image Preview with crop button --}}
            <div class="image-preview-wrap">
                <img src="" alt="Activity Preview" id="editImagePreview">
                <button class="btn-crop" title="Change Image" onclick="document.getElementById('editImageFile').click()">
                    <i class="bi bi-crop"></i>
                </button>
            </div>

            {{-- Description --}}
            <textarea
                class="edit-description"
                id="editDescription"
                placeholder="DESCRIPTION"
                rows="4"></textarea>

            {{-- File Upload --}}
            <div class="file-upload-section">
                <label class="file-upload-label">ACTIVITY IMAGE</label>
                <div class="file-upload-row">
                    <label class="btn-choose-file" for="editImageFile">CHOOSE FILE</label>
                    <input type="file" id="editImageFile" accept="image/*" style="display:none;">
                    <span class="file-name-display" id="fileNameDisplay">CHANGE FILE</span>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button class="btn-cancel" id="cancelEditModal">CANCEL</button>
            <button class="btn-save" id="saveActivity">SAVE</button>
        </div>

        <input type="hidden" id="editActivityId">
    </div>
</div>


<style>
    .activities-page {
        font-family: 'Inter', sans-serif;
        padding: 0 0 60px 0;
        background: #f5f5f5;
        min-height: 100vh;
    }

    /* Banner */
    .page-banner {
        background: var(--pcci-red, #be1e38);
        padding: 36px 40px;
        margin-bottom: 28px;
        border-radius: 10px;
    }

    .page-banner h1 {
        color: #fff;
        font-size: clamp(1.5rem, 4vw, 2rem);
        /* Fluid text */
        font-weight: 900;
        margin: 0;
        letter-spacing: 1px;
    }

    /* Toolbar */
    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 40px;
        margin-bottom: 32px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
        flex-wrap: wrap;
    }

    .search-input {
        flex: 1;
        min-width: 250px;
        max-width: 380px;
        height: 46px;
        border: 1.5px solid #ccc;
        border-radius: 8px;
        padding: 0 18px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #666;
        letter-spacing: 1px;
        outline: none;
        background: #fff;
        transition: border-color 0.2s;
    }

    .search-input:focus {
        border-color: var(--pcci-red, #be1e38);
    }

    .btn-filter {
        height: 46px;
        padding: 0 24px;
        border: 1.5px solid #ccc;
        border-radius: 8px;
        background: #fff;
        font-size: 0.9rem;
        font-weight: 700;
        color: #666;
        letter-spacing: 1px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-filter:hover,
    .btn-filter.active {
        border-color: var(--pcci-red, #be1e38);
        color: var(--pcci-red, #be1e38);
    }

    .btn-add-activity {
        height: 46px;
        padding: 0 28px;
        background: var(--pcci-red, #be1e38);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 800;
        letter-spacing: 1px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.2s, transform 0.15s;
    }

    .btn-add-activity:hover {
        background: #9a182d;
        transform: translateY(-1px);
    }

    /* Grid */
    .activity-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        padding: 0 40px;
    }

    /* Responsive Fixes for Grid & Toolbar */
    @media (max-width: 1100px) {
        .activity-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .page-banner {
            padding: 25px 20px;
            border-radius: 0;
        }

        .toolbar {
            padding: 0 20px;
            flex-direction: column;
            align-items: stretch;
        }

        .toolbar-left {
            flex-direction: column;
            align-items: stretch;
        }

        .search-input {
            max-width: 100%;
            min-width: 100%;
        }

        .btn-filter,
        .btn-add-activity {
            width: 100%;
        }

        .activity-grid {
            padding: 0 20px;
            gap: 16px;
        }
    }

    @media (max-width: 680px) {
        .activity-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Card */
    .activity-card {
        background: #fff;
        border-radius: 12px;
        border: 2px solid #e0e0e0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.25s, border-color 0.25s, transform 0.2s;
    }

    .activity-card:hover {
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12);
        transform: translateY(-3px);
        border-color: var(--pcci-red, #be1e38);
    }

    .activity-card.selected {
        border-color: #1a73e8;
        box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.25);
    }

    .card-image-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        background: #ddd;
    }

    .card-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.35s ease;
    }

    .activity-card:hover .card-image-wrap img {
        transform: scale(1.04);
    }

    .card-title-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.72));
        color: #fff;
        font-size: 0.9rem;
        font-weight: 700;
        padding: 32px 14px 12px;
        line-height: 1.3;
    }

    .card-actions {
        display: flex;
        flex-wrap: wrap;
        /* Allows buttons to stack if completely squished */
        gap: 10px;
        padding: 14px 16px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-view,
    .btn-edit {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 0;
        border-radius: 7px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-decoration: none;
        border: 1.5px solid;
        transition: all 0.2s;
        cursor: pointer;
        background: transparent;
        font-family: 'Inter', sans-serif;
        min-width: 100px;
    }

    .btn-view {
        color: var(--pcci-red, #be1e38);
        border-color: var(--pcci-red, #be1e38);
    }

    .btn-view:hover {
        background: var(--pcci-red, #be1e38);
        color: #fff;
    }

    .btn-edit {
        color: #666;
        border-color: #ccc;
    }

    .btn-edit:hover {
        background: #f0f0f0;
        border-color: #999;
        color: #333;
    }

    /* =====================
       MODAL OVERLAY
    ===================== */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(2px);
        padding: 15px;
        /* Prevents touching screen edges on mobile */
    }

    .modal-overlay.active {
        display: flex;
    }

    /* =====================
       EDIT MODAL
    ===================== */
    .edit-modal {
        background: #fff;
        border-radius: 14px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: modalIn 0.22s ease;
    }

    @keyframes modalIn {
        from {
            transform: scale(0.94) translateY(10px);
            opacity: 0;
        }

        to {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
    }

    .edit-modal .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px 14px;
    }

    .edit-modal .modal-header h2 {
        font-size: 1.1rem;
        font-weight: 900;
        color: #222;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #888;
        cursor: pointer;
        line-height: 1;
        padding: 0;
        transition: color 0.2s;
    }

    .modal-close:hover {
        color: #222;
    }

    .edit-modal .modal-body {
        padding: 0 24px 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Image preview */
    .image-preview-wrap {
        position: relative;
        width: 100%;
        border-radius: 10px;
        overflow: hidden;
        background: #eee;
        aspect-ratio: 16 / 9;
    }

    .image-preview-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .btn-crop {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.55);
        color: #fff;
        border: none;
        border-radius: 8px;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-crop:hover {
        background: rgba(0, 0, 0, 0.80);
    }

    /* Description */
    .edit-description {
        width: 100%;
        border: 1.5px solid var(--pcci-red, #be1e38);
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 0.88rem;
        font-family: 'Inter', sans-serif;
        color: #444;
        resize: vertical;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.2s;
    }

    .edit-description:focus {
        border-color: #9a182d;
    }

    .edit-description::placeholder {
        color: #bbb;
        letter-spacing: 0.5px;
        font-weight: 600;
        font-size: 0.82rem;
    }

    /* File Upload */
    .file-upload-section {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .file-upload-label {
        font-size: 0.82rem;
        font-weight: 800;
        color: #333;
        letter-spacing: 0.5px;
    }

    .file-upload-row {
        display: flex;
        align-items: center;
        border: 1.5px solid var(--pcci-red, #be1e38);
        border-radius: 8px;
        overflow: hidden;
        height: 40px;
    }

    .btn-choose-file {
        background: #e0e0e0;
        color: #333;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 0 16px;
        height: 100%;
        display: flex;
        align-items: center;
        cursor: pointer;
        white-space: nowrap;
        letter-spacing: 0.3px;
        transition: background 0.2s;
        border-right: 1.5px solid var(--pcci-red, #be1e38);
        user-select: none;
    }

    .btn-choose-file:hover {
        background: #ccc;
    }

    .file-name-display {
        flex: 1;
        padding: 0 14px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #888;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Footer */
    .edit-modal .modal-footer {
        display: flex;
        gap: 12px;
        padding: 12px 24px 24px;
    }

    .btn-cancel {
        flex: 1;
        height: 44px;
        border: 1.5px solid #ccc;
        border-radius: 8px;
        background: #fff;
        color: #555;
        font-size: 0.9rem;
        font-weight: 800;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
    }

    .btn-cancel:hover {
        background: #f5f5f5;
        border-color: #aaa;
    }

    .btn-save {
        flex: 1;
        height: 44px;
        border: none;
        border-radius: 8px;
        background: var(--pcci-red, #be1e38);
        color: #fff;
        font-size: 0.9rem;
        font-weight: 800;
        letter-spacing: 1px;
        cursor: pointer;
        transition: background 0.2s, transform 0.15s;
        font-family: 'Inter', sans-serif;
    }

    .btn-save:hover {
        background: #9a182d;
        transform: translateY(-1px);
    }

    /* =====================
       VIEW ACTIVITY MODAL
    ===================== */
    #viewModalOverlay .view-modal {
        background: #fff;
        border-radius: 14px;
        width: 100%;
        max-width: 740px;
        max-height: 90vh;
        overflow: hidden;
        position: relative;
        animation: modalIn 0.25s ease;
        display: flex;
        flex-direction: column;
    }

    .view-modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 32px;
        height: 32px;
        border: 1.5px solid #ccc;
        border-radius: 6px;
        background: #fff;
        font-size: 1.2rem;
        color: #555;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        transition: all 0.2s;
        line-height: 1;
    }

    .view-modal-close:hover {
        background: #f0f0f0;
        border-color: #aaa;
        color: #222;
    }

    .view-modal-inner {
        padding: 32px;
        overflow-y: auto;
    }

    .view-title {
        font-size: clamp(1.4rem, 4vw, 1.85rem);
        font-weight: 900;
        color: #111;
        margin: 0 0 10px 0;
        padding-right: 40px;
        line-height: 1.2;
    }

    .view-category-badge {
        display: inline-block;
        background: #f0f0f0;
        color: #444;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 4px 14px;
        border-radius: 20px;
        margin-bottom: 22px;
        letter-spacing: 0.3px;
    }

    .view-content-row {
        display: flex;
        gap: 28px;
        align-items: flex-start;
    }

    /* Photo */
    .view-photo-wrap {
        flex-shrink: 0;
        width: 280px;
        border-radius: 12px;
        overflow: hidden;
        border: 3px solid #e8e8e8;
    }

    .view-photo-wrap img {
        width: 100%;
        display: block;
        object-fit: cover;
        aspect-ratio: 3 / 4;
    }

    /* Right column */
    .view-details {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 18px;
        min-width: 0;
    }

    /* Meta icons row */
    .view-meta {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .view-meta-item {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1rem;
        color: #222;
        font-weight: 500;
    }

    .view-meta-item i {
        font-size: 1.25rem;
        color: #333;
        flex-shrink: 0;
        width: 22px;
        text-align: center;
    }

    /* Scrollable description */
    .view-description-wrap {
        max-height: 220px;
        overflow-y: auto;
        padding-right: 6px;
        border-left: 3px solid var(--pcci-red, #be1e38);
        padding-left: 12px;
    }

    .view-description-wrap::-webkit-scrollbar {
        width: 5px;
    }

    .view-description-wrap::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 4px;
    }

    .view-description-wrap::-webkit-scrollbar-thumb {
        background: var(--pcci-red, #be1e38);
        border-radius: 4px;
    }

    .view-description {
        font-size: 0.9rem;
        color: #333;
        line-height: 1.7;
        margin: 0;
    }

    /* Stack Image on top of text on small screens */
    @media (max-width: 768px) {
        .view-content-row {
            flex-direction: column;
        }

        .view-photo-wrap {
            width: 100%;
            aspect-ratio: 16/9;
        }

        .view-photo-wrap img {
            aspect-ratio: auto;
        }

        .view-modal-inner {
            padding: 24px 20px;
        }
    }
</style>

<script>
    let allActivities = [];

    // --- 1. FETCH ACTIVITIES ---
    async function fetchActivities() {
        const token = localStorage.getItem('token');
        if (!token) return;

        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/activities`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            const result = await response.json();
            allActivities = result.data?.data || result.data || result || [];
            renderActivities();
        } catch (error) {
            console.error('Error loading activities:', error);
            document.getElementById('activityGrid').innerHTML = '<div class="text-center py-5 w-100 text-danger">Failed to load activities.</div>';
        }
    }

    // --- 2. RENDER GRID (Added Edit & Delete Buttons) ---
    function renderActivities() {
        const grid = document.getElementById('activityGrid');
        const query = document.getElementById('searchInput')?.value.toLowerCase() || '';

        const filtered = allActivities.filter(act =>
            (act.title || '').toLowerCase().includes(query) ||
            (act.description || '').toLowerCase().includes(query)
        );

        if (filtered.length === 0) {
            grid.innerHTML = '<div class="text-center py-5 w-100 text-muted" style="grid-column: 1 / -1;">No activities found.</div>';
            return;
        }

        grid.innerHTML = filtered.map(act => {
            const imageUrl = act.image_url || (act.image_path ? `${window.API_BASE_URL.replace('/api', '')}/storage/${act.image_path}` : 'https://placehold.co/600x400?text=No+Image');

            return `
            <div class="activity-card">
                <div class="card-image-wrap">
                    <img src="${imageUrl}" alt="Activity">
                    <div class="card-title-overlay">${act.title || 'PCCI Activity'}</div>
                </div>
                <div class="card-actions">
                    <button class="btn-view" onclick="openViewModal(${act.id})">VIEW</button>
                    <button class="btn-edit" onclick="openEditModal(${act.id})"><i class="bi bi-pencil"></i> EDIT</button>
                    <button class="btn-edit" onclick="deleteActivity(${act.id})" style="color: #be1e38; border-color: #be1e38; min-width: 40px; flex: 0.2;"><i class="bi bi-trash"></i></button>
                </div>
            </div>`;
        }).join('');
    }

    // --- 3. ADD MODAL LOGIC ---
    function openAddModal() {
        // Reset the exact IDs from your HTML
        document.getElementById('addDescription').value = '';
        document.getElementById('addImageFile').value = '';
        document.getElementById('addFileNameDisplay').innerText = 'NO FILE CHOSEN';
        document.getElementById('addImagePreviewWrap').style.display = 'none';
        document.getElementById('addImagePreview').src = '';

        document.getElementById('addModalOverlay')?.classList.add('active');
    }

    document.getElementById('closeAddModal')?.addEventListener('click', () => {
        document.getElementById('addModalOverlay').classList.remove('active');
    });
    document.getElementById('cancelAddModal')?.addEventListener('click', () => {
        document.getElementById('addModalOverlay').classList.remove('active');
    });

    // --- 4. EDIT MODAL LOGIC ---
    function openEditModal(id) {
        const act = allActivities.find(a => a.id === id);
        if (!act) return;

        document.getElementById('editActivityId').value = act.id;
        document.getElementById('editDescription').value = act.description || '';
        document.getElementById('fileNameDisplay').innerText = 'CHANGE FILE';
        document.getElementById('editImageFile').value = '';

        const imageUrl = act.image_url || (act.image_path ? `${window.API_BASE_URL.replace('/api', '')}/storage/${act.image_path}` : '');
        document.getElementById('editImagePreview').src = imageUrl;

        document.getElementById('editModalOverlay')?.classList.add('active');
    }

    document.getElementById('closeEditModal')?.addEventListener('click', () => {
        document.getElementById('editModalOverlay').classList.remove('active');
    });
    document.getElementById('cancelEditModal')?.addEventListener('click', () => {
        document.getElementById('editModalOverlay').classList.remove('active');
    });

    // --- 5. VIEW MODAL LOGIC ---
    function openViewModal(id) {
        const act = allActivities.find(a => a.id === id);
        if (!act) return;

        const imageUrl = act.image_url || (act.image_path ? `${window.API_BASE_URL.replace('/api', '')}/storage/${act.image_path}` : '');
        document.getElementById('viewImage').src = imageUrl;
        document.getElementById('viewTitle').innerText = act.title || 'PCCI Activity';
        document.getElementById('viewDescription').innerText = act.description || '';

        // Optional fallbacks if the fields exist in DB
        if (document.getElementById('viewDate')) document.getElementById('viewDate').innerText = act.date || 'N/A';
        if (document.getElementById('viewLocation')) document.getElementById('viewLocation').innerText = act.location || 'N/A';

        document.getElementById('viewModalOverlay')?.classList.add('active');
    }

    document.getElementById('closeViewModal')?.addEventListener('click', () => {
        document.getElementById('viewModalOverlay').classList.remove('active');
    });

    // --- 6. IMAGE PREVIEWS ---
    document.getElementById('addImageFile')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('addFileNameDisplay').innerText = file.name;
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('addImagePreview').src = evt.target.result;
                document.getElementById('addImagePreviewWrap').style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('editImageFile')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('fileNameDisplay').innerText = file.name;
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('editImagePreview').src = evt.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // --- 7. SAVE NEW ACTIVITY API ---
    document.getElementById('saveNewActivity')?.addEventListener('click', async function() {
        const token = localStorage.getItem('token');
        const btn = this;
        btn.disabled = true;
        btn.innerText = 'SAVING...';

        const formData = new FormData();
        formData.append('description', document.getElementById('addDescription').value);
        formData.append('title', 'PCCI Activity'); // Backend failsafe requirement

        const imageFile = document.getElementById('addImageFile').files[0];
        if (imageFile) formData.append('image', imageFile);

        try {
            const res = await fetch(`${window.API_BASE_URL}/v1/activities`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: formData
            });
            if (res.ok) {
                document.getElementById('addModalOverlay').classList.remove('active');
                fetchActivities();
            } else {
                const data = await res.json();
                alert('Save failed: ' + (data.message || 'Validation Error'));
            }
        } catch (err) {
            alert('Network Error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'SAVE';
        }
    });

    // --- 8. SAVE EDITED ACTIVITY API ---
    document.getElementById('saveActivity')?.addEventListener('click', async function() {
        const token = localStorage.getItem('token');
        const id = document.getElementById('editActivityId').value;
        const btn = this;
        btn.disabled = true;
        btn.innerText = 'SAVING...';

        const formData = new FormData();
        formData.append('_method', 'PUT'); // Laravel requirement to process file updates
        formData.append('description', document.getElementById('editDescription').value);

        const imageFile = document.getElementById('editImageFile').files[0];
        if (imageFile) formData.append('image', imageFile);

        try {
            const res = await fetch(`${window.API_BASE_URL}/v1/activities/${id}`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: formData
            });
            if (res.ok) {
                document.getElementById('editModalOverlay').classList.remove('active');
                fetchActivities();
            } else {
                const data = await res.json();
                alert('Save failed: ' + (data.message || 'Validation Error'));
            }
        } catch (err) {
            alert('Network Error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'SAVE';
        }
    });

    // --- 9. DELETE ACTIVITY API ---
    async function deleteActivity(id) {
        if (!confirm('Are you sure you want to delete this activity?')) return;
        const token = localStorage.getItem('token');
        try {
            const res = await fetch(`${window.API_BASE_URL}/v1/activities/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if (res.ok) fetchActivities();
            else alert('Delete failed.');
        } catch (err) {
            alert('Network Error');
        }
    }

    // --- SEARCH ---
    document.getElementById('searchInput')?.addEventListener('input', renderActivities);
    document.getElementById('filterToggle')?.addEventListener('click', function() {
        this.classList.toggle('active');
    });

    // --- INIT ---
    if (document.readyState !== 'loading') {
        fetchActivities();
    } else {
        document.addEventListener('DOMContentLoaded', fetchActivities);
    }
</script>

@endsection