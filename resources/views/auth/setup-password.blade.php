@extends('layouts.auth')

@section('title', 'Secure Account Setup - PCCI')

@section('content')
@include('partials.api-config')

{{--
  =============================================================================
  PCCI MODERN PASSWORD SETUP PAGE
  Description: A secure, modern account onboarding screen.
  Includes: 
    - Custom modern SaaS-style CSS
    - Regex-based password strength validation
    - Real-time UI feedback
    - Secure API communication
  =============================================================================
--}}

<style>
    /* 1. Global Variables & Reset */
    :root {
        --pcci-red: #be1e38;
        --bg-body: #f8fafc;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --input-bg: #f1f5f9;
        --border-radius: 12px;
    }

    body {
        background-color: var(--bg-body);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* 2. Container Wrapper */
    .setup-wrapper {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 24px;
    }

    /* 3. Modern Card Design */
    .setup-card {
        background: #ffffff;
        border-radius: var(--border-radius);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 450px;
        padding: 40px;
        transition: transform 0.3s ease;
    }

    /* 4. Header Section */
    .setup-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .setup-header h2 {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .setup-header p {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    /* 5. Form Group Styling */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-dark);
        text-transform: uppercase;
        margin-bottom: 8px;
        letter-spacing: 0.6px;
    }

    .form-control {
        background: var(--input-bg);
        border: 2px solid transparent;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        width: 100%;
    }

    .form-control:focus {
        background: #fff;
        border-color: var(--pcci-red);
        box-shadow: 0 0 0 4px rgba(190, 30, 56, 0.1);
        outline: none;
    }

    /* 6. Password Visibility Icon */
    .input-group {
        position: relative;
    }

    .input-group-text {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        cursor: pointer;
        color: var(--text-muted);
        z-index: 10;
    }

    /* 7. Action Button */
    .btn-submit {
        background-color: var(--pcci-red);
        color: white;
        width: 100%;
        padding: 14px;
        border-radius: 10px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-top: 10px;
    }

    .btn-submit:hover {
        background-color: #a01a30;
    }

    .btn-submit:disabled {
        background-color: #cbd5e1;
        cursor: not-allowed;
    }

    /* 8. Validation Checklist */
    .validation-checklist {
        margin-top: 24px;
        font-size: 0.8rem;
        color: var(--text-muted);
        background: #f8fafc;
        padding: 16px;
        border-radius: 10px;
    }

    .check-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        transition: 0.2s;
    }

    .check-item.valid {
        color: #059669;
        font-weight: 600;
    }

    .check-item.invalid {
        color: #e11d48;
        font-weight: 600;
    }

    /* Responsive adjustment */
    @media (max-width: 480px) {
        .setup-card {
            padding: 24px;
        }
    }
</style>

<div class="setup-wrapper">
    <div class="setup-card">

        <div class="setup-header">
            <h2>Setup Password</h2>
            <p>Define a secure password for your account.</p>
        </div>

        {{-- Error Alert --}}
        <div id="setupAlert" class="alert alert-danger d-none mb-3"
            style="font-size: 0.85rem; border-radius: 8px; border: none; background: #fee2e2; color: #991b1b;">
        </div>

        <form id="setupForm" onsubmit="handleSetup(event)">

            {{-- Input: Password --}}
            <div class="form-group">
                <label>New Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="newPassword"
                        placeholder="••••••••" required maxlength="50">
                    <button type="button" class="input-group-text" onclick="togglePassword('newPassword', 'eye1')">
                        <i class="bi bi-eye" id="eye1"></i>
                    </button>
                </div>
            </div>

            {{-- Input: Confirm --}}
            <div class="form-group">
                <label>Confirm Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirmPassword"
                        placeholder="••••••••" required maxlength="50">
                    <button type="button" class="input-group-text" onclick="togglePassword('confirmPassword', 'eye2')">
                        <i class="bi bi-eye" id="eye2"></i>
                    </button>
                </div>
            </div>

            {{-- Validation Rules UI --}}
            <div class="validation-checklist">
                <div class="check-item" id="ruleLength"><i class="bi bi-circle"></i> 8 - 50 characters</div>
                <div class="check-item" id="ruleCap"><i class="bi bi-circle"></i> One uppercase letter</div>
                <div class="check-item" id="ruleDigit"><i class="bi bi-circle"></i> One number</div>
                <div class="check-item" id="ruleSymbol"><i class="bi bi-circle"></i> One special character</div>
                <div class="check-item" id="ruleMatch"><i class="bi bi-circle"></i> Passwords match</div>
            </div>

            {{-- Submission --}}
            <button type="submit" class="btn-submit" id="setupBtn" disabled>
                Save & Continue
            </button>
        </form>
    </div>
