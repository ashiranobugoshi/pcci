@extends('layouts.app')
@include('partials.api-config')
@section('title', 'Secure Account - PCCI')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;500;700&family=Poppins:wght@600;700;800&display=swap');

    body {
        background-color: #1a1c23; 
        color: #ffffff;
        font-family: 'DM Sans', sans-serif; 
    }

    .setup-container {
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
    }

    .form-control-dark {
        background-color: #1f222e !important;
        border: 1px solid #3a3f50 !important;
        color: white !important;
        border-radius: 8px;
        padding: 12px;
        transition: all 0.2s;
    }

    .form-control-dark:focus {
        border-color: #be1e38 !important;
        box-shadow: 0 0 0 0.25rem rgba(190, 30, 56, 0.25);
        outline: none;
    }

    .btn-success-custom {
        background-color: #22c55e;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 700;
        width: 100%;
        transition: all 0.3s;
    }
    .btn-success-custom:hover { background-color: #16a34a; transform: translateY(-2px); }

    @keyframes spin { 100% { transform: rotate(360deg); } }
    .spin { display: inline-block; animation: spin 1s linear infinite; }
</style>

<div class="setup-container">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            
            <div class="col-lg-5">
                <div class="glass-card">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/PCCI-Logo.png') }}" alt="PCCI Logo" style="width: 70px; height: 70px; background: white; border-radius: 50%; padding: 5px; margin-bottom: 15px;">
                        <h3 class="fw-bold mb-1" style="color: #4ade80; font-family: 'Poppins', sans-serif;">
                            <i class="bi bi-shield-lock-fill me-2"></i>Secure Your Account
                        </h3>
                        <p class="text-muted" style="font-size: 0.9rem;">Since this is your newly made account, you must set a personal, secure password before accessing the dashboard.</p>
                    </div>

                    <form id="setupPasswordForm" onsubmit="submitFirstTimePassword(event)">
                        <div class="mb-3">
                            <label class="form-label" style="color: #a0aec0; font-weight: 600; font-size: 0.85rem;">New Password <span class="text-danger">*</span></label>
                            <input type="password" id="new_password" class="form-control form-control-dark" placeholder="Enter new password" required minlength="8">
                        </div>
                        <div class="mb-4">
                            <label class="form-label" style="color: #a0aec0; font-weight: 600; font-size: 0.85rem;">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" id="new_password_confirmation" class="form-control form-control-dark" placeholder="Re-type password" required minlength="8">
                        </div>

                        <div id="setupError" class="alert alert-danger d-none mt-3" style="font-size: 0.85rem; padding: 10px; background-color: rgba(220, 53, 69, 0.15); color: #ff6b6b; border: 1px solid #dc3545;"></div>

                        <button type="submit" id="btnSetup" class="btn-success-custom mt-2">
                            Update Password & Continue
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Kicks user back to login if they try to visit this page without a token
        if (!localStorage.getItem('token')) {
            window.location.href = '/login';
        }
    });

    async function submitFirstTimePassword(event) {
        event.preventDefault();
        
        const token = localStorage.getItem('token');
        const pass = document.getElementById('new_password').value;
        const passConfirm = document.getElementById('new_password_confirmation').value;
        const errorBox = document.getElementById('setupError');
        const btn = document.getElementById('btnSetup');

        if (pass !== passConfirm) {
            errorBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Passwords do not match.';
            errorBox.classList.remove('d-none');
            return;
        }

        errorBox.classList.add('d-none');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Securing Account...';

        try {
            // FIXED: Added backticks around the API URL template
            const apiUrl = `${window.API_BASE_URL}/v1/user/first-time-password-change`;
            
            const response = await fetch(apiUrl, {
                method: 'POST', 
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    // FIXED: Added backticks around the Bearer token template
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    new_password: pass,
                    new_password_confirmation: passConfirm
                })
            });

            const data = await response.json();

            if (response.ok) {
                window.location.href = '/dashboard'; 
            } else {
                // FIXED: Added backticks for the HTML string template
                let errorHtml = `<b>Update Failed:</b> ${data.message || 'Invalid data.'}`;
                if (data.errors) {
                    errorHtml += '<ul style="margin-bottom:0; padding-left:20px; margin-top:5px;">';
                    for (const [field, messages] of Object.entries(data.errors)) {
                        // FIXED: Added backticks here as well
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