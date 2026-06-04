@extends('layouts.app')

@section('title', 'Overdue Invoices')

@section('content')
<div class="container-fluid overdue-page">

    <div class="overdue-header mb-4">
        <div>
            <span class="badge bg-warning text-dark mb-2">
                <i class="fas fa-exclamation-triangle me-1"></i> Debt Management
            </span>

            <h1 class="h3 mb-1 fw-bold">Overdue Invoices</h1>

            <p class="mb-0 text-white-75">
                Manage overdue customer invoices, reminders, and payment recovery.
            </p>
        </div>

        <div class="header-actions">
            <a href="{{ route('finance.dashboard') }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>

            <button onclick="window.print()" class="btn btn-outline-light">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body d-flex gap-3 align-items-center">
            <div class="info-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <strong>Tip:</strong>
                Use filters to prioritize critical invoices and send payment reminders.
            </div>
        </div>
    </div>

    @include('finance.debt.partials.overdue-invoices-table')

</div>

{{-- Include the payment plan modal here (inside content but after the table) --}}
@include('finance.debt.partials.payment-plan-modal')

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Overdue invoices page loaded');
});
</script>
@endpush

@push('styles')
<style>
.overdue-page {
    background: #f6f8fb;
    min-height: calc(100vh - 80px);
    padding-top: 1rem;
    padding-bottom: 2rem;
}

.overdue-header {
    background: linear-gradient(135deg, #b91c1c, #dc2626, #f59e0b);
    color: white;
    border-radius: 24px;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    box-shadow: 0 14px 35px rgba(220, 38, 38, .22);
}

.header-actions {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
}

.text-white-75 {
    color: rgba(255,255,255,.8);
}

.info-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: rgba(245, 158, 11, .15);
    color: #b45309;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Summary Card Hover Effects */
.summary-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.1);
}

/* Table Row Transitions */
.modern-table tbody tr {
    transition: background-color 0.2s ease;
}

.modern-table tbody tr:hover {
    background: #f1f5f9;
}

/* Action Button Hover Effects */
.action-btn {
    transition: all 0.2s ease;
}

.action-btn:hover {
    transform: translateY(-2px);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .overdue-header {
        flex-direction: column;
        align-items: flex-start;
        border-radius: 18px;
    }

    .header-actions {
        width: 100%;
    }

    .header-actions .btn {
        flex: 1;
        text-align: center;
    }

    .summary-card h2 {
        font-size: 1.25rem;
    }

    .bulk-actions-bar {
        flex-direction: column;
        align-items: flex-start !important;
    }
}

/* Loading State */
.btn-loading {
    position: relative;
    pointer-events: none;
    opacity: 0.7;
}

.btn-loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid #fff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Print Styles */
@media print {
    .header-actions,
    .filter-bar,
    .bulk-actions-bar,
    .action-btn,
    #scrollToTopBtn,
    .info-icon {
        display: none !important;
    }

    .overdue-page {
        background: white;
        padding: 0;
    }

    .overdue-header {
        box-shadow: none;
        color: black;
        background: white;
        border: 1px solid #ddd;
        padding: 1rem;
    }

    .modern-table-container {
        box-shadow: none;
        border: 1px solid #ddd;
    }

    .summary-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}

/* Empty State Animation */
.empty-state {
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Toast Customization */
.toast-container-custom {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}

.toast {
    backdrop-filter: blur(8px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}
</style>
@endpush
