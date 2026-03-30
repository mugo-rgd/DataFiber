@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="header-actions">
        <div>
            <h1 class="h3 text-gray-800 mb-2">
                <i class="fas fa-file-invoice text-primary me-2"></i> Customer Quotations
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                   <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}" class="text-decoration-none"><i class="fas fa-users me-1"></i>Customers</a></li>
                    <li class="breadcrumb-item active text-primary"><i class="fas fa-file-invoice me-1"></i>Quotations</li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-outline-secondary" onclick="goBack()">
                <i class="fas fa-arrow-left me-2"></i>Go Back
            </button>
        </div>
    </div>

    <!-- Customer Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="card-title">{{ $customer->name }}</h5>
                    <p class="card-text mb-1"><i class="fas fa-envelope me-2 text-muted"></i>{{ $customer->email }}</p>
                    @if($customer->phone)
                    <p class="card-text mb-1"><i class="fas fa-phone me-2 text-muted"></i>{{ $customer->phone }}</p>
                    @endif
                    @if($customer->company)
                    <p class="card-text"><i class="fas fa-building me-2 text-muted"></i>{{ $customer->company }}</p>
                    @endif
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-primary fs-6">Total Quotations: {{ $quotations->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotations Table -->
    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Quotations List</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Quotation ID</th>
                            <th>Project Title</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Expiry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotations as $quotation)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>#{{ $quotation->id }}</strong></td>
                            <td>{{ $quotation->project_title ?? 'N/A' }}</td>
                            <td>
                                @if($quotation->total_amount)
                                    ${{ number_format($quotation->total_amount, 2) }}
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'sent' => 'info',
                                        'accepted' => 'success',
                                        'rejected' => 'danger',
                                        'expired' => 'warning'
                                    ];
                                    $statusColor = $statusColors[$quotation->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ ucfirst($quotation->status) }}</span>
                            </td>
                            <td>{{ $quotation->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($quotation->expiry_date)
                                    {{ \Carbon\Carbon::parse($quotation->expiry_date)->format('M d, Y') }}
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.quotations.show', $quotation->id) }}">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.quotations.edit', $quotation->id) }}">
                                                <i class="fas fa-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                       <li>
    <a class="dropdown-item text-muted" href="#" onclick="alert('PDF download feature coming soon!')" style="pointer-events: none; opacity: 0.6;">
        <i class="fas fa-download me-2"></i>Download PDF
    </a>
</li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No quotations found</h5>
                                <p class="text-muted">This customer doesn't have any quotations yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($quotations->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $quotations->firstItem() }} to {{ $quotations->lastItem() }} of {{ $quotations->total() }} entries
                    </div>
                    <div>
                        {{ $quotations->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function goBack() {
    if (document.referrer && document.referrer.includes(window.location.host)) {
        window.history.back();
    } else {
        window.location.href = "{{ route('admin.customers.index') }}";
    }
}
</script>
@endsection
