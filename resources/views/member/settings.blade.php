<style>
    /* Table Styling for Member Dashboard */
    .table-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .custom-table thead th {
        background: #f8f9fb;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 15px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .custom-table tbody td {
        padding: 15px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 13px;
        vertical-align: middle;
    }

    .custom-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }
</style>

<div id="section-settings" class="content-section" style="display: none;">
    <div class="titleBox"><i class="fa fa-gear"></i> Settings</div>

    <div class="custom-card" style="gap: 14px;">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <h5 class="fw-bold mb-1">Account Options</h5>
                <p class="text-muted mb-0" style="font-size: 14px;">Open a sector modal to update each part of your account.</p>
            </div>
            <button class="logout-btn mt-0" onclick="logout()"><i class="fa fa-sign-out-alt me-2"></i> Log Out</button>
        </div>

        <div class="setting-box" onclick="openSettingsModal('account')">
            <div class="setting-left"><i class="fa fa-user text-secondary"></i><span>Account</span></div>
            <i class="fa fa-chevron-right text-muted"></i>
        </div>
        <div class="setting-box" onclick="openSettingsModal('security')">
            <div class="setting-left"><i class="fa fa-lock text-secondary"></i><span>Security</span></div>
            <i class="fa fa-chevron-right text-muted"></i>
        </div>
        <div class="setting-box" onclick="openSettingsModal('billing')">
            <div class="setting-left"><i class="fa fa-credit-card text-secondary"></i><span>Membership and Billing</span></div>
            <i class="fa fa-chevron-right text-muted"></i>
        </div>
        <div class="setting-box" onclick="openSettingsModal('preferences')">
            <div class="setting-left"><i class="fa fa-sliders-h text-secondary"></i><span>Preferences</span></div>
            <i class="fa fa-chevron-right text-muted"></i>
        </div>
    </div>
</div>

<div class="modal-overlay" id="settingsModalAccount">
    <div class="modal-content-box" style="max-width: 840px; padding: 0; border-radius: 18px;">
        <div style="padding: 18px 24px 10px; display: flex; align-items: center; gap: 14px;">
            <button class="btn btn-link p-0 text-dark" style="font-size: 22px; text-decoration: none;" onclick="closeSettingsModal('account')"><i class="fa fa-chevron-left"></i></button>
            <div class="d-flex align-items-center gap-3">
                <i class="fa fa-user-gear" style="font-size: 32px; color: #111827;"></i>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Account</h5>
                    <p class="text-muted mb-0" style="font-size: 12px;">Manage your company profile and personal account information.</p>
                </div>
            </div>
        </div>
        <div style="padding: 18px 24px 24px;">
            <div class="custom-card mb-4" style="border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('images/PCCI-Logo.svg') }}" alt="Profile" style="width: 66px; height: 66px; border-radius: 50%; object-fit: cover; border: 1px solid #e5e7eb; padding: 4px; background: #fff;">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Profile Picture</h6>
                            <small class="text-muted">PNG, JPG under 5MB</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-light btn-sm fw-bold" style="border: 1px solid #d1d5db;" onclick="openCropModal()">Upload new photo</button>
                        <button class="btn btn-light btn-sm fw-bold" style="border: 1px solid #d1d5db; color: #a3a3a3;" onclick="removeAvatar()">Delete</button>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <h6 class="fw-bold mb-2 text-dark" style="font-size: 14px;">Full Name</h6>
                </div>
                <div class="col-md-6">
                    <label class="text-muted fw-bold mb-1" style="font-size: 12px;">Last Name</label>
                    <input id="settingsLastName" class="form-control" type="text" placeholder="Last name">
                </div>
                <div class="col-md-6">
                    <label class="text-muted fw-bold mb-1" style="font-size: 12px;">First Name</label>
                    <input id="settingsFirstName" class="form-control" type="text" placeholder="First name">
                </div>
                <div class="col-12 mt-2">
                    <h6 class="fw-bold mb-2 text-dark" style="font-size: 14px;">Contact Details</h6>
                </div>
                <div class="col-md-6">
                    <label class="text-muted fw-bold mb-1" style="font-size: 12px;">Email</label>
                    <input id="settingsEmailInput" class="form-control" type="email" placeholder="Email address">
                </div>
                <div class="col-md-6">
                    <label class="text-muted fw-bold mb-1" style="font-size: 12px;">Contact Number</label>
                    <input id="settingsPhoneInput" class="form-control" type="text" placeholder="Contact number">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button class="btn btn-light fw-bold" onclick="closeSettingsModal('account')">Cancel</button>
                <button class="btn btn-danger fw-bold" onclick="saveSettingsAccount()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="settingsModalSecurity">
    <div class="modal-content-box" style="max-width: 840px; padding: 0; border-radius: 18px;">
        <div style="padding: 18px 24px 10px; display: flex; align-items: center; gap: 14px;">
            <button class="btn btn-link p-0 text-dark" style="font-size: 22px; text-decoration: none;" onclick="closeSettingsModal('security')"><i class="fa fa-chevron-left"></i></button>
            <div class="d-flex align-items-center gap-3">
                <i class="fa fa-lock" style="font-size: 32px; color: #111827;"></i>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Security</h5>
                    <p class="text-muted mb-0" style="font-size: 12px;">Protect your account by managing passwords and security settings.</p>
                </div>
            </div>
        </div>
        <div style="padding: 18px 24px 24px;">
            <div class="custom-card mb-4" style="border-radius: 14px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa fa-lock text-dark" style="font-size: 22px;"></i>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Change Password</h6>
                            <small class="text-muted">Use a strong password for better account safety.</small>
                        </div>
                    </div>
                    <button class="btn btn-light btn-sm fw-bold" style="border: 1px solid #d1d5db;" onclick="saveSettingsSecurity()">Update Password</button>
                </div>
            </div>

            <div class="custom-card mb-0" style="border-radius: 14px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <h6 class="fw-bold mb-3 text-dark">Login Activity</h6>
                <div class="table-responsive">
                    <table class="custom-table mb-0">
                        <thead>
                            <tr>
                                <th>Device</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="settingsLoginActivityTable">
                            <tr>
                                <td colspan="4" class="text-center text-muted">Loading recent activity...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button class="btn btn-light fw-bold" onclick="closeSettingsModal('security')">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="settingsModalBilling">
    <div class="modal-content-box" style="max-width: 900px; padding: 0; border-radius: 18px;">
        <div style="padding: 18px 24px 10px; display: flex; align-items: center; gap: 14px;">
            <button class="btn btn-link p-0 text-dark" style="font-size: 22px; text-decoration: none;" onclick="closeSettingsModal('billing')"><i class="fa fa-chevron-left"></i></button>
            <div class="d-flex align-items-center gap-3">
                <i class="fa fa-credit-card" style="font-size: 30px; color: #111827;"></i>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Membership and Billing</h5>
                    <p class="text-muted mb-0" style="font-size: 12px;">Manage your Membership and Billing</p>
                </div>
            </div>
        </div>
        <div style="padding: 18px 24px 24px;">
            <div id="renewal-banner" class="alert alert-danger d-none align-items-center justify-content-between mb-4 shadow-sm" style="border-radius: 12px; border-left: 5px solid #dc3545;">
                <div class="d-flex align-items-center">
                    <i class="fa fa-exclamation-triangle fs-3 me-3 text-danger"></i>
                    <div>
                        <h5 class="fw-bold mb-1 text-danger" style="font-family: 'Poppins', sans-serif;">Membership Inactive</h5>
                        <p class="mb-0 text-dark" style="font-size: 13px;">Your PCCI membership has expired or is pending. Please submit your renewal payment to restore your active status.</p>
                    </div>
                </div>
                <button class="btn btn-danger fw-bold rounded-pill px-4 shadow-sm" onclick="openRenewalModal()">Renew Now</button>
            </div>

            <div class="custom-card mb-4" style="border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <h6 class="fw-bold mb-3 text-dark">Membership Details</h6>
                <div class="row g-3 align-items-center">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('images/PCCI-Logo.svg') }}" alt="Profile" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 1px solid #e5e7eb; padding: 4px; background: #fff;">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark" id="billingCompanyName">Loading...</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge rounded-pill" id="billingStatusBadge" style="background: #dcfce7; color: #15803d; font-size: 11px; font-weight: bold;">Loading...</span>
                                    <small class="text-dark fw-bold" id="billingIndustryLabel">Loading...</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Inactive Date:</span>
                            <span class="text-dark" id="billingExpiryDate">Loading...</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Membership Type:</span>
                            <span class="text-dark" id="billingPlanLabel">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="custom-card mb-0" style="border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <h6 class="fw-bold mb-3 text-dark">Billing & Transaction History</h6>
                <div class="table-responsive">
                    <table class="custom-table mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>OR Number</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="billingSessionsTable">
                            <tr>
                                <td colspan="5" class="text-center text-muted">Loading history...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button class="btn btn-light fw-bold" onclick="closeSettingsModal('billing')">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="settingsModalPreferences">
    <div class="modal-content-box" style="max-width: 840px; padding: 0; border-radius: 18px;">
        <div style="padding: 18px 24px 10px; display: flex; align-items: center; gap: 14px;">
            <button class="btn btn-link p-0 text-dark" style="font-size: 22px; text-decoration: none;" onclick="closeSettingsModal('preferences')"><i class="fa fa-chevron-left"></i></button>
            <div class="d-flex align-items-center gap-3">
                <i class="fa fa-gear" style="font-size: 30px; color: #111827;"></i>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Preferences</h5>
                    <p class="text-muted mb-0" style="font-size: 12px;">Customize your notifications and communication preferences.</p>
                </div>
            </div>
        </div>
        <div style="padding: 18px 24px 24px;">
            <div class="d-grid gap-4">
                <div>
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Email Notification</h6>
                            <p class="text-muted mb-0" style="font-size: 13px;">Receive alerts about upcoming PCCI</p>
                        </div>
                        <div class="form-check form-switch fs-4 m-0">
                            <input class="form-check-input" type="checkbox" id="emailNotificationsSwitch" checked style="cursor: pointer;">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Event Announcement</h6>
                            <p class="text-muted mb-0" style="font-size: 13px;">Receive alerts about upcoming PCCI Events</p>
                        </div>
                        <div class="form-check form-switch fs-4 m-0">
                            <input class="form-check-input" type="checkbox" id="eventAnnouncementSwitch" checked style="cursor: pointer;">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Membership Expiry Reminder</h6>
                            <p class="text-muted mb-0" style="font-size: 13px;">Get reminders when your membership is about to Expire</p>
                        </div>
                        <div class="form-check form-switch fs-4 m-0">
                            <input class="form-check-input" type="checkbox" id="membershipReminderSwitch" checked style="cursor: pointer;">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Theme</h6>
                            <p class="text-muted mb-0" style="font-size: 13px;">Customize the theme of the website</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 110px; border: 1px solid #d1d5db;">
                                <span id="themeLabel">Light</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><button class="dropdown-item" type="button" onclick="setMemberTheme('light')">Light</button></li>
                                <li><button class="dropdown-item" type="button" onclick="setMemberTheme('dark')">Dark</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button class="btn btn-light fw-bold" onclick="closeSettingsModal('preferences')">Cancel</button>
                <button class="btn btn-danger fw-bold" onclick="saveSettingsPreferences()">Save Preferences</button>
            </div>
        </div>
    </div>
