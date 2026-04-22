<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    /* ============================================== */
    /* JOIN PCCI BUTTON ANIMATION (Solid Fill Slide)  */
    /* ============================================== */
    
    .btn-pcci-slide {
        position: relative;
        background-color: #A40D0F !important; 
        color: #ffffff;
        border: 2px solid #A40D0F;
        overflow: hidden; 
        z-index: 1;
        font-weight: 800 !important;

        /* Sliding White Fill */
        background-image: linear-gradient(to right, #ffffff, #ffffff);
        background-position: left center;
        background-size: 0% 100%; 
        background-repeat: no-repeat;
        
        transition: background-size 0.5s ease, color 0.5s ease, border-color 0.5s ease;
    }

    /* HOVER STATE */
    .btn-pcci-slide:hover {
        background-size: 100% 100%; 
        color: #A40D0F !important;   
        border-color: #ffffff;       
    }

    /* SCROLLED STATE */
    .btn-pcci-slide.scrolled-mode {
        background-color: #ffffff !important;
        color: #A40D0F !important;
        border-color: #ffffff !important;
        background-image: none; 
    }

    .btn-pcci-slide.scrolled-mode:hover {
        background-color: #f2f2f2 !important; 
    }

    .btn-pcci-slide:focus, .btn-pcci-slide:active {
        box-shadow: none !important;
        outline: none !important;
    }

   /* ============================================== */
    /* NAVIGATION LINKS STYLING                       */
    /* ============================================== */
    
    .nav-link-custom {
        font-weight: 600 !important;
        border-radius: 5px; 
        transition: opacity 0.2s ease;
    }
    
    /* Hover effect */
    .nav-link-custom:hover {
        opacity: 0.8; 
    }

    /* REMOVE ALL OUTLINES/BORDERS ON CLICK */
    .nav-link-custom:focus {
        outline: none !important;
        box-shadow: none !important;
        border: none !important;
    }

    /* ACTIVE STATE (WHILE CLICKING) */
    .nav-link-custom:active {
        background-color: transparent !important; 
        color: #ffffff !important; 
        transition: none !important; 
        opacity: 0.7 !important; 
    }

    /* CURRENT PAGE UNDERLINE STATE */
    .active-nav-underline {
        text-decoration: underline !important;
        text-decoration-color: #ffffff !important;
        text-decoration-thickness: 3px !important; 
        text-underline-offset: 8px !important;    
        opacity: 1 !important;
    }

    /* ============================================== */
    /* THEME SWITCH TOGGLE (DASH STYLE)               */
    /* ============================================== */
    .theme-switch-wrapper {
        display: flex;
        align-items: center;
        margin-right: 10px;
    }

    .theme-switch {
        display: inline-block;
        height: 30px;
        position: relative;
        width: 60px;
        margin-bottom: 0;
    }

    .theme-switch input {
        display: none;
    }

    .slider {
        background-color: rgba(255, 255, 255, 0.2);
        border: 2px solid #fff;
        bottom: 0;
        cursor: pointer;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        transition: .4s;
    }

    .slider:before {
        background-color: #fff;
        bottom: 3px;
        content: "";
        height: 20px;
        left: 4px;
        position: absolute;
        transition: .4s;
        width: 20px;
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    /* Checked State (Dark Mode Position) */
    input:checked + .slider {
        background-color: #1a1a2e; /* Dark PCCI Color */
        border-color: #fff;
    }

    input:checked + .slider:before {
        transform: translateX(28px); /* Moves the circle to the right */
        background-color: #fff;
    }
    
    /* Icons inside the toggle for visual cue (Optional) */
    .slider-icon {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        color: #fff;
        pointer-events: none;
    }
    
    .icon-sun { right: 8px; opacity: 1; transition: opacity 0.3s; }
    .icon-moon { left: 8px; opacity: 0; transition: opacity 0.3s; }
    
    input:checked + .slider .icon-sun { opacity: 0; }
    input:checked + .slider .icon-moon { opacity: 1; }

</style>

<header id="main-topbar" 
        class="fixed-top w-100 px-4 py-3" 
        style="background-color: transparent; transition: background-color 0.3s ease;">
    
    <nav class="navbar navbar-expand-xl w-100 p-0">
        
        <a href="{{ url('/') }}" class="d-flex align-items-center gap-3 text-decoration-none text-reset " style="outline: none; box-shadow: none;">
            <div class="rounded-circle overflow-hidden" style="width: 50px; height: 50px; ">
                <img src="{{ asset('images/PCCI-Logo.svg') }}" alt="PCCI Logo" class="w-100 h-100 object-fit-contain">
            </div>

            <div class="d-flex flex-column">
                <span class="fw-bold text-white" style="font-family: 'Poppins', sans-serif;">
                    PCCI - Valenzuela
                </span>
                <span class="d-none d-sm-block small text-white-50" style="font-family: 'DM Sans', sans-serif;">
                    Philippine Chambers of Commerce and Industry
                </span>
            </div>
            <span class="fw-bold d-xl-none text-white">PCCI Valenzuela</span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            
            <div class="d-flex flex-column flex-xl-row align-items-xl-center mx-auto gap-3 mt-3 mt-xl-0" style="font-family: 'DM Sans', sans-serif;">
                
                <a href="{{ url('/') }}" class="text-decoration-none px-2 nav-link-custom text-white {{ Request::is('/') ? 'active-nav-underline' : '' }}">
                    Home
                </a>
                
                <a href="{{ route('about') }}" class="text-decoration-none px-2 nav-link-custom text-white {{ Request::is('about') ? 'active-nav-underline' : '' }}">
                    About Us
                </a>
                
                <a href="{{ url('/membership') }}" class="text-decoration-none px-2 nav-link-custom text-white {{ (Request::is('membership') || Request::is('business/*')) ? 'active-nav-underline' : '' }}">
                    Membership
                </a>
                
                <a href="{{ route('event') }}" class="text-decoration-none px-2 nav-link-custom text-white {{ Request::is('event') ? 'active-nav-underline' : '' }}">
                    Events
                </a>
                
                <a href="{{ url('/contact') }}" class="text-decoration-none px-2 nav-link-custom text-white {{ Request::is('contact') ? 'active-nav-underline' : '' }}">
                    Contact Us
                </a>
            </div>

            <div class="d-flex flex-column flex-xl-row align-items-stretch align-items-xl-center gap-2 mt-4 mt-xl-0 border-top border-xl-0 pt-3 pt-xl-0" style="font-family: 'DM Sans', sans-serif; border-color: rgba(255,255,255,0.1) !important;">
                
               <div class="theme-switch-wrapper mb-3 mb-xl-0 justify-content-center justify-content-xl-start">
                   <label class="theme-switch" for="theme-checkbox">
                       <input type="checkbox" id="theme-checkbox" />
                       <div class="slider round">
                           <i class="bi bi-sun-fill slider-icon icon-sun"></i>
                           <i class="bi bi-moon-fill slider-icon icon-moon"></i>
                       </div>
                   </label>
               </div>

                <a href="{{ url('/login') }}" 
                   id="join-pcci-btn"
                   class="btn btn-pcci-slide text-nowrap rounded-2 px-4 mb-2 mb-xl-0">
                    Join PCCI
                </a>

                <div class="d-flex flex-column flex-xl-row gap-2 ps-xl-3 border-start-xl border-white-50" id="topbar-auth-actions">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" 
                           id="register-btn"
                           class="btn btn-outline-danger btn-outline-light-xl rounded-pill px-4 text-nowrap">
                            Register
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </nav>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const authActions = document.getElementById('topbar-auth-actions');
        const joinBtn = document.getElementById('join-pcci-btn');
        const token = localStorage.getItem('token');
        const storedRoles = JSON.parse(localStorage.getItem('userRoles') || '[]');

        if (token && authActions) {
            const dashboardUrl = storedRoles.includes('treasurer')
                ? '/treasurer-dashboard'
                : (storedRoles.includes('admin') || storedRoles.includes('superadmin') || storedRoles.includes('super_admin'))
                    ? '/dashboard'
                    : (storedRoles.includes('member') ? '/member-dashboard' : '/dashboard');

            authActions.innerHTML = `
                <button type="button" class="btn btn-outline-light rounded-pill px-4 mb-2 mb-xl-0" id="topbarLogoutBtn">Logout</button>
            `;

            const logoutBtn = document.getElementById('topbarLogoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function () {
                    localStorage.removeItem('token');
                    localStorage.removeItem('userName');
                    localStorage.removeItem('userRoles');
                    window.location.href = '/login';
                });
            }

            if (joinBtn) {
                joinBtn.textContent = 'My Account';
                joinBtn.href = dashboardUrl;
            }
        }
        
        // --- PART 1: OPTIMIZED SCROLL HANDLER (Prevents lag during scrolling) ---
        const topbar = document.getElementById("main-topbar");
        // re-use joinBtn from above
        let ticking = false; // Flag to prevent function from running too often

        function updateNavbar() {
            if (window.scrollY > 50) {
                topbar.style.backgroundColor = "rgba(164, 13, 15, 0.9)";
                topbar.classList.add("shadow-sm");
                if (joinBtn) joinBtn.classList.add("scrolled-mode");
            } else {
                topbar.style.backgroundColor = "transparent";
                topbar.classList.remove("shadow-sm");
                if (joinBtn) joinBtn.classList.remove("scrolled-mode");
            }
            ticking = false; // Reset flag after work is done
        }

        window.addEventListener("scroll", function() {
            if (!ticking) {
                window.requestAnimationFrame(updateNavbar);
                ticking = true;
            }
        });

        // Run once on load to set initial state
        updateNavbar();


        // --- PART 2: CLICK DELAY FEEDBACK (Shows 'Wait' cursor instantly) ---
        const links = document.querySelectorAll('a');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && href.startsWith('/') || href.startsWith('http')) {
                    document.body.style.cursor = 'wait'; 
                }
            });
        });

        window.addEventListener('pageshow', () => {
            document.body.style.cursor = 'default';
        });
    });
</script>