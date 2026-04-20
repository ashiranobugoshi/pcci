@extends('layouts.app')

@section('title', 'Member Dashboard - PCCI')

@section('content')
@include('partials.api-config')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
header, footer, .navbar, nav { display: none !important; }

:root {
    --member-bg: #f3f4f6;
    --member-surface: #ffffff;
    --member-surface-soft: #f9fafb;
    --member-border: #e5e7eb;
    --member-text: #111827;
    --member-muted: #6b7280;
}

body.dark-mode {
    --member-bg: #0b1220;
    --member-surface: #111827;
    --member-surface-soft: #1f2937;
    --member-border: #374151;
    --member-text: #dbe4ef;
    --member-muted: #93a3b8;
}

/* Allow natural scrolling again */
html, body {
    margin: 0;
    padding: 0;
    background: var(--member-bg);
    color: var(--member-text);
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
    background: var(--member-surface);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    border-bottom: 1px solid var(--member-border);
    z-index: 1050;
}

.topbar-brand { font-size: 18px; font-weight: bold; color: #b61b2a; width: 240px; display: flex; align-items: center; gap: 8px; }
.topbar-search-wrapper { width: 35%; position: relative; }
.topbar-search-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 13px; }
.topbar-search { width: 100%; padding: 6px 15px 6px 35px; border-radius: 50rem; border: 1px solid var(--member-border); background: var(--member-surface-soft); font-size: 13px; color: var(--member-text); }
.topbar-actions { display: flex; align-items: center; gap: 15px; }
.topbar-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb; }

.theme-toggle-btn {
    border: 1px solid var(--member-border);
    background: var(--member-surface-soft);
    color: var(--member-text);
    border-radius: 999px;
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

/* =========================================
   NOTIFICATION PANEL CSS
   ========================================= */
.notification-panel {
    position: fixed;
    top: 55px; 
    right: 60px;
    width: 320px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    display: none;
    flex-direction: column;
    z-index: 1100;
    overflow: hidden;
}
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
    position: fixed;
    top: 60px; 
    left: 0;
    width: 250px; 
    height: calc(100vh - 60px); 
    background: var(--member-surface);
    border-right: 1px solid var(--member-border);
    display: flex;
    flex-direction: column;
    z-index: 1000;
    overflow-y: auto;
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
   3. MAIN CONTENT AREA (SCROLLABLE)
   ========================================= */
.main { 
    margin-top: 60px; 
    margin-left: 250px; 
    padding: 30px; 
    min-height: calc(100vh - 60px); 
}

.content-section { display: none; padding-bottom: 40px; }

/* Dashboard Cards */
.custom-card { background: var(--member-surface); border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid var(--member-border); padding: 20px; display: flex; flex-direction: column; margin-bottom: 20px;}

/* Dashboard Specifics */
.stat-box { padding: 20px; border-radius: 16px; color: white; text-align: center; height: 100%; display: flex; flex-direction: column; justify-content: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
.stat-orange { background: #f97316; }
.stat-blue { background: #3b82f6; }
.stat-teal { background: #ccfbf1; color: #0f766e; }

.active-badge { background: #dcfce7; color: #15803d; padding: 6px 16px; border-radius: 50rem; font-size: 12px; font-weight: bold; display: flex; align-items: center; gap: 6px; }
.pill-card { background: var(--member-surface); border-radius: 50rem; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid var(--member-border); margin-bottom: 12px; }
.carousel-dots { display: flex; justify-content: center; gap: 6px; margin-top: auto; padding-top: 15px;}
.carousel-dot { width: 8px; height: 8px; border-radius: 50%; background: #d1d5db; }
.carousel-dot.active { background: #b61b2a; }

/* Restored Tab Styles */
.titleBox { background: #b61b2a; color: white; padding: 20px 25px; border-radius: 12px; font-size: 20px; display: flex; align-items: center; gap: 10px; margin-bottom: 25px; font-weight: bold;}
.editBtn { border: 1px solid #b61b2a; padding: 8px 16px; border-radius: 50rem; color: #b61b2a; background: white; cursor: pointer; font-weight: bold; font-size: 13px; transition: 0.2s; }
.editBtn:hover { background: #fdf0f1; }
.contactItem { margin-bottom: 12px; color: #444; font-size: 14px; display: flex; align-items: center; gap: 10px;}
.contactItem i { color: #888; width: 20px; text-align: center; }
.doc-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee; }
.doc-item:last-child { border-bottom: none; }
.viewBtn { border: 1px solid #ccc; padding: 5px 15px; border-radius: 6px; background: white; cursor: pointer; font-size: 12px; font-weight: bold;}

.tableTop { display: flex; justify-content: space-between; margin-bottom: 20px; }
.tableTop input { padding: 8px 16px; border: 1px solid #ccc; border-radius: 50rem; width: 300px; font-size: 14px;}
.addBtn { background: #b71c2b; color: white; border: none; padding: 8px 20px; border-radius: 50rem; cursor: pointer; font-size: 14px; font-weight:bold; }
.custom-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.custom-table th { background: #f9fafb; padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb; color: #4b5563;}
.custom-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; vertical-align: middle;}

.pricing-cards { display: flex; justify-content: center; gap: 30px; margin-top: 20px; flex-wrap: wrap;}
.pricing-card { width: 340px; background: #fff; padding: 30px; border: 2px solid #b00020; border-radius: 20px; box-shadow: 0 6px 10px -2px rgba(0,0,0,0.05); display: flex; flex-direction: column; }
.pricing-card h2 { text-align: center; font-size: 22px; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; font-weight: bold;}
.pricing-card ul { margin-top: 15px; list-style: none; padding: 0; flex-grow: 1; font-size: 14px; color: #4b5563;}
.pricing-card ul li { margin-bottom: 12px; display: flex; gap: 10px;}
.pricing-price { margin-top: 20px; padding: 12px; border-radius: 10px; font-size: 18px; font-weight: bold; text-align: center; border: 2px solid #b00020; color: #b00020;}
.pricing-price.red { background: #b00020; color: #fff; border: none; }

.setting-box { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; padding: 16px 20px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: 0.2s;}
.setting-box:hover { background: #f9fafb; }
.setting-left { display: flex; align-items: center; gap: 15px; font-size: 16px; font-weight: bold; color: #333; }
.logout-btn { background: #b00020; color: white; border: none; padding: 12px 30px; border-radius: 50rem; font-weight: bold; font-size: 15px; margin-top: 20px; align-self: flex-end;}

/* Modals */
.modal-overlay { display: none; position: fixed; top: 60px; left: 250px; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 1200; justify-content: center; align-items: center; padding: 40px 30px; overflow: hidden; }
.modal-content-box { background: white; padding: 30px; border-radius: 20px; width: 100%; max-width: 450px; max-height: calc(100vh - 200px); overflow-y: auto; }
#editProfileModal .modal-content-box { max-width: 900px !important; border-radius: 24px; }

/* Document View Modal */
.doc-view-modal {
    width: 100%;
    max-width: 840px;
    max-height: 92vh;
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 18px 48px rgba(0, 0, 0, 0.22);
}

.doc-view-header {
    padding: 16px 18px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.doc-view-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}

.doc-view-close {
    border: 1px solid #e5e7eb;
    background: #fff;
    width: 34px;
    height: 34px;
    border-radius: 8px;
    color: #6b7280;
    cursor: pointer;
}

.doc-view-close:hover {
    background: #f9fafb;
    color: #111827;
}

.doc-view-body {
    padding: 14px;
    background: #f8fafc;
    min-height: 360px;
    max-height: 62vh;
    overflow: auto;
}

.doc-preview-box {
    width: 100%;
    min-height: 320px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

#docViewModal,
#editProfileModal,
#docViewModal .doc-view-body,
#editProfileModal .modal-content-box {
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}

#docViewModal::-webkit-scrollbar,
#editProfileModal::-webkit-scrollbar,
#docViewModal .doc-view-body::-webkit-scrollbar,
#editProfileModal .modal-content-box::-webkit-scrollbar {
    width: 0 !important;
    height: 0 !important;
    display: none !important;
    background: transparent;
}

.doc-preview-box iframe {
    width: 100%;
    height: 62vh;
    border: 0;
}

.doc-preview-image {
    max-width: 100%;
    max-height: 62vh;
    object-fit: contain;
    display: none;
}

.doc-preview-empty {
    color: #6b7280;
    text-align: center;
    padding: 40px 20px;
    display: none;
}

.doc-view-footer {
    padding: 12px 16px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    background: #fff;
}

.doc-btn {
    border-radius: 10px;
    padding: 9px 14px;
    font-size: 13px;
    font-weight: 700;
    border: 1px solid #d1d5db;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.doc-btn-secondary {
    background: #fff;
    color: #374151;
}

.doc-btn-primary {
    background: #b61b2a;
    color: #fff;
    border-color: #b61b2a;
}

.doc-btn-primary:hover {
    background: #9c1624;
    border-color: #9c1624;
}

body.dark-mode .text-dark,
body.dark-mode .fw-bold {
    color: var(--member-text) !important;
}

body.dark-mode .text-muted {
    color: var(--member-muted) !important;
}

body.dark-mode .custom-table th {
    background: var(--member-surface-soft);
    border-bottom-color: var(--member-border);
}

body.dark-mode .custom-table td,
body.dark-mode .doc-item,
body.dark-mode .sidebar-profile,
body.dark-mode .tableTop input,
body.dark-mode .setting-box,
body.dark-mode .modal-content-box,
body.dark-mode .doc-view-modal,
body.dark-mode .notification-panel {
    background: var(--member-surface);
    border-color: var(--member-border) !important;
    color: var(--member-text);
}

/* Modal readability polish for dark mode */
body.dark-mode .modal-content-box,
body.dark-mode .doc-view-header,
body.dark-mode .doc-view-body,
body.dark-mode .doc-view-footer,
body.dark-mode #settingsModalAccount .modal-content-box,
body.dark-mode #settingsModalSecurity .modal-content-box,
body.dark-mode #settingsModalBilling .modal-content-box,
body.dark-mode #settingsModalPreferences .modal-content-box,
body.dark-mode #memberOtpModal .modal-content-box,
body.dark-mode #memberResetPasswordModal .modal-content-box,
body.dark-mode #cropPhotoModal .modal-content-box {
    background: #121b2a !important;
    color: var(--member-text) !important;
    border-color: var(--member-border) !important;
}

body.dark-mode .modal-content-box input,
body.dark-mode .modal-content-box select,
body.dark-mode .modal-content-box textarea,
body.dark-mode .modal-content-box div[contenteditable="true"],
body.dark-mode #settingsModalAccount .form-control,
body.dark-mode #settingsModalSecurity .form-control,
body.dark-mode #settingsModalBilling .form-control,
body.dark-mode #settingsModalPreferences .form-control,
body.dark-mode #section-settings .form-control {
    background: #0f172a !important;
    color: #dbe4ef !important;
    border: 1px solid #334155 !important;
}

body.dark-mode .modal-content-box input::placeholder,
body.dark-mode .modal-content-box textarea::placeholder {
    color: #8ea0b6 !important;
}

body.dark-mode .modal-content-box small,
body.dark-mode .modal-content-box label,
body.dark-mode .modal-content-box .text-muted,
body.dark-mode .modal-content-box th,
body.dark-mode .modal-content-box td {
    color: #a9b8ca !important;
}

body.dark-mode .modal-content-box .btn-light,
body.dark-mode .modal-content-box .btn-close,
body.dark-mode .doc-view-close,
body.dark-mode .doc-btn-secondary,
body.dark-mode .viewBtn {
    background: #1e293b !important;
    color: #dbe4ef !important;
    border-color: #3b4a60 !important;
}

body.dark-mode .doc-preview-box,
body.dark-mode #docPreviewEmpty {
    background: #0f172a !important;
    color: #9fb0c5 !important;
    border-color: #334155 !important;
}

/* Specific fix: Edit Business Profile modal in dark mode */
body.dark-mode #editProfileModal .modal-content-box {
    background: #0f172a !important;
    color: #dbe4ef !important;
}

body.dark-mode #editProfileModal .modal-content-box > div:first-child {
    background: #111827 !important;
    border-bottom-color: #334155 !important;
}

body.dark-mode #editProfileModal .modal-content-box > div:first-child h5,
body.dark-mode #editProfileModal .modal-content-box h4,
body.dark-mode #editProfileModal .modal-content-box h6,
body.dark-mode #editProfileModal .modal-content-box p,
body.dark-mode #editProfileModal .modal-content-box span,
body.dark-mode #editProfileModal .modal-content-box small {
    color: #dbe4ef !important;
}

body.dark-mode #editProfileModal .modal-content-box div[style*="background: #f9fafb"] {
    background: #0f172a !important;
    border-color: #334155 !important;
    color: #dbe4ef !important;
}

body.dark-mode #editProfileModal .modal-content-box div[style*="background: white"],
body.dark-mode #editProfileModal .modal-content-box button[style*="background: white"] {
    background: #1e293b !important;
    border-color: #3b4a60 !important;
    color: #dbe4ef !important;
}

body.dark-mode #editProfileModal .modal-content-box input,
body.dark-mode #editProfileModal .modal-content-box select,
body.dark-mode #editProfileModal .modal-content-box textarea {
    background: #0b1324 !important;
    color: #dbe4ef !important;
    border-color: #334155 !important;
}

body.dark-mode #editProfileModal .modal-content-box input::placeholder,
body.dark-mode #editProfileModal .modal-content-box textarea::placeholder {
    color: #8ea0b6 !important;
}

/* Specific fix: Add Product modal in dark mode */
body.dark-mode #addProductModal .modal-content-box {
    background: #0f172a !important;
    color: #dbe4ef !important;
}

body.dark-mode #addProductModal .modal-content-box > div:first-child {
    background: #111827 !important;
    border-bottom-color: #334155 !important;
}

body.dark-mode #addProductModal .modal-content-box > div:first-child h5,
body.dark-mode #addProductModal .modal-content-box label,
body.dark-mode #addProductModal .modal-content-box .text-muted,
body.dark-mode #addProductModal .modal-content-box p,
body.dark-mode #addProductModal .modal-content-box span {
    color: #dbe4ef !important;
}

body.dark-mode #addProductModal .modal-content-box input,
body.dark-mode #addProductModal .modal-content-box select,
body.dark-mode #addProductModal .modal-content-box textarea {
    background: #0b1324 !important;
    color: #dbe4ef !important;
    border-color: #334155 !important;
}

body.dark-mode #addProductModal .modal-content-box input::placeholder,
body.dark-mode #addProductModal .modal-content-box textarea::placeholder {
    color: #8ea0b6 !important;
}

body.dark-mode #addProductModal .btn-close {
    filter: invert(1) grayscale(100%);
    opacity: 0.9;
}

body.dark-mode #addProductModal .btn-close:hover {
    opacity: 1;
}

/* Dark mode typography and contrast tuning */
body.dark-mode .sidebar-profile h5,
body.dark-mode .sidebar-profile p,
body.dark-mode .sidebar-profile small,
body.dark-mode .sidebar-menu li,
body.dark-mode .contactItem,
body.dark-mode .contactItem i,
body.dark-mode .doc-item,
body.dark-mode .pill-card,
body.dark-mode .custom-card {
    color: #dbe4ef !important;
}

body.dark-mode .sidebar-menu li.active {
    background: #1e293b !important;
    color: #f1f5f9 !important;
    border-left-color: #be1e38;
}

body.dark-mode .sidebar-menu li:hover:not(.active) {
    background: #172033 !important;
}

body.dark-mode .btn-light,
body.dark-mode .viewBtn,
body.dark-mode .editBtn {
    background: #1f2937 !important;
    color: #dbe4ef !important;
    border-color: #334155 !important;
}

body.dark-mode .stat-teal {
    background: #153e3a;
    color: #e6fffa;
}

body.dark-mode #liveDate {
    color: #c9f5ea !important;
}

body.dark-mode #liveTime {
    color: #7ff0d9 !important;
}

body.dark-mode .pricing-card {
    background: #111b2d;
    color: #dbe4ef;
    border-color: #be1e38;
}

body.dark-mode .pricing-card h2 {
    color: #e2e8f0;
    border-bottom-color: #475569;
}

body.dark-mode .pricing-card ul {
    color: #b8c4d6;
}

body.dark-mode .pricing-price {
    background: #0f172a;
    color: #fecdd3;
    border-color: #be1e38;
}

body.dark-mode .pricing-price.red {
    background: #be1e38;
    color: #ffffff;
}

/* Match dark-mode readability in My Business section */
body.dark-mode #section-business .custom-card,
body.dark-mode #section-business .contactItem,
body.dark-mode #section-business .contactItem i,
body.dark-mode #section-business .text-muted,
body.dark-mode #section-business h4,
body.dark-mode #section-business h5,
body.dark-mode #section-business h6,
body.dark-mode #section-business p,
body.dark-mode #section-business span,
body.dark-mode #section-business small {
    color: #dbe4ef !important;
}

body.dark-mode #section-business .border-start {
    border-left-color: #334155 !important;
}

