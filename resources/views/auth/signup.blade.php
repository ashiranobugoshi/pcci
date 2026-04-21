@extends('layouts.app')
@include('partials.api-config')
@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;500;700&family=Poppins:wght@600;700;800&display=swap');

    body {
        background-color: #1a1c23; 
        color: #ffffff;
        font-family: 'DM Sans', sans-serif; 
    }

    h1, h2, h3, h4, h5, h6, .hero-title, .step-title, .success-title {
        font-family: 'Poppins', sans-serif;
    }

    /* --- LAYOUT --- */
    .registration-container {
        min-height: 100vh;
        background: radial-gradient(circle at top right, #252836, #1a1c23);
        display: flex;
        align-items: center;
        padding-top: 80px;
        padding-bottom: 40px;
    }

    .glass-card {
        background: #252836;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 10px 10px 30px rgba(76, 203, 254, 0.2), 0 0 20px rgba(78, 89, 140, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.05);
        position: relative;
        min-height: 600px; 
    }

    /* --- FORM ELEMENTS --- */
    .form-label-custom {
        color: #ffffff;
        font-weight: 500;
        margin-bottom: 4px;
        display: block;
        font-family: 'DM Sans', sans-serif;
    }

    .helper-text-right {
        color: #9ca3af;
        font-size: 0.75rem;
        font-style: italic;
    }
    
    .helper-text-small {
        color: #9ca3af;
        font-size: 0.8rem;
        margin-bottom: 8px;
        display: block;
    }

    .text-danger { color: #e32636; }

    .form-control-dark, .form-select-dark {
        background-color: #1f222e !important;
        border: 1px solid #3a3f50 !important;
        color: white !important;
        border-radius: 8px;
        padding: 12px;
        font-family: 'DM Sans', sans-serif;
        width: 100%;
    }

    .form-control-dark:focus, .form-select-dark:focus {
        border-color: #4e598c !important;
        box-shadow: 0 0 0 0.25rem rgba(78, 89, 140, 0.25);
        outline: none;
    }

    .form-control-dark::placeholder {
        color: #8b92a5 !important;
        opacity: 1;
        font-weight: 400;
    }

    .form-control-dark[type="file"] { padding: 8px; }
    .form-control-dark[type="file"]::file-selector-button {
        background-color: #3a3f50;
        color: white;
        border: none;
        border-radius: 4px;
        margin-right: 10px;
        padding: 5px 10px;
        transition: 0.3s;
    }

    .form-select-dark {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
    }

    /* --- PROGRESS BAR --- */
    .step-progress {
        height: 6px;
        background-color: #3f4252;
        border-radius: 3px;
        margin: 20px 0 30px 0;
        overflow: hidden;
    }

    .step-progress-fill {
        width: 20%;
        height: 100%;
        background-color: #d1d5db; 
        border-radius: 3px;
        transition: width 0.5s ease;
    }

    /* --- SUCCESS PAGE STYLES --- */
    .success-icon-container {
        width: 80px;
        height: 80px;
        background-color: rgba(25, 135, 84, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px auto;
    }
    
    .success-icon {
        color: #22c55e;
        font-size: 2.5rem;
        -webkit-text-stroke: 2px;
    }

    .success-title {
        color: #22c55e;
        font-weight: 700;
        font-size: 1.75rem;
        margin-bottom: 15px;
    }

    .info-box {
        background-color: #4b4f5e;
        border-radius: 8px;
        padding: 20px;
        margin-top: 40px;
        color: #d1d5db;
        font-size: 0.9rem;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.05);
    }

    /* --- BUTTONS --- */
    .btn-next {
        background-color: #b01f24;
        color: white;
        border: none;
        padding: 12px 40px;
        border-radius: 8px;
        font-weight: 700;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease; 
        text-align: center;
    }
    .btn-next:hover { 
        background-color: #e32636; 
        color: white; 
        transform: translateY(-2px);
    }
    
    .btn-next:disabled {
        background-color: #555;
        cursor: not-allowed;
    }

    .btn-prev {
        background-color: #ffffff;
        color: #1a1c23;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 700;
        transition: all 0.3s ease;
        text-align: center;
        text-decoration: none;
    }
    .btn-prev:hover { 
        background-color: #f0f0f0; 
        transform: translateY(-2px);
    }

    .d-none { display: none !important; }

    #global-error {
        background-color: rgba(220, 53, 69, 0.2);
        color: #ff6b6b;
        border: 1px solid #dc3545;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
        text-align: center;
    }

    /* ===== RESPONSIVE MEDIA QUERIES ===== */
    @media (max-width: 767.98px) {
        .glass-card {
            padding: 25px 20px;
            min-height: auto;
        }
        .hero-title {
            font-size: clamp(2rem, 8vw, 3.5rem) !important;
        }
    }
    
    @media (max-width: 575.98px) {
        .btn-prev, .btn-next {
            width: 100%;
        }
        /* Make form label and helper text stack neatly */
        .d-flex.justify-content-between .helper-text-right {
            font-size: 0.65rem;
            align-self: flex-end;
        }
    }

    .signup-gate-overlay {
        position: fixed;
        inset: 0;
        z-index: 2000;
        background: rgba(17, 20, 30, 0.48);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .signup-gate-card {
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

    .signup-gate-close {
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
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .signup-gate-close:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.4);
    }

    .signup-gate-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 16px;
        border-radius: 999px;
        border: 2px solid rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 30px;
    }

    .signup-gate-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 16px;
        line-height: 1.2;
    }

    .signup-gate-input {
        width: 100%;
        background: #232737;
        border: 1px solid #3a3f50;
        color: #fff;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 1rem;
        margin-bottom: 14px;
    }

    .signup-gate-input:focus {
        outline: none;
        border-color: #4e598c;
        box-shadow: 0 0 0 0.2rem rgba(78, 89, 140, 0.25);
    }

    .signup-gate-desc {
        color: #d1d5db;
        font-size: 0.95rem;
        line-height: 1.45;
        margin-bottom: 16px;
    }

    .signup-gate-error {
        display: none;
        background: rgba(227, 38, 54, 0.18);
        border: 1px solid rgba(227, 38, 54, 0.75);
        color: #ffc3c7;
        border-radius: 8px;
        padding: 8px 10px;
        margin-bottom: 12px;
        font-size: 0.84rem;
    }

    .signup-gate-line {
        width: 80px;
        height: 3px;
        border-radius: 2px;
        margin: 18px auto 0;
        background: rgba(255, 255, 255, 0.22);
    }

    .signup-gate-confirm {
        min-width: 160px;
        margin: 10px auto 0;
        display: block;
    }

    .signup-gate-otp-wrap {
        display: none;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }

    .signup-gate-otp-wrap.active {
        display: flex;
    }

    .signup-gate-otp-input {
        flex: 0 0 132px;
        background: #232737;
        border: 1px solid #3a3f50;
        color: #fff;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 1.1rem;
        letter-spacing: 2px;
        text-align: center;
    }

    .signup-gate-otp-input:focus {
        outline: none;
        border-color: #4e598c;
        box-shadow: 0 0 0 0.2rem rgba(78, 89, 140, 0.25);
    }

    .signup-gate-otp-help {
        color: #9ca3af;
        font-size: 0.74rem;
        line-height: 1.35;
        text-align: left;
        margin: 0;
    }

    @media (max-width: 575.98px) {
        .signup-gate-card {
            padding: 24px 18px 20px;
        }

        .signup-gate-title {
            font-size: 1.65rem;
        }
    }
