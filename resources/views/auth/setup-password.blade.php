@extends('layouts.app')

@section('title', 'Secure Account Setup - PCCI')

@section('content')
@include('partials.api-config')

<style>
    body {
        background-color: #f4f6f9;
        font-family: 'Inter', sans-serif;
    }

    .setup-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .setup-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        width: 100%;
        max-width: 900px;
        display: flex;
        flex-direction: row;
    }

    /* Left Side - Branding */
    .setup-left {
        background-color: var(--pcci-red, #be1e38);
        color: white;
        padding: 50px 40px;
        width: 45%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .setup-left i {
        font-size: 5rem;
        margin-bottom: 20px;
    }

    .setup-left h2 {
        font-weight: 700;
        font-size: 1.8rem;
        margin-bottom: 10px;
        letter-spacing: 1px;
    }

    .setup-left p {
        font-size: 0.95rem;
        opacity: 0.9;
    }

    /* Right Side - Form */
    .setup-right {
        padding: 50px 40px;
        width: 55%;
        background: #ffffff;
    }

    .setup-right h4 {
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }

    .setup-right p.subtitle {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 30px;
    }

    .form-group label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #555;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .input-group-text {
        background: transparent;
        cursor: pointer;
        border-left: none;
    }

    .form-control {
        border-right: none;
        padding: 12px 16px;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: var(--pcci-red, #be1e38);
    }

    .form-control:focus+.input-group-text {
        border-color: var(--pcci-red, #be1e38);
        color: var(--pcci-red, #be1e38);
    }

    .btn-submit {
        background-color: var(--pcci-red, #be1e38);
        color: white;
        font-weight: 700;
        padding: 12px;
        border-radius: 6px;
        width: 100%;
        border: none;
        transition: 0.2s;
    }

    .btn-submit:hover {
        background-color: #a01a30;
        color: white;
    }

    .btn-submit:disabled {
        background-color: #d1d5db;
        cursor: not-allowed;
    }

    /* Checklist Styles */
    .validation-checklist {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 25px;
        display: block;
        /* Always visible to prevent UI stretching */
    }

    .validation-checklist p {
        margin: 0 0 8px 0;
        font-size: 0.8rem;
        font-weight: 700;
        color: #333;
        text-transform: uppercase;
    }

    .check-item {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        transition: color 0.2s;
    }

    .check-item i {
        margin-right: 8px;
        font-size: 1rem;
        transition: transform 0.2s;
    }

    .check-item.valid {
        color: #10b981;
    }

    .check-item.invalid {
        color: #be1e38;
    }

    @media (max-width: 768px) {
        .setup-card {
            flex-direction: column;
        }

        .setup-left,
        .setup-right {
            width: 100%;
            padding: 30px 20px;
        }

        .setup-left {
            padding: 40px 20px;
        }
    }
</style>

<div class="setup-wrapper">
    <div class="setup-card">

        <div class="setup-left">
            <i class="bi bi-shield-lock-fill"></i>
            <h2>PCCI SECURE</h2>
            <p>ACCOUNT SETUP</p>
        </div>

        <div class="setup-right">
            <h4>Setup Your Password</h4>
            <p class="subtitle">Please choose a strong password to secure your new account before accessing the dashboard.</p>

            <div id="setupAlert" class="alert alert-danger d-none" style="font-size: 0.9rem;"></div>

            <form id="setupForm" onsubmit="handleSetup(event)">

                <div class="form-group mb-3">
                    <label>New Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="newPassword" placeholder="Minimum 8 characters" required>
                        <span class="input-group-text" onclick="togglePassword('newPassword', 'eyeIcon1')">
                            <i class="bi bi-eye" id="eyeIcon1"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label>Confirm Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm your password" required>
                        <span class="input-group-text" onclick="togglePassword('confirmPassword', 'eyeIcon2')">
                            <i class="bi bi-eye" id="eyeIcon2"></i>
                        </span>
                    </div>
                </div>

                <div id="checklistContainer" class="validation-checklist">
                    <p>Password Requirements:</p>
                    <div class="check-item" id="ruleLength"><i class="bi bi-circle"></i> At least 8 characters long</div>
                    <div class="check-item" id="ruleMatch"><i class="bi bi-circle"></i> Passwords match</div>
                </div>

                <button type="submit" class="btn-submit" id="setupBtn" disabled>
                    Save & Continue <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const token = localStorage.getItem('token');

    // Kick out unauthenticated users trying to bypass login
    if (!token) {
        window.location.href = '/login';
    }

    // --- Toggle Password Visibility ---
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    // --- REAL-TIME VALIDATION UI ---
    const newPasswordInput = document.getElementById('newPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const setupBtn = document.getElementById('setupBtn');
    const alertBox = document.getElementById('setupAlert');
    const ruleLength = document.getElementById('ruleLength');
    const ruleMatch = document.getElementById('ruleMatch');

    function validatePasswords() {
        const p1 = newPasswordInput.value;
        const p2 = confirmPasswordInput.value;

        const isLengthValid = p1.length >= 8;
        const isMatchValid = p1.length > 0 && p1 === p2;

        // Update Length UI (Gray if empty, Red if under 8, Green if 8+)
        if (isLengthValid) {
            ruleLength.className = 'check-item valid';
            ruleLength.innerHTML = '<i class="bi bi-check-circle-fill"></i> At least 8 characters long';
        } else if (p1.length > 0) {
            ruleLength.className = 'check-item invalid';
            ruleLength.innerHTML = '<i class="bi bi-x-circle-fill"></i> At least 8 characters long';
        } else {
            ruleLength.className = 'check-item';
            ruleLength.innerHTML = '<i class="bi bi-circle"></i> At least 8 characters long';
        }

        // Update Match UI (Gray if empty, Red if mistyped, Green if matching)
        if (isMatchValid) {
            ruleMatch.className = 'check-item valid';
            ruleMatch.innerHTML = '<i class="bi bi-check-circle-fill"></i> Passwords match';
        } else if (p2.length > 0) {
            ruleMatch.className = 'check-item invalid';
            ruleMatch.innerHTML = '<i class="bi bi-x-circle-fill"></i> Passwords match';
        } else {
            ruleMatch.className = 'check-item';
            ruleMatch.innerHTML = '<i class="bi bi-circle"></i> Passwords match';
        }

        // Lock button until rules are met
        if (isLengthValid && isMatchValid) {
            setupBtn.disabled = false;
            return true;
        } else {
            setupBtn.disabled = true;
            return false;
        }
    }

    // Listen as the user types
    newPasswordInput.addEventListener('input', validatePasswords);
    confirmPasswordInput.addEventListener('input', validatePasswords);


    // --- Handle Setup Form Submission & Smart Routing ---
    async function handleSetup(e) {
        e.preventDefault();

        if (!validatePasswords()) return;

        alertBox.classList.add('d-none');
        setupBtn.disabled = true;
        setupBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Updating...';

        try {
            // 1. Submit Password
            const response = await fetch(`${window.API_BASE_URL}/v1/user/first-time-password-change`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    new_password: newPasswordInput.value,
                    new_password_confirmation: confirmPasswordInput.value
                })
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                setupBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Loading Dashboard...';

                try {
                    // 2. Fetch Explicit User Roles
                    const userRes = await fetch(`${window.API_BASE_URL}/v1/user`, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    });

                    if (userRes.ok) {
                        const userData = await userRes.json();
                        const user = userData.user || userData.data || userData;

                        let roles = [];
                        if (user.roles && Array.isArray(user.roles)) {
                            roles = user.roles.map(r => typeof r === 'string' ? r.toLowerCase() : (r.name ? r.name.toLowerCase() : ''));
                        }

                        // 3. Smart Redirection! Map explicitly to your routes in web.php
                        if (roles.includes('super_admin') || roles.includes('superadmin') || roles.includes('admin')) {
                            window.location.href = '/dashboard'; // Admin Route
                        } else if (roles.includes('treasurer')) {
                            window.location.href = '/treasurer-dashboard'; // Treasurer Route
                        } else {
                            window.location.href = '/member-dashboard'; // Member Route
                        }
                    } else {
                        window.location.href = '/member-dashboard'; // Safe fallback
                    }
                } catch (e) {
                    window.location.href = '/member-dashboard'; // Safe fallback
                }

            } else {
                let errorHtml = `<b>Update Failed:</b> ${data.message || 'Invalid data.'}`;
                if (data.errors) {
                    errorHtml += '<ul class="mb-0 ps-3 mt-1">';
                    for (const [field, messages] of Object.entries(data.errors)) {
                        errorHtml += `<li>${messages.join(', ')}</li>`;
                    }
                    errorHtml += '</ul>';
                }

                alertBox.innerHTML = errorHtml;
                alertBox.classList.remove('d-none');
                setupBtn.disabled = false;
                setupBtn.innerHTML = 'Save & Continue <i class="bi bi-arrow-right ms-1"></i>';
            }
        } catch (error) {
            alertBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Network error. Please try again.';
            alertBox.classList.remove('d-none');
            setupBtn.disabled = false;
            setupBtn.innerHTML = 'Save & Continue <i class="bi bi-arrow-right ms-1"></i>';
        }
    }
</script>
@endsection