body.dark-mode #section-business div[style*="background: #f9fafb"] {
    background: #0f172a !important;
    border-color: #334155 !important;
    color: #dbe4ef !important;
}

body.dark-mode #section-business button[style*="background: white"],
body.dark-mode #section-business button[style*="background: #ffffff"] {
    background: #1e293b !important;
    border-color: #3b4a60 !important;
    color: #dbe4ef !important;
}

/* Specific fix: Settings list + Preferences options in dark mode */
body.dark-mode #section-settings .setting-box {
    background: #0f172a !important;
    border-color: #334155 !important;
}

body.dark-mode #section-settings .setting-box:hover {
    background: #172033 !important;
}

body.dark-mode #section-settings .setting-left,
body.dark-mode #section-settings .setting-left span,
body.dark-mode #section-settings .setting-left i,
body.dark-mode #section-settings .setting-box .fa-chevron-right {
    color: #dbe4ef !important;
}

body.dark-mode #settingsModalPreferences h6,
body.dark-mode #settingsModalPreferences p,
body.dark-mode #settingsModalPreferences .text-dark,
body.dark-mode #settingsModalPreferences .text-muted,
body.dark-mode #settings-preferences h6,
body.dark-mode #settings-preferences p,
body.dark-mode #settings-preferences .text-dark,
body.dark-mode #settings-preferences .text-muted {
    color: #dbe4ef !important;
}

body.dark-mode #settingsModalPreferences .form-check-input,
body.dark-mode #settings-preferences .form-check-input {
    background-color: #0b1324 !important;
    border-color: #334155 !important;
}

body.dark-mode #settingsModalPreferences .form-check-input:checked,
body.dark-mode #settings-preferences .form-check-input:checked {
    background-color: #be1e38 !important;
    border-color: #be1e38 !important;
}

body.dark-mode #settingsModalPreferences .form-check-input:focus,
body.dark-mode #settings-preferences .form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(190, 30, 56, 0.25) !important;
}

body.dark-mode #settingsModalPreferences .dropdown-toggle,
body.dark-mode #settingsModalPreferences .dropdown-menu,
body.dark-mode #settings-preferences .dropdown-toggle,
body.dark-mode #settings-preferences .dropdown-menu {
    background: #1e293b !important;
    color: #dbe4ef !important;
    border-color: #3b4a60 !important;
}

body.dark-mode #settingsModalPreferences .dropdown-item,
body.dark-mode #settings-preferences .dropdown-item {
    color: #dbe4ef !important;
}

body.dark-mode #settingsModalPreferences .dropdown-item:hover,
body.dark-mode #settingsModalPreferences .dropdown-item:focus,
body.dark-mode #settings-preferences .dropdown-item:hover,
body.dark-mode #settings-preferences .dropdown-item:focus {
    background: #334155 !important;
    color: #f8fafc !important;
}

@media (max-width: 992px) {
    .topbar {
        padding: 0 12px;
        gap: 10px;
    }

    .topbar-search-wrapper {
        width: 42%;
    }

    .sidebar {
        position: static;
        width: 100%;
        height: auto;
        margin-top: 60px;
        border-right: none;
        border-bottom: 1px solid var(--member-border);
    }

    .sidebar-profile {
        display: none;
    }

    .sidebar-menu {
        display: flex;
        overflow-x: auto;
        gap: 8px;
        padding: 10px 12px;
        white-space: nowrap;
    }

    .sidebar-menu li {
        margin-bottom: 0;
        flex: 0 0 auto;
    }

    .sidebar-divider {
        display: none;
    }

    .main {
        margin-left: 0;
        margin-top: 0;
        padding: 18px;
    }

    .modal-overlay {
        left: 0;
        top: 60px;
        padding: 16px;
    }

    .tableTop {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
    }

    .tableTop input {
        width: 100%;
    }

    #section-settings .setting-box {
        padding: 14px 14px;
    }

    #settingsModalPreferences .d-flex.justify-content-between.align-items-center.gap-3,
    #settings-preferences .d-flex.justify-content-between.align-items-center.gap-3 {
        align-items: flex-start !important;
    }
}

