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
    .text-success { color: #4ade80 !important; }

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
        width: 16%;
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
        transform: none;
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

    .btn-reset-form {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #d1d5db;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-reset-form:hover {
        background: rgba(220, 53, 69, 0.1);
        color: #ff6b6b;
        border-color: rgba(220, 53, 69, 0.3);
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

    /* Animation for buttons */
    @keyframes spin { 100% { transform: rotate(360deg); } }
    .spin { display: inline-block; animation: spin 1s linear infinite; }

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
        .d-flex.justify-content-between .helper-text-right {
            font-size: 0.65rem;
            align-self: flex-end;
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
                                    <i id="header-icon" class="bi bi-envelope fs-3 me-3"></i> 
                                    <h3 id="header-title" class="mb-0 fw-bold step-title fs-4 fs-sm-3">Verify Account</h3>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <button type="button" class="btn-reset-form" onclick="showResetModal()" title="Reset the entire form">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </button>
                                    <span id="step-counter" class="text-white small">Step 1 of 6</span>
                                </div>
                            </div>
                            <p id="header-desc" class="text-white mb-3" style="color: #d1d5db !important;">Verify your email address to begin registration.</p>
                            <div class="step-progress">
                                <div class="step-progress-fill" id="progress-bar"></div>
                            </div>
                            <div id="global-error"></div>
                        </div>

                        <div id="step-1">
                            <div class="mb-3">
                                <label class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                                <input id="signupGateEmail" type="email" class="form-control form-control-dark" placeholder="Enter your email address" required>
                                <div id="emailStatusMessage" class="helper-text-small mt-2 text-success" style="display: none;"></div>
                            </div>
                            
                            <div class="mb-3 d-none" id="signupGateOtpWrap">
                                <label class="form-label-custom">OTP Code <span class="text-danger">*</span></label>
                                <input id="signupGateOtp" type="text" class="form-control form-control-dark" placeholder="Enter 6-digit OTP" maxlength="6" inputmode="numeric">
                                <div class="helper-text-small mt-2 text-success" id="otpMessage"></div>
                            </div>
                            
                            <div id="signupGateError" style="display:none; color: #ff6b6b; font-size: 0.9rem; margin-bottom: 15px;"></div>
                            
                            <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4">
                                <a href="{{ route('login') }}" class="btn btn-prev">Back to Login</a>
                                <button type="button" class="btn btn-next" id="signupGateActionBtn" onclick="handleSignupGateAction()">Verify Email</button>
                            </div>
                        </div>

                        <div id="step-2" class="d-none">
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
                                    <input type="email" name="email" class="form-control form-control-dark" placeholder="Verified Email" readonly required>
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label-custom">TIN No. <span class="text-danger">*</span></label>
                                    <input type="text" name="tin_no" class="form-control form-control-dark" placeholder="Put N/A if none" required>
                                </div>
                            </div>
                            
                            <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-next" onclick="validateAndNext(2, 3)">Next</button>
                            </div>
                        </div>

                        <div id="step-3" class="d-none">
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

                            <div class="d-flex flex-column-reverse flex-sm-row justify-content-between gap-2 mt-4">
                                <button type="button" class="btn btn-prev" onclick="goToStep(2)">Previous</button>
                                <button type="button" class="btn btn-next" onclick="validateAndNext(3, 4)">Next</button>
                            </div>
                        </div>

                        <div id="step-4" class="d-none">
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

                            <div class="d-flex flex-column-reverse flex-sm-row justify-content-between gap-2 mt-4">
                                <button type="button" class="btn btn-prev" onclick="goToStep(3)">Previous</button>
                                <button type="button" class="btn btn-next" onclick="validateAndNext(4, 5)">Next</button>
                            </div>
                        </div>

                        <div id="step-5" class="d-none">
                            <div class="mb-4">
                                <label class="form-label-custom">Are you a member of other organization(s)?</label>
                                <select name="other_organizations" class="form-select form-select-dark" required>
                                    <option selected disabled value="">Choose</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>

                            <div class="d-flex flex-column-reverse flex-sm-row justify-content-between gap-2 mt-5">
                                <button type="button" class="btn btn-prev" onclick="goToStep(4)">Previous</button>
                                <button type="button" class="btn btn-next" onclick="validateAndNext(5, 6)">Next</button>
                            </div>
                        </div>

                        <div id="step-6" class="d-none">
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
                                
                                <div id="dynamicBankInfoBox" class="mb-3">
                                    <p class="mb-0" style="font-size: 0.92rem; color: #a0aec0; line-height: 1.5;">
                                        <i class="bi bi-arrow-repeat spin"></i> Loading bank details...
                                    </p>
                                </div>

                                <hr style="border-color: rgba(255, 255, 255, 0.18); margin: 16px 0 14px;">

                                <p class="mb-3" style="font-size: 0.78rem; color: #cbd5e1;">
                                    Upload 1 supported file: PDF, document or image. Max 100 MB.
                                </p>
                                <label class="form-label-custom mb-2">Proof of Payment <span class="text-danger">*</span></label>
                                <input type="file" name="proof_of_payment" class="form-control form-control-dark" accept="image/*,.pdf" required>
                            </div>

                            <div class="d-flex flex-column-reverse flex-sm-row justify-content-between gap-2 mt-4">
                                <button type="button" class="btn btn-prev" onclick="goToStep(5)">Previous</button>
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

<div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #2b2d3c; color: white; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Reset</h5>
            </div>
            <div class="modal-body border-0 pt-3">
                <p style="color: #d1d5db; margin-bottom: 0;">Are you sure you want to completely reset the form? This will erase everything you have typed and send you back to Step 1. This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 pt-2">
                <button type="button" class="btn btn-secondary" style="background: rgba(255,255,255,0.1); border: none;" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" style="background: #e32636; border: none; font-weight: 600;" onclick="executeReset()">Yes, Reset Form</button>
            </div>
        </div>
    </div>
</div>

<script>
    // --- RESET FORM LOGIC ---
    function showResetModal() {
        const modal = new bootstrap.Modal(document.getElementById('resetModal'));
        modal.show();
    }

    function executeReset() {
        // 1. Reset the actual HTML form inputs
        document.getElementById('registrationForm').reset();
        
        // 2. Erase everything from browser LocalStorage
        clearAutoSave();
        
        // 3. Reset the Email Verification Gate UI
        signupGateStep = 'verify_email';
        
        const emailEl = document.getElementById('signupGateEmail');
        const otpEl = document.getElementById('signupGateOtp');
        const actionBtn = document.getElementById('signupGateActionBtn');
        const otpWrap = document.getElementById('signupGateOtpWrap');
        const emailStatusMsg = document.getElementById('emailStatusMessage');
        const errorEl = document.getElementById('signupGateError');
        
        if (emailEl) {
            emailEl.readOnly = false;
            emailEl.style.opacity = '1';
            emailEl.value = '';
        }
        if (otpEl) otpEl.value = '';
        if (actionBtn) {
            actionBtn.textContent = 'Verify Email';
            actionBtn.disabled = false;
        }
        if (otpWrap) otpWrap.classList.add('d-none');
        if (emailStatusMsg) emailStatusMsg.style.display = 'none';
        if (errorEl) errorEl.style.display = 'none';

        // 4. Send them back to Step 1
        goToStep(1);

        // 5. Hide the Modal
        const modalEl = document.getElementById('resetModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
    }


    // --- FETCH DYNAMIC BANK DETAILS ---
    async function loadPaymentChannels() {
        const box = document.getElementById('dynamicBankInfoBox');
        if (!box) return;

        try {
            const apiUrl = `${window.API_BASE_URL || 'https://pcciv-api.onrender.com/api'}/v1/payment-channels`;
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });
            
            if (response.ok) {
                const result = await response.json();
                const channels = result.data || result || [];
                
                if (channels.length > 0) {
                    const bank = channels[0];
                    const displayBankName = bank.bank_name || bank.name || 'Bank Name Not Set';
                    const displayAccountName = bank.account_name || bank.account_title || 'N/A';
                    const displayAccountNumber = bank.account_number || bank.account_no || bank.number || 'N/A';

                    box.innerHTML = `
                        <p class="mb-0" style="font-size: 0.92rem; color: #e5e7eb; line-height: 1.5;">
                            <strong style="color: #fff; font-size: 1rem;">${displayBankName}</strong><br>
                            Account Name: ${displayAccountName}<br>
                            Account No.: <strong style="color: #4ade80;">${displayAccountNumber}</strong>
                        </p>
                    `;
                } else {
                    box.innerHTML = `<p style="color: #fbbf24; font-size: 0.9rem;">No bank details available at this time.</p>`;
                }
            } else {
                box.innerHTML = `<p style="color: #f87171; font-size: 0.9rem;">Failed to load bank details.</p>`;
            }
        } catch (e) {
            console.error('Error fetching bank details:', e);
            box.innerHTML = `<p style="color: #f87171; font-size: 0.9rem;">Network error loading bank details.</p>`;
        }
    }

    // --- AUTO-SAVE LOGIC ---
    const AUTOSAVE_KEY = 'pcci_signup_autosave_data';

    function saveFormData() {
        const form = document.getElementById('registrationForm');
        const inputs = form.querySelectorAll('input:not([type="file"]):not([type="hidden"]), select');
        const dataObj = {};

        const gateEmail = document.getElementById('signupGateEmail');
        if (gateEmail) dataObj['signupGateEmail'] = gateEmail.value;

        inputs.forEach(input => {
            if (input.name) dataObj[input.name] = input.value;
        });

        localStorage.setItem(AUTOSAVE_KEY, JSON.stringify(dataObj));
    }

    function loadFormData() {
        const savedData = localStorage.getItem(AUTOSAVE_KEY);
        if (!savedData) return;

        try {
            const dataObj = JSON.parse(savedData);
            const form = document.getElementById('registrationForm');

            Object.keys(dataObj).forEach(key => {
                if (key === 'signupGateEmail') {
                    const el = document.getElementById('signupGateEmail');
                    if(el) el.value = dataObj[key];
                    return;
                }

                const input = form.querySelector(`[name="${key}"]`);
                if (input && input.type !== 'file') {
                    input.value = dataObj[key];
                }
            });
        } catch (e) {
            console.error('Failed to load auto-save data', e);
        }
    }

    function clearAutoSave() {
        localStorage.removeItem(AUTOSAVE_KEY);
        localStorage.removeItem('pcci_email_verified');
        localStorage.removeItem('pcci_current_step');
    }

    // --- LIVE API: GATE / OTP LOGIC ---
    let signupGateStep = 'verify_email'; 

    function isValidGateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email || '').trim());
    }

    async function handleSignupGateAction() {
        const emailEl = document.getElementById('signupGateEmail');
        const otpWrap = document.getElementById('signupGateOtpWrap');
        const otpEl = document.getElementById('signupGateOtp');
        const errorEl = document.getElementById('signupGateError');
        const actionBtn = document.getElementById('signupGateActionBtn');
        const emailStatusMsg = document.getElementById('emailStatusMessage');
        const otpMessage = document.getElementById('otpMessage');

        const emailValue = emailEl.value.trim();

        if (!isValidGateEmail(emailValue)) {
            errorEl.textContent = 'Please enter a valid email address.';
            errorEl.style.display = 'block';
            return;
        }

        errorEl.style.display = 'none';

        // 1. STATE: SEND OTP 
        if (signupGateStep === 'verify_email') {
            actionBtn.disabled = true;
            actionBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Sending OTP...';

            try {
                const apiUrl = `${window.API_BASE_URL || 'http://127.0.0.1:8000/api'}/email/send-otp`;

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: emailValue }) 
                });

                const data = await response.json();

                if (response.ok) {
                    emailEl.readOnly = true;
                    emailEl.style.opacity = '0.6';
                    emailStatusMsg.style.display = 'none';
                    
                    otpWrap.classList.remove('d-none');
                    otpMessage.innerHTML = `<i class="bi bi-envelope-check"></i> A 6-digit code has been sent to <strong>${emailValue}</strong>.`;
                    actionBtn.textContent = 'Verify OTP';
                    
                    signupGateStep = 'verify_otp';
                    otpEl.focus();
                } else {
                    let errorHtml = `<b>Validation Failed:</b> ${data.message || 'Check your email.'}`;
                    if (data.errors) {
                        errorHtml += '<ul style="margin-bottom:0; margin-top:5px; padding-left:20px;">';
                        for (const [field, messages] of Object.entries(data.errors)) {
                            errorHtml += `<li><b>${field}</b>: ${messages.join(', ')}</li>`;
                        }
                        errorHtml += '</ul>';
                    }
                    errorEl.innerHTML = errorHtml;
                    errorEl.style.display = 'block';
                }
            } catch (err) {
                console.error(err);
                errorEl.textContent = 'Network error. Please make sure the server is running.';
                errorEl.style.display = 'block';
            } finally {
                actionBtn.disabled = false;
                if(signupGateStep !== 'verify_otp') {
                    actionBtn.textContent = 'Verify Email';
                }
            }
            return;
        }

        // 2. STATE: VERIFY OTP
        if (signupGateStep === 'verify_otp') {
            const otpValue = otpEl.value.trim();
            
            if (!/^\d{6}$/.test(otpValue)) {
                errorEl.textContent = 'Please enter a valid 6-digit OTP code.';
                errorEl.style.display = 'block';
                return;
            }

            actionBtn.disabled = true;
            actionBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Verifying Code...';

            try {
                const apiUrl = `${window.API_BASE_URL || 'http://127.0.0.1:8000/api'}/email/verify-otp`;

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        email: emailValue,
                        otp: otpValue
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    const step2EmailEl = document.querySelector('input[name="email"]');
                    if (step2EmailEl) {
                        step2EmailEl.value = emailValue;
                        saveFormData();
                    }
                    
                    localStorage.setItem('pcci_email_verified', 'true');
                    goToStep(2);
                } else {
                    let errorHtml = `<b>Verification Failed:</b> ${data.message || 'Invalid OTP.'}`;
                    if (data.errors) {
                        errorHtml += '<ul style="margin-bottom:0; margin-top:5px; padding-left:20px;">';
                        for (const [field, messages] of Object.entries(data.errors)) {
                            errorHtml += `<li><b>${field}</b>: ${messages.join(', ')}</li>`;
                        }
                        errorHtml += '</ul>';
                    }
                    errorEl.innerHTML = errorHtml;
                    errorEl.style.display = 'block';
                }
            } catch (err) {
                console.error(err);
                errorEl.textContent = 'Network error. Please try again.';
                errorEl.style.display = 'block';
            } finally {
                actionBtn.disabled = false;
                actionBtn.textContent = 'Verify OTP';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        loadFormData();
        loadPaymentChannels();

        const isVerified = localStorage.getItem('pcci_email_verified');
        if (isVerified === 'true') {
            const savedStep = parseInt(localStorage.getItem('pcci_current_step')) || 2;
            if (savedStep > 1) {
                goToStep(savedStep);
            }
        }

        const formInputs = document.querySelectorAll('#registrationForm input:not([type="file"]):not([type="hidden"]), #registrationForm select');
        formInputs.forEach(input => {
            input.addEventListener('input', saveFormData);
            input.addEventListener('change', saveFormData);
        });

        const gateEmailEl = document.getElementById('signupGateEmail');
        const gateOtpEl = document.getElementById('signupGateOtp');
        const errorEl = document.getElementById('signupGateError');
        const emailStatusMsg = document.getElementById('emailStatusMessage');
        const actionBtn = document.getElementById('signupGateActionBtn');

        if (gateEmailEl) {
            gateEmailEl.addEventListener('input', function () { 
                errorEl.style.display = 'none'; 
                emailStatusMsg.style.display = 'none';
                
                if (signupGateStep !== 'verify_otp') {
                    signupGateStep = 'verify_email';
                    actionBtn.textContent = 'Verify Email';
                }
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
                errorEl.style.display = 'none';
            });
            gateOtpEl.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    handleSignupGateAction();
                }
            });
        }
    });

    // --- FORM NAVIGATION LOGIC ---
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
            goToStep(nextStepId);
            window.scrollTo({ top: 0, behavior: 'smooth' }); 
        }
    }

    function goToStep(step) {
        const steps = [1, 2, 3, 4, 5, 6];
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

        if (step === 1) {
            headerTitle.innerText = 'Verify Account';
            headerDesc.innerText = 'Verify your email address to begin registration.';
            headerDesc.classList.remove('d-none');
            headerIcon.className = 'bi bi-envelope fs-3 me-3';
            progressBar.style.width = '16%';
            stepCounter.innerText = 'Step 1 of 6';
        } else if (step === 2) {
            headerTitle.innerText = 'Basic Profile';
            headerDesc.innerText = 'Tell us about yourself and your business.';
            headerDesc.classList.remove('d-none');
            headerIcon.className = 'bi bi-person fs-3 me-3';
            progressBar.style.width = '33%';
            stepCounter.innerText = 'Step 2 of 6';
        } else if (step === 3) {
            headerTitle.innerText = 'Official Representative';
            headerDesc.innerText = 'President or Officer.';
            headerDesc.classList.remove('d-none');
            headerIcon.className = 'bi bi-person fs-3 me-3'; 
            progressBar.style.width = '50%';
            stepCounter.innerText = 'Step 3 of 6';
        } else if (step === 4) {
            headerTitle.innerText = 'Alternative Representative/s';
            headerDesc.innerText = 'Add other business representatives.';
            headerDesc.classList.remove('d-none');
            headerIcon.className = 'bi bi-person fs-3 me-3';
            progressBar.style.width = '66%';
            stepCounter.innerText = 'Step 4 of 6';
        } else if (step === 5) {
            headerTitle.innerText = 'Other Organizations';
            headerDesc.classList.add('d-none'); 
            headerIcon.className = 'bi bi-person fs-3 me-3';
            progressBar.style.width = '83%';
            stepCounter.innerText = 'Step 5 of 6';
        } else if (step === 6) {
            headerTitle.innerText = 'Document Upload';
            headerDesc.innerText = 'Upload required business documents.';
            headerDesc.classList.remove('d-none');
            headerIcon.className = 'bi bi-file-earmark-text fs-3 me-3';
            progressBar.style.width = '100%';
            stepCounter.innerText = 'Step 6 of 6';
        }

        localStorage.setItem('pcci_current_step', step);
    }

    async function submitData() {
        const submitBtn = document.getElementById('finalSubmitBtn');
        const errorDiv = document.getElementById('global-error');
        
        submitBtn.disabled = true;
        submitBtn.innerText = 'Submitting...';
        errorDiv.style.display = 'none';

        const form = document.getElementById('registrationForm');
        const formData = new FormData(form);

        formData.delete('_token');

        formData.set('number_of_employees', parseInt(formData.get('number_of_employees') || 0));
        formData.set('year_established', parseInt(formData.get('year_established') || 2024));

        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/apply`, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                // IMPORTANT: Wipe everything when they succeed!
                clearAutoSave(); 
                showSuccessStep();
            } else {
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
        const steps = [1, 2, 3, 4, 5, 6];
        const headerContainer = document.getElementById('form-header');
        
        steps.forEach(s => document.getElementById('step-' + s).classList.add('d-none'));
        headerContainer.classList.add('d-none');
        document.getElementById('step-success').classList.remove('d-none');
    }
</script>
@endsection