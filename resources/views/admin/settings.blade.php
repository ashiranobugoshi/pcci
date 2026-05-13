@extends('layouts.admin')

@section('title', 'Admin Settings - PCCI')

@section('content')
@include('partials.api-config')

<style>
    .page-header {
        background-color: var(--pcci-red, #be1e38);
        color: #fff;
        padding: 36px 40px;
        border-radius: 10px;
        font-size: 2rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 30px;
        letter-spacing: 1px;
    }

    .settings-card {
        background: var(--bg-card, #ffffff);
        color: var(--text-main, #333);
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        border: 1px solid var(--border-color, #e0e0e0);
        transition: background 0.3s, color 0.3s, border-color 0.3s;
    }

    .btn-save {
        background: var(--pcci-red, #be1e38);
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-save:hover {
        background: #a01a30;
    }

    .payment-channels-card {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border-color, #e0e0e0);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .payment-channels-table th,
    .payment-channels-table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .payment-channels-table tbody tr.selected {
        background-color: rgba(190, 30, 56, 0.08);
    }

    .payment-channels-badge {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
</style>

<div class="page-header">Settings</div>

<div class="settings-card">
    <h4 style="color: var(--pcci-red, #be1e38); margin-bottom: 20px;"><i class="bi bi-palette"></i> Appearance</h4>
    <p class="text-muted" style="opacity: 0.8;">Personalize your admin interface mode. This will apply to the entire admin panel.</p>
    <div class="d-flex gap-3">
        <button class="btn btn-outline-secondary" onclick="setTheme('light')">
            <i class="bi bi-sun"></i> Light Mode
        </button>
        <button class="btn btn-dark" onclick="setTheme('dark')">
            <i class="bi bi-moon-stars"></i> Dark Mode
        </button>
    </div>
</div>

<div class="settings-card payment-channels-card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-3">
        <div>
            <h4 style="color: var(--pcci-red, #be1e38); margin-bottom: 10px;"><i class="bi bi-bank"></i> Payment Channels</h4>
            <p class="text-muted" style="opacity: 0.8; max-width: 760px;">Create, edit, activate, and remove payment channels used during registration. Select which channel should be active so applicants do not have to retype it.</p>
        </div>
        <button type="button" class="btn btn-outline-primary fw-bold" onclick="startNewChannel()">
            <i class="bi bi-plus-lg"></i> Add New Channel
        </button>
    </div>

    <div id="globalBankAlert" class="alert mt-2" style="display: none; border-radius: 8px;"></div>

    <div class="table-responsive mb-4">
        <table class="table table-hover payment-channels-table">
            <thead>
                <tr>
                    <th scope="col">Status</th>
                    <th scope="col">Channel Name</th>
                    <th scope="col">Account Name</th>
                    <th scope="col">Account Number</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="paymentChannelsTableBody">
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Loading payment channels...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="noPaymentChannelsMessage" class="alert alert-info" style="display: none;">No payment channels are defined yet. Click "Add New Channel" to create one.</div>
</div>

<div class="modal fade" id="channelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-top: 4px solid var(--pcci-red, #be1e38);">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold text-dark" id="channelModalTitle"><i class="bi bi-bank me-2"></i>Channel Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="bankDetailsForm" onsubmit="saveBankDetails(event)">
                <div class="modal-body">
                    <input type="hidden" id="channelId">

                    <div id="modalBankAlertMsg" class="alert mt-1" style="display: none; padding: 10px; border-radius: 8px;"></div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Method</label>
                        <input type="text" class="form-control" id="bankName" placeholder="e.g. ChinaBank, BDO Unibank, GCash" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Account Name</label>
                        <input type="text" class="form-control" id="accountName" placeholder="e.g. PCCI Valenzuela" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Account Number</label>
                        <input type="text" class="form-control" id="accountNumber" placeholder="0000-0000-0000" required>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn rounded-pill px-4" id="saveBtn" style="background: var(--pcci-red, #be1e38); color: white; font-weight: bold;">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var token = localStorage.getItem('token');
    let paymentChannels = [];
    let selectedChannelId = null;
    let currentActiveChannelId = null;
    let channelModalInstance = null;

    function initSettingsPage() {
        fetchPaymentChannels();
    }

    if (document.readyState !== 'loading') {
        initSettingsPage();
    } else {
        document.addEventListener('DOMContentLoaded', initSettingsPage);
    }

    function setTheme(mode) {
        if (mode === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-theme');
        }
        localStorage.setItem('admin-theme', mode);
    }

    function openModal() {
        const el = document.getElementById('channelModal');
        channelModalInstance = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
        channelModalInstance.show();
    }

    function closeModal() {
        if (channelModalInstance) channelModalInstance.hide();
    }

    // --- FETCH CHANNELS --- //
    async function fetchPaymentChannels() {
        if (!token) return;

        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/payment-channels?all=true`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) throw new Error('Failed to fetch data');

            const result = await response.json();
            paymentChannels = Array.isArray(result.data) ? result.data : (Array.isArray(result) ? result : []);

            if (paymentChannels.length > 0) {
                const activeChannel = paymentChannels.find(c => c.is_active) || paymentChannels[0];
                currentActiveChannelId = activeChannel.id;

                if (selectedChannelId) selectedChannelId = Number(selectedChannelId);

                if (!selectedChannelId || !paymentChannels.find(c => Number(c.id) === selectedChannelId)) {
                    selectedChannelId = Number(activeChannel.id);
                }
            } else {
                currentActiveChannelId = null;
                selectedChannelId = null;
            }

            renderPaymentChannelsList();
        } catch (error) {
            console.error('Error fetching payment channels:', error);
            showGlobalAlert('danger', 'Failed to load payment channels. Check network connection.');
        }
    }

    function renderPaymentChannelsList() {
        const tbody = document.getElementById('paymentChannelsTableBody');
        const noMessage = document.getElementById('noPaymentChannelsMessage');

        if (!tbody || !noMessage) return;

        if (paymentChannels.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No channels available.</td></tr>';
            noMessage.style.display = 'block';
            return;
        }

        noMessage.style.display = 'none';
        tbody.innerHTML = paymentChannels.map(channel => {
            const isActive = channel.is_active;
            const activeLabel = isActive ?
                '<span class="badge bg-success payment-channels-badge px-3 py-2">ACTIVE</span>' :
                '<span class="badge bg-secondary payment-channels-badge px-3 py-2">INACTIVE</span>';

            const rowClass = Number(channel.id) === Number(selectedChannelId) ? 'selected' : '';

            let actionBtns = `<button type="button" class="btn btn-sm btn-outline-primary me-2 fw-bold" onclick="selectPaymentChannel(${channel.id})"><i class="bi bi-pencil-square"></i> Edit</button>`;

            if (isActive) {
                actionBtns += `<button type="button" class="btn btn-sm btn-success fw-bold disabled"><i class="bi bi-check-circle"></i> Default</button>`;
            } else {
                actionBtns += `<button type="button" class="btn btn-sm btn-outline-success me-2 fw-bold" onclick="setActiveChannel(${channel.id})">Set Active</button>`;
                actionBtns += `<button type="button" class="btn btn-sm btn-outline-danger fw-bold" onclick="deletePaymentChannel(${channel.id})"><i class="bi bi-trash"></i> Delete</button>`;
            }

            return `
                <tr class="${rowClass}">
                    <td>${activeLabel}</td>
                    <td class="fw-bold text-dark">${channel.payment_method || 'N/A'}</td>
                    <td>${channel.account_name || 'N/A'}</td>
                    <td class="font-monospace text-primary fw-bold">${channel.account_no || 'N/A'}</td>
                    <td class="text-end">${actionBtns}</td>
                </tr>
            `;
        }).join('');
    }

    // --- MODAL TRIGGERS --- //
    function startNewChannel() {
        selectedChannelId = null;
        document.getElementById('channelId').value = '';
        document.getElementById('bankName').value = '';
        document.getElementById('accountName').value = '';
        document.getElementById('accountNumber').value = '';
        document.getElementById('modalBankAlertMsg').style.display = 'none';

        document.getElementById('channelModalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add New Channel';
        openModal();
    }

    function selectPaymentChannel(id) {
        const channel = paymentChannels.find(c => Number(c.id) === Number(id));
        if (!channel) return;
        selectedChannelId = Number(channel.id);

        document.getElementById('channelId').value = channel.id;
        document.getElementById('bankName').value = channel.payment_method || '';
        document.getElementById('accountName').value = channel.account_name || '';
        document.getElementById('accountNumber').value = channel.account_no || '';
        document.getElementById('modalBankAlertMsg').style.display = 'none';

        document.getElementById('channelModalTitle').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Payment Channel';
        openModal();
    }

    // --- CHANNEL ACTIONS --- //
    async function setActiveChannel(id) {
        const channel = paymentChannels.find(c => Number(c.id) === Number(id));
        if (!channel) return;

        try {
            await Promise.all(paymentChannels.map(async otherChannel => {
                if (Number(otherChannel.id) !== Number(id) && otherChannel.is_active) {
                    await fetch(`${window.API_BASE_URL}/v1/payment-channels/${otherChannel.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        },
                        body: JSON.stringify({
                            is_active: false
                        })
                    });
                }
            }));

            const response = await fetch(`${window.API_BASE_URL}/v1/payment-channels/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    is_active: true
                })
            });

            if (!response.ok) throw new Error('Toggle failed');

            await fetchPaymentChannels();
            showGlobalAlert('success', `<i class="bi bi-check-circle-fill"></i> ${channel.payment_method} is now the active default channel!`);
        } catch (error) {
            console.error('Error setting active channel:', error);
            showGlobalAlert('danger', '<i class="bi bi-exclamation-triangle-fill"></i> Failed to update active channel.');
        }
    }

    async function deletePaymentChannel(id) {
        if (!confirm('Delete this payment channel? This cannot be undone.')) return;

        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/payment-channels/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) throw new Error('Delete failed');

            if (Number(selectedChannelId) === Number(id)) {
                selectedChannelId = null;
            }

            await fetchPaymentChannels();
            showGlobalAlert('success', '<i class="bi bi-check-circle-fill"></i> Payment channel deleted.');
        } catch (error) {
            console.error('Delete error:', error);
            showGlobalAlert('danger', '<i class="bi bi-exclamation-triangle-fill"></i> Failed to delete payment channel.');
        }
    }

    // --- SAVE LOGIC --- //
    async function saveBankDetails(event) {
        event.preventDefault();

        const id = document.getElementById('channelId').value;
        const saveBtn = document.getElementById('saveBtn');

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Saving...';
        document.getElementById('modalBankAlertMsg').style.display = 'none';

        const updatedData = {
            payment_method: document.getElementById('bankName').value,
            account_name: document.getElementById('accountName').value,
            account_no: document.getElementById('accountNumber').value,
            is_active: Boolean((!id && paymentChannels.length === 0) || (id && Number(currentActiveChannelId) === Number(id)))
        };

        const method = id ? 'PUT' : 'POST';
        const url = id ? `${window.API_BASE_URL}/v1/payment-channels/${id}` : `${window.API_BASE_URL}/v1/payment-channels`;

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(updatedData)
            });

            const result = await response.json();

            if (response.ok) {
                closeModal();
                showGlobalAlert('success', '<i class="bi bi-check-circle-fill"></i> Bank details saved successfully!');

                if (!id && result.data && result.data.id) {
                    selectedChannelId = Number(result.data.id);
                }

                await fetchPaymentChannels();
            } else {
                let errorHtml = '<b>Save failed! The backend requires these fields:</b><ul style="margin-bottom:0;">';
                if (result.errors) {
                    for (const [field, messages] of Object.entries(result.errors)) {
                        errorHtml += `<li><b>${field}</b>: ${messages.join(', ')}</li>`;
                    }
                    errorHtml += '</ul>';
                } else {
                    errorHtml = `<i class="bi bi-exclamation-triangle-fill"></i> Save failed: ${result.message || 'Unknown error'}`;
                }
                showModalAlert('danger', errorHtml);
            }
        } catch (error) {
            console.error('Save error:', error);
            showModalAlert('danger', '<i class="bi bi-exclamation-triangle-fill"></i> Network error. Please try again later.');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-save"></i> Save Changes';
        }
    }

    // --- ALERTS --- //
    function showGlobalAlert(type, htmlMessage) {
        const alertBox = document.getElementById('globalBankAlert');
        alertBox.className = `alert alert-${type} mt-2`;
        alertBox.innerHTML = htmlMessage;
        alertBox.style.display = 'block';

        if (type === 'success') {
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 4000);
        }
    }

    function showModalAlert(type, htmlMessage) {
        const alertBox = document.getElementById('modalBankAlertMsg');
        alertBox.className = `alert alert-${type} mt-1`;
        alertBox.innerHTML = htmlMessage;
        alertBox.style.display = 'block';
    }

    if (typeof fetchPaymentChannels === 'function') {
        fetchPaymentChannels();
    }
</script>

<style>
    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }

    .spin {
        display: inline-block;
        animation: spin 1s linear infinite;
    }
</style>
@endsection