@extends('layouts.admin')

@section('title', 'Admin Users - PCCI')

@section('content')
@include('partials.api-config')

<style>
    :root {
        --primary-red: #be1e38;
        --dark-bg: #222431;
        --card-bg: #2b2d3c;
        --section-bg: #323545;
        --text-grey: #a0aec0;
        --border-color: #4a4d61;
    }

    .page-header {
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

    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        flex: 1;
        max-width: 360px;
    }

    .search-box input {
        width: 100%;
        padding: 10px 14px 10px 40px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.9rem;
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
    }

    .search-box input:focus {
        outline: none;
        border-color: #be1e38;
    }

    .search-box .icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
    }

    .btn-register {
        background: #be1e38;
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background 0.2s;
        white-space: nowrap;
    }

    .btn-register:hover {
        background: #a01a30;
    }

    /* Users Table */
    .users-table-wrapper {
        overflow-x: auto;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
        table-layout: fixed;
    }

    .users-table thead {
        background: #f8f8f8;
    }

    .users-table th {
        padding: 12px 14px;
        text-align: left;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #555;
        border-bottom: 2px solid #e0e0e0;
    }

    .users-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .users-table tr:hover {
        background: #fdf2f4;
    }

    .role-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 50rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .role-admin {
        background: #fef2f2;
        color: #be1e38;
    }

    .role-treasurer {
        background: #eff6ff;
        color: #2563eb;
    }

    .role-member {
        background: #f0fdf4;
        color: #15803d;
    }

    .role-default {
        background: #f5f5f5;
        color: #666;
    }

    /* Modals */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-box {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .modal-box h3 {
        margin: 0 0 6px;
        font-weight: 700;
        color: #be1e38;
        font-size: 1.2rem;
    }

    .modal-box p {
        color: #777;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .modal-form-group {
        margin-bottom: 14px;
    }

    .modal-form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 6px;
    }

    .modal-form-group input,
    .modal-form-group select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.95rem;
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
    }

    .modal-form-group input:focus,
    .modal-form-group select:focus {
        outline: none;
        border-color: #be1e38;
    }

    .modal-btn-row {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .modal-btn-row button {
        flex: 1;
        padding: 10px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        border: none;
        transition: 0.2s;
    }

    .btn-confirm {
        background: #be1e38;
        color: #fff;
    }

    .btn-confirm:hover {
        background: #a01a30;
    }

    .btn-cancel {
        background: #f5f5f5;
        color: #555;
        border: 1px solid #ddd;
    }

    .btn-cancel:hover {
        background: #eee;
    }

    .alert-msg {
        display: none;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 14px;
    }

    .alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .alert-success {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .alert-warning {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .password-box {
        background: #f8f8f8;
        border: 2px dashed #ddd;
        padding: 14px;
        border-radius: 8px;
        font-family: monospace;
        font-size: 1.15rem;
        text-align: center;
        margin: 14px 0;
        letter-spacing: 1px;
        color: #333;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 12px;
        display: block;
        color: #ddd;
    }

    @media (max-width: 992px) {
        .page-header {
            padding: 24px;
            font-size: 1.6rem;
        }

        .search-box {
            max-width: 100%;
        }

        .users-table th,
        .users-table td {
            padding: 12px;
        }

        .users-table {
            min-width: 760px;
        }
    }

    @media (max-width: 576px) {
        .page-header {
            padding: 20px 16px;
            font-size: 1.3rem;
        }

        .toolbar {
            gap: 10px;
        }

        .btn-register {
            width: 100%;
            justify-content: center;
        }

        .modal-box {
            padding: 20px 16px;
        }

        .modal-btn-row {
            flex-direction: column;
        }

        .users-table {
            min-width: 680px;
        }
    }
</style>

<div class="page-header">Admin Users</div>

{{-- Toolbar --}}
<div class="toolbar">
    <div class="search-box">
        <i class="bi bi-search icon"></i>
        <input type="text" placeholder="Search users..." id="userSearchInput">
    </div>
    <button class="btn-register" onclick="openRegisterModal()">
        <i class="bi bi-plus-lg"></i> Register New User
    </button>
</div>

{{-- Users Table --}}
<div class="users-table-wrapper">
    <table class="users-table">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody id="usersTableBody">
            <tr>
                <td colspan="4" style="text-align: center; padding: 40px; color: #999;">
                    <i class="bi bi-arrow-repeat" style="display: inline-block; animation: spin 1s linear infinite; font-size: 1.5rem;"></i>
                    <br>Loading users...
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Register User Modal --}}
<div id="registerModal" class="modal-overlay" onclick="if(event.target===this) closeRegisterModal()">
    <div class="modal-box">
        <h3><i class="bi bi-person-plus"></i> Register New User</h3>
        <p>Create a new admin or treasurer account.</p>
        <div id="registerError" class="alert-msg alert-error"></div>
        <form id="registerForm" onsubmit="handleRegister(event)">
            <div class="modal-form-group">
                <label>First Name</label>
                <input type="text" id="regFirstName" required placeholder="e.g. John">
            </div>
            <div class="modal-form-group">
                <label>Last Name</label>
                <input type="text" id="regLastName" required placeholder="e.g. Doe">
            </div>
            <div class="modal-form-group">
                <label>Email Address</label>
                <input type="email" id="regEmail" required placeholder="user@example.com">
            </div>
            <div class="modal-form-group">
                <label>Role</label>
                <select id="regRole" required>
                    <option value="treasurer">Treasurer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="modal-btn-row">
                <button type="button" class="btn-cancel" onclick="closeRegisterModal()">Cancel</button>
                <button type="submit" class="btn-confirm" id="regSubmitBtn">Register Account</button>
            </div>
        </form>
    </div>
</div>

{{-- Success Modal --}}
<div id="successModal" class="modal-overlay" onclick="if(event.target===this) closeSuccessModal()">
    <div class="modal-box">
        <h3><i class="bi bi-check-circle"></i> Account Created!</h3>
        <p>The account for <strong id="successEmail"></strong> has been registered.</p>
        <div class="alert-msg alert-warning" style="display: block;">
            <i class="bi bi-exclamation-triangle"></i> Copy this password now. It will only be shown once.
        </div>
        <div class="password-box" id="generatedPassword"></div>
        <div class="modal-btn-row">
            <button class="btn-confirm" onclick="copyPassword()" id="copyBtn"><i class="bi bi-clipboard"></i> Copy Password</button>
        </div>
        <div class="modal-btn-row">
            <button class="btn-cancel" onclick="closeSuccessModal()" style="width: 100%;">I have copied the password (Close)</button>
        </div>
    </div>
</div>

<script>
    var token = localStorage.getItem('token');
    let allUsers = [];

    if (!token) {
        window.location.href = '/login';
    }

    function initAdminUsersPage() {
        fetchUsers();
        document.getElementById('userSearchInput').addEventListener('input', function() {
            renderUsers(this.value.trim().toLowerCase());
        });
    }

    if (document.readyState !== 'loading') {
        initAdminUsersPage();
    } else {
        document.addEventListener('DOMContentLoaded', initAdminUsersPage);
    }

    async function fetchUsers() {
        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/users`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
                return;
            }

            const data = await response.json();

            if (response.ok) {
                const raw = data.data || data.users || (Array.isArray(data) ? data : []);
                // Only show admin/treasurer users, exclude members and applicants
                allUsers = raw.filter(u => {
                    const roles = (u.roles || []).map(r => r.toLowerCase());
                    return roles.includes('admin') || roles.includes('superadmin') || roles.includes('super_admin') || roles.includes('treasurer');
                });
                renderUsers('');
            } else {
                document.getElementById('usersTableBody').innerHTML = `<tr><td colspan="4" class="empty-state">Failed to load users.</td></tr>`;
            }
        } catch (err) {
            console.error('Error fetching users:', err);
            document.getElementById('usersTableBody').innerHTML = `<tr><td colspan="4" class="empty-state">Network error.</td></tr>`;
        }
    }

    // --- RENDER USERS GRID ---
    function renderUsers() {
        const tbody = document.getElementById('usersTableBody');
        const query = document.getElementById('searchInput') ? document.getElementById('searchInput').value.toLowerCase() : '';

        // Filter the users based on the search input
        const filtered = allUsers.filter(user => {
            const n = (user.name || '').toLowerCase();
            const f = (user.first_name || '').toLowerCase();
            const l = (user.last_name || '').toLowerCase();
            const e = (user.email || '').toLowerCase();
            return n.includes(query) || f.includes(query) || l.includes(query) || e.includes(query);
        });

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-5">No admin users found.</td></tr>';
            return;
        }

        tbody.innerHTML = filtered.map(user => {
            // Generate Role Badges
            const roles = user.roles || [];
            const roleBadges = roles.map(r => {
                let cls = 'role-default';
                if (r === 'admin' || r === 'superadmin' || r === 'super_admin') cls = 'role-admin';
                else if (r === 'treasurer') cls = 'role-treasurer';
                else if (r === 'member') cls = 'role-member';
                return `<span class="role-badge ${cls}">${r}</span>`;
            }).join(' ') || '<span class="role-badge role-default">user</span>';

            // Format Date
            const rawDate = user.created_at || user.date_created || user.registered_at || user.joined_at || null;
            const created = rawDate ?
                new Date(rawDate).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                }) :
                new Date().toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

            // ==========================================
            // SMART NAME FALLBACK LOGIC
            // ==========================================
            let fName = user.first_name;
            let lName = user.last_name;

            // If the database has a blank first name, try to split it out of the old 'name' column!
            if (!fName && user.name) {
                const parts = user.name.split(' ');
                fName = parts[0] || 'N/A';
                lName = parts.slice(1).join(' ') || 'N/A';
            }

            // Draw the Table Row
            return `
                <tr>
                    <td style="font-weight: 600;">${fName || 'N/A'}</td>
                    <td style="font-weight: 600;">${lName || 'N/A'}</td>
                    <td>${user.email || 'N/A'}</td>
                    <td>${roleBadges}</td>
                    <td>${created}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary fw-bold me-1" onclick="editUser(${user.id})">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger fw-bold" onclick="deleteUser(${user.id})">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </td>
                </tr>`;
        }).join('');
    }

    // === Register Modal ===
    function openRegisterModal() {
        document.getElementById('registerModal').style.display = 'flex';
        document.getElementById('registerError').style.display = 'none';
    }

    function closeRegisterModal() {
        document.getElementById('registerModal').style.display = 'none';
        document.getElementById('registerForm').reset();
    }

    async function handleRegister(e) {
        e.preventDefault();
        const btn = document.getElementById('regSubmitBtn');
        const errorDiv = document.getElementById('registerError');
        btn.disabled = true;
        btn.innerText = 'Registering...';
        errorDiv.style.display = 'none';

        const firstName = document.getElementById('regFirstName').value;
        const lastName = document.getElementById('regLastName').value;
        const email = document.getElementById('regEmail').value;
        const role = document.getElementById('regRole').value;

        try {
            // If editing, hit the /v1/users/{id} route. If creating, hit /register.
            const targetUrl = editUserId ? `${window.API_BASE_URL}/v1/users/${editUserId}` : `${window.API_BASE_URL}/register`;
            const methodType = editUserId ? 'PUT' : 'POST';

            const response = await fetch(targetUrl, {
                method: methodType,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    first_name: firstName,
                    last_name: lastName,
                    email: email,
                    role: role
                })
            });

            const data = await response.json();

            if (response.ok || response.status === 201) {
                closeRegisterModal();

                // Only show the success password modal if we CREATED a new user!
                if (!editUserId) {
                    showSuccessModal(email, data.generated_password);
                } else {
                    alert("User updated successfully!");
                }

                fetchUsers(); // Refresh table
                showSuccessModal(email, data.generated_password);
            } else {
                let msg = data.message || 'Registration failed.';
                if (data.errors) msg += ' ' + Object.values(data.errors).flat().join(' ');
                errorDiv.innerText = msg;
                errorDiv.style.display = 'block';
            }
        } catch (err) {
            errorDiv.innerText = 'Network error. Please try again.';
            errorDiv.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerText = 'Register Account';
        }
    }

    // Global variable to track if we are Editing or Creating
    let editUserId = null;

    // --- EDIT USER LOGIC ---
    function editUser(id) {
        // Find the user data from your existing list
        const user = allUsers.find(u => Number(u.id) === Number(id));
        if (!user) return;

        editUserId = user.id; // Set global ID

        // Populate the modal fields
        document.getElementById('regFirstName').value = user.first_name || '';
        document.getElementById('regLastName').value = user.last_name || '';
        document.getElementById('regEmail').value = user.email || '';

        // Try to set the role dropdown
        const userRole = user.roles && user.roles.length > 0 ? user.roles[0] : 'admin';
        const roleDropdown = document.getElementById('regRole');
        if (roleDropdown) roleDropdown.value = userRole;

        // Change Modal UI to "Edit Mode" (Smart Lookups that won't crash)
        const titleEl = document.getElementById('registerModalTitle') || document.querySelector('#registerModal h2') || document.querySelector('#registerModal h3') || document.querySelector('#registerModal .modal-title');
        if (titleEl) titleEl.innerHTML = '<i class="bi bi-pencil-square me-2"></i> Edit Admin User';

        const btnEl = document.getElementById('regSubmitBtn') || document.querySelector('#registerModal button[type="submit"]');
        if (btnEl) btnEl.innerHTML = '<i class="bi bi-save"></i> Save Changes';

        const errEl = document.getElementById('registerError');
        if (errEl) errEl.style.display = 'none';

        document.getElementById('registerModal').style.display = 'flex';
    }

    // --- OVERRIDE OPEN MODAL (For fresh creations) ---
    function openRegisterModal() {
        editUserId = null; // Reset ID

        const form = document.getElementById('registerForm') || document.querySelector('#registerModal form');
        if (form) form.reset();

        // Revert Modal UI back to "Register Mode"
        const titleEl = document.getElementById('registerModalTitle') || document.querySelector('#registerModal h2') || document.querySelector('#registerModal h3') || document.querySelector('#registerModal .modal-title');
        if (titleEl) titleEl.innerHTML = '<i class="bi bi-person-plus-fill me-2"></i> Register New Admin';

        const btnEl = document.getElementById('regSubmitBtn') || document.querySelector('#registerModal button[type="submit"]');
        if (btnEl) btnEl.innerHTML = 'Register Account';

        const errEl = document.getElementById('registerError');
        if (errEl) errEl.style.display = 'none';

        document.getElementById('registerModal').style.display = 'flex';
    }

    // --- DELETE USER LOGIC ---
    async function deleteUser(id) {
        if (!confirm("Are you sure you want to permanently delete this user? This cannot be undone.")) return;

        const token = localStorage.getItem('token');
        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.ok) {
                fetchUsers(); // Refresh the table instantly
                alert("User deleted successfully.");
            } else {
                const data = await response.json();
                alert('Delete failed: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            alert('Network error while trying to delete.');
        }
    }

    // === Success Modal ===
    function showSuccessModal(email, password) {
        document.getElementById('successEmail').innerText = email;
        document.getElementById('generatedPassword').innerText = password;
        document.getElementById('successModal').style.display = 'flex';
        document.getElementById('copyBtn').innerHTML = '<i class="bi bi-clipboard"></i> Copy Password';
    }

    function closeSuccessModal() {
        document.getElementById('successModal').style.display = 'none';
        document.getElementById('generatedPassword').innerText = '';
    }

    function copyPassword() {
        navigator.clipboard.writeText(document.getElementById('generatedPassword').innerText).then(() => {
            document.getElementById('copyBtn').innerHTML = '<i class="bi bi-check-lg"></i> Copied!';
        });
    }
</script>

<style>
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

@endsection