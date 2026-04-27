<div class="custom-modal-overlay" id="proofModal" onclick="closeProofModal(event)">
    <div class="custom-modal-card" onclick="event.stopPropagation()">
        <button class="modal-close-x" onclick="hideProofModal()">&times;</button>
        <h5 class="fw-bold mb-3"><i class="fa fa-file-invoice text-danger me-2"></i> Process Applicant Payment</h5>
        
        <div class="modal-img-wrapper mb-4" id="modalImgWrapper">
            <div id="modalSpinner" class="text-muted"><i class="fa fa-spinner fa-spin fs-2" style="display: block; margin-bottom: 8px;"></i><small>Loading Image...</small></div>
            <img id="modalImage" src="" alt="Proof of Payment" style="display: none;" onload="onImageLoad()">
        </div>

        <div>
            <label class="small text-muted fw-bold mb-2 d-block">SELECT MEMBERSHIP TYPE:</label>
            
            <div class="d-flex gap-3 w-100 mb-4 proof-type-grid">
                <button id="toggleBtn1" class="type-toggle-btn active-1 flex-grow-1" onclick="selectType(1)">
                    <i class="fa fa-store mb-1 fs-5"></i><br>Micro<br><small class="fw-normal">₱500.00</small>
                </button>
                <button id="toggleBtn2" class="type-toggle-btn flex-grow-1" onclick="selectType(2)">
                    <i class="fa fa-building mb-1 fs-5"></i><br>Small Enterprise<br><small class="fw-normal">₱5,000.00</small>
                </button>
            </div>

            {{-- NEW: Two Buttons for Reject and Approve --}}
            <div class="d-flex gap-2 border-top pt-3">
                <button class="btn btn-danger px-4 fw-bold rounded-pill shadow-sm flex-grow-1" onclick="rejectPaymentProcessing()" style="height: 45px; border: none;">
                    <i class="fa fa-times me-1"></i> Reject
                </button>
                <button class="btn btn-success px-4 fw-bold rounded-pill shadow-sm flex-grow-1" onclick="confirmProcessing()" style="height: 45px; background: #22c55e; border: none;">
                    <i class="fa fa-check me-1"></i> Approve
                </button>
            </div>
        </div>
    </div>
</div>

<div class="custom-modal-overlay" id="memberDetailsModal" onclick="closeMemberModal(event)">
    <div class="custom-modal-card" style="max-width: 600px;" onclick="event.stopPropagation()">
        <button class="modal-close-x" onclick="hideMemberModal()">&times;</button>
        <h5 class="fw-bold mb-4"><i class="fa fa-user-tie text-danger me-2"></i> Member Profile Details</h5>
        <div id="member-detail-content"></div>
        <div class="mt-4 text-end"><button class="btn btn-secondary px-4 fw-bold rounded-pill" onclick="hideMemberModal()">Close Details</button></div>
    </div>
</div>

<div class="custom-modal-overlay" id="simpleProofModal" onclick="closeSimpleProofModal(event)">
    <div class="custom-modal-card" onclick="event.stopPropagation()">
        <button class="modal-close-x" onclick="hideSimpleProofModal()">&times;</button>
        <h5 class="fw-bold mb-3"><i class="fa fa-image text-primary me-2"></i> View Proof</h5>
        <div class="modal-img-wrapper">
            <div id="simpleModalSpinner" class="text-muted">
                <i class="fa fa-spinner fa-spin fs-2" style="display: block; margin-bottom: 8px;"></i>
                <small style="display: block; text-align: center;">Loading Image...</small>
            </div>
            <img id="simpleModalImage" src="" alt="Proof of Payment" style="display: none;" onload="onSimpleImageLoad()">
        </div>
    </div>
</div>

{{-- ADD PAYMENT MODAL (NEW) --}}
<div class="custom-modal-overlay" id="addPaymentModal" onclick="closeAddPaymentOverlay(event)">
    <div class="custom-modal-card add-payment-modal-card" onclick="event.stopPropagation()">
        <button type="button" class="modal-close-x" onclick="closeAddPaymentModal(event)">&times;</button>
        <div class="add-payment-modal-header">
            <div class="add-payment-modal-icon-container">
                <i class="fa fa-user fs-1 text-dark"></i>
                <div class="add-payment-modal-check-icon"><i class="fa fa-check"></i></div>
            </div>
            <h5 class="add-payment-modal-title" id="addPaymentModalTitle">Add Payment</h5>
        </div>
        
        <div class="add-payment-modal-body">
            <div class="add-payment-form-group">
                <label class="add-payment-label">Members</label>
                <input type="text" class="add-payment-input" id="transactionMemberInput" value="Juan Dela Cruz">
            </div>
            <div class="add-payment-form-group">
                <label class="add-payment-label">OR Number</label>
                <input type="text" class="add-payment-input" id="transactionOrNumber" value="9403-4783" readonly>
            </div>
            <div class="add-payment-form-group">
                <label class="add-payment-label">Payment Date</label>
                <input type="text" class="add-payment-input" id="transactionPaymentDate" value="02-11-2027" readonly>
            </div>
            <div class="add-payment-form-group">
                <label class="add-payment-label">Membership Type</label>
                <select class="add-payment-input form-select" id="transactionMembershipType" style="font-size: 13px;">
                    <option selected>Annual</option>
                    <option>Semi-Annual</option>
                    <option>Quarterly</option>
                </select>
            </div>
            <div class="add-payment-form-group">
                <label class="add-payment-label">Payment Type</label>
                <select class="add-payment-input form-select" id="transactionPaymentType" style="font-size: 13px;">
                    <option selected>GCash</option>
                    <option>Cash</option>
                    <option>Bank Transfer</option>
                </select>
            </div>
            <div class="add-payment-form-group">
                <label class="add-payment-label">Proof of Payment</label>
                <input type="text" class="add-payment-input" id="transactionProofInput" value="Upload image (png, jpg)" readonly style="color: #999;">
            </div>
            <div class="add-payment-form-group">
                <label class="add-payment-label">Receiver</label>
                <select class="add-payment-input form-select" id="transactionReceiverSelect" style="font-size: 13px;">
                    <option selected>Jesus Versula</option>
                    <option>Admin Person B</option>
                </select>
            </div>
        </div>

        <div class="add-payment-modal-footer">
            <button class="add-payment-btn-clear" onclick="clearPaymentForm()">Clear Form</button>
            <button class="add-payment-btn-confirm" id="transactionModalConfirmBtn" onclick="confirmPaymentAdd()">Confirm</button>
        </div>
    </div>
