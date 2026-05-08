@extends('layouts.admin')

@section('title', 'Admin Settings - PCCI')

@section('content')
@include('partials.api-config')

<style>
    /* Notice we removed the local color variables here so it respects the global layout! */
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

    .form-control:read-only {
        background-color: rgba(0, 0, 0, 0.04) !important;
        cursor: not-allowed;
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

<div class="settings-card">
    <h4 style="color: var(--pcci-red, #be1e38); margin-bottom: 20px;"><i class="bi bi-bank"></i> PCCI Bank Account</h4>
    <p class="text-muted" style="opacity: 0.8;">Manage the official PCCI receiving bank account. These details are displayed to applicants during Step 6 of Registration.</p>

    <form id="bankDetailsForm" onsubmit="saveBankDetails(event)">
        <input type="hidden" id="channelId">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Bank Name</label>
                <input type="text" class="form-control" id="bankName" placeholder="e.g. ChinaBank, BDO Unibank" readonly required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Account Name</label>
                <input type="text" class="form-control" id="accountName" placeholder="e.g. PCCI Valenzuela" readonly required>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Account Number</label>
                <input type="text" class="form-control" id="accountNumber" placeholder="0000-0000-0000" readonly required>
            </div>
        </div>

        <div class="d-flex gap-2 mt-2">
            <button type="button" class="btn btn-secondary" id="editBtn" onclick="enableEditMode()">
                <i class="bi bi-pencil-square"></i> Edit Details
            </button>
            <button type="submit" class="btn-save" id="saveBtn" style="display: none;">
                <i class="bi bi-save"></i> Save Changes
            </button>
            <button type="button" class="btn btn-outline-secondary" id="cancelBtn" onclick="cancelEditMode()" style="display: none;">
                Cancel
            </button>
        </div>

        <div id="bankAlertMsg" class="alert mt-3" style="display: none; padding: 10px; border-radius: 8px;"></div>
    </form>
</div>

<script>
    var token = localStorage.getItem('token');
    let defaultChannelData = null;

    function initSettingsPage() {
        // Just fetch the channels, the layout handles the initial theme load now!
        fetchPaymentChannels();
    }

    if (document.readyState !== 'loading') {
        initSettingsPage();
    } else {
        document.addEventListener('DOMContentLoaded', initSettingsPage);
    }

    // --- NEW GLOBAL THEME TOGGLE --- //
    function setTheme(mode) {
        if (mode === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-theme');
        }
        // Save the choice so it remembers when you refresh!
        localStorage.setItem('admin-theme', mode);
    }

    // --- BANK DETAILS API LOGIC --- //
    async function fetchPaymentChannels() {
        try {
            const response = await fetch(`${window.API_BASE_URL}/v1/payment-channels`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) throw new Error('Failed to fetch data');

            const result = await response.json();
            const channels = result.data || result || [];

            if (channels.length > 0) {
                defaultChannelData = channels[0];
                document.getElementById('channelId').value = defaultChannelData.id;
                document.getElementById('bankName').value = defaultChannelData.payment_method || '';
                document.getElementById('accountName').value = defaultChannelData.account_name || '';
                document.getElementById('accountNumber').value = defaultChannelData.account_no || '';
            } else {
                defaultChannelData = null;
                document.getElementById('channelId').value = '';
                document.getElementById('bankName').value = '';
                document.getElementById('accountName').value = '';
                document.getElementById('accountNumber').value = '';
            }

            cancelEditMode();
        } catch (error) {
            console.error('Error fetching payment channels:', error);
            showAlert('danger', 'Failed to load bank accounts. Check network connection.');
        }
    }

    function enableEditMode() {
        document.getElementById('bankName').removeAttribute('readonly');
        document.getElementById('accountName').removeAttribute('readonly');
        document.getElementById('accountNumber').removeAttribute('readonly');

        document.getElementById('editBtn').style.display = 'none';
        document.getElementById('saveBtn').style.display = 'inline-block';
        document.getElementById('cancelBtn').style.display = 'inline-block';
    }

    function cancelEditMode() {
        document.getElementById('bankName').setAttribute('readonly', true);
        document.getElementById('accountName').setAttribute('readonly', true);
        document.getElementById('accountNumber').setAttribute('readonly', true);

        document.getElementById('editBtn').style.display = 'inline-block';
        document.getElementById('saveBtn').style.display = 'none';
        document.getElementById('cancelBtn').style.display = 'none';

        if (defaultChannelData) {
            document.getElementById('bankName').value = defaultChannelData.payment_method || '';
            document.getElementById('accountName').value = defaultChannelData.account_name || '';
            document.getElementById('accountNumber').value = defaultChannelData.account_no || '';
        } else {
            document.getElementById('bankName').value = '';
            document.getElementById('accountName').value = '';
            document.getElementById('accountNumber').value = '';
        }
    }

    async function saveBankDetails(event) {
        event.preventDefault();

        const id = document.getElementById('channelId').value;
        const saveBtn = document.getElementById('saveBtn');

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Saving...';

        const bankNameValue = document.getElementById('bankName').value;

        const updatedData = {
            payment_method: bankNameValue,
            account_name: document.getElementById('accountName').value,
            account_no: document.getElementById('accountNumber').value,
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

            if (response.ok) {
                showAlert('success', '<i class="bi bi-check-circle-fill"></i> Bank details saved successfully!');
                await fetchPaymentChannels();
            } else {
                const errData = await response.json();

                let errorHtml = '<b>Save failed! The backend requires these fields:</b><ul style="margin-bottom:0;">';
                if (errData.errors) {
                    for (const [field, messages] of Object.entries(errData.errors)) {
                        errorHtml += `<li><b>${field}</b>: ${messages.join(', ')}</li>`;
                    }
                    errorHtml += '</ul>';
                } else {
                    errorHtml = `<i class="bi bi-exclamation-triangle-fill"></i> Save failed: ${errData.message || 'Unknown error'}`;
                }

                showAlert('danger', errorHtml);
            }
        } catch (error) {
            console.error('Save error:', error);
            showAlert('danger', '<i class="bi bi-exclamation-triangle-fill"></i> Network error. Please try again later.');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-save"></i> Save Changes';
        }
    }

    function showAlert(type, htmlMessage) {
        const alertBox = document.getElementById('bankAlertMsg');
        alertBox.className = `alert alert-${type} mt-3`;
        alertBox.innerHTML = htmlMessage;
        alertBox.style.display = 'block';

        if (type === 'success') {
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 5000);
        }
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