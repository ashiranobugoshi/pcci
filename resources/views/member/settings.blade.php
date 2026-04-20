{{-- ========================================== --}}
{{-- SETTINGS TAB                               --}}
{{-- ========================================== --}}
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

<div class="modal-overlay" id="settingsModalAccount" onclick="handleSettingsModalOverlay(event, 'account')">
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
                        <button class="btn btn-light btn-sm fw-bold" style="border: 1px solid #d1d5db; color: #a3a3a3;">Delete</button>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <h6 class="fw-bold mb-2 text-dark" style="font-size: 14px;">Full Name</h6>
                </div>
                <div class="col-md-6">
                    <label class="text-muted fw-bold mb-1" style="font-size: 12px;">Last Name</label>
                    <div class="position-relative">
                        <input id="settingsLastName" class="form-control" type="text" placeholder="Last name">
                        <button class="btn btn-sm btn-outline-secondary position-absolute" style="top: 50%; right: 8px; transform: translateY(-50%); font-size: 11px; padding: 2px 8px;">Edit</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted fw-bold mb-1" style="font-size: 12px;">First Name</label>
                    <div class="position-relative">
                        <input id="settingsFirstName" class="form-control" type="text" placeholder="First name">
                        <button class="btn btn-sm btn-outline-secondary position-absolute" style="top: 50%; right: 8px; transform: translateY(-50%); font-size: 11px; padding: 2px 8px;">Edit</button>
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <h6 class="fw-bold mb-2 text-dark" style="font-size: 14px;">Contact Email</h6>
                </div>
                <div class="col-md-6">
                    <label class="text-muted fw-bold mb-1" style="font-size: 12px;">Email</label>
                    <div class="position-relative">
                        <input id="settingsEmailInput" class="form-control" type="email" placeholder="Email address">
                        <button class="btn btn-sm btn-outline-secondary position-absolute" style="top: 50%; right: 8px; transform: translateY(-50%); font-size: 11px; padding: 2px 8px;">Edit</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted fw-bold mb-1" style="font-size: 12px;">Contact Number</label>
                    <div class="position-relative">
                        <input id="settingsPhoneInput" class="form-control" type="text" placeholder="Contact number">
                        <button class="btn btn-sm btn-outline-secondary position-absolute" style="top: 50%; right: 8px; transform: translateY(-50%); font-size: 11px; padding: 2px 8px;">Edit</button>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button class="btn btn-light fw-bold" onclick="closeSettingsModal('account')">Cancel</button>
                <button class="btn btn-danger fw-bold" onclick="saveSettingsAccount()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="settingsModalSecurity" onclick="handleSettingsModalOverlay(event, 'security')">
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
                <button class="btn btn-light fw-bold" onclick="closeSettingsModal('security')">Cancel</button>
                <button class="btn btn-danger fw-bold" onclick="saveSettingsSecurity()">Update Password</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="settingsModalBilling" onclick="handleSettingsModalOverlay(event, 'billing')">
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
                            <span class="text-muted">Expires Date</span>
                            <span class="text-dark" id="billingExpiryDate">Loading...</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Membership Type</span>
                            <span class="text-dark" id="billingPlanLabel">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="custom-card mb-0" style="border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <h6 class="fw-bold mb-3 text-dark">Sessions</h6>
                <div class="table-responsive">
                    <table class="custom-table mb-0">
                        <thead>
                            <tr>
                                <th>OR Reg. No.</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="billingSessionsTable">
                            <tr>
                                <td colspan="5" class="text-center text-muted">No billing sessions available yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button class="btn btn-light fw-bold" onclick="closeSettingsModal('billing')">Close</button>
                <button class="btn btn-danger fw-bold" onclick="switchTab('membership'); closeSettingsModal('billing');">Open Membership Plans</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="settingsModalPreferences" onclick="handleSettingsModalOverlay(event, 'preferences')">
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
<div class="modal-overlay" id="memberOtpModal" onclick="closeMemberOtpOverlay(event)" style="display: none; background: rgba(0, 0, 0, 0.6); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 2060; justify-content: center; align-items: center;">
    <div class="modal-content-box" style="max-width: 450px; padding: 0; border-radius: 18px; background: #fff; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);" onclick="event.stopPropagation()">
        <div style="padding: 24px; text-align: center; border-bottom: 1px solid #e5e7eb;">
            <h5 class="fw-bold mb-1 text-dark" style="font-size: 18px;">Verify Your Identity</h5>
            <p class="text-muted mb-0" style="font-size: 13px;">Enter the 6-digit code sent to <span class="fw-bold" id="memberOtpEmail">Loading...</span></p>
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
<div class="modal-overlay" id="memberResetPasswordModal" onclick="closeMemberResetPasswordOverlay(event)" style="display: none; background: rgba(0, 0, 0, 0.6); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 2070; justify-content: center; align-items: center; overflow-y: auto;">
    <div class="modal-content-box" style="max-width: 500px; padding: 0; border-radius: 18px; background: #fff; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);" onclick="event.stopPropagation()">
        <div style="padding: 24px; text-align: center; border-bottom: 1px solid #e5e7eb;">
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

