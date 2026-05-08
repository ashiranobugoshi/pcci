{{-- APPLICANTS TAB (TREASURER) --}}
<div id="section-applicants" class="content-section" style="display: none;">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif;">Applicants Queue</h3>
        <p class="text-muted mb-0" style="font-size: 14px;">Review and process payments for newly approved businesses.</p>
    </div>

    <div class="floating-card table-card">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
            <div class="d-flex align-items-center gap-2">
                <h5 style="font-size: 18px; font-weight: bold; margin: 0; color: #111;">Applicants Directory</h5>
                <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded-pill" id="report-pending-count-badge">0 Pending</span>
            </div>

            <div class="d-flex align-items-center gap-3">
                {{-- STRICT STATUS FILTER FOR TREASURER --}}
                <select id="applicantStatusFilter" class="form-select form-select-sm text-muted fw-bold" style="height: 36px; border-radius: 6px; border-color: #eee; font-size: 13px; box-shadow: none; cursor:pointer; width: 140px; background-color: #f8f9fb;">
                    <option value="approved" selected>Approved</option>
                    <option value="rejected">Rejected</option>
                </select>

                <select id="applicantSort" class="form-select form-select-sm text-muted fw-bold" style="height: 36px; border-radius: 6px; border-color: #eee; font-size: 13px; box-shadow: none; cursor:pointer; width: 140px; background-color: #f8f9fb;">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                </select>

                <div style="position: relative; width: 250px;">
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
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted"><i class="fa fa-spinner fa-spin fs-3 mb-2"></i><br>Loading applicants...</td>
                    </tr>
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