@extends('layouts.app')

@section('title', 'Manage Quotations')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-file-invoice-dollar text-primary"></i> Manage Quotations
            </h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Quotations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $quotations->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Draft</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $quotations->where('status', 'draft')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pending Approval</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $quotations->where('status', 'draft')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $quotations->where('status', 'approved')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Quotations</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">All</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'draft']) }}">Draft</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'sent']) }}">Sent</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}">Approved</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}">Rejected</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($quotations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Quotation #</th>
                                <th>Design Request</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Valid Until</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotations as $quotation)
                                <tr>
                                    <td>
                                        <strong>{{ $quotation->quotation_number }}</strong>
                                        @if($quotation->isExpired() && $quotation->status === 'sent')
                                            <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Expired</small>
                                        @endif
                                    </td>
                                    <td>#{{ $quotation->designRequest->request_number }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $quotation->customer->name }}</strong>
                                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'accountmanager_admin')
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i>{{ $quotation->customer->email }}
                                                    @if($quotation->customer->phone)
                                                        <br><i class="fas fa-phone me-1"></i>{{ $quotation->customer->phone }}
                                                    @endif
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>${{ number_format($quotation->total_amount, 2) }}</td>
                                    <td>
    <span class="badge bg-{{ match($quotation->status) {
        'draft' => 'secondary',
        'approved' => 'success',
        'sent' => 'info',
        'rejected' => 'danger',
        default => 'secondary'
    } }}">
        {{ ucfirst($quotation->status) }}
        @if($quotation->status === 'approved')
            <i class="fas fa-check-circle ms-1" title="Ready to send to customer"></i>
        @endif
        @if($quotation->status === 'sent')
            <i class="fas fa-paper-plane ms-1" title="Sent to customer"></i>
        @endif
    </span>

    @if($quotation->approved_at && $quotation->status === 'approved')
        <br>
        <small class="text-muted">Approved: {{ $quotation->approved_at->format('M d') }}</small>
    @endif
    @if($quotation->sent_at && $quotation->status === 'sent')
        <br>
        <small class="text-muted">Sent: {{ $quotation->sent_at->format('M d') }}</small>
    @endif
</td>
                                    <td>
                                        {{ $quotation->valid_until->format('M d, Y') }}
                                        @if($quotation->valid_until->isPast())
                                            <br><small class="text-danger">Expired</small>
                                        @elseif($quotation->valid_until->diffInDays(now()) <= 7)
                                            <br><small class="text-warning">Expires soon</small>
                                        @endif
                                    </td>
                                    <td>{{ $quotation->created_at->format('M d, Y') }}</td>
<td>
    <div class="btn-group" role="group">
        {{-- View button --}}
   @auth
    @php
        $user = auth()->user();
    @endphp

    @if($user->role === 'customer')
        {{-- Direct URL for customer --}}
        <a href="/customer/quotations/{{ $quotation->id }}"
           class="btn btn-outline-primary btn-sm"
           data-bs-toggle="tooltip" title="View Quotation Details">
            <i class="fas fa-eye"></i>
        </a>
    @elseif(in_array($user->role, ['admin', 'system_admin', 'account_manager', 'accountmanager_admin']))
        {{-- Direct URL for admin --}}
        <a href="/admin/quotations/{{ $quotation->id }}"
           class="btn btn-outline-primary btn-sm"
           data-bs-toggle="tooltip" title="View Quotation Details">
            <i class="fas fa-eye"></i>
        </a>
    @else
        <button class="btn btn-outline-secondary btn-sm" disabled>
            <i class="fas fa-eye"></i>
        </button>
    @endif
