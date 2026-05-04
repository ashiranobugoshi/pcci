<script>
    const token = localStorage.getItem('token');
    const TREASURER_MEMBERS_AUTO_REFRESH_MS = 15000;
    
    // Global data
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
    
    let dashboardPaymentRange = 'day';
    let dashboardRevenueRange = 'month';
    let dashboardBarChartInstance = null;
    let dashboardPieChartInstance = null;
    let reportBarChartInstance = null;
    let reportPieChartInstance = null;
    let editingTransactionId = null;

    let currentApplicantId = null;
    let currentSelectedType = 1;
    let currentPasswordOtpCode = '';
    let currentPasswordOtpEmail = '';
    let accountImageFile = null;
    
    let approvedApplicantsForModal = []; // Used for the Add Member Modal

    let membershipTypes = [
        { "id": 1, "name": "Micro", "price": "500.00", "duration_in_months": 12 },
        { "id": 2, "name": "Small Enterprises", "price": "5000.00", "duration_in_months": 12 }
    ];

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
        return new Date(Number(year), Number(month) - 1, 1).toLocaleString('en-US', { month: 'short' });
    }

    function getTransactionAmount(record) {
        const amountRaw = record.amount || record.membership_fee || (record.membership_type_id === 1 ? 500 : 5000);
        return parseFloat(amountRaw) || 0;
    }

    function getMembershipLabel(record) {
        return (record.membership_type_id === 2 || getTransactionAmount(record) > 1000) ? 'Small' : 'Micro';
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
            return { currentStart: weekStart, currentEnd: weekEnd, prevStart: prevWeekStart, prevEnd: prevWeekEnd, currentLabel: "This Week's Payments:", previousLabel: 'Last week payment:' };
        }

        if (rangeKey === 'month') {
            const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
            const monthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            const prevMonthStart = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            const prevMonthEnd = new Date(now.getFullYear(), now.getMonth(), 0);
            return { currentStart: monthStart, currentEnd: monthEnd, prevStart: prevMonthStart, prevEnd: prevMonthEnd, currentLabel: "This Month's Payments:", previousLabel: 'Last month payment:' };
        }

        if (rangeKey === 'year') {
            const yearStart = new Date(now.getFullYear(), 0, 1);
            const yearEnd = new Date(now.getFullYear(), 11, 31);
            const prevYearStart = new Date(now.getFullYear() - 1, 0, 1);
            const prevYearEnd = new Date(now.getFullYear() - 1, 11, 31);
            return { currentStart: yearStart, currentEnd: yearEnd, prevStart: prevYearStart, prevEnd: prevYearEnd, currentLabel: "This Year's Payments:", previousLabel: 'Last year payment:' };
        }

        const yesterdayStart = addDays(todayStart, -1);
        return { currentStart: todayStart, currentEnd: todayStart, prevStart: yesterdayStart, prevEnd: yesterdayStart, currentLabel: "Today's Payments:", previousLabel: 'Yesterday payment:' };
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
            return { labels: ['Today'], data: [todayTotal] };
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
            return { labels, data };
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
            return { labels, data };
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
        return { labels: monthLabels, data: monthData };
    }

    function updateTransactionSummary(rows) {
        let total = 0, pending = 0, complete = 0, failed = 0;

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

        document.getElementById('trans-total-amt').innerText = fmt(total);
        document.getElementById('trans-pending-amt').innerText = fmt(pending);
        document.getElementById('trans-complete-amt').innerText = fmt(complete);
        document.getElementById('trans-failed-amt').innerText = fmt(failed);
        updateDashboardPaymentSummary(rows);
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

    function renderTransactionRows(rows) {
        const tbodyTrans = document.getElementById('transactions-table-body');
        if (!tbodyTrans) return;

        tbodyTrans.innerHTML = '';

        if (rows.length > 0) {
            rows.forEach((txn, index) => {
                const amountRaw = txn.amount || txn.membership_fee || (txn.membership_type_id === 1 ? 500 : 5000);
                const amt = parseFloat(amountRaw) || 0;
                const status = String(txn.status || 'pending').toLowerCase();
                const txnDate = (txn.created_at || txn.date_approved || '').split('T')[0];
                const transactionKey = getTransactionKey(txn, index);

                const statClass = status === 'pending' ? 'status-pending' : (status === 'failed' ? 'status-failed' : 'status-completed');
                const statusDisplay = status === 'completed' || status === 'paid' ? 'COMPLETED' : status.toUpperCase();
                const membershipText = amt > 1000 ? 'Small Enterprise' : 'Micro';
                const businessName = txn.applicant?.basic_profile?.registered_business_name
                    || txn.basic_profile?.registered_business_name
                    || 'Unknown';
                const orNumber = txn.or_number || txn.official_receipt_no || '---';

                tbodyTrans.insertAdjacentHTML('beforeend', `
                    <tr id="transaction-row-${transactionKey}">
                        <td class="fw-bold text-dark ps-4">${businessName}</td>
                        <td class="text-dark">Gcash</td>
                        <td class="text-dark">${txnDate || 'N/A'}</td>
                        <td class="text-dark">${membershipText}</td>
                        <td class="text-dark">${orNumber}</td>
                        <td class="text-center"><span class="status-badge ${statClass}">${statusDisplay}</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light border shadow-sm action-icon-btn" style="color: #3b82f6;" onclick="openTransactionEditModal('${transactionKey}')" title="Edit transaction"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-sm btn-light border shadow-sm action-icon-btn" style="color: #ef4444; margin-left: 4px;" onclick="deleteTransactionRecord('${transactionKey}')" title="Delete transaction"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `);
            });
        } else {
            tbodyTrans.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted">No transactions available.</td></tr>`;
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
        const membershipType = getMembershipLabel(record) === 'Small' ? 'Annual' : 'Annual';
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

        const completedStatuses = ['paid', 'completed'];
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
            monthBuckets[key] = { micro: 0, small: 0 };
        });

        allTransactionsData.forEach(record => {
            const status = String(record.status || '').toLowerCase();
            const amount = getTransactionAmount(record);
            const dateKey = getMonthKey(getRecordDate(record));
            const membershipLabel = getMembershipLabel(record);

            if (dateKey && monthBuckets[dateKey]) {
                if (membershipLabel === 'Small') {
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
        const revenueTrend = previousMonthRevenue > 0
            ? `${(((currentMonthRevenue - previousMonthRevenue) / previousMonthRevenue) * 100).toFixed(1)}%`
            : '0.0%';
        const failedTrend = previousMonthFailedCount > 0
            ? `${currentMonthFailedCount - previousMonthFailedCount}`
            : `${currentMonthFailedCount}`;

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

        const reportBar = document.getElementById('reportBarChart');
        if (reportBar) {
            const barLabels = monthKeys.map(getMonthLabel);
            const microData = monthKeys.map(key => monthBuckets[key].micro);
            const smallData = monthKeys.map(key => monthBuckets[key].small);

            if (reportBarChartInstance) reportBarChartInstance.destroy();
            reportBarChartInstance = new Chart(reportBar.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: [
                        { label: 'Micro', data: microData, backgroundColor: '#3b82f6', barPercentage: 0.6, categoryPercentage: 0.8 },
                        { label: 'Small', data: smallData, backgroundColor: '#ef4444', barPercentage: 0.6, categoryPercentage: 0.8 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: {size: 11} } } }, scales: { y: { grid: { color: '#eee', borderDash: [5, 5] }, ticks: { color: '#aaa', font: {size: 11} }, border: {display: false} }, x: { grid: { display: false }, ticks: { color: '#aaa', font: {size: 11} }, border: {display: false} } } }
            });
        }

        const reportPie = document.getElementById('reportPieChart');
        if (reportPie) {
            if (reportPieChartInstance) reportPieChartInstance.destroy();
            reportPieChartInstance = new Chart(reportPie.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Collected', 'Overdue'],
                    datasets: [{ data: [collectedPercent, overduePercent], backgroundColor: ['#22c55e', '#ef4444'], borderWidth: 0 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, font: {size: 11} } } } }
            });
        }

        const dashboardBar = document.getElementById('barChart');
        if (dashboardBar) {
            const revenueSeries = buildDashboardRevenueSeries(dashboardRevenueRange);

            if (dashboardBarChartInstance) dashboardBarChartInstance.destroy();
            dashboardBarChartInstance = new Chart(dashboardBar.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: revenueSeries.labels,
                    datasets: [{
                        label: 'Membership Revenue',
                        data: revenueSeries.data,
                        backgroundColor: '#3b82f6',
                        borderRadius: 8,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: '#eee', borderDash: [5, 5] }, ticks: { color: '#aaa', font: { size: 11 } }, border: { display: false } },
                        x: { grid: { display: false }, ticks: { color: '#aaa', font: { size: 11 } }, border: { display: false } }
                    }
                }
            });
        }

        const dashboardPie = document.getElementById('pieChart');
        if (dashboardPie) {
            if (dashboardPieChartInstance) dashboardPieChartInstance.destroy();
            dashboardPieChartInstance = new Chart(dashboardPie.getContext('2d'), {
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
                        legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, font: { size: 11 } } }
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
            document.getElementById('settingsEmailInput')?.value
            || localStorage.getItem('userEmail')
            || ''
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
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify({ email: candidateEmail || null })
            });

            const { data: result, raw } = await readApiResponse(response);
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

    function openOtpModal() { document.getElementById('otpModal').style.display = 'flex'; }
    function hideOtpModal() {
        document.getElementById('otpModal').style.display = 'none';
        document.querySelectorAll('.otp-box').forEach(box => box.value = '');
    }
    function closeOtpOverlay(e) { if (e.target.id === 'otpModal') hideOtpModal(); }
    
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

        boxes.forEach((box, index) => { box.value = digits[index] || ''; });
        currentPasswordOtpCode = boxes.map(box => box.value).join('');
        const firstEmpty = boxes.find(box => !box.value);
        if (firstEmpty) { firstEmpty.focus(); } else { verifyEnteredOtpAndProceed(); }
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
    function openResetPasswordModal() { document.getElementById('resetPasswordModal').style.display = 'flex'; }
    function hideResetPasswordModal() {
        document.getElementById('resetPasswordModal').style.display = 'none';
        document.getElementById('newPasswordInput').value = '';
        document.getElementById('rePasswordInput').value = '';
        validatePassword();
    }
    function closeResetPasswordOverlay(e) { if (e.target.id === 'resetPasswordModal') hideResetPasswordModal(); }
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
        if(/[a-z]/.test(pw)) { reqLower.classList.add('valid'); validCount++; } else { reqLower.classList.remove('valid'); }
        if(pw.length >= 8) { reqLen.classList.add('valid'); validCount++; } else { reqLen.classList.remove('valid'); }
        if(/[A-Z]/.test(pw)) { reqUpper.classList.add('valid'); validCount++; } else { reqUpper.classList.remove('valid'); }
        if(/[0-9]/.test(pw)) { reqNum.classList.add('valid'); validCount++; } else { reqNum.classList.remove('valid'); }

        if(validCount === 4) { submitBtn.classList.add('active'); } else { submitBtn.classList.remove('active'); }
    }
    async function submitNewPassword() {
        const pw1 = document.getElementById('newPasswordInput').value;
        const pw2 = document.getElementById('rePasswordInput').value;
        const btn = document.getElementById('resetPwSubmitBtn');
        const otpApiBase = (window.PCCI_API_BASE_URL || window.API_BASE_URL || '').replace(/\/$/, '');

        if(!btn.classList.contains('active')) { alert("Please ensure your password meets all security requirements."); return; }
        if(pw1 !== pw2) { alert("Passwords do not match!"); return; }
        if (!currentPasswordOtpCode || currentPasswordOtpCode.length !== 6) { alert('OTP is missing or invalid. Please request and enter OTP again.'); return; }

        try {
            btn.disabled = true;
            btn.innerText = 'Resetting...';

            const response = await fetch(`${otpApiBase}/user/request-password-change`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
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

            const { data: result, raw } = await readApiResponse(response);
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
    function openCropModal() { document.getElementById('cropModal').style.display = 'flex'; }
    function hideCropModal() { document.getElementById('cropModal').style.display = 'none'; }
    function closeCropOverlay(e) { if (e.target.id === 'cropModal') hideCropModal(); }
    function setNewProfilePicture() { alert("Profile picture successfully updated!"); hideCropModal(); }

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

    function prevTransactionPage() { if (currentTransactionPage > 1) { currentTransactionPage--; displayTransactionsPage(); } }
    function nextTransactionPage() { if (currentTransactionPage < Math.ceil(filteredTransactionsData.length / transactionsPerPage)) { currentTransactionPage++; displayTransactionsPage(); } }

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
            if(icon) icon.classList.replace('fa-moon', 'fa-sun');
            if(text) text.innerText = 'Light Mode';
            if(switchBtn) switchBtn.checked = true;
            localStorage.setItem('theme', 'dark');
        } else {
            if(icon) icon.classList.replace('fa-sun', 'fa-moon');
            if(text) text.innerText = 'Dark Mode';
            if(switchBtn) switchBtn.checked = false;
            localStorage.setItem('theme', 'light');
        }
    }

    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        setTimeout(() => {
            const icon = document.getElementById('darkModeIcon');
            const text = document.getElementById('darkModeText');
            const switchBtn = document.getElementById('darkModeSwitch');
            if(icon) icon.classList.replace('fa-moon', 'fa-sun');
            if(text) text.innerText = 'Light Mode';
            if(switchBtn) switchBtn.checked = true;
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
    function triggerAccountImagePicker() {
        const imageInput = document.getElementById('settingsImageInput');
        if (imageInput) imageInput.click();
    }
    function resolveUserFromResponse(payload) { return payload?.data?.user || payload?.data || payload?.user || payload || {}; }
    function normalizeImageUrl(value) {
        const raw = String(value || '').trim();
        if (!raw) return '';
        if (/^https?:\/\//i.test(raw)) return raw;
        return `${apiOrigin}/${raw.replace(/^\/+/, '')}`;
    }
    function applyAccountAvatar(imageValue) {
        const imageUrl = normalizeImageUrl(imageValue);
        if (!imageUrl) return;
        ['topbarAvatar', 'sidebarAvatar', 'settingsAccountAvatar'].forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.src = imageUrl;
        });
        localStorage.setItem('userImage', imageUrl);
    }
    function extractUserImage(user) {
        return user?.image_url || user?.image || user?.avatar || user?.profile_image || user?.profile_photo || user?.photo || '';
    }
    function handleAccountImageChange(event) {
        const file = event?.target?.files?.[0] || null;
        accountImageFile = file;
        if (file) {
            const previewUrl = URL.createObjectURL(file);
            ['topbarAvatar', 'sidebarAvatar', 'settingsAccountAvatar'].forEach((id) => {
                const el = document.getElementById(id);
                if (el) el.src = previewUrl;
            });
        }
    }
    function toggleAccountField(fieldId) {
        const input = document.getElementById(fieldId);
        if (!input) return;
        const editButton = input.parentElement ? input.parentElement.querySelector('.new-acc-edit') : null;
        const isReadOnly = input.hasAttribute('readonly');

        if (isReadOnly) {
            input.removeAttribute('readonly');
            input.focus();
            input.select();
            if (editButton) editButton.innerHTML = '<i class="fa fa-check"></i> Done';
        } else {
            input.setAttribute('readonly', 'readonly');
            if (editButton) editButton.innerHTML = '<i class="fa fa-edit"></i> Edit';
        }
    }
    async function saveAccountSettings() {
        const firstNameInput = document.getElementById('settingsFirstName');
        const lastNameInput = document.getElementById('settingsLastName');
        const emailInput = document.getElementById('settingsEmailInput');
        const contactInput = document.getElementById('settingsContactInput');

        const firstName = (firstNameInput?.value || '').trim();
        const lastName = (lastNameInput?.value || '').trim();
        const email = (emailInput?.value || '').trim();
        const contact = (contactInput?.value || '').trim();

        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { alert('Please enter a valid email address.'); return; }
        if (!email) { alert('Email is required.'); return; }

        const endpointBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
        const endpoint = `${endpointBase}/v1/user/change-info`;
        const payload = new FormData();
        if (accountImageFile) payload.append('image', accountImageFile);
        payload.append('email', email);
        payload.append('_method', 'PUT');
        payload.append('contact', contact);
        payload.append('first_name', firstName);
        payload.append('last_name', lastName);

        const saveButton = document.querySelector('#settings-account .new-acc-action-dark');
        if (saveButton) { saveButton.disabled = true; saveButton.dataset.originalText = saveButton.innerText; saveButton.innerText = 'Saving...'; }

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Accept': 'application/json', ...(token ? { 'Authorization': `Bearer ${token}` } : {}) },
                body: payload
            });

            const { data: result, raw } = await readApiResponse(response);
            if (!response.ok) throw new Error(result.message || raw || 'Failed to save account settings.');

            const userPayload = resolveUserFromResponse(result);
            const responseImage = extractUserImage(userPayload);
            if (responseImage) applyAccountAvatar(responseImage);

            const fullName = [firstName, lastName].filter(Boolean).join(' ').trim();
            if (fullName) {
                localStorage.setItem('userName', fullName);
                const sidebarName = document.getElementById('sidebarName');
                if (sidebarName) sidebarName.innerText = fullName;
            }

            localStorage.setItem('userEmail', email);
            const sidebarEmail = document.getElementById('sidebarEmail');
            if (sidebarEmail) sidebarEmail.innerText = email;

            localStorage.setItem('userContact', contact);

            ['settingsFirstName', 'settingsLastName', 'settingsEmailInput', 'settingsContactInput'].forEach((id) => {
                const input = document.getElementById(id);
                if (!input) return;
                input.setAttribute('readonly', 'readonly');
                const editButton = input.parentElement ? input.parentElement.querySelector('.new-acc-edit') : null;
                if (editButton) editButton.innerHTML = '<i class="fa fa-edit"></i> Edit';
            });

            accountImageFile = null;
            const imageInput = document.getElementById('settingsImageInput');
            if (imageInput) imageInput.value = '';

            alert(result.message || 'Account settings saved.');
        } catch (error) {
            console.error('Error updating account settings:', error);
            alert(error.message || 'Failed to save account settings.');
        } finally {
            if (saveButton) { saveButton.disabled = false; saveButton.innerText = saveButton.dataset.originalText || 'Save Changes'; }
        }
    }

    function applyStoredAccountSettings() {
        const storedName = localStorage.getItem('userName') || 'Treasurer';
        const sidebarName = document.getElementById('sidebarName');
        if (sidebarName) sidebarName.innerText = storedName;

        const nameParts = storedName.split(' ');
        const firstNameInput = document.getElementById('settingsFirstName');
        const lastNameInput = document.getElementById('settingsLastName');
        if (firstNameInput) firstNameInput.value = nameParts[0] || storedName;
        if (lastNameInput) lastNameInput.value = nameParts.slice(1).join(' ');

        const storedEmail = localStorage.getItem('userEmail') || '';
        const sidebarEmail = document.getElementById('sidebarEmail');
        if (sidebarEmail) sidebarEmail.innerText = storedEmail || 'No email';
        const settingsEmailInput = document.getElementById('settingsEmailInput');
        if (settingsEmailInput) settingsEmailInput.value = storedEmail;

        const storedContact = localStorage.getItem('userContact') || '';
        const settingsContactInput = document.getElementById('settingsContactInput');
        if (settingsContactInput) settingsContactInput.value = storedContact;

        const storedImage = localStorage.getItem('userImage') || '';
        if (storedImage) applyAccountAvatar(storedImage);
    }

    async function loadAccountSettingsFromApi() {
        if (!token) return false;
        try {
            const endpointBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
            const response = await fetch(`${endpointBase}/v1/user`, {
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
            });
            if (!response.ok) return false;

            const { data: result } = await readApiResponse(response);
            const user = result?.data || result?.user || result || {};

            const localName = (localStorage.getItem('userName') || '').trim();
            const localEmail = (localStorage.getItem('userEmail') || '').trim();
            const localContact = (localStorage.getItem('userContact') || '').trim();

            const firstName = (user.first_name || '').trim();
            const lastName = (user.last_name || '').trim();
            const fallbackName = (user.name || '').trim();
            const apiFullName = [firstName, lastName].filter(Boolean).join(' ').trim() || fallbackName;
            const fullName = localName || apiFullName;
            const email = localEmail || String(user.email || '').trim();
            const contact = localContact || String(user.contact || user.contact_no || user.phone || '').trim();
            const imageValue = extractUserImage(user) || localStorage.getItem('userImage') || '';

            if (fullName) {
                if (!localName && apiFullName) localStorage.setItem('userName', fullName);
                const sidebarName = document.getElementById('sidebarName');
                if (sidebarName) sidebarName.innerText = fullName;
                const nameParts = fullName.split(' ');
                const firstNameInput = document.getElementById('settingsFirstName');
                const lastNameInput = document.getElementById('settingsLastName');
                if (firstNameInput) firstNameInput.value = nameParts[0] || '';
                if (lastNameInput) lastNameInput.value = nameParts.slice(1).join(' ');
            }

            if (email) {
                if (!localEmail && String(user.email || '').trim()) localStorage.setItem('userEmail', email);
                const sidebarEmail = document.getElementById('sidebarEmail');
                if (sidebarEmail) sidebarEmail.innerText = email;
                const settingsEmailInput = document.getElementById('settingsEmailInput');
                if (settingsEmailInput) settingsEmailInput.value = email;
            }

            if (contact) {
                if (!localContact && String(user.contact || user.contact_no || user.phone || '').trim()) localStorage.setItem('userContact', contact);
                const settingsContactInput = document.getElementById('settingsContactInput');
                if (settingsContactInput) settingsContactInput.value = contact;
            }

            if (imageValue) applyAccountAvatar(imageValue);
            return Boolean(fullName || email || contact);
        } catch (_) { return false; }
    }

    console.log("Fetching from:", `${window.API_BASE_URL}/v1/notifications`);
 