</div>

<style>
    #memberOtpModal,
    #memberResetPasswordModal {
        justify-content: center !important;
        align-items: center !important;
        padding: 0;
    }

    #memberOtpModal .modal-content-box,
    #memberResetPasswordModal .modal-content-box {
        margin: 0 !important;
    }

    /* Keep modal scrolling behavior but hide scrollbar UI for cleaner layout */
    #settingsModalAccount,
    #settingsModalSecurity,
    #settingsModalBilling,
    #settingsModalPreferences,
    #memberOtpModal,
    #memberResetPasswordModal,
    #cropPhotoModal,
    #settingsModalAccount .modal-content-box,
    #settingsModalSecurity .modal-content-box,
    #settingsModalBilling .modal-content-box,
    #settingsModalPreferences .modal-content-box,
    #memberOtpModal .modal-content-box,
    #memberResetPasswordModal .modal-content-box,
    #cropPhotoModal .modal-content-box {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    #settingsModalAccount::-webkit-scrollbar,
    #settingsModalSecurity::-webkit-scrollbar,
    #settingsModalBilling::-webkit-scrollbar,
    #settingsModalPreferences::-webkit-scrollbar,
    #memberOtpModal::-webkit-scrollbar,
    #memberResetPasswordModal::-webkit-scrollbar,
    #cropPhotoModal::-webkit-scrollbar,
    #settingsModalAccount .modal-content-box::-webkit-scrollbar,
    #settingsModalSecurity .modal-content-box::-webkit-scrollbar,
    #settingsModalBilling .modal-content-box::-webkit-scrollbar,
    #settingsModalPreferences .modal-content-box::-webkit-scrollbar,
    #memberOtpModal .modal-content-box::-webkit-scrollbar,
    #memberResetPasswordModal .modal-content-box::-webkit-scrollbar,
    #cropPhotoModal .modal-content-box::-webkit-scrollbar {
        width: 0;
        height: 0;
        display: none;
    }
</style>