</div>

<script>
    /**
     * PCCI SECURITY SETUP SCRIPT
     * Handles password validation and backend API redirection.
     */
    const token = localStorage.getItem('token');

    // 1. Authentication Check
    if (!token) {
        window.location.href = '/login';
    }

    // 2. Utility: Password Visibility
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

    // 3. Validation Logic
    const newPasswordInput = document.getElementById('newPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const setupBtn = document.getElementById('setupBtn');

    function validatePasswords() {
        const p1 = newPasswordInput.value;
        const p2 = confirmPasswordInput.value;

        // Validation Criteria
        const rules = {
            ruleLength: p1.length >= 8 && p1.length <= 50,
            ruleCap: /[A-Z]/.test(p1),
            ruleDigit: /[0-9]/.test(p1),
            ruleSymbol: /[^A-Za-z0-9]/.test(p1),
            ruleMatch: p1.length > 0 && p1 === p2
        };

        // UI Updates
        Object.keys(rules).forEach(id => {
            const el = document.getElementById(id);
            if (p1.length === 0 && id !== 'ruleMatch') {
                el.className = 'check-item';
                el.querySelector('i').className = 'bi bi-circle';
            } else {
                el.className = rules[id] ? 'check-item valid' : 'check-item invalid';
                el.querySelector('i').className = rules[id] ? 'bi bi-check-circle-fill' : 'bi bi-x-circle-fill';
            }
        });

        // Toggle button state
        setupBtn.disabled = !Object.values(rules).every(Boolean);
    }

    newPasswordInput.addEventListener('input', validatePasswords);
    confirmPasswordInput.addEventListener('input', validatePasswords);

    // 4. Submission Handler
    async function handleSetup(e) {
        e.preventDefault();

        if (setupBtn.disabled) return;

        const alertBox = document.getElementById('setupAlert');
        alertBox.classList.add('d-none');
        setupBtn.disabled = true;
        setupBtn.innerText = 'Updating Security...';

        try {
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
                setupBtn.innerText = 'Redirecting...';

                // Fetch User Role to route correctly
                const userRes = await fetch(`${window.API_BASE_URL}/v1/user`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (userRes.ok) {
                    const userData = await userRes.json();
                    const user = userData.user || userData.data || userData;
                    const roles = user.roles || [];
                    const roleString = JSON.stringify(roles).toLowerCase();

                    // Route based on roles
                    if (roleString.includes('super_admin') || roleString.includes('admin')) {
                        window.location.href = '/dashboard';
                    } else if (roleString.includes('treasurer')) {
                        window.location.href = '/treasurer-dashboard';
                    } else {
                        window.location.href = '/member-dashboard';
                    }
                } else {
                    window.location.href = '/member-dashboard';
                }
            } else {
                // Display specific validation errors from backend
                let errorMessage = data.message || 'Setup failed.';
                if (data.errors) {
                    errorMessage = Object.values(data.errors).flat().join('<br>');
                }
                alertBox.innerHTML = errorMessage;
                alertBox.classList.remove('d-none');
                setupBtn.disabled = false;
                setupBtn.innerText = 'Save & Continue';
            }
        } catch (error) {
            alertBox.innerHTML = 'Network error. Please try again later.';
            alertBox.classList.remove('d-none');
            setupBtn.disabled = false;
            setupBtn.innerText = 'Save & Continue';
        }
    }
</script>

{{--
  =============================================================================
  NOTE: 
  This code block is designed to be modern, scalable, and responsive. 
  It purposefully keeps the layout contained in a centered card.
  If you need additional helper methods, ensure they are placed 
  within this same script context for optimal memory management.
  
  The logic flow ensures:
  1. Frontend validation runs first (saving API traffic).
  2. Backend validation runs second (ensuring data integrity).
  3. Dynamic routing ensures the user ends up in their specific dashboard.
  =============================================================================
--}}

@endsection