@media (max-width: 576px) {
    .topbar-search-wrapper {
        display: none;
    }

    .topbar-actions {
        gap: 10px;
    }

    .topbar-avatar {
        margin-left: 0 !important;
    }

    .main {
        padding: 14px;
    }

    .titleBox {
        font-size: 16px;
        padding: 14px 16px;
    }

    .custom-card,
    .modal-content-box {
        border-radius: 12px;
    }

    .doc-view-modal {
        max-height: 94vh;
        border-radius: 12px;
    }

    .doc-view-body {
        min-height: 280px;
    }

    .doc-preview-box iframe,
    .doc-preview-image {
        max-height: 50vh;
        height: 50vh;
    }

    .doc-view-footer {
        flex-direction: column-reverse;
    }

    .doc-btn {
        justify-content: center;
        width: 100%;
    }

    #settingsModalPreferences .dropdown,
    #settingsModalPreferences .dropdown-toggle,
    #settings-preferences .dropdown,
    #settings-preferences .dropdown-toggle {
        width: 100%;
    }

    #settingsModalPreferences .d-flex.justify-content-between.align-items-center.gap-3,
    #settings-preferences .d-flex.justify-content-between.align-items-center.gap-3 {
        flex-direction: column;
        gap: 12px;
    }

    #settingsModalPreferences .form-check.form-switch,
    #settings-preferences .form-check.form-switch {
        align-self: flex-start;
    }
}
</style>

<div class="topbar">
    <a href="{{ url('/') }}" class="d-flex align-items-center gap-3 text-decoration-none" style="outline: none; box-shadow: none;">
        <div class="rounded-circle overflow-hidden" style="width: 40px; height: 40px;">
            <img src="{{ asset('images/PCCI-Logo.svg') }}" alt="PCCI Logo" class="w-100 h-100 object-fit-contain">
        </div>

        <div class="d-flex flex-column">
            <span class="fw-bold text-dark" style="font-family: 'Poppins', sans-serif; font-size: 1rem; line-height: 1.2;">
                PCCI - Valenzuela
            </span>
            <span class="d-none d-sm-block text-muted" style="font-family: 'DM Sans', sans-serif; font-size: 0.7rem;">
                Philippine Chamber of Commerce and Industry
            </span>
        </div>
    </a>
    
    <div class="topbar-search-wrapper">
        <i class="fa fa-search"></i>
        <input type="text" class="topbar-search" placeholder="Search dashboard...">
    </div>

    <div class="topbar-actions">
        <button class="btn btn-sm border d-flex align-items-center gap-2 text-muted fw-bold px-3 py-1" onclick="toggleDarkMode()" id="darkModeBtn" style="border-radius: 50rem; background: #f9fafb; font-size: 13px;">
            <i class="fa fa-moon" id="darkModeIcon"></i> <span id="darkModeText">Dark Mode</span>
        </button>
        <div class="position-relative" onclick="toggleNotificationPanel(event)" style="cursor:pointer; display: flex; align-items: center;">
            <i class="fa fa-bell fs-5 text-muted"></i>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="margin-top: 4px; margin-left: -5px;"></span>
        </div>
        <img src="{{ asset('images/PCCI-Logo.svg') }}" class="topbar-avatar ms-3" id="topbarAvatar" alt="User">
    </div>
</div>

<div class="notification-panel" id="notificationPanel">
    <div class="notif-header">
        <h6 class="notif-header-title">Your Notification <span class="notif-badge" id="notifBadgeCount">0 New</span></h6>
        <button class="notif-clear-btn" onclick="clearNotifications(event)"><i class="fa fa-times"></i> Close</button>
    </div>
    <div class="notif-body">
        <div class="notif-item notif-read">
            <div class="notif-icon"><i class="fa fa-bell text-danger fs-5"></i></div>
            <div class="notif-text-content">
                <p id="notifPrimaryText">No new notifications.</p>
                <small id="notifPrimaryDate">Just now</small>
            </div>
        </div>
    </div>
    <div class="notif-footer" onclick="markAllRead(event)"><i class="fa fa-sync-alt"></i> Mark All as Read</div>
</div>

<div class="sidebar">
    <div class="sidebar-profile">
        <img src="{{ asset('images/PCCI-Logo.svg') }}" id="sidebarImage" alt="Profile">
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

        <div class="row mb-4 g-4">
            <div class="col-md-3">
                <div class="stat-box stat-orange">
                    <h2 class="fw-bold mb-0" id="dashProductsCount">--</h2>
                    <p class="mb-0" style="font-size: 14px;">Products/Services</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box stat-blue">
                    <h2 class="fw-bold mb-0" id="dashEventsCount">--</h2>
                    <p class="mb-0" style="font-size: 14px;">Upcoming Events</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-box stat-teal text-start px-4" style="align-items: flex-start; justify-content: center;">
                    <h5 id="liveDate" class="fw-bold mb-1 text-dark">Loading Date...</h5>
                    <h2 id="liveTime" class="fw-bold mb-0" style="color: #0f766e;">Loading Time...</h2>
                </div>
            </div>
        </div>

        <div class="row mb-4 g-4">
            <div class="col-md-6">
                <div class="custom-card">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('images/PCCI-Logo.svg') }}" id="dashBizImage" style="width: 60px; height: 60px; border-radius: 50%; border: 1px solid #ddd; padding: 2px; object-fit: cover;">
                            <div>
                                <h5 class="fw-bold mb-0" id="dashBizName">Loading...</h5>
                                <small class="text-muted" id="dashBizEmail">Loading...</small>
                            </div>
                        </div>
                        <button class="btn btn-light text-danger rounded-pill px-3 fw-bold" onclick="switchTab('business')" style="border: 1px solid #e5e7eb; font-size: 13px;">
                            <i class="fa fa-pen me-1"></i> Edit
                        </button>
                    </div>
                    <div style="font-size: 14px;">
                        <p class="mb-2"><strong class="text-muted d-inline-block" style="width: 140px;">Official Receipt No:</strong> <span class="text-dark fw-bold" id="dashOfficialReceiptNo">N/A</span></p>
                        <p class="mb-2"><strong class="text-muted d-inline-block" style="width: 140px;">Business Type:</strong> <span class="text-dark fw-bold" id="dashBizType">Loading...</span></p>
                        <p class="mb-0"><strong class="text-muted d-inline-block" style="width: 140px;">Ownership Type:</strong> <span class="text-dark fw-bold" id="dashOwnershipType">Not specified</span></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="custom-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Membership Overview</h5>
                        <div class="active-badge"><i class="fa fa-check-circle"></i> <span id="dashMembershipStatus">Loading...</span></div>
                    </div>
                    <div style="font-size: 14px;" class="mb-4 flex-grow-1">
                        <p class="mb-2"><strong class="text-muted d-inline-block" style="width: 140px;">Member ID:</strong> <span class="text-dark fw-bold" id="dashMembershipID">...</span></p>
                        <p class="mb-2"><strong class="text-muted d-inline-block" style="width: 140px;">Receipt No:</strong> <span class="text-dark fw-bold" id="dashReceiptNo">N/A</span></p>
                        <p class="mb-0"><strong class="text-muted d-inline-block" style="width: 140px;">Membership Type:</strong> <span class="text-dark fw-bold" id="dashMembershipType">Loading...</span></p>
                    </div>
                    <button class="btn btn-danger w-100 rounded-pill py-2 fw-bold mt-auto" onclick="switchTab('membership')">Renew Membership</button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                    <h5 class="fw-bold mb-0 text-dark">Recent Products/Services</h5>
                    <button class="btn btn-link text-danger p-0 text-decoration-none fw-bold" style="font-size: 13px;" onclick="switchTab('products')">View All <i class="fa fa-arrow-right ms-1"></i></button>
                </div>

                <div id="recentProductsList">
                    <div class="pill-card mb-0">
                        <b class="text-dark mb-0" style="font-size: 14px;">Loading products...</b>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="custom-card p-3">
                    <img src="{{ asset('images/PCCI-Logo.svg') }}" 
                         class="w-100 rounded-3 mb-3" style="height: 150px; object-fit: cover;">
                    <div class="px-2">
                        <h5 class="fw-bold text-dark mb-2" id="dashEventTitle">No upcoming events</h5>
                        <div class="d-flex justify-content-between text-muted" style="font-size: 13px;">
                            <span><i class="fa fa-calendar text-danger me-1"></i> <span id="dashEventDate">To be announced</span></span>
                            <span><i class="fa fa-map-marker-alt text-danger me-1"></i> <span id="dashEventLocation">-</span></span>
                        </div>
                    </div>
                    <div class="carousel-dots">
                        <div class="carousel-dot active"></div>
                        <div class="carousel-dot"></div>
                        <div class="carousel-dot"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MY BUSINESS TAB --}}
    <div id="section-business" class="content-section" style="display: none;">
        <div class="titleBox"><i class="fa fa-briefcase"></i> My Business</div>
        
        <div class="custom-card">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div class="d-flex align-items-center gap-4">
                    <img src="{{ asset('images/PCCI-Logo.svg') }}" id="bizMainImage" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 1px solid #e5e7eb; padding: 4px;">
                    <div>
                        <h4 class="fw-bold mb-1" id="bizNameTitle">Loading...</h4>
                        <div class="text-muted" style="font-size: 15px;" id="bizIndustryTitle">Loading Industry...</div>
                    </div>
                </div>
                <button class="editBtn" onclick="openEditProfileModal()"><i class="fa fa-pen me-1"></i> Edit Profile</button>
            </div>

            <div class="row mt-2">
                <div class="col-md-7">
                    <div class="contactItem"><i class="fa fa-envelope"></i> <span id="bizEmailText">loading...</span></div>
                    <div class="contactItem"><i class="fa fa-phone"></i> <span id="bizPhoneText">loading...</span></div>
                    <div class="contactItem"><i class="fa fa-location-dot"></i> <span id="bizAddressText">loading...</span></div>
                </div>
                <div class="col-md-5 border-start ps-4">
                    <h6 class="fw-bold mb-2">Membership Details</h6>
                    <p class="text-muted mb-0" style="font-size: 13px;">Expires Date</p>
                    <p class="fw-bold mb-3" style="font-size: 14px;" id="bizExpiryText">Loading...</p>
                    <p class="text-muted mb-0" style="font-size: 13px;">Membership Type</p>
                    <p class="fw-bold mb-0" style="font-size: 14px;" id="bizMembershipTypeText">Loading...</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-7">
                <div class="custom-card h-100">
                    <h5 class="fw-bold mb-3">Business Description</h5>
                    <p class="text-muted mb-0" style="line-height: 1.6; font-size: 14px;" id="bizDescriptionText">
                        Your business profile description will appear here. This area highlights your high-quality products, services, and core operations to other members of the PCCI network.
                    </p>
                </div>
            </div>
            <div class="col-md-5">
                <div class="custom-card mb-4">
                    <h5 class="fw-bold mb-3">Representative</h5>
                    <p class="fw-bold mb-0" style="font-size: 15px;" id="repNameText">Loading...</p>
                    <p class="text-muted mb-3" style="font-size: 13px;" id="repDesignationText">Loading Designation...</p>
                    <div class="contactItem mb-2"><i class="fa fa-envelope"></i> <span id="repEmailText">...</span></div>
                    <div class="contactItem mb-0"><i class="fa fa-phone"></i> <span id="repPhoneText">...</span></div>
                </div>
                <div class="custom-card">
                    <h5 class="fw-bold mb-3">Documents</h5>
                    <div class="doc-item">
                        <span class="text-success fw-bold" style="font-size: 14px;"><i class="fa fa-check me-2"></i> Mayor Permit</span>
                        <button class="viewBtn" id="btnViewMayors" onclick="viewDocument('mayors')">View</button>
                    </div>
                    <div class="doc-item">
                        <span class="text-success fw-bold" style="font-size: 14px;"><i class="fa fa-check me-2"></i> DTI / SEC</span>
                        <button class="viewBtn" id="btnViewDTI" onclick="viewDocument('dti')">View</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- REFACTORED INCLUDES --}}
    @include('member.products')
    @include('member.membership')
    @include('member.settings')

