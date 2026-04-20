<script>
    const token = localStorage.getItem('token');
    
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

        // Dashboard charts use the same computed values so they stay in sync with reports.
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
        if (e.target && e.target.id === 'otpFeedbackModal') {
            hideOtpFeedbackModal();
        }
    }

    async function requestPasswordChangeOtp() {
        const btn = document.getElementById('requestOtpBtn');
        const endpoint = '/api/user/confirm-password-change';
        currentPasswordOtpCode = '';

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
                body: JSON.stringify({})
            });

            const result = await response.json().catch(() => ({}));
            if (!response.ok) {
                if (result.tried_endpoints && Array.isArray(result.tried_endpoints)) {
                    console.warn('OTP request tried endpoints:', result.tried_endpoints);
                }
                throw new Error(result.message || 'Failed to request OTP. Please try again.');
            }

            const otpEmailEl = document.getElementById('otpTargetEmail');
            const emailFromApi = result.email || result.user?.email || result.data?.email || 'your email';
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
        if (input.value.length === 1) {
            let next = input.nextElementSibling;
            if (next && next.tagName.toLowerCase() === 'input') {
                next.focus();
            } else if (!next) {
                // Last OTP digit entered: verify OTP with backend before allowing reset flow.
                currentPasswordOtpCode = Array.from(document.querySelectorAll('.otp-box')).map(box => box.value).join('');
                verifyEnteredOtpAndProceed();
            }
        }
    }

    async function verifyEnteredOtpAndProceed() {
        if (!currentPasswordOtpCode || currentPasswordOtpCode.length !== 6) {
            openOtpFeedbackModal('Invalid OTP', 'Please enter a valid 6-digit OTP.');
            return;
        }

        try {
            const response = await fetch('/api/user/verify-password-otp', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify({
                    otp: currentPasswordOtpCode,
                    email: currentPasswordOtpEmail || null,
                })
            });

            const result = await response.json().catch(() => ({}));
            if (!response.ok) {
                if (response.status === 422) {
                    throw new Error('Wrong OTP. Please try again.');
                }
                throw new Error(result.message || 'Invalid OTP.');
            }

            hideOtpModal();
            openResetPasswordModal();
        } catch (error) {
            console.error('Error verifying OTP:', error);
            openOtpFeedbackModal('Wrong OTP', error.message || 'Wrong OTP. Please try again.');
            currentPasswordOtpCode = '';
            document.querySelectorAll('.otp-box').forEach(box => box.value = '');
            const firstOtpBox = document.querySelector('.otp-box');
            if (firstOtpBox) firstOtpBox.focus();
        }
    }
    
    document.querySelectorAll('.otp-box').forEach(box => {
        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value) {
                let prev = this.previousElementSibling;
                if (prev && prev.tagName.toLowerCase() === 'input') {
                    prev.focus();
                }
            }
        });
    });

    // --- RESET PASSWORD MODAL FUNCTIONS ---
    function openResetPasswordModal() {
        document.getElementById('resetPasswordModal').style.display = 'flex';
    }
    function hideResetPasswordModal() {
        document.getElementById('resetPasswordModal').style.display = 'none';
        document.getElementById('newPasswordInput').value = '';
        document.getElementById('rePasswordInput').value = '';
        validatePassword(); // Reset checklist
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

        if(/[a-z]/.test(pw)) { reqLower.classList.add('valid'); validCount++; } else { reqLower.classList.remove('valid'); }
        if(pw.length >= 8) { reqLen.classList.add('valid'); validCount++; } else { reqLen.classList.remove('valid'); }
        if(/[A-Z]/.test(pw)) { reqUpper.classList.add('valid'); validCount++; } else { reqUpper.classList.remove('valid'); }
        if(/[0-9]/.test(pw)) { reqNum.classList.add('valid'); validCount++; } else { reqNum.classList.remove('valid'); }

        // Light up button if all 4 conditions met
        if(validCount === 4) {
            submitBtn.classList.add('active');
        } else {
            submitBtn.classList.remove('active');
        }
    }
    async function submitNewPassword() {
        const pw1 = document.getElementById('newPasswordInput').value;
        const pw2 = document.getElementById('rePasswordInput').value;
        const btn = document.getElementById('resetPwSubmitBtn');

        if(!btn.classList.contains('active')) {
            alert("Please ensure your password meets all security requirements.");
            return;
        }
        if(pw1 !== pw2) {
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

            const response = await fetch('/api/user/request-password-change', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify({
                    otp: currentPasswordOtpCode,
                    email: currentPasswordOtpEmail || null,
                    password: pw1,
                    password_confirmation: pw2
                })
            });

            const result = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(result.message || 'Failed to reset password.');
            }

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

    const transSearchInput = document.getElementById('transactionSearch');
    if (transSearchInput) {
        transSearchInput.addEventListener('input', applyTransactionFilters);
    }

    function applyTransactionFilters() {
        const searchTerm = document.getElementById('transactionSearch').value.toLowerCase();
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

        // Sort to put pending transactions at the top
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

    // --- NOTIFICATION SYSTEM ---
    function updateNotificationsPanel() {
        const today = new Date();
        const items = [];

        const addItem = (title, subtitle, iconClass, toneClass, sortDate) => {
            items.push({ title, subtitle, iconClass, toneClass, sortDate });
        };

        allApplicantsData.forEach(app => {
            const businessName = app.basic_profile?.registered_business_name || 'Unknown Business';
            const status = String(app.status || '').toLowerCase();
            const rawDate = app.date_approved || app.created_at || app.date_submitted || '';
            const sortDate = new Date(rawDate).getTime() || 0;

            if (status === 'paid') {
                addItem(
                    'Payment processed',
                    `${businessName} was marked as paid`,
                    'fa-check-circle',
                    'text-success',
                    sortDate
                );
            } else if (status === 'approved') {
                addItem(
                    'Applicant awaiting payment',
                    `${businessName} is approved and waiting for treasurer action`,
                    'fa-clock',
                    'text-warning',
                    sortDate
                );
            }
        });

        allTransactionsData.forEach(txn => {
            const businessName = txn.applicant?.basic_profile?.registered_business_name || txn.basic_profile?.registered_business_name || 'Unknown Business';
            const status = String(txn.status || '').toLowerCase();
            const rawDate = txn.updated_at || txn.date_approved || txn.created_at || '';
            const sortDate = new Date(rawDate).getTime() || 0;

            if (status === 'failed' || status === 'cancelled') {
                addItem(
                    status === 'failed' ? 'Payment failed' : 'Payment cancelled',
                    `${businessName} has a ${status} payment record`,
                    status === 'failed' ? 'fa-times-circle' : 'fa-ban',
                    'text-danger',
                    sortDate
                );
            } else if (status === 'paid') {
                addItem(
                    'Payment recorded',
                    `${businessName} payment is now complete`,
                    'fa-receipt',
                    'text-success',
                    sortDate
                );
            }
        });

        allMembersData.forEach(member => {
            const endDateString = member.membership_end_date || (member.created_at ? new Date(new Date(member.created_at).setFullYear(new Date(member.created_at).getFullYear() + 1)).toISOString() : null);

            if (!endDateString) return;

            const expDate = new Date(endDateString);
            const diffTime = expDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays <= 30) {
                const name = member.applicant?.basic_profile?.registered_business_name || 'Unknown Business';
                if (diffDays < 0) {
                    addItem('Membership expired', `${name} expired ${Math.abs(diffDays)} days ago`, 'fa-times-circle', 'text-danger', expDate.getTime());
                } else if (diffDays === 0) {
                    addItem('Membership expires today', `${name} expires today`, 'fa-exclamation-circle', 'text-danger', expDate.getTime());
                } else {
                    addItem('Membership expiring soon', `${name} expires in ${diffDays} days`, 'fa-exclamation-triangle', 'text-warning', expDate.getTime());
                }
            }
        });

        items.sort((a, b) => b.sortDate - a.sortDate);

        const notifBody = document.getElementById('notifBody');
        const notifBadge = document.getElementById('notifBadge');
        const redDot = document.querySelector('.fa-bell').nextElementSibling;

        if (items.length > 0) {
            notifBody.innerHTML = items.slice(0, 6).map(item => `
                <div class="notif-item" style="background: #f9fafb;">
                    <div class="notif-icon" style="background: white; border: 1px solid #ddd;"><i class="fa ${item.iconClass} ${item.toneClass} fs-5"></i></div>
                    <div class="notif-text-content">
                        <p class="fw-bold mb-1 text-dark" style="font-size: 13px;">${item.title}</p>
                        <small>${item.subtitle}</small>
                    </div>
                </div>
            `).join('');
            notifBadge.innerText = `${items.length} New`;
            if (redDot) redDot.style.display = 'block';
        } else {
            notifBody.innerHTML = `
                <div class="notif-item" style="background: #f9fafb;">
                    <div class="notif-icon" style="background: white; border: 1px solid #ddd;"><i class="fa fa-check-circle text-success fs-5"></i></div>
                    <div class="notif-text-content"><p class="text-dark">No live notifications right now.</p><small>You're all caught up!</small></div>
                </div>
            `;
            notifBadge.innerText = `0 New`;
            if (redDot) redDot.style.display = 'none';
        }
    }

    function checkAuth(res) {
        if (res.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/login';
            return false;
        }
        return true;
    }

    // Modals
    function openSimpleProof(url) {
        if (!url || url === '#' || url === 'null') { alert("No proof found."); return; }
        const img = document.getElementById('simpleModalImage');
        document.getElementById('simpleModalSpinner').style.display = 'flex';
        img.style.display = 'none';
        img.src = url.startsWith('http') ? url : `https://pcci-laravel-api.onrender.com/${url.replace(/^\/+/, '')}`;
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
        img.src = url.startsWith('http') ? url : `https://pcci-laravel-api.onrender.com/${url.replace(/^\/+/, '')}`;
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

    async function confirmProcessing() {
        const data = membershipTypes.find(m => m.id == currentSelectedType);
        if (!data || !currentApplicantId) return;

        try {
            // Use local proxy route which forwards to remote API with admin token
            const response = await fetch(`/treasurer/process-payment/${currentApplicantId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify({
                    membership_type_id: currentSelectedType,
                    membership_type: 'Regular'
                })
            });

            if (response.ok || response.status === 200 || response.status === 201) {
                hideProofModal();

                // Tell the Admin Members page to refresh so the new member appears there.
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
                    alert("Access denied. Your account may not have permission to process payments. Please contact the administrator to grant treasurer access to the applicants endpoint.");
                } else if (response.status === 422 && result.errors) {
                    let errorMessages = "Validation Failed:\n\n";
                    for (let field in result.errors) {
                        errorMessages += `- ${field}: ${result.errors[field].join(', ')}\n`;
                    }
                    alert(errorMessages);
                } else {
                    alert(`Error: ${result.message || 'Something went wrong. Please try again.'}`);
                }
            }
        } catch (err) { alert("Network error: Could not reach the server."); }
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

    // --- ADD PAYMENT MODAL LOGIC ---
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

        const updatedBusinessName = document.getElementById('transactionMemberInput').value || 'Unknown';
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

    document.addEventListener('DOMContentLoaded', () => {
        if (!token) { window.location.href = '/login'; return; }
        
        // Settings Name population
        const storedName = localStorage.getItem('userName') || 'Jesus Versula';
        document.getElementById('sidebarName').innerText = storedName;
        const nameInput = document.getElementById('settingsLastName');
        if (nameInput) {
             const parts = storedName.split(' ');
             if(parts.length > 1) {
                 document.getElementById('settingsFirstName').value = parts[0];
                 document.getElementById('settingsLastName').value = parts.slice(1).join(' ');
             }
        }
        
        fetchApplicants();
        fetchMembers();
        fetchRecentPayments();
        fetchTransactions();
        initCharts(); 

        document.getElementById('memberSearch').addEventListener('input', applyMemberFilters);
        document.getElementById('memberSort').addEventListener('change', applyMemberFilters);
        
        document.getElementById('applicantSearch').addEventListener('input', applyApplicantFilters);
        document.getElementById('applicantSort').addEventListener('change', applyApplicantFilters);

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

        // 🌟 THIS IS WHERE IT BELONGS: Check memory and switch tab immediately on load
        const savedTab = localStorage.getItem('activeTab') || 'dashboard';
        switchTab(savedTab, false);
    });

    // --- MEMBER FILTER/SORT ---
    function applyMemberFilters() {
        const term = document.getElementById('memberSearch').value.toLowerCase();
        const sortVal = document.getElementById('memberSort').value;

        filteredMembersData = allMembersData.filter(m => {
            const name = (m.applicant?.basic_profile?.registered_business_name || '').toLowerCase();
            return name.includes(term);
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

        currentMemberPage = 1; 
        displayMembersPage();
    }

    // --- APPLICANT FILTER/SORT ---
    function applyApplicantFilters() {
        const term = document.getElementById('applicantSearch').value.toLowerCase();
        const sortVal = document.getElementById('applicantSort').value;

        filteredApplicantsData = allApplicantsData.filter(a => {
            const name = (a.basic_profile?.registered_business_name || '').toLowerCase();
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

        currentApplicantPage = 1; 
        displayApplicantsPage();
    }


    // API Fetches
    async function fetchApplicants() {
        try {
            const [res1, res2] = await Promise.all([
                fetch(`${window.API_BASE_URL}/v1/applicants?status=approved`, { headers: { 'Authorization': `Bearer ${token}` } }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=paid`, { headers: { 'Authorization': `Bearer ${token}` } })
            ]);

            let combinedData = [];

            if (res1.ok) {
                const data1 = await res1.json();
                if (data1.data) combinedData = combinedData.concat(data1.data);
            }
            if (res2.ok) {
                const data2 = await res2.json();
                if (data2.data) combinedData = combinedData.concat(data2.data);
            }

            allApplicantsData = combinedData;

            const pendingCount = allApplicantsData.filter(a => String(a.status).toLowerCase() !== 'paid').length;
            document.getElementById('report-pending-count').innerText = pendingCount;
            document.getElementById('report-pending-count-badge').innerText = `${pendingCount} Pending`;

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
        tbody.innerHTML = '';
        
        if(pageData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted fw-bold">No applicants found.</td></tr>`;
        }

        pageData.forEach(app => {
            const profile = app.basic_profile || {};
            const isPaid = String(app.status).toLowerCase() === 'paid';
            
            let typeLabelHTML = isPaid ? `<span id="type-label-${app.id}" class="text-success fw-bold">PAID</span>` : `<span id="type-label-${app.id}" class="text-danger fw-bold">PENDING</span>`;
            let statusBadge = isPaid ? `<span id="status-badge-${app.id}" class="badge bg-success text-white px-2 py-1 rounded-pill fw-bold shadow-sm" style="font-size:10px;"><i class="fa fa-check-double me-1"></i> PAID</span>` : `<span id="status-badge-${app.id}" class="badge bg-warning text-dark px-2 py-1 rounded-pill fw-bold" style="font-size:10px;">APPROVED</span>`;
            let amountText = isPaid ? '₱ Processed' : '---';
            
            let actionButton = isPaid 
                ? `<button class="action-btn btn-gray" disabled style="width: 130px;"><i class="fa fa-check"></i> Processed</button>`
                : `<button onclick="openProof('${app.proof_of_payment_url}', ${app.id})" class="action-btn btn-green" style="width: 130px;"><i class="fa fa-image me-1"></i> Process Payment</button>`;

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
        document.getElementById('applicant-pagination-text').innerText = `Page ${currentApplicantPage} of ${totalPages}`;
    }

    function prevApplicantPage() { if (currentApplicantPage > 1) { currentApplicantPage--; displayApplicantsPage(); } }
    function nextApplicantPage() { if (currentApplicantPage < Math.ceil(filteredApplicantsData.length / applicantsPerPage)) { currentApplicantPage++; displayApplicantsPage(); } }


    // 🌟 HERE IS THE MEMBER FETCH YOU ASKED FOR
    async function fetchMembers() {
        try {
            const response = await fetch('https://pcci-laravel-api.onrender.com/api/v1/members', { headers: { 'Authorization': `Bearer ${token}` } });
            if (!checkAuth(response)) return;
            const data = await response.json();
            if (response.ok && data.data) {
                allMembersData = data.data; 
                
                const totalMembersBadge = document.getElementById('total-members-badge');
                if (totalMembersBadge) totalMembersBadge.innerText = `${allMembersData.length} Active`;
                
                const reportActive = document.getElementById('report-active-members');
                if (reportActive) reportActive.innerText = allMembersData.length;
                
                applyMemberFilters(); 
                updateNotificationsPanel();
                updateReportsDashboard();
            }
        } catch (err) {
            console.error("Failed to fetch members:", err);
            const tbody = document.getElementById('members-table-body');
            if(tbody) tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 text-danger fw-bold">Network error. Failed to load data.</td></tr>`;
        }
    }

    function displayMembersPage() {
        const totalPages = Math.ceil(filteredMembersData.length / membersPerPage) || 1;
        if (currentMemberPage > totalPages) currentMemberPage = totalPages;
        if (currentMemberPage < 1) currentMemberPage = 1;
        
        const pageData = filteredMembersData.slice((currentMemberPage - 1) * membersPerPage, currentMemberPage * membersPerPage);
        
        const tbody = document.getElementById('members-table-body');
        tbody.innerHTML = '';
        
        if(pageData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 text-muted fw-bold">No members found matching your search.</td></tr>`;
        }

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
        document.getElementById('member-pagination-text').innerText = `Page ${currentMemberPage} of ${totalPages}`;
    }

    function prevMemberPage() { if (currentMemberPage > 1) { currentMemberPage--; displayMembersPage(); } }
    function nextMemberPage() { if (currentMemberPage < Math.ceil(filteredMembersData.length / membersPerPage)) { currentMemberPage++; displayMembersPage(); } }

    function verifyRecentPayment(proofUrl, applicantId) {
        openProof(proofUrl, applicantId);
    }

    function rejectRecentPayment(applicantId) {
        const row = document.getElementById(`recent-payment-row-${applicantId}`);
        if (row) row.remove();

        const tbody = document.getElementById('recent-payments-table-body');
        if (tbody && tbody.children.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No recent payments available.</td></tr>`;
        }
    }

    function printRecentReceipt(applicantId) {
        window.print();
    }

    async function fetchRecentPayments() {
        try {
            const response = await fetch('https://pcci-laravel-api.onrender.com/api/v1/applicants?status=approved', { headers: { 'Authorization': `Bearer ${token}` } });
            if (!checkAuth(response)) return;
            const data = await response.json();
            if (response.ok && data.data) {
                const tbody = document.getElementById('recent-payments-table-body');
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

                if ((data.data || []).length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No recent payments available.</td></tr>`;
                }
            }
        } catch (err) {}
    }

    async function fetchTransactions() {
        try {
            const [paidRes, approvedRes, failedRes, cancelledRes] = await Promise.all([
                fetch(`${window.API_BASE_URL}/v1/applicants?status=paid`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=approved`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=failed`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                }),
                fetch(`${window.API_BASE_URL}/v1/applicants?status=cancelled`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                })
            ]);

            if (!checkAuth(paidRes) || !checkAuth(approvedRes) || !checkAuth(failedRes) || !checkAuth(cancelledRes)) return;

            const paidData = paidRes.ok ? await paidRes.json() : { data: [] };
            const approvedData = approvedRes.ok ? await approvedRes.json() : { data: [] };
            const failedData = failedRes.ok ? await failedRes.json() : { data: [] };
            const cancelledData = cancelledRes.ok ? await cancelledRes.json() : { data: [] };

            const paidRows = (paidData.data || []).map(app => ({ ...app, status: 'paid' }));
            const pendingRows = (approvedData.data || []).map(app => ({ ...app, status: 'pending' }));
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
            updateNotificationsPanel();
            updateReportsDashboard();
        }
    }

    // --- CHARTS (DASHBOARD & REPORTS) ---
    function initCharts() {
        // Charts are rendered from live aggregates inside updateReportsDashboard().
        updateReportsDashboard();
    }

    function refreshTabData(tabName) {
        if (!token) return;

        if (tabName === 'dashboard') {
            fetchApplicants();
            fetchMembers();
            fetchRecentPayments();
            fetchTransactions();
            initCharts();
            return;
        }

        if (tabName === 'members') {
            fetchMembers();
            return;
        }

        if (tabName === 'applicants') {
            fetchApplicants();
            fetchRecentPayments();
            return;
        }

        if (tabName === 'transactions') {
            fetchTransactions();
            return;
        }

        if (tabName === 'reports') {
            fetchApplicants();
            fetchMembers();
            fetchTransactions();
            initCharts();
        }
    }

    // Mobile Sidebar Toggle
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.toggle('active');
        }
    }

    // Close sidebar when clicking on a menu item (mobile)
    document.querySelectorAll('.sidebar-menu li').forEach(item => {
        item.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                document.querySelector('.sidebar').classList.remove('active');
            }
        });
    });

    // UI Tab Switcher
    // Track current active tab
    let currentActiveTab = 'dashboard';

    function switchTab(tabName, shouldReload = true) {
        currentActiveTab = tabName;
        localStorage.setItem('activeTab', tabName);

        // For user tab clicks: always hard refresh (same tab or different tab)
        // to guarantee fresh data and avoid being blocked by in-page refresh errors.
        if (shouldReload) {
            window.location.href = `${window.location.pathname}?refresh=${Date.now()}#${tabName}`;
            return;
        }

        // Initial page load path (shouldReload=false): render tab and refresh data in-place.
        document.querySelectorAll('.content-section').forEach(s => s.style.display = 'none');
        document.querySelectorAll('.sidebar-menu li').forEach(li => li.classList.remove('active'));
        document.getElementById('section-' + tabName).style.display = 'block';
        document.getElementById('nav-' + tabName).classList.add('active');
        
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
