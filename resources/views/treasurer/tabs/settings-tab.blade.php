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
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 16px;">Account Settings</div>
                                <div class="text-muted" style="font-size: 13px;">Profile details, email, and roles</div>
                            </div>
                        </div>
                        <i class="fa fa-chevron-right text-muted"></i>
                    </div>

                    <div class="setting-box p-4 border-bottom d-flex justify-content-between align-items-center mb-3" onclick="openSetting('settings-security')">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #e0e7ff; color: #3b82f6; display: flex; justify-content: center; align-items: center; font-size: 18px;"><i class="fa fa-shield-alt"></i></div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 16px;">Security</div>
                                <div class="text-muted" style="font-size: 13px;">Change password, 2FA</div>
                            </div>
                        </div>
                        <i class="fa fa-chevron-right text-muted"></i>
                    </div>

                    <div class="setting-box p-4 d-flex justify-content-between align-items-center mb-3" onclick="openSetting('settings-preferences')">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #f3e8ff; color: #6366f1; display: flex; justify-content: center; align-items: center; font-size: 18px;"><i class="fa fa-sliders-h"></i></div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 16px;">Preferences</div>
                                <div class="text-muted" style="font-size: 13px;">Dark mode, Notifications</div>
                            </div>
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
                <span class="text-muted" style="font-size: 13px;">Manage your profile and personal account information.</span>
            </div>
        </div>

        <div class="new-acc-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/PCCI-Logo.svg') }}" id="settingsAccountAvatar" class="new-acc-avatar" alt="Profile" style="width: 66px; height: 66px; border-radius: 50%; object-fit: cover; border: 1px solid #e5e7eb;">
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 16px;">Profile Picture</h6>
                        <span class="text-muted" style="font-size: 12px; text-transform: uppercase;">PNG, JPEG under 5MB</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="new-acc-btn-upload shadow-sm" style="border: 1px solid #d1d5db; background: #fff; padding: 6px 12px; border-radius: 6px; font-weight: bold;" onclick="triggerAccountImagePicker()">Upload new photo</button>
                    <button type="button" class="new-acc-btn-delete shadow-sm" style="border: 1px solid #d1d5db; background: #fff; padding: 6px 12px; border-radius: 6px; font-weight: bold; color: #a3a3a3;" onclick="removeAccountAvatar()">Delete</button>
                    <input type="file" id="settingsImageInput" accept="image/*" style="display:none;" onchange="handleAccountImageChange(event)">
                </div>
            </div>

            <h6 class="fw-bold text-dark mt-4 mb-3" style="font-size: 14px;">Full Name</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="text-muted mb-1 w-100 fw-bold" style="font-size: 12px;">Last Name</label>
                    <input type="text" class="form-control" id="settingsLastName" placeholder="Enter last name" style="border-radius: 8px; border: 1px solid #d1d5db; padding: 10px;">
                </div>
                <div class="col-md-6">
                    <label class="text-muted mb-1 w-100 fw-bold" style="font-size: 12px;">First Name</label>
                    <input type="text" class="form-control" id="settingsFirstName" placeholder="Enter first name" style="border-radius: 8px; border: 1px solid #d1d5db; padding: 10px;">
                </div>
            </div>

            <hr class="border-secondary opacity-25" style="margin: 25px 0;">

            <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;">Contact Details</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="text-muted mb-1 w-100 fw-bold" style="font-size: 12px;">Email</label>
                    <input type="email" class="form-control" id="settingsEmailInput" placeholder="Enter email" style="border-radius: 8px; border: 1px solid #d1d5db; padding: 10px;">
                </div>
                <div class="col-md-6">
                    <label class="text-muted mb-1 w-100 fw-bold" style="font-size: 12px;">Contact Number</label>
                    <input type="text" class="form-control" id="settingsContactInput" placeholder="Enter contact number" style="border-radius: 8px; border: 1px solid #d1d5db; padding: 10px;">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-5">
                <button type="button" class="btn btn-danger fw-bold shadow-sm" id="saveAccountBtn" onclick="saveAccountSettings()">Save Changes</button>
            </div>
        </div>
    </div>

    {{-- Crop Photo Modal (Cropper.js) --}}
    <div class="modal-overlay" id="accountCropModal" style="display: none; background: rgba(0, 0, 0, 0.6); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; justify-content: center; align-items: center;">
        <div class="modal-content-box" style="max-width: 500px; width: 100%; padding: 0; border-radius: 18px; background: #fff;">
            <div style="padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e5e7eb;">
                <h5 class="fw-bold mb-0 text-dark text-uppercase" style="font-size: 14px;">Crop Profile Picture</h5>
                <button class="btn btn-link p-0 text-dark" style="font-size: 18px; text-decoration: none;" onclick="closeAccountCropModal()"><i class="fa fa-times"></i></button>
            </div>

            <div style="padding: 20px; background: #000; text-align: center;">
                <div style="max-height: 400px; overflow: hidden; display: flex; justify-content: center;">
                    <img id="accountCropperImage" src="" style="max-width: 100%; display: block;">
                </div>
            </div>

            <div style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-light fw-bold" onclick="closeAccountCropModal()">Cancel</button>
                <button type="button" class="btn btn-danger fw-bold" onclick="applyAccountCrop()"><i class="fa fa-crop-simple me-2"></i> Apply Crop</button>
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

<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>