</style>

<div class="registration-container">
    <div class="container">
        <div class="row align-items-center">
            
            <div class="col-lg-6 pe-lg-5 mb-5 mb-lg-0 text-center text-lg-start">
                <p class="fw-bold mb-2" style="letter-spacing: 1px; font-family: 'Poppins', sans-serif;">Member Registration</p>
                <h1 class="hero-title mb-2" style="font-size: 3.5rem; font-weight: 800; line-height: 1.1;">Become a <span style="color: #e32636;">Member</span></h1>
                <p class="lead fw-bold mx-auto mx-lg-0" style="max-width: 1000px; color: #d6d6d6; padding-top: 0px;">
                    Join our vibrant community of business leaders and entrepreneurs. 
                    Complete your registration to unlock networking opportunities and business growth.
                </p>
            </div>

            <div class="col-lg-6">
                <div class="glass-card">
                    
                    <form id="registrationForm" onsubmit="return false;">
                        @csrf
                        
                        <input type="hidden" name="membership_type" value="Regular">
                        <input type="hidden" name="form_of_organization" value="Corporation">
                        <input type="hidden" name="registration_type" value="SEC">
                        <input type="hidden" name="registration_number" value="N/A">
                        <input type="hidden" name="date_of_registration" value="2024-01-01">
                        <input type="hidden" name="type_of_company" value="Single Proprietorship">
                        <input type="hidden" name="number_of_employees" value="1">
                        <input type="hidden" name="year_established" value="2024">
                        <input type="hidden" name="business_line" value="General">
                        <input type="hidden" name="referred_by" value="Website">
                        <input type="hidden" name="rep_title" value="Mr./Ms.">
                        <input type="hidden" name="alt_rep_title" value="Mr./Ms.">

                        <div id="form-header">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center">
                                    <i id="header-icon" class="bi bi-person fs-3 me-3"></i> 
                                    <h3 id="header-title" class="mb-0 fw-bold step-title fs-4 fs-sm-3">Basic Profile</h3>
                                </div>
                                <span id="step-counter" class="text-white small">Step 1 of 5</span>
                            </div>
                            
                            <p id="header-desc" class="text-white mb-3" style="color: #d1d5db !important;">Tell us about yourself and your business.</p>

                            <div class="step-progress">
                                <div class="step-progress-fill" id="progress-bar"></div>
                            </div>
                            
                            <div id="global-error"></div>
                        </div>

                        <div id="step-1">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label-custom">Business Name <span class="text-danger">*</span></label>
                                    <span class="helper-text-right">Indicated in your DTI/SEC/Mayor's</span>
                                </div>
                                <input type="text" name="registered_business_name" class="form-control form-control-dark" placeholder="Enter your business name" required>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label-custom">Business Trade Name <span class="text-danger">*</span></label>
                                    <span class="helper-text-right">Operating Name/DBA</span>
                                </div>
                                <input type="text" name="trade_name" class="form-control form-control-dark" placeholder="Enter your business trade name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-custom">Business Address <span class="text-danger">*</span></label>
                                <input type="text" name="business_address" class="form-control form-control-dark" placeholder="Enter your business address" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label-custom">City/Municipality <span class="text-danger">*</span></label>
                                    <input type="text" name="city_municipality" class="form-control form-control-dark" placeholder="Enter your municipality" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Province <span class="text-danger">*</span></label>
                                    <input type="text" name="province" class="form-control form-control-dark" placeholder="Enter your province" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Region <span class="text-danger">*</span></label>
                                    <input type="text" name="region" class="form-control form-control-dark" placeholder="Enter your region" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Zip Code <span class="text-danger">*</span></label>
                                    <input type="text" name="zip_code" class="form-control form-control-dark" placeholder="Enter your zip code" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Telephone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="telephone_no" class="form-control form-control-dark" placeholder="Ex. (02) 8352-5000" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Website/Social <span class="text-danger">*</span></label>
                                    <input type="text" name="website" class="form-control form-control-dark" placeholder="Put N/A if none" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="member_dob" class="form-control form-control-dark" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control form-control-dark" placeholder="Enter your email" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label-custom">TIN No. <span class="text-danger">*</span></label>
                                    <input type="text" name="tin_no" class="form-control form-control-dark" placeholder="Put N/A if none" required>
                                </div>
                            </div>
                            <div class="data-notice mb-4" style="background-color: rgba(63, 81, 181, 0.1); border: 1px solid #5c6bc0; border-radius: 8px; padding: 15px;">
                                <div class="d-flex">
                                    <i class="bi bi-info-circle me-2" style="color: #5c6bc0; margin-top: 2px;"></i>
                                    <div>
                                        <strong style="color: #7986cb; font-size: 0.95rem;">Data Usage Notice</strong><br>
                                        <span style="font-size: 0.8rem; color: #8c9eff; line-height: 1.4; display: block;">
                                            The information you provide will be reviewed by our administrators and, upon approval, will be listed in our public member directory.
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- RESPONSIVE BUTTON WRAPPER --}}
                            <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4">
                                <a href="{{ route('login') }}" class="btn btn-prev">Back to Login</a>
                                <button type="button" class="btn btn-next" onclick="validateAndNext(1, 2)">Next</button>
                            </div>
                        </div>

                        <div id="step-2" class="d-none">
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Surname <span class="text-danger">*</span></label>
                                    <input type="text" name="rep_surname" class="form-control form-control-dark" placeholder="Enter your surname" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label-custom">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="rep_first_name" class="form-control form-control-dark" placeholder="Enter your first name" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Middle Name <span class="text-danger">*</span></label>
                                    <input type="text" name="rep_mi" class="form-control form-control-dark" placeholder="Put N/A if none" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Designation <span class="text-danger">*</span></label>
                                    <input type="text" name="rep_designation" class="form-control form-control-dark" placeholder="Enter your designation" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="rep_dob" class="form-control form-control-dark" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Contact Number <span class="text-danger">*</span></label>
                                    <input type="text" name="rep_contact_no" class="form-control form-control-dark" placeholder="Enter your contact number" required>
                                </div>
                            </div>

                            {{-- RESPONSIVE BUTTON WRAPPER --}}
                            <div class="d-flex flex-column-reverse flex-sm-row justify-content-between gap-2 mt-4">
                                <button type="button" class="btn btn-prev" onclick="goToStep(1)">Previous</button>
                                <button type="button" class="btn btn-next" onclick="validateAndNext(2, 3)">Next</button>
                            </div>
                        </div>

                        <div id="step-3" class="d-none">
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Surname <span class="text-danger">*</span></label>
                                    <input type="text" name="alt_surname" class="form-control form-control-dark" placeholder="Enter your surname" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label-custom">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="alt_first_name" class="form-control form-control-dark" placeholder="Enter your first name" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Middle Name <span class="text-danger">*</span></label>
                                    <input type="text" name="alt_mi" class="form-control form-control-dark" placeholder="Put N/A if none" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Designation <span class="text-danger">*</span></label>
                                    <input type="text" name="alt_designation" class="form-control form-control-dark" placeholder="Enter your designation" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="alt_dob" class="form-control form-control-dark" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label-custom">Contact Number <span class="text-danger">*</span></label>
                                    <input type="text" name="alt_contact_no" class="form-control form-control-dark" placeholder="Enter your contact number" required>
                                </div>
                            </div>

                            {{-- RESPONSIVE BUTTON WRAPPER --}}
                            <div class="d-flex flex-column-reverse flex-sm-row justify-content-between gap-2 mt-4">
                                <button type="button" class="btn btn-prev" onclick="goToStep(2)">Previous</button>
                                <button type="button" class="btn btn-next" onclick="validateAndNext(3, 4)">Next</button>
                            </div>
                        </div>

                        <div id="step-4" class="d-none">
                            <div class="mb-4">
                                <label class="form-label-custom">Are you a member of other organization(s)?</label>
                                <select name="other_organizations" class="form-select form-select-dark" required>
                                    <option selected disabled value="">Choose</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>

                            {{-- RESPONSIVE BUTTON WRAPPER --}}
                            <div class="d-flex flex-column-reverse flex-sm-row justify-content-between gap-2 mt-5">
                                <button type="button" class="btn btn-prev" onclick="goToStep(3)">Previous</button>
                                <button type="button" class="btn btn-next" onclick="validateAndNext(4, 5)">Next</button>
                            </div>
                        </div>

                        <div id="step-5" class="d-none">
                            <div class="data-notice mb-4" style="background-color: rgba(63, 81, 181, 0.1); border: 1px solid #5c6bc0; border-radius: 8px; padding: 15px;">
                                <div class="d-flex">
                                    <i class="bi bi-info-circle me-2" style="color: #5c6bc0; margin-top: 2px;"></i>
                                    <div>
                                        <strong style="color: #7986cb; font-size: 0.95rem;">Document Upload Notice</strong><br>
                                        <span style="font-size: 0.8rem; color: #8c9eff; line-height: 1.4; display: block;">
                                            (Skipped for API connection - text data only)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label-custom">Mayor's Permit</label>
                                <input type="file" name="mayors_permit" class="form-control form-control-dark" accept="image/*,.pdf">
                            </div>
                            <div class="mb-4">
                                <label class="form-label-custom">DTI/SEC Business Registration Copy</label>
                                <input type="file" name="dti_sec_registration" class="form-control form-control-dark" accept="image/*,.pdf">
                            </div>

                            <div class="mb-5" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.14); border-radius: 10px; padding: 16px 18px;">
                                <h6 class="mb-1" style="font-size: 1.1rem; font-weight: 700; color: #f8fafc;">Annual Membership Fee</h6>
                                <p class="mb-1" style="font-size: 0.9rem; font-style: italic; font-weight: 600; color: #e5e7eb;">To be deposited to:</p>
                                <p class="mb-0" style="font-size: 0.92rem; color: #e5e7eb; line-height: 1.5;">
                                    ChinaBank<br>
                                    Account Name: PCCI Valenzuela<br>
                                    Account No.: 1054 0000 5989
                                </p>
                                <p class="mt-2 mb-3" style="font-size: 0.78rem; color: #cbd5e1;">
                                    Upload 1 supported file: PDF, document or image. Max 100 MB.
                                </p>
                                <label class="form-label-custom mb-2">Proof of Payment <span class="text-danger">*</span></label>
                                <input type="file" name="proof_of_payment" class="form-control form-control-dark" accept="image/*,.pdf" required>
                            </div>

                            {{-- RESPONSIVE BUTTON WRAPPER --}}
                            <div class="d-flex flex-column-reverse flex-sm-row justify-content-between gap-2 mt-4">
                                <button type="button" class="btn btn-prev" onclick="goToStep(4)">Previous</button>
                                <button type="button" id="finalSubmitBtn" class="btn btn-next" onclick="submitData()">Submit</button>
                            </div>
                        </div>

                        <div id="step-success" class="d-none text-center py-5">
                            <div class="success-icon-container">
                                <i class="bi bi-check-lg success-icon"></i>
                            </div>
                            <h2 class="success-title">Registration Submitted!</h2>
                            <p class="mb-4" style="color: #d1d5db;">Thank you for your application. We'll review your submission and get back to you soon.</p>

                            <div class="info-box">
                                <strong class="d-block mb-2 text-white">What's next?</strong>
                                Our administrators will review your application. You'll receive an email notification once your application has been processed.
                                <br><br>
                                <a href="{{ route('login') }}" style="color:#22c55e; text-decoration:underline;">Return to Login</a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="signupGateOverlay" class="signup-gate-overlay">
    <div class="signup-gate-card">
        <button type="button" class="signup-gate-close" aria-label="Close" onclick="goToLoginFromSignupGate()">&times;</button>
        <div class="signup-gate-icon">
            <i class="bi bi-envelope"></i>
        </div>
        <h2 class="signup-gate-title">Verify your email address</h2>
        <input id="signupGateEmail" type="email" class="signup-gate-input" placeholder="Enter your email address">
        <p class="signup-gate-desc mb-0">
            Ready to begin your PCCI journey? Just enter your email, and we'll send a one-time code to verify it's you.
        </p>
        <div id="signupGateOtpWrap" class="signup-gate-otp-wrap">
            <input id="signupGateOtp" type="text" class="signup-gate-otp-input" placeholder="------" maxlength="6" inputmode="numeric">
            <p class="signup-gate-otp-help">Enter the one-time code sent to your email to verify your email.</p>
        </div>
        <div id="signupGateError" class="signup-gate-error">Please enter a valid email address.</div>
        <button type="button" id="signupGateActionBtn" class="btn btn-next signup-gate-confirm" onclick="handleSignupGateAction()">Verify Email</button>
    </div>
