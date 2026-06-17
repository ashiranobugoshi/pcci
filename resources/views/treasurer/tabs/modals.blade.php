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

{{-- SIMPLE PROOF MODAL (FOR RENEWALS/TRANSACTIONS) --}}
<div class="modal-overlay" id="simpleProofModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:9999; justify-content:center; align-items:center; padding:20px;" onclick="closeSimpleProofModal(event)">
    <div style="background:#fff; border-radius:12px; overflow:hidden; max-width:700px; width:100%; position:relative; display:flex; flex-direction:column;" onclick="event.stopPropagation()">

        <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
            <h5 style="margin:0; font-size:1.1rem; font-weight:bold;"><i class="fa fa-search-dollar text-primary me-2"></i> Review Payment</h5>
            <button type="button" onclick="hideSimpleProofModal()" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
        </div>

        <div style="padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e5e7eb;">
            <div class="row text-center">
                <div class="col-4 border-end">
                    <small class="text-muted d-block fw-bold" style="font-size: 11px; text-transform: uppercase;">Amount Due</small>
                    <strong id="spModalAmount" class="text-danger fs-5">₱0.00</strong>
                </div>
                <div class="col-4 border-end">
                    <small class="text-muted d-block fw-bold" style="font-size: 11px; text-transform: uppercase;">Payment Method</small>
                    <strong id="spModalMethod" class="text-dark">N/A</strong>
                </div>
                <div class="col-4">
                    <small class="text-muted d-block fw-bold" style="font-size: 11px; text-transform: uppercase;">Ref / Trace No.</small>
                    <strong id="spModalRef" class="text-dark">N/A</strong>
                </div>
            </div>
        </div>

        <div style="padding:20px; text-align:center; position:relative; height:450px; overflow:auto; display:flex; justify-content:center; align-items:center; background:#e2e8f0;">
            <div id="simpleModalSpinner" class="text-muted" style="display:none; flex-direction:column; align-items:center;">
                <i class="fa fa-spinner fa-spin" style="font-size: 3rem; margin-bottom: 10px;"></i>
                <small class="fw-bold">Loading secure image...</small>
            </div>

            <img id="simpleModalImage" src="" onclick="toggleImageZoom(this)" style="display:none; max-width:100%; border-radius:4px; box-shadow:0 4px 12px rgba(0,0,0,0.15); cursor:zoom-in; transition:transform 0.2s ease-in-out; transform-origin:top center;" alt="Receipt Image" title="Click to zoom">
        </div>

        <div id="spModalActions" style="padding: 16px 20px; border-top: 1px solid #e5e7eb; display: none; justify-content: center; gap: 15px; background: #fff;">
        </div>

    </div>
</div>

{{-- REJECT PAYMENT MODAL --}}
<div class="modal-overlay" id="rejectPaymentModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:9999; justify-content:center; align-items:center; padding:20px;" onclick="closeRejectPaymentModal(event)">
    <div style="background:#fff; border-radius:12px; overflow:hidden; max-width:500px; width:100%; position:relative; display:flex; flex-direction:column;" onclick="event.stopPropagation()">

        <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; background:#fef2f2;">
            <h5 style="margin:0; font-size:1.1rem; font-weight:bold; color:#dc2626;">
                <i class="fa fa-exclamation-triangle me-2"></i> Reject Payment
            </h5>
            <button type="button" onclick="hideRejectPaymentModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#dc2626;">&times;</button>
        </div>

        <div style="padding:20px;">
            <p class="text-muted mb-3" style="font-size: 14px;">Please provide a reason for rejecting this payment. This will be emailed directly to the member.</p>
            <textarea id="rejectPaymentReason" class="form-control" rows="4" placeholder="e.g., The uploaded image is blurry, incorrect amount paid, etc." style="resize:none; border-radius: 8px;"></textarea>
            <input type="hidden" id="rejectPaymentTxnId">
        </div>

        <div style="padding:16px 20px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; background:#f8fafc;">
            <button class="btn btn-light fw-bold px-4 rounded-pill shadow-sm border" onclick="hideRejectPaymentModal()">Cancel</button>
            <button class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm" id="btnConfirmReject" onclick="confirmRejectPayment()">Confirm Rejection</button>
        </div>

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