async function updateNotificationsPanel() {
    try {
        // Fetch from your new single endpoint
        const response = await fetch(`${window.API_BASE_URL}/v1/notifications`, {
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
                const dateStr = isNaN(dateObj.getTime()) ? 'Recent' : dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                
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
            `}).join('');
            
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
 
 
// Initialize on page load and set up auto-refresh
document.addEventListener('DOMContentLoaded', () => {
    if (typeof allApplicantsData !== 'undefined' && 
        typeof allTransactionsData !== 'undefined' && 
        typeof allMembersData !== 'undefined') {
        updateNotificationsPanel();
        
        // Auto-refresh notifications every 30 seconds
        setInterval(() => {
            if (typeof allApplicantsData !== 'undefined') {
                updateNotificationsPanel();
            }
        }, 30000);
    }
});

function openFullNotificationsModal() {
    document.getElementById('notificationPanel').style.display = 'none';
    
    const modalBody = document.getElementById('fullNotificationsBody');
    const items = window.allNotificationItems || [];

    if (items.length > 0) {
        // Render ALL items in the array, unsliced
        modalBody.innerHTML = items.map(item => {
            const dateObj = new Date(item.created_at);
            const dateStr = isNaN(dateObj.getTime()) 
                ? 'Recent' 
                : dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute:'2-digit' });
            
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
async function markAllNotificationsAsRead() {
    try {
        await fetch('/api/v1/notifications/read-all', {
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

    // --- MODALS FOR PAYMENTS ---
    function openSimpleProof(url) {
        if (!url || url === '#' || url === 'null') { alert("No proof found."); return; }
        const img = document.getElementById('simpleModalImage');
        document.getElementById('simpleModalSpinner').style.display = 'flex';
        img.style.display = 'none';
        img.src = url.startsWith('http') ? url : `${window.API_BASE_URL || ''}/${url.replace(/^\/+/, '')}`;
        document.getElementById('simpleProofModal').style.display = 'flex';
    }
    function onSimpleImageLoad() { document.getElementById('simpleModalImage').style.display = 'block'; document.getElementById('simpleModalSpinner').style.display = 'none'; }
    function hideSimpleProofModal() { document.getElementById('simpleProofModal').style.display = 'none'; }
    function closeSimpleProofModal(e) { if (e.target.id === 'simpleProofModal') hideSimpleProofModal(); }

    function openProof(url, applicantId) {
        if (!url || url === '#' || url === 'null') { alert("No proof found."); return; }
        currentApplicantId = applicantId; 
        const img = document.getElementById('modalImage');
        document.getElementById('modalSpinner').style.display = 'flex';
        img.style.display = 'none';
        img.src = url.startsWith('http') ? url : `${window.API_BASE_URL || ''}/${url.replace(/^\/+/, '')}`;
        selectType(1); 
        document.getElementById('proofModal').style.display = 'flex';
    }
    function onImageLoad() { document.getElementById('modalImage').style.display = 'block'; document.getElementById('modalSpinner').style.display = 'none'; }
    function hideProofModal() { document.getElementById('proofModal').style.display = 'none'; }
    function closeProofModal(e) { if (e.target.id === 'proofModal') hideProofModal(); }

    function selectType(id) {
        currentSelectedType = id; 
        document.getElementById('toggleBtn1').className = (id == 1) ? 'type-toggle-btn active-1 flex-grow-1' : 'type-toggle-btn flex-grow-1';
        document.getElementById('toggleBtn2').className = (id == 2) ? 'type-toggle-btn active-2 flex-grow-1' : 'type-toggle-btn flex-grow-1';
    }

    // POST /v1/payments (Approve)
    async function confirmProcessing() {
        const data = membershipTypes.find(m => m.id == currentSelectedType);
        if (!data || !currentApplicantId) return;

        try {
            const endpointBase = (window.API_BASE_URL || '/api').replace(/\/$/, '');
            const response = await fetch(`${endpointBase}/v1/payments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify({
                    applicant_id: parseInt(currentApplicantId),
                    membership_type_id: parseInt(currentSelectedType)
                })
            });

            if (response.ok || response.status === 200 || response.status === 201) {
                hideProofModal();
                localStorage.setItem('membersNeedsRefresh', '1');
                localStorage.setItem('membersNeedsRefreshAt', String(Date.now()));

                const amtLbl = document.getElementById(`amount-label-${currentApplicantId}`);
                const typeLbl = document.getElementById(`type-label-${currentApplicantId}`);
                const bge = document.getElementById(`status-badge-${currentApplicantId}`);
                const actionBox = document.getElementById(`action-container-${currentApplicantId}`);

                if(amtLbl) { amtLbl.innerText = `₱ Processed`; amtLbl.className = "fw-bold text-dark"; }
                if(typeLbl) { typeLbl.innerText = "PAID"; typeLbl.className = "text-success fw-bold"; }
                if(bge) { bge.innerHTML = `<i class="fa fa-check-double me-1"></i> PAID`; bge.className = "badge bg-success text-white px-2 py-1 rounded-pill fw-bold shadow-sm"; }
                if(actionBox) { actionBox.innerHTML = `<button class="action-btn btn-gray" disabled style="width: 130px;"><i class="fa fa-check"></i> Processed</button>`; }

                fetchMembers();
                fetchTransactions();
                fetchRecentPayments();
                alert("Success: Payment Processed!");
            } else {
                const result = await response.json().catch(() => ({}));
                if (response.status === 401 || response.status === 403) {
                    alert("Access denied. Your account may not have permission to process payments.");
                } else {
                    alert(`Error: ${result.message || 'Something went wrong. Please try again.'}`);
                }
            }
        } catch (err) { alert("Network error: Could not reach the server."); }
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
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify({ rejection_reason: rejectionReason.trim() })
            });

            if (response.ok) {
                hideProofModal();
                const amtLbl = document.getElementById(`amount-label-${currentApplicantId}`);
                const typeLbl = document.getElementById(`type-label-${currentApplicantId}`);
                const bge = document.getElementById(`status-badge-${currentApplicantId}`);
                const actionBox = document.getElementById(`action-container-${currentApplicantId}`);

                if(amtLbl) { amtLbl.innerText = `---`; amtLbl.className = "fw-bold text-muted"; }
                if(typeLbl) { typeLbl.innerText = "REJECTED"; typeLbl.className = "text-danger fw-bold"; }
                if(bge) { bge.innerHTML = `<i class="fa fa-times-circle me-1"></i> REJECTED`; bge.className = "badge bg-danger text-white px-2 py-1 rounded-pill fw-bold shadow-sm"; }
                if(actionBox) { actionBox.innerHTML = `<button class="action-btn btn-gray" disabled style="width: 130px;"><i class="fa fa-times"></i> Rejected</button>`; }

                fetchMembers();
                fetchTransactions();
                fetchRecentPayments();
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
    function hideMemberModal() { document.getElementById('memberDetailsModal').style.display = 'none'; }
    function closeMemberModal(e) { if (e.target.id === 'memberDetailsModal') hideMemberModal(); }

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
    function closeAddPaymentModal(e) { if (e) { e.preventDefault(); e.stopPropagation(); } hideAddPaymentModal(); }
    function closeAddPaymentOverlay(e) { if (e.target.id === 'addPaymentModal') hideAddPaymentModal(); }
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
        if (!editingTransactionId) { alert('Payment details confirmed!'); hideAddPaymentModal(); return; }
        const record = getTransactionRecordByKey(editingTransactionId);
        if (!record) { hideAddPaymentModal(); editingTransactionId = null; return; }

        const updatedOrNumber = document.getElementById('transactionOrNumber').value || record.or_number || '---';
        const updatedDate = document.getElementById('transactionPaymentDate').value || getRecordDate(record);
        const updatedPaymentType = document.getElementById('transactionPaymentType').value || 'GCash';
        const updatedMembership = document.getElementById('transactionMembershipType').value || 'Annual';
        const apiId = getTransactionApiId(editingTransactionId);

        if (!apiId) { alert('This transaction cannot be updated because it has no backend id.'); return; }

        try {
            const response = await fetch(`/treasurer/transactions/${apiId}`, {
                method: 'PUT',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
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
        } catch (error) { console.error(error); alert(error.message || 'Failed to update transaction.'); }
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
            return { data: await response.json().catch(() => ({})), raw: '' }; 
        }
        return { data: {}, raw: await response.text().catch(() => '') };
    }
        // --- INITIALIZATION ---
        document.addEventListener('DOMContentLoaded', async () => {
        if (!token) { window.location.href = '/login'; return; }
        
        sanitizeSearchAutofill();
        setTimeout(sanitizeSearchAutofill, 120);
        
        const loadedFromApi = await loadAccountSettingsFromApi();
        if (!loadedFromApi) applyStoredAccountSettings();
        
        fetchApplicants();
        fetchMembers();
        fetchRecentPayments();
        fetchTransactions();
        initCharts();

        const searchInputs = [
            { id: 'memberSearch', func: applyMemberFilters },
            { id: 'memberSort', func: applyMemberFilters },
            { id: 'applicantSearch', func: applyApplicantFilters },
            { id: 'applicantSort', func: applyApplicantFilters },
            { id: 'applicantStatusFilter', func: applyApplicantFilters } // Strict Treasurer Dropdown
        ];
        
        searchInputs.forEach(input => {
            const el = document.getElementById(input.id);
            if (el) {
                el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', input.func);
            }
        });

        // Event listener for Add Member Dropdown
        const companySelect = document.getElementById('addMemberCompanySelect');
        if (companySelect) {
            companySelect.addEventListener('change', function() {
                const selectedId = this.value;
                if (!selectedId) {
                    populateAddMemberReadOnlyFields(null);
                    return;
                }
                const selectedApplicant = approvedApplicantsForModal.find(app => String(app.id) === String(selectedId));
                populateAddMemberReadOnlyFields(selectedApplicant);
            });
        }

        const dashboardPaymentRangeEl = document.getElementById('dashboardPaymentRange');
        if (dashboardPaymentRangeEl) {
            dashboardPaymentRange = dashboardPaymentRangeEl.value || 'day';
            dashboardPaymentRangeEl.addEventListener('change', (event) => {
                dashboardPaymentRange = event.target.value;
                updateDashboardPaymentSummary(allTransactionsData);
            });
        }

        const dashboardRevenueRangeEl = document.getElementById('dashboardRevenueRange');
        if (dashboardRevenueRangeEl) {
            dashboardRevenueRange = dashboardRevenueRangeEl.value || 'month';
            dashboardRevenueRangeEl.addEventListener('change', (event) => {
                dashboardRevenueRange = event.target.value;
                updateReportsDashboard();
            });
        }

        const savedTab = localStorage.getItem('activeTab') || 'dashboard';
        switchTab(savedTab, false);

        setInterval(() => {
            if (document.visibilityState !== 'visible') return;
            if (!['members', 'dashboard', 'reports'].includes(currentActiveTab)) return;
            fetchMembers();
        }, TREASURER_MEMBERS_AUTO_REFRESH_MS);
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
                    headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` } 
                }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=paid`, { 
                    method: 'GET', 
                    headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` } 
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
                fetch(`${window.API_BASE_URL}/v1/applicants?status=approved`, { headers: { 'Authorization': `Bearer ${token}` } }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=rejected`, { headers: { 'Authorization': `Bearer ${token}` } })
            ]);

            let combinedData = [];
            if (resApproved.ok) { const data1 = await resApproved.json(); if (data1.data) combinedData = combinedData.concat(data1.data); }
            if (resRejected.ok) { const data2 = await resRejected.json(); if (data2.data) combinedData = combinedData.concat(data2.data); }

            allApplicantsData = combinedData;

            try {
                const membersRes = await fetch('/api/v1/members', { headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` } });
                if (membersRes.ok) {
                    const membersJson = await membersRes.json();
                    if (Array.isArray(membersJson?.data)) allMembersData = membersJson.data;
                }
            } catch (_) {}

            applyApplicantFilters();
            updateNotificationsPanel();
            updateReportsDashboard();
        } catch (err) { console.error('Error fetching applicants:', err); }
    }

    function displayApplicantsPage() {
        const totalPages = Math.ceil(filteredApplicantsData.length / applicantsPerPage) || 1;
        if (currentApplicantPage > totalPages) currentApplicantPage = totalPages;
        if (currentApplicantPage < 1) currentApplicantPage = 1;
        
        const pageData = filteredApplicantsData.slice((currentApplicantPage - 1) * applicantsPerPage, currentApplicantPage * applicantsPerPage);
        const tbody = document.getElementById('applicants-table-body');
        if(!tbody) return;
        
        tbody.innerHTML = '';
        if(pageData.length === 0) {
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
        if(paginationText) paginationText.innerText = `Page ${currentApplicantPage} of ${totalPages}`;
    }

    function prevApplicantPage() { if (currentApplicantPage > 1) { currentApplicantPage--; displayApplicantsPage(); } }
    function nextApplicantPage() { if (currentApplicantPage < Math.ceil(filteredApplicantsData.length / applicantsPerPage)) { currentApplicantPage++; displayApplicantsPage(); } }

    async function fetchMembers() {
        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/members`, {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
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
            if(tbody) tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 text-danger fw-bold">Network error. Failed to load data.</td></tr>`;
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

        if (pageData.length === 0 && totalRecords > 0 && currentMemberPage > 1) {
            currentMemberPage = totalPages;
            startIndex = (currentMemberPage - 1) * membersPerPage;
            pageData = filteredMembersData.slice(startIndex, startIndex + membersPerPage);
        }
        
        const tbody = document.getElementById('members-table-body');
        if(!tbody) return;

        tbody.innerHTML = '';
        if(totalRecords === 0) tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 text-muted fw-bold">No members found matching your search.</td></tr>`;

        pageData.forEach(member => {
            const name = member.applicant?.basic_profile?.registered_business_name || 'N/A';
            const orNumber = `OR-${10000 + member.id}`; 
            const regDate = member.created_at ? member.created_at.split('T')[0] : 'N/A';
            let expDate = member.membership_end_date ? member.membership_end_date.split('T')[0] : 'N/A';
            if(expDate === 'N/A' && member.created_at) {
                 const dateObj = new Date(member.created_at);
                 dateObj.setFullYear(dateObj.getFullYear() + 1);
                 expDate = dateObj.toISOString().split('T')[0];
            }

            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="fw-bold text-dark">${name}</td>
                    <td>Annual</td>
                    <td class="fw-bold text-dark">₱5,000</td>
                    <td class="text-dark">${orNumber}</td>
                    <td class="text-dark">${regDate}</td>
                    <td class="text-dark">${expDate}</td>
                    <td><span class="status-badge status-completed">Active</span></td>
                    <td><button class="btn btn-sm btn-link p-0 fw-bold" onclick="openSimpleProof('${member.proof_of_payment_url}')">View File</button></td>
                    <td><button class="action-btn btn-gray" onclick="viewMemberDetails(${member.id})">Details</button></td>
                </tr>
            `);
        });
        const paginationText = document.getElementById('member-pagination-text');
        if(paginationText) paginationText.innerText = `Page ${currentMemberPage} of ${totalPages}`;
    }

    function prevMemberPage() { if (currentMemberPage > 1) { currentMemberPage--; displayMembersPage(); } }
    function nextMemberPage() { if (currentMemberPage < getMembersTotalPages()) { currentMemberPage++; displayMembersPage(); } }

    function verifyRecentPayment(proofUrl, applicantId) { openProof(proofUrl, applicantId); }
    function printRecentReceipt(applicantId) { window.print(); }
    
    function rejectRecentPayment(applicantId) {
        const row = document.getElementById(`recent-payment-row-${applicantId}`);
        if (row) row.remove();
        const tbody = document.getElementById('recent-payments-table-body');
        if (tbody && tbody.children.length === 0) tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No recent payments available.</td></tr>`;
    }

    async function fetchRecentPayments() {
        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/applicants?status=approved`, { headers: { 'Authorization': `Bearer ${token}` } });
            if (!checkAuth(response)) return;
            const data = await response.json();
            if (response.ok && data.data) {
                const tbody = document.getElementById('recent-payments-table-body');
                if(!tbody) return;
                tbody.innerHTML = ''; 
                data.data.forEach(app => {
                    tbody.insertAdjacentHTML('beforeend', `
                        <tr id="recent-payment-row-${app.id}">
                            <td class="fw-bold text-dark">${app.basic_profile?.registered_business_name || 'N/A'}</td>
                            <td>Annual</td>
                            <td class="fw-bold text-dark">₱5,000</td>
                            <td class="text-dark">Pending</td>
                            <td class="text-dark">${app.date_approved || 'N/A'}</td>
                            <td><button class="btn btn-sm btn-link p-0 fw-bold" onclick="openSimpleProof('${app.proof_of_payment_url}')">View File</button></td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap">
                                    <button class="btn btn-success btn-sm fw-semibold" style="min-width: 74px;" onclick="verifyRecentPayment('${app.proof_of_payment_url}', ${app.id})">Verify</button>
                                    <button class="btn btn-danger btn-sm fw-semibold" style="min-width: 74px;" onclick="rejectRecentPayment(${app.id})">Reject</button>
                                    <button class="btn btn-sm fw-semibold text-white" style="background:#b61b2a; min-width: 118px;" onclick="printRecentReceipt(${app.id})"><i class="fa fa-print me-1"></i> Print Receipt</button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
                if ((data.data || []).length === 0) tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No recent payments available.</td></tr>`;
            }
        } catch (err) {}
    }

    async function fetchTransactions() {
        try {
            const [paidRes, approvedRes, failedRes, cancelledRes] = await Promise.all([
                fetch(`${window.API_BASE_URL}/v1/applicants?status=paid`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=approved`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=failed`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=cancelled`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } })
            ]);

            if (!checkAuth(paidRes) || !checkAuth(approvedRes) || !checkAuth(failedRes) || !checkAuth(cancelledRes)) return;

            const paidData = paidRes.ok ? await paidRes.json() : { data: [] };
            const approvedData = approvedRes.ok ? await approvedRes.json() : { data: [] };
            const failedData = failedRes.ok ? await failedRes.json() : { data: [] };
            const cancelledData = cancelledRes.ok ? await cancelledRes.json() : { data: [] };

            const paidRows = (paidData.data || []).map(app => ({ ...app, status: 'paid' }));
            const pendingRows = (approvedData.data || []).map(app => ({ ...app, status: 'approved' }));
            const failedRows = (failedData.data || []).map(app => ({ ...app, status: 'failed' }));
            const cancelledRows = (cancelledData.data || []).map(app => ({ ...app, status: 'cancelled' }));
            
            const rows = [...pendingRows, ...paidRows, ...failedRows, ...cancelledRows];

            allTransactionsData = rows;
            filteredTransactionsData = rows.slice();
            currentTransactionPage = 1;
            updateTransactionSummary(allTransactionsData);
            displayTransactionsPage();
            updateNotificationsPanel();
            updateReportsDashboard();

        } catch (err) {
            console.error('Error fetching transactions:', err);
            allTransactionsData = [];
            filteredTransactionsData = [];
            currentTransactionPage = 1;
            updateTransactionSummary([]);
            displayTransactionsPage();
        }
    }

    function initCharts() { updateReportsDashboard(); }

    function refreshTabData(tabName) {
        if (!token) return;

        if (tabName === 'dashboard') {
            fetchApplicants(); fetchMembers(); fetchRecentPayments(); fetchTransactions(); initCharts();
        } else if (tabName === 'members') {
            fetchMembers();
            fetchTreasurerApprovedApplicantsForModal(); 
        } else if (tabName === 'applicants') {
            fetchApplicants(); fetchRecentPayments();
        } else if (tabName === 'transactions') {
            fetchTransactions();
        } else if (tabName === 'reports') {
            fetchApplicants(); fetchMembers(); fetchTransactions(); initCharts();
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

    let currentActiveTab = 'dashboard';

    function switchTab(tabName, shouldReload = true) {
        currentActiveTab = tabName;
        localStorage.setItem('activeTab', tabName);

        if (shouldReload) {
            window.location.href = `${window.location.pathname}?refresh=${Date.now()}#${tabName}`;
            return;
        }

        document.querySelectorAll('.content-section').forEach(s => s.style.display = 'none');
        document.querySelectorAll('.sidebar-menu li').forEach(li => li.classList.remove('active'));
        
        const targetSection = document.getElementById('section-' + tabName);
        const targetNav = document.getElementById('nav-' + tabName);
        if(targetSection) targetSection.style.display = 'block';
        if(targetNav) targetNav.classList.add('active');
        
        if(tabName !== 'settings') {
            const mainSet = document.getElementById('settings-main');
            if(mainSet) mainSet.style.display = 'block';
            const accSet = document.getElementById('settings-account');
            if(accSet) accSet.style.display = 'none';
            const secSet = document.getElementById('settings-security');
            if(secSet) secSet.style.display = 'none';
            const prefSet = document.getElementById('settings-preferences');
            if(prefSet) prefSet.style.display = 'none';
        }

        refreshTabData(tabName);
    }
    
    function toggleNotificationPanel(e) { e.stopPropagation(); const p = document.getElementById('notificationPanel'); p.style.display = p.style.display === 'flex' ? 'none' : 'flex'; }
    function clearNotifications(e) { e.stopPropagation(); document.getElementById('notificationPanel').style.display = 'none'; }
    function logout() { localStorage.removeItem('token'); window.location.href = '/login'; }
</script>