{{-- Crop Photo Modal --}}
<div class="modal-overlay" id="cropPhotoModal" onclick="handleCropModalOverlay(event)">
    <div class="modal-content-box" style="max-width: 500px; padding: 0; border-radius: 18px; background: #f5f5f5;">
        <div style="padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; background: #fff; border-bottom: 1px solid #e5e7eb; border-radius: 18px 18px 0 0;">
            <h5 class="fw-bold mb-0 text-dark" style="text-transform: uppercase; font-size: 14px; letter-spacing: 0.5px;">Crop Your New Profile Picture</h5>
            <button class="btn btn-link p-0 text-dark" style="font-size: 18px; text-decoration: none;" onclick="closeCropModal()"><i class="fa fa-times"></i></button>
        </div>
        <div style="padding: 20px; background: #f5f5f5; display: flex; flex-direction: column; align-items: center;">
            <div style="position: relative; width: 100%; max-width: 400px; aspect-ratio: 1; overflow: hidden; border-radius: 8px; background: #ddd;">
                <img id="cropImagePreview" src="" style="width: 100%; height: 100%; object-fit: cover; display: block;" alt="Crop preview">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 80%; height: 80%; border: 2px dashed #999; border-radius: 50%; pointer-events: none;"></div>
            </div>
            <div style="margin-top: 16px; padding: 0 20px; max-width: 100%;">
                <input type="range" id="cropZoom" class="form-range" min="0" max="100" value="50" style="width: 100%; cursor: pointer;">
                <small class="text-muted d-block text-center mt-2">Drag to adjust crop area</small>
            </div>
        </div>
        <div style="padding: 16px 24px; background: #fff; border-top: 1px solid #e5e7eb; border-radius: 0 0 18px 18px; display: flex; justify-content: flex-end;">
            <button class="btn btn-danger fw-bold" style="min-width: 200px;" onclick="uploadCroppedPhoto()">Upload Profile Picture</button>
        </div>
    </div>
</div>

<input type="file" id="photoFileInput" accept="image/*" style="display: none;" onchange="handlePhotoSelect(event)">

<script>
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

function renderSettingsLoginActivity(profile) {
    const tbody = document.getElementById('settingsLoginActivityTable');
    if (!tbody) return;

    const recentDate = profile?.updated_at || profile?.date_approved || profile?.created_at;
    tbody.innerHTML = `
        <tr>
            <td>${navigator.platform || 'Current Device'} - ${navigator.userAgent.includes('Chrome') ? 'Chrome' : 'Browser'}</td>
            <td>${profile?.basic_profile?.business_location?.city_municipality || 'Unknown location'}</td>
            <td>${formatMemberDateTime(recentDate)}</td>
            <td><span style="color:#22c55e;">Successful</span></td>
        </tr>
    `;
}

