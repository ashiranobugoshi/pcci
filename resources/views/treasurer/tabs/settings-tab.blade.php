{{-- SETTINGS TAB --}}
<div id="section-settings" class="content-section" style="display: none;">
    
    {{-- VIEW 1: Main Settings Menu --}}
    <div id="settings-main" class="fade-in">
        <div class="mb-4 pb-2 border-bottom">
            <h3 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif;">Settings</h3>
            <p class="text-muted mb-0" style="font-size: 14px;">Manage your account, security, and preferences.</p>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="custom-card mb-4 p-0 overflow-hidden" style="max-width: 1000px; margin: 0 auto; box-shadow: none; background: transparent;">
                    
                    <div class="setting-box p-4 border-bottom d-flex justify-content-between align-items-center mb-3" onclick="openSetting('settings-account')">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #fee2e2; color: #ef4444; display: flex; justify-content: center; align-items: center; font-size: 18px;"><i class="fa fa-user"></i></div>
                            <div><div class="fw-bold text-dark" style="font-size: 16px;">Account Settings</div><div class="text-muted" style="font-size: 13px;">Profile details, email, and roles</div></div>
                        </div>
                        <i class="fa fa-chevron-right text-muted"></i>
                    </div>

                    <div class="setting-box p-4 border-bottom d-flex justify-content-between align-items-center mb-3" onclick="openSetting('settings-security')">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #e0e7ff; color: #3b82f6; display: flex; justify-content: center; align-items: center; font-size: 18px;"><i class="fa fa-shield-alt"></i></div>
                            <div><div class="fw-bold text-dark" style="font-size: 16px;">Security</div><div class="text-muted" style="font-size: 13px;">Change password, 2FA</div></div>
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
                <div class="d-flex justify-content-end mb-5" style="max-width: 1000px; margin: 0 auto;">
                    <button class="btn btn-danger px-4 py-2 fw-bold shadow-sm rounded-pill" onclick="logout()">
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/PCCI-Logo.svg') }}" id="settingsAccountAvatar" class="new-acc-avatar" alt="Profile">
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 16px;">Profile Picture</h6>
                        <span class="text-muted" style="font-size: 12px; text-transform: uppercase;">PNG, JPEG under 15MB</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="new-acc-btn-upload shadow-sm" onclick="triggerAccountImagePicker()">Upload new photo</button>
                    <button class="new-acc-btn-delete shadow-sm">Delete</button>
                    <input type="file" id="settingsImageInput" accept="image/*" style="display:none;" onchange="handleAccountImageChange(event)">
                </div>
            </div>

            <h6 class="fw-bold text-dark mt-4 mb-3" style="font-size: 14px;">Full Name</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="text-muted mb-1 w-100" style="font-size: 12px;">Last Name</label>
                    <div class="position-relative">
                        <input type="text" class="new-acc-input" id="settingsLastName" value="" readonly>
                        <button type="button" class="new-acc-edit" onclick="toggleAccountField('settingsLastName')"><i class="fa fa-edit"></i> Edit</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted mb-1 w-100" style="font-size: 12px;">First Name</label>
                    <div class="position-relative">
                        <input type="text" class="new-acc-input" id="settingsFirstName" value="" readonly>
                        <button type="button" class="new-acc-edit" onclick="toggleAccountField('settingsFirstName')"><i class="fa fa-edit"></i> Edit</button>
                    </div>
                </div>
            </div>

            <hr class="border-secondary opacity-25" style="margin: 25px 0;">

            <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;">Contact Email</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="text-muted mb-1 w-100" style="font-size: 12px;">Email</label>
                    <div class="position-relative">
                        <input type="email" class="new-acc-input" id="settingsEmailInput" value="" readonly>
                        <button type="button" class="new-acc-edit" onclick="toggleAccountField('settingsEmailInput')"><i class="fa fa-edit"></i> Edit</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted mb-1 w-100" style="font-size: 12px;">Contact Number</label>
                    <div class="position-relative">
                        <input type="text" class="new-acc-input" id="settingsContactInput" value="" readonly>
                        <button type="button" class="new-acc-edit" onclick="toggleAccountField('settingsContactInput')"><i class="fa fa-edit"></i> Edit</button>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-5">
                <button class="new-acc-action-gray shadow-sm">Deactive Account</button>
                <button type="button" class="new-acc-action-dark shadow-sm" onclick="saveAccountSettings()">Save Changes</button>
            </div>
        </div>
    </div>

    {{-- VIEW 3: NEW SECURITY SETTINGS --}}
    <div id="settings-security" class="fade-in" style="display: none;">
        
        <div class="acc-header-out">
            <button class="back-btn-ui" onclick="closeSetting('settings-security')">
                <i class="fa fa-angle-left fs-4"></i>
            </button>
            <i class="fa fa-lock acc-header-icon" style="font-size: 32px;"></i>
            <div class="acc-title-wrap">
                <h4 class="fw-bold mb-0 text-dark" style="font-size: 20px;">Security</h4>
                <span class="text-muted" style="font-size: 13px;">Protect your account by managing passwords and security settings.</span>
            </div>
        </div>

        <div class="new-acc-card p-0" style="background: transparent; box-shadow: none;">
            
            {{-- Change Password Bar --}}
            <div class="sec-change-pw-box">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa fa-lock fs-4 text-dark"></i>
                        <span class="fw-bold text-dark fs-5" style="font-size: 16px !important;">Change Password</span>
                    </div>
                    <button class="sec-btn-update shadow-sm" id="requestOtpBtn" onclick="requestPasswordChangeOtp()">Update Password</button>
                </div>
            </div>

            {{-- Login Activity Box --}}
            <div class="sec-login-box shadow-sm">
                <h5 class="sec-login-title text-dark">Login Activity</h5>
                <hr class="sec-divider">
                <div class="table-responsive">
                    <table class="sec-table">
                        <thead>
                            <tr>
                                <th class="text-dark">Device</th>
                                <th class="text-dark">Location</th>
                                <th class="text-dark">Date</th>
                                <th class="text-dark">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Android - Chrome</td>
                                <td>Marilao, Bulacan</td>
                                <td>10:00Am, March 9, 2025</td>
                                <td class="text-muted">Successful</td>
                            </tr>
                            <tr>
                                <td>Lenovo LOQ - Chrome</td>
                                <td>Marilao, Bulacan</td>
                                <td>10:00Am, March 9, 2025</td>
                                <td class="text-muted">Successful</td>
                            </tr>
                        </tbody>
                    </table>
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
                <span class="text-muted" style="font-size: 13px;">Manage appearance and notification settings.</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="new-acc-card">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">Appearance & Notifications</h6>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Dark Mode</h6>
                            <small class="text-muted">Switch between a light and dark interface</small>
                        </div>
                        <div class="form-check form-switch fs-4">
                            <input class="form-check-input" type="checkbox" id="darkModeSwitch" style="cursor: pointer;" onclick="toggleDarkMode()">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Email Notifications</h6>
                            <small class="text-muted">Receive alerts for new pending payments</small>
                        </div>
                        <div class="form-check form-switch fs-4">
                            <input class="form-check-input" type="checkbox" checked style="cursor: pointer;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Membership Expiry Reminder</h6>
                            <small class="text-muted">Get notified 30 days before a membership expires</small>
                        </div>
                        <div class="form-check form-switch fs-4">
                            <input class="form-check-input" type="checkbox" checked style="cursor: pointer;">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
