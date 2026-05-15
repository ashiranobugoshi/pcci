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

{{-- Change Password Card (OTP Flow) --}}
<div class="profile-card">
    <h5><i class="bi bi-shield-lock"></i> Change Password</h5>
    <div id="pwdAlertMsg" class="alert-box"></div>

    <div id="step1SendOtp">
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 16px;">To securely change your password, we must first verify your identity. Click the button below to send a 6-digit OTP to your registered email.</p>
        <button type="button" class="btn-save" id="btnRequestOtp" onclick="requestPasswordOtp()">
            Request OTP via Email
        </button>
    </div>

    <form id="step2ChangeForm" style="display: none;" onsubmit="submitNewPassword(event)">

        <div class="form-group" style="max-width: 350px;">
            <label>Enter 6-Digit OTP</label>
            <div style="display: flex; gap: 10px;">
                <input type="text" id="changeOtpInput" placeholder="123456" required maxlength="6" style="letter-spacing: 4px; font-size: 1.1rem; text-align: center; font-weight: bold; flex: 1;">
                <button type="button" class="btn-save" id="btnVerifyOtp" onclick="lockOtpField()" style="padding: 10px 20px;">Verify</button>
            </div>
            <small style="color: #888; font-size: 0.8rem; margin-top: 6px; display: block;" id="otpHelpText">Please check your email for the code.</small>
        </div>

        <div id="newPasswordSection" style="display: none; border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;">
            <div class="profile-row">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" id="newPasswordInput" placeholder="Enter new password" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" id="confirmNewPasswordInput" placeholder="Confirm new password" required>
                </div>
            </div>
            <button type="submit" class="btn-save" id="btnSavePassword">
                <i class="bi bi-save"></i> Save New Password
            </button>
        </div>
    </form>
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

        const sidebarAvatar = document.querySelector('.sidebar .avatar');
        if (sidebarAvatar) sidebarAvatar.src = 'https://i.pravatar.cc/150?u=default';
    }

    function loadAvatarFromStorage() {
        const savedAvatar = localStorage.getItem('adminAvatar');
        if (savedAvatar) {
            document.getElementById('avatarPreview').innerHTML = `<img src="${savedAvatar}" alt="Avatar">`;
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

        loadAvatarFromStorage();

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

        if (avatarChanged && avatarFile) {
            const reader = new FileReader();
            reader.onload = function(e) {
                localStorage.setItem('adminAvatar', e.target.result);
                const sidebarAvatar = document.querySelector('.sidebar .avatar');
                if (sidebarAvatar) sidebarAvatar.src = e.target.result;
            };
            reader.readAsDataURL(avatarFile);
        }

        try {
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('_method', 'PUT');
            if (avatarFile) formData.append('avatar', avatarFile);

            const response = await fetch(`${window.API_BASE_URL}/v1/user/change-info`, {
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
                const sidebarName = document.getElementById('sidebarAdminName');
                if (sidebarName) sidebarName.textContent = name.toUpperCase();

                avatarChanged = false;
                showAlert(alertBox, 'Profile updated successfully!', 'success');
            } else {
                localStorage.setItem('userName', name);
                const sidebarName = document.getElementById('sidebarAdminName');
                if (sidebarName) sidebarName.textContent = name.toUpperCase();

                showAlert(alertBox, 'Profile saved locally. API: ' + (data.message || 'Could not sync to server.'), 'success');
            }
        } catch (err) {
            localStorage.setItem('userName', name);
            showAlert(alertBox, 'Profile saved locally. Could not reach server.', 'success');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Save Changes';
        }
    }

    // --- STEP 1: Trigger the OTP Email ---
    async function requestPasswordOtp() {
        const btn = document.getElementById('btnRequestOtp');
        const alertBox = document.getElementById('pwdAlertMsg');

        btn.disabled = true;
        btn.textContent = 'Sending...';
        alertBox.style.display = 'none';

        try {
            const response = await fetch(`${window.API_BASE_URL}/user/confirm-password-change`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.ok) {
                document.getElementById('step1SendOtp').style.display = 'none';
                document.getElementById('step2ChangeForm').style.display = 'block';
                showAlert(alertBox, 'OTP sent! Please check your email for the 6-digit code.', 'success');
            } else {
                const data = await response.json().catch(() => ({}));
                showAlert(alertBox, data.message || 'Failed to send OTP.', 'error');
                btn.disabled = false;
                btn.textContent = 'Request OTP via Email';
            }
        } catch (error) {
            showAlert(alertBox, 'Network error. Please try again later.', 'error');
            btn.disabled = false;
            btn.textContent = 'Request OTP via Email';
        }
    }

    // --- STEP 2: Lock the OTP Field & Reveal Passwords ---
    function lockOtpField() {
        const otpInput = document.getElementById('changeOtpInput');
        const alertBox = document.getElementById('pwdAlertMsg');

        if (otpInput.value.trim().length === 6) {
            otpInput.setAttribute('readonly', true);
            otpInput.style.backgroundColor = '#e9ecef';
            otpInput.style.cursor = 'not-allowed';
            otpInput.style.color = '#6c757d';

            document.getElementById('btnVerifyOtp').style.display = 'none';
            document.getElementById('otpHelpText').innerText = 'OTP Locked.';
            document.getElementById('newPasswordSection').style.display = 'block';

            alertBox.style.display = 'none';
        } else {
            showAlert(alertBox, 'Please enter a valid 6-digit OTP code.', 'error');
        }
    }

    // --- STEP 3: Submit the Final Request ---
    async function submitNewPassword(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSavePassword');
        const alertBox = document.getElementById('pwdAlertMsg');

        const otp = document.getElementById('changeOtpInput').value.trim();
        const newPassword = document.getElementById('newPasswordInput').value;
        const confirmPassword = document.getElementById('confirmNewPasswordInput').value;

        if (newPassword !== confirmPassword) {
            showAlert(alertBox, 'Passwords do not match!', 'error');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Saving...';
        alertBox.style.display = 'none';

        try {
            const response = await fetch(`${window.API_BASE_URL}/user/request-password-change`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    otp: otp,
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword
                })
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                showAlert(alertBox, 'Password successfully changed!', 'success');
                document.getElementById('step2ChangeForm').style.display = 'none';
            } else {
                let errorHtml = data.message || 'Validation failed.';
                if (data.errors) {
                    errorHtml += '\n' + Object.values(data.errors).flat().join('\n');
                }
                showAlert(alertBox, errorHtml, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-save"></i> Save New Password';
            }
        } catch (error) {
            showAlert(alertBox, 'Network error. Please try again.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save"></i> Save New Password';
        }
    }

    // --- UTILITY: Show Alerts ---
    function showAlert(el, msg, type) {
        el.innerText = msg;
        el.className = 'alert-box ' + (type === 'success' ? 'alert-success' : 'alert-error');
        el.style.whiteSpace = 'pre-line';
        el.style.display = 'block';
    }
</script>
@endsection