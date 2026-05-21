{{-- MEMBERS TAB --}}
<div id="section-members" class="content-section" style="display: none;">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif;">Members</h3>
        <p class="text-muted mb-0" style="font-size: 14px;">Manage and review all member records.</p>
    </div>

    <div class="d-flex gap-4 mb-4 flex-wrap">
        <div class="summary-card bg-red d-flex align-items-center flex-grow-1" style="max-width: 450px;">
            <div class="icon-circle me-3"><i class="fa fa-user-times"></i></div>
            <div>
                <div class="label">Unpaid Members</div>
                <div class="value" id="unpaid-count">0</div>
            </div>
        </div>
        <div class="summary-card bg-green d-flex align-items-center flex-grow-1" style="max-width: 450px;">
            <div class="icon-circle me-3"><i class="fa fa-user-check"></i></div>
            <div>
                <div class="label">Paid Members</div>
                <div class="value" id="paid-count">0</div>
            </div>
        </div>
    </div>

    <div class="floating-card table-card">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
            <div class="d-flex align-items-center gap-2">
                <h5 style="font-size: 18px; font-weight: bold; margin: 0; color: #111;">Members Directory</h5>
                <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill" id="total-members-badge">0 Active</span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <select id="memberSort" class="form-select form-select-sm text-muted fw-bold" style="height: 36px; border-radius: 6px; border-color: #eee; font-size: 13px; box-shadow: none; cursor:pointer; width: 140px; background-color: #f8f9fb;">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                </select>

                <div style="position: relative; width: 250px;">
                    <i class="fa fa-search text-muted" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                    <input type="text" id="memberSearch" placeholder="Search members..." style="width: 100%; height: 36px; padding-left: 35px; border-radius: 6px; border: 1px solid #eee; font-size: 13px; outline: none; background: #f8f9fb;">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th class="ps-4">Member Name</th>
                        <th>OR Number</th>
                        <th>Reg Date</th>
                        <th>Exp Date</th>
                        <th>Status</th>
                        <th>Proof</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="members-table-body">
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted"><i class="fa fa-spinner fa-spin fs-3 mb-2"></i><br>Loading members...</td>
                    </tr>
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