</div>

<div class="modal-overlay" id="docViewModal" onclick="handleDocOverlay(event)">
    <div class="doc-view-modal">
        <div class="doc-view-header">
            <h5 class="doc-view-title" id="docViewTitle"><i class="fa fa-file-lines text-danger"></i> Document Preview</h5>
            <button class="doc-view-close" onclick="closeDocModal()"><i class="fa fa-xmark"></i></button>
        </div>

        <div class="doc-view-body">
            <div class="doc-preview-box" id="docPreviewBox">
                <img id="docPreviewImage" class="doc-preview-image" alt="Document preview">
                <iframe id="docPreviewFrame" title="Document preview" style="display:none;"></iframe>
                <div class="doc-preview-empty" id="docPreviewEmpty">
                    <i class="fa fa-file-circle-xmark mb-2 d-block" style="font-size: 28px;"></i>
                    No file available for this document yet.
                </div>
            </div>
        </div>

        <div class="doc-view-footer">
            <button class="doc-btn doc-btn-secondary" onclick="closeDocModal()"><i class="fa fa-xmark"></i> Close</button>
            <a id="docOpenLink" class="doc-btn doc-btn-primary" href="#" target="_blank" rel="noopener noreferrer"><i class="fa fa-arrow-up-right-from-square"></i> Open Full File</a>
        </div>
    </div>
</div>

<div class="modal-overlay" id="editProfileModal" onclick="handleEditProfileOverlay(event)">
    <div class="modal-content-box" style="max-width: 900px; max-height: 90vh; overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none; padding: 0;">
        <!-- Modal Header -->
        <div style="padding: 20px 25px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: #f9fafb;">
            <h5 class="fw-bold mb-0 text-dark">Edit Business Profile</h5>
            <button class="btn-close" onclick="closeEditProfileModal()"></button>
        </div>

        <!-- Modal Body -->
        <div style="padding: 25px;">
            <div id="profileAlert" class="alert alert-danger" style="display: none; font-size: 13px;"></div>
            
            <!-- Company Header Section -->
            <div style="display: flex; gap: 20px; margin-bottom: 30px; padding-bottom: 25px; border-bottom: 1px solid #e5e7eb; align-items: flex-start;">
                <!-- Logo -->
                <img id="ep_companyImage" src="{{ asset('images/PCCI-Logo.svg') }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb; flex-shrink: 0;">
                
                <!-- Company Info -->
                <div style="flex-grow: 1;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <h4 class="fw-bold mb-0 text-dark" id="ep_companyNameDisplay">Loading...</h4>
                        <span class="badge rounded-pill" style="background: #dcfce7; color: #15803d; font-size: 11px; font-weight: bold;"><i class="fa fa-check-circle me-1"></i>Active</span>
                    </div>
                    <p class="mb-0 text-muted" style="font-size: 13px;" id="ep_companyTypeDisplay">Loading...</p>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="row g-4">
                <!-- Left Column: Business Information -->
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3 text-dark" style="font-size: 14px;">Business information</h6>
                    
                    <div class="mb-3" style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                        <div style="flex-grow: 1;">
                            <small class="text-muted fw-bold d-block mb-1" style="font-size: 12px;">REGISTERED NAME</small>
                            <div style="padding: 8px 12px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 14px;" id="ep_companyNameDisplay2">Loading...</div>
                        </div>
                        <button class="btn btn-sm" style="border: 1px solid #e5e7eb; padding: 6px 12px; border-radius: 6px; font-size: 12px; background: white; cursor: pointer;" onclick="editField('companyName')"><i class="fa fa-pen"></i> Edit</button>
                    </div>

                    <div class="mb-3" style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                        <div style="flex-grow: 1;">
                            <small class="text-muted fw-bold d-block mb-1" style="font-size: 12px;">BUSINESS TYPE</small>
                            <select id="ep_businessType" class="form-control form-control-sm" style="padding: 8px 12px; font-size: 14px; background-color: #ffffff; color: #333; border: 1px solid #e5e7eb;">
                                <option value="">Select Business Type</option>
                                <option value="Manufacturing">Manufacturing</option>
                                <option value="Retail">Retail</option>
                                <option value="Services">Services</option>
                            </select>
                        </div>
                        <button class="btn btn-sm" style="border: 1px solid #e5e7eb; padding: 6px 12px; border-radius: 6px; font-size: 12px; background: white; cursor: pointer; margin-top: 18px;" onclick="editField('businessType')"><i class="fa fa-pen"></i> Edit</button>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted fw-bold d-block mb-1" style="font-size: 12px;">BUSINESS DESCRIPTION</small>
                        <textarea id="ep_description" class="form-control form-control-sm" style="padding: 8px 12px; font-size: 14px; resize: vertical; height: 100px; font-family: inherit; background-color: #ffffff; color: #333; border: 1px solid #e5e7eb;" placeholder="Enter business description"></textarea>
                        <small class="text-muted d-block text-end mt-1">0/200</small>
                    </div>

                    <div class="mb-3" style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                        <div style="flex-grow: 1;">
                            <small class="text-muted fw-bold d-block mb-1" style="font-size: 12px;">REPRESENTATIVE NAME</small>
                            <div style="padding: 8px 12px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 14px;" id="ep_repNameDisplay">Loading...</div>
                        </div>
                        <button class="btn btn-sm" style="border: 1px solid #e5e7eb; padding: 6px 12px; border-radius: 6px; font-size: 12px; background: white; cursor: pointer;" onclick="editField('repName')"><i class="fa fa-pen"></i> Edit</button>
                    </div>

                    <div class="mb-3" style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                        <div style="flex-grow: 1;">
                            <small class="text-muted fw-bold d-block mb-1" style="font-size: 12px;">REPRESENTATIVE POSITION</small>
                            <div style="padding: 8px 12px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 14px;" id="ep_repPositionDisplay">Loading...</div>
                        </div>
                        <button class="btn btn-sm" style="border: 1px solid #e5e7eb; padding: 6px 12px; border-radius: 6px; font-size: 12px; background: white; cursor: pointer;" onclick="editField('repPosition')"><i class="fa fa-pen"></i> Edit</button>
                    </div>

                    <div class="mb-3" style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                        <div style="flex-grow: 1;">
                            <small class="text-muted fw-bold d-block mb-1" style="font-size: 12px;">CONTACT NUMBER</small>
                            <div style="padding: 8px 12px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 14px;" id="ep_contactDisplay">Loading...</div>
                        </div>
                        <button class="btn btn-sm" style="border: 1px solid #e5e7eb; padding: 6px 12px; border-radius: 6px; font-size: 12px; background: white; cursor: pointer;" onclick="editField('contact')"><i class="fa fa-pen"></i> Edit</button>
                    </div>
                </div>

                <!-- Right Column: Address & Documentation -->
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3 text-dark" style="font-size: 14px;">Address</h6>
                    
                    <div class="mb-3" style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                        <div style="flex-grow: 1;">
                            <small class="text-muted fw-bold d-block mb-1" style="font-size: 12px;">ADDRESS</small>
                            <div style="padding: 8px 12px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 14px;" id="ep_addressDisplay">Loading...</div>
                        </div>
                        <button class="btn btn-sm" style="border: 1px solid #e5e7eb; padding: 6px 12px; border-radius: 6px; font-size: 12px; background: white; cursor: pointer;" onclick="editField('address')"><i class="fa fa-pen"></i> Edit</button>
                    </div>

                    <div class="mb-4" style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                        <div style="flex-grow: 1;">
                            <small class="text-muted fw-bold d-block mb-1" style="font-size: 12px;">URL</small>
                            <div style="padding: 8px 12px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 14px; word-break: break-all;" id="ep_urlDisplay">Loading...</div>
                        </div>
                        <button class="btn btn-sm" style="border: 1px solid #e5e7eb; padding: 6px 12px; border-radius: 6px; font-size: 12px; background: white; cursor: pointer;" onclick="editField('url')"><i class="fa fa-pen"></i> Edit</button>
                    </div>

                    <h6 class="fw-bold mb-3 text-dark" style="font-size: 14px;">Business Documentation</h6>
                    
                    <div class="mb-2" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                        <span style="font-size: 14px;">Mayor Permit</span>
                        <button class="btn btn-sm" style="border: 1px solid #ccc; padding: 5px 12px; border-radius: 6px; font-size: 12px; background: white; color: #666; cursor: pointer;">Update</button>
                    </div>
                    
                    <div class="mb-2" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                        <span style="font-size: 14px;">DTI Registration</span>
                        <button class="btn btn-sm" style="border: 1px solid #ccc; padding: 5px 12px; border-radius: 6px; font-size: 12px; background: white; color: #666; cursor: pointer;">Update</button>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
                        <span style="font-size: 14px;">Secretary Certification</span>
                        <button class="btn btn-sm" style="border: 1px solid #ccc; padding: 5px 12px; border-radius: 6px; font-size: 12px; background: white; color: #666; cursor: pointer;">Update</button>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button class="btn btn-light fw-bold px-4" style="border: 1px solid #e5e7eb;" onclick="closeEditProfileModal()">Cancel</button>
                <button class="btn btn-danger fw-bold px-4" id="btnSaveProfile" onclick="saveProfile()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
