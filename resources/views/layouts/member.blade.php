@extends('layouts.app')

@section('title', 'Member Dashboard - PCCI')

@section('content')
@include('partials.api-config')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
header, footer, .navbar, nav { display: none !important; }

/* Allow natural scrolling again */
html, body {
    margin: 0;
    padding: 0;
    background: #f3f4f6; 
    font-family: Arial, sans-serif;
    overflow-x: hidden;
}

main { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }

/* =========================================
   1. TOP NAVIGATION BAR (FIXED)
   ========================================= */
.topbar {
    position: fixed;
    top: 0; left: 0; right: 0;
    height: 60px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    z-index: 1050;
}

.topbar-brand { font-size: 18px; font-weight: bold; color: #b61b2a; width: 240px; display: flex; align-items: center; gap: 8px; }
.topbar-search-wrapper { width: 35%; position: relative; }
.topbar-search-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 13px; }
.topbar-search { width: 100%; padding: 6px 15px 6px 35px; border-radius: 50rem; border: 1px solid #e5e7eb; background: #f9fafb; font-size: 13px; }
.topbar-actions { display: flex; align-items: center; gap: 15px; }
.topbar-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb; }

/* =========================================
   MOBILE HAMBURGER & OVERLAY
   ========================================= */
.hamburger-btn { display: none; background: none; border: none; font-size: 1.5rem; color: #b61b2a; cursor: pointer; padding-right: 15px; }
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1040; backdrop-filter: blur(2px); }
.sidebar-overlay.active { display: block; }

/* =========================================
   NOTIFICATION PANEL CSS
   ========================================= */