@endauth

        {{-- Actions for DRAFT quotations --}}
        @if($quotation->status === 'draft')
            {{-- Edit button --}}
            <a href="{{ route('admin.quotations.edit', $quotation) }}"
               class="btn btn-outline-warning btn-sm"
               data-bs-toggle="tooltip" title="Edit Quotation">
                <i class="fas fa-edit"></i>
            </a>

            {{-- Admin actions for drafts --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'accountmanager_admin')
                {{-- Approve button --}}
                <button type="button"
                        class="btn btn-outline-success btn-sm"
                        onclick="approveQuotation({{ $quotation->id }})"
                        data-bs-toggle="tooltip" title="Approve Quotation">
                    <i class="fas fa-check"></i>
                </button>

                {{-- Reject button --}}
                <button type="button"
                        class="btn btn-outline-danger btn-sm"
                        onclick="rejectQuotation({{ $quotation->id }})"
                        data-bs-toggle="tooltip" title="Reject Quotation">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        @endif

        {{-- Send to Customer button for APPROVED quotations --}}
        @if($quotation->status === 'approved')
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'accountmanager_admin')
                <button type="button"
                        class="btn btn-outline-info btn-sm"
                        onclick="sendQuotation({{ $quotation->id }})"
                        data-bs-toggle="tooltip" title="Send to Customer">
                    <i class="fas fa-paper-plane"></i>
                </button>
            @endif
        @endif

        {{-- Info for non-actionable quotations --}}
        @if(!in_array($quotation->status, ['draft', 'approved']))
            <span class="badge bg-light text-dark ms-2" data-bs-toggle="tooltip"
                  title="This quotation is {{ $quotation->status }} and cannot be modified">
                <i class="fas fa-lock"></i>
            </span>
        @endif
    </div>
</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $quotations->firstItem() }} to {{ $quotations->lastItem() }} of {{ $quotations->total() }} quotations
                    </div>
                    <div>
                        {{ $quotations->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No quotations found</h5>
                    <p class="text-muted">
                        @if(request('status'))
                            No {{ request('status') }} quotations found.
                        @else
                            Create your first quotation from a design request.
                        @endif
                    </p>
                    <a href="{{ route('admin.design-requests.index') }}" class="btn btn-primary">
                        <i class="fas fa-drafting-compass me-2"></i>Go to Design Requests
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Define functions first to ensure they're available
function showAlert(type, message) {
    console.log('Showing alert:', type, message);
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Insert at the top of the content
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 5000);
}

function approveQuotation(quotationId) {
    console.log('Approve quotation clicked:', quotationId);
    const notes = prompt('Enter approval notes (optional):');
    if (notes !== null) {
        fetch(`/admin/quotations/${quotationId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ notes: notes })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Quotation approved! You can now send it to the customer.');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while approving the quotation.');
        });
    }
}

function rejectQuotation(quotationId) {
    console.log('Reject quotation clicked:', quotationId);
    const notes = prompt('Enter rejection reason (required):');
    if (notes !== null && notes.trim() !== '') {
        fetch(`/admin/quotations/${quotationId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ notes: notes })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while rejecting the quotation.');
        });
    } else if (notes !== null) {
        showAlert('error', 'Rejection reason is required.');
    }
}

function sendQuotation(quotationId) {
    console.log('Send quotation clicked:', quotationId);
    if (confirm('Are you sure you want to send this APPROVED quotation to the customer?')) {
        fetch(`/admin/quotations/${quotationId}/send`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while sending the quotation.');
        });
    }
}

// Initialize tooltips after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    console.log('Tooltips initialized');
    console.log('Quotation functions available:', {
        approveQuotation: typeof approveQuotation,
        rejectQuotation: typeof rejectQuotation,
        sendQuotation: typeof sendQuotation
    });
});

// Make functions available globally
window.approveQuotation = approveQuotation;
window.rejectQuotation = rejectQuotation;
window.sendQuotation = sendQuotation;
window.showAlert = showAlert;
</script>

<style>
.btn-group .btn {
    margin-right: 2px;
}
.btn-group .btn:last-child {
    margin-right: 0;
}
.table td {
    vertical-align: middle;
}
.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endsection