function renderBillingSessions(profile) {
    const tbody = document.getElementById('billingSessionsTable');
    if (!tbody) return;

    const receiptNo = profile?.membership_receipt_no || profile?.official_receipt_no || profile?.receipt_no || profile?.or_number || 'N/A';
    const location = profile?.basic_profile?.business_location?.city_municipality || 'N/A';
    const dateText = formatMemberDateTime(profile?.date_approved || profile?.updated_at || profile?.created_at);

    tbody.innerHTML = `
        <tr>
            <td>${receiptNo}</td>
            <td>${location}</td>
            <td>${dateText}</td>
            <td><span style="color:#22c55e;">Recorded</span></td>
            <td><button class="btn btn-light btn-sm" style="border: 1px solid #d1d5db;" disabled>Download</button></td>
        </tr>
    `;
}

function renderBillingOverview(profile) {
    if (!profile) return;

    const basic = profile.basic_profile || {};
    const org = profile.organization_membership || {};
    const companyName = basic.registered_business_name || 'N/A';
    const industry = org.type_of_company || 'N/A';
    const status = (profile.status || 'Pending').toString();

    let expiry = 'Pending Approval';
    if (profile.date_approved) {
        const approved = new Date(profile.date_approved);
        if (!Number.isNaN(approved.getTime())) {
            approved.setFullYear(approved.getFullYear() + 1);
            expiry = approved.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        }
    }

    const companyEl = document.getElementById('billingCompanyName');
    const industryEl = document.getElementById('billingIndustryLabel');
    const expiryEl = document.getElementById('billingExpiryDate');
    const planEl = document.getElementById('billingPlanLabel');
    const badgeEl = document.getElementById('billingStatusBadge');

    if (companyEl) companyEl.innerText = companyName;
    if (industryEl) industryEl.innerText = industry;
    if (expiryEl) expiryEl.innerText = expiry;
    if (planEl) planEl.innerText = profile.membership_type || 'N/A';
    if (badgeEl) {
        badgeEl.innerText = status.toUpperCase();
        const ok = ['approved', 'active'].includes(status.toLowerCase());
        badgeEl.style.background = ok ? '#dcfce7' : '#fef3c7';
        badgeEl.style.color = ok ? '#15803d' : '#b45309';
    }
}

function populateSettingsAccountForm(profile) {
    const basic = profile?.basic_profile || {};
    const rep = profile?.official_representative || {};

    const firstEl = document.getElementById('settingsFirstName');
    const lastEl = document.getElementById('settingsLastName');
    const emailEl = document.getElementById('settingsEmailInput');
    const phoneEl = document.getElementById('settingsPhoneInput');

    if (firstEl) firstEl.value = rep.first_name || '';
    if (lastEl) lastEl.value = rep.surname || '';
    if (emailEl) emailEl.value = basic.email || '';
    if (phoneEl) phoneEl.value = basic.contact_number || basic.telephone_no || '';
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
    }

    if (sector === 'billing') {
        const profile = window.currentProfileData || {};
        renderBillingOverview(profile);
        renderBillingSessions(profile);
    }

    if (sector === 'preferences') {
        const savedTheme = localStorage.getItem('theme') || 'light';
        const themeLabel = document.getElementById('themeLabel');
        if (themeLabel) themeLabel.innerText = savedTheme === 'dark' ? 'Dark' : 'Light';
    }

    if (sector === 'security') {
        renderSettingsLoginActivity(window.currentProfileData || {});
    }
}

function closeSettingsModal(sector) {
    const modal = document.getElementById(`settingsModal${sector.charAt(0).toUpperCase() + sector.slice(1)}`);
    if (modal) modal.style.display = 'none';
}

function handleSettingsModalOverlay(event, sector) {
    if (event.target.id === `settingsModal${sector.charAt(0).toUpperCase() + sector.slice(1)}`) {
        closeSettingsModal(sector);
    }
}