function handleEditProfileOverlay(event) {
    if (event.target === document.getElementById('editProfileModal')) {
        closeEditProfileModal();
    }
}

function setTextIfExists(id, value) {
    const el = document.getElementById(id);
    if (el) el.innerText = value;
}

function syncBusinessPreviewFromEditor() {
    const name = (document.getElementById('ep_companyNameDisplay2')?.innerText || '').trim();
    const type = (document.getElementById('ep_businessType')?.value || '').trim();
    const description = (document.getElementById('ep_description')?.value || '').trim();
    const repName = (document.getElementById('ep_repNameDisplay')?.innerText || '').trim();
    const repPosition = (document.getElementById('ep_repPositionDisplay')?.innerText || '').trim();
    const contact = (document.getElementById('ep_contactDisplay')?.innerText || '').trim();
    const address = (document.getElementById('ep_addressDisplay')?.innerText || '').trim();

    if (name) {
        setTextIfExists('ep_companyNameDisplay', name);
        setTextIfExists('bizNameTitle', name);
    }

    if (type) {
        setTextIfExists('ep_companyTypeDisplay', type);
        setTextIfExists('bizIndustryTitle', type);
        setTextIfExists('bizMembershipTypeText', type);
    }

    if (description) setTextIfExists('bizDescriptionText', description);
    if (repName) setTextIfExists('repNameText', repName);
    if (repPosition) setTextIfExists('repDesignationText', repPosition);
    if (contact) {
        setTextIfExists('bizPhoneText', contact);
        setTextIfExists('repPhoneText', contact);
    }
    if (address) setTextIfExists('bizAddressText', address);
}

function placeCaretAtEnd(el) {
    const range = document.createRange();
    const sel = window.getSelection();
    range.selectNodeContents(el);
    range.collapse(false);
    sel.removeAllRanges();
    sel.addRange(range);
}

function enableInlineEdit(target, allowMultiline = false) {
    if (!target) return;

    if (!target.dataset.boundInlineEdit) {
        target.addEventListener('input', syncBusinessPreviewFromEditor);
        target.addEventListener('blur', function () {
            const cleaned = (target.innerText || '').replace(/\n+/g, ' ').replace(/\s{2,}/g, ' ').trim();
            target.innerText = cleaned || target.dataset.prevValue || '';
            target.contentEditable = 'false';
            target.style.background = '#f9fafb';
            target.style.border = '1px solid #e5e7eb';
            syncBusinessPreviewFromEditor();
        });

        if (!allowMultiline) {
            target.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    target.blur();
                }
            });
        }

        target.dataset.boundInlineEdit = '1';
    }

    target.dataset.prevValue = (target.innerText || '').trim();
    target.contentEditable = 'true';
    target.style.background = '#ffffff';
    target.style.border = '1px solid #b61b2a';
    target.style.outline = 'none';
    target.focus();
    placeCaretAtEnd(target);
}

function editField(field) {
    const fieldConfig = {
        companyName: { id: 'ep_companyNameDisplay2' },
        repName: { id: 'ep_repNameDisplay' },
        repPosition: { id: 'ep_repPositionDisplay' },
        contact: { id: 'ep_contactDisplay' },
        address: { id: 'ep_addressDisplay' },
        url: { id: 'ep_urlDisplay' }
    };

    if (field === 'businessType') {
        const businessTypeSelect = document.getElementById('ep_businessType');
        if (businessTypeSelect) businessTypeSelect.focus();
        return;
    }

    if (field === 'description') {
        const descriptionInput = document.getElementById('ep_description');
        if (descriptionInput) descriptionInput.focus();
        return;
    }

    const config = fieldConfig[field];
    if (!config) return;

    const target = document.getElementById(config.id);
    if (!target) return;

    enableInlineEdit(target);
}
</script>

<script>

const token = localStorage.getItem('token');

function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    document.body.classList.toggle('member-dark');

    const icon = document.getElementById('darkModeIcon');
    const text = document.getElementById('darkModeText');

    if (document.body.classList.contains('dark-mode')) {
        if (icon) icon.classList.replace('fa-moon', 'fa-sun');
        if (text) text.innerText = 'Light Mode';
        localStorage.setItem('theme', 'dark');
    } else {
        if (icon) icon.classList.replace('fa-sun', 'fa-moon');
        if (text) text.innerText = 'Dark Mode';
        localStorage.setItem('theme', 'light');
    }
}

// ==========================================
// INITIALIZATION & AUTHENTICATION
// ==========================================
document.addEventListener('DOMContentLoaded', function(){
    if(!token) { 
        window.location.href = '/login'; 
        return; 
    }

    initMemberNotifications();

    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        document.body.classList.add('member-dark');
        setTimeout(() => {
            const icon = document.getElementById('darkModeIcon');
            const text = document.getElementById('darkModeText');
            if (icon) icon.classList.replace('fa-moon', 'fa-sun');
            if (text) text.innerText = 'Light Mode';
        }, 50);
    }

    seedUserFallbackUI();
    startLiveClock();

    const hashTab = (window.location.hash || '').replace('#', '').trim();
    const savedTab = hashTab || localStorage.getItem('activeTab') || 'dashboard';
    switchTab(savedTab, false);
});

function seedUserFallbackUI() {
    const storedName = (localStorage.getItem('userName') || '').trim();
    if (!storedName) return;

    const nameParts = storedName.split(/\s+/).filter(Boolean);
    const first = nameParts[0] || storedName;

    setTextIfExists('sidebarName', storedName);
    setTextIfExists('welcomeMessage', `Welcome, ${first}!`);
    setTextIfExists('repNameText', storedName);
}

// Update the Date and Time in the Light-Teal widget
function startLiveClock() {
    const dateEl = document.getElementById('liveDate');
    const timeEl = document.getElementById('liveTime');
    function updateTime() {
        const now = new Date();
        dateEl.innerText = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        timeEl.innerText = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    }
    updateTime(); 
    setInterval(updateTime, 1000);
}

// Tab Logic
let currentActiveTab = 'dashboard';

function refreshMemberTabData(tabName) {
    if (!token) return;

    if (tabName === 'products') {
        fetchProducts();
        return;
    }

    // Dashboard, Business, Membership, and Settings depend on profile/application payload.
    fetchRealDashboardData(token);
}

function switchTab(tabName, shouldReload = true) {
    currentActiveTab = tabName;
    localStorage.setItem('activeTab', tabName);

    // Match treasurer behavior: user tab clicks always force a same-page hard refresh.
    if (shouldReload) {
        window.location.href = `${window.location.pathname}?refresh=${Date.now()}#${tabName}`;
        return;
    }

    document.querySelectorAll('.content-section').forEach(section => section.style.display = 'none');
    document.querySelectorAll('.sidebar-menu li').forEach(li => li.classList.remove('active'));
    document.getElementById('section-' + tabName).style.display = 'block'; // Block restores natural scroll flow
    const activeNav = document.getElementById('nav-' + tabName);
    if(activeNav) activeNav.classList.add('active');

    refreshMemberTabData(tabName);
}

// Notification Logic
const MEMBER_API_BASE = (window.PCCI_API_BASE_URL || window.API_BASE_URL || '/api').replace(/\/$/, '');
const MEMBER_NOTIFICATIONS_URL = `${MEMBER_API_BASE}/v1/notifications`;
const MEMBER_NOTIF_CACHE_KEY = 'member_notifications_cache_v1';
const MEMBER_NOTIF_LAST_CHECK_KEY = 'member_notifications_last_check_date_v1';
const MEMBER_NOTIF_READ_DATE_KEY = 'member_notifications_read_date_v1';

function getTodayKey() {
    const now = new Date();
    return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
}

function getRelativeTimeText(dateValue) {
    const date = new Date(dateValue);
    if (Number.isNaN(date.getTime())) return 'Just now';

    const diffMs = Date.now() - date.getTime();
    const diffMin = Math.floor(diffMs / 60000);
    if (diffMin < 1) return 'Just now';
    if (diffMin < 60) return `${diffMin} min ago`;

    const diffHrs = Math.floor(diffMin / 60);
    if (diffHrs < 24) return `${diffHrs} hr${diffHrs > 1 ? 's' : ''} ago`;

    const diffDays = Math.floor(diffHrs / 24);
    if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;

    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function normalizeNotificationItems(payload) {
    const list = Array.isArray(payload?.data)
        ? payload.data
        : Array.isArray(payload?.notifications)
            ? payload.notifications
            : Array.isArray(payload)
                ? payload
                : [];

    return list.map((item, index) => {
        const title = item.title || item.subject || item.type || 'Notification';
        const message = item.message || item.body || item.description || 'You have a new notification.';
        const createdAt = item.created_at || item.createdAt || item.date || item.updated_at || new Date().toISOString();
        const severity = String(item.severity || item.level || item.status || '').toLowerCase();

        let iconClass = 'fa-bell';
        let toneClass = 'text-primary';
        if (severity.includes('danger') || severity.includes('error') || severity.includes('failed')) {
            iconClass = 'fa-exclamation-circle';
            toneClass = 'text-danger';
        } else if (severity.includes('warning') || severity.includes('expir')) {
            iconClass = 'fa-exclamation-triangle';
            toneClass = 'text-warning';
        } else if (severity.includes('success') || severity.includes('ok') || severity.includes('completed')) {
            iconClass = 'fa-check-circle';
            toneClass = 'text-success';
        }

        return {
            id: item.id || `notif-${index}`,
            title,
            message,
            createdAt,
            iconClass,
            toneClass,
            sortTime: new Date(createdAt).getTime() || 0
        };
    }).sort((a, b) => b.sortTime - a.sortTime);
}

function readCachedNotifications() {
    try {
        const raw = localStorage.getItem(MEMBER_NOTIF_CACHE_KEY);
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed?.items) ? parsed.items : [];
    } catch (_) {
        return [];
    }
}

function writeCachedNotifications(items) {
    localStorage.setItem(MEMBER_NOTIF_CACHE_KEY, JSON.stringify({
        checkedAt: new Date().toISOString(),
        items: Array.isArray(items) ? items : []
    }));
}