</div>

<script>
    let signupGateStep = 'email';

    function goToLoginFromSignupGate() {
        window.location.href = "{{ route('login') }}";
    }

    function isValidGateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email || '').trim());
    }

    function openOtpStep(emailValue) {
        const emailEl = document.getElementById('signupGateEmail');
        const otpWrapEl = document.getElementById('signupGateOtpWrap');
        const otpEl = document.getElementById('signupGateOtp');
        const descEl = document.querySelector('.signup-gate-desc');
        const actionBtn = document.getElementById('signupGateActionBtn');
        const lineEl = document.getElementById('signupGateLine');

        signupGateStep = 'otp';
        if (emailEl) {
            emailEl.readOnly = true;
            emailEl.style.opacity = '0.75';
        }
        if (descEl) {
            descEl.textContent = `OTP sent to ${emailValue}. Enter the code below to continue.`;
        }
        if (otpWrapEl) otpWrapEl.classList.add('active');
        if (lineEl) lineEl.style.margin = '12px auto 14px';
        if (actionBtn) actionBtn.textContent = 'Confirm';
        otpEl?.focus();
    }

    function unlockSignupForm(emailValue) {
        const signupEmailEl = document.querySelector('input[name="email"]');
        if (signupEmailEl) signupEmailEl.value = emailValue;

        const overlay = document.getElementById('signupGateOverlay');
        if (overlay) overlay.style.display = 'none';
    }

    function handleSignupGateAction() {
        const gateEmailEl = document.getElementById('signupGateEmail');
        const gateOtpEl = document.getElementById('signupGateOtp');
        const errorEl = document.getElementById('signupGateError');
        const emailValue = (gateEmailEl?.value || '').trim();

        if (!isValidGateEmail(emailValue)) {
            if (errorEl) {
                errorEl.textContent = 'Please enter a valid email address.';
                errorEl.style.display = 'block';
            }
            gateEmailEl?.focus();
            return;
        }

        if (signupGateStep === 'email') {
            if (errorEl) errorEl.style.display = 'none';
            openOtpStep(emailValue);
            return;
        }

        const otpValue = (gateOtpEl?.value || '').replace(/\D/g, '');
        if (!/^\d{6}$/.test(otpValue)) {
            if (errorEl) {
                errorEl.textContent = 'Please enter a valid 6-digit OTP code.';
                errorEl.style.display = 'block';
            }
            gateOtpEl?.focus();
            return;
        }

        if (errorEl) errorEl.style.display = 'none';
        unlockSignupForm(emailValue);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const gateEmailEl = document.getElementById('signupGateEmail');
        const gateOtpEl = document.getElementById('signupGateOtp');
        const errorEl = document.getElementById('signupGateError');

        if (gateEmailEl) {
            gateEmailEl.focus();
            gateEmailEl.addEventListener('input', function () {
                if (errorEl) errorEl.style.display = 'none';
            });
            gateEmailEl.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    handleSignupGateAction();
                }
            });
        }

        if (gateOtpEl) {
            gateOtpEl.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
                if (errorEl) errorEl.style.display = 'none';
            });
            gateOtpEl.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    handleSignupGateAction();
                }
            });
        }
    });

    function validateAndNext(currentStepId, nextStepId) {
        const currentStepContainer = document.getElementById('step-' + currentStepId);
        const requiredFields = currentStepContainer.querySelectorAll('[required]');
        let allValid = true;
        
        for (let field of requiredFields) {
            if (!field.checkValidity()) {
                field.reportValidity();
                allValid = false;
                break; 
            }
        }

        if (allValid) {
            if (nextStepId === 'success') {
                // Do nothing, submitData handles this
            } else {
                goToStep(nextStepId);
            }
        }
    }

    function goToStep(step) {
        const steps = [1, 2, 3, 4, 5];
        const headerContainer = document.getElementById('form-header');
        const headerTitle = document.getElementById('header-title');
        const headerDesc = document.getElementById('header-desc');
        const headerIcon = document.getElementById('header-icon');
        const stepCounter = document.getElementById('step-counter');
        const progressBar = document.getElementById('progress-bar');
        const successStep = document.getElementById('step-success');

        headerContainer.classList.remove('d-none');
        successStep.classList.add('d-none');
        steps.forEach(s => document.getElementById('step-' + s).classList.add('d-none'));

        document.getElementById('step-' + step).classList.remove('d-none');

        // Logic to update headers/progress
        if (step === 1) {
            headerTitle.innerText = 'Basic Profile';
            headerDesc.innerText = 'Tell us about yourself and your business.';
            headerDesc.classList.remove('d-none');
            headerIcon.className = 'bi bi-person fs-3 me-3';
            progressBar.style.width = '20%';
            stepCounter.innerText = 'Step 1 of 5';
        } else if (step === 2) {
            headerTitle.innerText = 'Official Representative';
            headerDesc.innerText = 'President or Officer.';
            headerDesc.classList.remove('d-none');
            headerIcon.className = 'bi bi-person fs-3 me-3'; 
            progressBar.style.width = '40%';
            stepCounter.innerText = 'Step 2 of 5';
        } else if (step === 3) {
            headerTitle.innerText = 'Alternative Representative/s';
            headerDesc.innerText = 'Add other business representatives.';
            headerDesc.classList.remove('d-none');
            headerIcon.className = 'bi bi-person fs-3 me-3';
            progressBar.style.width = '60%';
            stepCounter.innerText = 'Step 3 of 5';
        } else if (step === 4) {
            headerTitle.innerText = 'Membership in Other Business Organization';
            headerDesc.classList.add('d-none'); 
            headerIcon.className = 'bi bi-person fs-3 me-3';
            progressBar.style.width = '80%';
            stepCounter.innerText = 'Step 4 of 5';
        } else if (step === 5) {
            headerTitle.innerText = 'Document Upload';
            headerDesc.innerText = 'Upload required business documents.';
            headerDesc.classList.remove('d-none');
            headerIcon.className = 'bi bi-file-earmark-text fs-3 me-3';
            progressBar.style.width = '100%';
            stepCounter.innerText = 'Step 5 of 5';
        }
    }

    async function submitData() {
        // Button State
        const submitBtn = document.getElementById('finalSubmitBtn');
        const errorDiv = document.getElementById('global-error');
        
        submitBtn.disabled = true;
        submitBtn.innerText = 'Submitting...';
        errorDiv.style.display = 'none';

        // Gather Data as FormData (supports file uploads)
        const form = document.getElementById('registrationForm');
        const formData = new FormData(form);

        // Remove Laravel CSRF token — not needed for external API
        formData.delete('_token');

        // Fix Type Conversions (API expects integers)
        formData.set('number_of_employees', parseInt(formData.get('number_of_employees') || 0));
        formData.set('year_established', parseInt(formData.get('year_established') || 2024));

        try {
            // Post to the API as multipart/form-data (for file uploads)
            const response = await fetch(`${window.API_BASE_URL}/v1/apply`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                showSuccessStep();
            } else {
                // Error Handling — show specific validation errors
                console.error('API validation errors:', result);
                let msg = result.message || 'Submission failed.';
                if (result.errors) {
                    const errorList = Object.entries(result.errors)
                        .map(([field, messages]) => `- ${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`)
                        .join('\n');
                    msg += '\n' + errorList;
                }
                errorDiv.style.whiteSpace = 'pre-line';
                errorDiv.innerText = msg;
                errorDiv.style.display = 'block';
                document.getElementById('form-header').scrollIntoView({ behavior: 'smooth' });
            }
        } catch (error) {
            console.error(error);
            errorDiv.innerText = 'Network Error. Please try again.';
            errorDiv.style.display = 'block';
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Submit';
        }
    }

    function showSuccessStep() {
        const steps = [1, 2, 3, 4, 5];
        const headerContainer = document.getElementById('form-header');
        
        steps.forEach(s => document.getElementById('step-' + s).classList.add('d-none'));
        headerContainer.classList.add('d-none');
        document.getElementById('step-success').classList.remove('d-none');
    }
</script>
@endsection