function saveSettingsAccount() {
    const firstName = document.getElementById('settingsFirstName')?.value?.trim() || '';
    const lastName = document.getElementById('settingsLastName')?.value?.trim() || '';
    const email = document.getElementById('settingsEmailInput')?.value?.trim() || '';
    const phone = document.getElementById('settingsPhoneInput')?.value?.trim() || '';
    const fullName = `${firstName} ${lastName}`.trim();

    document.getElementById('sidebarName').innerText = fullName || document.getElementById('sidebarName').innerText;
    document.getElementById('sidebarEmail').innerText = email || document.getElementById('sidebarEmail').innerText;

    if (window.currentProfileData) {
        window.currentProfileData = {
            ...window.currentProfileData,
            basic_profile: {
                ...(window.currentProfileData.basic_profile || {}),
                email: email || (window.currentProfileData.basic_profile || {}).email,
                contact_number: phone || (window.currentProfileData.basic_profile || {}).contact_number,
                telephone_no: phone || (window.currentProfileData.basic_profile || {}).telephone_no
            },
            official_representative: {
                ...(window.currentProfileData.official_representative || {}),
                first_name: firstName || (window.currentProfileData.official_representative || {}).first_name,
                surname: lastName || (window.currentProfileData.official_representative || {}).surname
            }
        };
        localStorage.setItem('member_profile_cache', JSON.stringify(window.currentProfileData));
    }

    if (typeof applyProfileDataToUI === 'function' && window.currentProfileData) {
        applyProfileDataToUI(window.currentProfileData);
    }

    closeSettingsModal('account');
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

document.addEventListener('DOMContentLoaded', function () {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const themeLabel = document.getElementById('themeLabel');
    if (themeLabel) themeLabel.innerText = savedTheme === 'dark' ? 'Dark' : 'Light';
    const profile = window.currentProfileData || null;
    if (profile) syncSettingsFromProfile(profile);
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
        document.getElementById('settingsEmailInput')?.value
        || window.currentProfileData?.basic_profile?.email
        || localStorage.getItem('userEmail')
        || ''
    ).trim();

    try {
        const response = await fetch(`${otpApiBase}/user/confirm-password-change`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
            body: JSON.stringify({ email: candidateEmail || null })
        });

        const { data, raw } = await readMemberApiResponse(response);
        if (!response.ok) {
            throw new Error(data.message || raw || 'Failed to request OTP.');
        }

        const otpPayload = data?.data || {};
        currentMemberOtpCode = otpPayload.otp ? String(otpPayload.otp) : '';
        currentMemberOtpEmail = (otpPayload.email || candidateEmail || '').trim();
        if (!currentMemberOtpEmail) {
            throw new Error('OTP response missing email.');
        }

        openMemberOtpModal(currentMemberOtpEmail);
        alert(data.message || 'OTP has been sent to your email.');
    } catch (error) {
        alert(error?.message || 'Failed to request OTP. Please try again.');
    }
}

function openMemberOtpModal(emailOverride = '') {
    const userEmail = emailOverride || (window.currentProfileData?.basic_profile?.email || 'N/A');
    document.getElementById('memberOtpEmail').textContent = userEmail;
    positionMemberSecurityChildModal('memberOtpModal');
    document.getElementById('memberOtpModal').style.display = 'flex';
}

function closeMemberOtpModal() {
    document.getElementById('memberOtpModal').style.display = 'none';
    document.querySelectorAll('.member-otp-box').forEach(box => box.value = '');
}

