{{-- resources/views/finance/debt/partials/overdue-invoices-table.blade.php --}}

<style>
.modern-table-container {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,.05);
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    min-width: 900px;
}

.modern-table thead th {
    background: #f8fafc;
    color: #1e293b;
    font-weight: 700;
    font-size: .78rem;
    text-transform: uppercase;
    padding: 16px 12px;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}

.modern-table tbody td {
    padding: 16px 12px;
    vertical-align: middle;
    font-size: .9rem;
    border-bottom: 1px solid #f1f5f9;
}

.modern-table tbody tr:hover {
    background: #f8fafc;
}

.summary-card {
    background: #fff;
    border-radius: 20px;
    padding: 1.25rem;
    border: 1px solid #eef2ff;
    box-shadow: 0 4px 18px rgba(15,23,42,.05);
}

.summary-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
}

.filter-bar {
    background: #fff;
    border-radius: 16px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border: 1px solid #eef2ff;
    box-shadow: 0 4px 18px rgba(15,23,42,.04);
}

.bulk-actions-bar {
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
    border-radius: 16px;
    padding: .85rem 1.25rem;
    margin-bottom: 1rem;
    display: none;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.invoice-number {
    font-family: Monaco, Menlo, monospace;
    font-size: .8rem;
    font-weight: 700;
    color: #4f46e5;
    background: #eef2ff;
    padding: 5px 10px;
    border-radius: 8px;
}

.customer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    flex-shrink: 0;
}

.amount-usd {
    font-weight: 800;
    color: #dc2626;
}

.amount-ksh {
    font-weight: 800;
    color: #d97706;
}