.notification-panel {
    position: fixed; top: 55px; right: 20px; width: 320px; background: #ffffff; border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; display: none; flex-direction: column; z-index: 1100; overflow: hidden;
}
@media (max-width: 576px) { .notification-panel { width: 90%; right: 5%; } }
.notif-header { background-color: #b61b2a; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; color: white; }
.notif-header-title { display: flex; align-items: center; gap: 8px; font-weight: bold; font-size: 13px; margin: 0;}
.notif-badge { background-color: white; color: black; padding: 2px 8px; border-radius: 50rem; font-size: 10px; font-weight: bold; }
.notif-clear-btn { background-color: white; color: black; border: none; padding: 4px 10px; border-radius: 50rem; font-size: 10px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: 0.2s; }
.notif-clear-btn:hover { background-color: #f3f4f6; }
.notif-body { max-height: 350px; overflow-y: auto; display: flex; flex-direction: column; }
.notif-item { display: flex; align-items: flex-start; padding: 12px 15px; border-bottom: 1px solid #f3f4f6; gap: 10px; }
.notif-unread { background-color: #f3f4f6; } 
.notif-read { background-color: #ffffff; }   
.notif-icon { width: 32px; height: 32px; border-radius: 50%; background-color: white; display: flex; justify-content: center; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex-shrink: 0; font-size: 12px;}
.notif-read .notif-icon { background-color: #f9fafb; box-shadow: none; border: 1px solid #e5e7eb; }
.notif-text-content { display: flex; flex-direction: column; gap: 4px; }
.notif-text-content p { margin: 0; font-size: 12px; color: #111827; line-height: 1.3; }
.notif-text-content small { font-size: 10px; color: #6b7280; font-weight: 500; }
.notif-footer { border-top: 1px solid #e5e7eb; padding: 10px; text-align: center; font-size: 12px; font-weight: bold; color: #4b5563; background-color: #ffffff; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 6px; transition: 0.2s; }
.notif-footer:hover { background-color: #f9fafb; color: #111827; }

/* =========================================
   2. SIDEBAR (FIXED)
   ========================================= */
.sidebar {
    position: fixed; top: 60px; left: 0; width: 250px; height: calc(100vh - 60px); 
    background: #ffffff; border-right: 1px solid #e5e7eb; display: flex; flex-direction: column; z-index: 1050; overflow-y: auto; transition: transform 0.3s ease;
}
.sidebar-profile { padding: 20px 15px 15px; text-align: center; border-bottom: 1px solid #f3f4f6; }
.sidebar-profile img { width: 70px; height: 70px; border-radius: 50%; object-fit: cover; background: #000; padding: 3px; border: 1px solid #e5e7eb; margin-bottom: 10px; }
.sidebar-profile h5 { font-size: 15px; font-weight: bold; margin-bottom: 0; color: #111827; }
.sidebar-profile p { font-size: 13px; font-weight: bold; color: #4b5563; margin-bottom: 0; }
.sidebar-profile small { font-size: 12px; color: #6b7280; }

.sidebar-menu { list-style: none; padding: 15px 10px; margin: 0; flex-grow: 1; }
.sidebar-menu li { padding: 12px 15px; margin-bottom: 4px; cursor: pointer; font-weight: 600; font-size: 14px; transition: 0.2s; color: #4b5563; border-radius: 8px; display: flex; align-items: center; gap: 10px; }
.sidebar-menu li i { font-size: 16px; width: 20px; text-align: center; }
.sidebar-menu li.active { background: #f3f4f6; color: #111827; border-left: 4px solid #b61b2a;}
.sidebar-menu li:hover:not(.active) { background: #f9fafb; }
.sidebar-divider { border-top: 1px solid #e5e7eb; margin: 10px; }

/* =========================================
   3. MAIN CONTENT AREA
   ========================================= */
.main { margin-top: 60px; margin-left: 250px; padding: 30px; min-height: calc(100vh - 60px); transition: margin-left 0.3s ease;}
.content-section { display: none; padding-bottom: 40px; }

/* Dashboard Cards */
.custom-card { background: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6; padding: 20px; display: flex; flex-direction: column; margin-bottom: 20px;}
.stat-box { padding: 20px; border-radius: 16px; color: white; text-align: center; height: 100%; display: flex; flex-direction: column; justify-content: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
.stat-orange { background: #f97316; } .stat-blue { background: #3b82f6; } .stat-teal { background: #ccfbf1; color: #0f766e; }

.active-badge { background: #dcfce7; color: #15803d; padding: 6px 16px; border-radius: 50rem; font-size: 12px; font-weight: bold; display: flex; align-items: center; gap: 6px; }
.pill-card { background: #ffffff; border-radius: 50rem; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #f3f4f6; margin-bottom: 12px; }
.carousel-dots { display: flex; justify-content: center; gap: 6px; margin-top: auto; padding-top: 15px;}
.carousel-dot { width: 8px; height: 8px; border-radius: 50%; background: #d1d5db; }
.carousel-dot.active { background: #b61b2a; }

.titleBox { background: #b61b2a; color: white; padding: 20px 25px; border-radius: 12px; font-size: 20px; display: flex; align-items: center; gap: 10px; margin-bottom: 25px; font-weight: bold;}
.editBtn { border: 1px solid #b61b2a; padding: 8px 16px; border-radius: 50rem; color: #b61b2a; background: white; cursor: pointer; font-weight: bold; font-size: 13px; transition: 0.2s; white-space: nowrap;}
.editBtn:hover { background: #fdf0f1; }
.contactItem { margin-bottom: 12px; color: #444; font-size: 14px; display: flex; align-items: center; gap: 10px;}
.contactItem i { color: #888; width: 20px; text-align: center; }
.doc-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee; }
.doc-item:last-child { border-bottom: none; }
.viewBtn { border: 1px solid #ccc; padding: 5px 15px; border-radius: 6px; background: white; cursor: pointer; font-size: 12px; font-weight: bold;}

/* Modals */
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1200; justify-content: center; align-items: center; padding: 15px;}
.modal-content-box { background: white; padding: 30px; border-radius: 20px; width: 100%; max-width: 450px; position: relative;}

/* =========================================
   RESPONSIVE MEDIA QUERIES
   ========================================= */
@media (max-width: 992px) {
    .hamburger-btn { display: block; }
    .sidebar { transform: translateX(-100%); }
    .sidebar.open { transform: translateX(0); box-shadow: 4px 0 15px rgba(0,0,0,0.1); }
    .main { margin-left: 0; padding: 15px; }
    .topbar-search-wrapper { display: none; } /* Hide search on mobile to save space */
    .topbar { padding: 0 15px; }
    .pill-card { flex-direction: column; align-items: flex-start; border-radius: 12px; gap: 5px;}
}
</style>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="topbar">
    <div class="d-flex align-items-center">
        <button class="hamburger-btn" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none" style="outline: none; box-shadow: none;">
            <div class="rounded-circle overflow-hidden" style="width: 35px; height: 35px;">
                <img src="{{ asset('images/PCCI-Logo.svg') }}" alt="PCCI Logo" class="w-100 h-100 object-fit-contain">
            </div>
            <div class="d-flex flex-column">
                <span class="fw-bold text-dark" style="font-family: 'Poppins', sans-serif; font-size: 1rem; line-height: 1.2;">
                    PCCI Valenzuela
                </span>
            </div>
        </a>
    </div>
    
    <div class="topbar-search-wrapper d-none d-md-block">
        <i class="fa fa-search"></i>
        <input type="text" class="topbar-search" placeholder="Search dashboard...">
    </div>

    <div class="topbar-actions">
        <div class="position-relative" onclick="toggleNotificationPanel(event)" style="cursor:pointer; display: flex; align-items: center;">
            <i class="fa fa-bell fs-5 text-muted"></i>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="margin-top: 4px; margin-left: -5px;"></span>
        </div>
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/ABC_Logo_2021.svg/1200px-ABC_Logo_2021.svg.png" class="topbar-avatar ms-2" id="topbarAvatar" alt="User">
    </div>
</div>

<div class="notification-panel" id="notificationPanel">
    <div class="notif-header">
        <h6 class="notif-header-title">Your Notification <span class="notif-badge">1 New</span></h6>
        <button class="notif-clear-btn" onclick="clearNotifications(event)"><i class="fa fa-times"></i> Close</button>
    </div>
    <div class="notif-body">
        <div class="notif-item notif-unread">
            <div class="notif-icon"><i class="fa fa-bell text-danger fs-5"></i></div>
            <div class="notif-text-content">
                <p><strong>Mr. ABC</strong> your membership is near to <strong>inactive</strong> to .........</p>
                <small>Monday, February 23, 2026</small>
            </div>
        </div>
    </div>
    <div class="notif-footer" onclick="markAllRead(event)"><i class="fa fa-sync-alt"></i> Mark All as Read</div>
</div>

<div class="sidebar">
    <div class="sidebar-profile">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/ABC_Logo_2021.svg/1200px-ABC_Logo_2021.svg.png" id="sidebarImage" alt="Profile">
        <h5 id="sidebarCompany">Loading...</h5>
        <p id="sidebarName">Loading...</p>
        <small id="sidebarEmail">loading...</small>
    </div>

    <ul class="sidebar-menu text-start">
        <li class="active" id="nav-dashboard" onclick="switchTab('dashboard')"><i class="fa fa-chart-pie"></i> Dashboard</li>
        <li id="nav-business" onclick="switchTab('business')"><i class="fa fa-briefcase"></i> My Business</li>
        <li id="nav-products" onclick="switchTab('products')"><i class="fa fa-box-open"></i> My Products</li>
        <li id="nav-membership" onclick="switchTab('membership')"><i class="fa fa-id-badge"></i> Membership</li>
        <div class="sidebar-divider"></div>
        <li id="nav-settings" onclick="switchTab('settings')"><i class="fa fa-gear"></i> Settings</li>
    </ul>
</div>

<div class="main">

    {{-- DASHBOARD TAB --}}
    <div id="section-dashboard" class="content-section" style="display: block;">
        <div class="mb-4">
            <h4 id="welcomeMessage" class="fw-bold text-dark mb-1">Welcome, Loading... !</h4>
            <p class="text-muted mb-0" style="font-size: 14px;">Manage your business profile, products, and membership</p>
        </div>

        <div class="row mb-4 g-3">
            <div class="col-6 col-md-3">
                <div class="stat-box stat-orange">
                    <h2 class="fw-bold mb-0">12</h2>
                    <p class="mb-0" style="font-size: 14px;">Products</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box stat-blue">
                    <h2 class="fw-bold mb-0">3</h2>
                    <p class="mb-0" style="font-size: 14px;">Events</p>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="stat-box stat-teal text-center text-md-start px-4">
                    <h5 id="liveDate" class="fw-bold mb-1 text-dark">Loading Date...</h5>
                    <h2 id="liveTime" class="fw-bold mb-0" style="color: #0f766e;">Loading Time...</h2>
                </div>
            </div>
        </div>

        <div class="row mb-4 g-4">
            <div class="col-lg-6">
                <div class="custom-card h-100">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/ABC_Logo_2021.svg/1200px-ABC_Logo_2021.svg.png" id="dashBizImage" style="width: 50px; height: 50px; border-radius: 50%; border: 1px solid #ddd; object-fit: cover;">
                            <div>
                                <h5 class="fw-bold mb-0" id="dashBizName">Loading...</h5>
                                <small class="text-muted text-break" id="dashBizEmail">Loading...</small>
                            </div>
                        </div>
                        <button class="btn btn-light text-danger rounded-pill px-3 fw-bold" onclick="switchTab('business')" style="border: 1px solid #e5e7eb; font-size: 12px;">
                            <i class="fa fa-pen me-1"></i> Edit
                        </button>
                    </div>
                    <div style="font-size: 14px;">
                        <p class="mb-2"><strong class="text-muted d-inline-block" style="width: 130px;">Receipt No:</strong> <span class="text-dark fw-bold">123456789</span></p>
                        <p class="mb-2"><strong class="text-muted d-inline-block" style="width: 130px;">Business Type:</strong> <span class="text-dark fw-bold text-break" id="dashBizType">Loading...</span></p>
                        <p class="mb-0"><strong class="text-muted d-inline-block" style="width: 130px;">Ownership:</strong> <span class="text-dark fw-bold">Solo Partnership</span></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="custom-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Membership</h5>
                        <div class="active-badge"><i class="fa fa-check-circle"></i> <span id="dashMembershipStatus">Loading...</span></div>
                    </div>
                    <div style="font-size: 14px;" class="mb-4 flex-grow-1">
                        <p class="mb-2"><strong class="text-muted d-inline-block" style="width: 130px;">Member ID:</strong> <span class="text-dark fw-bold" id="dashMembershipID">...</span></p>
                        <p class="mb-0"><strong class="text-muted d-inline-block" style="width: 130px;">Type:</strong> <span class="text-dark fw-bold text-break" id="dashMembershipType">Loading...</span></p>
                    </div>
                    <button class="btn btn-danger w-100 rounded-pill py-2 fw-bold mt-auto" onclick="switchTab('membership')">Renew Membership</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MY BUSINESS TAB --}}
    <div id="section-business" class="content-section" style="display: none;">
        <div class="titleBox"><i class="fa fa-briefcase"></i> My Business</div>
        
        <div class="custom-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/ABC_Logo_2021.svg/1200px-ABC_Logo_2021.svg.png" id="bizMainImage" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 1px solid #e5e7eb;">
                    <div>
                        <h4 class="fw-bold mb-1 text-break" id="bizNameTitle">Loading...</h4>
                        <div class="text-muted" style="font-size: 14px;" id="bizIndustryTitle">Loading Industry...</div>
                    </div>
                </div>
                <button class="editBtn w-100 w-md-auto" onclick="openEditProfileModal()"><i class="fa fa-pen me-1"></i> Edit Profile</button>
            </div>

            <div class="row mt-2 g-4">
                <div class="col-md-7">
                    <div class="contactItem"><i class="fa fa-envelope"></i> <span id="bizEmailText" class="text-break">loading...</span></div>
                    <div class="contactItem"><i class="fa fa-phone"></i> <span id="bizPhoneText">loading...</span></div>
                    <div class="contactItem"><i class="fa fa-location-dot"></i> <span id="bizAddressText">loading...</span></div>
                </div>
                <div class="col-md-5 border-start-md ps-md-4">
                    <h6 class="fw-bold mb-2">Membership Details</h6>
                    <p class="text-muted mb-0" style="font-size: 13px;">Expires Date</p>
                    <p class="fw-bold mb-3" style="font-size: 14px;" id="bizExpiryText">March 1, 2027</p>
                    <p class="text-muted mb-0" style="font-size: 13px;">Membership Type</p>
                    <p class="fw-bold mb-0" style="font-size: 14px;" id="bizMembershipTypeText">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- REFACTORED INCLUDES --}}
    @include('member.products')
    @include('member.membership')
    @include('member.settings')

</div>

<div class="modal-overlay" id="editProfileModal">
    <div class="modal-content-box" style="max-width: 650px; max-height: 90vh; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-pen text-danger me-2"></i>Edit Profile</h5>
            <button class="btn-close" onclick="closeEditProfileModal()"></button>
        </div>

        <div id="profileAlert" class="alert alert-danger" style="display: none; font-size: 13px;"></div>
        
        <h6 class="fw-bold mb-3 text-danger border-bottom pb-2">Business Details</h6>
        <div class="mb-3">
            <label class="form-label fw-bold text-muted" style="font-size: 13px;">Business Name</label>
            <input type="text" id="ep_companyName" class="form-control" placeholder="Company Name">
        </div>
        <div class="row mb-3 g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold text-muted" style="font-size: 13px;">Email</label>
                <input type="email" id="ep_email" class="form-control" placeholder="example@email.com">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold text-muted" style="font-size: 13px;">Phone</label>
                <input type="text" id="ep_phone" class="form-control" placeholder="+63 912 345 6789">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button class="btn btn-light fw-bold" onclick="closeEditProfileModal()">Cancel</button>
            <button class="btn btn-danger fw-bold px-4" id="btnSaveProfile" onclick="saveProfile()">Save</button>
        </div>
    </div>
</div>

<script>
const token = localStorage.getItem('token');

document.addEventListener('DOMContentLoaded', function(){
    if(!token) { window.location.href = '/login'; return; }
    startLiveClock();
    fetchRealDashboardData(token);
});

function startLiveClock() {
    const dateEl = document.getElementById('liveDate');
    const timeEl = document.getElementById('liveTime');
    function updateTime() {
        const now = new Date();
        dateEl.innerText = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        timeEl.innerText = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    }
    updateTime(); setInterval(updateTime, 1000);
}

// Sidebar Mobile Logic
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('active');
    document.body.style.overflow = document.querySelector('.sidebar').classList.contains('open') ? 'hidden' : '';
}

function switchTab(tabName) {
    document.querySelectorAll('.content-section').forEach(section => section.style.display = 'none');
    document.querySelectorAll('.sidebar-menu li').forEach(li => li.classList.remove('active'));
    document.getElementById('section-' + tabName).style.display = 'block';
    const activeNav = document.getElementById('nav-' + tabName);
    if(activeNav) activeNav.classList.add('active');

    // Close sidebar on mobile after clicking a link
    if (window.innerWidth <= 992) {
        document.querySelector('.sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    if (tabName === 'products') fetchProducts();
}

function toggleNotificationPanel(event) {
    event.stopPropagation(); 
    const panel = document.getElementById('notificationPanel');
    panel.style.display = panel.style.display === 'flex' ? 'none' : 'flex';
}

document.addEventListener('click', function(event) {
    const panel = document.getElementById('notificationPanel');
    if (panel.style.display === 'flex' && !panel.contains(event.target)) {
        panel.style.display = 'none';
    }
});

function clearNotifications(event) { event.stopPropagation(); document.getElementById('notificationPanel').style.display = 'none'; }
function markAllRead(event) { event.stopPropagation(); alert('All notifications marked as read!'); }
function logout() { localStorage.removeItem('token'); window.location.href = '/login'; }

// Profile edit Modal
function openEditProfileModal() {
    if (!window.currentProfileData) return;
    const profile = window.currentProfileData;
    const basic = profile.basic_profile || {};
    document.getElementById('ep_companyName').value = basic.registered_business_name || '';
    document.getElementById('ep_email').value = basic.email || '';
    document.getElementById('ep_phone').value = basic.contact_number || '';
    document.getElementById('profileAlert').style.display = 'none';
    document.getElementById('editProfileModal').style.display = 'flex';
}
function closeEditProfileModal() { document.getElementById('editProfileModal').style.display = 'none'; }

async function fetchRealDashboardData(token) {
    try {
        const response = await fetch(`${window.API_BASE_URL}/v1/application`, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        if (response.status === 401) { logout(); return; }
        const data = await response.json();
        if (response.ok && data.data) {
            const profile = Array.isArray(data.data) ? data.data[0] : data.data;
            window.currentProfileData = profile;
            if (!profile) return;
            const basic = profile.basic_profile || {};
            const org = profile.organization_membership || {};
            const rep = profile.official_representative || {};
            const loc = basic.business_location || {};
            const companyName = basic.registered_business_name || 'Your Company';
            const repName = `${rep.first_name || ''} ${rep.surname || ''}`.trim();
            const memberID = `PCCI-${new Date().getFullYear()}-${String(profile.id).padStart(4, '0')}`;

            document.getElementById('sidebarCompany').innerText = companyName;
            document.getElementById('sidebarName').innerText = repName || 'No Rep Assigned';
            document.getElementById('sidebarEmail').innerText = basic.email || 'N/A';
            document.getElementById('welcomeMessage').innerText = `Welcome, ${companyName}!`;
            document.getElementById('dashBizName').innerText = companyName;
            document.getElementById('dashBizEmail').innerText = basic.email || 'N/A';
            document.getElementById('dashMembershipStatus').innerText = (profile.status || 'Pending').toUpperCase();
            document.getElementById('dashMembershipID').innerText = memberID;
            
            document.getElementById('bizNameTitle').innerText = companyName;
            document.getElementById('bizEmailText').innerText = basic.email || 'N/A';
            document.getElementById('bizPhoneText').innerText = basic.contact_number || 'N/A';
        }
    } catch(error) { console.error("Failed to fetch API Data:", error); }
}

async function saveProfile() {
    alert("Saving Profile details...");
    closeEditProfileModal();
}
</script>
@endsection