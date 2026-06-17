<script>
    const token = localStorage.getItem('token');
    const TREASURER_MEMBERS_AUTO_REFRESH_MS = 15000;

    // Global Data Tables
    let allMembersData = [];
    let filteredMembersData = [];
    let currentMemberPage = 1;
    const membersPerPage = 10;

    let allApplicantsData = [];
    let filteredApplicantsData = [];
    let currentApplicantPage = 1;
    const applicantsPerPage = 10;

    let allTransactionsData = [];
    let filteredTransactionsData = [];
    let currentTransactionPage = 1;
    const transactionsPerPage = 10;

    // --- THE MISSING CHART VARIABLES ---
    let dashboardPaymentRange = 'day';
    let dashboardRevenueRange = 'month';
    let dashboardBarChartInstance = null;
    let dashboardPieChartInstance = null;
    let reportBarChartInstance = null;
    let reportPieChartInstance = null;

    // Modal & State Variables
    let editingTransactionId = null;
    let currentApplicantId = null;
    let currentSelectedType = 1;
    let currentPasswordOtpCode = '';
    let currentPasswordOtpEmail = '';
    let accountImageFile = null;
    let approvedApplicantsForModal = [];

    // Dynamic Membership Fetch
    let globalMembershipTypes = [];

    // Global variables to track chart instances
    let dashPieChartInstance = null;
    let dashBarChartInstance = null;

    function destroyChart(canvasId) {
        const chart = Chart.getChart(canvasId);
        if (chart) {
            chart.destroy();
        }
    }

    // HELPER: Generates the required Auth Headers for API calls
    function getAuthHeader() {
        return {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + (localStorage.getItem('token') || window.token)
        };
    }

    // ==========================================
    // 1. DATA & CHART CALCULATIONS
    // ==========================================
    function renderRealDashboardCharts() {
        // Destroy existing
        destroyChart('pieChart');
        destroyChart('barChart');

        // A. Pie Chart Logic (COUNTING ACTIVE MEMBERS)
        const pieCanvas = document.getElementById('pieChart');
        if (pieCanvas) {
            destroyChart('pieChart');

            // Count based on MEMBER status
            let active = 0,
                inactive = 0,
                pending = 0;
            allMembersData.forEach(m => {
                const stat = String(m.status || 'pending').toLowerCase();
                if (stat === 'active') active++;
                else if (stat === 'inactive') inactive++;
                else pending++;
            });

            new Chart(pieCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Active Members', 'Inactive Members', 'Pending'],
                    datasets: [{
                        data: [active, inactive, pending],
                        backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // B. Bar Chart Logic
        const barCanvas = document.getElementById('barChart');
        if (barCanvas) {
            const currentYear = new Date().getFullYear();
            const monthlyRev = {
                'Jan': 0,
                'Feb': 0,
                'Mar': 0,
                'Apr': 0,
                'May': 0,
                'Jun': 0,
                'Jul': 0,
                'Aug': 0,
                'Sep': 0,
                'Oct': 0,
                'Nov': 0,
                'Dec': 0
            };
            const monthNames = Object.keys(monthlyRev);

            allTransactionsData.forEach(t => {
                const stat = String(t.status || '').toLowerCase();
                if (['paid', 'completed', 'approved'].includes(stat)) {
                    const d = new Date(t.created_at);
                    if (d.getFullYear() === currentYear) {
                        const amt = typeof getTransactionAmount === 'function' ? getTransactionAmount(t) : parseFloat(t.amount || 0);
                        monthlyRev[monthNames[d.getMonth()]] += amt;
                    }
                }
            });

            new Chart(barCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: monthNames,
                    datasets: [{
                        label: 'Revenue ₱',
                        data: Object.values(monthlyRev),
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    function populateMainDashboard() {
        let totalRevenue = 0;
        allTransactionsData.forEach(txn => {
            const stat = String(txn.status || '').toLowerCase();
            if (['paid', 'completed', 'approved'].includes(stat)) {
                totalRevenue += (typeof getTransactionAmount === 'function' ? getTransactionAmount(txn) : parseFloat(txn.amount || 0));
            }
        });

        // Update the Cards using IDs from dashboard-tab.blade.php 
        const revEl = document.getElementById('dash-total-revenue-val');
        const paidEl = document.getElementById('dash-paid-members-val');
        const activeEl = document.getElementById('dash-active-accounts-val');

        if (revEl) revEl.innerText = `PHP ${totalRevenue.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        if (paidEl) paidEl.innerText = allMembersData.length;
        if (activeEl) activeEl.innerText = allMembersData.length;
    }

    // ==========================================
    // SMART LEDGER STATS CALCULATOR
    // ==========================================
    function updateTransactionLedgerStats() {
        if (!allTransactionsData || allTransactionsData.length === 0) return;

        let totalRev = 0;
        let initialRev = 0;
        let initialCount = 0; // NEW: Tracks the exact number of approved registrations
        let renewalRev = 0;
        let pendingFailedCount = 0;

        allTransactionsData.forEach(txn => {
            const status = String(txn.status || 'pending').toLowerCase();
            const typeRaw = txn.transaction_type || txn.type || 'renewal';
            const type = String(typeRaw).toLowerCase();

            const amt = typeof getTransactionAmount === 'function' ? getTransactionAmount(txn) : parseFloat(txn.amount || 0);

            // 1. Explicit Status Grouping
            const isApproved = ['approved', 'paid', 'completed', 'success'].includes(status);
            // explicitly includes 'rejected' so they aren't lost
            const isFailedOrPending = ['pending', 'pending_review', 'failed', 'rejected', 'unpaid'].includes(status);

            if (isApproved) {
                // Total Revenue is the sum of ALL approved money
                totalRev += amt;

                // 2. Separate Registration Revenue vs Renewal Revenue
                if (type.includes('initial') || type.includes('registration')) {
                    initialRev += amt;
                    initialCount++; // Increment the exact count
                } else {
                    renewalRev += amt;
                }
            }

            if (isFailedOrPending) {
                pendingFailedCount++;
            }
        });

        const fmt = (num) => `₱ ${num.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

        const elTotal = document.getElementById('trans-total-amt');
        const elInit = document.getElementById('trans-initial-amt');
        const elInitCount = document.getElementById('trans-initial-count'); // Target the new span
        const elRen = document.getElementById('trans-renewal-amt');
        const elPend = document.getElementById('trans-pending-amt');

        if (elTotal) elTotal.innerText = fmt(totalRev);
        if (elInit) elInit.innerText = fmt(initialRev);
        if (elInitCount) elInitCount.innerText = `${initialCount} approved`; // Inject the count
        if (elRen) elRen.innerText = fmt(renewalRev);
        if (elPend) elPend.innerText = pendingFailedCount + ' Transactions';
    }

    // ==========================================
    // SMART BUSINESS NAME RESOLVER
    // ==========================================
    function resolveBusinessNameFromTxn(txn) {
        // 1. Direct check from the transaction object
        let name = txn.company_name || txn.member?.company_name || txn.applicant?.registered_business_name || '';

        // 2. Check nested applicant basic_profile if included in API response
        if (!name) {
            let app = txn.applicant || txn.member?.applicant;
            if (app) {
                let bp = {};
                try {
                    bp = typeof app.basic_profile === 'string' ? JSON.parse(app.basic_profile) : (app.basic_profile || {});
                } catch (e) {}
                name = bp.registered_business_name || app.registered_business_name || '';
            }
        }

        // 3. FALLBACK: Cross-reference with global Members array (Crucial for Renewals!)
        if (!name && txn.member_id) {
            const m = allMembersData.find(x => String(x.id) === String(txn.member_id));
            if (m) {
                let bp = {};
                try {
                    bp = typeof m.applicant?.basic_profile === 'string' ? JSON.parse(m.applicant.basic_profile) : (m.applicant?.basic_profile || {});
                } catch (e) {}
                name = m.company_name || bp.registered_business_name || m.applicant?.registered_business_name || '';
            }
        }

        // 4. FALLBACK: Cross-reference with global Applicants array (Crucial for Initial Registrations!)
        if (!name && txn.applicant_id) {
            const a = allApplicantsData.find(x => String(x.id) === String(txn.applicant_id));
            if (a) {
                let bp = {};
                try {
                    bp = typeof a.basic_profile === 'string' ? JSON.parse(a.basic_profile) : (a.basic_profile || {});
                } catch (e) {}
                name = a.registered_business_name || bp.registered_business_name || '';
            }
        }

        return name || 'Unnamed Business';
    }

    // ==========================================
    // 2. RECENT PAYMENTS (Today & Yesterday Only)
    // ==========================================
    function renderRecentPayments() {
        const tbody = document.getElementById('recent-payments-table-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        // Calculate cutoff: 48 hours ago
        const twoDaysAgo = new Date();
        twoDaysAgo.setDate(twoDaysAgo.getDate() - 2);

        // Only transactions within last 48 hours, sorted newest first
        const recentTxns = allTransactionsData
            .filter(txn => new Date(txn.created_at) >= twoDaysAgo)
            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

        if (recentTxns.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted fw-bold">No payments in the last 48 hours.</td></tr>`;
            return;
        }

        recentTxns.forEach(txn => {
            const status = String(txn.status || 'pending').toLowerCase();
            const amt = parseFloat(txn.amount || 0);

            // --- USE SMART RESOLVER HERE ---
            let bizName = resolveBusinessNameFromTxn(txn);

            // Parameters for the Action Button
            const traceNumber = txn.reference_number || 'N/A';
            const method = String(txn.payment_method || 'Cash').replace('_', ' ').toUpperCase();
            const safeImgUrl = encodeURIComponent(txn.receipt_image_url || txn.proof_of_payment_path || txn.proof_of_payment_url || '');

            let actionContent = '';
            if (status === 'pending' || status === 'pending_review') {
                actionContent = `<button class="btn btn-sm btn-primary fw-bold px-3 shadow-sm rounded-pill" onclick="openSimpleProof('${safeImgUrl}', '${amt}', '${method}', '${traceNumber}', ${txn.id}, '${status}')" title="Review Payment">Review</button>`;
            } else if (status === 'rejected') {
                actionContent = '<span class="badge bg-danger">Rejected</span>';
            } else {
                actionContent = '<span class="badge bg-success">Approved</span>';
            }

            tbody.insertAdjacentHTML('beforeend', `
            <tr class="align-middle">
                <td class="fw-bold text-dark ps-4">${bizName}</td>
                <td>${txn.transaction_type === 'initial_registration' ? 'Registration' : 'Renewal'}</td>
                <td class="fw-bold text-danger">₱${amt.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                <td class="font-monospace text-primary fw-bold">${txn.or_number || '---'}</td>
                <td>${(txn.created_at || '').split('T')[0]}</td>
                <td class="text-center">${actionContent}</td>
            </tr>
        `);
        });
    }

    // ==========================================
    // 3. MASTER INITIALIZATION
    // ==========================================
    function initCharts() {
        populateMainDashboard();
        renderRealDashboardCharts();
        renderRecentPayments();
        updateReportsDashboard();
    }

    async function fetchWelcomeName() {
        const welcomeEl = document.getElementById('dashWelcomeName');
        const sidebarName = document.getElementById('sidebarName');
        const sidebarEmail = document.getElementById('sidebarEmail');

        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/user`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) return;

            const responseData = await response.json();

            // Correctly handle the API response structure {data: {...}}
            const user = responseData.data || responseData;

            if (user) {
                if (welcomeEl) welcomeEl.innerText = user.first_name || user.name || 'Treasurer';
                if (sidebarName) {
                    sidebarName.innerText = user.first_name ? `${user.first_name} ${user.last_name || ''}` : (user.name || 'Treasurer');
                }
                if (sidebarEmail) {
                    sidebarEmail.innerText = user.email || 'No email';
                }
            }
        } catch (e) {
            console.error("Profile Fetch Failed:", e);
        }
    }

    async function fetchMembershipTypes() {
        if (!token) return;
        try {
            const endpointBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
            const res = await fetch(`${endpointBase}/v1/membership-types`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if (res.ok) {
                const json = await res.json();
                globalMembershipTypes = json.data || json || [];
            }
        } catch (err) {
            console.error('Error fetching membership types:', err);
        }
    }

    function formatPeso(value) {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(Number(value) || 0);
    }

    function getRecordDate(record) {
        return (record.created_at || record.date_approved || record.date_submitted || '').split('T')[0] || '';
    }

    function getMonthKey(dateValue) {
        const date = new Date(dateValue);
        if (Number.isNaN(date.getTime())) return null;
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
    }

    function getMonthLabel(key) {
        const [year, month] = key.split('-');
        return new Date(Number(year), Number(month) - 1, 1).toLocaleString('en-US', {
            month: 'short'
        });
    }

    // ==========================================
    // 1. EXACT AMOUNT CALCULATOR
    // ==========================================
    function getTransactionAmount(record) {
        if (record.amount !== null && record.amount !== undefined && parseFloat(record.amount) > 0) {
            return parseFloat(record.amount);
        }
        let typeId = record.membership_type_id || record.member?.membership_type_id || record.applicant?.membership_type_id;
        if (!typeId && record.member_id) {
            const foundM = allMembersData.find(m => String(m.id) === String(record.member_id));
            if (foundM) typeId = foundM.membership_type_id;
        }
        if (typeId && globalMembershipTypes.length > 0) {
            const found = globalMembershipTypes.find(t => String(t.id) === String(typeId));
            if (found) {
                if (record.transaction_type === 'renewal' && found.renewal_price) return parseFloat(found.renewal_price);
                return parseFloat(found.price);
            }
        }
        return parseFloat(record.membership_fee || 0);
    }

    function getMembershipLabel(record) {
        let typeId = record.membership_type_id ||
            record.member?.membership_type_id ||
            record.applicant?.membership_type_id;

        if (!typeId && record.member_id) {
            const foundM = allMembersData.find(m => String(m.id) === String(record.member_id));
            if (foundM) typeId = foundM.membership_type_id;
        }

        // 1. Try to find the exact name from the database types
        if (typeId && globalMembershipTypes.length > 0) {
            const found = globalMembershipTypes.find(t => String(t.id) === String(typeId));
            if (found) return found.name;
        }

        // ==========================================
        // 2. SMART FALLBACK (For Initial Registrations)
        // ==========================================
        // If the ID is missing, automatically infer the type from the amount paid!
        const amt = parseFloat(record.amount || 0);
        if (amt === 500) return 'Micro';
        if (amt === 5000) return 'Small Enterprise'; // Matches your 5000 requirement
        if (amt === 10000) return 'Medium';
        if (amt === 15000) return 'Large';

        // 3. Last resort: Check if there's a text-based company type in the applicant profile
        if (record.applicant?.organization_membership?.type_of_company) {
            return record.applicant.organization_membership.type_of_company;
        }

        return 'Unknown Type';
    }

    function getBusinessTypeLabel(record) {
        const profile = record.basic_profile || record.applicant?.basic_profile || {};
        return profile.business_type || profile.business_category || profile.business_nature || profile.business_line || 'Unknown';
    }

    function getLastSixMonthKeys() {
        const keys = [];
        const now = new Date();
        for (let index = 5; index >= 0; index--) {
            const date = new Date(now.getFullYear(), now.getMonth() - index, 1);
            keys.push(`${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`);
        }
        return keys;
    }

    function getTransactionKey(record, index = 0) {
        return record.id ?? record.or_number ?? `txn-${index}`;
    }

    function getTransactionRecordByKey(transactionKey) {
        return allTransactionsData.find((record, index) => String(getTransactionKey(record, index)) === String(transactionKey)) || null;
    }

    function getTransactionApiId(transactionKey) {
        const record = getTransactionRecordByKey(transactionKey);
        return record && record.id ? record.id : null;
    }

    function getDateKey(dateValue) {
        const dateObj = new Date(dateValue);
        if (Number.isNaN(dateObj.getTime())) return '';
        const year = dateObj.getFullYear();
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const day = String(dateObj.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function getDateFromKey(dateKey) {
        return new Date(`${dateKey}T00:00:00`);
    }

    function addDays(dateValue, days) {
        const dateObj = new Date(dateValue);
        dateObj.setDate(dateObj.getDate() + days);
        return dateObj;
    }

    function getPaymentRangeMeta(rangeKey) {
        const now = new Date();
        const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate());

        if (rangeKey === 'week') {
            const dayOfWeek = todayStart.getDay();
            const weekStart = addDays(todayStart, -dayOfWeek);
            const weekEnd = addDays(weekStart, 6);
            const prevWeekEnd = addDays(weekStart, -1);
            const prevWeekStart = addDays(prevWeekEnd, -6);
            return {
                currentStart: weekStart,
                currentEnd: weekEnd,
                prevStart: prevWeekStart,
                prevEnd: prevWeekEnd,
                currentLabel: "This Week's Payments:",
                previousLabel: 'Last week payment:'
            };
        }

        if (rangeKey === 'month') {
            const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
            const monthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            const prevMonthStart = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            const prevMonthEnd = new Date(now.getFullYear(), now.getMonth(), 0);
            return {
                currentStart: monthStart,
                currentEnd: monthEnd,
                prevStart: prevMonthStart,
                prevEnd: prevMonthEnd,
                currentLabel: "This Month's Payments:",
                previousLabel: 'Last month payment:'
            };
        }

        if (rangeKey === 'year') {
            const yearStart = new Date(now.getFullYear(), 0, 1);
            const yearEnd = new Date(now.getFullYear(), 11, 31);
            const prevYearStart = new Date(now.getFullYear() - 1, 0, 1);
            const prevYearEnd = new Date(now.getFullYear() - 1, 11, 31);
            return {
                currentStart: yearStart,
                currentEnd: yearEnd,
                prevStart: prevYearStart,
                prevEnd: prevYearEnd,
                currentLabel: "This Year's Payments:",
                previousLabel: 'Last year payment:'
            };
        }

        const yesterdayStart = addDays(todayStart, -1);
        return {
            currentStart: todayStart,
            currentEnd: todayStart,
            prevStart: yesterdayStart,
            prevEnd: yesterdayStart,
            currentLabel: "Today's Payments:",
            previousLabel: 'Yesterday payment:'
        };
    }

    function isDateBetween(dateObj, startDate, endDate) {
        if (!dateObj || Number.isNaN(dateObj.getTime())) return false;
        return dateObj >= startDate && dateObj <= endDate;
    }

    function updateDashboardPaymentSummary(rows) {
        const meta = getPaymentRangeMeta(dashboardPaymentRange);
        let currentTotal = 0;
        let previousTotal = 0;

        rows.forEach(txn => {
            const status = String(txn.status || 'pending').toLowerCase();
            if (!(status === 'paid' || status === 'completed')) return;

            const amountRaw = txn.amount || txn.membership_fee || (txn.membership_type_id === 1 ? 500 : 5000);
            const amount = parseFloat(amountRaw) || 0;
            const recordDate = getDateFromKey((txn.created_at || txn.date_approved || '').split('T')[0]);

            if (isDateBetween(recordDate, meta.currentStart, meta.currentEnd)) {
                currentTotal += amount;
            } else if (isDateBetween(recordDate, meta.prevStart, meta.prevEnd)) {
                previousTotal += amount;
            }
        });

        const fmt = val => `₱${val.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        const currentLabelEl = document.getElementById('today-payments-label');
        const previousLabelEl = document.getElementById('yesterday-payments-label');
        const currentValueEl = document.getElementById('today-payments-amt');
        const previousValueEl = document.getElementById('yesterday-payments-amt');

        if (currentLabelEl) currentLabelEl.innerText = meta.currentLabel;
        if (previousLabelEl) previousLabelEl.innerText = meta.previousLabel;
        if (currentValueEl) currentValueEl.innerText = fmt(currentTotal);
        if (previousValueEl) previousValueEl.innerText = fmt(previousTotal);
    }

    function buildDashboardRevenueSeries(rangeKey) {
        const completedStatuses = ['paid', 'completed'];
        const now = new Date();
        const monthShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const labels = [];
        const data = [];

        if (rangeKey === 'today') {
            const todayKey = getDateKey(now);
            const todayTotal = allTransactionsData.reduce((sum, txn) => {
                const status = String(txn.status || '').toLowerCase();
                const txnDateKey = (txn.created_at || txn.date_approved || '').split('T')[0];
                if (!completedStatuses.includes(status) || txnDateKey !== todayKey) return sum;
                return sum + getTransactionAmount(txn);
            }, 0);
            return {
                labels: ['Today'],
                data: [todayTotal]
            };
        }

        if (rangeKey === 'week') {
            for (let offset = 6; offset >= 0; offset--) {
                const dateObj = addDays(now, -offset);
                const dateKey = getDateKey(dateObj);
                labels.push(`${monthShort[dateObj.getMonth()]} ${dateObj.getDate()}`);
                const dayTotal = allTransactionsData.reduce((sum, txn) => {
                    const status = String(txn.status || '').toLowerCase();
                    const txnDateKey = (txn.created_at || txn.date_approved || '').split('T')[0];
                    if (!completedStatuses.includes(status) || txnDateKey !== dateKey) return sum;
                    return sum + getTransactionAmount(txn);
                }, 0);
                data.push(dayTotal);
            }
            return {
                labels,
                data
            };
        }

        if (rangeKey === 'year') {
            for (let monthIndex = 0; monthIndex < 12; monthIndex++) {
                labels.push(monthShort[monthIndex]);
                const monthTotal = allTransactionsData.reduce((sum, txn) => {
                    const status = String(txn.status || '').toLowerCase();
                    if (!completedStatuses.includes(status)) return sum;
                    const txnDate = getDateFromKey((txn.created_at || txn.date_approved || '').split('T')[0]);
                    if (Number.isNaN(txnDate.getTime())) return sum;
                    if (txnDate.getFullYear() === now.getFullYear() && txnDate.getMonth() === monthIndex) {
                        return sum + getTransactionAmount(txn);
                    }
                    return sum;
                }, 0);
                data.push(monthTotal);
            }
            return {
                labels,
                data
            };
        }

        const monthKeys = getLastSixMonthKeys();
        const monthLabels = monthKeys.map(getMonthLabel);
        const monthData = monthKeys.map(key => {
            return allTransactionsData.reduce((sum, txn) => {
                const status = String(txn.status || '').toLowerCase();
                if (!completedStatuses.includes(status)) return sum;
                const txnMonthKey = getMonthKey(getRecordDate(txn));
                if (txnMonthKey !== key) return sum;
                return sum + getTransactionAmount(txn);
            }, 0);
        });
        return {
            labels: monthLabels,
            data: monthData
        };
    }

    function updateTransactionSummary(rows) {
        let total = 0,
            pending = 0,
            complete = 0,
            failed = 0;

        rows.forEach(txn => {
            const amountRaw = txn.amount || txn.membership_fee || (txn.membership_type_id === 1 ? 500 : 5000);
            const amt = parseFloat(amountRaw) || 0;
            const status = String(txn.status || 'pending').toLowerCase();

            total += amt;
            if (status === 'completed' || status === 'paid') {
                complete += amt;
            } else if (status === 'failed') {
                failed += amt;
            } else {
                pending += amt;
            }
        });

        const fmt = val => `₱${val.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

        // SAFELY check if element exists before setting text
        const setSafe = (id, val) => {
            const el = document.getElementById(id);
            if (el) {
                el.innerText = val;
            }
        };

        setSafe('trans-total-amt', fmt(total));
        setSafe('trans-pending-amt', fmt(pending));
        setSafe('trans-complete-amt', fmt(complete));
        setSafe('trans-failed-amt', fmt(failed));

        if (typeof updateDashboardPaymentSummary === 'function') {
            updateDashboardPaymentSummary(rows);
        }
    }

    function updateTransactionPagination(totalPages) {
        const paginationText = document.getElementById('transaction-pagination-text');
        const prevBtn = document.getElementById('transaction-prev-btn');
        const nextBtn = document.getElementById('transaction-next-btn');

        if (paginationText) paginationText.innerText = `Page ${currentTransactionPage} of ${totalPages}`;
        if (prevBtn) prevBtn.disabled = currentTransactionPage <= 1;
        if (nextBtn) nextBtn.disabled = currentTransactionPage >= totalPages;
    }

    function displayTransactionsPage() {
        const totalPages = Math.ceil(filteredTransactionsData.length / transactionsPerPage) || 1;
        if (currentTransactionPage > totalPages) currentTransactionPage = totalPages;
        if (currentTransactionPage < 1) currentTransactionPage = 1;

        const pageData = filteredTransactionsData.slice((currentTransactionPage - 1) * transactionsPerPage, currentTransactionPage * transactionsPerPage);
        renderTransactionRows(pageData);
        updateTransactionPagination(totalPages);
    }

    // ==========================================
    // RENDER TRANSACTIONS TABLE & STATS
    // ==========================================
    function renderTransactionRows(rows) {
        // 1. UPDATE THE 4 DASHBOARD STAT CARDS DYNAMICALLY
        updateTransactionLedgerStats();

        // 2. RENDER THE HTML TABLE
        const tbodyTrans = document.getElementById('transactions-table-body');
        if (!tbodyTrans) return;
        tbodyTrans.innerHTML = '';

        if (rows.length > 0) {
            rows.forEach((txn) => {
                const status = String(txn.status || 'pending').toLowerCase();
                const txnDate = (txn.created_at || '').split('T')[0] || 'N/A';

                let typeDisplay = 'Unknown';
                let typeBadgeColor = 'text-secondary';
                if (txn.transaction_type === 'initial_registration') {
                    typeDisplay = 'Registration';
                    typeBadgeColor = 'text-primary';
                } else if (txn.transaction_type === 'renewal') {
                    typeDisplay = 'Renewal';
                    typeBadgeColor = 'text-success';
                }

                // --- SMART BUSINESS NAME RESOLVER ---
                let businessName = resolveBusinessNameFromTxn(txn);

                const amt = parseFloat(txn.amount || 0);
                const membershipText = txn.membership_type_name || 'N/A';
                const fmtAmt = `₱ ${amt.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

                // Hide OR Number if it is still pending
                const isApproved = ['approved', 'paid', 'completed', 'success'].includes(status);
                const orNumber = isApproved ? (txn.or_number || '---') : '---';

                const traceNumber = txn.reference_number || 'N/A';
                const method = String(txn.payment_method || 'Cash').replace('_', ' ').toUpperCase();
                const safeImgUrl = encodeURIComponent(txn.receipt_image_url || txn.proof_of_payment_path || txn.proof_of_payment_url || '');

                let statBadge = '';
                if (status === 'pending' || status === 'pending_review') {
                    statBadge = '<span class="badge bg-warning text-dark px-3 py-1 rounded-pill">PENDING</span>';
                } else if (isApproved) {
                    statBadge = '<span class="badge bg-success text-white px-3 py-1 rounded-pill">APPROVED</span>';
                } else {
                    statBadge = '<span class="badge bg-danger text-white px-3 py-1 rounded-pill">REJECTED</span>';
                }

                const viewBtn = `<button class="btn btn-sm btn-primary fw-bold px-3 shadow-sm rounded-pill" onclick="openSimpleProof('${safeImgUrl}', '${amt}', '${method}', '${traceNumber}', ${txn.id}, '${status}')" title="Review Payment"><i class="fa fa-search me-1"></i> Review</button>`;

                tbodyTrans.insertAdjacentHTML('beforeend', `
                <tr class="align-middle">
                    <td class="fw-bold text-dark ps-4">${businessName}<div class="text-muted fw-normal" style="font-size: 10px;">${membershipText}</div></td>
                    <td class="fw-bold ${typeBadgeColor}">${typeDisplay}</td>
                    <td class="fw-bold text-dark">${fmtAmt}</td>
                    <td class="text-dark">${method}</td>
                    <td class="text-dark fw-bold font-monospace">${orNumber}</td>
                    <td class="text-dark">${txnDate}</td>
                    <td class="text-center">${statBadge}</td>
                    <td class="text-center">
                        ${viewBtn}
                    </td>
                </tr>
            `);
            });
        } else {
            tbodyTrans.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted">No transactions available.</td></tr>`;
        }
    }

    // THE APPROVE ACTION
    async function approveTreasurerPayment(paymentId, event) {
        if (!confirm("Approve this payment and reactivate the member?")) return;
        const btn = event.target;

        try {
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

            const response = await fetch(`${window.API_BASE_URL}/v1/members/approve-renewal-payment/${paymentId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await response.json();
            if (response.ok) {
                alert("Success! Payment approved and member reactivated.");
                if (typeof fetchTransactions === 'function') fetchTransactions();
                if (typeof fetchMembers === 'function') fetchMembers();
            } else {
                alert("Approval Failed: " + (data.message || "Unknown error."));
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (error) {
            alert("Network error.");
            btn.disabled = false;
            btn.innerHTML = 'Approve';
        }
    }

    // ==========================================
    // REJECT ACTION SCRIPT (Make sure this is at the bottom of the file!)
    // ==========================================
    async function rejectTreasurerPayment(paymentId, event) {
        const reason = prompt("Please enter the reason for rejecting this payment (e.g., 'Image is blurry'):");
        if (!reason || reason.trim() === '') return;

        const token = localStorage.getItem('token');
        const btn = event.target;

        try {
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

            const response = await fetch(`${window.API_BASE_URL}/v1/members/reject-renewal-payment/${paymentId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    rejection_reason: reason.trim()
                })
            });

            const data = await response.json();

            if (response.ok) {
                alert("Success: " + (data.message || "Payment rejected."));
                if (typeof fetchTransactions === 'function') fetchTransactions();
                if (typeof fetchMembers === 'function') fetchMembers();
            } else {
                alert("Rejection Failed: " + (data.message || "Unknown error occurred."));
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (error) {
            alert("A network error occurred while trying to reject the payment.");
            btn.disabled = false;
            btn.innerHTML = 'Reject';
        }
    }

    function openTransactionEditModal(transactionKey) {
        const record = getTransactionRecordByKey(transactionKey);
        if (!record) return;

        editingTransactionId = String(transactionKey);
        document.getElementById('addPaymentModalTitle').innerText = 'Edit Payment';
        document.getElementById('transactionModalConfirmBtn').innerText = 'Save Changes';

        const businessName = record.applicant?.basic_profile?.registered_business_name || record.basic_profile?.registered_business_name || 'Unknown';
        const paymentType = record.payment_type || 'GCash';
        const membershipType = getMembershipLabel(record) === 'Small Enterprise' ? 'Annual' : 'Annual';
        const paymentDate = (record.created_at || record.date_approved || '').split('T')[0] || '';

        document.getElementById('transactionMemberInput').value = businessName;
        document.getElementById('transactionOrNumber').value = record.or_number || record.official_receipt_no || '---';
        document.getElementById('transactionPaymentDate').value = paymentDate;
        document.getElementById('transactionMembershipType').value = membershipType;
        document.getElementById('transactionPaymentType').value = paymentType;
        document.getElementById('transactionProofInput').value = record.proof_of_payment_url ? 'Attached' : 'No file';
        document.getElementById('transactionReceiverSelect').value = record.receiver || 'Jesus Versula';

        openAddPaymentModal('edit');
    }

    async function deleteTransactionRecord(transactionKey) {
        const record = getTransactionRecordByKey(transactionKey);
        if (!record) return;

        const businessName = record.applicant?.basic_profile?.registered_business_name || record.basic_profile?.registered_business_name || 'this transaction';
        if (!confirm(`Delete the transaction for ${businessName}?`)) return;

        const apiId = getTransactionApiId(transactionKey);
        if (!apiId) {
            alert('This transaction cannot be deleted because it has no backend id.');
            return;
        }

        try {
            const response = await fetch(`/treasurer/transactions/${apiId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Failed to delete transaction.');
            }

            await fetchTransactions();
            alert('Transaction deleted.');
        } catch (error) {
            console.error(error);
            alert(error.message || 'Failed to delete transaction.');
        }
    }

    function updateReportsDashboard() {
        const activeMembersCount = allMembersData.length;
        const pendingCount = allApplicantsData.filter(app => String(app.status).toLowerCase() !== 'paid').length;
        const newThisWeekCount = allMembersData.filter(member => {
            const createdAt = new Date(member.created_at || 0);
            if (Number.isNaN(createdAt.getTime())) return false;
            const diffDays = (Date.now() - createdAt.getTime()) / (1000 * 60 * 60 * 24);
            return diffDays >= 0 && diffDays <= 7;
        }).length;

        const completedStatuses = ['paid', 'completed', 'approved'];
        const failedStatuses = ['failed', 'cancelled'];
        const currentMonthKey = getMonthKey(new Date().toISOString());

        const previousMonthDate = new Date();
        previousMonthDate.setMonth(previousMonthDate.getMonth() - 1);
        const previousMonthKey = getMonthKey(previousMonthDate.toISOString());

        let currentMonthRevenue = 0;
        let previousMonthRevenue = 0;
        let failedCount = 0;
        let currentMonthFailedCount = 0;
        let previousMonthFailedCount = 0;
        let collectedAmount = 0;
        let overdueAmount = 0;

        const businessTypeCounts = {};
        const monthKeys = getLastSixMonthKeys();
        const monthBuckets = {};

        monthKeys.forEach(key => {
            monthBuckets[key] = {
                micro: 0,
                small: 0
            };
        });

        allTransactionsData.forEach(record => {
            const status = String(record.status || '').toLowerCase();
            const amount = getTransactionAmount(record);
            const dateKey = getMonthKey(getRecordDate(record));
            const membershipLabel = getMembershipLabel(record);

            if (dateKey && monthBuckets[dateKey]) {
                if (membershipLabel.includes('Small')) {
                    monthBuckets[dateKey].small += amount;
                } else {
                    monthBuckets[dateKey].micro += amount;
                }
            }

            if (completedStatuses.includes(status)) {
                collectedAmount += amount;
                if (dateKey === currentMonthKey) currentMonthRevenue += amount;
                if (dateKey === previousMonthKey) previousMonthRevenue += amount;
            } else if (failedStatuses.includes(status)) {
                failedCount++;
                overdueAmount += amount;
                if (dateKey === currentMonthKey) currentMonthFailedCount++;
                if (dateKey === previousMonthKey) previousMonthFailedCount++;
            } else {
                overdueAmount += amount;
            }
        });

        allApplicantsData.forEach(record => {
            const label = String(getBusinessTypeLabel(record)).trim() || 'Unknown';
            businessTypeCounts[label] = (businessTypeCounts[label] || 0) + 1;
        });

        const totalCollectionBase = collectedAmount + overdueAmount;
        const collectedPercent = totalCollectionBase > 0 ? Math.round((collectedAmount / totalCollectionBase) * 100) : 0;
        const overduePercent = totalCollectionBase > 0 ? Math.max(0, 100 - collectedPercent) : 0;

        const revenueTrend = previousMonthRevenue > 0 ?
            `${(((currentMonthRevenue - previousMonthRevenue) / previousMonthRevenue) * 100).toFixed(1)}%` :
            '0.0%';

        const failedTrend = previousMonthFailedCount > 0 ?
            `${currentMonthFailedCount - previousMonthFailedCount}` :
            `${currentMonthFailedCount}`;

        // Update DOM elements
        const monthlyRevenueEl = document.getElementById('report-monthly-revenue');
        if (monthlyRevenueEl) monthlyRevenueEl.innerText = formatPeso(currentMonthRevenue);

        const revenueTrendEl = document.getElementById('report-monthly-revenue-trend');
        if (revenueTrendEl) {
            const trendValue = parseFloat(revenueTrend);
            const trendIsPositive = Number.isNaN(trendValue) || trendValue >= 0;
            revenueTrendEl.className = `report-indicator ${trendIsPositive ? 'text-green' : 'text-red'}`;
            revenueTrendEl.innerHTML = `<i class="fa ${trendIsPositive ? 'fa-arrow-up' : 'fa-arrow-down'}"></i> ${revenueTrend} <span class="text-muted fw-normal">vs last month</span>`;
        }

        const activeMembersEl = document.getElementById('report-active-members');
        if (activeMembersEl) activeMembersEl.innerText = activeMembersCount;

        const newThisWeekEl = document.getElementById('report-new-this-week');
        if (newThisWeekEl) newThisWeekEl.innerHTML = `<i class="fa fa-arrow-up"></i> ${newThisWeekCount} <span class="text-muted fw-normal">new this week</span>`;

        const pendingCountEl = document.getElementById('report-pending-count');
        if (pendingCountEl) pendingCountEl.innerText = pendingCount;

        const failedCountEl = document.getElementById('report-failed-count');
        if (failedCountEl) failedCountEl.innerText = failedCount;

        const failedTrendEl = document.getElementById('report-failed-trend');
        if (failedTrendEl) {
            failedTrendEl.innerHTML = `<i class="fa fa-arrow-down"></i> ${failedTrend} <span class="text-muted fw-normal">vs last month</span>`;
        }

        const collectedPercentEl = document.getElementById('report-collected-percent');
        if (collectedPercentEl) collectedPercentEl.innerText = `${collectedPercent}%`;

        const overduePercentEl = document.getElementById('report-overdue-percent');
        if (overduePercentEl) overduePercentEl.innerText = `${overduePercent}%`;

        const businessTypeBody = document.getElementById('report-business-type-body');
        if (businessTypeBody) {
            const entries = Object.entries(businessTypeCounts).sort((a, b) => b[1] - a[1]);
            if (entries.length === 0) {
                businessTypeBody.innerHTML = `<tr><td colspan="2" class="text-muted">No business types available.</td></tr>`;
            } else {
                const totalBusinesses = entries.reduce((sum, [, count]) => sum + count, 0);
                businessTypeBody.innerHTML = entries.slice(0, 6).map(([label, count]) => {
                    const pct = totalBusinesses > 0 ? Math.round((count / totalBusinesses) * 100) : 0;
                    return `<tr><td>${label}</td><td class="text-end fw-bold">${pct}%</td></tr>`;
                }).join('');
            }
        }

        // ==========================================
        // FIX: CLEANUP BEFORE REDRAWING CHARTS
        // ==========================================
        const existingBar = Chart.getChart('reportBarChart');
        if (existingBar) existingBar.destroy();

        const existingPie = Chart.getChart('reportPieChart');
        if (existingPie) existingPie.destroy();

        const reportBar = document.getElementById('reportBarChart');
        if (reportBar) {
            const barLabels = monthKeys.map(getMonthLabel);
            const microData = monthKeys.map(key => monthBuckets[key].micro);
            const smallData = monthKeys.map(key => monthBuckets[key].small);

            new Chart(reportBar.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: [{
                            label: 'Micro',
                            data: microData,
                            backgroundColor: '#3b82f6',
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Small',
                            data: smallData,
                            backgroundColor: '#ef4444',
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: '#eee',
                                borderDash: [5, 5]
                            },
                            ticks: {
                                color: '#aaa',
                                font: {
                                    size: 11
                                }
                            },
                            border: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#aaa',
                                font: {
                                    size: 11
                                }
                            },
                            border: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        const reportPie = document.getElementById('reportPieChart');
        if (reportPie) {
            new Chart(reportPie.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Collected', 'Overdue'],
                    datasets: [{
                        data: [collectedPercent, overduePercent],
                        backgroundColor: ['#22c55e', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // --- OTP MODAL & NEW PASSWORD MODAL LOGIC ---
    function openOtpFeedbackModal(title, message) {
        const titleEl = document.getElementById('otpFeedbackTitle');
        const msgEl = document.getElementById('otpFeedbackMessage');
        if (titleEl) titleEl.innerText = title || 'Notice';
        if (msgEl) msgEl.innerText = message || '';
        const modal = document.getElementById('otpFeedbackModal');
        if (modal) modal.style.display = 'flex';
    }

    function hideOtpFeedbackModal() {
        const modal = document.getElementById('otpFeedbackModal');
        if (modal) modal.style.display = 'none';
    }

    function closeOtpFeedbackOverlay(e) {
        if (e.target && e.target.id === 'otpFeedbackModal') hideOtpFeedbackModal();
    }

    async function requestPasswordChangeOtp() {
        const btn = document.getElementById('requestOtpBtn');
        const otpApiBase = (window.PCCI_API_BASE_URL || window.API_BASE_URL || '').replace(/\/$/, '');
        const endpoint = `${otpApiBase}/user/confirm-password-change`;
        currentPasswordOtpCode = '';
        const candidateEmail = (
            document.getElementById('settingsEmailInput')?.value ||
            localStorage.getItem('userEmail') ||
            ''
        ).trim();

        if (btn) {
            btn.disabled = true;
            btn.dataset.originalText = btn.innerText;
            btn.innerText = 'Sending...';
        }

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...(token ? {
                        'Authorization': `Bearer ${token}`
                    } : {})
                },
                body: JSON.stringify({
                    email: candidateEmail || null
                })
            });

            const {
                data: result,
                raw
            } = await readApiResponse(response);
            if (!response.ok) throw new Error(result.message || raw || 'Failed to request OTP. Please try again.');

            const otpPayload = result?.data || {};
            const otpEmailEl = document.getElementById('otpTargetEmail');
            const emailFromApi = (otpPayload.email || candidateEmail || '').trim();
            if (!emailFromApi) throw new Error('OTP response missing email.');

            currentPasswordOtpCode = otpPayload.otp ? String(otpPayload.otp) : '';
            if (otpEmailEl) otpEmailEl.innerText = emailFromApi;
            currentPasswordOtpEmail = emailFromApi;

            openOtpModal();
            openOtpFeedbackModal('OTP Sent', result.message || 'OTP has been sent to your email.');
        } catch (error) {
            console.error('Error requesting password OTP:', error);
            openOtpFeedbackModal('Request Failed', error.message || 'Failed to request OTP. Please try again.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerText = btn.dataset.originalText || 'Update Password';
            }
        }
    }

    function openOtpModal() {
        document.getElementById('otpModal').style.display = 'flex';
    }

    function hideOtpModal() {
        document.getElementById('otpModal').style.display = 'none';
        document.querySelectorAll('.otp-box').forEach(box => box.value = '');
    }

    function closeOtpOverlay(e) {
        if (e.target.id === 'otpModal') hideOtpModal();
    }

    function moveToNext(input, event) {
        input.value = String(input.value || '').replace(/\D/g, '').slice(-1);
        if (input.value.length === 1) {
            let next = input.nextElementSibling;
            if (next && next.tagName.toLowerCase() === 'input') {
                next.focus();
            } else if (!next) {
                currentPasswordOtpCode = Array.from(document.querySelectorAll('.otp-box')).map(box => box.value).join('');
                verifyEnteredOtpAndProceed();
            }
        }
    }

    function pasteOtpIntoTreasurerBoxes(event) {
        event.preventDefault();
        const pasted = (event.clipboardData?.getData('text') || '').replace(/\D/g, '');
        if (!pasted) return;

        const boxes = Array.from(document.querySelectorAll('.otp-box'));
        const digits = pasted.slice(0, boxes.length).split('');

        boxes.forEach((box, index) => {
            box.value = digits[index] || '';
        });
        currentPasswordOtpCode = boxes.map(box => box.value).join('');
        const firstEmpty = boxes.find(box => !box.value);
        if (firstEmpty) {
            firstEmpty.focus();
        } else {
            verifyEnteredOtpAndProceed();
        }
    }

    async function verifyEnteredOtpAndProceed() {
        if (!currentPasswordOtpCode || currentPasswordOtpCode.length !== 6) {
            openOtpFeedbackModal('Invalid OTP', 'Please enter a valid 6-digit OTP.');
            return;
        }
        hideOtpModal();
        openResetPasswordModal();
    }

    document.querySelectorAll('.otp-box').forEach(box => {
        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value) {
                let prev = this.previousElementSibling;
                if (prev && prev.tagName.toLowerCase() === 'input') prev.focus();
            }
        });
        box.addEventListener('paste', pasteOtpIntoTreasurerBoxes);
    });

    // --- RESET PASSWORD MODAL FUNCTIONS ---
    function openResetPasswordModal() {
        document.getElementById('resetPasswordModal').style.display = 'flex';
    }

    function hideResetPasswordModal() {
        document.getElementById('resetPasswordModal').style.display = 'none';
        document.getElementById('newPasswordInput').value = '';
        document.getElementById('rePasswordInput').value = '';
        validatePassword();
    }

    function closeResetPasswordOverlay(e) {
        if (e.target.id === 'resetPasswordModal') hideResetPasswordModal();
    }

    function togglePasswordView(inputId) {
        const input = document.getElementById(inputId);
        input.type = input.type === "password" ? "text" : "password";
    }

    function validatePassword() {
        const pw = document.getElementById('newPasswordInput').value;
        const reqLower = document.getElementById('req-lower');
        const reqLen = document.getElementById('req-len');
        const reqUpper = document.getElementById('req-upper');
        const reqNum = document.getElementById('req-num');
        const submitBtn = document.getElementById('resetPwSubmitBtn');

        let validCount = 0;
        if (/[a-z]/.test(pw)) {
            reqLower.classList.add('valid');
            validCount++;
        } else {
            reqLower.classList.remove('valid');
        }
        if (pw.length >= 8) {
            reqLen.classList.add('valid');
            validCount++;
        } else {
            reqLen.classList.remove('valid');
        }
        if (/[A-Z]/.test(pw)) {
            reqUpper.classList.add('valid');
            validCount++;
        } else {
            reqUpper.classList.remove('valid');
        }
        if (/[0-9]/.test(pw)) {
            reqNum.classList.add('valid');
            validCount++;
        } else {
            reqNum.classList.remove('valid');
        }

        if (validCount === 4) {
            submitBtn.classList.add('active');
        } else {
            submitBtn.classList.remove('active');
        }
    }
    async function submitNewPassword() {
        const pw1 = document.getElementById('newPasswordInput').value;
        const pw2 = document.getElementById('rePasswordInput').value;
        const btn = document.getElementById('resetPwSubmitBtn');
        const otpApiBase = (window.PCCI_API_BASE_URL || window.API_BASE_URL || '').replace(/\/$/, '');

        if (!btn.classList.contains('active')) {
            alert("Please ensure your password meets all security requirements.");
            return;
        }
        if (pw1 !== pw2) {
            alert("Passwords do not match!");
            return;
        }
        if (!currentPasswordOtpCode || currentPasswordOtpCode.length !== 6) {
            alert('OTP is missing or invalid. Please request and enter OTP again.');
            return;
        }

        try {
            btn.disabled = true;
            btn.innerText = 'Resetting...';

            const response = await fetch(`${otpApiBase}/user/request-password-change`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...(token ? {
                        'Authorization': `Bearer ${token}`
                    } : {})
                },
                body: JSON.stringify({
                    otp: currentPasswordOtpCode,
                    email: currentPasswordOtpEmail || null,
                    new_password: pw1,
                    new_password_confirmation: pw2,
                    password: pw1,
                    password_confirmation: pw2
                })
            });

            const {
                data: result,
                raw
            } = await readApiResponse(response);
            if (!response.ok) throw new Error(result.message || raw || 'Failed to reset password.');

            alert(result.message || 'Password updated successfully.');
            currentPasswordOtpCode = '';
            hideResetPasswordModal();
        } catch (error) {
            console.error('Error resetting password:', error);
            alert(error.message || 'Failed to reset password.');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Reset Password';
        }
    }

    // --- CROP PROFILE PICTURE MODAL LOGIC ---
    function openCropModal() {
        document.getElementById('cropModal').style.display = 'flex';
    }

    function hideCropModal() {
        document.getElementById('cropModal').style.display = 'none';
    }

    function closeCropOverlay(e) {
        if (e.target.id === 'cropModal') hideCropModal();
    }

    function setNewProfilePicture() {
        alert("Profile picture successfully updated!");
        hideCropModal();
    }

    // --- DROPDOWN MENUS LOGIC ---
    function toggleReportDropdown(e) {
        e.stopPropagation();
        const menu = document.getElementById('reportDropdownMenu');
        menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
    }

    function toggleTransDropdown(e) {
        e.stopPropagation();
        const menu = document.getElementById('transDropdownMenu');
        menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
    }

    function toggleTransFilter(e) {
        e.stopPropagation();
        const menu = document.getElementById('transFilterMenu');
        menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
    }

    function downloadReport(type) {
        alert(`Initiating ${type.toUpperCase()} Report Download...`);
        document.getElementById('reportDropdownMenu').style.display = 'none';
    }

    function exportTransactions() {
        alert("Preparing transaction data for export...");
        document.getElementById('transDropdownMenu').style.display = 'none';
    }

    // --- TRANSACTIONS SEARCH & FILTER LOGIC ---
    let currentTransFilter = 'all';

    function filterTransactions(filterType) {
        currentTransFilter = filterType;
        document.getElementById('transFilterMenu').style.display = 'none';
        applyTransactionFilters();
    }

    function applyTransactionFilters() {
        const transSearchInput = document.getElementById('transactionSearch');
        const searchTerm = transSearchInput ? transSearchInput.value.toLowerCase() : '';

        filteredTransactionsData = allTransactionsData.filter((txn, index) => {
            const status = String(txn.status || 'pending').toLowerCase();
            const businessName = (txn.applicant?.basic_profile?.registered_business_name || txn.basic_profile?.registered_business_name || '').toLowerCase();
            const orNumber = String(txn.or_number || txn.official_receipt_no || '').toLowerCase();
            const rowText = `${businessName} ${orNumber} ${status}`;

            let matchesFilter = false;
            if (currentTransFilter === 'all') {
                matchesFilter = true;
            } else if (currentTransFilter === 'completed') {
                matchesFilter = status === 'completed' || status === 'paid';
            } else {
                matchesFilter = status.includes(currentTransFilter);
            }

            const matchesSearch = rowText.includes(searchTerm);
            return matchesFilter && matchesSearch;
        });

        filteredTransactionsData.sort((a, b) => {
            const statusA = String(a.status || 'pending').toLowerCase();
            const statusB = String(b.status || 'pending').toLowerCase();
            const isPendingA = statusA === 'pending' ? 0 : 1;
            const isPendingB = statusB === 'pending' ? 0 : 1;
            return isPendingA - isPendingB;
        });

        currentTransactionPage = 1;
        displayTransactionsPage();
    }

    function prevTransactionPage() {
        if (currentTransactionPage > 1) {
            currentTransactionPage--;
            displayTransactionsPage();
        }
    }

    function nextTransactionPage() {
        if (currentTransactionPage < Math.ceil(filteredTransactionsData.length / transactionsPerPage)) {
            currentTransactionPage++;
            displayTransactionsPage();
        }
    }

    document.addEventListener('click', (e) => {
        const reportMenu = document.getElementById('reportDropdownMenu');
        if (reportMenu && reportMenu.style.display === 'flex' && !e.target.closest('#reportDropdownContainer')) {
            reportMenu.style.display = 'none';
        }
        const transMenu = document.getElementById('transDropdownMenu');
        if (transMenu && transMenu.style.display === 'flex' && !e.target.closest('#transMenuContainer')) {
            transMenu.style.display = 'none';
        }
        const filterMenu = document.getElementById('transFilterMenu');
        if (filterMenu && filterMenu.style.display === 'flex' && !e.target.closest('#transFilterContainer')) {
            filterMenu.style.display = 'none';
        }
        const p = document.getElementById('notificationPanel');
        if (p && p.style.display === 'flex' && !p.contains(e.target) && !e.target.closest('.fa-bell')) {
            p.style.display = 'none';
        }
    });

    // --- DARK MODE LOGIC ---
    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        const icon = document.getElementById('darkModeIcon');
        const text = document.getElementById('darkModeText');
        const switchBtn = document.getElementById('darkModeSwitch');

        if (document.body.classList.contains('dark-mode')) {
            if (icon) icon.classList.replace('fa-moon', 'fa-sun');
            if (text) text.innerText = 'Light Mode';
            if (switchBtn) switchBtn.checked = true;
            localStorage.setItem('theme', 'dark');
        } else {
            if (icon) icon.classList.replace('fa-sun', 'fa-moon');
            if (text) text.innerText = 'Dark Mode';
            if (switchBtn) switchBtn.checked = false;
            localStorage.setItem('theme', 'light');
        }
    }

    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        setTimeout(() => {
            const icon = document.getElementById('darkModeIcon');
            const text = document.getElementById('darkModeText');
            const switchBtn = document.getElementById('darkModeSwitch');
            if (icon) icon.classList.replace('fa-moon', 'fa-sun');
            if (text) text.innerText = 'Light Mode';
            if (switchBtn) switchBtn.checked = true;
        }, 50);
    }

    // --- SETTINGS VIEW SWITCHER ---
    function openSetting(id) {
        document.getElementById('settings-main').style.display = 'none';
        document.getElementById(id).style.display = 'block';
    }

    function closeSetting(id) {
        document.getElementById(id).style.display = 'none';
        document.getElementById('settings-main').style.display = 'block';
    }

    const notifApiBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
    console.log("Fetching from:", `${notifApiBase}/v1/notifications`);

    async function updateNotificationsPanel() {
        try {
            // Fetch from your new single endpoint
            const response = await fetch(`${notifApiBase}/v1/notifications`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    // Add Authorization header here if you aren't using session cookies
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });

            if (!response.ok) throw new Error('Failed to fetch notifications');

            const data = await response.json();
            const items = data.notifications || [];
            const unreadCount = data.unread_count || 0;

            // Save globally so the Modal can use the exact same data without re-fetching
            window.allNotificationItems = items;

            const notifBody = document.getElementById('notifBody');
            const notifBadge = document.getElementById('notifBadge');
            const redDot = document.querySelector('.fa-bell')?.nextElementSibling;

            if (items.length > 0) {
                // Slice the top 6 for the compact dropdown view
                notifBody.innerHTML = items.slice(0, 6).map(item => {
                    const dateObj = new Date(item.created_at);
                    const dateStr = isNaN(dateObj.getTime()) ? 'Recent' : dateObj.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric'
                    });

                    // Laravel stores our custom data inside the 'data' JSON column
                    const payload = item.data;

                    // Slight green tint if unread, white if read
                    const bgStyle = item.read_at === null ? 'background: #f0fdf4;' : 'background: #ffffff;';

                    return `
                <div class="notif-item" style="${bgStyle} padding: 12px 15px; border-bottom: 1px solid #f3f4f6; display: flex; gap: 12px; align-items: flex-start; cursor: pointer; transition: background 0.2s;" onclick="markNotificationAsRead('${item.id}')">
                    <div class="notif-icon" style="width: 32px; height: 32px; border-radius: 50%; display: flex; justify-content: center; align-items: center; background: #f9fafb; border: 1px solid #e5e7eb; flex-shrink: 0;">
                        <i class="fa ${payload.icon} ${payload.tone}" style="font-size: 13px;"></i>
                    </div>
                    <div class="notif-text-content" style="width: 100%;">
                        <div class="d-flex justify-content-between align-items-start">
                            <p class="fw-bold mb-0 text-dark" style="font-size: 13px;">${payload.title}</p>
                            <small class="text-muted" style="font-size: 10px;">${dateStr}</small>
                        </div>
                        <small style="font-size: 11px; color: #4b5563; display: block; margin-top: 2px;">${payload.message}</small>
                    </div>
                </div>
            `
                }).join('');

                // Update Badges
                if (notifBadge) notifBadge.innerText = `${unreadCount} New`;
                if (redDot) redDot.style.display = unreadCount > 0 ? 'block' : 'none';
            } else {
                // Empty State
                notifBody.innerHTML = `
                <div class="notif-item" style="background: #f9fafb; padding: 15px; text-align: center;">
                    <i class="fa fa-check-circle text-success fs-3 mb-2 d-block"></i>
                    <p class="text-dark fw-bold mb-0" style="font-size: 13px;">No live notifications</p>
                    <small class="text-muted" style="font-size: 11px;">You're all caught up!</small>
                </div>
            `;
                if (notifBadge) notifBadge.innerText = `0 New`;
                if (redDot) redDot.style.display = 'none';
            }
        } catch (error) {
            console.error('Error updating notifications:', error);
        }
    }




    function openFullNotificationsModal() {
        document.getElementById('notificationPanel').style.display = 'none';

        const modalBody = document.getElementById('fullNotificationsBody');
        const items = window.allNotificationItems || [];

        if (items.length > 0) {
            // Render ALL items in the array, unsliced
            modalBody.innerHTML = items.map(item => {
                const dateObj = new Date(item.created_at);
                const dateStr = isNaN(dateObj.getTime()) ?
                    'Recent' :
                    dateObj.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                const payload = item.data;
                const bgStyle = item.read_at === null ? 'background: #f0fdf4;' : 'background: #ffffff;';

                return `
            <div class="p-4 border-bottom d-flex align-items-start gap-3 transition-hover" style="${bgStyle} cursor: pointer;" onclick="markNotificationAsRead('${item.id}')">
                <div style="width: 45px; height: 45px; border-radius: 50%; background: #f9fafb; border: 1px solid #e5e7eb; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                    <i class="fa ${payload.icon} ${payload.tone} fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 text-dark">${payload.title}</h6>
                    <p class="mb-2 text-muted" style="font-size: 14px;">${payload.message}</p>
                    <small class="text-secondary fw-bold" style="font-size: 11px;"><i class="fa fa-clock me-1"></i> ${dateStr}</small>
                </div>
            </div>`;
            }).join('');
        } else {
            modalBody.innerHTML = `<div class="p-5 text-center text-muted fw-bold">No notifications found.</div>`;
        }

        const modal = new bootstrap.Modal(document.getElementById('fullNotificationsModal'));
        modal.show();
    }

    // 4. INTERACTIVE HELPER: MARK ALL AS READ
    async function markNotificationAsRead(id) {
        if (!id) return;
        try {
            await fetch(`${notifApiBase}/v1/notifications/${id}/read`, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });
        } catch (error) {
            console.error('Error marking notification as read:', error);
        } finally {
            updateNotificationsPanel();
        }
    }

    async function markAllNotificationsAsRead() {
        try {
            await fetch(`${notifApiBase}/v1/notifications/read-all`, {
                method: 'POST', // The route we made uses POST for this
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}` // Ensure token is sent
                }
            });

            // Refresh the panel immediately to clear all green backgrounds and red dots
            updateNotificationsPanel();

            if (document.getElementById('fullNotificationsModal').classList.contains('show')) {
                setTimeout(openFullNotificationsModal, 200);
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    }

    // ==========================================
    // OPEN PROOF MODAL WITH BUTTONS & DETAILS
    // ==========================================
    function openSimpleProof(encodedUrl, amount, method, refNum, txnId, status) {
        const url = decodeURIComponent(encodedUrl || '');

        // Populate Details
        const amtEl = document.getElementById('spModalAmount');
        if (amtEl) amtEl.innerText = amount ? `₱${parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2})}` : '₱0.00';

        const methodEl = document.getElementById('spModalMethod');
        if (methodEl) methodEl.innerText = (method || 'N/A').toUpperCase();

        const refEl = document.getElementById('spModalRef');
        if (refEl) refEl.innerText = refNum || 'N/A';

        // Inject Approve/Reject Buttons if pending
        const actionDiv = document.getElementById('spModalActions');
        if (status === 'pending' || status === 'pending_review') {
            actionDiv.innerHTML = `
                <button class="btn btn-success fw-bold px-4 py-2 shadow-sm rounded-pill" onclick="approveTreasurerPayment(${txnId}, event); hideSimpleProofModal();"><i class="fa fa-check me-1"></i> Approve Payment</button>
                
                <button class="btn btn-danger fw-bold px-4 py-2 shadow-sm rounded-pill" onclick="openRejectPaymentModal(${txnId}); hideSimpleProofModal();"><i class="fa fa-times me-1"></i> Reject</button>
            `;
            actionDiv.style.display = 'flex';
        } else {
            actionDiv.style.display = 'none';
            actionDiv.innerHTML = '';
        }

        if (!url || url === '#' || url === 'null' || url === 'undefined' || url === 'N/A' || url.trim() === '') {
            alert("No receipt image was found in the database.");
            return;
        }

        const img = document.getElementById('simpleModalImage');
        const spinner = document.getElementById('simpleModalSpinner');

        // Reset zoom state on open
        img.dataset.zoomed = 'false';
        img.style.maxWidth = '100%';
        img.style.maxHeight = '100%';
        img.style.width = 'auto';
        img.style.cursor = 'zoom-in';
        if (img.parentElement) {
            img.parentElement.style.alignItems = 'center';
            img.parentElement.style.justifyContent = 'center';
        }

        if (spinner) spinner.style.display = 'flex';
        img.style.display = 'none';

        img.onload = function() {
            if (spinner) spinner.style.display = 'none';
            img.style.display = 'block';
        };

        img.onerror = function() {
            this.onerror = null;
            if (spinner) spinner.style.display = 'none';
            alert("The image could not be loaded. The secure link may have expired.");
        };

        img.src = url;

        const modal = document.getElementById('simpleProofModal');
        if (modal) modal.style.display = 'flex';
    }

    // ==========================================
    // IMAGE ZOOM FEATURE (NATIVE SCROLL)
    // ==========================================
    function toggleImageZoom(img) {
        const container = img.parentElement;

        if (img.dataset.zoomed === 'true') {
            // Zoom Out (Fit to screen)
            img.dataset.zoomed = 'false';
            img.style.maxWidth = '100%';
            img.style.maxHeight = '100%';
            img.style.width = 'auto';
            img.style.cursor = 'zoom-in';

            // Re-center image
            container.style.alignItems = 'center';
            container.style.justifyContent = 'center';
        } else {
            // Zoom In (Expands heavily to trigger native scrollbars)
            img.dataset.zoomed = 'true';
            img.style.maxWidth = 'none';
            img.style.maxHeight = 'none';
            img.style.width = '250%'; // Massive zoom size
            img.style.cursor = 'zoom-out';

            // Align to top-left so the user can scroll naturally
            container.style.alignItems = 'flex-start';
            container.style.justifyContent = 'flex-start';
        }
    }

    function hideSimpleProofModal() {
        const modal = document.getElementById('simpleProofModal');
        if (modal) modal.style.display = 'none';
    }

    function closeSimpleProofModal(e) {
        if (e.target.id === 'simpleProofModal') hideSimpleProofModal();
    }

    function onSimpleImageLoad() {
        document.getElementById('simpleModalImage').style.display = 'block';
        document.getElementById('simpleModalSpinner').style.display = 'none';
    }

    function hideSimpleProofModal() {
        document.getElementById('simpleProofModal').style.display = 'none';
    }

    function closeSimpleProofModal(e) {
        if (e.target.id === 'simpleProofModal') hideSimpleProofModal();
    }

    // ==========================================
    // REJECT PAYMENT MODAL LOGIC
    // ==========================================
    function openRejectPaymentModal(txnId) {
        document.getElementById('rejectPaymentTxnId').value = txnId;
        document.getElementById('rejectPaymentReason').value = '';
        const modal = document.getElementById('rejectPaymentModal');
        if (modal) modal.style.display = 'flex';
    }

    function hideRejectPaymentModal() {
        const modal = document.getElementById('rejectPaymentModal');
        if (modal) modal.style.display = 'none';
    }

    function closeRejectPaymentModal(e) {
        if (e.target.id === 'rejectPaymentModal') hideRejectPaymentModal();
    }

    async function confirmRejectPayment() {
        const txnId = document.getElementById('rejectPaymentTxnId').value;
        const reason = document.getElementById('rejectPaymentReason').value.trim();
        const btn = document.getElementById('btnConfirmReject');

        if (!reason) {
            alert("Please provide a rejection reason so the member knows what to fix.");
            document.getElementById('rejectPaymentReason').focus();
            return;
        }

        // UX: Show loading state
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Rejecting...';

        try {
            const apiBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');

            // Sends the request to your backend with the rejection reason
            const response = await fetch(`${apiBase}/v1/treasurer/reject-payment/${txnId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
                body: JSON.stringify({
                    rejection_reason: reason
                })
            });

            const result = await response.json();

            if (response.ok) {
                hideRejectPaymentModal();
                alert("Payment has been rejected and the member has been notified.");

                // Refresh the table to show updated status
                if (typeof fetchTransactionsData === 'function') {
                    fetchTransactionsData(currentTransactionPage || 1);
                } else {
                    location.reload();
                }
            } else {
                alert(result.message || "Failed to reject payment.");
            }
        } catch (error) {
            console.error(error);
            alert("An error occurred while communicating with the server.");
        } finally {
            // Reset button
            btn.disabled = false;
            btn.innerHTML = 'Confirm Rejection';
        }
    }

    function openProof(url, applicantId) {
        if (!url || url === '#' || url === 'null' || url === 'N/A') {
            alert("No proof found.");
            return;
        }
        currentApplicantId = applicantId;
        const img = document.getElementById('modalImage');
        const spinner = document.getElementById('modalSpinner');

        if (spinner) spinner.style.display = 'flex';
        img.style.display = 'none';

        let finalUrl = url;
        if (!url.startsWith('http')) {
            const base = (window.API_BASE_URL || '').replace(/\/api$/, '');
            const cleanPath = url.startsWith('/') ? url.substring(1) : url;
            finalUrl = `${base}/storage/${cleanPath}`;
        }

        img.onload = function() {
            if (spinner) spinner.style.display = 'none';
            img.style.display = 'block';
        };

        img.onerror = function() {
            // STOP the loop immediately
            this.onerror = null;

            const s3Base = "https://s3.us-east-005.backblazeb2.com/pcci-storage-files-v1";
            if (this.src.startsWith(s3Base)) {
                this.src = "/images/placeholder-image.png";
                return;
            }

            const cleanPath = url.startsWith('/') ? url.substring(1) : url;
            const s3Path = cleanPath.includes('/') ? cleanPath : `applicants/documents/${cleanPath}`;
            this.src = `${s3Base}/${s3Path}`;
        };

        img.src = finalUrl;
        selectType(1);

        const modal = document.getElementById('proofModal');
        if (modal) modal.style.display = 'flex';
    }

    function onImageLoad() {
        document.getElementById('modalImage').style.display = 'block';
        document.getElementById('modalSpinner').style.display = 'none';
    }

    function hideProofModal() {
        document.getElementById('proofModal').style.display = 'none';
    }

    function closeProofModal(e) {
        if (e.target.id === 'proofModal') hideProofModal();
    }

    function selectType(id) {
        currentSelectedType = id;
        document.getElementById('toggleBtn1').className = (id == 1) ? 'type-toggle-btn active-1 flex-grow-1' : 'type-toggle-btn flex-grow-1';
        document.getElementById('toggleBtn2').className = (id == 2) ? 'type-toggle-btn active-2 flex-grow-1' : 'type-toggle-btn flex-grow-1';
    }

    // ==========================================
    // GLOBAL LOADING SCREEN HELPERS
    // ==========================================
    function showGlobalLoader(text = 'Processing...') {
        let loader = document.getElementById('global-action-loader');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'global-action-loader';
            loader.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center; color: white; backdrop-filter: blur(3px);">
                    <i class="fa fa-circle-notch fa-spin" style="font-size: 3rem; margin-bottom: 15px;"></i>
                    <h5 id="global-loader-text" class="fw-bold" style="font-family: 'Poppins', sans-serif;">${text}</h5>
                </div>
            `;
            document.body.appendChild(loader);
        } else {
            document.getElementById('global-loader-text').innerText = text;
            loader.style.display = 'flex';
        }
    }

    function hideGlobalLoader() {
        const loader = document.getElementById('global-action-loader');
        if (loader) loader.style.display = 'none';
    }

    // POST /v1/payments (Approve)
    async function confirmProcessing() {
        const data = globalMembershipTypes.find(m => String(m.id) === String(currentSelectedType));
        if (!data || !currentApplicantId) return;

        // 1. Show the loading screen
        showGlobalLoader('Approving Payment & Generating Record...');

        try {
            const endpointBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
            const response = await fetch(`${endpointBase}/v1/payments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? {
                        'Authorization': `Bearer ${token}`
                    } : {})
                },
                body: JSON.stringify({
                    applicant_id: parseInt(currentApplicantId),
                    membership_type_id: parseInt(currentSelectedType)
                })
            });

            if (response.ok || response.status === 200 || response.status === 201) {
                hideProofModal();

                // 2. Wait for all the data to refresh
                await fetchApplicants();
                await fetchMembers();
                await fetchTransactions();
                await renderRecentPayments();

                // 3. Hide loader and show success
                hideGlobalLoader();
                alert("Success: Payment Processed!");

            } else {
                hideGlobalLoader(); // Hide loader on error
                const result = await response.json().catch(() => ({}));
                if (response.status === 401 || response.status === 403) {
                    alert("Access denied. Your account may not have permission to process payments.");
                } else {
                    alert(`Error: ${result.message || 'Something went wrong. Please try again.'}`);
                }
            }
        } catch (err) {
            hideGlobalLoader(); // Hide loader on error
            alert("Network error: Could not reach the server.");
        }
    }

    // PATCH /v1/payments/{id}/reject
    async function rejectPaymentProcessing() {
        if (!currentApplicantId) return;

        const rejectionReason = prompt("Please enter the reason for rejection (e.g., 'Payment is Fraud!'):");
        if (!rejectionReason || rejectionReason.trim() === '') return;

        // 1. Show the loading screen
        showGlobalLoader('Rejecting Payment...');

        try {
            const endpointBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
            const response = await fetch(`${endpointBase}/v1/payments/${currentApplicantId}/reject`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? {
                        'Authorization': `Bearer ${token}`
                    } : {})
                },
                body: JSON.stringify({
                    rejection_reason: rejectionReason.trim()
                })
            });

            if (response.ok) {
                hideProofModal();

                // 2. Wait for all the data to refresh
                await fetchApplicants();
                await fetchMembers();
                await fetchTransactions();
                await renderRecentPayments();

                // 3. Hide loader and show success
                hideGlobalLoader();
                alert("Success: Payment Rejected.");

            } else {
                hideGlobalLoader(); // Hide loader on error
                const result = await response.json().catch(() => ({}));
                alert(`Error: ${result.message || 'Failed to reject payment.'}`);
            }
        } catch (err) {
            hideGlobalLoader(); // Hide loader on error
            alert("Network error: Could not reach the server.");
        }
    }

    // PATCH /v1/payments/{id}/reject
    async function rejectPaymentProcessing() {
        if (!currentApplicantId) return;

        const rejectionReason = prompt("Please enter the reason for rejection (e.g., 'Payment is Fraud!'):");
        if (!rejectionReason || rejectionReason.trim() === '') return;

        try {
            const endpointBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
            const response = await fetch(`${endpointBase}/v1/payments/${currentApplicantId}/reject`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? {
                        'Authorization': `Bearer ${token}`
                    } : {})
                },
                body: JSON.stringify({
                    rejection_reason: rejectionReason.trim()
                })
            });

            if (response.ok) {
                hideProofModal();
                alert("Success: Payment Rejected.");

                // FIX 2: Await fresh data fetches so the rejected applicant leaves the queue
                await fetchApplicants();
                await fetchMembers();
                await fetchTransactions();
                await renderRecentPayments();

            } else {
                const result = await response.json().catch(() => ({}));
                alert(`Error: ${result.message || 'Failed to reject payment.'}`);
            }
        } catch (err) {
            alert("Network error: Could not reach the server.");
        }
    }

    // PATCH /v1/payments/{id}/reject
    async function rejectPaymentProcessing() {
        if (!currentApplicantId) return;

        const rejectionReason = prompt("Please enter the reason for rejection (e.g., 'Payment is Fraud!'):");
        if (!rejectionReason || rejectionReason.trim() === '') return;

        try {
            const endpointBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
            const response = await fetch(`${endpointBase}/v1/payments/${currentApplicantId}/reject`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? {
                        'Authorization': `Bearer ${token}`
                    } : {})
                },
                body: JSON.stringify({
                    rejection_reason: rejectionReason.trim()
                })
            });

            if (response.ok) {
                hideProofModal();
                const amtLbl = document.getElementById(`amount-label-${currentApplicantId}`);
                const typeLbl = document.getElementById(`type-label-${currentApplicantId}`);
                const bge = document.getElementById(`status-badge-${currentApplicantId}`);
                const actionBox = document.getElementById(`action-container-${currentApplicantId}`);

                if (amtLbl) {
                    amtLbl.innerText = `---`;
                    amtLbl.className = "fw-bold text-muted";
                }
                if (typeLbl) {
                    typeLbl.innerText = "REJECTED";
                    typeLbl.className = "text-danger fw-bold";
                }
                if (bge) {
                    bge.innerHTML = `<i class="fa fa-times-circle me-1"></i> REJECTED`;
                    bge.className = "badge bg-danger text-white px-2 py-1 rounded-pill fw-bold shadow-sm";
                }
                if (actionBox) {
                    actionBox.innerHTML = `<button class="action-btn btn-gray" disabled style="width: 130px;"><i class="fa fa-times"></i> Rejected</button>`;
                }

                fetchMembers();
                fetchTransactions();
                renderRecentPayments();
                alert("Success: Payment Rejected.");
            } else {
                const result = await response.json().catch(() => ({}));
                alert(`Error: ${result.message || 'Failed to reject payment.'}`);
            }
        } catch (err) {
            alert("Network error: Could not reach the server.");
        }
    }

    function viewMemberDetails(memberId) {
        const member = allMembersData.find(m => m.id === memberId);
        if (!member) return;
        const profile = member.applicant?.basic_profile || {};

        document.getElementById('member-detail-content').innerHTML = `
            <div class="row g-3 text-start">
                <div class="col-12 border-bottom pb-2 mb-2"><label class="text-muted small fw-bold">BUSINESS NAME</label><h5 class="fw-bold text-dark">${profile.registered_business_name || 'N/A'}</h5></div>
                <div class="col-md-6"><label class="text-muted small fw-bold">TRADE NAME</label><p class="fw-bold text-dark">${profile.trade_name || 'N/A'}</p></div>
                <div class="col-md-6"><label class="text-muted small fw-bold">EMAIL</label><p class="fw-bold text-dark">${profile.email || 'N/A'}</p></div>
            </div>
        `;
        document.getElementById('memberDetailsModal').style.display = 'flex';
    }

    function hideMemberModal() {
        document.getElementById('memberDetailsModal').style.display = 'none';
    }

    function closeMemberModal(e) {
        if (e.target.id === 'memberDetailsModal') hideMemberModal();
    }

    // --- ADD PAYMENT MODAL ---
    function openAddPaymentModal(mode = 'add') {
        if (mode !== 'edit') {
            editingTransactionId = null;
            document.getElementById('transactionMemberInput').value = '';
            document.getElementById('transactionOrNumber').value = '';
            document.getElementById('transactionPaymentDate').value = '';
            document.getElementById('transactionMembershipType').selectedIndex = 0;
            document.getElementById('transactionPaymentType').selectedIndex = 0;
            document.getElementById('transactionProofInput').value = '';
            document.getElementById('transactionReceiverSelect').selectedIndex = 0;
        }
        document.getElementById('addPaymentModalTitle').innerText = mode === 'edit' ? 'Edit Payment' : 'Add Payment';
        document.getElementById('transactionModalConfirmBtn').innerText = mode === 'edit' ? 'Save Changes' : 'Confirm';
        document.getElementById('addPaymentModal').style.display = 'flex';
    }

    function hideAddPaymentModal() {
        document.getElementById('addPaymentModal').style.display = 'none';
        editingTransactionId = null;
        document.getElementById('addPaymentModalTitle').innerText = 'Add Payment';
        document.getElementById('transactionModalConfirmBtn').innerText = 'Confirm';
    }

    function closeAddPaymentModal(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        hideAddPaymentModal();
    }

    function closeAddPaymentOverlay(e) {
        if (e.target.id === 'addPaymentModal') hideAddPaymentModal();
    }

    function clearPaymentForm() {
        document.getElementById('transactionMemberInput').value = '';
        document.getElementById('transactionOrNumber').value = '';
        document.getElementById('transactionPaymentDate').value = '';
        document.getElementById('transactionMembershipType').selectedIndex = 0;
        document.getElementById('transactionPaymentType').selectedIndex = 0;
        document.getElementById('transactionProofInput').value = '';
        document.getElementById('transactionReceiverSelect').selectedIndex = 0;
    }
    async function confirmPaymentAdd() {
        if (!editingTransactionId) {
            alert('Payment details confirmed!');
            hideAddPaymentModal();
            return;
        }
        const record = getTransactionRecordByKey(editingTransactionId);
        if (!record) {
            hideAddPaymentModal();
            editingTransactionId = null;
            return;
        }

        const updatedOrNumber = document.getElementById('transactionOrNumber').value || record.or_number || '---';
        const updatedDate = document.getElementById('transactionPaymentDate').value || getRecordDate(record);
        const updatedPaymentType = document.getElementById('transactionPaymentType').value || 'GCash';
        const updatedMembership = document.getElementById('transactionMembershipType').value || 'Annual';
        const apiId = getTransactionApiId(editingTransactionId);

        if (!apiId) {
            alert('This transaction cannot be updated because it has no backend id.');
            return;
        }

        try {
            const response = await fetch(`/treasurer/transactions/${apiId}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    or_number: updatedOrNumber,
                    payment_type: updatedPaymentType,
                    receiver: document.getElementById('transactionReceiverSelect').value || 'Jesus Versula',
                    payment_date: updatedDate,
                    membership_type: updatedMembership,
                    membership_type_id: updatedMembership === 'Annual' ? 1 : 2,
                    status: record.status || 'paid'
                })
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Failed to update transaction.');
            }

            const responseData = await response.json().catch(() => ({}));
            if (responseData && responseData.data) {
                record.or_number = responseData.data.or_number || updatedOrNumber;
                record.payment_type = responseData.data.payment_type || updatedPaymentType;
            }

            hideAddPaymentModal();
            editingTransactionId = null;
            document.getElementById('addPaymentModalTitle').innerText = 'Add Payment';
            document.getElementById('transactionModalConfirmBtn').innerText = 'Confirm';
            await fetchTransactions();
            alert('Transaction updated.');
        } catch (error) {
            console.error(error);
            alert(error.message || 'Failed to update transaction.');
        }
    }

    // --- UTILITY FUNCTIONS ---
    function sanitizeSearchAutofill() {
        const isEmailLike = (value) => /\S+@\S+\.\S+/.test(String(value || '').trim());
        const searchInputs = [
            document.getElementById('memberSearch'), document.getElementById('applicantSearch'),
            document.getElementById('transactionSearch'), document.querySelector('.topbar-search')
        ].filter(Boolean);

        searchInputs.forEach((input, index) => {
            input.setAttribute('autocomplete', 'off');
            input.setAttribute('autocapitalize', 'off');
            input.setAttribute('autocorrect', 'off');
            input.setAttribute('spellcheck', 'false');
            input.setAttribute('name', `search_query_${index + 1}`);
            if (isEmailLike(input.value)) input.value = '';
        });
    }

    // NEW: Added missing checkAuth function to prevent ReferenceErrors
    function checkAuth(res) {
        if (res.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/login';
            return false;
        }
        return true;
    }

    // NEW: Added missing readApiResponse function to safely parse API responses
    async function readApiResponse(response) {
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

    document.addEventListener('DOMContentLoaded', async () => {
        if (!token) {
            window.location.href = '/login';
            return;
        }

        // Step 1: Fetch all data. Use try/catch so one failure doesn't break the whole dashboard
        try {
            await Promise.all([
                fetchWelcomeName(),
                fetchMembershipTypes(),
                fetchApplicants(),
                fetchMembers(),
                fetchTransactions()
            ]);
        } catch (e) {
            console.error("Critical Fetch Error:", e);
        }

        // Step 2: Populate UI (Only run these if data is ready)
        updateNotificationsPanel();
        populateMainDashboard();
        renderRecentPayments();

        // Step 3: Initialize Charts (Now data is guaranteed to exist)
        renderRealDashboardCharts();

        const savedTab = localStorage.getItem('activeTab') || 'dashboard';
        switchTab(savedTab, false);
    });

    // --- MEMBER FILTER/SORT ---
    function applyMemberFilters() {
        const term = document.getElementById('memberSearch')?.value.toLowerCase() || '';
        const sortVal = document.getElementById('memberSort')?.value || 'newest';

        filteredMembersData = allMembersData.filter(m => {
            const status = String(m.status || m.applicant?.status || '').toLowerCase();
            const isPaidOrApproved = ['paid', 'approved', 'active'].includes(status);
            const name = (m.applicant?.basic_profile?.registered_business_name || '').toLowerCase();
            return isPaidOrApproved && name.includes(term);
        });

        filteredMembersData.sort((a, b) => {
            const nameA = (a.applicant?.basic_profile?.registered_business_name || '').toLowerCase();
            const nameB = (b.applicant?.basic_profile?.registered_business_name || '').toLowerCase();
            const dateA = new Date(a.created_at || 0);
            const dateB = new Date(b.created_at || 0);

            if (sortVal === 'name_asc') return nameA.localeCompare(nameB);
            if (sortVal === 'name_desc') return nameB.localeCompare(nameA);
            if (sortVal === 'oldest') return dateA - dateB;
            return dateB - dateA;
        });

        const paidCount = filteredMembersData.length;
        const unpaidCount = allMembersData.filter(m => {
            const s = String(m.status || m.applicant?.status || '').toLowerCase();
            return ['pending', 'unpaid', 'failed'].includes(s);
        }).length;

        const paidCountEl = document.getElementById('paid-count');
        const unpaidCountEl = document.getElementById('unpaid-count');
        if (paidCountEl) paidCountEl.innerText = paidCount;
        if (unpaidCountEl) unpaidCountEl.innerText = unpaidCount;

        currentMemberPage = 1;
        displayMembersPage();
    }

    // --- STRICT TREASURER APPLICANT FILTER/SORT ---
    function applyApplicantFilters() {
        const term = document.getElementById('applicantSearch')?.value.toLowerCase() || '';
        const sortVal = document.getElementById('applicantSort')?.value || 'newest';
        const statusFilterEl = document.getElementById('applicantStatusFilter');
        const statusVal = statusFilterEl ? statusFilterEl.value : 'approved'; // Default to approved for Treasurer

        const memberEmails = new Set(
            (Array.isArray(allMembersData) ? allMembersData : [])
            .map(m => String(m?.applicant?.basic_profile?.email || '').trim().toLowerCase())
            .filter(Boolean)
        );

        const memberCompanyNames = new Set(
            (Array.isArray(allMembersData) ? allMembersData : [])
            .map(m => String(m?.applicant?.basic_profile?.registered_business_name || '').trim().toLowerCase())
            .filter(Boolean)
        );

        filteredApplicantsData = allApplicantsData.filter(a => {
            const name = (a.basic_profile?.registered_business_name || '').toLowerCase();
            const email = String(a.basic_profile?.email || '').trim().toLowerCase();
            const companyName = String(a.basic_profile?.registered_business_name || '').trim().toLowerCase();
            const appStatus = String(a.status || '').toLowerCase();

            // STRICT STATUS MATCH
            if (appStatus !== statusVal) return false;

            const isExistingMember = (email && memberEmails.has(email)) || (companyName && memberCompanyNames.has(companyName));
            if (isExistingMember) return false;

            return name.includes(term);
        });

        filteredApplicantsData.sort((a, b) => {
            const nameA = (a.basic_profile?.registered_business_name || '').toLowerCase();
            const nameB = (b.basic_profile?.registered_business_name || '').toLowerCase();
            const dateA = new Date(a.created_at || 0);
            const dateB = new Date(b.created_at || 0);

            if (sortVal === 'name_asc') return nameA.localeCompare(nameB);
            if (sortVal === 'name_desc') return nameB.localeCompare(nameA);
            if (sortVal === 'oldest') return dateA - dateB;
            return dateB - dateA;
        });

        const pendingCount = allApplicantsData.filter(a => String(a.status).toLowerCase() === 'approved').length;
        const pendingCountEl = document.getElementById('report-pending-count');
        const pendingBadgeEl = document.getElementById('report-pending-count-badge');
        if (pendingCountEl) pendingCountEl.innerText = pendingCount;
        if (pendingBadgeEl) pendingBadgeEl.innerText = `${pendingCount} Pending`;

        currentApplicantPage = 1;
        displayApplicantsPage();
    }

    // --- ADD MEMBER MODAL API & READ-ONLY LOGIC ---
    async function fetchTreasurerApprovedApplicantsForModal() {
        const companySelect = document.getElementById('addMemberCompanySelect');
        const token = localStorage.getItem('token');
        if (!companySelect) return;

        companySelect.innerHTML = '<option value="">Loading eligible companies . . .</option>';

        try {
            // FIX: Only fetch the fresh members list and PAID applicants (removed 'approved')
            const [membersRes, paidRes] = await Promise.all([
                fetch(`${window.API_BASE_URL}/v1/members`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=paid`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                })
            ]);

            if (!membersRes.ok || !paidRes.ok) {
                throw new Error('Failed to load necessary API data');
            }

            const membersData = await membersRes.json();
            const paidData = await paidRes.json();

            const freshMembers = Array.isArray(membersData.data) ? membersData.data : [];
            const paidApplicants = Array.isArray(paidData.data) ? paidData.data : [];

            // Map exact emails and company names from the fresh members list for strict validation
            const existingMemberEmails = new Set(
                freshMembers
                .map((member) => String(member?.applicant?.basic_profile?.email || '').trim().toLowerCase())
                .filter(Boolean)
            );

            const existingMemberCompanyNames = new Set(
                freshMembers
                .map((member) => String(member?.applicant?.basic_profile?.registered_business_name || '').trim().toLowerCase())
                .filter(Boolean)
            );

            // Filter out anyone who is already an existing member
            approvedApplicantsForModal = paidApplicants.filter((applicant) => {
                const email = String(applicant?.basic_profile?.email || '').trim().toLowerCase();
                const companyName = String(applicant?.basic_profile?.registered_business_name || '').trim().toLowerCase();

                if (email && existingMemberEmails.has(email)) return false;
                if (companyName && existingMemberCompanyNames.has(companyName)) return false;

                return true;
            });

            companySelect.innerHTML = '<option value="">Select eligible company . . .</option>';

            approvedApplicantsForModal.forEach((applicant) => {
                const companyName = applicant?.basic_profile?.registered_business_name || `Applicant #${applicant.id}`;
                const statusLabel = String(applicant.status || '').toUpperCase();

                companySelect.insertAdjacentHTML(
                    'beforeend',
                    `<option value="${applicant.id}">${companyName} (${statusLabel})</option>`
                );
            });

            if (approvedApplicantsForModal.length === 0) {
                companySelect.innerHTML = '<option value="">No eligible companies available</option>';
            }
        } catch (error) {
            console.error('Error loading eligible applicants for modal:', error);
            approvedApplicantsForModal = [];
            companySelect.innerHTML = '<option value="">No eligible companies available</option>';
        }

        populateAddMemberReadOnlyFields(null);
    }

    function populateAddMemberReadOnlyFields(applicant) {
        if (!applicant) {
            document.getElementById('memberTradeName').value = '';
            document.getElementById('memberEmail').value = '';
            document.getElementById('memberContactNo').value = '';
            document.getElementById('memberAddress').value = '';
            document.getElementById('memberRepName').value = '';
            document.getElementById('memberRepDesignation').value = '';
            document.getElementById('memberOrgType').value = '';
            document.getElementById('memberSecDti').value = '';
            document.getElementById('memberExpDate').value = '';
            document.getElementById('memberOrNo').value = '';
            return;
        }

        const safe = (val) => val || '';
        const profile = applicant.basic_profile || {};
        const loc = profile.business_location || {};
        const rep = applicant.official_representative || {};
        const org = applicant.organization_membership || {};

        const repName = [safe(rep.first_name), safe(rep.surname)].filter(Boolean).join(' ');
        const addressParts = [safe(loc.business_address), safe(loc.city_municipality), safe(loc.province)].filter(Boolean);
        const fullAddress = addressParts.length > 0 ? addressParts.join(', ') : '';

        let expDateStr = '';
        if (applicant.date_approved) {
            const approvedDate = new Date(applicant.date_approved);
            if (!isNaN(approvedDate)) {
                approvedDate.setFullYear(approvedDate.getFullYear() + 1);
                expDateStr = approvedDate.toISOString().split('T')[0];
            }
        }

        document.getElementById('memberTradeName').value = safe(profile.trade_name);
        document.getElementById('memberEmail').value = safe(profile.email);
        document.getElementById('memberContactNo').value = safe(profile.telephone_no);
        document.getElementById('memberAddress').value = fullAddress;
        document.getElementById('memberRepName').value = repName;
        document.getElementById('memberRepDesignation').value = safe(rep.designation);
        document.getElementById('memberOrgType').value = safe(org.type_of_company);
        document.getElementById('memberSecDti').value = safe(org.registration_number);
        document.getElementById('memberExpDate').value = expDateStr;
        document.getElementById('memberOrNo').value = `OR-${10000 + applicant.id}`;
    }

    // --- API FETCHES ---
    async function fetchApplicants() {
        try {
            const [resApproved, resRejected] = await Promise.all([
                fetch(`${window.API_BASE_URL}/v1/applicants?status=approved`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=rejected`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                })
            ]);

            let combinedData = [];
            if (resApproved.ok) {
                const data1 = await resApproved.json();
                if (data1.data) combinedData = combinedData.concat(data1.data);
            }
            if (resRejected.ok) {
                const data2 = await resRejected.json();
                if (data2.data) combinedData = combinedData.concat(data2.data);
            }

            allApplicantsData = combinedData;

            try {
                const membersRes = await fetch('/api/v1/members', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });
                if (membersRes.ok) {
                    const membersJson = await membersRes.json();
                    if (Array.isArray(membersJson?.data)) allMembersData = membersJson.data;
                }
            } catch (_) {}

            applyApplicantFilters();
            updateNotificationsPanel();
            updateReportsDashboard();
        } catch (err) {
            console.error('Error fetching applicants:', err);
        }
    }

    function displayApplicantsPage() {
        const totalPages = Math.ceil(filteredApplicantsData.length / applicantsPerPage) || 1;
        if (currentApplicantPage > totalPages) currentApplicantPage = totalPages;
        if (currentApplicantPage < 1) currentApplicantPage = 1;

        const pageData = filteredApplicantsData.slice((currentApplicantPage - 1) * applicantsPerPage, currentApplicantPage * applicantsPerPage);
        const tbody = document.getElementById('applicants-table-body');
        if (!tbody) return;

        tbody.innerHTML = '';
        if (pageData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted fw-bold">No applicants found matching the selected status.</td></tr>`;
        }

        pageData.forEach(app => {
            const profile = app.basic_profile || {};
            const appStatus = String(app.status).toLowerCase();

            let typeLabelHTML = '';
            let statusBadge = '';
            let amountText = '---';
            let actionButton = '';

            if (appStatus === 'approved') {
                typeLabelHTML = `<span id="type-label-${app.id}" class="text-warning fw-bold" style="color: #d97706 !important;">PENDING PAYMENT</span>`;
                statusBadge = `<span id="status-badge-${app.id}" class="badge bg-warning text-dark px-2 py-1 rounded-pill fw-bold shadow-sm" style="font-size:10px;"><i class="fa fa-clock me-1"></i> APPROVED</span>`;
                actionButton = `<button onclick="openProof('${app.proof_of_payment_url}', ${app.id})" class="action-btn btn-green" style="width: 130px;"><i class="fa fa-image me-1"></i> Process Payment</button>`;
            } else if (appStatus === 'rejected') {
                typeLabelHTML = `<span id="type-label-${app.id}" class="text-danger fw-bold">REJECTED</span>`;
                statusBadge = `<span id="status-badge-${app.id}" class="badge bg-danger text-white px-2 py-1 rounded-pill fw-bold shadow-sm" style="font-size:10px;"><i class="fa fa-times-circle me-1"></i> REJECTED</span>`;
                actionButton = `<button class="action-btn btn-gray" disabled style="width: 130px;"><i class="fa fa-times"></i> Rejected</button>`;
            }

            tbody.insertAdjacentHTML('beforeend', `
                <tr id="applicant-row-${app.id}">
                    <td class="fw-bold text-dark">${profile.registered_business_name || 'N/A'}</td>
                    <td class="text-dark">${profile.trade_name || 'N/A'}</td>
                    <td class="text-dark">${profile.email || 'N/A'}</td>
                    <td class="text-dark">${app.date_submitted || 'N/A'}</td>
                    <td>${typeLabelHTML}</td>
                    <td><span id="amount-label-${app.id}" class="fw-bold text-dark">${amountText}</span></td>
                    <td>${statusBadge}</td>
                    <td id="action-container-${app.id}">${actionButton}</td>
                </tr>
            `);
        });
        const paginationText = document.getElementById('applicant-pagination-text');
        if (paginationText) paginationText.innerText = `Page ${currentApplicantPage} of ${totalPages}`;
    }

    function prevApplicantPage() {
        if (currentApplicantPage > 1) {
            currentApplicantPage--;
            displayApplicantsPage();
        }
    }

    function nextApplicantPage() {
        if (currentApplicantPage < Math.ceil(filteredApplicantsData.length / applicantsPerPage)) {
            currentApplicantPage++;
            displayApplicantsPage();
        }
    }

    async function fetchMembers() {
        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/members`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!checkAuth(response)) return;
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            allMembersData = (data && Array.isArray(data.data)) ? data.data : [];

            const totalMembersBadge = document.getElementById('total-members-badge');
            if (totalMembersBadge) totalMembersBadge.innerText = `${allMembersData.length} Active`;
            const reportActive = document.getElementById('report-active-members');
            if (reportActive) reportActive.innerText = allMembersData.length;

            applyMemberFilters();
            applyApplicantFilters();
            updateNotificationsPanel();
            updateReportsDashboard();

        } catch (err) {
            console.error("Failed to fetch members:", err);
            const tbody = document.getElementById('members-table-body');
            if (tbody) tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 text-danger fw-bold">Network error. Failed to load data.</td></tr>`;
        }
    }

    function getMembersTotalPages() {
        const totalRecords = Array.isArray(filteredMembersData) ? filteredMembersData.length : 0;
        return Math.max(1, Math.ceil(totalRecords / membersPerPage));
    }

    function displayMembersPage() {
        const totalRecords = Array.isArray(filteredMembersData) ? filteredMembersData.length : 0;
        const totalPages = getMembersTotalPages();
        if (currentMemberPage > totalPages) currentMemberPage = totalPages;
        if (currentMemberPage < 1) currentMemberPage = 1;

        let startIndex = (currentMemberPage - 1) * membersPerPage;
        let pageData = filteredMembersData.slice(startIndex, startIndex + membersPerPage);

        const tbody = document.getElementById('members-table-body');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (totalRecords === 0) {
            // Updated colspan to 5 since we removed the 2 columns
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted fw-bold">No members found matching your search.</td></tr>`;
            const paginationText = document.getElementById('member-pagination-text');
            if (paginationText) paginationText.innerText = `Page 0 of 0`;
            return;
        }

        pageData.forEach(member => {
            let name = member.company_name || member.applicant?.registered_business_name || '';
            if (!name && member.applicant?.basic_profile) {
                try {
                    let bp = typeof member.applicant.basic_profile === 'string' ?
                        JSON.parse(member.applicant.basic_profile) :
                        member.applicant.basic_profile;
                    name = bp.registered_business_name;
                } catch (e) {}
            }
            name = name || 'Unnamed Business';

            const fallbackOrNumber = `OR-${10000 + member.id}`;
            const regDate = member.created_at ? member.created_at.split('T')[0] : 'N/A';

            let expDate = member.membership_end_date ? member.membership_end_date.split('T')[0] : 'N/A';
            if (expDate === 'N/A' && member.created_at) {
                const dateObj = new Date(member.created_at);
                dateObj.setFullYear(dateObj.getFullYear() + 1);
                expDate = dateObj.toISOString().split('T')[0];
            }

            const memId = String(member.id || '');
            const appId = String(member.applicant_id || member.applicant?.id || '');
            const memberTxns = allTransactionsData.filter(t =>
                (String(t.member_id) === memId) || (String(t.applicant_id) === appId)
            );
            const latestTxn = memberTxns.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];

            // Render without the Proof and Details buttons
            tbody.insertAdjacentHTML('beforeend', `
            <tr class="align-middle">
                <td class="fw-bold text-dark ps-4">${name}</td>
                <td class="text-dark font-monospace fw-bold">${latestTxn && latestTxn.or_number ? latestTxn.or_number : fallbackOrNumber}</td>
                <td class="text-dark">${regDate}</td>
                <td class="text-dark">${expDate}</td>
                <td><span class="badge bg-success rounded-pill px-3 py-1">Active</span></td>
            </tr>
        `);
        });

        const paginationText = document.getElementById('member-pagination-text');
        if (paginationText) paginationText.innerText = `Page ${currentMemberPage} of ${totalPages}`;
    }

    function prevMemberPage() {
        if (currentMemberPage > 1) {
            currentMemberPage--;
            displayMembersPage();
        }
    }

    function nextMemberPage() {
        if (currentMemberPage < getMembersTotalPages()) {
            currentMemberPage++;
            displayMembersPage();
        }
    }

    function verifyRecentPayment(proofUrl, applicantId) {
        openProof(proofUrl, applicantId);
    }

    function printRecentReceipt(applicantId) {
        window.print();
    }

    function rejectRecentPayment(applicantId) {
        const row = document.getElementById(`recent-payment-row-${applicantId}`);
        if (row) row.remove();
        const tbody = document.getElementById('recent-payments-table-body');
        if (tbody && tbody.children.length === 0) tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No recent payments available.</td></tr>`;
    }

    async function fetchTransactions() {
        try {
            // Use 'all=true' so we get the full dataset for charts/revenue, not just one page
            const transRes = await fetch(`${window.API_BASE_URL}/v1/transactions?all=true`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (transRes.ok) {
                const transData = await transRes.json();
                // Backend now returns {data: [...]} for both paginated and non-paginated
                allTransactionsData = transData.data || [];
                filteredTransactionsData = allTransactionsData.slice();

                populateMainDashboard();
                updateTransactionSummary(allTransactionsData);
                displayTransactionsPage(); // Handles its own pagination via filteredTransactionsData
                renderRecentPayments();
                renderRealDashboardCharts();
            }
        } catch (err) {
            console.error('Error fetching transactions:', err);
        }
    }

    // Mobile Sidebar Toggle
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) sidebar.classList.toggle('active');
    }

    document.querySelectorAll('.sidebar-menu li').forEach(item => {
        item.addEventListener('click', () => {
            if (window.innerWidth <= 768) document.querySelector('.sidebar').classList.remove('active');
        });
    });

    // ==========================================
    // SEAMLESS TAB SWITCHING (NO PAGE RELOADS)
    // ==========================================
    let currentActiveTab = 'dashboard';

    function switchTab(tabName) {
        currentActiveTab = tabName;
        localStorage.setItem('activeTab', tabName);

        // 1. Smoothly update the URL without refreshing the page!
        window.history.pushState(null, null, `#${tabName}`);

        // 2. Hide all sections and remove active states
        document.querySelectorAll('.content-section').forEach(s => s.style.display = 'none');
        document.querySelectorAll('.sidebar-menu li').forEach(li => li.classList.remove('active'));

        // 3. Show target section and highlight sidebar
        const targetSection = document.getElementById('section-' + tabName);
        const targetNav = document.getElementById('nav-' + tabName);
        if (targetSection) targetSection.style.display = 'block';
        if (targetNav) targetNav.classList.add('active');

        // Handle Settings sub-menus
        if (tabName !== 'settings') {
            const mainSet = document.getElementById('settings-main');
            if (mainSet) mainSet.style.display = 'block';
            const accSet = document.getElementById('settings-account');
            if (accSet) accSet.style.display = 'none';
            const secSet = document.getElementById('settings-security');
            if (secSet) secSet.style.display = 'none';
            const prefSet = document.getElementById('settings-preferences');
            if (prefSet) prefSet.style.display = 'none';
        }

        // 4. Trigger the smart cache loader
        refreshTabData(tabName);
    }

    // ==========================================
    // TREASURER SMART CACHE (NO DATA RESET)
    // ==========================================
    async function refreshTabData(tabName) {
        if (!token) return;

        const hasMemberRows = document.getElementById('members-table-body')?.children.length > 0;
        const hasApplicantRows = document.getElementById('applicant-table-body')?.children.length > 0;
        const hasTransactionRows = document.getElementById('transaction-table-body')?.children.length > 0;

        const needsApplicants = allApplicantsData.length === 0 || !hasApplicantRows;
        const needsMembers = allMembersData.length === 0 || !hasMemberRows;
        const needsTransactions = allTransactionsData.length === 0 || !hasTransactionRows;

        if (tabName === 'dashboard') {
            if (needsApplicants) await fetchApplicants();
            if (needsMembers) await fetchMembers();
            if (needsTransactions) await fetchTransactions();

            if (!window.dashPieChartInstance) {
                initCharts();
            }

        } else if (tabName === 'members') {
            if (needsMembers) await fetchMembers();
            fetchTreasurerApprovedApplicantsForModal();

        } else if (tabName === 'applicants') {
            if (needsApplicants) await fetchApplicants();

        } else if (tabName === 'transactions') {
            if (needsTransactions) await fetchTransactions();

        } else if (tabName === 'reports') {
            if (needsApplicants) await fetchApplicants();
            if (needsMembers) await fetchMembers();
            if (needsTransactions) await fetchTransactions();
        }

        // Note: We DO NOT call applyFilters() here anymore. 
        // The HTML table is already rendered, so switching tabs will just reveal it exactly as you left it!
    }

    function toggleNotificationPanel(e) {
        e.stopPropagation();
        const p = document.getElementById('notificationPanel');
        p.style.display = p.style.display === 'flex' ? 'none' : 'flex';
    }

    function clearNotifications(e) {
        e.stopPropagation();
        document.getElementById('notificationPanel').style.display = 'none';
    }

    function logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('activeTab');
        window.location.href = '/login';
    }

    // ==========================================
    // 4. TREASURER APPROVAL FUNCTION
    // ==========================================
    async function approveTreasurerPayment(txnId, event) {
        if (event) event.stopPropagation();

        if (!confirm("Are you sure you want to approve this payment and generate the Official Receipt?")) {
            return;
        }

        try {
            // Setup the base URL safely
            const apiBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');

            // Send the request to the CORRECT new route
            const response = await fetch(`${apiBase}/v1/treasurer/approve-payment/${txnId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });

            const result = await response.json();

            if (response.ok) {
                alert(result.message || "Payment approved successfully.");

                // Refresh the table dynamically to show the new OR Number
                if (typeof fetchTransactionsData === 'function') {
                    fetchTransactionsData(currentTransactionPage || 1);
                } else {
                    location.reload();
                }
            } else {
                alert(result.message || "Failed to approve payment.");
            }
        } catch (error) {
            console.error("Approval Error:", error);
            alert("An error occurred while communicating with the server.");
        }
    }

    // ========================================
    // TREASURER ACCOUNT & DATA LOGIC (SMART CACHE)
    // ========================================

    let trsCropper = null;
    let trsAccountImageFile = null;
    let trsSessionAvatarUrl = null;

    // Run immediately if the page is already loaded!
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => fetchTreasurerProfile(false));
    } else {
        fetchTreasurerProfile(false);
    }

    async function fetchTreasurerProfile(forceRefresh = false) {
        // 1. INSTANT LOAD: Check the browser cache first
        const cachedProfile = localStorage.getItem('pcci_treasurer_profile');

        if (!forceRefresh && cachedProfile) {
            populateSettingsAccountForm(JSON.parse(cachedProfile));
            return; // EXIT EARLY: Zero database load time!
        }

        // 2. HARD LOAD: Hit the database ONLY if forced (after saving) or cache is empty
        try {
            const apiBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
            const response = await fetch(`${apiBase}/v1/user`, {
                method: 'GET',
                cache: 'no-store',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const responsePayload = await response.json();

                // 3. SAVE CACHE: Store the fresh database data in the browser
                localStorage.setItem('pcci_treasurer_profile', JSON.stringify(responsePayload));

                populateSettingsAccountForm(responsePayload);
            }
        } catch (error) {
            console.error("Failed to load user profile:", error);
        }
    }

    function populateSettingsAccountForm(responsePayload) {
        if (!responsePayload) return;
        const user = responsePayload.data || responsePayload;

        const firstEl = document.getElementById('settingsFirstName');
        const lastEl = document.getElementById('settingsLastName');
        const emailEl = document.getElementById('settingsEmailInput');
        const phoneEl = document.getElementById('settingsContactInput');

        if (firstEl) firstEl.value = user.first_name || '';
        if (lastEl) lastEl.value = user.last_name || '';
        if (emailEl) emailEl.value = user.email || '';

        // Strictly mapped to the User table's contact_number column
        if (phoneEl) {
            phoneEl.value = user.contact_number || '';
        }

        const defaultAvatar = "{{ asset('images/PCCI-Logo.svg') }}";
        let finalUrl = defaultAvatar;

        if (window.trsSessionAvatarUrl) {
            finalUrl = window.trsSessionAvatarUrl;
        } else {
            const avatarUrl = user.photo_url || user.profile_photo_url || user.profile_photo_path || null;
            if (avatarUrl) {
                let storageBase = (window.API_BASE_URL || '/api').replace(/\/$/, '').replace('/api', '');
                finalUrl = avatarUrl.startsWith('http') ? avatarUrl : `${storageBase}/storage/${avatarUrl}`;
            }
        }

        document.querySelectorAll('img[alt="Profile"], #settingsAccountAvatar').forEach(img => {
            img.src = finalUrl;
            img.onerror = function() {
                this.onerror = null;
                this.src = defaultAvatar;
            };
        });
    }

    async function saveAccountSettings() {
        const firstName = document.getElementById('settingsFirstName')?.value?.trim() || '';
        const lastName = document.getElementById('settingsLastName')?.value?.trim() || '';
        const email = document.getElementById('settingsEmailInput')?.value?.trim() || '';
        const phone = document.getElementById('settingsContactInput')?.value?.trim() || '';
        const btn = document.getElementById('saveAccountBtn');

        const formData = new FormData();
        formData.append('_method', 'PUT');

        if (firstName) formData.append('first_name', firstName);
        if (lastName) formData.append('last_name', lastName);
        if (email) formData.append('email', email);

        // Exact match to what works on the Member dashboard
        if (phone) formData.append('contact_number', phone);

        if (window.trsAccountImageFile) {
            formData.append('image', window.trsAccountImageFile);
        }

        try {
            if (btn) {
                btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Saving...';
                btn.disabled = true;
            }

            const apiBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
            const response = await fetch(`${apiBase}/v1/user/change-info`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
                body: formData
            });

            if (response.ok) {
                alert('Account updated successfully!');
                window.trsAccountImageFile = null;

                const fullName = `${firstName} ${lastName}`.trim();
                const sidebarName = document.getElementById('sidebarName');
                const welcomeName = document.getElementById('dashWelcomeName');
                if (sidebarName) sidebarName.innerText = fullName;
                if (welcomeName) welcomeName.innerText = firstName;

                await fetchTreasurerProfile(true);
            } else {
                const data = await response.json();
                alert('Failed to update account: ' + (data.message || 'Validation error'));
            }
        } catch (error) {
            alert('Network error while saving account.');
        } finally {
            if (btn) {
                btn.innerText = 'Save Changes';
                btn.disabled = false;
            }
        }
    }

    function triggerAccountImagePicker() {
        const input = document.getElementById('settingsImageInput');
        if (input) input.click();
    }

    function handleAccountImageChange(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            alert('File size exceeds 5MB.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const cropModal = document.getElementById('accountCropModal');
            if (cropModal) cropModal.style.display = 'flex';

            const img = document.getElementById('accountCropperImage');
            if (img) img.src = e.target.result;

            if (window.trsCropper) window.trsCropper.destroy();
            if (img) {
                window.trsCropper = new Cropper(img, {
                    aspectRatio: 1,
                    viewMode: 1
                });
            }
        };
        reader.readAsDataURL(file);
        event.target.value = '';
    }

    function closeAccountCropModal() {
        const cropModal = document.getElementById('accountCropModal');
        if (cropModal) cropModal.style.display = 'none';

        if (window.trsCropper) {
            window.trsCropper.destroy();
            window.trsCropper = null;
        }
    }

    function applyAccountCrop() {
        if (!window.trsCropper) return;

        window.trsCropper.getCroppedCanvas({
            width: 500,
            height: 500
        }).toBlob((blob) => {
            window.trsAccountImageFile = new File([blob], "admin_profile.jpg", {
                type: "image/jpeg"
            });
            const previewUrl = URL.createObjectURL(window.trsAccountImageFile);

            window.trsSessionAvatarUrl = previewUrl;

            document.querySelectorAll('img[alt="Profile"], #settingsAccountAvatar').forEach(img => {
                img.src = previewUrl;
            });

            closeAccountCropModal();
        }, 'image/jpeg', 0.9);
    }

    async function removeAccountAvatar() {
        if (!confirm("Are you sure you want to remove your profile photo?")) return;

        try {
            const apiBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
            const response = await fetch(`${apiBase}/v1/user/avatar`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                alert('Avatar removed successfully.');
                window.trsAccountImageFile = null;
                window.trsSessionAvatarUrl = null;

                // CRITICAL CACHE FLUSH: Forcing refresh to erase image from memory
                await fetchTreasurerProfile(true);
            } else {
                alert('Failed to delete avatar from the server.');
            }
        } catch (err) {
            console.error('Error:', err);
        }
    }
</script>