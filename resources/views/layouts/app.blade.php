<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'PCCI Valenzuela')</title>
        @include('partials.api-config')
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <style>
            /* ============================================== */
            /* GLOBAL STYLES & THEME VARIABLES */
            /* ============================================== */
            
            :root {
                /* --- LIGHT MODE (Default) --- */
                --pcci-red: #A40033;
                --pcci-red-dark: #8a002b;
                
                /* Backgrounds */
                --bg-body: #ffffff;
                --bg-card: #ffffff;
                --bg-section-gray: #e9ecef;
                --bg-hero: #252631; /* Original Dark Hero */
                --bg-input: #ffffff;
                
                /* Text */
                --text-main: #000000;
                --text-secondary: #000000;
                --text-hero-headline: #ffffff; 
                --text-hero-sub: #ffffff;
                
                /* Borders */
                --border-color: #dee2e6;
                --border-input: #ced4da;
                
                /* Font Families */
                --font-heading: 'DM Sans', sans-serif;
                --font-body: 'Poppins', sans-serif;
            }

            /* --- DARK MODE OVERRIDES --- */
            body.dark-mode {
                /* Backgrounds */
                --bg-body: #121212;
                --bg-card: #1e1e24;
                --bg-section-gray: #1a1a20; 
                --bg-hero: #0d0d10; /* Even darker hero */
                --bg-input: #2a2a35;
                
                /* Text */
                --text-main: #ffffff;
                --text-secondary: #ffffff;
                --text-hero-headline: #ffffff;
                --text-hero-sub: #ffffff;
                
                /* Borders */
                --border-color: #2d2d3a;
                --border-input: #444450;
            }
            
            * {
                box-sizing: border-box;
            }
            
            html {
                scroll-behavior: smooth;
            }
            
            body {
                font-family: var(--font-body);
                margin: 0;
                padding: 0;
                overflow-x: hidden;
                background-color: var(--bg-body); /* APPLIED VARIABLE */
                color: var(--text-main);          /* APPLIED VARIABLE */
                transition: background-color 0.3s ease, color 0.3s ease;
            }
            
            h1, h2, h3, h4, h5, h6 {
                font-family: var(--font-heading);
                color: var(--text-main);
            }
            
            img {
                max-width: 100%;
                height: auto;
            }

            /* FORM ELEMENTS IN DARK MODE */
            body.dark-mode .form-control, 
            body.dark-mode .form-select {
                background-color: var(--bg-input);
                border-color: var(--border-input);
                color: var(--text-main);
            }
            
            body.dark-mode .form-control::placeholder {
                color: var(--text-secondary);
            }

            body.dark-mode .input-group-text {
                background-color: var(--bg-input) !important;
                border-color: var(--border-input);
                color: var(--text-secondary);
            }
            
            /* ============================================== */
            /* NAVBAR STYLES */
            /* ============================================== */
            
            .navbar {
                background-color: transparent;
                transition: all 0.3s ease;
                padding: 1rem 0;
            }
            
            .navbar.scrolled {
                background-color: rgba(255, 255, 255, 0.45);
                box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
                padding: 0.5rem 0;
            }
            
            .navbar-brand {
                font-family: var(--font-heading);
                font-weight: 700;
                font-size: 1.5rem;
                color: #fff !important;
            }
            
            .navbar.scrolled .navbar-brand {
                color: #1a1a2e !important;
            }
            
            .navbar-nav .nav-link {
                font-family: var(--font-heading);
                font-weight: 500;
                color: rgba(255, 255, 255, 0.9) !important;
                padding: 0.5rem 1rem !important;
                transition: color 0.3s ease;
            }
            
            .navbar.scrolled .navbar-nav .nav-link {
                color: #1a1a2e !important;
            }
            
            .navbar-nav .nav-link:hover {
                color: var(--pcci-red) !important;
            }
            
            .navbar-toggler {
                border: none;
                padding: 0.5rem;
            }
            
            .navbar-toggler:focus {
                box-shadow: none;
            }
            
            .navbar-toggler-icon {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            }
            
            .navbar.scrolled .navbar-toggler-icon {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2826, 26, 46, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            }
            
            /* --- RESPONSIVE MOBILE MENU FIX --- */
            /* Mobile Navigation (Matches navbar-expand-xl) */
            @media (max-width: 1199.98px) {
                .navbar-collapse {
                    background-color: var(--bg-card); /* Adapts to light/dark mode */
                    padding: 1.5rem;
                    border-radius: 12px;
                    margin-top: 1rem;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
                    border: 1px solid var(--border-color);
                }
                
                .navbar-collapse .navbar-nav .nav-link,
                .navbar-collapse .nav-link-custom {
                    color: var(--text-main) !important; /* Forces text to be visible on mobile */
                    padding: 0.75rem 1rem !important;
                    margin-bottom: 0.5rem;
                    display: block;
                }
                
                .navbar-collapse .nav-link-custom:hover,
                .navbar-collapse .active-nav-underline {
                    background-color: var(--pcci-red);
                    color: #ffffff !important;
                    border-radius: 6px;
                    text-decoration: none !important;
                }
                
                /* Ensures the action buttons sit nicely on mobile */
                .navbar-collapse .btn {
                    width: 100%;
                    text-align: center;
                    margin-top: 0.5rem;
                }
            }
            
            /* ============================================== */
            /* CONTENT SPACER */
            /* ============================================== */
            
            .content-spacer {
                min-height: 100%;
            }
            
            /* ============================================== */
            /* FOOTER STYLES (STATIC - UNAFFECTED BY THEME)   */
            /* ============================================== */

            .footer {
                background-color: #1a1a2e !important; 
                color: #ffffff !important;
                padding: 4rem 0 2rem;
            }

            /* This forces all headings and text inside the footer to stay white */
            .footer h1, .footer h2, .footer h3, .footer h4, .footer h5, .footer h6,
            .footer p, .footer span, .footer div, .footer small {
                color: #ffffff !important;
            }

            .footer a {
                color: rgba(255, 255, 255, 0.7) !important;
                text-decoration: none;
                transition: color 0.3s ease;
            }

            .footer a:hover {
                color: var(--pcci-red) !important;
            }

            .footer-bottom {
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                padding-top: 2rem;
                margin-top: 3rem;
            }
            
            /* ============================================== */
            /* RESPONSIVE UTILITIES */
            /* ============================================== */
            
            /* Container responsive adjustments */
            @media (max-width: 1400px) {
                .container {
                    max-width: 1140px;
                }
            }
            
            @media (max-width: 1200px) {
                .container {
                    max-width: 960px;
                }
            }
            
            @media (max-width: 992px) {
                .container {
                    max-width: 720px;
                }
                
                .py-5 {
                    padding-top: 3rem !important;
                    padding-bottom: 3rem !important;
                }
            }
            
            @media (max-width: 768px) {
                .container {
                    max-width: 540px;
                    padding-left: 15px;
                    padding-right: 15px;
                }
                
                .py-5 {
                    padding-top: 2.5rem !important;
                    padding-bottom: 2.5rem !important;
                }
                
                h1 {
                    font-size: 2rem;
                }
                
                h2 {
                    font-size: 1.75rem;
                }
                
                .display-4 {
                    font-size: 2.25rem;
                }
            }
            
            @media (max-width: 576px) {
                .container {
                    padding-left: 12px;
                    padding-right: 12px;
                }
                
                .py-5 {
                    padding-top: 2rem !important;
                    padding-bottom: 2rem !important;
                }
                
                h1 {
                    font-size: 1.75rem;
                }
                
                h2 {
                    font-size: 1.5rem;
                }
                
                .display-4 {
                    font-size: 1.75rem;
                }
                
                .lead {
                    font-size: 1rem;
                }
                
                .btn {
                    padding: 0.75rem 1.25rem;
                    font-size: 0.9rem;
                }
            }
            
            /* ============================================== */
            /* BUTTON STYLES */
            /* ============================================== */
            
            .btn-pcci-primary {
                background-color: var(--pcci-red);
                color: #fff;
                border: none;
                padding: 0.875rem 1.75rem;
                border-radius: 8px;
                font-family: var(--font-heading);
                font-weight: 600;
                transition: all 0.3s ease;
            }
            
            .btn-pcci-primary:hover {
                background-color: var(--pcci-red-dark);
                color: #fff;
                transform: translateY(-2px);
            }
            
            .btn-pcci-outline {
                background-color: transparent;
                color: var(--pcci-red);
                border: 2px solid var(--pcci-red);
                padding: 0.875rem 1.75rem;
                border-radius: 8px;
                font-family: var(--font-heading);
                font-weight: 600;
                transition: all 0.3s ease;
            }
            
            .btn-pcci-outline:hover {
                background-color: var(--pcci-red);
                color: #fff;
                transform: translateY(-2px);
            }
            
            /* ============================================== */
            /* CARD STYLES */
            /* ============================================== */
            
            .card {
                border: none;
                border-radius: 12px;
                transition: all 0.3s ease;
                /* VARIABLES APPLIED FOR DARK MODE */
                background-color: var(--bg-card);
                color: var(--text-main);
            }
            
            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            }
            
            /* ============================================== */
            /* SECTION LABEL STYLES */
            /* ============================================== */
            
            .section-label {
                color: var(--pcci-red);
                font-family: var(--font-heading);
                font-size: 0.875rem;
                font-weight: 600;
                letter-spacing: 2px;
                text-transform: uppercase;
                margin-bottom: 0.5rem;
            }
            
            .section-title {
                font-family: var(--font-heading);
                font-size: 2.5rem;
                font-weight: 700;
                color: var(--text-main); /* VARIABLE APPLIED */
                margin-bottom: 1rem;
            }
            
            .section-title span {
                color: var(--pcci-red);
            }
            
            .section-description {
                font-family: var(--font-body);
                color: var(--text-secondary); /* VARIABLE APPLIED */
                font-size: 1rem;
                margin-bottom: 2.5rem;
            }
            
            /* Responsive section typography */
            @media (max-width: 768px) {
                .section-label {
                    font-size: 0.75rem;
                }
                
                .section-title {
                    font-size: 1.75rem;
                }
                
                .section-description {
                    font-size: 0.95rem;
                }
            }
            
            @media (max-width: 576px) {
                .section-title {
                    font-size: 1.5rem;
                }
                
                .section-description {
                    font-size: 0.9rem;
                    margin-bottom: 1.5rem;
                }
            }
            
            /* ============================================== */
            /* IMPACT SECTION STYLES */
            /* ============================================== */
            
            .impact-number {
                font-family: var(--font-heading);
                font-size: 3rem;
                font-weight: 700;
                color: #fff;
                margin-bottom: 0;
                line-height: 1;
            }

            .impact-label {
                font-family: var(--font-heading);
                font-size: 1.1rem;
                font-weight: 600;
                color: #D4E157;
                margin-bottom: 0.75rem;
            }

            .impact-desc {
                font-family: var(--font-body);
                color: #4a3a3f;
                font-size: 0.95rem;
                margin-bottom: 0;
            }
            
            /* ============================================== */
            /* SWIPER GLOBAL OVERRIDES */
            /* ============================================== */
            
            .swiper {
                width: 100%;
                overflow: hidden;
            }
            
            .swiper-pagination-bullet {
                cursor: pointer;
            }
            
            /* ============================================== */
            /* UTILITY CLASSES */
            /* ============================================== */
            
            .text-pcci-red {
                color: var(--pcci-red) !important;
            }
            
            .bg-pcci-red {
                background-color: var(--pcci-red) !important;
            }
            
            .bg-pcci-light {
                background-color: var(--pcci-light) !important;
            }
            
            .bg-pcci-dark {
                background-color: #1a1a2e !important;
            }
            
            /* Smooth transitions for interactive elements */
            a, button, .btn {
                transition: all 0.3s ease;
            }
            
            /* Focus states for accessibility */
            a:focus, button:focus, .btn:focus {
                outline: 2px solid var(--pcci-red);
                outline-offset: 2px;
            }
            
            /* ============================================== */
            /* SCROLL TO TOP BUTTON */
            /* ============================================== */
            
            .scroll-to-top {
                position: fixed;
                bottom: 30px;
                right: 30px;
                width: 50px;
                height: 50px;
                background: var(--pcci-red);
                color: #fff;
                border: none;
                border-radius: 50%;
                cursor: pointer;
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 1000;
                box-shadow: 0 5px 20px rgba(164, 0, 51, 0.3);
                transition: all 0.3s ease;
            }
            
            .scroll-to-top:hover {
                background: var(--pcci-red-dark);
                transform: translateY(-3px);
            }
            
            .scroll-to-top.visible {
                display: flex;
            }

            @media (max-width: 576px) {
                .scroll-to-top {
                    width: 40px;
                    height: 40px;
                    bottom: 20px;
                    right: 20px;
                }
            }

            /* ============================================== */
            /* LOADING SCREEN */
            /* ============================================== */

            .pcci-loader-overlay {
                position: fixed;
                inset: 0;
                z-index: 99999;
                background: #ffffff;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                transition: opacity 0.5s ease, visibility 0.5s ease;
            }

            body.dark-mode .pcci-loader-overlay {
                background: #121212;
            }

            .pcci-loader-overlay.hidden {
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
            }

            .pcci-loader-logo {
                width: 90px;
                height: 90px;
                animation: loaderPulse 1.4s ease-in-out infinite;
            }

            @keyframes loaderPulse {
                0%, 100% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.08); opacity: 0.7; }
            }

            .pcci-loader-bar-track {
                width: 180px;
                height: 4px;
                background: #e9ecef;
                border-radius: 4px;
                margin-top: 28px;
                overflow: hidden;
            }

            body.dark-mode .pcci-loader-bar-track {
                background: #2d2d3a;
            }

            .pcci-loader-bar {
                width: 40%;
                height: 100%;
                background: var(--pcci-red, #A40033);
                border-radius: 4px;
                animation: loaderSlide 1.2s ease-in-out infinite;
            }

            @keyframes loaderSlide {
                0%   { transform: translateX(-100%); }
                100% { transform: translateX(350%); }
            }

            .pcci-loader-text {
                margin-top: 16px;
                font-family: var(--font-heading, 'DM Sans', sans-serif);
                font-size: 0.8rem;
                font-weight: 600;
                letter-spacing: 3px;
                text-transform: uppercase;
                color: #6c757d;
            }

            body.dark-mode .pcci-loader-text {
                color: #9ca3af;
            }

        </style>
    </head>
    <body>

        <div class="pcci-loader-overlay" id="pcciLoader">
            <img src="{{ asset('images/PCCI-Logo.svg') }}" alt="PCCI" class="pcci-loader-logo">
            <div class="pcci-loader-bar-track">
                <div class="pcci-loader-bar"></div>
            </div>
            <span class="pcci-loader-text">Loading</span>
        </div>

        @include('partials.topbar')

        <div class="content-spacer w-100">
            @yield('content')
        </div>

        @include('partials.footer')
        
        <button class="scroll-to-top" id="scrollToTop" aria-label="Scroll to top">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
        </button>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        
        <script>
            // --- GLOBAL THEME SWITCH LOGIC ---
            document.addEventListener("DOMContentLoaded", function () {
                const themeCheckbox = document.getElementById('theme-checkbox');
                const body = document.body;

                // Load saved preference
                const savedTheme = localStorage.getItem('theme');
                const isDarkMode = savedTheme === 'dark';
                
                // Apply theme if dark mode is saved and checkbox exists
                if (isDarkMode) {
                    body.classList.add('dark-mode');
                    if (themeCheckbox) {
                        themeCheckbox.checked = true;
                    }
                }

                if (themeCheckbox) {
                    themeCheckbox.addEventListener('change', function() {
                        if (this.checked) {
                            body.classList.add('dark-mode');
                            localStorage.setItem('theme', 'dark');
                        } else {
                            body.classList.remove('dark-mode');
                            localStorage.setItem('theme', 'light');
                        }
                    });
                }
            });

            // Navbar scroll behavior
            window.addEventListener('scroll', function() {
                const navbar = document.getElementById('mainNavbar');
                if (navbar) {
                    if (window.scrollY > 50) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                }
            });
            
            // Scroll to top button
            const scrollToTopBtn = document.getElementById('scrollToTop');
            
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    scrollToTopBtn.classList.add('visible');
                } else {
                    scrollToTopBtn.classList.remove('visible');
                }
            });
            
            scrollToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Loading screen
            window.addEventListener('load', function() {
                const loader = document.getElementById('pcciLoader');
                if (loader) {
                    loader.classList.add('hidden');
                    setTimeout(() => loader.remove(), 500);
                }
            });
        </script>

    </body>
</html>