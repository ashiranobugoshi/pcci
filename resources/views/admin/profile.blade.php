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
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eee;
    }

    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .initials {
        font-size: 2rem;
        font-weight: bold;
        color: #888;
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
            {{-- HIDDEN FILE INPUT --}}
            <input type="file" id="avatarInput" accept="image/*" onchange="handleAccountImageChange(this)" style="display: none;">

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

{{-- MODAL FOR CROPPING --}}
<div class="modal-overlay" id="cropModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center;">
    <div class="modal-card" style="background: #fff; width: 90%; max-width: 500px; padding: 20px; border-radius: 12px;">
        <h5 class="fw-bold mb-3">Crop Image</h5>
        <div style="max-height: 400px;"><img id="cropperImage" src="" style="max-width: 100%;"></div>
        <div class="d-flex justify-content-end gap-2 mt-3">
            <button class="btn btn-secondary" onclick="closeCropModal()">Cancel</button>
            <button class="btn text-white" style="background-color: var(--pcci-red);" onclick="applyCrop()">Apply Crop</button>
        </div>
    </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
    let cropper = null;
    let accountImageFile = null; // Holds the file to be uploaded
    var token = localStorage.getItem('token');

    if (!token) {
        window.location.href = '/login';
    }

    // --- AVATAR CROPPING LOGIC ---
    function handleAccountImageChange(input) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            // 1. Show the Modal
            const modal = document.getElementById('cropModal');
            if (modal) modal.style.display = 'flex';

            // 2. Load image into Cropper
            const img = document.getElementById('cropperImage');
            img.src = e.target.result;

            if (window.cropper) window.cropper.destroy();
            window.cropper = new Cropper(img, {
                aspectRatio: 1,
                viewMode: 1
            });
        };
        reader.readAsDataURL(file);
    }

    window.cropper = null;
    window.accountImageFile = null;

    function applyCrop() {
        if (!window.cropper) {
            console.error("Cropper instance not found!");
            return;
        }

        window.cropper.getCroppedCanvas({
            width: 500,
            height: 500
        }).toBlob((blob) => {
            // 1. Create the File Object for the backend
            window.accountImageFile = new File([blob], "profile_crop.jpg", {
                type: "image/jpeg"
            });

            // 2. Generate a local preview URL
            const previewUrl = URL.createObjectURL(window.accountImageFile);

            // 3. Update the Profile Page Preview
            document.getElementById('avatarPreview').innerHTML = `<img src="${previewUrl}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;

            // 4. Update the Sidebar/Navbar Preview (if they exist)
            const sidebarAvatar = document.querySelector('.sidebar .avatar');
            if (sidebarAvatar) sidebarAvatar.src = previewUrl;

            const navbarAvatar = document.getElementById('navbarAvatar');
            if (navbarAvatar) navbarAvatar.src = previewUrl;

            // 5. Force Close Modal
            document.getElementById('cropModal').style.display = 'none';

        }, 'image/jpeg', 0.9);
    }

    function closeCropModal() {
        document.getElementById('cropModal').style.display = 'none';
    }

    async function removeAvatar() {
        if (!confirm("Are you sure you want to remove your profile photo?")) return;

        try {
            // Ensure this matches the route in routes/api.php exactly
            const response = await fetch(`${window.API_BASE_URL}/user/avatar`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                alert('Avatar removed successfully.');
                // Refresh UI
                location.reload();
            } else {
                console.error("Failed to delete avatar");
            }
        } catch (err) {
            console.error('Error:', err);
        }
    }

    // function loadAvatarFromStorage() {
    //     const savedAvatar = localStorage.getItem('adminAvatar');
    //     if (savedAvatar) {
    //         document.getElementById('avatarPreview').innerHTML = `<img src="${savedAvatar}" alt="Avatar">`;
    //         const sidebarAvatar = document.querySelector('.sidebar .avatar');
    //         if (sidebarAvatar) sidebarAvatar.src = savedAvatar;
    //     } else {
    //         updateInitials();
    //     }
    // }

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
        const preview = document.getElementById('avatarPreview');
        // Set a "loading" state immediately
        preview.innerHTML = `<span class="initials">...</span>`;

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

                // 1. Update text fields
                document.getElementById('profileName').value = user.name || (user.first_name + ' ' + user.last_name) || '';
                document.getElementById('profileEmail').value = user.email || '';

                // 2. Set the image URL 
                // We use 'photo_url' which you set up in your User.php model
                const avatarUrl = user.photo_url;

                if (avatarUrl) {
                    // Success: Set the image and STOP. Do NOT run updateInitials().
                    preview.innerHTML = `<img src="${avatarUrl}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;

                    const sidebarAvatar = document.querySelector('.sidebar .avatar');
                    if (sidebarAvatar) sidebarAvatar.src = avatarUrl;
                } else {
                    // Only if photo_url is missing, run initials
                    updateInitials();
                }
            } else {
                updateInitials();
            }
        } catch (err) {
            console.error('Error fetching profile:', err);
            updateInitials();
        }
    }

    async function loadProfile() {
        const preview = document.getElementById('avatarPreview');
        // Set a loading state
        preview.innerHTML = `<span class="initials">...</span>`;

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

                // 1. POPULATE TEXT FIELDS (Restored from your old code)
                document.getElementById('profileName').value = user.name || (user.first_name + ' ' + user.last_name) || '';
                document.getElementById('profileEmail').value = user.email || '';
                document.getElementById('profileRole').value = (user.roles && user.roles.length > 0) ? user.roles.join(', ') : 'Admin';

                document.getElementById('profileJoined').value = user.created_at ?
                    new Date(user.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) : 'N/A';

                // 2. POPULATE IMAGES (Restored from the new Backblaze logic)
                const avatarUrl = user.photo_url; // This comes from your UserResource

                if (avatarUrl) {
                    // Update Main Preview
                    preview.innerHTML = `<img src="${avatarUrl}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;

                    // Update Sidebar Avatar
                    const sidebarAvatar = document.querySelector('.sidebar .avatar');
                    if (sidebarAvatar) sidebarAvatar.src = avatarUrl;

                    // Update Navbar Avatar
                    const navbarAvatar = document.getElementById('navbarAvatar');
                    if (navbarAvatar) navbarAvatar.src = avatarUrl;
                } else {
                    // Only run initials if no photo_url exists
                    updateInitials();
                }
            } else {
                // Error handling fallback
                document.getElementById('profileName').value = localStorage.getItem('userName') || '';
                document.getElementById('profileRole').value = 'Admin';
                document.getElementById('profileJoined').value = 'N/A';
                updateInitials();
            }
        } catch (err) {
            console.error('Error fetching profile:', err);
            updateInitials();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadProfile();
    });

    // --- SAVING LOGIC (BACKBLAZE & CORS READY) ---
    async function saveProfile() {
        const btn = document.getElementById('btnSaveProfile');
        const alertBox = document.getElementById('profileAlert');
        const formData = new FormData();

        const newName = document.getElementById('profileName').value.trim();
        const newEmail = document.getElementById('profileEmail').value.trim();

        // CRITICAL FOR LARAVEL: Tells Laravel to treat this POST as a PUT route
        formData.append('_method', 'PUT');
        formData.append('name', newName);
        formData.append('email', newEmail);

        // Also append first/last name as the backend changeInfo might expect them
        const nameParts = newName.split(' ');
        formData.append('first_name', nameParts[0] || '');
        formData.append('last_name', nameParts.slice(1).join(' ') || '');

        // Only append image if one exists from the cropper
        if (window.accountImageFile) {
            formData.append('image', window.accountImageFile, 'avatar.jpg');
        }

        try {
            btn.disabled = true;
            btn.textContent = 'Saving...';
            alertBox.style.display = 'none';

            // Ensure we hit the correct endpoint using POST to avoid CORS/Redirect issues with files
            const response = await fetch(`${window.API_BASE_URL}/v1/user/change-info`, {
                method: 'POST', // Strictly POST for FormData
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                    // Do NOT set Content-Type here
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                showAlert(alertBox, 'Profile updated successfully!', 'success');

                window.accountImageFile = null;
                localStorage.setItem('userName', newName);

                // Update UI instantly
                if (data.user) {
                    const realAvatarUrl = data.user.photo_url || data.user.image_url || data.user.avatar || data.user.profile_photo_path;

                    if (realAvatarUrl) {
                        const finalUrl = realAvatarUrl.startsWith('http') ? realAvatarUrl : `${window.location.origin}/storage/${realAvatarUrl}`;
                        document.getElementById('avatarPreview').innerHTML = `<img src="${finalUrl}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;

                        const sidebarAvatar = document.querySelector('.sidebar .avatar');
                        if (sidebarAvatar) sidebarAvatar.src = finalUrl;
                    }
                }

                // Force a hard reload to ensure all cached data is cleared
                setTimeout(() => location.reload(), 1000);

            } else {
                console.error("Backend returned error:", data);
                showAlert(alertBox, 'Error: ' + (data.message || 'Validation failed'), 'error');
            }
        } catch (err) {
            console.error("Fetch failed:", err);
            showAlert(alertBox, 'Network Error while saving.', 'error');
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