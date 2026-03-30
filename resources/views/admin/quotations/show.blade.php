@extends('layouts.app')

@section('title', 'Quotation Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-file-invoice-dollar text-primary"></i> Quotation Details
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.quotations.index') }}">Quotations</a></li>
                    <li class="breadcrumb-item active">{{ $quotation->quotation_number }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Quotation Header -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Quotation: {{ $quotation->quotation_number }}</h5>
                        <span class="badge bg-{{ match($quotation->status) {
                            'draft' => 'secondary',
                            'sent' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'secondary'
                        } }}">
                            {{ ucfirst($quotation->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Design Request:</strong> #{{ $quotation->designRequest->request_number }}</p>
                            <p><strong>Customer:</strong> {{ $quotation->customer->name }}</p>
                            <p><strong>Account Manager:</strong> {{ $quotation->accountManager->name }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Created:</strong> {{ $quotation->created_at->format('M d, Y') }}</p>
                            <p><strong>Valid Until:</strong> {{ $quotation->valid_until->format('M d, Y') }}</p>
                            @if($quotation->sent_at)
                                <p><strong>Sent:</strong> {{ $quotation->sent_at->format('M d, Y') }}</p>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <p><strong>Subtotal:</strong> ${{ number_format($quotation->subtotal, 2) }}</p>
                            <p><strong>Tax ({{ $quotation->tax_rate * 100 }}%):</strong> ${{ number_format($quotation->tax_amount, 2) }}</p>
                            <p><strong class="h5">Total: ${{ number_format($quotation->total_amount, 2) }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Items -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Line Items</h5>
                </div>
                <div class="card-body">
                    @if($quotation->line_items && count($quotation->line_items) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotation->line_items as $item)
                                        <tr>
                                            <td>{{ $item['description'] }}</td>
                                            <td>{{ $item['quantity'] }}</td>
                                            <td>${{ number_format($item['unit_price'], 2) }}</td>
                                            <td>${{ number_format($item['total'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                        <td><strong>${{ number_format($quotation->subtotal, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tax ({{ $quotation->tax_rate * 100 }}%):</strong></td>
                                        <td><strong>${{ number_format($quotation->tax_amount, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td><strong class="h5">${{ number_format($quotation->total_amount, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No line items found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Scope of Work & Terms -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Scope of Work</h5>
                </div>
                <div class="card-body">
                    <p>{{ $quotation->scope_of_work }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Terms & Conditions</h5>
                </div>
                <div class="card-body">
                    <p>{{ $quotation->terms_and_conditions }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.quotations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Quotations
                        </a>
                        <div class="btn-group">
                            @if($quotation->status === 'draft')
                                <button type="button" class="btn btn-primary" onclick="sendQuotation({{ $quotation->id }})">
                                    <i class="fas fa-paper-plane me-2"></i>Send to Customer
                                </button>
                                <a href="{{ route('admin.quotations.edit', $quotation) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit
                                </a>
                            @endif

                            @if($quotation->status === 'sent' && auth()->user()->can('approve-quotations'))
                                <button type="button" class="btn btn-success" onclick="approveQuotation({{ $quotation->id }})">
                                    <i class="fas fa-check me-2"></i>Approve
                                </button>
                                <button type="button" class="btn btn-danger" onclick="rejectQuotation({{ $quotation->id }})">
                                    <i class="fas fa-times me-2"></i>Reject
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function sendQuotation(quotationId) {
    if (confirm('Are you sure you want to send this quotation to the customer?')) {
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
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the quotation.');
        });
    }
}

function approveQuotation(quotationId) {
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
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while approving the quotation.');
        });
    }
}

function rejectQuotation(quotationId) {
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
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while rejecting the quotation.');
        });
    } else if (notes !== null) {
        alert('Rejection reason is required.');
    }
}
</script>
@endsection