.badge-critical,
.badge-high,
.badge-medium,
.badge-low {
    color: #fff;
    padding: 6px 12px;
    border-radius: 30px;
    font-size: .75rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.badge-critical { background: linear-gradient(135deg, #dc2626, #991b1b); }
.badge-high { background: linear-gradient(135deg, #f59e0b, #d97706); }
.badge-medium { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.badge-low { background: linear-gradient(135deg, #10b981, #059669); }

.action-btn {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.modern-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #4f46e5;
}

.toast-container-custom {
    position: fixed;
    right: 20px;
    bottom: 20px;
    z-index: 9999;
}

@media (max-width: 768px) {
    .summary-card h2 {
        font-size: 1.25rem;
    }

    .bulk-actions-bar {
        align-items: flex-start;
    }
}

@media print {
    .filter-bar,
    .bulk-actions-bar,
    .action-btn,
    #scrollToTopBtn,
    #exportTableBtn {
        display: none !important;
    }

    .modern-table-container,
    .summary-card {
        box-shadow: none;
    }
}
</style>

@if(isset($error))
    <div class="text-center py-5">
        <div class="bg-danger bg-opacity-10 rounded-3 p-5 d-inline-block">
            <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
            <h5 class="text-danger">Error Loading Data</h5>
            <p class="text-muted">{{ $error }}</p>
        </div>
    </div>

@elseif(isset($overdueBillings) && $overdueBillings->count() > 0)

    @php
        $totalDays = 0;
        $validCount = 0;

        foreach ($overdueBillings as $billing) {
            $dueDate = $billing->due_date instanceof \Carbon\Carbon
                ? $billing->due_date
                : \Carbon\Carbon::parse($billing->due_date);

            if ($dueDate->isPast()) {
                $totalDays += $dueDate->diffInDays(now());
                $validCount++;
            }
        }

        $avgOverdueDays = $validCount > 0 ? round($totalDays / $validCount) : 0;
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="summary-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1 text-uppercase fw-semibold">Total Overdue USD</p>
                        <h2 class="mb-0 fw-bold text-danger" id="totalOverdueUsd">$0</h2>
                        <small class="text-muted">Visible invoices</small>
                    </div>
                    <div class="summary-icon bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="summary-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1 text-uppercase fw-semibold">Total Overdue KSH</p>
                        <h2 class="mb-0 fw-bold text-warning" id="totalOverdueKsh">KSH 0</h2>
                        <small class="text-muted">Visible invoices</small>
                    </div>
                    <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="summary-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1 text-uppercase fw-semibold">Overdue Invoices</p>
                        <h2 class="mb-0 fw-bold text-primary">{{ $overdueBillings->count() }}</h2>
                        <small class="text-muted">Need action</small>
                    </div>
                    <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="summary-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1 text-uppercase fw-semibold">Avg Overdue Days</p>
                        <h2 class="mb-0 fw-bold text-info">{{ $avgOverdueDays }}</h2>
                        <small class="text-muted">Since due date</small>
                    </div>
                    <div class="summary-icon bg-info bg-opacity-10 text-info">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Currency</label>
                <select class="form-select form-select-sm" id="currencyFilter">
                    <option value="all">All Currencies</option>
                    <option value="USD">USD Only</option>
                    <option value="KSH">KSH Only</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Severity</label>
                <select class="form-select form-select-sm" id="severityFilter">
                    <option value="all">All Overdue</option>
                    <option value="critical">Critical 90+ days</option>
                    <option value="high">High 60-89 days</option>
                    <option value="medium">Medium 30-59 days</option>
                    <option value="low">Low 1-29 days</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control" id="customerSearch" placeholder="Search customer, company, invoice...">
                    <button class="btn btn-outline-secondary" id="clearSearchBtn" type="button">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary btn-sm w-100" id="exportTableBtn" type="button">
                    <i class="fas fa-download me-1"></i> Export CSV
                </button>
            </div>
        </div>
    </div>

    <div class="bulk-actions-bar" id="bulkActionsBar">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <i class="fas fa-check-circle text-primary"></i>
            <span class="fw-semibold"><span id="selectedCount">0</span> invoice(s) selected</span>

            <button class="btn btn-sm btn-success" id="bulkSendReminderBtn" type="button">
                <i class="fas fa-envelope me-1"></i> Send Reminders
            </button>

            <button class="btn btn-sm btn-secondary" id="clearSelectionBtn" type="button">
                <i class="fas fa-times me-1"></i> Clear
            </button>
        </div>
    </div>

    <div class="modern-table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th style="width:40px;">
                        <input type="checkbox" id="selectAllCheckbox" class="modern-checkbox">
                    </th>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Overdue Status</th>
                    <th>Payment Status</th>
                    <th style="width:120px;">Actions</th>
                </tr>
            </thead>

            <tbody id="overdueTableBody">
                @foreach($overdueBillings as $billing)
                    @php
                        $dueDate = $billing->due_date instanceof \Carbon\Carbon
                            ? $billing->due_date
                            : \Carbon\Carbon::parse($billing->due_date);

                        $daysOverdue = $dueDate->isPast() ? $dueDate->diffInDays(now()) : 0;

                        if ($daysOverdue >= 90) {
                            $severity = 'critical';
                            $severityBadge = 'badge-critical';
                            $severityIcon = '🔥';
                        } elseif ($daysOverdue >= 60) {
                            $severity = 'high';
                            $severityBadge = 'badge-high';
                            $severityIcon = '🔴';
                        } elseif ($daysOverdue >= 30) {
                            $severity = 'medium';
                            $severityBadge = 'badge-medium';
                            $severityIcon = '🟡';
                        } else {
                            $severity = 'low';
                            $severityBadge = 'badge-low';
                            $severityIcon = '🟢';
                        }

                        $currency = strtoupper($billing->currency ?? 'KSH');
                        $amount = (float) ($billing->total_amount ?? 0);

                        $formattedAmount = $currency === 'USD'
                            ? '$' . number_format($amount, 2)
                            : 'KSH ' . number_format($amount, 2);

                        $amountClass = $currency === 'USD' ? 'amount-usd' : 'amount-ksh';

                        $customer = $billing->user ?? null;
                        $customerName = $customer->name ?? 'Unknown Customer';
                        $companyName = $customer->company_name ?? $customer->company ?? '';
                        $initial = strtoupper(substr($customerName, 0, 1));

                        $avatarColors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
                        $avatarColor = $avatarColors[abs(crc32($customerName)) % count($avatarColors)];

                        $paymentStatus = strtolower($billing->status ?? 'overdue');
                        $statusText = $paymentStatus === 'partial' ? 'Partially Paid' : 'Overdue';
                        $statusIcon = $paymentStatus === 'partial' ? '⏳' : '⚠️';
                        $statusClass = $paymentStatus === 'partial'
                            ? 'bg-warning bg-opacity-10 text-warning'
                            : 'bg-danger bg-opacity-10 text-danger';

                        $billingNumber = $billing->billing_number ?? 'CONS-' . $billing->id;
                    @endphp

                    <tr
                        data-currency="{{ $currency }}"
                        data-severity="{{ $severity }}"
                        data-customer="{{ strtolower($customerName) }}"
                        data-company="{{ strtolower($companyName) }}"
                        data-invoice="{{ strtolower($billingNumber) }}"
                    >
                        <td>
                            <input type="checkbox"
                                   class="invoice-select modern-checkbox"
                                   data-id="{{ $billing->id }}">
                        </td>

                        <td>
                            <span class="invoice-number">{{ $billingNumber }}</span>
                        </td>

                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="customer-avatar" style="background: {{ $avatarColor }};">
                                    {{ $initial }}
                                </div>

                                <div>
                                    <div class="fw-semibold">{{ $customerName }}</div>

                                    @if($companyName)
                                        <small class="text-muted d-block">{{ $companyName }}</small>
                                    @endif

                                    <span class="badge bg-{{ $currency === 'USD' ? 'primary' : 'secondary' }} bg-opacity-10 text-{{ $currency === 'USD' ? 'primary' : 'secondary' }} mt-1">
                                        {{ $currency }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="{{ $amountClass }}">
                            {{ $formattedAmount }}
                        </td>

                        <td class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ $dueDate->format('M d, Y') }}
                        </td>

                        <td>
                            <span class="{{ $severityBadge }}">
                                {{ $severityIcon }} {{ number_format($daysOverdue) }} days overdue
                            </span>
                        </td>

                        <td>
                            <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill">
                                {{ $statusIcon }} {{ $statusText }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-warning action-btn send-reminder-btn"
                                        type="button"
                                        data-id="{{ $billing->id }}"
                                        data-customer="{{ $customerName }}"
                                        data-invoice="{{ $billingNumber }}"
                                        title="Send Reminder">
                                    <i class="fas fa-envelope"></i>
                                </button>

                                <button class="btn btn-sm btn-outline-info action-btn payment-plan-btn"
                                        type="button"
                                        data-id="{{ $billing->id }}"
                                        data-amount="{{ $amount }}"
                                        data-currency="{{ $currency }}"
                                        title="Create Payment Plan">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>

                                @if(Route::has('finance.debt.invoice.details'))
                                    <a href="{{ route('finance.debt.invoice.details', $billing->id) }}"
                                       class="btn btn-sm btn-outline-primary action-btn"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-chart-simple text-muted"></i>
            <span class="text-muted small">
                Showing <span id="visibleCount">{{ $overdueBillings->count() }}</span>
                of {{ $overdueBillings->count() }} invoices
            </span>
        </div>

        <button class="btn btn-sm btn-link text-primary" id="scrollToTopBtn" type="button">
            <i class="fas fa-arrow-up me-1"></i> Back to Top
        </button>
    </div>

    <div class="toast-container-custom" id="toastContainer"></div>

@else
    <div class="text-center py-5">
        <div class="bg-success bg-opacity-10 rounded-3 p-5 d-inline-block">
            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
            <h4 class="text-success">All Clear!</h4>
            <p class="text-muted mb-0">No overdue invoices found. All invoices are paid or up to date.</p>
        </div>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkSendReminderBtn = document.getElementById('bulkSendReminderBtn');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    const currencyFilter = document.getElementById('currencyFilter');
    const severityFilter = document.getElementById('severityFilter');
    const customerSearch = document.getElementById('customerSearch');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const exportTableBtn = document.getElementById('exportTableBtn');
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    const toastContainer = document.getElementById('toastContainer');

    let selectedInvoices = new Set();

    function getVisibleRows() {
        return Array.from(document.querySelectorAll('#overdueTableBody tr'))
            .filter(row => row.style.display !== 'none');
    }

    function getVisibleCheckboxes() {
        return getVisibleRows()
            .map(row => row.querySelector('.invoice-select'))
            .filter(Boolean);
    }

    function showToast(message, type = 'success') {
        if (!toastContainer) return;

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0 mb-2`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.style.minWidth = '280px';

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);

        if (typeof bootstrap !== 'undefined') {
            const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
            bsToast.show();
            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        } else {
            setTimeout(() => toast.remove(), 3000);
        }
    }

    function calculateTotals() {
        let totalUsd = 0;
        let totalKsh = 0;

        getVisibleRows().forEach(row => {
            const currency = row.getAttribute('data-currency');
            const amountText = row.querySelector('td:nth-child(4)')?.innerText || '0';
            const amount = parseFloat(amountText.replace(/[^0-9.-]/g, '')) || 0;

            if (currency === 'USD') totalUsd += amount;
            if (currency === 'KSH') totalKsh += amount;
        });

        const totalOverdueUsd = document.getElementById('totalOverdueUsd');
        const totalOverdueKsh = document.getElementById('totalOverdueKsh');
        const visibleCount = document.getElementById('visibleCount');

        if (totalOverdueUsd) totalOverdueUsd.innerHTML = '$' + totalUsd.toLocaleString();
        if (totalOverdueKsh) totalOverdueKsh.innerHTML = 'KSH ' + totalKsh.toLocaleString();
        if (visibleCount) visibleCount.innerHTML = getVisibleRows().length;
    }

    function updateBulkActions() {
        const count = selectedInvoices.size;

        if (selectedCountSpan) selectedCountSpan.innerHTML = count;
        if (bulkActionsBar) bulkActionsBar.style.display = count > 0 ? 'flex' : 'none';

        const visibleCheckboxes = getVisibleCheckboxes();
        const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => cb.checked);

        if (selectAllCheckbox) selectAllCheckbox.checked = allChecked;
    }

    function selectAllVisible(checked) {
        getVisibleCheckboxes().forEach(cb => {
            cb.checked = checked;

            const id = cb.getAttribute('data-id');

            if (checked) {
                selectedInvoices.add(id);
            } else {
                selectedInvoices.delete(id);
            }
        });

        updateBulkActions();
    }

    function filterTable() {
        const currency = currencyFilter?.value || 'all';
        const severity = severityFilter?.value || 'all';
        const searchTerm = customerSearch?.value.toLowerCase() || '';

        document.querySelectorAll('#overdueTableBody tr').forEach(row => {
            const rowCurrency = row.getAttribute('data-currency');
            const rowSeverity = row.getAttribute('data-severity');
            const customerText = row.getAttribute('data-customer') || '';
            const invoiceText = row.getAttribute('data-invoice') || '';

            let show = true;

            if (currency !== 'all' && rowCurrency !== currency) show = false;
            if (severity !== 'all' && rowSeverity !== severity) show = false;
            if (searchTerm && !customerText.includes(searchTerm) && !invoiceText.includes(searchTerm)) show = false;

            row.style.display = show ? '' : 'none';
        });

        calculateTotals();
        updateBulkActions();
    }

    async function sendReminder(invoiceId, customerName, invoiceNumber) {
        if (!confirm(`Send payment reminder to ${customerName} for invoice ${invoiceNumber}?`)) return;

        const btn = document.querySelector(`.send-reminder-btn[data-id="${invoiceId}"]`);

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        try {
            const response = await fetch(`/finance/debt/send-reminder/${invoiceId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to send reminder');
            }

            showToast(data.message || 'Reminder sent successfully!', 'success');
        } catch (error) {
            showToast(error.message || 'Failed to send reminder', 'danger');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-envelope"></i>';
            }
        }
    }

    async function bulkSendReminders() {
        const ids = Array.from(selectedInvoices);

        if (ids.length === 0) return;
        if (!confirm(`Send reminders to ${ids.length} customer(s)?`)) return;

        if (bulkSendReminderBtn) {
            bulkSendReminderBtn.disabled = true;
            bulkSendReminderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        }

        try {
            const response = await fetch('/finance/debt/bulk-send-reminder', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error sending reminders');
            }

            showToast(`Sent ${data.sent || ids.length} reminder(s) successfully!`, 'success');

            selectedInvoices.clear();
            document.querySelectorAll('.invoice-select').forEach(cb => cb.checked = false);
            updateBulkActions();
        } catch (error) {
            showToast(error.message || 'Error sending reminders', 'danger');
        } finally {
            if (bulkSendReminderBtn) {
                bulkSendReminderBtn.disabled = false;
                bulkSendReminderBtn.innerHTML = '<i class="fas fa-envelope me-1"></i> Send Reminders';
            }
        }
    }

    function exportToCSV() {
        const rows = getVisibleRows();

        if (rows.length === 0) {
            showToast('No data to export', 'warning');
            return;
        }

        let csv = '\uFEFFInvoice #,Customer,Currency,Amount,Due Date,Overdue Days,Status\n';

        rows.forEach(row => {
            const invoice = row.querySelector('td:nth-child(2) .invoice-number')?.innerText?.trim() || '';
            const customer = row.querySelector('td:nth-child(3) .fw-semibold')?.innerText?.trim() || '';
            const currency = row.getAttribute('data-currency') || '';
            const amount = row.querySelector('td:nth-child(4)')?.innerText?.trim() || '';
            const dueDate = row.querySelector('td:nth-child(5)')?.innerText?.trim() || '';
            const overdue = row.querySelector('td:nth-child(6) span')?.innerText?.trim() || '';
            const status = row.querySelector('td:nth-child(7) span')?.innerText?.trim() || '';

            csv += `"${invoice}","${customer}","${currency}","${amount}","${dueDate}","${overdue}","${status}"\n`;
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);

        link.href = url;
        link.setAttribute('download', `overdue_invoices_${new Date().toISOString().slice(0, 19)}.csv`);

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        URL.revokeObjectURL(url);

        showToast('Export completed!', 'success');
    }

    function clearSearch() {
        if (customerSearch) customerSearch.value = '';
        filterTable();
    }

    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', e => selectAllVisible(e.target.checked));
    }

    document.querySelectorAll('.invoice-select').forEach(cb => {
        cb.addEventListener('change', e => {
            e.stopPropagation();

            const id = cb.getAttribute('data-id');

            if (cb.checked) {
                selectedInvoices.add(id);
            } else {
                selectedInvoices.delete(id);
            }

            updateBulkActions();
        });
    });

    document.querySelectorAll('.send-reminder-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();

            sendReminder(
                btn.getAttribute('data-id'),
                btn.getAttribute('data-customer'),
                btn.getAttribute('data-invoice')
            );
        });
    });

    // Payment Plan Modal Logic
let currentPaymentPlanInvoice = null;

// Function to calculate installment schedule
function calculateInstallmentSchedule(invoiceId, downPayment, installmentCount, frequency, startDate, totalOutstanding) {
    const remainingAmount = totalOutstanding - downPayment;
    const installmentAmount = remainingAmount / installmentCount;
    const schedule = [];
    let currentDate = new Date(startDate);

    for (let i = 1; i <= installmentCount; i++) {
        let dueDate = new Date(currentDate);

        switch (frequency) {
            case 'weekly':
                dueDate.setDate(dueDate.getDate() + (i - 1) * 7);
                break;
            case 'biweekly':
                dueDate.setDate(dueDate.getDate() + (i - 1) * 14);
                break;
            case 'quarterly':
                dueDate.setMonth(dueDate.getMonth() + (i - 1) * 3);
                break;
            default: // monthly
                dueDate.setMonth(dueDate.getMonth() + (i - 1));
                break;
        }

        schedule.push({
            number: i,
            due_date: dueDate,
            amount: installmentAmount
        });
    }

    return { schedule, remainingAmount, installmentAmount };
}

// Update installment preview table
function updateInstallmentPreview() {
    const invoiceId = currentPaymentPlanInvoice?.id;
    if (!invoiceId) return;

    const downPayment = parseFloat(document.getElementById('downPayment').value) || 0;
    const installmentCount = parseInt(document.getElementById('installmentCount').value);
    const frequency = document.getElementById('frequency').value;
    const startDate = document.getElementById('startDate').value;
    const totalOutstanding = currentPaymentPlanInvoice.outstanding;

    const { schedule, remainingAmount, installmentAmount } = calculateInstallmentSchedule(
        invoiceId, downPayment, installmentCount, frequency, startDate, totalOutstanding
    );

    // Update summary
    const downPaymentSpan = document.getElementById('downPaymentPreview');
    if (downPaymentSpan) downPaymentSpan.textContent = formatAmount(downPayment, currentPaymentPlanInvoice.currency);

    const remainingSpan = document.getElementById('remainingAmountPreview');
    if (remainingSpan) remainingSpan.textContent = formatAmount(remainingAmount, currentPaymentPlanInvoice.currency);

    const installmentSpan = document.getElementById('installmentAmountPreview');
    if (installmentSpan) installmentSpan.textContent = formatAmount(installmentAmount, currentPaymentPlanInvoice.currency);

    // Update table
    const tbody = document.querySelector('#installmentPreviewTable tbody');
    tbody.innerHTML = '';

    schedule.forEach(inst => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${inst.number}</td>
            <td>${inst.due_date.toLocaleDateString()}</td>
            <td>${formatAmount(inst.amount, currentPaymentPlanInvoice.currency)}</td>
        `;
        tbody.appendChild(row);
    });
}

// Format amount with currency
function formatAmount(amount, currency) {
    return currency === 'USD' ? `$${amount.toFixed(2)}` : `KSH ${amount.toFixed(2)}`;
}

// Load invoice details for payment plan
async function loadPaymentPlanInvoice(invoiceId) {
    try {
        const response = await fetch(`/finance/debt/invoice/${invoiceId}/payment-plan-data`);
        if (!response.ok) throw new Error('Failed to load invoice data');

        const invoice = await response.json();
        currentPaymentPlanInvoice = invoice;

        // Update modal summary
        document.getElementById('summaryInvoiceNumber').textContent = invoice.billing_number;
        document.getElementById('summaryCustomerName').textContent = invoice.customer_name;
        document.getElementById('summaryTotalAmount').innerHTML = formatAmount(invoice.total_amount, invoice.currency);
        document.getElementById('summaryOutstanding').innerHTML = formatAmount(invoice.outstanding, invoice.currency);
        document.getElementById('summaryDueDate').textContent = invoice.due_date ? new Date(invoice.due_date).toLocaleDateString() : 'N/A';
        document.getElementById('summaryDaysOverdue').innerHTML = invoice.days_overdue > 0 ? `<span class="badge bg-danger">${invoice.days_overdue} days overdue</span>` : 'Current';

        // Update currency symbol
        const currencySymbol = invoice.currency === 'USD' ? '$' : 'KSH';
        document.getElementById('downPaymentCurrency').textContent = currencySymbol;

        // Reset form
        document.getElementById('downPayment').value = 0;
        document.getElementById('installmentCount').value = '3';
        document.getElementById('frequency').value = 'monthly';
        document.getElementById('startDate').value = new Date().toISOString().split('T')[0];

        // Update preview
        updateInstallmentPreview();

    } catch (error) {
        console.error('Error loading invoice:', error);
        showToast('Failed to load invoice data', 'danger');
    }
}

// Create payment plan
async function createPaymentPlan(invoiceId, formData) {
    try {
        const response = await fetch(`/finance/debt/payment-plan/${invoiceId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Failed to create payment plan');
        }

        return { success: true, data };
    } catch (error) {
        return { success: false, error: error.message };
    }
}

// Payment Plan Button Click Handler
document.querySelectorAll('.payment-plan-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
        e.stopPropagation();

        const invoiceId = btn.getAttribute('data-id');
        const amount = btn.getAttribute('data-amount');
        const currency = btn.getAttribute('data-currency');

        // Load invoice data first
        currentPaymentPlanInvoice = {
            id: invoiceId,
            billing_number: btn.closest('tr')?.querySelector('.invoice-number')?.innerText || `CONS-${invoiceId}`,
            customer_name: btn.closest('tr')?.querySelector('td:nth-child(3) .fw-semibold')?.innerText || 'Unknown',
            total_amount: parseFloat(amount),
            outstanding: parseFloat(amount),
            currency: currency,
            due_date: btn.closest('tr')?.querySelector('td:nth-child(5)')?.innerText || null,
            days_overdue: 0
        };

        // Update modal with basic info
        document.getElementById('plan_invoice_id').value = invoiceId;
        document.getElementById('summaryInvoiceNumber').textContent = currentPaymentPlanInvoice.billing_number;
        document.getElementById('summaryCustomerName').textContent = currentPaymentPlanInvoice.customer_name;
        document.getElementById('summaryTotalAmount').innerHTML = formatAmount(currentPaymentPlanInvoice.total_amount, currency);
        document.getElementById('summaryOutstanding').innerHTML = formatAmount(currentPaymentPlanInvoice.outstanding, currency);

        const currencySymbol = currency === 'USD' ? '$' : 'KSH';
        document.getElementById('downPaymentCurrency').textContent = currencySymbol;

        // Reset form
        document.getElementById('downPayment').value = 0;
        document.getElementById('installmentCount').value = '3';
        document.getElementById('frequency').value = 'monthly';
        document.getElementById('startDate').value = new Date().toISOString().split('T')[0];

        // Update preview
        updateInstallmentPreview();

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('paymentPlanModal'));
        modal.show();
    });
});

