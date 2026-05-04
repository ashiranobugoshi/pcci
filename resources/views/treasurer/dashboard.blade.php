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
    position: fixed; top: 0; left: 0; right: 0; height: clamp(50px, 10vh, 70px); background: #ffffff;
    display: flex; align-items: center; justify-content: space-between; padding: 0 clamp(10px, 3vw, 20px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); z-index: 1050; transition: background-color 0.3s;
}
.topbar-search-wrapper { width: clamp(150px, 25vw, 300px); position: relative; display: none; }
.topbar-search-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: clamp(11px, 2vw, 13px); }
.topbar-search { width: 100%; height: 36px; padding: 6px 15px 6px 35px; border-radius: 8px; border: 1px solid #eee; background: #eee; font-size: clamp(11px, 2vw, 13px); outline: none; transition: 0.3s;}
.topbar-actions { display: flex; align-items: center; gap: clamp(8px, 2vw, 15px); }
.topbar-avatar { width: clamp(28px, 6vw, 35px); height: clamp(28px, 6vw, 35px); border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb; cursor: pointer; }
.sidebar-toggle { display: none; background: none; border: none; font-size: clamp(16px, 4vw, 20px); cursor: pointer; color: #4b5563; }

/* =========================================
   2. NOTIFICATION PANEL
   ========================================= */
.notification-panel {
    position: fixed; top: 60px; right: 60px; width: 320px; background: #ffffff; border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; display: none; flex-direction: column; z-index: 1100; overflow: hidden;
}
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
    position: fixed; top: clamp(50px, 10vh, 70px); left: 0; width: 250px; height: calc(100vh - clamp(50px, 10vh, 70px));
    background: #f8f9fb; border-right: 1px solid #e5e7eb; display: flex; flex-direction: column; z-index: 1000; overflow-y: auto; transition: background-color 0.3s, transform 0.3s;
}
.sidebar-profile { padding: clamp(12px, 2vw, 20px) clamp(10px, 2vw, 15px) clamp(10px, 2vw, 15px); text-align: center; border-bottom: 1px solid #e5e7eb; }
.sidebar-profile img { width: clamp(50px, 8vw, 65px); height: clamp(50px, 8vw, 65px); border-radius: 50%; object-fit: cover; border: 1px solid #e5e7eb; margin-bottom: clamp(6px, 1vw, 10px); }
.sidebar-profile h5 { font-size: clamp(13px, 2vw, 15px); font-weight: bold; margin-bottom: 0; color: #111; }
.sidebar-profile p { font-size: clamp(11px, 1.5vw, 13px); font-weight: bold; color: #4b5563; margin-bottom: 0; }
.sidebar-profile small { font-size: clamp(10px, 1.5vw, 12px); color: #777; }

.sidebar-menu { list-style: none; padding: clamp(10px, 2vw, 15px); margin: 0; flex-grow: 1; }
.sidebar-menu li { height: clamp(40px, 6vw, 45px); padding: 0 clamp(10px, 2vw, 15px); margin-bottom: 4px; cursor: pointer; font-weight: 600; font-size: clamp(12px, 2vw, 14px); color: #4b5563; border-radius: 8px; display: flex; align-items: center; gap: 10px; transition: 0.2s;}
.sidebar-menu li i { font-size: clamp(14px, 2vw, 16px); width: 20px; text-align: center; }
.sidebar-menu li.active { background: #e5e7eb; color: #111; border-left: 4px solid #b61b2a;}
.sidebar-menu li:hover:not(.active) { background: #eef0f4; }
.sidebar-divider { border-top: 1px solid #e5e7eb; margin: clamp(8px, 1vw, 10px); }

/* =========================================
   4. MAIN CONTENT AREA & COMPONENTS
   ========================================= */
.main { margin-top: clamp(50px, 10vh, 70px); margin-left: 250px; padding: clamp(15px, 4vw, 25px); min-height: calc(100vh - clamp(50px, 10vh, 70px)); background: #f4f6f9; transition: background-color 0.3s;}
.content-section { display: none; padding-bottom: 40px; }

/* Drill-down UI Animations */
.fade-in { animation: fadeIn 0.3s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

/* Floating Cards Base */
.floating-card { background: #ffffff; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); padding: clamp(12px, 3vw, 16px); border: none; transition: 0.3s; }
.custom-card { background: #ffffff; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); padding: clamp(15px, 3vw, 20px); margin-bottom: clamp(15px, 3vw, 20px); transition: 0.3s;}

/* Specific Summary Cards (Dashboard) */
.summary-card { width: 100%; height: clamp(85px, 15vw, 105px); border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); padding: clamp(12px, 3vw, 20px); color: white; position: relative; overflow: hidden; display: flex; align-items: center;}
.summary-card.dash-card { flex-direction: column; align-items: flex-start; justify-content: center; }
.summary-card .icon-circle { width: clamp(35px, 6vw, 45px); height: clamp(35px, 6vw, 45px); border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; justify-content: center; align-items: center; font-size: clamp(16px, 3vw, 20px); flex-shrink: 0;}
.summary-card .label { font-size: clamp(12px, 2vw, 14px); font-weight: 500; opacity: 0.9; margin-bottom: 2px; }
.summary-card .value { font-size: clamp(18px, 4vw, 26px); font-weight: bold; margin: 0; line-height: 1; }
.summary-card .bg-icon { position: absolute; right: clamp(10px, 3vw, 20px); top: 50%; transform: translateY(-50%); font-size: clamp(30px, 6vw, 40px); opacity: 0.2; }

.bg-red { background: linear-gradient(135deg, #e53935, #c62828); }
.bg-green { background: linear-gradient(135deg, #43a047, #2e7d32); }
.bg-orange { background: linear-gradient(135deg, #fb8c00, #ef6c00); }

/* Small Info Card */
.small-info-card { width: 100%; min-height: 88px; display: flex; align-items: center; gap: 14px; }
.small-info-card .icon-box { width: 44px; height: 44px; border-radius: 10px; background: #eef4ff; color: #1d4ed8; display: flex; justify-content: center; align-items: center; font-size: 20px; flex-shrink: 0; }
.small-info-content { flex: 1; min-width: 0; }
.small-info-content p { margin: 0; font-size: 14px; color: #374151; font-weight: 600; line-height: 1.35; }
.small-info-content p + p { margin-top: 2px; font-size: 13px; font-weight: 500; }
.small-info-filter-wrap { flex-shrink: 0; }
.dashboard-inline-filter { min-width: 126px; border-radius: 10px; border: 1px solid #d1d5db; font-weight: 600; font-size: 13px; background-color: #fff; }

/* Charts Area */
.chart-container { height: clamp(200px, 40vw, 280px); width: 100%; position: relative; }
.card-title-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: clamp(10px, 2vw, 15px); flex-wrap: wrap; gap: clamp(8px, 2vw, 15px); }
.card-title-row h5 { font-size: clamp(14px, 2.5vw, 16px); font-weight: bold; margin: 0; color: #333; }

/* Table Area */
.table-card { min-height: clamp(250px, 50vw, 350px); padding: clamp(10px, 2vw, 20px) clamp(12px, 2vw, 20px); overflow-x: auto; }
.custom-table { width: 100%; border-collapse: collapse; font-size: clamp(11px, 1.5vw, 13px); }
.custom-table th { background: #f8f9fb; color: #777; font-weight: 600; padding: clamp(8px, 1vw, 12px) clamp(6px, 1vw, 10px); text-align: left; position: sticky; top: 0; z-index: 1;}
.custom-table td { padding: clamp(8px, 1vw, 12px) clamp(6px, 1vw, 10px); border-bottom: 1px solid #eee; color: #444; vertical-align: middle; height: clamp(40px, 6vw, 45px);}
.custom-table tbody tr:hover { background-color: #f9fafb; }

/* Center all columns in dashboard Recent Payments table */
.recent-payments-table th,
.recent-payments-table td {
    text-align: center !important;
}

/* Action Buttons & Badges */
.action-btn { height: clamp(26px, 4vw, 30px); padding: 0 clamp(8px, 2vw, 10px); border-radius: 6px; border: none; font-size: clamp(10px, 1.5vw, 12px); font-weight: bold; cursor: pointer; color: white; display: inline-flex; align-items: center; justify-content: center; gap: 5px; margin-right: 4px;}
.btn-gray { background-color: #9ca3af; width: auto; }
.btn-green { background-color: #22c55e; width: auto; padding: 0 clamp(10px, 2vw, 12px);} 
.btn-red { background-color: #ef4444; width: auto; padding: 0 clamp(8px, 2vw, 10px); } 

.action-icon-btn { width: clamp(28px, 5vw, 32px); height: clamp(28px, 5vw, 32px); padding: 0; border-radius: 6px; transition: 0.2s; display: inline-flex; justify-content: center; align-items: center; font-size: clamp(12px, 2vw, 14px);}
.action-icon-btn:hover { background-color: #e5e7eb; }

/* Status Badges */
.status-badge { display: inline-block; padding: 4px clamp(6px, 1.5vw, 12px); border-radius: 50rem; font-size: clamp(10px, 1.5vw, 12px); color: white; text-align: center; font-weight: 500; }
.status-completed { background-color: #22c55e; }
.status-pending { background-color: #f59e0b; }
.status-failed { background-color: #ef4444; }

/* =========================================
   REPORTS TAB SPECIFIC STYLES & DROPDOWN
   ========================================= */
.reports-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(clamp(150px, 30vw, 200px), 1fr)); gap: clamp(12px, 2vw, 16px); margin-bottom: clamp(18px, 3vw, 24px); }
.report-stat-card { background: #ffffff; border-radius: 12px; padding: clamp(12px, 2vw, 16px); height: clamp(100px, 20vw, 120px); box-shadow: 0 2px 6px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: center; gap: clamp(4px, 1vw, 6px); transition: 0.3s; }
.report-stat-card .report-label { font-size: clamp(10px, 1.5vw, 12px); color: #777; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;}
.report-stat-card .report-value { font-size: clamp(18px, 4vw, 24px); font-weight: 600; color: #111; margin: 0; line-height: 1;}
.report-stat-card .report-indicator { font-size: clamp(10px, 1.5vw, 11px); font-weight: 600; }
.text-green { color: #22c55e !important; }
.text-red { color: #ef4444 !important; }
.report-chart-box { background: #ffffff; border-radius: 12px; padding: clamp(12px, 2vw, 16px); box-shadow: 0 2px 6px rgba(0,0,0,0.05); height: clamp(250px, 40vw, 300px); display: flex; flex-direction: column; transition: 0.3s; }
.mini-card-container { display: flex; gap: clamp(10px, 2vw, 16px); align-items: center; justify-content: center; flex-wrap: wrap; height: 100%; }
.mini-stat-card { background: #f8f9fb; border-radius: 12px; width: clamp(120px, 20vw, 150px); height: clamp(75px, 12vw, 90px); display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px solid #eee; transition: 0.3s; }
.mini-stat-card .m-val { font-size: clamp(16px, 3vw, 20px); font-weight: 600; color: #111; line-height: 1.2;}
.mini-stat-card .m-lbl { font-size: clamp(10px, 1.5vw, 12px); color: #777; }
.report-flat-table { width: 100%; border-collapse: collapse; }
.report-flat-table td { padding: clamp(8px, 1vw, 12px) 0; border-bottom: 1px solid #eee; font-size: clamp(11px, 1.5vw, 13px); color: #111; font-weight: 500;}
.report-flat-table tr:last-child td { border-bottom: none; }

.report-dropdown-menu { position: absolute; right: 0; top: 100%; margin-top: 8px; background: #ffffff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: clamp(180px, 40vw, 220px); padding: 8px; display: none; flex-direction: column; z-index: 1050; border: 1px solid #e5e7eb; animation: fadeIn 0.2s ease-in-out;}
.report-dropdown-item { padding: clamp(8px, 1.5vw, 10px) clamp(12px, 2vw, 15px); border-radius: 8px; cursor: pointer; font-size: clamp(11px, 1.5vw, 13px); font-weight: 600; color: #4b5563; display: flex; align-items: center; gap: 12px; transition: 0.2s;}
.report-dropdown-item:hover { background: #f3f4f6; color: #111; }
.report-dropdown-item i { font-size: clamp(14px, 2vw, 16px); text-align: center; width: 20px; }
.trans-filter-divider { height: 1px; background: #eee; margin: 4px 0; border: none; }

/* =========================================
   MODAL STYLES (GENERAL)
   ========================================= */
.custom-modal-overlay { position: fixed; top: 0; left: 250px; width: calc(100% - 250px); height: 100%; background: rgba(0, 0, 0, 0.7); display: none; justify-content: center; align-items: center; z-index: 1060; backdrop-filter: blur(3px); }
.custom-modal-card { background: #ffffff; width: 90%; max-width: 700px; border-radius: 16px; padding: 25px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); position: relative; animation: slideIn 0.3s ease-out; }
@keyframes slideIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
.modal-close-x { position: absolute; top: 15px; right: 20px; font-size: 28px; color: #888; cursor: pointer; border: none; background: none; line-height: 1; z-index: 2; }
.modal-img-wrapper { width: 100%; min-height: 120px; display: flex; justify-content: center; align-items: center; position: relative; }
.modal-img-wrapper img { max-width: 100%; max-height: 400px; width: auto; height: auto; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: block; }

#proofModal .modal-img-wrapper {
    min-height: 220px;
    display: flex;
    justify-content: center;
    align-items: center;
}

#proofModal #modalSpinner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    white-space: nowrap;
}

#simpleProofModal #simpleModalSpinner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    white-space: nowrap;
}

/* Process payment modal: keep this one tighter than other modals */
#proofModal .custom-modal-card {
    max-width: 560px;
}

/* Large screens: keep confirm button reachable */
@media (min-width: 1000px) {
    #proofModal .custom-modal-card {
        max-height: 88vh;
        overflow-y: auto;
    }

    #proofModal .modal-img-wrapper img {
        max-height: 320px;
    }
}

.type-toggle-btn { padding: 12px; border-radius: 10px; border: 2px solid #e5e7eb; background: #f9fafb; color: #4b5563; font-weight: bold; cursor: pointer; transition: 0.2s; text-align: center; }
.type-toggle-btn:hover { border-color: #d1d5db; background: #f3f4f6; }
.type-toggle-btn.active-1 { background: #fff1f2; color: #ef4444; border-color: #ef4444; }
.type-toggle-btn.active-2 { background: #eff6ff; color: #3b82f6; border-color: #3b82f6; }

/* Settings Buttons */
.setting-box { transition: 0.2s; background: #ffffff; cursor: pointer; border: 1px solid #eee; border-radius: 12px;}
.setting-box:hover { background-color: #f9fafb; border-color: #d1d5db !important; }
.back-btn-ui { cursor: pointer; font-size: 14px; font-weight: bold; color: #4b5563; transition: 0.2s; display: inline-flex; align-items: center; padding: 5px 10px; border-radius: 6px; margin-left: -10px; box-shadow: none; border: none; background: transparent; outline: none; }
.back-btn-ui:hover { background: #e5e7eb; color: #111; }

.acc-header-out { display: flex; align-items: center; gap: 15px; margin: 0 auto 20px auto; max-width: 1000px; }
.acc-header-icon { font-size: 36px; color: #111; }

/* WIDER SETTINGS FLOATING CARD */
.new-acc-card { background: #fff; border-radius: 12px; padding: 35px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 1000px; margin: 0 auto; transition: 0.3s; }
.new-acc-avatar { width: 85px; height: 85px; border-radius: 50%; border: 3px solid #ef4444; object-fit: cover; }
.new-acc-btn-upload { background: #fff; border: 1px solid #ddd; color: #333; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; transition: 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.new-acc-btn-upload:hover { background: #f9fafb; }
.new-acc-btn-delete { background: #b0b0b0; border: none; color: #fff; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; transition: 0.2s; }
.new-acc-btn-delete:hover { background: #999; }
.new-acc-input { background: #f8f9fb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 12px; padding-right: 65px; font-size: 14px; width: 100%; color: #111; outline: none; transition: 0.2s; height: 42px; }
.new-acc-edit { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); background: #fff; border: 1px solid #ef4444; color: #ef4444; font-size: 11px; padding: 3px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; transition: 0.2s; cursor: pointer; }
.new-acc-edit:hover { background: #ef4444; color: #fff; }
.new-acc-action-gray { background: #9ca3af; color: #fff; border: none; padding: 10px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; transition: 0.2s; }
.new-acc-action-gray:hover { background: #6b7280; }
.new-acc-action-dark { background: #8b8b8b; color: #fff; border: none; padding: 10px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; transition: 0.2s; }
.new-acc-action-dark:hover { background: #666; }

/* Security Settings Elements */
.sec-change-pw-box { background: #f8f9fb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 15px 25px; margin-bottom: 25px; transition: 0.3s; }
.sec-btn-update { background: #9ca3af; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 500; transition: 0.2s; cursor: pointer;}
.sec-btn-update:hover { background: #6b7280; }
.sec-login-box { background: #f8f9fb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; min-height: 250px; transition: 0.3s;}
.sec-login-title { font-size: 16px; font-weight: 600; margin-bottom: 10px; }
.sec-divider { height: 1px; background: #ddd; margin-bottom: 15px; border: none; }
.sec-table { width: 100%; border-collapse: collapse; }
.sec-table th { text-align: left; padding: 12px 10px; font-weight: 500; border-bottom: 1px solid #ddd; }
.sec-table td { padding: 12px 10px; color: #666; font-size: 13px; }

/* =========================================
   ADD PAYMENT MODAL STYLES (NEW)
   ========================================= */
.add-payment-modal-card { background: #ffffff; max-width: 450px !important; padding: 25px; border-radius: 12px; color: #111; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
.add-payment-modal-header { display: flex; align-items: center; justify-content: flex-start; margin-bottom: 25px; position: relative; }
.add-payment-modal-icon-container { position: relative; font-size: 32px; color: #111; margin-right: 15px; }
.add-payment-modal-check-icon { position: absolute; bottom: -2px; right: -6px; font-size: 12px; color: #fff; background: #111; border: 2px solid #fff; border-radius: 50%; width: 18px; height: 18px; display: flex; justify-content: center; align-items: center; }
.add-payment-modal-title { font-size: 18px; font-weight: bold; color: #111; margin: 0; }
.add-payment-modal-body { display: flex; flex-direction: column; gap: 15px; }
.add-payment-form-group { display: flex; flex-direction: column; gap: 5px; text-align: left; }
.add-payment-label { font-size: 13px; color: #666; margin: 0; font-weight: normal; }
.add-payment-input { width: 100%; height: 40px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 0 12px; font-size: 14px; color: #111; outline: none; transition: 0.2s; }
.add-payment-input:focus { border-color: #b61b2a; }
.add-payment-input[readonly] { background-color: #f3f4f6; color: #888; cursor: not-allowed; }
.add-payment-modal-footer { display: flex; justify-content: space-between; gap: 12px; margin-top: 30px; }
.add-payment-btn-clear { flex: 1; height: 42px; background: #e5e7eb; color: #4b5563; font-weight: bold; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; transition: 0.2s; }
.add-payment-btn-clear:hover { background: #d1d5db; }
.add-payment-btn-confirm { flex: 1; height: 42px; background: #b61b2a; color: #fff; font-weight: bold; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; transition: 0.2s; }
.add-payment-btn-confirm:hover { background: #9b1724; }

/* =========================================
   OTP & RESET PASSWORD MODAL STYLES
   ======================================
   
   === */
.otp-modal-card { background: #ffffff; max-width: 450px !important; padding: 30px; border-radius: 12px; border: none; color: #111; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
.otp-title { font-size: 20px; font-weight: bold; margin-bottom: 10px; color: #111; }
.otp-subtitle { font-size: 14px; color: #666; margin-bottom: 25px; line-height: 1.5; }
.otp-subtitle span { color: #111; font-weight: bold; }
.otp-input-container { display: flex; gap: 10px; justify-content: flex-start; }
.otp-box { width: 55px; height: 55px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 24px; text-align: center; color: #111; outline: none; transition: 0.2s; }
.otp-box:focus { background: #fff; border-color: #b61b2a; box-shadow: 0 0 0 2px rgba(182, 27, 42, 0.1); }

.otp-feedback-modal-card { background: #ffffff; max-width: 420px !important; padding: 26px; border-radius: 12px; border: none; color: #111; box-shadow: 0 10px 30px rgba(0,0,0,0.12); }
.otp-feedback-title { font-size: 20px; font-weight: 700; margin: 0 0 8px 0; color: #111; }
.otp-feedback-message { font-size: 14px; margin: 0; color: #4b5563; line-height: 1.5; }

.reset-pw-modal-card { background: #ffffff; max-width: 450px !important; padding: 30px; border-radius: 12px; border: none; color: #111; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
.reset-pw-title { font-size: 20px; font-weight: bold; margin-bottom: 5px; color: #111; }
.reset-pw-subtitle { font-size: 14px; color: #666; margin-bottom: 25px; }
.reset-pw-label { font-size: 14px; font-weight: bold; color: #111; margin-bottom: 8px; display: block; text-align: left;}
.reset-pw-input-wrap { position: relative; margin-bottom: 15px; }
.reset-pw-input { width: 100%; height: 42px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 0 40px; font-size: 14px; color: #111; outline: none; transition: 0.2s; }
.reset-pw-input:focus { border-color: #b61b2a; }
.reset-pw-icon-left { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #888; font-size: 14px; }
.reset-pw-icon-right { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #888; font-size: 14px; cursor: pointer; transition: 0.2s; }
.reset-pw-icon-right:hover { color: #111; }
.reset-pw-checklist { list-style: none; padding: 0; margin: -5px 0 20px 10px; font-size: 12px; color: #aaa; text-align: left; }
.reset-pw-checklist li { display: flex; align-items: center; gap: 8px; margin-bottom: 5px; transition: color 0.3s; }
.reset-pw-checklist li.valid { color: #22c55e; } 
.reset-pw-checklist li i { font-size: 14px; }
.reset-pw-actions { display: flex; justify-content: space-between; gap: 15px; margin-top: 25px; }
.reset-pw-btn-cancel { flex: 1; background: #fff; border: 1px solid #e5e7eb; color: #111; border-radius: 8px; height: 42px; font-weight: bold; font-size: 14px; cursor: pointer; transition: 0.2s; }
.reset-pw-btn-cancel:hover { background: #f9fafb; }
.reset-pw-btn-submit { flex: 1; background: #d1d5db; color: #fff; border: none; border-radius: 8px; height: 42px; font-weight: bold; font-size: 14px; cursor: not-allowed; transition: 0.2s;}
.reset-pw-btn-submit.active { background: #b61b2a; cursor: pointer; }
.reset-pw-btn-submit.active:hover { background: #9b1724; }

/* =========================================
   DARK MODE CSS OVERRIDES
   ========================================= */
body.dark-mode, body.dark-mode .main { background-color: #121212 !important; color: #e0e0e0 !important; }
body.dark-mode .topbar, body.dark-mode .sidebar, body.dark-mode .floating-card, body.dark-mode .custom-card, body.dark-mode .report-stat-card, body.dark-mode .report-chart-box, body.dark-mode .custom-modal-card:not(.otp-modal-card):not(.reset-pw-modal-card):not(.add-payment-modal-card), body.dark-mode .notification-panel, body.dark-mode .setting-box, body.dark-mode .mini-stat-card, body.dark-mode .new-acc-card { background-color: #1e1e1e !important; border-color: #333 !important; box-shadow: none !important; }
body.dark-mode h3, body.dark-mode h4, body.dark-mode h5, body.dark-mode h6, body.dark-mode p, body.dark-mode .label, body.dark-mode .value, body.dark-mode td, body.dark-mode th, body.dark-mode .report-value, body.dark-mode .text-dark, body.dark-mode .sidebar-profile h5, body.dark-mode .m-val, body.dark-mode .acc-header-icon { color: #f8f9fa !important; }
body.dark-mode .text-muted, body.dark-mode .report-label, body.dark-mode small, body.dark-mode .sidebar-profile small, body.dark-mode .m-lbl { color: #9ca3af !important; }
body.dark-mode .custom-table th, body.dark-mode .sidebar-menu li.active { background-color: #2d2d2d !important; color: #f8f9fa !important; }
body.dark-mode .custom-table tbody tr:hover, body.dark-mode .sidebar-menu li:hover:not(.active), body.dark-mode .setting-box:hover { background-color: #2d2d2d !important; }
body.dark-mode .topbar-search, body.dark-mode input:not(.add-payment-input):not(.new-acc-input):not(.otp-box):not(.reset-pw-input), body.dark-mode select:not(.add-payment-input), body.dark-mode .form-control { background-color: #2d2d2d !important; color: #f8f9fa !important; border-color: #444 !important; }
body.dark-mode .sidebar-menu li i { color: #9ca3af; }
body.dark-mode .sidebar-menu li.active i { color: #f8f9fa; }
body.dark-mode .report-indicator.text-green { color: #4ade80 !important; }
body.dark-mode .report-indicator.text-red { color: #f87171 !important; }
body.dark-mode .report-flat-table td { border-bottom-color: #333 !important; }
body.dark-mode .summary-card .label, body.dark-mode .summary-card .value { color: #ffffff !important; }
body.dark-mode #darkModeBtn { background: #2d2d2d !important; color: #f8f9fa !important; border-color: #444 !important; }
body.dark-mode .type-toggle-btn { background: #2d2d2d; border-color: #444; color: #e0e0e0; }
body.dark-mode .type-toggle-btn.active-1 { background: rgba(239, 68, 68, 0.2); border-color: #ef4444; color: #fca5a5; }
body.dark-mode .type-toggle-btn.active-2 { background: rgba(59, 130, 246, 0.2); border-color: #3b82f6; color: #93c5fd; }
body.dark-mode .back-btn { color: #e0e0e0; }
body.dark-mode .back-btn:hover { background: #333; color: #fff; }
body.dark-mode .back-btn-ui { color: #e0e0e0; }
body.dark-mode .back-btn-ui:hover { background: #333; color: #fff; }
body.dark-mode .btn-light { background-color: #2d2d2d !important; color: #e0e0e0 !important; border-color: #444 !important; } 
body.dark-mode .action-icon-btn:hover { background-color: #333 !important; }
body.dark-mode .report-dropdown-menu { background: #1e1e1e; border-color: #333; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
body.dark-mode .report-dropdown-item { color: #e0e0e0; }
body.dark-mode .report-dropdown-item:hover { background: #2d2d2d; color: #fff; }
body.dark-mode .trans-filter-divider { background: #333; }

/* Dark mode for the Account UI */
body.dark-mode .new-acc-input { background: #2d2d2d; border-color: #444; color: #fff; }
body.dark-mode .new-acc-btn-upload { background: #2d2d2d; border-color: #444; color: #fff; }
body.dark-mode .new-acc-btn-upload:hover { background: #333; }
body.dark-mode .new-acc-btn-delete { background: #444; color: #ccc; }
body.dark-mode .new-acc-edit { background: #1e1e1e; border-color: #ef4444; color: #ef4444; }
body.dark-mode .new-acc-edit:hover { background: #ef4444; color: #fff; }
body.dark-mode hr.border-secondary { border-color: #444 !important; opacity: 1;}

/* Dark Mode for Security Card */
body.dark-mode .sec-change-pw-box { background: #2d2d2d; border-color: #333; }
body.dark-mode .sec-change-pw-box i { color: #e0e0e0 !important; }
body.dark-mode .sec-change-pw-box span { color: #fff !important; }
body.dark-mode .sec-btn-update { background: #4b5563; color: #e0e0e0; }
body.dark-mode .sec-btn-update:hover { background: #374151; }
body.dark-mode .sec-login-box { background: #2d2d2d; border-color: #333; }
body.dark-mode .sec-login-title { color: #fff !important; }
body.dark-mode .sec-divider { background: #444; }
body.dark-mode .sec-table th { color: #e0e0e0 !important; border-bottom-color: #444; }
body.dark-mode .sec-table td { color: #aaa; }

/* Dark Mode Overrides for Modals */
body.dark-mode .add-payment-modal-card { background: #1e1e1e; color: #fff; border-color: #333; }
body.dark-mode .add-payment-modal-icon-container { color: #fff; }
body.dark-mode .add-payment-modal-check-icon { background: #4ade80; border-color: #1e1e1e; color: #1e1e1e; }
body.dark-mode .add-payment-modal-title { color: #fff; }
body.dark-mode .add-payment-label { color: #aaa; }
body.dark-mode .add-payment-input { background: #2d2d2d; border-color: #444; color: #fff; }
body.dark-mode .add-payment-input[readonly] { background: #1a1a1a; border-color: #333; color: #777; }
body.dark-mode .add-payment-btn-clear { background: #333; color: #ccc; border: none; }
body.dark-mode .add-payment-btn-clear:hover { background: #444; }

body.dark-mode .otp-modal-card { background: #1c1c1c; color: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
body.dark-mode .otp-title { color: #fff; }
body.dark-mode .otp-subtitle { color: #aaa; }
body.dark-mode .otp-subtitle span { color: #fff; }
body.dark-mode .otp-box { background: #2d2d2d; border-color: #444; color: #fff; }
body.dark-mode .otp-box:focus { background: #333; border-color: #ef4444; box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2); }
body.dark-mode .otp-feedback-modal-card { background: #1c1c1c; color: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
body.dark-mode .otp-feedback-title { color: #fff; }
body.dark-mode .otp-feedback-message { color: #d1d5db; }

body.dark-mode .reset-pw-modal-card { background: #1c1c1c; color: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
body.dark-mode .reset-pw-title { color: #fff; }
body.dark-mode .reset-pw-subtitle { color: #aaa; }
body.dark-mode .reset-pw-label { color: #fff; }
body.dark-mode .reset-pw-input { background: #2d2d2d; border-color: #444; color: #fff; }
body.dark-mode .reset-pw-input:focus { border-color: #ef4444; }
body.dark-mode .reset-pw-btn-cancel { background: #2d2d2d; border-color: #444; color: #fff; }
body.dark-mode .reset-pw-btn-submit { background: #444; color: #888; }
body.dark-mode .reset-pw-btn-submit.active { background: #ef4444; color: #fff; }
body.dark-mode .reset-pw-checklist { color: #666; }

/* =========================================
   CROP PROFILE PICTURE MODAL
   ========================================= */
.crop-modal-card {
    background: #1a1a1a !important; 
    border: 1px solid #b61b2a !important; 
    max-width: 420px !important;
    padding: 0 !important; 
    border-radius: 12px;
    color: #fff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.8);
    overflow: hidden;
}
.crop-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #333;
}
.crop-title {
    font-size: 11px;
    font-weight: bold;
    letter-spacing: 1px;
    margin: 0;
    color: #fff;
    text-transform: uppercase;
}
.crop-close-btn {
    background: #2d2d2d;
    border: 1px solid #b61b2a;
    color: #555;
    border-radius: 6px;
    width: 28px;
    height: 28px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    font-size: 14px;
    transition: 0.2s;
}
.crop-close-btn:hover {
    background: #b61b2a;
    color: #fff;
}
.crop-body {
    padding: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
}
.crop-image-container {
    position: relative;
    width: 320px;
    height: 320px;
    background: #333;
    border-radius: 4px;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
}
.crop-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.85;
}
.crop-overlay-circle {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 260px;
    height: 260px;
    border: 3px dotted #ffffff;
    border-radius: 50%;
    box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.4); /* Dims the outside of the circle */
    pointer-events: none; 
}
.crop-footer {
    padding: 0 25px 25px 25px;
}
.crop-btn-submit {
    width: 100%;
    background: #b61b2a;
    color: #fff;
    border: none;
    border-radius: 8px;
    height: 42px;
    font-weight: bold;
    font-size: 11px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    cursor: pointer;
    transition: 0.2s;
}
.crop-btn-submit:hover {
    background: #9b1724;
}

.back-to-top-btn {
    position: fixed;
    right: 22px;
    bottom: 22px;
    width: 46px;
    height: 46px;
    border: none;
    border-radius: 999px;
    background: #b61b2a;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 20px rgba(182, 27, 42, 0.3);
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transform: translateY(8px);
    transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease, background-color 0.2s ease;
    z-index: 1200;
}

.back-to-top-btn.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.back-to-top-btn:hover {
    background: #9b1724;
}

body.dark-mode .back-to-top-btn {
    background: #ef4444;
    box-shadow: 0 10px 20px rgba(239, 68, 68, 0.25);
}

body.dark-mode .back-to-top-btn:hover {
    background: #dc2626;
}

/* =========================================
   RESPONSIVE MEDIA QUERIES
   ========================================= */

/* Mobile Layout (< 768px) */
@media (max-width: 768px) {
    /* Hide topbar search on mobile */
    .topbar-search-wrapper { display: none !important; }
    
    /* Show sidebar toggle button */
    .sidebar-toggle { display: block !important; }
    
    /* Collapse sidebar on mobile */
    .sidebar {
        width: 0;
        transform: translateX(-250px);
    }
    
    .sidebar.active {
        width: 250px;
        transform: translateX(0);
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    
    /* Adjust main content when sidebar hidden */
    .main {
        margin-left: 0;
        padding: clamp(12px, 3vw, 20px);
    }
    
    /* Hide logo span on very small screens */
    .topbar a > div > span { display: none; }
    
    /* Adjust modal for mobile */
    .custom-modal-overlay { left: 0 !important; width: 100% !important; }
    .custom-modal-card { width: 95% !important; max-width: 100% !important; }

    /* Process payment modal: compact mobile card instead of near full-screen */
    #proofModal .custom-modal-card {
        width: 88vw !important;
        max-width: 420px !important;
        max-height: 82vh;
        overflow-y: auto;
        padding: 18px !important;
        border-radius: 12px;
    }

    #proofModal .modal-img-wrapper {
        min-height: 160px;
        margin-bottom: 12px !important;
    }

    #proofModal .modal-img-wrapper img {
        max-height: 220px;
    }

    #proofModal .proof-type-grid {
        flex-direction: column;
        gap: 10px;
    }

    #proofModal .type-toggle-btn {
        width: 100%;
        min-height: 72px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        padding: 10px;
    }

    #proofModal .type-toggle-btn small {
        font-size: 12px;
    }
    
    /* Stack notification panel */
    .notification-panel { width: 90vw !important; right: 5vw !important; left: auto !important; }
}

/* Tablet Layout (768px - 1024px) */
@media (min-width: 769px) and (max-width: 1024px) {
    .summary-card { flex-direction: row; justify-content: space-between; }
    .reports-grid { grid-template-columns: repeat(2, 1fr); }
    .mini-card-container { flex-wrap: wrap; justify-content: center; }
}

/* Very Small Phones (< 480px) */
@media (max-width: 480px) {
    /* Make action buttons wrap better */
    .action-btn { margin-bottom: 4px; }
    
    /* Stack table horizontally scrollable */
    .table-card { padding: 8px; }
    
    /* Reduce button sizes */
    .btn-small { padding: 4px 8px !important; font-size: 10px !important; }
    
    /* Make cards full width */
    .floating-card { width: 100%; }
    
    /* Reduce modal padding on tiny screens */
    .custom-modal-card { padding: 15px !important; }

    #proofModal .custom-modal-card {
        width: 90vw !important;
        max-width: 360px !important;
    }

    .back-to-top-btn {
        right: 14px;
        bottom: 14px;
        width: 42px;
        height: 42px;
    }
}

/* Landscape Mode */
@media (max-height: 600px) {
    .topbar { height: 50px !important; }
    .sidebar { top: 50px !important; height: calc(100vh - 50px) !important; }
    .main { margin-top: 50px !important; }
}

/* Touch-friendly UI */
@media (hover: none) and (pointer: coarse) {
    /* Increase touch target sizes */
    .btn, .action-btn, .action-icon-btn, .sidebar-menu li { min-height: 44px !important; }
    
    /* Add more spacing for touch */
    .status-badge { padding: 6px 12px !important; }
}

</style>

{{-- TOP NAVIGATION --}}
<div class="topbar">
    <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
    <a href="{{ url('/') }}" class="d-flex align-items-center gap-3 text-decoration-none" style="outline: none; box-shadow: none;">
        <div class="rounded-circle overflow-hidden" style="width: 40px; height: 40px; border: 1px solid #e5e7eb;">
            <img src="{{ asset('images/PCCI-Logo.svg') }}" alt="PCCI Logo" class="w-100 h-100 object-fit-contain p-1">
        </div>
        <div class="d-flex flex-column">
            <span class="fw-bold text-dark" style="font-family: 'Poppins', sans-serif; font-size: 1rem; line-height: 1.2;">PCCI - Valenzuela</span>
        </div>
    </a>
    
    <div class="topbar-search-wrapper">
        <i class="fa fa-search"></i>
        <input type="text" class="topbar-search" placeholder="Search records, members...">
    </div>

    <div class="topbar-actions">
        <button class="btn btn-sm border d-flex align-items-center gap-2 text-muted fw-bold px-3 py-1" onclick="toggleDarkMode()" id="darkModeBtn" style="border-radius: 50rem; background: #f9fafb; font-size: 13px;">
            <i class="fa fa-moon" id="darkModeIcon"></i> <span id="darkModeText">Dark Mode</span>
        </button>

        <div class="position-relative" onclick="toggleNotificationPanel(event)" style="cursor:pointer; display: flex; align-items: center; margin-left: 10px;">
            <i class="fa fa-bell fs-5 text-muted"></i>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="margin-top: 4px; margin-left: -5px; display: none;"></span>
        </div>
        <img src="{{ asset('images/PCCI-Logo.svg') }}" class="topbar-avatar ms-3" id="topbarAvatar" alt="User">
    </div>
</div>

{{-- NOTIFICATION PANEL --}} 
<div class="notification-panel" id="notificationPanel">     
    <div class="notif-header">         
        <h6 class="notif-header-title">Notifications <span class="notif-badge" id="notifBadge">0 New</span></h6>         
        <button class="notif-clear-btn" onclick="clearNotifications(event)"><i class="fa fa-times"></i></button>     
    </div>     
    <div class="notif-body" id="notifBody">         
        <div class="notif-item" style="background: #f9fafb;">             
            <div class="notif-icon" style="background: white; border: 1px solid #ddd;"><i class="fa fa-info-circle text-primary fs-5"></i></div>             
            <div class="notif-text-content">                 
                <p>No notifications yet.</p>             
            </div>         
        </div>     
    </div>     
    <div class="notif-footer d-flex justify-content-between align-items-center p-2" style="background: #f8f9fb; border-top: 1px solid #e5e7eb;">         
        <span class="text-primary fw-bold" style="cursor:pointer; font-size: 12px;" onclick="openFullNotificationsModal()">             
            <i class="fa fa-expand me-1"></i> View All         
        </span>         
        <span class="text-success fw-bold" style="cursor:pointer; font-size: 12px;" onclick="markAllNotificationsAsRead()">             
            <i class="fa fa-check-double me-1"></i> Mark all read         
        </span>     
    </div> 
</div>

{{-- SIDEBAR --}}
<div class="sidebar">
    <div class="sidebar-profile">
        <img src="{{ asset('images/PCCI-Logo.svg') }}" id="sidebarAvatar" alt="Profile">
        <h5 id="sidebarName">Loading...</h5>
        <small id="sidebarEmail">Loading...</small>
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
    @include('treasurer.tabs.dashboard-tab')
    @include('treasurer.tabs.members-tab')
    @include('treasurer.tabs.applicants-tab')
    @include('treasurer.tabs.transactions-tab')
    @include('treasurer.tabs.reports-tab')
    @include('treasurer.tabs.settings-tab')
</div>

{{-- MODALS --}}
@include('treasurer.tabs.modals')

<button class="back-to-top-btn" id="treasurerBackToTop" aria-label="Back to top">
    <i class="fa fa-arrow-up"></i>
</button>

{{-- FULLSCREEN / LARGE NOTIFICATIONS MODAL --}}
<div class="modal fade" id="fullNotificationsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background-color: #b61b2a; color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa fa-bell me-2"></i> All Notifications</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="fullNotificationsBody" style="background-color: #f3f4f6;">
                <!-- JavaScript will inject all notifications here -->
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIC --}}
@include('treasurer.tabs.scripts')

<script>
    (function () {
        const backToTopBtn = document.getElementById('treasurerBackToTop');
        if (!backToTopBtn) return;

        function currentScrollTop() {
            return window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
        }

        function toggleBackToTop() {
            backToTopBtn.classList.toggle('show', currentScrollTop() > 220);
        }

        window.addEventListener('scroll', toggleBackToTop, { passive: true });
        backToTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        toggleBackToTop();
    })();
</script>
@endsection