{{-- OTP Modal for Password Reset --}}
<div class="modal-overlay" id="memberOtpModal" style="display: none; background: rgba(0, 0, 0, 0.6); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 2060; justify-content: center; align-items: center;">
    <div class="modal-content-box" style="max-width: 450px; padding: 0; border-radius: 18px; background: #fff; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);">
        <div style="padding: 24px; display: flex; justify-content: space-between; border-bottom: 1px solid #e5e7eb;">
            <div>
                <h5 class="fw-bold mb-1 text-dark" style="font-size: 18px;">Verify Your Identity</h5>
                <p class="text-muted mb-0" style="font-size: 13px;">Enter the 6-digit code sent to <span class="fw-bold" id="memberOtpEmail">Loading...</span></p>
            </div>
            <button class="btn btn-link p-0 text-dark" onclick="closeMemberOtpModal()"><i class="fa fa-times"></i></button>
        </div>
        <div style="padding: 32px 24px; text-align: center;">
            <div style="display: flex; gap: 8px; justify-content: center; margin-bottom: 20px;">
                <input type="text" maxlength="1" class="member-otp-box" oninput="moveMemberOtpNext(this, event)" style="width: 48px; height: 48px; font-size: 20px; font-weight: bold; text-align: center; border: 2px solid #d1d5db; border-radius: 8px; transition: all 0.2s;">
                <input type="text" maxlength="1" class="member-otp-box" oninput="moveMemberOtpNext(this, event)" style="width: 48px; height: 48px; font-size: 20px; font-weight: bold; text-align: center; border: 2px solid #d1d5db; border-radius: 8px; transition: all 0.2s;">
                <input type="text" maxlength="1" class="member-otp-box" oninput="moveMemberOtpNext(this, event)" style="width: 48px; height: 48px; font-size: 20px; font-weight: bold; text-align: center; border: 2px solid #d1d5db; border-radius: 8px; transition: all 0.2s;">
                <input type="text" maxlength="1" class="member-otp-box" oninput="moveMemberOtpNext(this, event)" style="width: 48px; height: 48px; font-size: 20px; font-weight: bold; text-align: center; border: 2px solid #d1d5db; border-radius: 8px; transition: all 0.2s;">
                <input type="text" maxlength="1" class="member-otp-box" oninput="moveMemberOtpNext(this, event)" style="width: 48px; height: 48px; font-size: 20px; font-weight: bold; text-align: center; border: 2px solid #d1d5db; border-radius: 8px; transition: all 0.2s;">
                <input type="text" maxlength="1" class="member-otp-box" oninput="moveMemberOtpNext(this, event)" style="width: 48px; height: 48px; font-size: 20px; font-weight: bold; text-align: center; border: 2px solid #d1d5db; border-radius: 8px; transition: all 0.2s;">
            </div>
            <small class="text-muted">Didn't receive? <button class="btn btn-link p-0 text-danger" style="text-decoration: none; font-size: 12px;">Resend code</button></small>
        </div>
    </div>
</div>

{{-- Reset Password Modal --}}
<div class="modal-overlay" id="memberResetPasswordModal" style="display: none; background: rgba(0, 0, 0, 0.6); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 2070; justify-content: center; align-items: center; overflow-y: auto;">
    <div class="modal-content-box" style="max-width: 500px; padding: 0; border-radius: 18px; background: #fff; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);">
        <div style="padding: 24px; text-align: center; border-bottom: 1px solid #e5e7eb; position: relative;">
            <button class="btn btn-link p-0 text-dark" style="position: absolute; right: 24px; top: 24px;" onclick="closeMemberResetPasswordModal()"><i class="fa fa-times"></i></button>
            <h5 class="fw-bold mb-1 text-dark" style="font-size: 18px;">Set New Password</h5>
            <p class="text-muted mb-0" style="font-size: 13px;">Create a strong password for your account</p>
        </div>
        <div style="padding: 24px;">
            <label class="fw-bold text-dark mb-2 d-block" style="font-size: 13px;">New Password</label>
            <div style="position: relative; margin-bottom: 16px;">
                <input type="password" class="form-control" id="memberNewPasswordInput" placeholder="Enter new password" oninput="validateMemberPassword()" style="padding-left: 38px; border-radius: 8px; border: 1px solid #d1d5db;">
                <i class="fa fa-key" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                <i class="fa fa-eye" onclick="toggleMemberPasswordView('memberNewPasswordInput')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6b7280;"></i>
            </div>

            <ul style="list-style: none; padding: 0; margin: 16px 0; background: #f9fafb; padding: 16px; border-radius: 8px;">
                <li id="memberReqLower" style="margin-bottom: 8px; font-size: 12px; color: #9ca3af; transition: color 0.3s;"><i class="fa fa-check-circle" style="margin-right: 8px;"></i> At least one lowercase letter</li>
                <li id="memberReqLen" style="margin-bottom: 8px; font-size: 12px; color: #9ca3af; transition: color 0.3s;"><i class="fa fa-check-circle" style="margin-right: 8px;"></i> Minimum 8 characters</li>
                <li id="memberReqUpper" style="margin-bottom: 8px; font-size: 12px; color: #9ca3af; transition: color 0.3s;"><i class="fa fa-check-circle" style="margin-right: 8px;"></i> At least one uppercase letter</li>
                <li id="memberReqNum" style="font-size: 12px; color: #9ca3af; transition: color 0.3s;"><i class="fa fa-check-circle" style="margin-right: 8px;"></i> At least one number</li>
            </ul>

            <label class="fw-bold text-dark mb-2 d-block" style="font-size: 13px; margin-top: 20px;">Confirm Password</label>
            <div style="position: relative; margin-bottom: 24px;">
                <input type="password" class="form-control" id="memberRePasswordInput" placeholder="Confirm password" style="padding-left: 38px; border-radius: 8px; border: 1px solid #d1d5db;">
                <i class="fa fa-key" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                <i class="fa fa-eye" onclick="toggleMemberPasswordView('memberRePasswordInput')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6b7280;"></i>
            </div>

            <div style="display: flex; gap: 12px;">
                <button class="btn btn-light fw-bold" style="flex: 1;" onclick="closeMemberResetPasswordModal()">Cancel</button>
                <button class="btn btn-danger fw-bold" id="memberResetPwSubmitBtn" style="flex: 1; opacity: 0.5; cursor: not-allowed;" onclick="submitMemberNewPassword()">Reset Password</button>
            </div>
        </div>
    </div>
</div>

{{-- Crop Photo Modal (Cropper.js) --}}
<div class="modal-overlay" id="cropPhotoModal">
    <div class="modal-content-box" style="max-width: 500px; padding: 0; border-radius: 18px; background: #fff;">
        <div style="padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e5e7eb;">
            <h5 class="fw-bold mb-0 text-dark text-uppercase" style="font-size: 14px;">Crop Profile Picture</h5>
            <button class="btn btn-link p-0 text-dark" onclick="closeCropModal()"><i class="fa fa-times"></i></button>
        </div>

        <div style="padding: 20px; background: #000; text-align: center;">
            <div style="max-height: 400px; overflow: hidden; display: flex; justify-content: center;">
                {{-- CRITICAL: This ID must exactly match what Javascript uses --}}
                <img id="cropperImage" src="" style="max-width: 100%; display: block;">
            </div>
        </div>

        <div style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 10px;">
            <button class="btn btn-light fw-bold" onclick="closeCropModal()">Cancel</button>
            <button class="btn btn-danger fw-bold" onclick="applyCrop()"><i class="fa fa-crop-simple me-2"></i> Apply Crop</button>
        </div>
    </div>
