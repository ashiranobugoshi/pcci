@extends('layouts.app')

@section('title', 'Treasurer Dashboard - PCCI')

@section('content')
@include('partials.api-config')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* Reset Layout Constraints */
header, footer, .navbar, nav { display: none !important; }
html, body { margin: 0; padding: 0; background: #f3f4f6; font-family: 'Inter', 'Poppins', sans-serif; overflow-x: hidden; transition: background-color 0.3s, color 0.3s;}
main { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }

/* =========================================
   1. TOP NAVIGATION BAR (FIXED)
   ========================================= */
.topbar {
    position: fixed; top: 0; left: 0; right: 0; height: 70px; background: #ffffff;
    display: flex; align-items: center; justify-content: space-between; padding: 0 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); z-index: 1050; transition: background-color 0.3s;
}
.topbar-search-wrapper { width: 300px; position: relative; }
.topbar-search-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 13px; }
.topbar-search { width: 100%; height: 36px; padding: 6px 15px 6px 35px; border-radius: 8px; border: 1px solid #eee; background: #eee; font-size: 13px; outline: none; transition: 0.3s;}
.topbar-actions { display: flex; align-items: center; gap: 15px; }
.topbar-avatar { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb; cursor: pointer; }

/* =========================================
   MOBILE HAMBURGER & OVERLAY
   ========================================= */
.hamburger-btn { display: none; background: none; border: none; font-size: 1.5rem; color: #b61b2a; cursor: pointer; padding-right: 15px; }
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1040; backdrop-filter: blur(2px); }
.sidebar-overlay.active { display: block; }

/* =========================================
   2. NOTIFICATION PANEL
   ========================================= */
.notification-panel {
    position: fixed; top: 60px; right: 20px; width: 320px; background: #ffffff; border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; display: none; flex-direction: column; z-index: 1100; overflow: hidden;
}
@media (max-width: 576px) { .notification-panel { width: 90%; right: 5%; } }
.notif-header { background-color: #b61b2a; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; color: white; }
.notif-header-title { font-weight: bold; font-size: 13px; margin: 0;}
.notif-badge { background-color: white; color: black; padding: 2px 8px; border-radius: 50rem; font-size: 10px; font-weight: bold; }
.notif-clear-btn { background-color: white; color: black; border: none; padding: 4px 10px; border-radius: 50rem; font-size: 10px; font-weight: bold; cursor: pointer; }
.notif-body { max-height: 350px; overflow-y: auto; display: flex; flex-direction: column; }
.notif-item { display: flex; padding: 12px 15px; border-bottom: 1px solid #f3f4f6; gap: 10px; }
.notif-icon { width: 32px; height: 32px; border-radius: 50%; display: flex; justify-content: center; align-items: center; flex-shrink: 0; font-size: 12px;}
.notif-text-content p { margin: 0; font-size: 12px; color: #111827; line-height: 1.3; }
.notif-text-content small { font-size: 10px; color: #6b7280; }
.notif-footer { padding: 10px; text-align: center; font-size: 12px; font-weight: bold; color: #4b5563; background-color: #ffffff; cursor: pointer; border-top: 1px solid #e5e7eb;}

/* =========================================
   3. SIDEBAR (FIXED)
   ========================================= */
.sidebar {
    position: fixed; top: 70px; left: 0; width: 250px; height: calc(100vh - 70px);
    background: #f8f9fb; border-right: 1px solid #e5e7eb; display: flex; flex-direction: column; z-index: 1050; overflow-y: auto; transition: transform 0.3s ease, background-color 0.3s;
}
.sidebar-profile { padding: 20px 15px 15px; text-align: center; border-bottom: 1px solid #e5e7eb; }
.sidebar-profile img { width: 65px; height: 65px; border-radius: 50%; object-fit: cover; border: 1px solid #e5e7eb; margin-bottom: 10px; }
.sidebar-profile h5 { font-size: 15px; font-weight: bold; margin-bottom: 0; color: #111; }
.sidebar-profile p { font-size: 13px; font-weight: bold; color: #4b5563; margin-bottom: 0; }
.sidebar-profile small { font-size: 12px; color: #777; }

.sidebar-menu { list-style: none; padding: 15px 10px; margin: 0; flex-grow: 1; }
.sidebar-menu li { height: 45px; padding: 0 15px; margin-bottom: 4px; cursor: pointer; font-weight: 600; font-size: 14px; color: #4b5563; border-radius: 8px; display: flex; align-items: center; gap: 10px; transition: 0.2s;}
.sidebar-menu li i { font-size: 16px; width: 20px; text-align: center; }
.sidebar-menu li.active { background: #e5e7eb; color: #111; border-left: 4px solid #b61b2a;}
.sidebar-menu li:hover:not(.active) { background: #eef0f4; }
.sidebar-divider { border-top: 1px solid #e5e7eb; margin: 10px; }

/* =========================================
   4. MAIN CONTENT AREA & COMPONENTS
   ========================================= */
.main { margin-top: 70px; margin-left: 250px; padding: 25px; min-height: calc(100vh - 70px); background: #f4f6f9; transition: margin-left 0.3s ease, background-color 0.3s;}
.content-section { display: none; padding-bottom: 40px; }

/* Floating Cards Base */
.floating-card { background: #ffffff; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); padding: 16px; border: none; transition: 0.3s; }
.custom-card { background: #ffffff; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); padding: 20px; margin-bottom: 20px; transition: 0.3s;}

/* Specific Summary Cards (Dashboard) */
.summary-card { width: 100%; height: 105px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); padding: 20px; color: white; position: relative; overflow: hidden; display: flex; align-items: center;}
.summary-card.dash-card { flex-direction: column; align-items: flex-start; justify-content: center; }
.summary-card .icon-circle { width: 45px; height: 45px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; justify-content: center; align-items: center; font-size: 20px; flex-shrink: 0;}
.summary-card .label { font-size: 14px; font-weight: 500; opacity: 0.9; margin-bottom: 2px; }
.summary-card .value { font-size: 26px; font-weight: bold; margin: 0; line-height: 1; }
.summary-card .bg-icon { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 40px; opacity: 0.2; }

.bg-red { background: linear-gradient(135deg, #e53935, #c62828); }
.bg-green { background: linear-gradient(135deg, #43a047, #2e7d32); }
.bg-orange { background: linear-gradient(135deg, #fb8c00, #ef6c00); }

/* Small Info Card */
.small-info-card { width: 100%; max-width: 320px; height: auto; padding: 15px; display: flex; align-items: center; gap: 15px; }
.small-info-card .icon-box { width: 40px; height: 40px; border-radius: 8px; background: #e3f2fd; color: #1976d2; display: flex; justify-content: center; align-items: center; font-size: 18px; flex-shrink: 0;}
.small-info-card p { margin: 0; font-size: 12px; color: #555; font-weight: 500; }
.small-info-card p span { font-weight: bold; color: #222; }

/* Charts Area */
.chart-container { height: 280px; width: 100%; position: relative; }
.card-title-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
.card-title-row h5 { font-size: 16px; font-weight: bold; margin: 0; color: #333; }

/* Table Area */
.table-card { min-height: 350px; padding: 15px 20px; }
.custom-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.custom-table th { background: #f8f9fb; color: #777; font-weight: 600; padding: 12px 10px; text-align: left; position: sticky; top: 0; z-index: 1; white-space: nowrap;}
.custom-table td { padding: 12px 10px; border-bottom: 1px solid #eee; color: #444; vertical-align: middle; height: 45px;}

/* Action Buttons & Badges */
.action-btn { height: 30px; padding: 0 10px; border-radius: 6px; border: none; font-size: 12px; font-weight: bold; cursor: pointer; color: white; display: inline-flex; align-items: center; justify-content: center; gap: 5px; margin-right: 4px; white-space: nowrap;}
.btn-gray { background-color: #9ca3af; }
.btn-green { background-color: #22c55e; } 
.btn-red { background-color: #ef4444; } 

.action-icon-btn { width: 32px; height: 32px; padding: 0; border-radius: 6px; transition: 0.2s; display: inline-flex; justify-content: center; align-items: center; font-size: 14px;}
.action-icon-btn:hover { background-color: #e5e7eb; }

/* Status Badges */
.status-badge { display: inline-block; padding: 4px 10px; border-radius: 50rem; font-size: 12px; color: white; text-align: center; font-weight: 500; }
.status-completed { background-color: #22c55e; }
.status-pending { background-color: #f59e0b; }
.status-failed { background-color: #ef4444; }

/* =========================================
   REPORTS TAB SPECIFIC STYLES & DROPDOWN
   ========================================= */
.reports-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
.report-stat-card { background: #ffffff; border-radius: 12px; padding: 16px; height: 120px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: center; gap: 6px; transition: 0.3s; }
.report-stat-card .report-label { font-size: 12px; color: #777; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;}
.report-stat-card .report-value { font-size: 24px; font-weight: 600; color: #111; margin: 0; line-height: 1;}
.report-stat-card .report-indicator { font-size: 11px; font-weight: 600; }
.text-green { color: #22c55e !important; }
.text-red { color: #ef4444 !important; }
.report-chart-box { background: #ffffff; border-radius: 12px; padding: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); height: 300px; display: flex; flex-direction: column; transition: 0.3s; }
.mini-card-container { display: flex; gap: 16px; align-items: center; justify-content: center; height: 100%; flex-wrap: wrap;}
.mini-stat-card { background: #f8f9fb; border-radius: 12px; width: 140px; height: 90px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px solid #eee; transition: 0.3s; }
.mini-stat-card .m-val { font-size: 20px; font-weight: 600; color: #111; line-height: 1.2;}
.mini-stat-card .m-lbl { font-size: 12px; color: #777; }

.report-dropdown-menu { position: absolute; right: 0; top: 100%; margin-top: 8px; background: #ffffff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 220px; padding: 8px; display: none; flex-direction: column; z-index: 1050; border: 1px solid #e5e7eb; animation: fadeIn 0.2s ease-in-out;}
.report-dropdown-item { padding: 10px 15px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: #4b5563; display: flex; align-items: center; gap: 12px; transition: 0.2s;}
.report-dropdown-item:hover { background: #f3f4f6; color: #111; }

/* =========================================
   MODAL STYLES (GENERAL)
   ========================================= */
.custom-modal-overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.7); display: none; justify-content: center; align-items: center; z-index: 1200; backdrop-filter: blur(3px); padding: 15px;}
.custom-modal-card { background: #ffffff; width: 100%; max-width: 700px; border-radius: 16px; padding: 25px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); position: relative; }
.modal-close-x { position: absolute; top: 15px; right: 20px; font-size: 28px; color: #888; cursor: pointer; border: none; background: none; line-height: 1; }
.modal-img-wrapper { width: 100%; min-height: 120px; display: flex; justify-content: center; align-items: center; position: relative; }
.modal-img-wrapper img { max-width: 100%; max-height: 400px; width: auto; height: auto; border-radius: 8px; border: 1px solid #ddd; display: block; }

.type-toggle-btn { padding: 12px; border-radius: 10px; border: 2px solid #e5e7eb; background: #f9fafb; color: #4b5563; font-weight: bold; cursor: pointer; transition: 0.2s; text-align: center; }
.type-toggle-btn.active-1 { background: #fff1f2; color: #ef4444; border-color: #ef4444; }
.type-toggle-btn.active-2 { background: #eff6ff; color: #3b82f6; border-color: #3b82f6; }

/* Setting UI */
.setting-box { transition: 0.2s; background: #ffffff; cursor: pointer; border: 1px solid #eee; border-radius: 12px;}
.setting-box:hover { background-color: #f9fafb; border-color: #d1d5db !important; }
.new-acc-card { background: #fff; border-radius: 12px; padding: 35px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 1000px; margin: 0 auto; transition: 0.3s; }
.new-acc-input { background: #f8f9fb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 12px; font-size: 14px; width: 100%; height: 42px; }

/* =========================================
   RESPONSIVE MEDIA QUERIES
   ========================================= */
@media (max-width: 992px) {
    .hamburger-btn { display: block; }
    .sidebar { transform: translateX(-100%); }
    .sidebar.open { transform: translateX(0); box-shadow: 4px 0 15px rgba(0,0,0,0.1); }
    .main { margin-left: 0; padding: 15px; }
    .topbar-search-wrapper { display: none; } /* Hide search to save topbar space */
    .topbar { padding: 0 15px; }
    .reports-grid { grid-template-columns: repeat(2, 1fr); }
    .summary-card { margin-bottom: 10px; }
}

@media (max-width: 576px) {
    .reports-grid { grid-template-columns: 1fr; }
    .new-acc-card { padding: 20px; }
}
</style>

{{-- OVERLAY FOR MOBILE SIDEBAR --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

{{-- TOP NAVIGATION --}}
<div class="topbar">
    <div class="d-flex align-items-center">
        <button class="hamburger-btn" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none" style="outline: none; box-shadow: none;">
            <div class="rounded-circle overflow-hidden" style="width: 35px; height: 35px; border: 1px solid #e5e7eb;">
                <img src="{{ asset('images/PCCI-Logo.svg') }}" alt="PCCI Logo" class="w-100 h-100 object-fit-contain p-1">
            </div>
            <div class="d-flex flex-column d-none d-sm-flex">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark" style="font-family: 'Poppins', sans-serif; font-size: 1rem; line-height: 1.2;">PCCI - Valenzuela</span>
                </div>
            </div>
        </a>
    </div>
    
    <div class="topbar-search-wrapper d-none d-md-block">
        <i class="fa fa-search"></i>
        <input type="text" class="topbar-search" placeholder="Search records, members...">
    </div>

    <div class="topbar-actions">
        <button class="btn btn-sm border d-none d-md-flex align-items-center gap-2 text-muted fw-bold px-3 py-1" onclick="toggleDarkMode()" id="darkModeBtn" style="border-radius: 50rem; background: #f9fafb; font-size: 13px;">
            <i class="fa fa-moon" id="darkModeIcon"></i> <span id="darkModeText">Dark Mode</span>
        </button>

        <div class="position-relative" onclick="toggleNotificationPanel(event)" style="cursor:pointer; display: flex; align-items: center; margin-left: 10px;">
            <i class="fa fa-bell fs-5 text-muted"></i>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="margin-top: 4px; margin-left: -5px; display: none;"></span>
        </div>
        <img src="{{ asset('images/PCCI-Logo.svg') }}" class="topbar-avatar ms-2" id="topbarAvatar" alt="User">
    </div>
</div>

{{-- NOTIFICATION PANEL --}}
<div class="notification-panel" id="notificationPanel">
    <div class="notif-header">
        <h6 class="notif-header-title">Notifications <span class="notif-badge">0 New</span></h6>
        <button class="notif-clear-btn" onclick="clearNotifications(event)"><i class="fa fa-times"></i></button>
    </div>
    <div class="notif-body">
        <div class="notif-item" style="background: #f9fafb;">
            <div class="notif-icon" style="background: white; border: 1px solid #ddd;"><i class="fa fa-info-circle text-primary fs-5"></i></div>
            <div class="notif-text-content">
                <p>Loading notifications...</p>
            </div>
        </div>
    </div>
    <div class="notif-footer" onclick="clearNotifications(event)">Close Panel</div>
</div>

{{-- SIDEBAR --}}
<div class="sidebar">
    <div class="sidebar-profile">
        <img src="{{ asset('images/PCCI-Logo.svg') }}" alt="Profile">
        <h5 id="sidebarName">Jesus Versula</h5>
        <small id="sidebarEmail">jesus.versula@pcci.com</small>
    </div>

    <ul class="sidebar-menu text-start">
        <li class="active" id="nav-dashboard" onclick="switchTab('dashboard')">
            <i class="fa fa-chart-pie"></i> Dashboard
        </li>
        <li id="nav-members" onclick="switchTab('members')">
            <i class="fa fa-users"></i> Members
        </li>
        <li id="nav-applicants" onclick="switchTab('applicants')">
            <i class="fa fa-user-plus"></i> Applicants
        </li>
        <li id="nav-transactions" onclick="switchTab('transactions')">
            <i class="fa fa-money-bill-wave"></i> Transactions
        </li>
        <li id="nav-reports" onclick="switchTab('reports')">
            <i class="fa fa-chart-line"></i> Reports
        </li>
        <div class="sidebar-divider"></div>
        <li id="nav-settings" onclick="switchTab('settings')">
            <i class="fa fa-cog"></i> Settings
        </li>
    </ul>
</div>

{{-- MAIN CONTENT --}}
<div class="main">

    {{-- DASHBOARD OVERVIEW TAB --}}
    <div id="section-dashboard" class="content-section" style="display: block;">
        <div class="mb-4 pb-2">
            <h3 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif;">Welcome, <span id="dashWelcomeName">Jesus</span>!</h3>
            <p class="text-muted mb-0" style="font-size: 14px;">Here is your financial overview for today.</p>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="summary-card dash-card bg-red">
                    <div class="label">Total Revenue</div>
                    <div class="value">PHP 205,500</div>
                    <i class="fa fa-wallet bg-icon"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card dash-card bg-green">
                    <div class="label">Paid Members</div>
                    <div class="value">Php. 205,500</div>
                    <i class="fa fa-users bg-icon"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card dash-card bg-orange">
                    <div class="label">Active Account</div>
                    <div class="value">20</div>
                    <i class="fa fa-user-check bg-icon"></i>
                </div>
            </div>
        </div>

        {{-- SMALL INFO CARD --}}
        <div class="floating-card small-info-card mb-4">
            <div class="icon-box"><i class="fa fa-calendar-check"></i></div>
            <div>
                <p>Today's Payments: <span id="today-payments-amt" class="text-success fs-6">₱0</span></p>
                <p>Yesterday payment: <span id="yesterday-payments-amt" class="text-muted fw-bold">₱0</span></p>
            </div>
        </div>

        {{-- MIDDLE SECTION: CHARTS --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="floating-card">
                    <div class="card-title-row">
                        <h5>Membership Revenue</h5>
                        <select class="form-select form-select-sm" style="width: auto; font-size: 12px; cursor: pointer;">
                            <option>This Month</option>
                            <option>This Year</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="floating-card">
                    <div class="card-title-row">
                        <h5>Payment Breakdown</h5>
                    </div>
                    <div class="chart-container d-flex justify-content-center">
                        <canvas id="pieChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTTOM SECTION: RECENT PAYMENTS TABLE --}}
        <div class="floating-card table-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="font-size: 18px; font-weight: bold; margin: 0; color: #111;">Recent Payments</h5>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Business Name</th>
                            <th>Membership Type</th>
                            <th>Amount</th>
                            <th>OR Number</th>
                            <th>Date</th>
                            <th>Proof of Payment</th>
                        </tr>
                    </thead>
                    <tbody id="recent-payments-table-body">
                        <tr><td colspan="6" class="text-center py-4">Loading records...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- MEMBERS TAB --}}
    <div id="section-members" class="content-section" style="display: none;">
        <div class="mb-4">
            <h3 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif;">Members</h3>
            <p class="text-muted mb-0" style="font-size: 14px;">Manage and review all member records.</p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="summary-card bg-red d-flex align-items-center">
                    <div class="icon-circle me-3"><i class="fa fa-user-times"></i></div>
                    <div><div class="label">Unpaid Members</div><div class="value" id="unpaid-count">0</div></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="summary-card bg-green d-flex align-items-center">
                    <div class="icon-circle me-3"><i class="fa fa-user-check"></i></div>
                    <div><div class="label">Paid Members</div><div class="value" id="paid-count">0</div></div>
                </div>
            </div>
        </div>

        <div class="floating-card table-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 border-bottom pb-3 gap-3">
                <div class="d-flex align-items-center gap-2">
                    <h5 style="font-size: 18px; font-weight: bold; margin: 0; color: #111;">Members Directory</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill" id="total-members-badge">0 Active</span>
                </div>
                
                <div class="d-flex flex-column flex-sm-row align-items-center gap-3 w-100 w-md-auto">
                    <select id="memberSort" class="form-select form-select-sm text-muted fw-bold w-100 w-sm-auto" style="height: 36px; border-radius: 6px; font-size: 13px;">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                    
                    <div style="position: relative; width: 100%; max-width: 250px;">
                        <i class="fa fa-search text-muted" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                        <input type="text" id="memberSearch" placeholder="Search members..." style="width: 100%; height: 36px; padding-left: 35px; border-radius: 6px; border: 1px solid #eee; font-size: 13px; outline: none; background: #f8f9fb;">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Member Name</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>OR Number</th>
                            <th>Reg Date</th>
                            <th>Exp Date</th>
                            <th>Status</th>
                            <th>Proof</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="members-table-body">
                        <tr><td colspan="9" class="text-center py-5 text-muted"><i class="fa fa-spinner fa-spin fs-3 mb-2"></i><br>Loading members...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center align-items-center mt-3" style="height: 50px; gap: 15px;">
                <button id="member-prev-btn" class="btn btn-sm btn-light border rounded" onclick="prevMemberPage()"><i class="fa fa-chevron-left"></i></button>
                <span style="font-size: 14px; font-weight: bold; color: #4b5563;" id="member-pagination-text">Page 1 of 1</span>
                <button id="member-next-btn" class="btn btn-sm btn-light border rounded" onclick="nextMemberPage()"><i class="fa fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

    {{-- APPLICANTS TAB --}}
    <div id="section-applicants" class="content-section" style="display: none;">
        <div class="mb-4">
            <h3 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif;">Applicants Queue</h3>
            <p class="text-muted mb-0" style="font-size: 14px;">Review and process payments for newly approved businesses.</p>
        </div>

        <div class="floating-card table-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 border-bottom pb-3 gap-3">
                <div class="d-flex align-items-center gap-2">
                    <h5 style="font-size: 18px; font-weight: bold; margin: 0; color: #111;">Applicants Directory</h5>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded-pill" id="report-pending-count-badge">0 Pending</span>
                </div>
                
                <div class="d-flex flex-column flex-sm-row align-items-center gap-3 w-100 w-md-auto">
                    <select id="applicantSort" class="form-select form-select-sm text-muted fw-bold w-100 w-sm-auto" style="height: 36px; border-radius: 6px; font-size: 13px;">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                    
                    <div style="position: relative; width: 100%; max-width: 250px;">
                        <i class="fa fa-search text-muted" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                        <input type="text" id="applicantSearch" placeholder="Search applicants..." style="width: 100%; height: 36px; padding-left: 35px; border-radius: 6px; border: 1px solid #eee; font-size: 13px; outline: none; background: #f8f9fb;">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Business Name</th>
                            <th>Trade Name</th>
                            <th>Email</th>
                            <th>Date Submitted</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="applicants-table-body">
                        <tr><td colspan="8" class="text-center py-5 text-muted"><i class="fa fa-spinner fa-spin fs-3 mb-2"></i><br>Loading applicants...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center align-items-center mt-3" style="height: 50px; gap: 15px;">
                <button class="btn btn-sm btn-light border rounded" onclick="prevApplicantPage()"><i class="fa fa-chevron-left"></i></button>
                <span style="font-size: 14px; font-weight: bold; color: #4b5563;" id="applicant-pagination-text">Page 1 of 1</span>
                <button class="btn btn-sm btn-light border rounded" onclick="nextApplicantPage()"><i class="fa fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

    {{-- TRANSACTIONS TAB --}}
    <div id="section-transactions" class="content-section" style="display: none;">
        <div class="mb-4">
            <h3 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif;">Transactions</h3>
            <p class="text-muted mb-0" style="font-size: 14px;">Manage and track all payment activities.</p>
        </div>

        <div class="reports-grid mb-4">
            <div class="report-stat-card">
                <div class="report-label">Total Payments</div>
                <div class="report-value" id="trans-total-amt">Php. 0</div>
            </div>
            <div class="report-stat-card">
                <div class="report-label">Pending Payments</div>
                <div class="report-value" id="trans-pending-amt">Php. 0</div>
            </div>
            <div class="report-stat-card">
                <div class="report-label">Complete Payments</div>
                <div class="report-value" id="trans-complete-amt">Php. 0</div>
            </div>
            <div class="report-stat-card">
                <div class="report-label">Failed Payment</div>
                <div class="report-value" id="trans-failed-amt">Php. 0</div>
            </div>
        </div>

        <div class="floating-card table-card" style="padding: 0; overflow: hidden; border-bottom: 6px solid #b61b2a;">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center p-3 border-bottom gap-3">
                <div>
                    <h5 style="font-size: 18px; font-weight: bold; margin: 0; color: #111;">Transaction Records</h5>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    
                    <div class="position-relative w-100" id="transFilterContainer" style="max-width: 250px;">
                        <i class="fa fa-search text-muted" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                        <input type="text" id="transactionSearch" placeholder="Search transactions..." style="width: 100%; height: 38px; padding-left: 35px; border-radius: 8px; border: 1px solid #eee; font-size: 13px; outline: none; background: #f8f9fb;">
                    </div>
                    
                    <button class="btn btn-danger fw-bold shadow-sm d-flex align-items-center gap-2" style="height: 38px; border-radius: 8px; background: #dc2626; border: none; font-size: 13px; padding: 0 16px;" onclick="openAddPaymentModal()">
                        <i class="fa fa-plus"></i> Add
                    </button>
                    
                </div>
            </div>

            <div class="table-responsive">
                <table class="custom-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Member Name</th>
                            <th>Payment Type</th>
                            <th>Date</th>
                            <th>Membership Type</th>
                            <th>OR Number</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="transactions-table-body">
                        <tr><td colspan="7" class="text-center py-5 text-muted"><i class="fa fa-spinner fa-spin fs-3 mb-2"></i><br>Loading transactions...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- REPORTS TAB --}}
    <div id="section-reports" class="content-section" style="display: none;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom gap-3">
            <div>
                <h3 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif; font-size: 24px;">Reports</h3>
                <p class="text-muted mb-0" style="font-size: 14px;">Generate and review comprehensive financial analytics.</p>
            </div>
            
            <div class="position-relative d-inline-block" id="reportDropdownContainer">
                <button class="btn btn-success fw-bold rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2 w-100" onclick="toggleReportDropdown(event)" style="background: #22c55e; border: none; font-size: 14px;">
                    <i class="fa fa-download"></i> Download Reports
                </button>
                <div class="report-dropdown-menu" id="reportDropdownMenu">
                    <div class="report-dropdown-item" onclick="downloadReport('pdf')">
                        <i class="fa fa-file-pdf text-danger w-20px"></i> Download as PDF
                    </div>
                </div>
            </div>
        </div>

        <div class="reports-grid">
            <div class="report-stat-card">
                <div class="report-label">Monthly Revenue</div>
                <div class="report-value">₱24,500</div>
            </div>
            <div class="report-stat-card">
                <div class="report-label">Total Active Members</div>
                <div class="report-value" id="report-active-members">0</div>
            </div>
            <div class="report-stat-card">
                <div class="report-label">Pending Verifications</div>
                <div class="report-value text-warning" id="report-pending-count">0</div>
            </div>
            <div class="report-stat-card">
                <div class="report-label">Failed / Cancelled</div>
                <div class="report-value text-red">0</div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="report-chart-box">
                    <h6 class="fw-bold mb-3 text-dark" style="font-size: 15px;">Membership Revenue</h6>
                    <div style="flex-grow: 1; position: relative;">
                        <canvas id="reportBarChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="report-chart-box">
                    <h6 class="fw-bold mb-3 text-dark" style="font-size: 15px;">Payment Breakdown</h6>
                    <div style="flex-grow: 1; position: relative; display:flex; justify-content:center;">
                        <canvas id="reportPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SETTINGS TAB --}}
    <div id="section-settings" class="content-section" style="display: none;">
        
        {{-- VIEW 1: Main Settings Menu --}}
        <div id="settings-main" class="fade-in">
            <div class="mb-4 pb-2 border-bottom">
                <h3 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif;">Settings</h3>
                <p class="text-muted mb-0" style="font-size: 14px;">Manage your account, security, and preferences.</p>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="custom-card mb-4 p-0 overflow-hidden" style="max-width: 1000px; margin: 0 auto; box-shadow: none; background: transparent;">
                        
                        <div class="setting-box p-4 border-bottom d-flex justify-content-between align-items-center mb-3" onclick="openSetting('settings-account')">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 40px; height: 40px; border-radius: 8px; background: #fee2e2; color: #ef4444; display: flex; justify-content: center; align-items: center; font-size: 18px;"><i class="fa fa-user"></i></div>
                                <div><div class="fw-bold text-dark" style="font-size: 16px;">Account Settings</div><div class="text-muted" style="font-size: 13px;">Profile details, email, and roles</div></div>
                            </div>
                            <i class="fa fa-chevron-right text-muted"></i>
                        </div>

                        <div class="setting-box p-4 d-flex justify-content-between align-items-center mb-3" onclick="openSetting('settings-preferences')">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 40px; height: 40px; border-radius: 8px; background: #f3e8ff; color: #6366f1; display: flex; justify-content: center; align-items: center; font-size: 18px;"><i class="fa fa-sliders-h"></i></div>
                                <div><div class="fw-bold text-dark" style="font-size: 16px;">Preferences</div><div class="text-muted" style="font-size: 13px;">Dark mode, Notifications</div></div>
                            </div>
                            <i class="fa fa-chevron-right text-muted"></i>
                        </div>

                    </div>
                    <div class="d-flex justify-content-center justify-content-lg-end mb-5" style="max-width: 1000px; margin: 0 auto;">
                        <button class="btn btn-danger w-100 w-lg-auto px-4 py-2 fw-bold shadow-sm rounded-pill" onclick="logout()">
                            <i class="fa fa-sign-out-alt me-2"></i> Log Out
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- VIEW 2: ACCOUNT SETTINGS --}}
        <div id="settings-account" class="fade-in" style="display: none;">
            <div class="acc-header-out">
                <button class="back-btn-ui" onclick="closeSetting('settings-account')">
                    <i class="fa fa-angle-left fs-4"></i>
                </button>
                <i class="fa fa-user-cog acc-header-icon"></i>
                <div class="acc-title-wrap">
                    <h4 class="fw-bold mb-0 text-dark" style="font-size: 20px;">Account</h4>
                    <span class="text-muted" style="font-size: 13px;">Manage your company profile and personal account information.</span>
                </div>
            </div>

            <div class="new-acc-card">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('images/PCCI-Logo.svg') }}" class="new-acc-avatar" alt="Profile">
                        <div>
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 16px;">Profile Picture</h6>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted mb-1 w-100" style="font-size: 12px;">Last Name</label>
                        <div class="position-relative">
                            <input type="text" class="new-acc-input" id="settingsLastName" value="Jesus">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted mb-1 w-100" style="font-size: 12px;">First Name</label>
                        <div class="position-relative">
                            <input type="text" class="new-acc-input" id="settingsFirstName" value="Versula">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- VIEW 4: Preferences --}}
        <div id="settings-preferences" class="fade-in" style="display: none;">
            <div class="acc-header-out">
                <button class="back-btn-ui" onclick="closeSetting('settings-preferences')">
                    <i class="fa fa-angle-left fs-4"></i>
                </button>
                <i class="fa fa-sliders-h acc-header-icon" style="font-size: 32px;"></i>
                <div class="acc-title-wrap">
                    <h4 class="fw-bold mb-0 text-dark" style="font-size: 20px;">Preferences</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="new-acc-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Dark Mode</h6>
                            </div>
                            <div class="form-check form-switch fs-4">
                                <input class="form-check-input" type="checkbox" id="darkModeSwitch" style="cursor: pointer;" onclick="toggleDarkMode()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODALS --}}
<div class="custom-modal-overlay" id="proofModal" onclick="closeProofModal(event)">
    <div class="custom-modal-card" onclick="event.stopPropagation()">
        <button class="modal-close-x" onclick="hideProofModal()">&times;</button>
        <h5 class="fw-bold mb-3"><i class="fa fa-file-invoice text-danger me-2"></i> Process Applicant Payment</h5>
        
        <div class="modal-img-wrapper mb-4" id="modalImgWrapper">
            <div id="modalSpinner" class="text-muted"><i class="fa fa-spinner fa-spin fs-2"></i><br><small>Loading Image...</small></div>
            <img id="modalImage" src="" alt="Proof of Payment" style="display: none;" onload="onImageLoad()">
        </div>

        <div>
            <label class="small text-muted fw-bold mb-2 d-block">SELECT MEMBERSHIP TYPE:</label>
            <div class="d-flex gap-3 w-100 mb-4">
                <button id="toggleBtn1" class="type-toggle-btn active-1 flex-grow-1" onclick="selectType(1)">
                    <i class="fa fa-store mb-1 fs-5"></i><br>Micro<br><small class="fw-normal">₱500.00</small>
                </button>
                <button id="toggleBtn2" class="type-toggle-btn flex-grow-1" onclick="selectType(2)">
                    <i class="fa fa-building mb-1 fs-5"></i><br>Small Enterprise<br><small class="fw-normal">₱5,000.00</small>
                </button>
            </div>
            <div class="text-end border-top pt-3">
                <button class="btn btn-success px-4 fw-bold rounded-pill shadow-sm" onclick="confirmProcessing()" style="height: 45px; background: #22c55e; border: none; width: 100%;">
                    Confirm Payment Processing
                </button>
            </div>
        </div>
    </div>
</div>

<div class="custom-modal-overlay" id="simpleProofModal" onclick="closeSimpleProofModal(event)">
    <div class="custom-modal-card" onclick="event.stopPropagation()">
        <button class="modal-close-x" onclick="hideSimpleProofModal()">&times;</button>
        <h5 class="fw-bold mb-3"><i class="fa fa-image text-primary me-2"></i> View Proof</h5>
        <div class="modal-img-wrapper">
            <div id="simpleModalSpinner" class="text-muted"><i class="fa fa-spinner fa-spin fs-2"></i><br><small>Loading</small></div>
            <img id="simpleModalImage" src="" alt="Proof of Payment" style="display: none;" onload="onSimpleImageLoad()">
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIC --}}
<script>
    const token = localStorage.getItem('token');
    let allMembersData = []; 
    let filteredMembersData = []; 
    let currentMemberPage = 1;
    const membersPerPage = 10; 
    let allApplicantsData = [];
    let filteredApplicantsData = [];
    let currentApplicantPage = 1;
    const applicantsPerPage = 10;
    let currentApplicantId = null;
    let currentSelectedType = 1;

    let membershipTypes = [
        { "id": 1, "name": "Micro", "price": "500.00", "duration_in_months": 12 },
        { "id": 2, "name": "Small Enterprises", "price": "5000.00", "duration_in_months": 12 }
    ];

    // Mobile Sidebar Toggle
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('active');
        document.body.style.overflow = document.querySelector('.sidebar').classList.contains('open') ? 'hidden' : '';
    }

    // Drops & Settings
    function toggleReportDropdown(e) { e.stopPropagation(); const menu = document.getElementById('reportDropdownMenu'); menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex'; }
    function downloadReport(type) { alert(`Initiating ${type.toUpperCase()} Report Download...`); document.getElementById('reportDropdownMenu').style.display = 'none'; }
    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        const icon = document.getElementById('darkModeIcon');
        const text = document.getElementById('darkModeText');
        const switchBtn = document.getElementById('darkModeSwitch');
        
        if (document.body.classList.contains('dark-mode')) {
            if(icon) icon.classList.replace('fa-moon', 'fa-sun');
            if(text) text.innerText = 'Light Mode';
            if(switchBtn) switchBtn.checked = true;
            localStorage.setItem('theme', 'dark');
        } else {
            if(icon) icon.classList.replace('fa-sun', 'fa-moon');
            if(text) text.innerText = 'Dark Mode';
            if(switchBtn) switchBtn.checked = false;
            localStorage.setItem('theme', 'light');
        }
    }
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        setTimeout(() => {
            const icon = document.getElementById('darkModeIcon');
            const text = document.getElementById('darkModeText');
            const switchBtn = document.getElementById('darkModeSwitch');
            if(icon) icon.classList.replace('fa-moon', 'fa-sun');
            if(text) text.innerText = 'Light Mode';
            if(switchBtn) switchBtn.checked = true;
        }, 50);
    }
    function openSetting(id) { document.getElementById('settings-main').style.display = 'none'; document.getElementById(id).style.display = 'block'; }
    function closeSetting(id) { document.getElementById(id).style.display = 'none'; document.getElementById('settings-main').style.display = 'block'; }

    function checkAuth(res) {
        if (res.status === 401) { localStorage.removeItem('token'); window.location.href = '/login'; return false; }
        return true;
    }

    function openSimpleProof(url) {
        if (!url || url === '#' || url === 'null') { alert("No proof found."); return; }
        const img = document.getElementById('simpleModalImage');
        document.getElementById('simpleModalSpinner').style.display = 'block';
        img.style.display = 'none';
        img.src = url.startsWith('http') ? url : `https://pcci-laravel-api.onrender.com/${url.replace(/^\/+/, '')}`;
        document.getElementById('simpleProofModal').style.display = 'flex';
    }
    function onSimpleImageLoad() { document.getElementById('simpleModalImage').style.display = 'block'; document.getElementById('simpleModalSpinner').style.display = 'none'; }
    function hideSimpleProofModal() { document.getElementById('simpleProofModal').style.display = 'none'; }
    function closeSimpleProofModal(e) { if (e.target.id === 'simpleProofModal') hideSimpleProofModal(); }

    function openProof(url, applicantId) {
        if (!url || url === '#' || url === 'null') { alert("No proof found."); return; }
        currentApplicantId = applicantId; 
        const img = document.getElementById('modalImage');
        document.getElementById('modalSpinner').style.display = 'block';
        img.style.display = 'none';
        img.src = url.startsWith('http') ? url : `https://pcci-laravel-api.onrender.com/${url.replace(/^\/+/, '')}`;
        selectType(1); 
        document.getElementById('proofModal').style.display = 'flex';
    }
    function onImageLoad() { document.getElementById('modalImage').style.display = 'block'; document.getElementById('modalSpinner').style.display = 'none'; }
    function hideProofModal() { document.getElementById('proofModal').style.display = 'none'; }
    function closeProofModal(e) { if (e.target.id === 'proofModal') hideProofModal(); }

    function selectType(id) {
        currentSelectedType = id; 
        document.getElementById('toggleBtn1').className = (id == 1) ? 'type-toggle-btn active-1 flex-grow-1' : 'type-toggle-btn flex-grow-1';
        document.getElementById('toggleBtn2').className = (id == 2) ? 'type-toggle-btn active-2 flex-grow-1' : 'type-toggle-btn flex-grow-1';
    }

    async function confirmProcessing() {
        const data = membershipTypes.find(m => m.id == currentSelectedType);
        if (!data || !currentApplicantId) return;

        try {
            const response = await fetch(`/treasurer/process-payment/${currentApplicantId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify({
                    membership_type_id: currentSelectedType,
                    membership_type: 'Regular'
                })
            });

            if (response.ok || response.status === 200 || response.status === 201) {
                hideProofModal();
                fetchMembers(); fetchTransactions(); fetchRecentPayments();
                alert("Success: Payment Processed!");
            } else {
                alert(`Error processing payment.`);
            }
        } catch (err) { alert("Network error."); }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (!token) { window.location.href = '/login'; return; }
        
        const storedName = localStorage.getItem('userName') || 'Jesus Versula';
        document.getElementById('sidebarName').innerText = storedName;
        const nameInput = document.getElementById('settingsLastName');
        if (nameInput) {
             const parts = storedName.split(' ');
             if(parts.length > 1) {
                 document.getElementById('settingsFirstName').value = parts[0];
                 document.getElementById('settingsLastName').value = parts.slice(1).join(' ');
             }
        }
        
        fetchApplicants(); fetchMembers(); fetchRecentPayments(); fetchTransactions(); initCharts(); 

        document.getElementById('memberSearch').addEventListener('input', applyMemberFilters);
        document.getElementById('applicantSearch').addEventListener('input', applyApplicantFilters);

        const savedTab = localStorage.getItem('activeTab') || 'dashboard';
        switchTab(savedTab, false);
    });

    function applyMemberFilters() {
        const term = document.getElementById('memberSearch').value.toLowerCase();
        filteredMembersData = allMembersData.filter(m => (m.applicant?.basic_profile?.registered_business_name || '').toLowerCase().includes(term));
        currentMemberPage = 1; displayMembersPage();
    }

    function applyApplicantFilters() {
        const term = document.getElementById('applicantSearch').value.toLowerCase();

        const memberEmails = new Set(
            (Array.isArray(allMembersData) ? allMembersData : [])
                .map(m => String(m?.applicant?.basic_profile?.email || '').trim().toLowerCase())
                .filter(Boolean)
        );
        const memberCompanyNames = new Set(
            (Array.isArray(allMembersData) ? allMembersData : [])
                .map(m => String(m?.applicant?.basic_profile?.registered_business_name || '').trim().toLowerCase())
                .filter(Boolean)
        );

        filteredApplicantsData = allApplicantsData.filter(a => {
            const companyName = String(a.basic_profile?.registered_business_name || '').trim().toLowerCase();
            const email = String(a.basic_profile?.email || '').trim().toLowerCase();
            const isMember = (email && memberEmails.has(email)) || (companyName && memberCompanyNames.has(companyName));
            if (isMember) return false;
            return companyName.includes(term);
        });

        currentApplicantPage = 1; displayApplicantsPage();
    }

    async function fetchApplicants() {
        try {
            const [res1, res2] = await Promise.all([
                fetch(`${window.API_BASE_URL}/v1/applicants?status=approved`, { headers: { 'Authorization': `Bearer ${token}` } }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=paid`, { headers: { 'Authorization': `Bearer ${token}` } })
            ]);
            let combinedData = [];
            if (res1.ok) { const data1 = await res1.json(); if (data1.data) combinedData = combinedData.concat(data1.data); }
            if (res2.ok) { const data2 = await res2.json(); if (data2.data) combinedData = combinedData.concat(data2.data); }
            allApplicantsData = combinedData;

            try {
                const membersRes = await fetch('/api/v1/members', { headers: { 'Authorization': `Bearer ${token}` } });
                if (membersRes.ok) {
                    const membersData = await membersRes.json();
                    if (Array.isArray(membersData?.data)) {
                        allMembersData = membersData.data;
                    }
                }
            } catch (_) {}

            applyApplicantFilters();
        } catch (err) {}
    }

    function displayApplicantsPage() {
        const totalPages = Math.ceil(filteredApplicantsData.length / applicantsPerPage) || 1;
        if (currentApplicantPage > totalPages) currentApplicantPage = totalPages;
        if (currentApplicantPage < 1) currentApplicantPage = 1;
        const pageData = filteredApplicantsData.slice((currentApplicantPage - 1) * applicantsPerPage, currentApplicantPage * applicantsPerPage);
        const tbody = document.getElementById('applicants-table-body');
        tbody.innerHTML = '';
        if(pageData.length === 0) tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted fw-bold">No applicants found.</td></tr>`;
        pageData.forEach(app => {
            const isPaid = String(app.status).toLowerCase() === 'paid';
            let actionButton = isPaid 
                ? `<button class="action-btn btn-gray" disabled style="width: 130px;"><i class="fa fa-check"></i> Processed</button>`
                : `<button onclick="openProof('${app.proof_of_payment_url}', ${app.id})" class="action-btn btn-green" style="width: 130px;"><i class="fa fa-image me-1"></i> Process</button>`;
            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="fw-bold text-dark">${app.basic_profile?.registered_business_name || 'N/A'}</td>
                    <td class="text-dark">${app.basic_profile?.trade_name || 'N/A'}</td>
                    <td class="text-dark text-break">${app.basic_profile?.email || 'N/A'}</td>
                    <td class="text-dark">${app.date_submitted || 'N/A'}</td>
                    <td><span class="badge ${isPaid ? 'bg-success' : 'bg-warning text-dark'}">${isPaid ? 'PAID' : 'APPROVED'}</span></td>
                    <td><span class="fw-bold text-dark">${isPaid ? '₱ Processed' : '---'}</span></td>
                    <td><span class="badge ${isPaid ? 'bg-success' : 'bg-warning text-dark'}">${isPaid ? 'PAID' : 'APPROVED'}</span></td>
                    <td>${actionButton}</td>
                </tr>
            `);
        });
        document.getElementById('applicant-pagination-text').innerText = `Page ${currentApplicantPage} of ${totalPages}`;
    }
    function prevApplicantPage() { if (currentApplicantPage > 1) { currentApplicantPage--; displayApplicantsPage(); } }
    function nextApplicantPage() { if (currentApplicantPage < Math.ceil(filteredApplicantsData.length / applicantsPerPage)) { currentApplicantPage++; displayApplicantsPage(); } }

    async function fetchMembers() {
        try {
            const response = await fetch('/api/v1/members', { headers: { 'Authorization': `Bearer ${token}` } });
            if (!checkAuth(response)) return;
            const data = await response.json();
            if (response.ok && data.data) {
                allMembersData = data.data; 
                applyMemberFilters();
                applyApplicantFilters();
            }
        } catch (err) {}
    }

    function displayMembersPage() {
        const totalPages = Math.ceil(filteredMembersData.length / membersPerPage) || 1;
        if (currentMemberPage > totalPages) currentMemberPage = totalPages;
        if (currentMemberPage < 1) currentMemberPage = 1;
        const pageData = filteredMembersData.slice((currentMemberPage - 1) * membersPerPage, currentMemberPage * membersPerPage);
        const tbody = document.getElementById('members-table-body');
        tbody.innerHTML = '';
        if(pageData.length === 0) tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 text-muted fw-bold">No members found.</td></tr>`;
        pageData.forEach(member => {
            const name = member.applicant?.basic_profile?.registered_business_name || 'N/A';
            const orNumber = `OR-${10000 + member.id}`; 
            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="fw-bold text-dark">${name}</td>
                    <td>Annual</td>
                    <td class="fw-bold text-dark">₱5,000</td>
                    <td class="text-dark">${orNumber}</td>
                    <td class="text-dark">${member.created_at ? member.created_at.split('T')[0] : 'N/A'}</td>
                    <td class="text-dark">N/A</td>
                    <td><span class="status-badge status-completed">Active</span></td>
                    <td><button class="btn btn-sm btn-link p-0 fw-bold" onclick="openSimpleProof('${member.proof_of_payment_url}')">View File</button></td>
                    <td><button class="action-btn btn-gray">Details</button></td>
                </tr>
            `);
        });
        document.getElementById('member-pagination-text').innerText = `Page ${currentMemberPage} of ${totalPages}`;
    }
    function prevMemberPage() { if (currentMemberPage > 1) { currentMemberPage--; displayMembersPage(); } }
    function nextMemberPage() { if (currentMemberPage < Math.ceil(filteredMembersData.length / membersPerPage)) { currentMemberPage++; displayMembersPage(); } }

    async function fetchRecentPayments() {
        try {
            const response = await fetch('https://pcci-laravel-api.onrender.com/api/v1/applicants?status=approved', { headers: { 'Authorization': `Bearer ${token}` } });
            if (!checkAuth(response)) return;
            const data = await response.json();
            if (response.ok && data.data) {
                const tbody = document.getElementById('recent-payments-table-body');
                tbody.innerHTML = ''; 
                data.data.forEach(app => {
                    tbody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td class="fw-bold text-dark">${app.basic_profile?.registered_business_name || 'N/A'}</td>
                            <td>Annual</td>
                            <td class="fw-bold text-dark">₱5,000</td>
                            <td class="text-dark">Pending</td>
                            <td class="text-dark">${app.date_approved || 'N/A'}</td>
                            <td><button class="action-btn btn-green" onclick="openProof('${app.proof_of_payment_url}', ${app.id})">Process</button></td>
                        </tr>
                    `);
                });
            }
        } catch (err) {}
    }

    async function fetchTransactions() {
        try {
            const data = { data: [ { applicant: { basic_profile: { registered_business_name: "Tech Innovators" } }, amount: "5000", status: "paid", created_at: "2025-01-01", or_number: "OR-10234" } ] };
            const tbodyTrans = document.getElementById('transactions-table-body');
            if(tbodyTrans) tbodyTrans.innerHTML = '';
            if (data.data) {
                data.data.forEach(txn => {
                    if(tbodyTrans) {
                        tbodyTrans.insertAdjacentHTML('beforeend', `
                            <tr>
                                <td class="fw-bold text-dark ps-4">${txn.applicant?.basic_profile?.registered_business_name || 'Unknown'}</td>
                                <td class="text-dark">Gcash</td>
                                <td class="text-dark">${txn.created_at || 'N/A'}</td>
                                <td class="text-dark">Small Enterprise</td>
                                <td class="text-dark">${txn.or_number || '---'}</td>
                                <td class="text-center"><span class="status-badge status-completed">PAID</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-light border shadow-sm action-icon-btn text-primary"><i class="fa fa-edit"></i></button>
                                </td>
                            </tr>
                        `);
                    }
                });
            }
        } catch (err) {}
    }

    function initCharts() {
        const ctxBar = document.getElementById('barChart');
        if(ctxBar) new Chart(ctxBar, { type: 'bar', data: { labels: ['21', '22'], datasets: [{ data: [120, 150], backgroundColor: '#3b82f6' }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } } });
        const reportBar = document.getElementById('reportBarChart');
        if(reportBar) new Chart(reportBar, { type: 'bar', data: { labels: ['Jan'], datasets: [{ label: 'Micro', data: [120], backgroundColor: '#3b82f6'}] }, options: { responsive: true, maintainAspectRatio: false } });
    }

    function refreshTabData(tabName) {
        if (tabName === 'dashboard') {
            if (typeof fetchApplicants === 'function') fetchApplicants();
            if (typeof fetchMembers === 'function') fetchMembers();
            if (typeof fetchRecentPayments === 'function') fetchRecentPayments();
            if (typeof fetchTransactions === 'function') fetchTransactions();
            if (typeof initCharts === 'function') initCharts();
            return;
        }

        if (tabName === 'members' && typeof fetchMembers === 'function') {
            fetchMembers();
            return;
        }

        if (tabName === 'applicants') {
            if (typeof fetchApplicants === 'function') fetchApplicants();
            if (typeof fetchRecentPayments === 'function') fetchRecentPayments();
            return;
        }

        if (tabName === 'transactions' && typeof fetchTransactions === 'function') {
            fetchTransactions();
            return;
        }

        if (tabName === 'reports') {
            if (typeof fetchApplicants === 'function') fetchApplicants();
            if (typeof fetchMembers === 'function') fetchMembers();
            if (typeof fetchTransactions === 'function') fetchTransactions();
            if (typeof initCharts === 'function') initCharts();
        }
    }

    function switchTab(tabName, shouldReload = true) {
        localStorage.setItem('activeTab', tabName); 
        document.querySelectorAll('.content-section').forEach(s => s.style.display = 'none');
        document.querySelectorAll('.sidebar-menu li').forEach(li => li.classList.remove('active'));
        document.getElementById('section-' + tabName).style.display = 'block';
        document.getElementById('nav-' + tabName).classList.add('active');
        
        if (window.innerWidth <= 992) {
            document.querySelector('.sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
            document.body.style.overflow = '';
        }

        refreshTabData(tabName);

        if (shouldReload) {
            window.location.reload();
        }
    }
</script>
@endsection