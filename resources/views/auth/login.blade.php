@extends('layouts.app')
@include('partials.api-config')

@section('content')
<style>
    :root {
        --primary-red: #be1e38;
        --dark-bg: #222431;
        --card-bg: #2b2d3c; 
        --input-bg: #323545;
        --text-grey: #a0aec0;
        --text-white: #ffffff;
    }

    .login-page-wrapper {
        font-family: 'DM Sans', sans-serif;
        background-color: var(--dark-bg);
        color: var(--text-white);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding-top: 120px;    
        padding-bottom: 80px;  
        padding-left: 15px;
        padding-right: 15px;
    }

    .login-container {
        background-color: var(--card-bg);
        display: flex;
        overflow: hidden;
        border-radius: 20px;
        border: 1px solid rgba(155, 152, 152, 0.63);
        box-shadow: 0px 6px 14.7px rgba(108, 120, 175, 0.47);
        max-width: 1000px; 
        width: 100%;
    }

    .login-form-side {
        flex: 1;
        padding: 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        box-shadow: 0 4px 20px rgba(69, 70, 123, 0.58);
        z-index: 2;
    }

    .form-header h1 {
        font-family: 'Poppins', sans-serif;
        font-size: clamp(2rem, 5vw, 2.5rem); /* Responsive Font */
        margin-bottom: 5px;
    }

    .form-header p {
        color: var(--text-grey);
        margin-bottom: 30px;
    }

    /* STRICT FORM GROUP STYLING */
    .custom-form-group { 
        display: block !important;
        width: 100% !important;
        margin-bottom: 25px; 
    }
    
    .custom-form-group label { 
        display: block !important;
        margin-bottom: 10px; 
        font-weight: 700; 
        font-size: 0.95rem; 
        color: var(--text-white);
    }

    .input-wrapper { 
        position: relative; 
        width: 100% !important; 
        display: block !important;
    }
    
    .input-wrapper i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-red);
        font-size: 1.2rem;
        z-index: 5;
    }

    /* STRICT INPUT BOX STYLING */
    .input-wrapper input {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        height: 55px !important;
        box-sizing: border-box !important;
        margin: 0 !important;
        padding: 15px 15px 15px 55px !important; /* Extra padding to clear icon */
        background-color: var(--input-bg) !important;
        border: 1px solid #4a4d61 !important;
        border-radius: 8px !important;
        color: white !important;
        font-size: 1rem !important;
        outline: none !important;
        transition: border 0.3s;
    }

    .input-wrapper.password-wrapper input {
        padding-right: 55px !important;
    }

    .input-wrapper.password-wrapper .password-toggle {
        position: absolute;
        right: 18px !important;
        left: auto !important;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-grey);
        font-size: 1.1rem;
        cursor: pointer;
        z-index: 6;
    }

    .input-wrapper input:focus { 
        border-color: var(--primary-red) !important; 
    }

    .input-wrapper input::placeholder {
        color: #6b7280;
    }

    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap; /* Allows wrap on very small screens */
        gap: 10px;
        margin-bottom: 30px;
        font-size: 0.9rem;
        color: var(--text-grey);
    }
    
    .form-options a { 
        color: var(--text-grey); 
        text-decoration: underline; 
    }

    .btn-submit {
        width: 100%;
        padding: 15px;
        background-color: var(--primary-red);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }
    
    .btn-submit:hover { 
        background-color: #900f24; 
    }
    
    .btn-submit:disabled {
        background-color: #555;
        cursor: not-allowed;
    }

    .login-image-side {
        flex: 1;
        position: relative;
        background-color: #000;
    }
    
    .login-image-side img.bg-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.7;
    }

    .image-overlay {
        position: absolute;
        top: 25px;
        left: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 10;
    }
    
    #api-error {
        color: #ff6b6b;
        background-color: rgba(255, 107, 107, 0.1);
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        text-align: center;
        display: none; 
    }

    .forgot-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 2500;
        background: rgba(17, 20, 30, 0.5);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .forgot-modal-overlay.active {
        display: flex;
    }

    .forgot-modal-card {
        width: 100%;
        max-width: 430px;
        background: #2a2e3e;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        box-shadow: 0 18px 55px rgba(0, 0, 0, 0.45);
        padding: 28px 28px 24px;
        text-align: center;
        position: relative;
    }

    .forgot-modal-close {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        background: transparent;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
    }

    .forgot-modal-icon {
        width: 66px;
        height: 66px;
        margin: 0 auto 14px;
        border-radius: 999px;
        border: 2px solid rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 28px;
    }

    .forgot-modal-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1.95rem;
        margin-bottom: 14px;
        line-height: 1.2;
    }

    .forgot-modal-input {
        width: 100%;
        background: #232737;
        border: 1px solid #3a3f50;
        color: #fff;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 1rem;
        margin-bottom: 12px;
    }

    .forgot-password-field {
        position: relative;
    }

    .forgot-password-field .forgot-modal-input {
        padding-right: 44px;
    }

    .forgot-password-field .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        cursor: pointer;
        font-size: 1rem;
    }

    .forgot-modal-input:focus {
        outline: none;
        border-color: #4e598c;
        box-shadow: 0 0 0 0.2rem rgba(78, 89, 140, 0.25);
    }

    .forgot-modal-desc {
        color: #d1d5db;
        font-size: 0.92rem;
        line-height: 1.45;
        margin-bottom: 14px;
    }

    .forgot-modal-password-wrap {
        display: none;
    }

    .forgot-modal-password-wrap.active {
        display: block;
    }

    .forgot-modal-password-requirements {
        display: none;
        text-align: left;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 14px;
        margin: 8px 0 12px;
    }

    .forgot-modal-password-requirements.active {
        display: block;
    }

    .forgot-modal-password-requirements h6 {
        color: #ffffff;
        font-size: 0.92rem;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .forgot-modal-password-requirements ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .forgot-modal-password-requirements li {
        color: #9ca3af;
        font-size: 0.82rem;
        margin-bottom: 6px;
    }

    .forgot-modal-password-requirements li.valid {
        color: #22c55e;
    }

    .forgot-modal-otp-wrap {
        display: none;
        margin-bottom: 12px;
    }

    .forgot-modal-otp-wrap.active {
        display: block;
    }

    .forgot-modal-otp-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 0;
    }

    .forgot-modal-otp-input {
        flex: 1;
        min-width: 0;
        background: #232737;
        border: 1px solid #3a3f50;
        color: #fff;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 1.1rem;
        letter-spacing: 2px;
        text-align: center;
    }

    .forgot-modal-otp-input::placeholder {
        color: #6b7280;
        letter-spacing: 0;
    }

    .forgot-modal-otp-help {
        color: #9ca3af;
        font-size: 0.74rem;
        line-height: 1.35;
        text-align: left;
        margin: 0;
    }

    .forgot-modal-error {
        display: none;
        background: rgba(227, 38, 54, 0.18);
        border: 1px solid rgba(227, 38, 54, 0.75);
        color: #ffc3c7;
        border-radius: 8px;
        padding: 8px 10px;
        margin-bottom: 12px;
        font-size: 0.84rem;
    }

    .forgot-modal-action {
        min-width: 160px;
        margin: 10px auto 0;
        display: block;
    }

    .page-toast {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 3000;
        background: #16a34a;
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.9rem;
        font-weight: 600;
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.35);
        transform: translateX(140%);
        opacity: 0;
        transition: transform 0.35s ease, opacity 0.35s ease;
        pointer-events: none;
    }

    .page-toast.show {
        transform: translateX(0);
        opacity: 1;
    }

    /* ===== RESPONSIVE MEDIA QUERIES ===== */
    @media (max-width: 991.98px) {
        .login-container { flex-direction: column; }
        .login-image-side { height: 250px; order: -1; }
        .login-form-side { padding: 40px; }
    }

    @media (max-width: 575.98px) {
        .login-page-wrapper { padding-top: 100px; padding-bottom: 40px; }
        .login-form-side { padding: 30px 20px; }
        .form-options { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="login-page-wrapper">
    <div class="login-container">
        <div class="login-form-side">
            <div class="form-header">
                <h1>Sign in</h1>
                <p>Welcome back! Please enter your details</p>
            </div>

            <div id="api-error"></div>

            <form id="loginForm" onsubmit="handleLogin(event)">
                @csrf
                
                <div class="custom-form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" name="email" required placeholder="@gmail.com" autofocus>
                    </div>
                </div>

                <div class="custom-form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper password-wrapper">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                        <i class="bi bi-eye password-toggle" id="passwordToggle" onclick="toggleLoginPassword()" aria-label="Show password" role="button" tabindex="0"></i>
                    </div>
                </div>

                <div class="form-options">
                    <label>
                        <input type="checkbox" name="remember"> Remember for 30 Days
                    </label>
                    <a href="#" onclick="openForgotPasswordModal(event)">Forgot password</a>
                </div>

                <button type="submit" id="submitBtn" class="btn-submit">Sign In</button>
                
                <p style="text-align: center; margin-top: 20px; font-size: 0.9rem; color: var(--text-grey);">
                    Don't have an account? <a href="{{ route('signup') }}" style="color: white; font-weight: bold;">Sign Up</a>
                </p>
            </form>
        </div>

        <div class="login-image-side">
            <div class="image-overlay">
                <img src="{{ asset('images/PCCI-Logo.svg') }}" style="height: 35px;" alt="Logo">
                <div style="color: white; line-height: 1.2;">
                    <strong style="font-family: 'Poppins', sans-serif; font-size: 1.1rem;">PCCI - Valenzuela</strong><br>
                    <span style="font-size: 0.8rem;">Philippine Chamber of Commerce and Industry</span>
                </div>
            </div>
            
            <img src="{{ asset('images/log in.png') }}" alt="Background" class="bg-img">
        </div>
    </div>
</div>

<div id="forgotModalOverlay" class="forgot-modal-overlay">
    <div class="forgot-modal-card">
        <button type="button" class="forgot-modal-close" aria-label="Close" onclick="closeForgotPasswordModal()">&times;</button>
        <div class="forgot-modal-icon"><i class="bi bi-envelope"></i></div>
        <h2 class="forgot-modal-title">Forgot Password</h2>
        <input id="forgotEmail" type="email" class="forgot-modal-input" placeholder="Enter your email address">
        <p id="forgotDesc" class="forgot-modal-desc">Enter your email and click Verify Email to receive an OTP.</p>

        <button type="button" id="forgotEmailVerifyBtn" class="btn-submit forgot-modal-action" onclick="sendForgotOtp()">Verify Email</button>

        <div id="forgotOtpWrap" class="forgot-modal-otp-wrap">
            <label for="forgotOtp" class="forgot-modal-desc mb-2" style="margin-bottom: 8px; display: block; text-align: left; font-size: 0.9rem;">OTP Code</label>
            <div class="forgot-modal-otp-row">
                <input id="forgotOtp" type="text" class="forgot-modal-otp-input" placeholder="Enter OTP" maxlength="6" inputmode="numeric">
            </div>
            <p class="forgot-modal-otp-help mt-2">Paste the one-time code sent to your email directly into this box.</p>
            <button type="button" id="forgotVerifyOtpBtn" class="btn-submit forgot-modal-action" onclick="verifyForgotOtp()">Verify OTP</button>
        </div>

        <div id="forgotPasswordWrap" class="forgot-modal-password-wrap">
            <div class="forgot-password-field">
                <input id="forgotPassword" type="password" class="forgot-modal-input" placeholder="New password">
                <i class="bi bi-eye password-toggle" id="forgotPasswordToggle" onclick="toggleForgotPasswordField('forgotPassword', 'forgotPasswordToggle')" aria-label="Show password" role="button" tabindex="0"></i>
            </div>

            <div class="forgot-password-field">
                <input id="forgotPasswordConfirm" type="password" class="forgot-modal-input" placeholder="Confirm new password">
                <i class="bi bi-eye password-toggle" id="forgotPasswordConfirmToggle" onclick="toggleForgotPasswordField('forgotPasswordConfirm', 'forgotPasswordConfirmToggle')" aria-label="Show password" role="button" tabindex="0"></i>
            </div>

            <div id="forgotPasswordRequirements" class="forgot-modal-password-requirements">
                <h6>Password must include:</h6>
                <ul>
                    <li id="forgotReqLower">At least one lower case letter</li>
                    <li id="forgotReqLen">Minimum of 8 characters</li>
                    <li id="forgotReqUpper">At least one upper case letter</li>
                    <li id="forgotReqNum">At least one number</li>
                </ul>
            </div>

            <button type="button" id="forgotResetBtn" class="btn-submit forgot-modal-action" onclick="handleForgotReset()">Reset Password</button>
        </div>

        <div id="forgotModalError" class="forgot-modal-error">Please enter a valid email address.</div>
    </div>
</div>

<div id="pageToast" class="page-toast" role="status" aria-live="polite"></div>

<script>
    let forgotStep = 'email';
    let toastTimer = null;
    const forgotApi = {
        sendOtp: 'https://pcciv-api.onrender.com/api/forgot-password/send-otp',
        reset: 'https://pcciv-api.onrender.com/api/forgot-password/reset',
    };

    function toggleLoginPassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('passwordToggle');

        if (!passwordInput || !toggleIcon) return;

        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        toggleIcon.classList.toggle('bi-eye', !isHidden);
        toggleIcon.classList.toggle('bi-eye-slash', isHidden);
        toggleIcon.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email || '').trim());
    }

    function toggleForgotPasswordField(inputId, toggleId) {
        const input = document.getElementById(inputId);
        const toggle = document.getElementById(toggleId);
        if (!input || !toggle) return;

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        toggle.classList.toggle('bi-eye', !isHidden);
        toggle.classList.toggle('bi-eye-slash', isHidden);
        toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    }

    function showPageToast(message) {
        const toast = document.getElementById('pageToast');
        if (!toast) return;

        if (toastTimer) {
            clearTimeout(toastTimer);
            toastTimer = null;
        }

        toast.textContent = message;
        toast.classList.add('show');

        toastTimer = setTimeout(() => {
            toast.classList.remove('show');
        }, 1800);
    }

    function openForgotPasswordModal(event) {
        if (event) event.preventDefault();
        resetForgotModal();

        const loginEmail = (document.getElementById('email')?.value || '').trim();
        const emailEl = document.getElementById('forgotEmail');
        if (emailEl) {
            emailEl.value = loginEmail;
            emailEl.readOnly = false;
            emailEl.style.opacity = '1';
        }

        const overlay = document.getElementById('forgotModalOverlay');
        if (overlay) overlay.classList.add('active');
        emailEl?.focus();
    }

    function closeForgotPasswordModal() {
        const overlay = document.getElementById('forgotModalOverlay');
        if (overlay) overlay.classList.remove('active');
    }

    function resetForgotModal() {
        forgotStep = 'email';
        const emailEl = document.getElementById('forgotEmail');
        const otpEl = document.getElementById('forgotOtp');
        const pwEl = document.getElementById('forgotPassword');
        const pwcEl = document.getElementById('forgotPasswordConfirm');
        const otpWrap = document.getElementById('forgotOtpWrap');
        const pwWrap = document.getElementById('forgotPasswordWrap');
        const reqWrap = document.getElementById('forgotPasswordRequirements');
        const desc = document.getElementById('forgotDesc');
        const emailBtn = document.getElementById('forgotEmailVerifyBtn');
        const otpBtn = document.getElementById('forgotVerifyOtpBtn');
        const resetBtn = document.getElementById('forgotResetBtn');
        const err = document.getElementById('forgotModalError');

        if (emailEl) {
            emailEl.value = '';
            emailEl.readOnly = false;
            emailEl.style.opacity = '1';
        }
        if (otpEl) otpEl.value = '';
        if (pwEl) pwEl.value = '';
        if (pwcEl) pwcEl.value = '';
        if (otpWrap) otpWrap.classList.remove('active');
        if (pwWrap) pwWrap.classList.remove('active');
        if (reqWrap) reqWrap.classList.remove('active');
        if (desc) desc.textContent = 'Enter your email and click Verify Email to receive an OTP.';
        if (emailBtn) {
            emailBtn.disabled = false;
            emailBtn.style.display = 'block';
            emailBtn.textContent = 'Verify Email';
        }
        if (otpBtn) otpBtn.style.display = 'block';
        if (resetBtn) resetBtn.style.display = 'block';
        if (err) {
            err.style.display = 'none';
            err.textContent = 'Please enter a valid email address.';
        }
    }

    function showForgotOtpStep(emailValue) {
        forgotStep = 'otp';
        const emailEl = document.getElementById('forgotEmail');
        const otpWrap = document.getElementById('forgotOtpWrap');
        const otpEl = document.getElementById('forgotOtp');
        const desc = document.getElementById('forgotDesc');
        const emailBtn = document.getElementById('forgotEmailVerifyBtn');

        if (emailEl) {
            emailEl.readOnly = true;
            emailEl.style.opacity = '0.75';
        }
        if (otpWrap) otpWrap.classList.add('active');
        if (desc) desc.textContent = `OTP sent to ${emailValue}. Enter the code below, then continue.`;
        if (emailBtn) emailBtn.style.display = 'none';
        otpEl?.focus();
    }

    function validateForgotPasswordRequirements() {
        const password = document.getElementById('forgotPassword')?.value || '';
        const reqLower = document.getElementById('forgotReqLower');
        const reqLen = document.getElementById('forgotReqLen');
        const reqUpper = document.getElementById('forgotReqUpper');
        const reqNum = document.getElementById('forgotReqNum');

        const lower = /[a-z]/.test(password);
        const len = password.length >= 8;
        const upper = /[A-Z]/.test(password);
        const num = /[0-9]/.test(password);

        if (reqLower) reqLower.classList.toggle('valid', lower);
        if (reqLen) reqLen.classList.toggle('valid', len);
        if (reqUpper) reqUpper.classList.toggle('valid', upper);
        if (reqNum) reqNum.classList.toggle('valid', num);

        return lower && len && upper && num;
    }

    async function parseForgotResponse(response) {
        const contentType = (response.headers.get('content-type') || '').toLowerCase();
        if (contentType.includes('application/json')) {
            return await response.json().catch(() => ({}));
        }
        const text = await response.text().catch(() => '');
        return { message: text || 'Unexpected server response.' };
    }

    async function sendForgotOtp() {
        const emailEl = document.getElementById('forgotEmail');
        const err = document.getElementById('forgotModalError');
        const emailBtn = document.getElementById('forgotEmailVerifyBtn');

        const email = (emailEl?.value || '').trim();
        if (!isValidEmail(email)) {
            if (err) {
                err.textContent = 'Please enter a valid email address.';
                err.style.display = 'block';
            }
            emailEl?.focus();
            return;
        }

        try {
            if (err) err.style.display = 'none';
            if (emailBtn) {
                emailBtn.disabled = true;
                emailBtn.textContent = 'Sending...';
            }

            const response = await fetch(forgotApi.sendOtp, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ email }),
            });

            const result = await parseForgotResponse(response);
            if (!response.ok) {
                throw new Error(result.message || 'Failed to send OTP. Please try again.');
            }

            showForgotOtpStep(email);
        } catch (error) {
            if (err) {
                err.textContent = error?.message || 'Failed to send OTP. Please try again.';
                err.style.display = 'block';
            }
        } finally {
            if (emailBtn) {
                emailBtn.disabled = false;
                emailBtn.textContent = 'Verify Email';
            }
        }
    }

    async function verifyForgotOtp() {
        const otpEl = document.getElementById('forgotOtp');
        const err = document.getElementById('forgotModalError');
        const desc = document.getElementById('forgotDesc');
        const otp = (otpEl?.value || '').replace(/\D/g, '');

        if (!/^\d{6}$/.test(otp)) {
            if (err) {
                err.textContent = 'Please enter a valid 6-digit OTP code.';
                err.style.display = 'block';
            }
            otpEl?.focus();
            return;
        }

        if (err) err.style.display = 'none';
        const otpBtn = document.getElementById('forgotVerifyOtpBtn');
        const otpWrap = document.getElementById('forgotOtpWrap');
        const pwWrap = document.getElementById('forgotPasswordWrap');
        const reqWrap = document.getElementById('forgotPasswordRequirements');

        if (otpBtn) otpBtn.style.display = 'none';
        if (otpWrap) otpWrap.style.display = 'none';
        if (pwWrap) pwWrap.classList.add('active');
        if (reqWrap) reqWrap.classList.add('active');
        if (desc) desc.textContent = 'OTP verified. You can now enter your new password.';
        forgotStep = 'password';
        validateForgotPasswordRequirements();
        document.getElementById('forgotPassword')?.focus();
    }

    async function handleForgotReset() {
        const emailEl = document.getElementById('forgotEmail');
        const otpEl = document.getElementById('forgotOtp');
        const pwEl = document.getElementById('forgotPassword');
        const pwcEl = document.getElementById('forgotPasswordConfirm');
        const err = document.getElementById('forgotModalError');
        const btn = document.getElementById('forgotResetBtn');

        if (forgotStep !== 'password') {
            if (err) {
                err.textContent = 'Please verify OTP first.';
                err.style.display = 'block';
            }
            return;
        }

        const email = (emailEl?.value || '').trim();
        const otp = (otpEl?.value || '').replace(/\D/g, '');
        const password = (pwEl?.value || '').trim();
        const password_confirmation = (pwcEl?.value || '').trim();

        if (!/^\d{6}$/.test(otp)) {
            if (err) {
                err.textContent = 'Please enter a valid 6-digit OTP code.';
                err.style.display = 'block';
            }
            otpEl?.focus();
            return;
        }

        if (!validateForgotPasswordRequirements()) {
            if (err) {
                err.textContent = 'Please meet all password requirements first.';
                err.style.display = 'block';
            }
            pwEl?.focus();
            return;
        }

        if (password !== password_confirmation) {
            if (err) {
                err.textContent = 'Password confirmation does not match.';
                err.style.display = 'block';
            }
            pwcEl?.focus();
            return;
        }

        try {
            if (err) err.style.display = 'none';
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Resetting...';
            }

            const response = await fetch(forgotApi.reset, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    email,
                    otp,
                    password,
                    password_confirmation,
                }),
            });

            const result = await parseForgotResponse(response);
            if (!response.ok) {
                throw new Error(result.message || 'Failed to reset password. Please try again.');
            }

            showPageToast(result.message || 'Password reset successful. You can now sign in.');
            closeForgotPasswordModal();
        } catch (error) {
            if (err) {
                err.textContent = error?.message || 'Failed to reset password. Please try again.';
                err.style.display = 'block';
            }
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Reset Password';
            }
        }
    }

    async function handleLogin(event) {
        event.preventDefault(); 

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const errorDiv = document.getElementById('api-error');
        const submitBtn = document.getElementById('submitBtn');

        // Reset UI
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';
        submitBtn.disabled = true;
        submitBtn.textContent = 'Signing In...';

        let response;
        const maxRetries = 3;
        const retryDelayMs = 2000; // 2 seconds

        try {
            // RETRY LOGIC LOOP
            for (let attempt = 1; attempt <= maxRetries; attempt++) {
                try {
                    response = await fetch(`${window.API_BASE_URL}/login`, {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'Accept': 'application/json' 
                        },
                        body: JSON.stringify({ email, password })
                    });
                    
                    // If fetch succeeds (even if it's a 401 unauthorized), break out of the retry loop.
                    // A response means the network is working.
                    break; 

                } catch (networkError) {
                    // This catches ERR_NAME_NOT_RESOLVED and ERR_NETWORK_CHANGED
                    console.warn(`Network error on attempt ${attempt}:`, networkError);
                    
                    if (attempt === maxRetries) {
                        throw new Error('Network unavailable. Please check your internet connection and try again.'); // Pass to the outer catch
                    }
                    
                    // Update UI to inform user
                    submitBtn.textContent = `Reconnecting (${attempt}/${maxRetries - 1})...`;
                    
                    // Wait before the next attempt
                    await new Promise(resolve => setTimeout(resolve, retryDelayMs));
                }
            }

            // --- PROCEED WITH EXISTING RESPONSE HANDLING ---
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                const textResponse = await response.text();
                console.error("API returned HTML instead of JSON. Here is the HTML:", textResponse);
                throw new Error("Server error: The API returned an invalid format (likely a 502 Bad Gateway or 404). Check console.");
            }

            const data = await response.json();

            if (response.ok) {
                localStorage.setItem('token', data.token);
                localStorage.setItem('userName', data.user.name);
                localStorage.setItem('userRoles', JSON.stringify(data.user.roles || []));

                const roles = data.user.roles || [];

                if (roles.includes('treasurer')) {
                    window.location.href = '/treasurer-dashboard';
                } else if (roles.includes('superadmin') || roles.includes('admin') || roles.includes('super_admin')) {
                    window.location.href = '/dashboard'; 
                } else if (roles.includes('member')) {
                    window.location.href = '/member-dashboard'; 
                } else {
                    window.location.href = '/'; 
                }
            } else {
                errorDiv.textContent = data.message || 'Login failed. Check credentials.';
                errorDiv.style.display = 'block';
            }

        } catch (err) {
            console.error("Fetch/Logic Error:", err);
            errorDiv.textContent = err.message || 'An error occurred. Please check your connection.';
            errorDiv.style.display = 'block';
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.textContent = 'Sign In';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const existingToken = localStorage.getItem('token');
        const existingRoles = JSON.parse(localStorage.getItem('userRoles') || '[]');

        if (existingToken) {
            const redirectUrl = existingRoles.includes('treasurer')
                ? '/treasurer-dashboard'
                : (existingRoles.includes('admin') || existingRoles.includes('superadmin') || existingRoles.includes('super_admin'))
                    ? '/dashboard'
                    : (existingRoles.includes('member') ? '/member-dashboard' : '/');

            window.location.href = redirectUrl;
            return;
        }

        const err = document.getElementById('forgotModalError');
        const otpEl = document.getElementById('forgotOtp');
        const emailEl = document.getElementById('forgotEmail');
        const pwEl = document.getElementById('forgotPassword');
        const pwcEl = document.getElementById('forgotPasswordConfirm');

        if (otpEl) {
            otpEl.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
                if (err) err.style.display = 'none';
            });
        }

        [emailEl, otpEl, pwEl, pwcEl].forEach(function (el) {
            if (!el) return;
            el.addEventListener('input', function () {
                if (err) err.style.display = 'none';
            });
            el.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    handleForgotReset();
                }
            });
        });

        if (pwEl) {
            pwEl.addEventListener('input', function () {
                validateForgotPasswordRequirements();
            });
        }

        if (pwcEl) {
            pwcEl.addEventListener('input', function () {
                validateForgotPasswordRequirements();
            });
        }
    });
</script>
@endsection