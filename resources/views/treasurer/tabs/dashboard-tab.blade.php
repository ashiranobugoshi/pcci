{{-- DASHBOARD OVERVIEW TAB --}}
<div id="section-dashboard" class="content-section" style="display: block;">
    <div class="mb-4 pb-2">
        <h3 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif;">Welcome, <span id="dashWelcomeName">User</span>!</h3>
        <p class="text-muted mb-0" style="font-size: 14px;">Here is your financial overview for today.</p>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="summary-card dash-card bg-red">
                <div class="label">Total Revenue</div>
                <div class="value" id="dash-total-revenue-val">PHP 0.00</div>
                <i class="fa fa-wallet bg-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card dash-card bg-green">
                <div class="label">Paid Members</div>
                <div class="value" id="dash-paid-members-val">0</div>
                <i class="fa fa-users bg-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card dash-card bg-orange">
                <div class="label">Active Accounts</div>
                <div class="value" id="dash-active-accounts-val">0</div>
                <i class="fa fa-user-check bg-icon"></i>
            </div>
        </div>
    </div>

    {{-- SMALL INFO CARD --}}
    <div class="floating-card small-info-card mb-4">
        <div class="icon-box"><i class="fa fa-money-check-dollar"></i></div>
        <div class="small-info-content">
            <p><span id="today-payments-label">Today's Payments:</span> <strong id="today-payments-amt" class="text-dark">₱0.00</strong></p>
            <p><span id="yesterday-payments-label" class="text-muted">Yesterday payment:</span> <span id="yesterday-payments-amt" class="text-muted fw-bold">₱0.00</span></p>
        </div>
        <div class="small-info-filter-wrap">
            <select id="dashboardPaymentRange" class="form-select form-select-sm dashboard-inline-filter">
                <option value="day" selected>This Day</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
            </select>
        </div>
    </div>

    {{-- MIDDLE SECTION: CHARTS --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="floating-card">
                <div class="card-title-row">
                    <h5>Membership Revenue</h5>
                    <select id="dashboardRevenueRange" class="form-select form-select-sm" style="width: auto; font-size: 12px; cursor: pointer;">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month" selected>This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="floating-card">
                <div class="card-title-row">
                    <h5>Payment Breakdown</h5>
                    <button class="btn btn-sm btn-outline-secondary" style="font-size: 12px; font-weight: bold;">View Report</button>
                </div>
                <div class="chart-container d-flex justify-content-center">
                    <canvas id="pieChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- BOTTOM SECTION: RECENT PAYMENTS TABLE --}}
    <div class="floating-card table-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <h5 style="font-size: 18px; font-weight: bold; margin: 0; color: #111;">Recent Payments</h5>
            </div>
        </div>
        <div class="table-responsive">
            <table class="custom-table recent-payments-table">
                <thead>
                    <tr>
                        <th>Business Name</th>
                        <th>Membership Type</th>
                        <th>Amount</th>
                        <th>OR Number</th>
                        <th>Date</th>
                        <th>Proof of Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="recent-payments-table-body">
                    <tr>
                        <td colspan="7" class="text-center py-4">Loading records...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>