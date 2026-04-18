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
                    <div class="input-wrapper">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                    </div>
                </div>

                <div class="form-options">
                    <label>
                        <input type="checkbox" name="remember"> Remember for 30 Days
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Forgot password</a>
                    @else
                        <a href="#">Forgot password</a>
                    @endif
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

<script>
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
</script>
@endsection