function renderMemberNotifications(items) {
    const notifBody = document.querySelector('#notificationPanel .notif-body');
    const notifBadge = document.getElementById('notifBadgeCount');
    if (!notifBody || !notifBadge) return;

    const todayKey = getTodayKey();
    const markedReadToday = localStorage.getItem(MEMBER_NOTIF_READ_DATE_KEY) === todayKey;
    const unreadCount = markedReadToday ? 0 : items.length;

    if (items.length === 0) {
        notifBody.innerHTML = `
            <div class="notif-item notif-read">
                <div class="notif-icon"><i class="fa fa-check-circle text-success fs-5"></i></div>
                <div class="notif-text-content">
                    <p>No new notifications.</p>
                    <small>Daily check is up to date.</small>
                </div>
            </div>
        `;
    } else {
        notifBody.innerHTML = items.slice(0, 8).map(item => `
            <div class="notif-item ${markedReadToday ? 'notif-read' : 'notif-unread'}">
                <div class="notif-icon"><i class="fa ${item.iconClass} ${item.toneClass} fs-5"></i></div>
                <div class="notif-text-content">
                    <p><strong>${escapeHtml(item.title)}:</strong> ${escapeHtml(item.message)}</p>
                    <small>${getRelativeTimeText(item.createdAt)}</small>
                </div>
            </div>
        `).join('');
    }

    notifBadge.innerText = `${unreadCount} New`;
}

async function refreshMemberNotifications(options = {}) {
    const { force = false } = options;
    const todayKey = getTodayKey();
    const lastCheckDate = localStorage.getItem(MEMBER_NOTIF_LAST_CHECK_KEY);

    if (!force && lastCheckDate === todayKey) {
        renderMemberNotifications(readCachedNotifications());
        return;
    }

    try {
        const response = await fetch(MEMBER_NOTIFICATIONS_URL, {
            headers: {
                'Accept': 'application/json',
                ...(token ? { 'Authorization': `Bearer ${token}` } : {})
            }
        });

        if (response.status === 401) {
            logout();
            return;
        }

        if (!response.ok) {
            throw new Error('Failed to fetch notifications');
        }

        const payload = await response.json();
        const items = normalizeNotificationItems(payload);
        writeCachedNotifications(items);
        localStorage.setItem(MEMBER_NOTIF_LAST_CHECK_KEY, todayKey);
        localStorage.removeItem(MEMBER_NOTIF_READ_DATE_KEY);
        renderMemberNotifications(items);
    } catch (error) {
        renderMemberNotifications(readCachedNotifications());
    }
}

function scheduleMemberNotificationMidnightCheck() {
    const now = new Date();
    const nextMidnight = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 0, 0, 5);
    const delay = Math.max(1000, nextMidnight.getTime() - now.getTime());

    setTimeout(async () => {
        await refreshMemberNotifications({ force: true });
        scheduleMemberNotificationMidnightCheck();
    }, delay);
}

function initMemberNotifications() {
    renderMemberNotifications(readCachedNotifications());
    refreshMemberNotifications();
    scheduleMemberNotificationMidnightCheck();
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

function clearNotifications(event) { 
    event.stopPropagation(); 
    document.getElementById('notificationPanel').style.display = 'none'; 
}

function markAllRead(event) { 
    event.stopPropagation(); 
    localStorage.setItem(MEMBER_NOTIF_READ_DATE_KEY, getTodayKey());
    renderMemberNotifications(readCachedNotifications());
    document.getElementById('notificationPanel').style.display = 'none';
}

function logout() { 
    localStorage.removeItem('token'); 
    window.location.href = '/login'; 
}

function openAddProductModal() { document.getElementById('addProductModal').style.display = 'flex'; }
function editProfileAlert() { alert("Edit Profile modal will open here!"); }

function getNestedValue(obj, path) {
    return path.split('.').reduce((acc, key) => (acc && acc[key] !== undefined ? acc[key] : null), obj);
}

function escapeHtml(value) {
    return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function renderRecentProducts(products) {
    const list = document.getElementById('recentProductsList');
    if (!list) return;

    const topProducts = Array.isArray(products) ? products.slice(0, 3) : [];
    if (topProducts.length === 0) {
        list.innerHTML = '<div class="pill-card mb-0"><b class="text-dark mb-0" style="font-size: 14px;">No products/services found</b></div>';
        return;
    }

    list.innerHTML = topProducts.map((prod, index) => {
        const name = escapeHtml(prod.name || 'Unnamed service');
        const rawUrl = (prod.url || prod.service_url || '').trim();
        const fullUrl = rawUrl && !rawUrl.startsWith('http') ? `https://${rawUrl}` : rawUrl;
        const safeDisplayUrl = escapeHtml(rawUrl || 'No URL provided');
        const safeFullUrl = escapeHtml(fullUrl || '#');
        const marginClass = index === topProducts.length - 1 ? ' mb-0' : '';

        return `
            <div class="pill-card${marginClass}">
                <b class="text-dark mb-0" style="font-size: 14px;">${name}</b>
                ${rawUrl ? `<a href="${safeFullUrl}" target="_blank" class="text-primary text-decoration-none fw-bold text-truncate" style="font-size: 13px; max-width: 200px;">${safeDisplayUrl} <i class="fa fa-external-link-alt ms-1"></i></a>` : '<span class="text-muted" style="font-size: 13px;">No URL provided</span>'}
            </div>
        `;
    }).join('');
}

async function fetchRecentProducts() {
    try {
        const response = await fetch(`${window.API_BASE_URL}/v1/products`, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });

        if (response.status === 401) {
            logout();
            return;
        }

        const data = await response.json();
        const products = data.data || [];
        const countEl = document.getElementById('dashProductsCount');
        if (countEl) countEl.innerText = String(products.length);
        renderRecentProducts(products);
    } catch (error) {
        renderRecentProducts([]);
    }
}

function normalizeDocumentUrl(raw) {
    if (!raw || typeof raw !== 'string') return null;
    const trimmed = raw.trim();
    if (!trimmed) return null;

    if (trimmed.startsWith('http://') || trimmed.startsWith('https://') || trimmed.startsWith('data:') || trimmed.startsWith('blob:')) {
        return trimmed;
    }

    if (trimmed.startsWith('/')) {
        return trimmed;
    }

    if (trimmed.startsWith('storage/')) {
        return '/' + trimmed;
    }

    return '/storage/' + trimmed;
}

function findDocumentUrl(type) {
    const profile = window.currentProfileData || {};

    const candidatePaths = {
        mayors: [
            'documents.mayors_permit',
            'documents.mayor_permit',
            'uploaded_documents.mayors_permit',
            'uploaded_documents.mayor_permit',
            'requirements.mayors_permit',
            'attachments.mayors_permit',
            'basic_profile.mayors_permit',
            'mayors_permit',
            'mayor_permit',
            'business_permit',
            'mayors_permit_url',
            'business_permit_url'
        ],
        dti: [
            'documents.dti_sec',
            'documents.dti_or_sec',
            'uploaded_documents.dti_sec',
            'uploaded_documents.dti_or_sec',
            'requirements.dti_sec',
            'attachments.dti_sec',
            'basic_profile.dti_sec',
            'dti_sec',
            'dti_or_sec',
            'dti',
            'sec',
            'dti_sec_url',
            'dti_url',
            'sec_url'
        ]
    };

    const paths = candidatePaths[type] || [];
    for (const path of paths) {
        const val = getNestedValue(profile, path);
        const normalized = normalizeDocumentUrl(val);
        if (normalized) return normalized;
    }
    return null;
}

function openDocModal(title, url) {
    const modal = document.getElementById('docViewModal');
    const titleEl = document.getElementById('docViewTitle');
    const imgEl = document.getElementById('docPreviewImage');
    const frameEl = document.getElementById('docPreviewFrame');
    const emptyEl = document.getElementById('docPreviewEmpty');
    const openLink = document.getElementById('docOpenLink');

    titleEl.innerHTML = `<i class="fa fa-file-lines text-danger"></i> ${title}`;

    imgEl.style.display = 'none';
    frameEl.style.display = 'none';
    emptyEl.style.display = 'none';
    imgEl.src = '';
    frameEl.src = '';

    if (!url) {
        emptyEl.style.display = 'block';
        openLink.style.display = 'none';
    } else {
        openLink.style.display = 'inline-flex';
        openLink.href = url;

        const isImage = /\.(png|jpe?g|gif|webp|bmp|svg)$/i.test(url);
        if (isImage) {
            imgEl.src = url;
            imgEl.style.display = 'block';
        } else {
            frameEl.src = url;
            frameEl.style.display = 'block';
        }
    }

    modal.style.display = 'flex';
}

function closeDocModal() {
    const modal = document.getElementById('docViewModal');
    const imgEl = document.getElementById('docPreviewImage');
    const frameEl = document.getElementById('docPreviewFrame');
    modal.style.display = 'none';
    imgEl.src = '';
    frameEl.src = '';
}

function handleDocOverlay(event) {
    if (event.target.id === 'docViewModal') {
        closeDocModal();
    }
}

function viewDocument(type) {
    const title = type === 'mayors' ? 'Mayor Permit' : 'DTI / SEC Document';
    const fileUrl = findDocumentUrl(type);
    openDocModal(title, fileUrl);
}

function applyProfileDataToUI(profile) {
    if (!profile) return;

    const basic = profile.basic_profile || {};
    const org = profile.organization_membership || {};
    const rep = profile.official_representative || {};
    const loc = basic.business_location || {};

    const companyName = basic.registered_business_name || 'Your Company';
    const repName = `${rep.first_name || ''} ${rep.surname || ''}`.trim();
    const memberID = `PCCI-${new Date().getFullYear()}-${String(profile.id || 0).padStart(4, '0')}`;
    const memStatus = profile.status || 'Pending';
    const memType = profile.membership_type || 'N/A';
    const contactNo = basic.contact_number || basic.telephone_no || 'N/A';
    const officialReceiptNo = profile.official_receipt_no || profile.or_number || profile.receipt_no || 'N/A';
    const membershipReceiptNo = profile.membership_receipt_no || profile.receipt_no || 'N/A';
    const ownershipType = org.ownership_type || org.organization_type || 'Not specified';

    // Sidebar
    setTextIfExists('sidebarCompany', companyName);
    setTextIfExists('sidebarName', repName || 'No Rep Assigned');
    setTextIfExists('sidebarEmail', basic.email || 'N/A');

    if (profile.photo_url) {
        const sidebarImage = document.getElementById('sidebarImage');
        const topbarAvatar = document.getElementById('topbarAvatar');
        const dashBizImage = document.getElementById('dashBizImage');
        const bizMainImage = document.getElementById('bizMainImage');
        if (sidebarImage) sidebarImage.src = profile.photo_url;
        if (topbarAvatar) topbarAvatar.src = profile.photo_url;
        if (dashBizImage) dashBizImage.src = profile.photo_url;
        if (bizMainImage) bizMainImage.src = profile.photo_url;
    }

    // Dashboard
    setTextIfExists('welcomeMessage', `Welcome, ${companyName}!`);
    setTextIfExists('dashBizName', companyName);
    setTextIfExists('dashBizEmail', basic.email || 'N/A');
    setTextIfExists('dashBizType', org.type_of_company || 'Industry not specified');
    setTextIfExists('dashMembershipStatus', memStatus.toUpperCase());
    setTextIfExists('dashMembershipID', memberID);
    setTextIfExists('dashMembershipType', memType);
    setTextIfExists('dashOfficialReceiptNo', officialReceiptNo);
    setTextIfExists('dashReceiptNo', membershipReceiptNo);
    setTextIfExists('dashOwnershipType', ownershipType);

    // My Business
    setTextIfExists('bizNameTitle', companyName);
    setTextIfExists('bizIndustryTitle', org.type_of_company || 'Industry not specified');
    setTextIfExists('bizEmailText', basic.email || 'N/A');
    setTextIfExists('bizPhoneText', contactNo);

    const addressString = `${loc.business_address || ''}, ${loc.city_municipality || ''}`.trim().replace(/^,|,$/g, '');
    setTextIfExists('bizAddressText', addressString || 'Address not provided');

    setTextIfExists('bizMembershipTypeText', memType);
    if (profile.date_approved) {
        let d = new Date(profile.date_approved);
        d.setFullYear(d.getFullYear() + 1);
        setTextIfExists('bizExpiryText', d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }));
    } else {
        setTextIfExists('bizExpiryText', 'Pending Approval');
    }

    setTextIfExists('repNameText', repName || 'N/A');
    setTextIfExists('repDesignationText', rep.designation || 'Representative');
    setTextIfExists('repEmailText', rep.email || basic.email || 'N/A');
    setTextIfExists('repPhoneText', rep.contact_number || contactNo);

    updateDashboardEventCard(profile);
    updateMembershipPlanDetails(profile);

    if (typeof syncSettingsFromProfile === 'function') {
        syncSettingsFromProfile(profile);
    }
}

