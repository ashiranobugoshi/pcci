@extends('layouts.admin')

@section('title', 'My Profile - PCCI Admin')

@section('content')
@include('partials.api-config')

<div class="dashboard-header">MY PROFILE</div>

<style>
    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    .profile-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 24px;
        max-width: 800px;
    }

    .profile-card h5 {
        font-weight: 700;
        color: #be1e38;
        margin-bottom: 20px;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
    }

    .form-group input,
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"] {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.95rem;
        font-family: 'Inter', sans-serif;
        color: #333;
        background-color: #fff;
        transition: border 0.2s;
        display: block;
    }

    .form-group input:focus {
        outline: none;
        border-color: #be1e38;
        box-shadow: 0 0 0 3px rgba(190, 30, 56, 0.1);
    }

    .form-group input:disabled {
        background: #f5f5f5;
        color: #999;
        cursor: not-allowed;
    }

    .btn-save {
        background: #be1e38;
        color: #fff;
        border: none;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-save:hover {
        background: #a01a30;
    }

    .btn-save:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .alert-box {
        display: none;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .alert-success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .profile-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width: 768px) {
        .profile-row {
            grid-template-columns: 1fr;
        }
    }

    .avatar-section {
        display: flex;
        align-items: center;
        gap: 24px;
        margin-bottom: 24px;
    }

    .avatar-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 3px solid #be1e38;
        object-fit: cover;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-preview .initials {
        font-size: 2rem;
        font-weight: 700;
        color: #be1e38;
    }

    .avatar-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .btn-upload {
        background: #be1e38;
        color: #fff;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-upload:hover {
        background: #a01a30;
    }

    .btn-remove {
        background: none;
        color: #888;
        border: 1px solid #ddd;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-remove:hover {
        border-color: #be1e38;
        color: #be1e38;
    }

    @media (max-width: 768px) {
        .profile-card {
            padding: 20px;
            max-width: 100%;
        }

        .avatar-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
    }

    @media (max-width: 576px) {
        .profile-card {
            padding: 16px;
        }

        .avatar-preview {
            width: 84px;
            height: 84px;
        }

        .avatar-preview .initials {
            font-size: 1.6rem;
        }

        .avatar-actions {
            width: 100%;
        }

        .btn-upload,
        .btn-remove,
        .btn-save {
            width: 100%;
        }
    }
</style>

{{-- Profile Info Card --}}
<div class="profile-card">
    <h5><i class="bi bi-person-circle"></i> Account Information</h5>
    <div id="profileAlert" class="alert-box"></div>

    {{-- Avatar Upload --}}
    <div class="avatar-section">
        <div class="avatar-preview" id="avatarPreview">
            <span class="initials" id="avatarInitials">A</span>
        </div>
        <div class="avatar-actions">
            <input type="file" id="avatarInput" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
            <button class="btn-upload" onclick="document.getElementById('avatarInput').click()">
                <i class="bi bi-camera"></i> Change Photo
            </button>
            <button class="btn-remove" onclick="removeAvatar()">
                <i class="bi bi-trash3"></i> Remove
            </button>
            <span style="font-size: 0.75rem; color: #999;">JPG, PNG. Max 2MB.</span>
        </div>
    </div>

    <div class="profile-row">
        <div class="form-group">
            <label>Name</label>
            <input type="text" id="profileName" placeholder="Your name">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" id="profileEmail" placeholder="Your email">
        </div>
    </div>

    <div class="profile-row">
        <div class="form-group">
            <label>Role</label>
            <input type="text" id="profileRole" disabled>
        </div>
        <div class="form-group">
            <label>Joined</label>
            <input type="text" id="profileJoined" disabled>
        </div>
    </div>

    <button class="btn-save" id="btnSaveProfile" onclick="saveProfile()">Save Changes</button>
</div>

{{-- Change Password Card --}}
<div class="profile-card">
    <h5><i class="bi bi-shield-lock"></i> Change Password</h5>
    <div id="passwordAlert" class="alert-box"></div>

    <div class="form-group">
        <label>Current Password</label>
        <input type="password" id="currentPassword" placeholder="Enter current password">
    </div>

    <div class="profile-row">
        <div class="form-group">
            <label>New Password</label>
            <input type="password" id="newPassword" placeholder="Enter new password">
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" id="confirmPassword" placeholder="Confirm new password">
        </div>
    </div>

    <button class="btn-save" id="btnChangePassword" onclick="changePassword()">Change Password</button>
</div>

<script>
    var token = localStorage.getItem('token');
    let avatarChanged = false;

    if (!token) {
        window.location.href = '/login';
    }

    // === Avatar Functions ===
    function previewAvatar(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];

        if (file.size > 2 * 1024 * 1024) {
            showAlert(document.getElementById('profileAlert'), 'Image must be under 2MB.', 'error');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Avatar">`;
            avatarChanged = true;
        };
        reader.readAsDataURL(file);
    }

    function removeAvatar() {
        const preview = document.getElementById('avatarPreview');
        const name = document.getElementById('profileName').value || 'A';
        const initials = name.substring(0, 2).toUpperCase();
        preview.innerHTML = `<span class="initials">${initials}</span>`;
        document.getElementById('avatarInput').value = '';
        avatarChanged = true;
        localStorage.removeItem('adminAvatar');

        // Update sidebar avatar
        const sidebarAvatar = document.querySelector('.sidebar .avatar');
        if (sidebarAvatar) sidebarAvatar.src = 'https://i.pravatar.cc/150?u=default';
    }

    function loadAvatarFromStorage() {
        const savedAvatar = localStorage.getItem('adminAvatar');
        if (savedAvatar) {
            document.getElementById('avatarPreview').innerHTML = `<img src="${savedAvatar}" alt="Avatar">`;
            // Also update sidebar
            const sidebarAvatar = document.querySelector('.sidebar .avatar');
            if (sidebarAvatar) sidebarAvatar.src = savedAvatar;
        } else {
            updateInitials();
        }
    }

    function updateInitials() {
        const name = document.getElementById('profileName').value || localStorage.getItem('userName') || 'A';
        const words = name.split(' ');
        let initials = name.substring(0, 2).toUpperCase();
        if (words.length > 1) initials = (words[0][0] + words[1][0]).toUpperCase();
        document.getElementById('avatarInitials')?.replaceWith();
        const preview = document.getElementById('avatarPreview');
        if (!preview.querySelector('img')) {
            preview.innerHTML = `<span class="initials">${initials}</span>`;
        }
    }

    function initProfilePage() {
        loadProfile();
    }

    if (document.readyState !== 'loading') {
        initProfilePage();
    } else {
        document.addEventListener('DOMContentLoaded', initProfilePage);
    }

    async function loadProfile() {
        const storedName = localStorage.getItem('userName') || '';
        document.getElementById('profileName').value = storedName;

        // Load saved avatar
        loadAvatarFromStorage();

        // Fetch user data from API
        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/user`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.ok) {
                const data = await response.json();
                const user = data.user || data.data || data;
                document.getElementById('profileName').value = user.name || storedName;
                document.getElementById('profileEmail').value = user.email || '';
                document.getElementById('profileRole').value = (user.roles || []).join(', ') || 'Admin';
                document.getElementById('profileJoined').value = user.created_at ?
                    new Date(user.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) :
                    'N/A';

                // Load avatar from API if available
                if (user.avatar || user.photo_url) {
                    const avatarUrl = user.avatar || user.photo_url;
                    document.getElementById('avatarPreview').innerHTML = `<img src="${avatarUrl}" alt="Avatar">`;
                    localStorage.setItem('adminAvatar', avatarUrl);
                }
            } else {
                document.getElementById('profileName').value = storedName;
                document.getElementById('profileRole').value = 'Admin';
                document.getElementById('profileJoined').value = 'N/A';
            }
        } catch (err) {
            console.error('Error fetching profile:', err);
            document.getElementById('profileName').value = storedName;
            document.getElementById('profileRole').value = 'Admin';
        }

        updateInitials();
    }

    async function saveProfile() {
        const btn = document.getElementById('btnSaveProfile');
        const alertBox = document.getElementById('profileAlert');
        const name = document.getElementById('profileName').value.trim();
        const email = document.getElementById('profileEmail').value.trim();
        const avatarFile = document.getElementById('avatarInput').files[0];

        if (!name) {
            showAlert(alertBox, 'Name is required.', 'error');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Saving...';
        alertBox.style.display = 'none';

        // Save avatar to localStorage if changed
        if (avatarChanged && avatarFile) {
            const reader = new FileReader();
            reader.onload = function(e) {
                localStorage.setItem('adminAvatar', e.target.result);
                // Update sidebar avatar immediately
                const sidebarAvatar = document.querySelector('.sidebar .avatar');
                if (sidebarAvatar) sidebarAvatar.src = e.target.result;
            };
            reader.readAsDataURL(avatarFile);
        }

        try {
            // Try sending as FormData to support avatar upload
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('_method', 'PUT');
            if (avatarFile) formData.append('avatar', avatarFile);

            const response = await fetch(`${window.API_BASE_URL}/v1/user`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                localStorage.setItem('userName', name);
                // Update sidebar name
                const sidebarName = document.getElementById('sidebarAdminName');
                if (sidebarName) sidebarName.textContent = name.toUpperCase();

                avatarChanged = false;
                showAlert(alertBox, 'Profile updated successfully!', 'success');
            } else {
                // Even if API fails, save locally
                localStorage.setItem('userName', name);
                const sidebarName = document.getElementById('sidebarAdminName');
                if (sidebarName) sidebarName.textContent = name.toUpperCase();

                showAlert(alertBox, 'Profile saved locally. API: ' + (data.message || 'Could not sync to server.'), 'success');
            }
        } catch (err) {
            // Save locally even on network error
            localStorage.setItem('userName', name);
            showAlert(alertBox, 'Profile saved locally. Could not reach server.', 'success');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Save Changes';
        }
    }

    async function changePassword() {
        const btn = document.getElementById('btnChangePassword');
        const alertBox = document.getElementById('passwordAlert');
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        alertBox.style.display = 'none';

        if (!currentPassword || !newPassword || !confirmPassword) {
            showAlert(alertBox, 'All password fields are required.', 'error');
            return;
        }
        if (newPassword.length < 8) {
            showAlert(alertBox, 'New password must be at least 8 characters.', 'error');
            return;
        }
        if (newPassword !== confirmPassword) {
            showAlert(alertBox, 'New password and confirmation do not match.', 'error');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Changing...';

        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/change-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    password: newPassword,
                    password_confirmation: confirmPassword
                })
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                showAlert(alertBox, 'Password changed successfully!', 'success');
                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmPassword').value = '';
            } else {
                const msg = data.message || 'Failed to change password.';
                const errors = data.errors ? '\n' + Object.values(data.errors).flat().join('\n') : '';
                showAlert(alertBox, msg + errors, 'error');
            }
        } catch (err) {
            showAlert(alertBox, 'Network error. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Change Password';
        }
    }

    function showAlert(el, msg, type) {
        el.textContent = msg;
        el.className = 'alert-box ' + (type === 'success' ? 'alert-success' : 'alert-error');
        el.style.whiteSpace = 'pre-line';
        el.style.display = 'block';
    }
</script>
@endsection