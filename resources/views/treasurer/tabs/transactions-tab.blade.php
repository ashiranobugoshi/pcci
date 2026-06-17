{{-- TRANSACTIONS TAB --}}
<div id="section-transactions" class="content-section" style="display: none;">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif;">Master Ledger</h3>
        <p class="text-muted mb-0" style="font-size: 14px;">Manage and track all global payment activities across the organization.</p>
    </div>

    <!-- NEW: Updated Stat Cards mapped to the new Stats Endpoint -->
    <div class="reports-grid mb-4">
        <div class="report-stat-card">
            <div class="report-label">New Registrations</div>
            <div class="report-value text-primary" id="trans-initial-amt">Php. 0</div>
            <div class="report-indicator"><i class="fa fa-user-plus text-primary"></i> <span class="text-muted fw-normal" id="trans-initial-count">0 approved</span></div>
        </div>
        <div class="report-stat-card">
            <div class="report-label">Renewals</div>
            <div class="report-value text-success" id="trans-renewal-amt">Php. 0</div>
        </div>
        <div class="report-stat-card">
            <div class="report-label">Pending / Failed</div>
            <div class="report-value text-danger" id="trans-pending-amt">0 Transactions</div>
            <div class="report-indicator"><i class="fa fa-exclamation-circle text-danger"></i> <span class="text-muted fw-normal" style="font-size: 11px;">Includes rejected payments</span></div>
        </div>
    </div>

    <div class="floating-card table-card" style="padding: 0; overflow: hidden; border-bottom: 6px solid #b61b2a;">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <div>
                <h5 style="font-size: 18px; font-weight: bold; margin: 0; color: #111;">Transaction Records</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="position-relative" id="transFilterContainer" style="width: 280px;">
                    <i class="fa fa-search text-muted" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                    <input type="text" id="transactionSearch" placeholder="Search transactions..." style="width: 100%; height: 38px; padding-left: 35px; padding-right: 40px; border-radius: 8px; border: 1px solid #eee; font-size: 13px; outline: none; background: #f8f9fb;">

                    <button class="btn btn-sm p-0 d-flex justify-content-center align-items-center text-muted" onclick="toggleTransFilter(event)" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; border-radius: 6px;">
                        <i class="fa fa-sliders-h"></i>
                    </button>

                    <div class="report-dropdown-menu" id="transFilterMenu" style="width: 180px; right: 0; top: 100%; margin-top: 5px;">
                        <div class="report-dropdown-item text-dark" onclick="filterTransactions('all')">All Transactions</div>
                        <hr class="trans-filter-divider">
                        <div class="report-dropdown-item text-success" onclick="filterTransactions('approved')"><i class="fa fa-check-circle w-20px"></i> Approved</div>
                        <div class="report-dropdown-item text-warning" onclick="filterTransactions('pending')"><i class="fa fa-clock w-20px"></i> Pending</div>
                    </div>
                </div>

                <div class="position-relative" id="transMenuContainer">
                    <button class="btn btn-light border shadow-sm d-flex justify-content-center align-items-center" onclick="toggleTransDropdown(event)" style="height: 38px; width: 38px; border-radius: 8px;">
                        <i class="fa fa-ellipsis-v text-muted"></i>
                    </button>
                    <div class="report-dropdown-menu" id="transDropdownMenu" style="width: 160px;">
                        <div class="report-dropdown-item" onclick="exportTransactions()">
                            <i class="fa fa-file-export text-success w-20px"></i> Export
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="custom-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Business Name</th>
                        <th>Transaction Type</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>OR Number</th>
                        <th>Date</th>
                        <th class="text-center">Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="transactions-table-body">
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted"><i class="fa fa-spinner fa-spin fs-3 mb-2"></i><br>Loading transactions...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center align-items-center mt-3 mb-3" style="height: 50px; gap: 15px;">
            <button id="transaction-prev-btn" class="btn btn-sm btn-light border rounded" onclick="prevTransactionPage()"><i class="fa fa-chevron-left"></i></button>
            <span style="font-size: 14px; font-weight: bold; color: #4b5563;" id="transaction-pagination-text">Page 1 of 1</span>
            <button id="transaction-next-btn" class="btn btn-sm btn-light border rounded" onclick="nextTransactionPage()"><i class="fa fa-chevron-right"></i></button>
        </div>
    </div>
</div>