function updateDashboardEventCard(profile) {
    const events = profile?.events || profile?.upcoming_events || [];
    const firstEvent = Array.isArray(events) && events.length ? events[0] : null;

    const title = firstEvent?.title || firstEvent?.name || 'No upcoming events';
    const eventDateRaw = firstEvent?.event_date || firstEvent?.date || null;
    const location = firstEvent?.location || firstEvent?.venue || '-';

    let dateText = 'To be announced';
    if (eventDateRaw) {
        const eventDate = new Date(eventDateRaw);
        if (!Number.isNaN(eventDate.getTime())) {
            dateText = eventDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        }
    }

    setTextIfExists('dashEventTitle', title);
    setTextIfExists('dashEventDate', dateText);
    setTextIfExists('dashEventLocation', location);
    setTextIfExists('dashEventsCount', String(Array.isArray(events) ? events.length : 0));
}

function updateMembershipPlanDetails(profile) {
    const plans = profile?.available_plans || profile?.membership_plans || [];
    if (Array.isArray(plans) && plans.length >= 2) {
        const [primary, secondary] = plans;
        setTextIfExists('membershipPlanPrimaryName', primary?.name || 'Plan A');
        setTextIfExists('membershipPlanPrimaryPrice', primary?.price || primary?.amount || 'N/A');
        setTextIfExists('membershipPlanSecondaryName', secondary?.name || 'Plan B');
        setTextIfExists('membershipPlanSecondaryPrice', secondary?.price || secondary?.amount || 'N/A');
        return;
    }

    const membershipType = (profile?.membership_type || '').toString().toLowerCase();
    const annualAmount = profile?.annual_membership_fee || profile?.membership_fee || 'Php 500.00 / year';
    const lifetimeAmount = profile?.lifetime_membership_fee || 'Php 10,000.00';

    setTextIfExists('membershipPlanPrimaryName', 'Lifetime Sponsorship');
    setTextIfExists('membershipPlanPrimaryPrice', lifetimeAmount);
    setTextIfExists('membershipPlanSecondaryName', 'Yearly Subscription');
    setTextIfExists('membershipPlanSecondaryPrice', annualAmount);

    if (membershipType.includes('lifetime')) {
        setTextIfExists('billingPlanLabel', 'Lifetime Sponsorship');
    } else if (membershipType) {
        setTextIfExists('billingPlanLabel', profile.membership_type);
    }
}

function extractProfileFromResponse(data) {
    if (!data) return null;

    const candidates = [
        data.data,
        data.application,
        data.profile,
        data.applicant,
        data.member,
        data
    ];

    for (const item of candidates) {
        if (!item) continue;
        if (Array.isArray(item)) {
            if (item.length > 0) return item[0];
            continue;
        }
        if (typeof item === 'object') {
            if (item.basic_profile || item.organization_membership || item.official_representative) {
                return item;
            }
        }
    }

    return null;
}

function normalizeProfileShape(rawProfile) {
    if (!rawProfile || typeof rawProfile !== 'object') return null;

    if (rawProfile.basic_profile || rawProfile.organization_membership || rawProfile.official_representative) {
        return rawProfile;
    }

    if (rawProfile.applicant && typeof rawProfile.applicant === 'object') {
        const applicant = rawProfile.applicant;
        if (applicant.basic_profile || applicant.organization_membership || applicant.official_representative) {
            return {
                ...applicant,
                status: rawProfile.status || applicant.status,
                id: rawProfile.id || applicant.id
            };
        }
    }

    const source = rawProfile.user && typeof rawProfile.user === 'object' ? rawProfile.user : rawProfile;
    const name = (source.name || localStorage.getItem('userName') || '').trim();
    const nameParts = name.split(/\s+/).filter(Boolean);

    return {
        id: source.id || rawProfile.id || 0,
        status: source.status || rawProfile.status || 'Active',
        membership_type: source.membership_type || rawProfile.membership_type || 'N/A',
        basic_profile: {
            registered_business_name: source.registered_business_name || source.company_name || source.business_name || name || 'Your Company',
            email: source.email || rawProfile.email || 'N/A',
            contact_number: source.contact_number || source.telephone_no || rawProfile.contact_number || 'N/A',
            telephone_no: source.telephone_no || source.contact_number || rawProfile.telephone_no || 'N/A',
            business_location: {
                business_address: source.business_address || '',
                city_municipality: source.city_municipality || ''
            }
        },
        organization_membership: {
            type_of_company: source.type_of_company || source.industry || 'N/A',
            ownership_type: source.ownership_type || 'Not specified'
        },
        official_representative: {
            first_name: source.first_name || nameParts.slice(0, -1).join(' ') || nameParts[0] || '',
            surname: source.surname || (nameParts.length > 1 ? nameParts[nameParts.length - 1] : ''),
            email: source.email || rawProfile.email || 'N/A',
            contact_number: source.contact_number || source.telephone_no || 'N/A',
            designation: source.designation || 'Representative'
        }
    };
}

// ========================================
// REAL API FETCH LOGIC
// ==========================================
async function fetchRealDashboardData(token) {
    const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    };

    const endpoints = ['/v1/application', '/v1/user'];

    try {
        for (const endpoint of endpoints) {
            const response = await fetch(`${window.API_BASE_URL}${endpoint}`, { headers });

            if (response.status === 401) {
                logout();
                return;
            }

            if (!response.ok) continue;

            const data = await response.json();
            const rawProfile = extractProfileFromResponse(data);
            const profile = normalizeProfileShape(rawProfile);

            if (profile) {
                window.currentProfileData = profile;
                localStorage.setItem('member_profile_cache', JSON.stringify(profile));
                applyProfileDataToUI(profile);
                fetchRecentProducts();
                return;
            }
        }

        const cachedProfileRaw = localStorage.getItem('member_profile_cache');
        if (cachedProfileRaw) {
            const cachedProfile = JSON.parse(cachedProfileRaw);
            const normalizedCached = normalizeProfileShape(cachedProfile);
            window.currentProfileData = normalizedCached;
            applyProfileDataToUI(normalizedCached);
            fetchRecentProducts();
        }
    } catch(error) {
        console.error("Failed to fetch API Data:", error);
        try {
            const cachedProfileRaw = localStorage.getItem('member_profile_cache');
            if (cachedProfileRaw) {
                const cachedProfile = JSON.parse(cachedProfileRaw);
                const normalizedCached = normalizeProfileShape(cachedProfile);
                window.currentProfileData = normalizedCached;
                applyProfileDataToUI(normalizedCached);
                fetchRecentProducts();
            }
        } catch (_) {
            // Ignore cache parse errors
        }
    }
}

// ==========================================
// REAL PRODUCTS API LOGIC
// ==========================================