function closeMemberOtpOverlay(event) {
    if (event.target.id === 'memberOtpModal') closeMemberOtpModal();
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
    if (firstEmpty) {
        firstEmpty.focus();
    } else {
        verifyMemberOtpAndProceed();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const otpBoxes = document.querySelectorAll('.member-otp-box');
    otpBoxes.forEach(box => {
        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value) {
                const prev = this.previousElementSibling;
                if (prev && prev.classList.contains('member-otp-box')) {
                    prev.focus();
                }
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
    positionMemberSecurityChildModal('memberResetPasswordModal');
    document.getElementById('memberResetPasswordModal').style.display = 'flex';
}

function positionMemberSecurityChildModal(modalId) {
    const modal = document.getElementById(modalId);
    const securityContainer = document.querySelector('#settingsModalSecurity .modal-content-box');
    if (!modal || !securityContainer) return;

    const rect = securityContainer.getBoundingClientRect();

    modal.style.position = 'fixed';
    modal.style.left = `${rect.left}px`;
    modal.style.top = `${rect.top}px`;
    modal.style.width = `${rect.width}px`;
    modal.style.height = `${rect.height}px`;
    modal.style.background = 'rgba(0, 0, 0, 0.45)';
}

function closeMemberResetPasswordModal() {
    document.getElementById('memberResetPasswordModal').style.display = 'none';
    document.getElementById('memberNewPasswordInput').value = '';
    document.getElementById('memberRePasswordInput').value = '';
    validateMemberPassword();
}

function closeMemberResetPasswordOverlay(event) {
    if (event.target.id === 'memberResetPasswordModal') closeMemberResetPasswordModal();
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

    if(/[a-z]/.test(pw)) { 
        reqLower.style.color = '#22c55e';
        validCount++; 
    } else { 
        reqLower.style.color = '#9ca3af';
    }

    if(pw.length >= 8) { 
        reqLen.style.color = '#22c55e';
        validCount++; 
    } else { 
        reqLen.style.color = '#9ca3af';
    }

    if(/[A-Z]/.test(pw)) { 
        reqUpper.style.color = '#22c55e';
        validCount++; 
    } else { 
        reqUpper.style.color = '#9ca3af';
    }

    if(/[0-9]/.test(pw)) { 
        reqNum.style.color = '#22c55e';
        validCount++; 
    } else { 
        reqNum.style.color = '#9ca3af';
    }

    if(validCount === 4) {
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

        const { data, raw } = await readMemberApiResponse(response);
        if (!response.ok) {
            throw new Error(data.message || raw || 'Failed to reset password.');
        }

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

async function readMemberApiResponse(response) {
    const contentType = (response.headers.get('content-type') || '').toLowerCase();
    if (contentType.includes('application/json')) {
        return { data: await response.json().catch(() => ({})), raw: '' };
    }

    return { data: {}, raw: await response.text().catch(() => '') };
}

// ========================================
// CROP PHOTO MODAL FUNCTIONS
// ========================================

let cropImageData = null;
let cropImageScale = 1;

function openCropModal() {
    document.getElementById('photoFileInput').click();
}

function closeCropModal() {
    document.getElementById('cropPhotoModal').style.display = 'none';
    cropImageData = null;
    cropImageScale = 1;
}

function handleCropModalOverlay(event) {
    if (event.target.id === 'cropPhotoModal') {
        closeCropModal();
    }
}

function handlePhotoSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        alert('File size exceeds 5MB. Please select a smaller image.');
        return;
    }

    if (!file.type.startsWith('image/')) {
        alert('Please select a valid image file.');
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        cropImageData = e.target.result;
        const cropImg = document.getElementById('cropImagePreview');
        cropImg.src = cropImageData;
        
        // Show crop modal
        document.getElementById('cropPhotoModal').style.display = 'flex';
        
        // Setup zoom
        document.getElementById('cropZoom').addEventListener('input', function() {
            updateCropZoom(this.value);
        });
    };
    reader.readAsDataURL(file);
}

function updateCropZoom(value) {
    const cropImg = document.getElementById('cropImagePreview');
    const zoomPercent = value / 100;
    cropImageScale = 0.8 + (zoomPercent * 1.2); // Range from 0.8x to 2x
    cropImg.style.transform = `scale(${cropImageScale})`;
    cropImg.style.transformOrigin = 'center';
}

function uploadCroppedPhoto() {
    if (!cropImageData) {
        alert('No image selected.');
        return;
    }

    // Get the crop image element
    const cropImg = document.getElementById('cropImagePreview');
    
    // Create canvas for circular crop
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const size = 400;
    
    canvas.width = size;
    canvas.height = size;

    // Create image
    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.src = cropImageData;

    img.onload = function() {
        // Draw circular crop
        ctx.clearRect(0, 0, size, size);
        
        // Create circular clipping path
        ctx.beginPath();
        ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
        ctx.clip();
        
        // Draw image with scale
        const scaledWidth = img.width * cropImageScale;
        const scaledHeight = img.height * cropImageScale;
        const x = (size - scaledWidth) / 2;
        const y = (size - scaledHeight) / 2;
        
        ctx.drawImage(img, x, y, scaledWidth, scaledHeight);
        
        // Update profile picture
        const profileImg = document.querySelector('img[alt="Profile"]');
        if (profileImg) {
            profileImg.src = canvas.toDataURL('image/jpeg', 0.9);
        }
        
        alert('Profile photo updated successfully!');
        closeCropModal();
        document.getElementById('photoFileInput').value = '';
    };
}
</script>