// Preview updates on form change
document.getElementById('downPayment')?.addEventListener('input', updateInstallmentPreview);
document.getElementById('installmentCount')?.addEventListener('change', updateInstallmentPreview);
document.getElementById('frequency')?.addEventListener('change', updateInstallmentPreview);
document.getElementById('startDate')?.addEventListener('change', updateInstallmentPreview);

// Payment Plan Form Submit
document.getElementById('paymentPlanForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const submitBtn = document.getElementById('submitPaymentPlanBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';

    const invoiceId = document.getElementById('plan_invoice_id').value;
    const formData = {
        down_payment: parseFloat(document.getElementById('downPayment').value) || 0,
        installment_count: parseInt(document.getElementById('installmentCount').value),
        frequency: document.getElementById('frequency').value,
        start_date: document.getElementById('startDate').value,
        notes: document.getElementById('planNotes').value
    };

    const result = await createPaymentPlan(invoiceId, formData);

    if (result.success) {
        showToast('Payment plan created successfully!', 'success');

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('paymentPlanModal'));
        modal.hide();

        // Optionally refresh the page or update the row
        setTimeout(() => {
            location.reload();
        }, 1500);
    } else {
        showToast(result.error || 'Failed to create payment plan', 'danger');
    }

    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
});

    document.querySelectorAll('#overdueTableBody tr').forEach(row => {
        row.addEventListener('click', e => {
            if (
                e.target.type !== 'checkbox' &&
                !e.target.closest('.action-btn') &&
                !e.target.closest('a') &&
                !e.target.closest('button')
            ) {
                const checkbox = row.querySelector('.invoice-select');

                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });
    });

    if (currencyFilter) currencyFilter.addEventListener('change', filterTable);
    if (severityFilter) severityFilter.addEventListener('change', filterTable);
    if (customerSearch) customerSearch.addEventListener('keyup', filterTable);
    if (clearSearchBtn) clearSearchBtn.addEventListener('click', clearSearch);
    if (exportTableBtn) exportTableBtn.addEventListener('click', exportToCSV);
    if (bulkSendReminderBtn) bulkSendReminderBtn.addEventListener('click', bulkSendReminders);
    if (scrollToTopBtn) scrollToTopBtn.addEventListener('click', scrollToTop);

    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', () => {
            selectedInvoices.clear();
            document.querySelectorAll('.invoice-select').forEach(cb => cb.checked = false);
            updateBulkActions();
        });
    }

    calculateTotals();
    filterTable();
});
</script>