</div>

{{-- RESET PASSWORD OTP MODAL (NEW) --}}
<div class="custom-modal-overlay" id="otpModal" onclick="closeOtpOverlay(event)">
    <div class="custom-modal-card otp-modal-card" onclick="event.stopPropagation()">
        <h5 class="otp-title">Reset Password</h5>
        <p class="otp-subtitle">Enter the code sent to <span id="otpTargetEmail">your email</span> to reset your password</p>
        <div class="otp-input-container">
            <input type="text" maxlength="1" class="otp-box" oninput="moveToNext(this, event)">
            <input type="text" maxlength="1" class="otp-box" oninput="moveToNext(this, event)">
            <input type="text" maxlength="1" class="otp-box" oninput="moveToNext(this, event)">
            <input type="text" maxlength="1" class="otp-box" oninput="moveToNext(this, event)">
            <input type="text" maxlength="1" class="otp-box" oninput="moveToNext(this, event)">
            <input type="text" maxlength="1" class="otp-box" oninput="moveToNext(this, event)">
        </div>
    </div>
</div>

{{-- NEW: ENTER NEW PASSWORD MODAL --}}
<div class="custom-modal-overlay" id="resetPasswordModal" onclick="closeResetPasswordOverlay(event)">
    <div class="custom-modal-card reset-pw-modal-card" onclick="event.stopPropagation()">
        <h5 class="reset-pw-title">Reset Password</h5>
        <p class="reset-pw-subtitle">Enter your new password</p>
        
        <label class="reset-pw-label">New Password</label>
        <div class="reset-pw-input-wrap">
            <i class="fa fa-key reset-pw-icon-left"></i>
            <input type="password" class="reset-pw-input" id="newPasswordInput" placeholder="abcde" oninput="validatePassword()">
            <i class="fa fa-eye reset-pw-icon-right" onclick="togglePasswordView('newPasswordInput')"></i>
        </div>

        {{-- Live Checklist --}}
        <ul class="reset-pw-checklist">
            <li id="req-lower"><i class="fa fa-check-circle"></i> At least one lower case letter.</li>
            <li id="req-len"><i class="fa fa-check-circle"></i> Minimum of 8 characters.</li>
            <li id="req-upper"><i class="fa fa-check-circle"></i> At least one upper case letter.</li>
            <li id="req-num"><i class="fa fa-check-circle"></i> At least one number.</li>
        </ul>

        <label class="reset-pw-label">Re-Password</label>
        <div class="reset-pw-input-wrap">
            <i class="fa fa-key reset-pw-icon-left"></i>
            <input type="password" class="reset-pw-input" id="rePasswordInput" placeholder="abcde">
            <i class="fa fa-eye reset-pw-icon-right" onclick="togglePasswordView('rePasswordInput')"></i>
        </div>

        <div class="reset-pw-actions">
            <button class="reset-pw-btn-cancel" onclick="hideResetPasswordModal()">Cancel</button>
            <button class="reset-pw-btn-submit" id="resetPwSubmitBtn" onclick="submitNewPassword()">Reset Password</button>
        </div>
    </div>
</div>

{{-- OTP FLOW FEEDBACK MODAL --}}
<div class="custom-modal-overlay" id="otpFeedbackModal" onclick="closeOtpFeedbackOverlay(event)">
    <div class="custom-modal-card otp-feedback-modal-card" onclick="event.stopPropagation()">
        <h5 class="otp-feedback-title" id="otpFeedbackTitle">Notice</h5>
        <p class="otp-feedback-message" id="otpFeedbackMessage">Message</p>
        <div class="text-end mt-3">
            <button class="btn btn-danger px-4 py-2 fw-bold rounded-pill" onclick="hideOtpFeedbackModal()">OK</button>
        </div>
    </div>
</div>

{{-- CROP PROFILE PICTURE MODAL --}}
<div class="custom-modal-overlay" id="cropModal" onclick="closeCropOverlay(event)">
    <div class="custom-modal-card crop-modal-card" onclick="event.stopPropagation()">
        <div class="crop-header">
            <h5 class="crop-title">Crop your new profile picture</h5>
            <button class="crop-close-btn" onclick="hideCropModal()"><i class="fa fa-times"></i></button>
        </div>
        
        <div class="crop-body">
            <div class="crop-image-container">
                <img src="https://i.pravatar.cc/400?img=11" alt="To Crop">
                <div class="crop-overlay-circle"></div>
            </div>
        </div>

        <div class="crop-footer">
            <button class="crop-btn-submit" onclick="setNewProfilePicture()">Set New Profile Picture</button>
        </div>
    </div>
</div>