</div>

<input type="file" id="photoFileInput" accept="image/*" style="display: none;" onchange="handlePhotoSelect(event)">

<div class="modal-overlay" id="renewalModal" style="display: none; background: rgba(0, 0, 0, 0.6); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 2090; justify-content: center; align-items: center;">
    <div class="modal-content-box" style="max-width: 500px; width: 100%; padding: 0; border-radius: 18px; background: #fff; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);">
        <div style="padding: 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e5e7eb;">
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 18px;">
                <i class="fa fa-file-invoice-dollar me-2 text-danger"></i> Submit Renewal Payment
            </h5>
            <button class="btn btn-link p-0 text-dark" style="font-size: 18px; text-decoration: none;" onclick="closeRenewalModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="renewalForm" onsubmit="submitRenewalPayment(event)" style="padding: 24px;">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="fw-bold text-dark mb-2" style="font-size: 13px;">Payment Method <span class="text-danger">*</span></label>
                    <select id="renewalPaymentMethod" class="form-control" style="border-radius: 8px; border: 1px solid #d1d5db; padding: 10px;" required>
                        <option value="">Select...</option>
                        <option value="gcash">GCash</option>
                        <option value="transfer">Bank Transfer</option>
                        <option value="cash">Cash (Walk-in)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold text-dark mb-2" style="font-size: 13px;">Amount Due (₱) <span class="text-danger">*</span></label>
                    <input type="number" id="renewalAmount" class="form-control" style="border-radius: 8px; border: 1px solid #d1d5db; padding: 10px; background-color: #f3f4f6; font-weight: bold;" readonly required>
                </div>
            </div>

            <div class="mb-3">
                <label class="fw-bold text-dark mb-2" style="font-size: 13px;">Reference / Trace Number <span class="text-danger">*</span></label>
                <input type="text" id="renewalReference" class="form-control" placeholder="Enter transaction reference number" style="border-radius: 8px; border: 1px solid #d1d5db; padding: 10px;" required>
            </div>

            <div class="mb-4">
                <label class="fw-bold text-dark mb-2" style="font-size: 13px;">Proof of Payment (Screenshot) <span class="text-danger">*</span></label>
                <input type="file" id="renewalProofFile" accept="image/*" class="form-control" style="border-radius: 8px; border: 1px solid #d1d5db; padding: 10px;" required>
                <small class="text-muted mt-1 d-block">Please upload a clear screenshot of your transaction (Max: 10MB).</small>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 10px;">
                <button type="button" class="btn btn-light fw-bold" style="flex: 1; border: 1px solid #d1d5db;" onclick="closeRenewalModal()">Cancel</button>
                <button type="submit" class="btn btn-danger fw-bold" id="renewalSubmitBtn" style="flex: 1;">Submit Payment</button>
            </div>
        </form>

    </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
    async function openRenewalModal() {
        document.getElementById('renewalModal').style.display = 'flex';
        const amountInput = document.getElementById('renewalAmount');
        amountInput.value = '';
        amountInput.placeholder = 'Calculating...';

        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/member/renewal-status`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            if (data.amount_due) {
                amountInput.value = data.amount_due;
            } else {
                const profile = window.currentProfileData || {};
                const typeId = String(profile.membership_type_id || profile.applicant?.membership_type_id || profile.member?.membership_type_id || '');
                amountInput.value = (typeId === '2' || profile.membershipType?.name === 'Small Enterprise') ? 3000 : 500;
            }
        } catch (error) {
            amountInput.value = 500;
        }
    }

    function closeRenewalModal() {
        document.getElementById('renewalModal').style.display = 'none';
        document.getElementById('renewalForm').reset();
    }

    async function submitRenewalPayment(event) {
        event.preventDefault();
        const method = document.getElementById('renewalPaymentMethod').value;
        const amount = document.getElementById('renewalAmount').value;
        const reference = document.getElementById('renewalReference').value;
        const file = document.getElementById('renewalProofFile').files[0];
        const btn = document.getElementById('renewalSubmitBtn');
        const token = localStorage.getItem('token');

        if (!method || !amount || !reference || !file) {
            alert('Please fill out all fields and upload your receipt.');
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            alert('File size exceeds 10MB.');
            return;
        }

        const formData = new FormData();
        formData.append('payment_method', method);
        formData.append('amount', amount);
        formData.append('reference_number', reference);
        formData.append('or_number', reference);
        formData.append('proof_of_payment', file);

        try {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Submitting...';
            const endpointBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');

            const response = await fetch(`${endpointBase}/v1/member/request-payment`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            const result = await readMemberApiResponse(response);
            if (!response.ok) throw new Error(result.data?.message || result.raw || 'Failed to submit renewal payment.');

            alert('Success: Your payment request has been submitted to the Treasurer for review!');
            closeRenewalModal();
            fetchMyBillingHistory();
        } catch (error) {
            alert(error.message || 'An error occurred while submitting your payment.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Submit Payment';
        }
    }

    // ========================================
    // AVATAR CROPPING & UPLOAD LOGIC
    // ========================================

    window.cropper = null;
    window.accountImageFile = null;
    window.sessionAvatarUrl = null; // Add this global protector variable

    function openCropModal() {
        document.getElementById('photoFileInput').click();
    }

    function closeCropModal() {
        document.getElementById('cropPhotoModal').style.display = 'none';
        if (window.cropper) {
            window.cropper.destroy();
            window.cropper = null;
        }
    }

    function handlePhotoSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            alert('File size exceeds 5MB. Please select a smaller image.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('cropPhotoModal').style.display = 'flex';

            const img = document.getElementById('cropperImage');
            img.src = e.target.result;

            if (window.cropper) window.cropper.destroy();
            window.cropper = new Cropper(img, {
                aspectRatio: 1,
                viewMode: 1
            });
        };
        reader.readAsDataURL(file);
        event.target.value = '';
    }

    function applyCrop() {
        if (!window.cropper) return;

        window.cropper.getCroppedCanvas({
            width: 500,
            height: 500
        }).toBlob((blob) => {
            window.accountImageFile = new File([blob], "member_profile.jpg", {
                type: "image/jpeg"
            });

            const previewUrl = URL.createObjectURL(window.accountImageFile);

            document.querySelectorAll('img[alt="Profile"]').forEach(img => {
                img.src = previewUrl;
            });

            const sidebarAvatar = document.querySelector('.sidebar .avatar');
            if (sidebarAvatar) sidebarAvatar.src = previewUrl;

            closeCropModal();
        }, 'image/jpeg', 0.9);
    }

    async function removeAvatar() {
        if (!confirm("Are you sure you want to remove your profile photo?")) return;

        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/user/avatar`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                alert('Avatar removed successfully.');
                const defaultAvatar = "{{ asset('images/PCCI-Logo.svg') }}";
                document.querySelectorAll('img[alt="Profile"]').forEach(img => img.src = defaultAvatar);

                const sidebarAvatar = document.querySelector('.sidebar .avatar');
                if (sidebarAvatar) sidebarAvatar.src = defaultAvatar;

                window.accountImageFile = null;
                window.sessionAvatarUrl = null; // Clear the protected preview

                await fetchMyBillingHistory();
            } else {
                console.error("Failed to delete avatar");
                alert('Failed to delete avatar from the server.');
            }
        } catch (err) {
            console.error('Error:', err);
        }
    }

    window.cropper = null;
    window.accountImageFile = null;
    window.sessionAvatarUrl = null; // Add this global protector variable

    function applyCrop() {
        if (!window.cropper) return;

        window.cropper.getCroppedCanvas({
            width: 500,
            height: 500
        }).toBlob((blob) => {
            window.accountImageFile = new File([blob], "member_profile.jpg", {
                type: "image/jpeg"
            });

            const previewUrl = URL.createObjectURL(window.accountImageFile);
            window.sessionAvatarUrl = previewUrl; // Protect the local preview

            document.querySelectorAll('img[alt="Profile"]').forEach(img => {
                img.src = previewUrl;
            });

            const sidebarAvatar = document.querySelector('.sidebar .avatar');
            if (sidebarAvatar) sidebarAvatar.src = previewUrl;

            closeCropModal();
        }, 'image/jpeg', 0.9);
    }

    async function fetchMyBillingHistory() {
        try {
            const [profileRes, transRes] = await Promise.all([
                fetch(`${window.API_BASE_URL}/v1/member/profile`, {
                    method: 'GET', // <--- MISSING
                    cache: 'no-store', // <--- CRITICAL: Forces browser to get fresh data
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                }),
                fetch(`${window.API_BASE_URL}/v1/member/payments`, {
                    method: 'GET', // <--- MISSING
                    cache: 'no-store', // <--- CRITICAL
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                })
            ]);

            let profile = null;
            let transactions = [];

            if (transRes.ok) {
                const transData = await transRes.json();
                transactions = transData?.data || transData || [];
            }

            if (profileRes.ok) {
                const profileData = await profileRes.json();
                profile = profileData?.data || profileData;
                window.currentProfileData = profile;

                syncSettingsFromProfile(profile);

                renderBillingOverview(profile, transactions);

                const status = String(profile?.membership_status || profile?.member?.status || profile?.status || 'active').toLowerCase();
                const banner = document.getElementById('renewal-banner');

                if (banner) {
                    if (status === 'active' || status === 'approved') {
                        banner.classList.remove('d-flex');
                        banner.classList.add('d-none');
                    } else {
                        banner.classList.remove('d-none');
                        banner.classList.add('d-flex');
                        const sortedTxns = [...transactions].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                        const latestTxn = sortedTxns.length > 0 ? sortedTxns[0] : null;
                        const latestTxnStatus = latestTxn ? String(latestTxn.status).toLowerCase() : '';

                        if (latestTxnStatus === 'pending' || latestTxnStatus === 'pending_review') {
                            banner.className = 'alert alert-warning d-flex align-items-center justify-content-between mb-4 shadow-sm';
                            banner.style.borderLeft = '5px solid #ffc107';
                            banner.innerHTML = `<div class="d-flex align-items-center"><i class="fa fa-clock fs-3 me-3 text-warning"></i><div><h5 class="fw-bold mb-1 text-dark">Payment Under Review</h5><p class="mb-0 text-dark" style="font-size: 13px;">Your payment is currently being reviewed by the Treasurer. Please wait for approval.</p></div></div>`;
                        } else if (latestTxnStatus === 'rejected' || latestTxnStatus === 'failed') {
                            banner.className = 'alert alert-danger d-flex align-items-center justify-content-between mb-4 shadow-sm';
                            banner.style.borderLeft = '5px solid #dc3545';
                            banner.innerHTML = `<div class="d-flex align-items-center"><i class="fa fa-times-circle fs-3 me-3 text-danger"></i><div><h5 class="fw-bold mb-1 text-danger">Payment Rejected</h5><p class="mb-0 text-dark" style="font-size: 13px;">Your previous payment was rejected. Please review your details and re-submit your receipt.</p></div></div><button class="btn btn-danger fw-bold rounded-pill px-4 shadow-sm" onclick="openRenewalModal()">Re-submit Payment</button>`;
                        } else {
                            banner.className = 'alert alert-danger d-flex align-items-center justify-content-between mb-4 shadow-sm';
                            banner.style.borderLeft = '5px solid #dc3545';
                            banner.innerHTML = `<div class="d-flex align-items-center"><i class="fa fa-exclamation-triangle fs-3 me-3 text-danger"></i><div><h5 class="fw-bold mb-1 text-danger">Membership Inactive</h5><p class="mb-0 text-dark" style="font-size: 13px;">Your PCCI membership has expired or is pending. Please submit your renewal payment.</p></div></div><button class="btn btn-danger fw-bold rounded-pill px-4 shadow-sm" onclick="openRenewalModal()">Renew Now</button>`;
                        }
                    }
                }
            }

            const tbody = document.getElementById('billingSessionsTable');
            if (!tbody) return;
            tbody.innerHTML = '';

            if (transactions.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4 fw-bold">No billing history found.</td></tr>`;
                return;
            }

            transactions.forEach(txn => {
                const date = (txn.created_at || '').split('T')[0] || 'N/A';
                const type = txn.transaction_type === 'initial_registration' ? 'Registration' : 'Renewal';
                const amount = `₱ ${parseFloat(txn.amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2})}`;
                const orNum = txn.or_number || '---';
                const rawStatus = String(txn.status || 'pending').toLowerCase();
                let statBadge = '';
                if (rawStatus === 'pending' || rawStatus === 'pending_review') statBadge = '<span style="color: #b45309; background: #fef3c7; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">PENDING</span>';
                else if (rawStatus === 'approved' || rawStatus === 'paid' || rawStatus === 'completed') statBadge = '<span style="color: #15803d; background: #dcfce7; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">APPROVED</span>';
                else statBadge = '<span style="color: #b91c1c; background: #fee2e2; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">FAILED</span>';

                tbody.insertAdjacentHTML('beforeend', `<tr><td class="text-dark">${date}</td><td class="text-dark fw-bold">${type}</td><td class="text-dark fw-bold">${amount}</td><td class="text-dark fw-bold">${orNum}</td><td>${statBadge}</td></tr>`);
            });
        } catch (error) {
            console.error("Error fetching billing history:", error);
        }
    }

    function renderBillingOverview(profileData, transactions = []) {
        if (!profileData || Object.keys(profileData).length === 0) return;
        const member = profileData.member || profileData.data?.member || profileData;
        const basic = member.applicant?.basic_profile || member.basic_profile || {};
        const org = member.applicant?.organization_membership || member.organization_membership || {};
        const companyName = basic.registered_business_name || 'Unknown Business';
        const industry = org.type_of_company || 'Not Specified';
        const status = String(member.status || profileData.membership_status || 'pending').toLowerCase();

        // Shorten membership type name to tier label
        function shortenMembershipType(name) {
            if (!name || name === 'N/A') return name;
            const lower = name.toLowerCase();
            if (lower.includes('micro')) return 'Micro';
            if (lower.includes('small')) return 'Small';
            if (lower.includes('medium')) return 'Medium';
            if (lower.includes('large')) return 'Large';
            if (lower.includes('lifetime')) return 'Lifetime';
            if (lower.includes('regular')) return 'Regular';
            return name;
        }

        let typeName = 'N/A';
        if (member.membershipType && member.membershipType.name) {
            typeName = shortenMembershipType(member.membershipType.name);
        } else if (member.membership_type && member.membership_type.name) {
            typeName = shortenMembershipType(member.membership_type.name);
        }

        // Use membership_end_date from API directly — reflects the correct duration
        // per membership tier (Micro, Small, Regular, Lifetime, etc.)
        const rawEndDate = member.membership_end_date ||
            profileData.membership_end_date ||
            null;

        let calculatedExpiry = 'N/A';
        if (rawEndDate) {
            const d = new Date(rawEndDate);
            if (!Number.isNaN(d.getTime())) {
                calculatedExpiry = d.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
        } else {
            // Fallback: calculate from induction_date only if end date is missing
            const baseDate = member.induction_date || member.applicant?.induction_date || member.created_at;
            if (baseDate) {
                const d = new Date(baseDate);
                if (!Number.isNaN(d.getTime())) {
                    d.setFullYear(d.getFullYear() + 1);
                    calculatedExpiry = d.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }
            }
        }

        const memTxns = transactions.filter(t => t.transaction_type === 'renewal' || t.transaction_type === 'initial_registration');
        memTxns.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        const latestTxn = memTxns.length > 0 ? memTxns[0] : null;

        let dateLabel = 'Inactive Date:';
        let dateValue = 'N/A';
        if (status === 'inactive' || status === 'expired') {
            dateLabel = 'Inactive Date';
            dateValue = calculatedExpiry;
        } else if (status === 'pending') {
            dateLabel = 'Transaction Date';
            dateValue = latestTxn ? new Date(latestTxn.created_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : 'N/A';
        } else {
            dateLabel = 'Expires';
            dateValue = calculatedExpiry;
        }

        const companyEl = document.getElementById('billingCompanyName');
        const industryEl = document.getElementById('billingIndustryLabel');
        const expiryEl = document.getElementById('billingExpiryDate');
        const planEl = document.getElementById('billingPlanLabel');
        const badgeEl = document.getElementById('billingStatusBadge');

        if (companyEl) companyEl.innerText = companyName;
        if (industryEl) industryEl.innerText = industry;
        if (planEl) planEl.innerText = typeName;
        if (expiryEl) {
            expiryEl.innerText = dateValue;
            if (expiryEl.previousElementSibling) expiryEl.previousElementSibling.innerText = dateLabel;
        }
        if (badgeEl) {
            badgeEl.innerText = status.toUpperCase();
            const ok = ['approved', 'active', 'paid'].includes(status);
            badgeEl.style.background = ok ? '#dcfce7' : '#fee2e2';
            badgeEl.style.color = ok ? '#15803d' : '#b91c1c';
        }
    }

    function populateSettingsAccountForm(profile) {
        if (!profile) return;

        // ── Resolve the profile shape from /v1/member/profile response ────────
        // Response: { member: { user: {...}, applicant: { basic_profile:{...}, official_representative:{...} } } }
        const member = profile.member || profile.data?.member || profile || {};
        const user = member.user || profile.user || {};

        // applicant is nested inside member, NOT the top-level profile
        const applicant = member.applicant || {};

        // Safe nested object extractor (handles JSON-string values from older endpoints)
        function safeObj(val) {
            if (!val) return {};
            if (typeof val === 'string') {
                try {
                    return JSON.parse(val);
                } catch (e) {
                    return {};
                }
            }
            return (typeof val === 'object' && !Array.isArray(val)) ? val : {};
        }

        const basic = safeObj(applicant.basic_profile);
        const rep = safeObj(applicant.official_representative);

        const firstEl = document.getElementById('settingsFirstName');
        const lastEl = document.getElementById('settingsLastName');
        const emailEl = document.getElementById('settingsEmailInput');
        const phoneEl = document.getElementById('settingsPhoneInput');

        const firstName = user.first_name || rep.first_name || applicant.rep_first_name || '';
        const lastName = user.last_name || rep.surname || applicant.rep_surname || '';
        const email = user.email || basic.email || applicant.email || '';

        // Phone priority:
        // 1. user.contact_number  — saved via UserController::changeInfo (users table)
        // 2. basic.telephone_no   — business landline from applicant.basic_profile
        // 3. rep.contact_no       — rep mobile from applicant.official_representative
        // 4. applicant.telephone_no — flat fallback on applicant object
        // NOTE: user.contact_number is now always returned by UserResource (after the fix).
        //       If it's empty, fall through to the applicant's telephone_no.
        const phone =
            user.contact_number // users table — primary (UserResource now returns this)
            ||
            basic.telephone_no // applicant basic_profile landline
            ||
            rep.contact_no // applicant official_representative mobile
            ||
            applicant.telephone_no // flat fallback
            ||
            '';

        if (firstEl) firstEl.value = firstName;
        if (lastEl) lastEl.value = lastName;
        if (emailEl) emailEl.value = email;
        if (phoneEl) phoneEl.value = phone;

        // ── Avatar ────────────────────────────────────────────────────────────
        const avatarUrl = user.photo_url || user.profile_photo_url || user.profile_photo_path;
        if (avatarUrl) {
            const finalUrl = avatarUrl.startsWith('http') ?
                avatarUrl :
                `${(window.API_BASE_URL || '').replace('/api', '')}/storage/${avatarUrl}`;

            document.querySelectorAll('img[alt="Profile"]').forEach(img => {
                img.src = finalUrl;
            });

            const sidebarAvatar = document.querySelector('.sidebar .avatar');
            if (sidebarAvatar) sidebarAvatar.src = finalUrl;
        }
    }

    function syncSettingsFromProfile(profile) {
        populateSettingsAccountForm(profile);
        renderSettingsLoginActivity(profile);
        renderBillingOverview(profile);
        renderBillingSessions(profile);
    }

    function openSettingsModal(sector) {
        const modal = document.getElementById(`settingsModal${sector.charAt(0).toUpperCase() + sector.slice(1)}`);
        if (!modal) return;
        modal.style.display = 'flex';

        if (sector === 'account') {
            populateSettingsAccountForm(window.currentProfileData || {});
        } else if (sector === 'billing') {
            fetchMyBillingHistory();
        } else if (sector === 'preferences') {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const themeLabel = document.getElementById('themeLabel');
            if (themeLabel) themeLabel.innerText = savedTheme === 'dark' ? 'Dark' : 'Light';
        } else if (sector === 'security') {
            renderSettingsLoginActivity();
        }
    }

    function closeSettingsModal(sector) {
        const modal = document.getElementById(`settingsModal${sector.charAt(0).toUpperCase() + sector.slice(1)}`);
        if (modal) modal.style.display = 'none';
    }

    async function saveSettingsAccount() {
        const firstName = document.getElementById('settingsFirstName')?.value?.trim() || '';
        const lastName = document.getElementById('settingsLastName')?.value?.trim() || '';
        const email = document.getElementById('settingsEmailInput')?.value?.trim() || '';
        const phone = document.getElementById('settingsPhoneInput')?.value?.trim() || '';

        const formData = new FormData();
        formData.append('_method', 'PUT');
        if (firstName) formData.append('first_name', firstName);
        if (lastName) formData.append('last_name', lastName);
        if (email) formData.append('email', email);
        if (phone) formData.append('contact_number', phone);

        if (window.accountImageFile) {
            formData.append('image', window.accountImageFile);
        }

        try {
            const btn = document.querySelector('#settingsModalAccount .btn-danger');
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Saving...';
            btn.disabled = true;

            const response = await fetch(`${window.API_BASE_URL}/v1/user/change-info`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
                body: formData
            });

            if (response.ok) {
                const responseData = await response.json();
                alert('Account updated successfully!');
                window.accountImageFile = null;

                // Update the local cache so reopening the modal shows the saved values.
                // responseData.user now includes contact_number (UserResource fix).
                if (window.currentProfileData) {
                    // Handle both response shapes: { member: { user } } and { user }
                    const target = window.currentProfileData.member ?
                        window.currentProfileData.member :
                        window.currentProfileData;

                    target.user = Object.assign(target.user || {}, responseData.user || {});
                }

                // Force a hard refresh from the server to confirm everything is in sync
                await fetchMyBillingHistory();
                closeSettingsModal('account');

            } else {
                const data = await response.json();
                alert('Failed to update account: ' + (data.message || 'Validation error'));
            }
        } catch (error) {
            console.error(error);
            alert('Network error while saving account.');
        } finally {
            const btn = document.querySelector('#settingsModalAccount .btn-danger');
            btn.innerText = 'Save Changes';
            btn.disabled = false;
        }
    }

    function saveSettingsSecurity() {
        requestPasswordChangeOtp();
    }

    function saveSettingsPreferences() {
        const emailNotifications = document.getElementById('emailNotificationsSwitch')?.checked ?? true;
        const eventAnnouncement = document.getElementById('eventAnnouncementSwitch')?.checked ?? true;
        const membershipReminder = document.getElementById('membershipReminderSwitch')?.checked ?? true;
        localStorage.setItem('memberEmailNotifications', emailNotifications ? '1' : '0');
        localStorage.setItem('memberEventAnnouncements', eventAnnouncement ? '1' : '0');
        localStorage.setItem('memberMembershipReminder', membershipReminder ? '1' : '0');
        alert('Preferences saved locally.');
        closeSettingsModal('preferences');
    }

    function setMemberTheme(theme) {
        if (typeof window.applyMemberTheme === 'function') {
            window.applyMemberTheme(theme);
            return;
        }
        localStorage.setItem('theme', theme);
        const themeLabel = document.getElementById('themeLabel');
        if (themeLabel) themeLabel.innerText = theme === 'dark' ? 'Dark' : 'Light';
    }

    function toggleDarkMode() {
        const currentTheme = localStorage.getItem('theme') || 'light';
        setMemberTheme(currentTheme === 'dark' ? 'light' : 'dark');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        const themeLabel = document.getElementById('themeLabel');
        if (themeLabel) themeLabel.innerText = savedTheme === 'dark' ? 'Dark' : 'Light';
        const profile = window.currentProfileData || null;
        if (profile) syncSettingsFromProfile(profile);
        fetchMyBillingHistory();
    });

    // ========================================
    // OTP & PASSWORD RESET MODAL FUNCTIONS
    // ========================================

    let currentMemberOtpCode = '';
    let currentMemberOtpEmail = '';

    async function requestPasswordChangeOtp() {
        const token = localStorage.getItem('token');
        const otpApiBase = (window.PCCI_API_BASE_URL || window.API_BASE_URL || '').replace(/\/$/, '');
        if (!token) {
            alert('Session expired. Please login again.');
            return;
        }

        const candidateEmail = (
            document.getElementById('settingsEmailInput')?.value ||
            window.currentProfileData?.basic_profile?.email ||
            localStorage.getItem('userEmail') ||
            ''
        ).trim();

        try {
            const response = await fetch(`${otpApiBase}/user/confirm-password-change`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({
                    email: candidateEmail || null
                })
            });

            const {
                data,
                raw
            } = await readMemberApiResponse(response);
            if (!response.ok) throw new Error(data.message || raw || 'Failed to request OTP.');

            const otpPayload = data?.data || {};
            currentMemberOtpCode = otpPayload.otp ? String(otpPayload.otp) : '';
            currentMemberOtpEmail = (otpPayload.email || candidateEmail || '').trim();
            if (!currentMemberOtpEmail) throw new Error('OTP response missing email.');

            openMemberOtpModal(currentMemberOtpEmail);
            alert(data.message || 'OTP has been sent to your email.');
        } catch (error) {
            alert(error?.message || 'Failed to request OTP. Please try again.');
        }
    }

    function openMemberOtpModal(emailOverride = '') {
        const userEmail = emailOverride || (window.currentProfileData?.basic_profile?.email || 'N/A');
        document.getElementById('memberOtpEmail').textContent = userEmail;
        document.getElementById('memberOtpModal').style.display = 'flex';
    }

    function closeMemberOtpModal() {
        document.getElementById('memberOtpModal').style.display = 'none';
        document.querySelectorAll('.member-otp-box').forEach(box => box.value = '');
    }

    function moveMemberOtpNext(input, event) {
        input.value = String(input.value || '').replace(/\D/g, '').slice(-1);
        if (input.value.length === 1) {
            const next = input.nextElementSibling;
            if (next && next.classList.contains('member-otp-box')) {
                next.focus();
            } else {
                currentMemberOtpCode = Array.from(document.querySelectorAll('.member-otp-box')).map(box => box.value).join('');
                verifyMemberOtpAndProceed();
            }
        }
    }

    function pasteOtpIntoMemberBoxes(event) {
        event.preventDefault();
        const pasted = (event.clipboardData?.getData('text') || '').replace(/\D/g, '');
        if (!pasted) return;

        const boxes = Array.from(document.querySelectorAll('.member-otp-box'));
        const digits = pasted.slice(0, boxes.length).split('');
        boxes.forEach((box, index) => {
            box.value = digits[index] || '';
        });

        currentMemberOtpCode = boxes.map(box => box.value).join('');
        const firstEmpty = boxes.find(box => !box.value);
        if (firstEmpty) firstEmpty.focus();
        else verifyMemberOtpAndProceed();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const otpBoxes = document.querySelectorAll('.member-otp-box');
        otpBoxes.forEach(box => {
            box.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value) {
                    const prev = this.previousElementSibling;
                    if (prev && prev.classList.contains('member-otp-box')) prev.focus();
                }
            });
            box.addEventListener('paste', pasteOtpIntoMemberBoxes);
            box.addEventListener('focus', function() {
                this.style.borderColor = '#3b82f6';
                this.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.1)';
            });
            box.addEventListener('blur', function() {
                this.style.borderColor = '#d1d5db';
                this.style.boxShadow = 'none';
            });
        });
    });

    async function verifyMemberOtpAndProceed() {
        if (!currentMemberOtpCode || currentMemberOtpCode.length !== 6) {
            alert('Please enter a valid 6-digit OTP.');
            return;
        }
        closeMemberOtpModal();
        openMemberResetPasswordModal();
    }

    function openMemberResetPasswordModal() {
        document.getElementById('memberResetPasswordModal').style.display = 'flex';
    }

    function closeMemberResetPasswordModal() {
        document.getElementById('memberResetPasswordModal').style.display = 'none';
        document.getElementById('memberNewPasswordInput').value = '';
        document.getElementById('memberRePasswordInput').value = '';
        validateMemberPassword();
    }

    function toggleMemberPasswordView(inputId) {
        const input = document.getElementById(inputId);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function validateMemberPassword() {
        const pw = document.getElementById('memberNewPasswordInput').value;
        const reqLower = document.getElementById('memberReqLower');
        const reqLen = document.getElementById('memberReqLen');
        const reqUpper = document.getElementById('memberReqUpper');
        const reqNum = document.getElementById('memberReqNum');
        const submitBtn = document.getElementById('memberResetPwSubmitBtn');

        let validCount = 0;
        if (/[a-z]/.test(pw)) {
            reqLower.style.color = '#22c55e';
            validCount++;
        } else {
            reqLower.style.color = '#9ca3af';
        }
        if (pw.length >= 8) {
            reqLen.style.color = '#22c55e';
            validCount++;
        } else {
            reqLen.style.color = '#9ca3af';
        }
        if (/[A-Z]/.test(pw)) {
            reqUpper.style.color = '#22c55e';
            validCount++;
        } else {
            reqUpper.style.color = '#9ca3af';
        }
        if (/[0-9]/.test(pw)) {
            reqNum.style.color = '#22c55e';
            validCount++;
        } else {
            reqNum.style.color = '#9ca3af';
        }

        if (validCount === 4) {
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        } else {
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        }
    }

    async function submitMemberNewPassword() {
        const pw1 = document.getElementById('memberNewPasswordInput')?.value || '';
        const pw2 = document.getElementById('memberRePasswordInput')?.value || '';
        const btn = document.getElementById('memberResetPwSubmitBtn');
        const token = localStorage.getItem('token');
        const otpApiBase = (window.PCCI_API_BASE_URL || window.API_BASE_URL || '').replace(/\/$/, '');

        if (btn && btn.style.opacity === '0.5') {
            alert('Please ensure your password meets all security requirements.');
            return;
        }
        if (!token) {
            alert('Session expired. Please login again.');
            return;
        }
        if (pw1 !== pw2) {
            alert('Passwords do not match!');
            return;
        }
        if (!currentMemberOtpCode || currentMemberOtpCode.length !== 6) {
            alert('OTP is missing or invalid. Please request and verify OTP again.');
            return;
        }

        try {
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Resetting...';
            }

            const response = await fetch(`${otpApiBase}/user/request-password-change`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({
                    otp: currentMemberOtpCode,
                    email: currentMemberOtpEmail || null,
                    new_password: pw1,
                    new_password_confirmation: pw2,
                    password: pw1,
                    password_confirmation: pw2,
                })
            });

            const {
                data,
                raw
            } = await readMemberApiResponse(response);
            if (!response.ok) throw new Error(data.message || raw || 'Failed to reset password.');

            alert(data.message || 'Password successfully reset!');
            currentMemberOtpCode = '';
            closeMemberResetPasswordModal();
        } catch (error) {
            alert(error?.message || 'Failed to reset password. Please try again.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'Reset Password';
            }
        }
    }

    function formatMemberDateTime(value) {
        if (!value) return 'N/A';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return 'N/A';
        return d.toLocaleString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
    }

    function renderSettingsLoginActivity() {
        const tbody = document.getElementById('settingsLoginActivityTable');
        if (!tbody) return;

        const userAgent = navigator.userAgent;
        let browser = "Web Browser";
        if (userAgent.includes("Edg")) browser = "Microsoft Edge";
        else if (userAgent.includes("Chrome")) browser = "Google Chrome";
        else if (userAgent.includes("Firefox")) browser = "Mozilla Firefox";
        else if (userAgent.includes("Safari") && !userAgent.includes("Chrome")) browser = "Apple Safari";

        let os = "Unknown Device";
        if (userAgent.includes("Win")) os = "Windows PC";
        else if (userAgent.includes("Mac")) os = "Mac OS";
        else if (userAgent.includes("Android")) os = "Android Device";
        else if (userAgent.includes("iPhone") || userAgent.includes("iPad")) os = "iOS Device";

        const now = new Date().toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        tbody.innerHTML = `<tr><td class="fw-bold text-dark"><i class="fa fa-laptop text-muted me-2"></i> ${os} - ${browser}</td><td class="text-dark">Current Active Session</td><td class="text-dark">${now}</td><td><span style="color: #15803d; background: #dcfce7; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Online Now</span></td></tr>`;
    }

    function renderBillingSessions(profile) {
        const tbody = document.getElementById('billingSessionsTable');
        if (!tbody) return;

        const receiptNo = profile?.membership_receipt_no || profile?.official_receipt_no || profile?.receipt_no || profile?.or_number || 'N/A';
        const location = profile?.basic_profile?.business_location?.city_municipality || 'N/A';
        const dateText = formatMemberDateTime(profile?.date_approved || profile?.updated_at || profile?.created_at);

        tbody.innerHTML = `<tr><td>${receiptNo}</td><td>${location}</td><td>${dateText}</td><td><span style="color:#22c55e;">Recorded</span></td><td><button class="btn btn-light btn-sm" style="border: 1px solid #d1d5db;" disabled>Download</button></td></tr>`;
    }

    async function readMemberApiResponse(response) {
        const contentType = (response.headers.get('content-type') || '').toLowerCase();
        if (contentType.includes('application/json')) {
            return {
                data: await response.json().catch(() => ({})),
                raw: ''
            };
        }
        return {
            data: {},
            raw: await response.text().catch(() => '')
        };
    }
</script>