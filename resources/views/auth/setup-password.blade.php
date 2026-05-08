@extends('layouts.app')
@include('partials.api-config')
@section('title', 'Secure Account - PCCI')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;500;700&family=Poppins:wght@600;700;800&display=swap');

    body {
        background-color: #f3f4f6;
        /* Switched to light gray background for a cleaner PCCI feel */
        color: #333333;
        font-family: 'DM Sans', sans-serif;
    }

    .setup-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding-top: 60px;
        padding-bottom: 40px;
    }

    .glass-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }

    .form-control-custom {
        background-color: #f9fafb !important;
        border: 1px solid #e5e7eb !important;
        color: #111827 !important;
        border-radius: 8px;
        padding: 12px;
        transition: all 0.2s;
    }

    .form-control-custom:focus {
        border-color: #b61b2a !important;
        box-shadow: 0 0 0 0.25rem rgba(182, 27, 42, 0.15);
        outline: none;
        background-color: #ffffff !important;
    }

    .input-group-text-custom {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-left: none;
        cursor: pointer;
        color: #6b7280;
        border-radius: 0 8px 8px 0;
    }

    .form-control-custom:focus+.input-group-text-custom {
        border-color: #b61b2a;
    }

    .btn-red-custom {
        background-color: #b61b2a;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 700;
        width: 100%;
        transition: all 0.3s;
    }

    .btn-red-custom:hover:not(:disabled) {
        background-color: #8f1521;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(182, 27, 42, 0.2);
    }

    .btn-red-custom:disabled {
        background-color: #d1d5db;
        cursor: not-allowed;
    }

    /* Validation Checklist Styles */
    .validation-list {
        list-style: none;
        padding: 0;
        margin: 10px 0 0 0;
        font-size: 0.8rem;
        color: #6b7280;
    }

    .validation-list li {
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .validation-list li i {
        font-size: 0.9rem;
    }

    .val-invalid i {
        color: #ef4444;
    }

    /* Red cross */
    .val-valid {
        color: #10b981;
    }

    /* Green check */
    .val-valid i {
        color: #10b981;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }

    .spin {
        display: inline-block;
        animation: spin 1s linear infinite;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="setup-container">
    <div class="container">
        <div class="row align-items-center justify-content-center">

            <div class="col-lg-5">
                <div class="glass-card">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/PCCI-Logo.png') }}" alt="PCCI Logo" style="width: 75px; height: 75px; margin-bottom: 15px;">
                        <h4 class="fw-bold mb-2" style="color: #111827; font-family: 'Poppins', sans-serif;">
                            Secure Your Account
                        </h4>
                        <p class="text-muted" style="font-size: 0.9rem;">Since this is your newly made account, you must set a personal, secure password before accessing the dashboard.</p>
                    </div>

                    <form id="setupPasswordForm" onsubmit="submitFirstTimePassword(event)">

                        <div class="mb-3">
                            <label class="form-label" style="color: #4b5563; font-weight: 600; font-size: 0.85rem;">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="new_password" class="form-control form-control-custom border-end-0" placeholder="Enter new password" required oninput="validatePassword()">
                                <span class="input-group-text input-group-text-custom" onclick="togglePasswordVisibility('new_password', 'toggleIcon1')">
                                    <i class="bi bi-eye-slash" id="toggleIcon1"></i>
                                </span>
                            </div>

                            <ul class="validation-list" id="password-validation">
                                <li id="req-len" class="val-invalid"><i class="bi bi-x-circle-fill"></i> At least 8 characters</li>
                                <li id="req-upper" class="val-invalid"><i class="bi bi-x-circle-fill"></i> One uppercase letter</li>
                                <li id="req-lower" class="val-invalid"><i class="bi bi-x-circle-fill"></i> One lowercase letter</li>
                                <li id="req-num" class="val-invalid"><i class="bi bi-x-circle-fill"></i> One number</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" style="color: #4b5563; font-weight: 600; font-size: 0.85rem;">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="new_password_confirmation" class="form-control form-control-custom border-end-0" placeholder="Re-type password" required oninput="checkMatch()">
                                <span class="input-group-text input-group-text-custom" onclick="togglePasswordVisibility('new_password_confirmation', 'toggleIcon2')">
                                    <i class="bi bi-eye-slash" id="toggleIcon2"></i>
                                </span>
                            </div>
                            <small id="match-text" class="d-none mt-1 fw-bold" style="font-size: 0.8rem;"></small>
                        </div>

                        <div id="setupError" class="alert alert-danger d-none mt-3" style="font-size: 0.85rem; padding: 10px; background-color: #fef2f2; color: #ef4444; border: 1px solid #fca5a5;"></div>

                        <button type="submit" id="btnSetup" class="btn-red-custom mt-2" disabled>
                            Update Password & Continue
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    let isPasswordValid = false;
    let doPasswordsMatch = false;

    document.addEventListener('DOMContentLoaded', () => {
        if (!localStorage.getItem('token')) {
            window.location.href = '/login';
        }
    });

    // 1. Show/Hide Password Toggle
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("bi-eye-slash", "bi-eye");
        } else {
            input.type = "password";
            icon.classList.replace("bi-eye", "bi-eye-slash");
        }
    }

    // 2. Real-time Password Validation
    function validatePassword() {
        const pw = document.getElementById('new_password').value;
        let validCount = 0;

        const setValid = (elementId) => {
            const el = document.getElementById(elementId);
            el.className = 'val-valid';
            el.querySelector('i').className = 'bi bi-check-circle-fill';
            validCount++;
        };
        const setInvalid = (elementId) => {
            const el = document.getElementById(elementId);
            el.className = 'val-invalid';
            el.querySelector('i').className = 'bi bi-x-circle-fill';
        };

        if (pw.length >= 8) setValid('req-len');
        else setInvalid('req-len');
        if (/[A-Z]/.test(pw)) setValid('req-upper');
        else setInvalid('req-upper');
        if (/[a-z]/.test(pw)) setValid('req-lower');
        else setInvalid('req-lower');
        if (/[0-9]/.test(pw)) setValid('req-num');
        else setInvalid('req-num');

        isPasswordValid = (validCount === 4);
        checkMatch();
    }

    // 3. Confirm Password Match Checker
    function checkMatch() {
        const pw1 = document.getElementById('new_password').value;
        const pw2 = document.getElementById('new_password_confirmation').value;
        const matchText = document.getElementById('match-text');
        const btn = document.getElementById('btnSetup');

        if (pw2.length > 0) {
            matchText.classList.remove('d-none');
            if (pw1 === pw2) {
                matchText.className = 'mt-1 fw-bold text-success';
                matchText.innerHTML = '<i class="bi bi-check-circle-fill"></i> Passwords match';
                doPasswordsMatch = true;
            } else {
                matchText.className = 'mt-1 fw-bold text-danger';
                matchText.innerHTML = '<i class="bi bi-x-circle-fill"></i> Passwords do not match';
                doPasswordsMatch = false;
            }
        } else {
            matchText.classList.add('d-none');
            doPasswordsMatch = false;
        }

        // Enable button only if everything is perfect
        btn.disabled = !(isPasswordValid && doPasswordsMatch);
    }

    // 4. Submission & Smart Redirection
    async function submitFirstTimePassword(event) {
        event.preventDefault();

        const token = localStorage.getItem('token');
        const pass = document.getElementById('new_password').value;
        const passConfirm = document.getElementById('new_password_confirmation').value;
        const errorBox = document.getElementById('setupError');
        const btn = document.getElementById('btnSetup');

        errorBox.classList.add('d-none');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Securing Account...';

        try {
            const apiUrl = `${window.API_BASE_URL}/v1/user/first-time-password-change`;

            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    new_password: pass,
                    new_password_confirmation: passConfirm
                })
            });

            const data = await response.json();

            if (response.ok) {
                btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Success! Redirecting...';
                btn.classList.replace('btn-red-custom', 'btn-success');

                // Smart Redirection: Fetch user role to send them to the correct dashboard
                try {
                    const userRes = await fetch(`${window.API_BASE_URL}/v1/user`, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    });

                    if (userRes.ok) {
                        const userData = await userRes.json();
                        const user = userData.data || userData;
                        const roles = user.roles ? user.roles.map(r => r.name) : [];

                        if (roles.includes('super_admin') || roles.includes('admin')) {
                            window.location.href = '/admin/dashboard';
                        } else if (roles.includes('treasurer')) {
                            window.location.href = '/treasurer/dashboard';
                        } else {
                            window.location.href = '/member-dashboard';
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
                    errorHtml += '<ul style="margin-bottom:0; padding-left:20px; margin-top:5px;">';
                    for (const [field, messages] of Object.entries(data.errors)) {
                        errorHtml += `<li>${messages.join(', ')}</li>`;
                    }
                    errorHtml += '</ul>';
                }
                errorBox.innerHTML = errorHtml;
                errorBox.classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = 'Update Password & Continue';
            }
        } catch (error) {
            errorBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Network error. Please try again.';
            errorBox.classList.remove('d-none');
            btn.disabled = false;
            btn.innerHTML = 'Update Password & Continue';
        }
    }
</script>
@endsection