// 1. Fetch & Display Products (GET)
async function fetchProducts() {
    const tbody = document.getElementById('productsTableBody');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Loading products...</td></tr>';
    
    try {
        const response = await fetch(`${window.API_BASE_URL}/v1/products`, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        
        if (response.status === 401) { logout(); return; }
        
        const data = await response.json();
        // Adjust "data.data" if your API wraps the array differently
        const products = data.data || []; 
        const countEl = document.getElementById('dashProductsCount');
        if (countEl) countEl.innerText = String(products.length);
        renderRecentProducts(products);
        
        tbody.innerHTML = '';
        if(products.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No products found.</td></tr>';
            return;
        }
        
        products.forEach(prod => {
            const name = prod.name || 'N/A';
            const desc = prod.description || '';
            const url = prod.url || prod.service_url || '#'; // Adjust property name to match your API response
            const rawStatus = (prod.status || 'active').toString().toLowerCase();
            const status = rawStatus === 'inactive' ? 'Inactive' : 'Active';
            const statusColor = rawStatus === 'active' ? 'green' : 'gray';
            
            // Format URL to be clickable
            const cleanUrl = url !== '#' && !url.startsWith('http') ? 'https://' + url : url;
            const urlDisplay = url !== '#' ? `<a href="${cleanUrl}" target="_blank" class="text-primary text-decoration-none">${url}</a>` : '<span class="text-muted">N/A</span>';

            tbody.innerHTML += `
                <tr>
                    <td class="fw-bold text-dark">${name}</td>
                    <td class="text-muted">${desc}</td>
                    <td>${urlDisplay}</td>
                    <td style="color: ${statusColor}; font-weight: bold;">${status}</td>
                    <td>
                        <button class="btn btn-sm btn-light text-warning shadow-sm me-1" onclick="editProduct(${prod.id}, '${name.replace(/'/g, "\\'")}', '${desc.replace(/'/g, "\\'")}', '${url.replace(/'/g, "\\'")}', '${rawStatus.replace(/'/g, "\\'")}')"><i class="fa fa-pen"></i></button>
                        <button class="btn btn-sm btn-light text-danger shadow-sm" onclick="deleteProduct(${prod.id})"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error("Error fetching products:", error);
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load products.</td></tr>';
    }
}

// 2. Open Add Modal
function openAddProductModal() {
    document.getElementById('productModalTitle').innerText = 'Add New Product';
    document.getElementById('prodId').value = '';
    document.getElementById('prodName').value = '';
    document.getElementById('prodUrl').value = '';
    document.getElementById('prodDesc').value = '';
    document.getElementById('prodStatus').value = 'active';
    document.getElementById('prodStatusWrap').style.display = 'none';
    document.getElementById('productAlert').style.display = 'none';
    document.getElementById('addProductModal').style.display = 'flex';
}

// 3. Open Edit Modal
function editProduct(id, name, desc, url, status) {
    document.getElementById('productModalTitle').innerText = 'Edit Product';
    document.getElementById('prodId').value = id;
    document.getElementById('prodName').value = name;
    document.getElementById('prodDesc').value = desc === 'null' ? '' : desc;
    document.getElementById('prodUrl').value = url === 'null' || url === '#' ? '' : url;
    document.getElementById('prodStatus').value = (status || 'active').toLowerCase() === 'inactive' ? 'inactive' : 'active';
    document.getElementById('prodStatusWrap').style.display = 'block';
    document.getElementById('productAlert').style.display = 'none';
    document.getElementById('addProductModal').style.display = 'flex';
}

// Close Modal
function closeProductModal() {
    document.getElementById('addProductModal').style.display = 'none';
}

// 4. Create (POST) or Update (PUT/POST) Product
async function saveProduct() {
    const id = document.getElementById('prodId').value;
    const name = document.getElementById('prodName').value;
    const desc = document.getElementById('prodDesc').value;
    const url = document.getElementById('prodUrl').value;
    const isUpdate = id !== '';
    const status = isUpdate ? document.getElementById('prodStatus').value : 'active';
    const btn = document.getElementById('btnSaveProduct');
    const alertBox = document.getElementById('productAlert');
    
    if(!name) {
        alertBox.innerText = 'Product name is required.';
        alertBox.style.display = 'block';
        return;
    }

    btn.disabled = true;
    btn.innerText = 'Saving...';
    alertBox.style.display = 'none';

    // Ensure property names match your Laravel validation request
    const payload = {
        name: name,
        description: desc,
        url: url, // change to 'service_url' if that's what your API expects
        status: status
    };

    const endpoint = isUpdate ? `${window.API_BASE_URL}/v1/products/${id}` : `${window.API_BASE_URL}/v1/products`;
    
    // Note: If your Postman update route requires POST instead of PUT, change method here to 'POST'
    let method = isUpdate ? 'PUT' : 'POST';

    try {
        const response = await fetch(endpoint, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (response.ok || response.status === 201 || response.status === 200) {
            closeProductModal();
            fetchProducts(); // Refresh the table
        } else {
            alertBox.innerText = data.message || 'Failed to save product.';
            if(data.errors) alertBox.innerText += ' ' + Object.values(data.errors).flat().join(' ');
            alertBox.style.display = 'block';
        }
    } catch (error) {
        alertBox.innerText = 'Network error occurred.';
        alertBox.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.innerText = 'Save Product';
    }
}

// 5. Delete Product (DELETE)
async function deleteProduct(id) {
    if(!confirm('Are you sure you want to remove this product?')) return;
    
    try {
        const response = await fetch(`${window.API_BASE_URL}/v1/products/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if(response.ok) {
            fetchProducts(); // Refresh the list
        } else {
            const data = await response.json();
            alert(data.message || "Failed to delete product.");
        }
    } catch(error) {
        alert("Network error. Could not delete product.");
    }
}

// ==========================================
// EDIT PROFILE LOGIC
// ==========================================

function openEditProfileModal() {
    console.log('Opening edit profile modal...');
    console.log('Profile data available:', !!window.currentProfileData);
    
    // Get profile data if available, otherwise use defaults
    const profile = window.currentProfileData || {};
    const basic = profile.basic_profile || {};
    const org = profile.organization_membership || {};
    const rep = profile.official_representative || {};
    const loc = basic.business_location || {};

    // Populate Modal Display Fields (for the new modal structure)
    document.getElementById('ep_companyNameDisplay').innerText = basic.registered_business_name || 'Not provided';
    document.getElementById('ep_companyNameDisplay2').innerText = basic.registered_business_name || 'Not provided';
    document.getElementById('ep_companyTypeDisplay').innerText = org.type_of_company || 'Not provided';
    
    document.getElementById('ep_businessType').value = org.type_of_company || '';
    document.getElementById('ep_description').value = basic.business_description || '';
    
    const fullName = ((rep.first_name || '') + ' ' + (rep.surname || '')).trim();
    document.getElementById('ep_repNameDisplay').innerText = fullName || 'Not provided';
    document.getElementById('ep_repPositionDisplay').innerText = rep.designation || 'Not provided';
    document.getElementById('ep_contactDisplay').innerText = basic.contact_number || basic.telephone_no || 'Not provided';
    
    document.getElementById('ep_addressDisplay').innerText = loc.business_address || 'Not provided';
    document.getElementById('ep_urlDisplay').innerText = basic.website_url || 'Not provided';

    // Keep type/description and preview sections in sync while editing.
    const businessTypeEl = document.getElementById('ep_businessType');
    const descriptionEl = document.getElementById('ep_description');
    if (businessTypeEl && !businessTypeEl.dataset.syncBound) {
        businessTypeEl.addEventListener('change', syncBusinessPreviewFromEditor);
        businessTypeEl.dataset.syncBound = '1';
    }
    if (descriptionEl && !descriptionEl.dataset.syncBound) {
        descriptionEl.addEventListener('input', syncBusinessPreviewFromEditor);
        descriptionEl.dataset.syncBound = '1';
    }
    syncBusinessPreviewFromEditor();

    // Show Modal
    document.getElementById('profileAlert').style.display = 'none';
    const modal = document.getElementById('editProfileModal');
    console.log('Modal element:', modal);
    modal.style.display = 'flex';
    console.log('Modal should now be visible');
}

function closeEditProfileModal() {
    document.getElementById('editProfileModal').style.display = 'none';
}

async function saveProfile() {
    const btn = document.getElementById('btnSaveProfile');
    const alertBox = document.getElementById('profileAlert');
    
    if (!window.currentProfileData) {
        alertBox.innerText = 'Profile data not loaded yet. Please refresh and try again.';
        alertBox.style.display = 'block';
        return;
    }

    btn.disabled = true;
    btn.innerText = 'Saving...';
    alertBox.style.display = 'none';

    // 1. Create a deep copy of the existing profile data so we send EVERYTHING back
    let payload = JSON.parse(JSON.stringify(window.currentProfileData));

    // Ensure objects exist to prevent javascript errors
    payload.basic_profile = payload.basic_profile || {};
    payload.basic_profile.business_location = payload.basic_profile.business_location || {};
    payload.organization_membership = payload.organization_membership || {};
    payload.official_representative = payload.official_representative || {};

    // 2. Override fields from the current modal controls/display values.
    const companyName = (document.getElementById('ep_companyNameDisplay2')?.innerText || '').trim();
    const businessType = (document.getElementById('ep_businessType')?.value || '').trim();
    const businessDescription = (document.getElementById('ep_description')?.value || '').trim();
    const contactNumber = (document.getElementById('ep_contactDisplay')?.innerText || '').trim();
    const addressDisplay = (document.getElementById('ep_addressDisplay')?.innerText || '').trim();
    const repNameDisplay = (document.getElementById('ep_repNameDisplay')?.innerText || '').trim();
    const repDesignation = (document.getElementById('ep_repPositionDisplay')?.innerText || '').trim();
    const websiteUrl = (document.getElementById('ep_urlDisplay')?.innerText || '').trim();

    if (companyName) payload.basic_profile.registered_business_name = companyName;
    if (businessDescription !== '') payload.basic_profile.business_description = businessDescription;
    if (contactNumber) {
        payload.basic_profile.telephone_no = contactNumber;
        payload.basic_profile.contact_number = contactNumber;
    }
    if (businessType) payload.organization_membership.type_of_company = businessType;
    if (websiteUrl) payload.basic_profile.website_url = websiteUrl;

    if (addressDisplay) {
        const parts = addressDisplay.split(',').map(p => p.trim()).filter(Boolean);
        payload.basic_profile.business_location.business_address = parts[0] || payload.basic_profile.business_location.business_address || '';
        payload.basic_profile.business_location.city_municipality = parts.slice(1).join(', ') || payload.basic_profile.business_location.city_municipality || '';
    }

    if (repNameDisplay) {
        const nameParts = repNameDisplay.split(/\s+/).filter(Boolean);
        payload.official_representative.first_name = nameParts.slice(0, -1).join(' ') || nameParts[0] || payload.official_representative.first_name || '';
        payload.official_representative.surname = nameParts.length > 1 ? nameParts[nameParts.length - 1] : (payload.official_representative.surname || '');
    }
    if (repDesignation) payload.official_representative.designation = repDesignation;

    try {
        const response = await fetch(`${window.API_BASE_URL}/v1/application`, {
            method: 'PUT', // /v1/application update route supports PUT
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (response.ok || response.status === 200 || response.status === 201) {
            // Apply optimistic UI update immediately so users see changes right after closing modal.
            window.currentProfileData = payload;
            applyProfileDataToUI(payload);
            closeEditProfileModal();
            alert('Profile updated successfully!'); 
        } else {
            alertBox.innerText = data.message || 'Failed to update profile.';
            if(data.errors) {
                alertBox.innerText += ' ' + Object.values(data.errors).flat().join(' ');
            }
            alertBox.style.display = 'block';
        }
    } catch (error) {
        console.error("Error saving profile:", error);
        alertBox.innerText = 'Network error occurred while saving.';
        alertBox.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.innerText = 'Save Changes';
    }
}

</script>
@endsection