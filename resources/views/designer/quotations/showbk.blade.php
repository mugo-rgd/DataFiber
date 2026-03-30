@extends('layouts.app')

@section('title', 'Quotation ' . $quotation->quotation_number)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-invoice-dollar text-success"></i>
                        Quotation: {{ $quotation->quotation_number }}
                    </h1>
                    <p class="text-muted mb-0">Created for {{ $quotation->designRequest->customer->name }}</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ match($quotation->status) {
                        'draft' => 'secondary',
                        'sent' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'light'
                    } }} fs-6">
                        {{ ucfirst($quotation->status) }}
                    </span>
                    @if($quotation->isExpired())
                        <span class="badge bg-warning fs-6">Expired</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quotation Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-receipt me-2"></i>Quotation Details
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Customer Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary">Bill To:</h6>
                            <p class="mb-1"><strong>{{ $quotation->designRequest->customer->name }}</strong></p>
                            <p class="mb-1 text-muted">{{ $quotation->designRequest->customer->email }}</p>
                            <p class="mb-0 text-muted">{{ $quotation->designRequest->customer->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Quotation Info:</h6>
                            <p class="mb-1"><strong>Quotation #:</strong> {{ $quotation->quotation_number }}</p>
                            <p class="mb-1"><strong>Design Request:</strong> {{ $quotation->designRequest->request_number }}</p>
                            <p class="mb-1"><strong>Valid Until:</strong> {{ $quotation->valid_until->format('F d, Y') }}</p>
                            <p class="mb-0"><strong>Created:</strong> {{ $quotation->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <h6 class="text-primary mb-3">Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50%">Description</th>
                                    <th width="15%" class="text-center">Quantity</th>
                                    <th width="15%" class="text-end">Unit Price</th>
                                    <th width="20%" class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($quotation->line_items) && is_array($quotation->line_items))
                                    @foreach($quotation->line_items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item['description'] ?? 'N/A' }}</strong>
                                                @if(isset($item['technology']) || isset($item['cores']))
                                                    <br>
                                                    <small class="text-muted">
                                                        @if(isset($item['technology']))
                                                            Technology: {{ $item['technology'] }}
                                                        @endif
                                                        @if(isset($item['cores']))
                                                            | Cores: {{ $item['cores'] }}
                                                        @endif
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $item['quantity'] ?? 0 }} {{ $item['unit'] ?? '' }}
                                            </td>
                                            <td class="text-end">
                                                ${{ number_format($item['unit_price'] ?? 0, 2) }}
                                            </td>
                                            <td class="text-end">
                                                ${{ number_format($item['amount'] ?? 0, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            No line items available
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="text-end"><strong>${{ number_format($quotation->subtotal, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Tax ({{ $quotation->tax_rate * 100 }}%):</strong></td>
                                    <td class="text-end"><strong>${{ number_format($quotation->tax_amount, 2) }}</strong></td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                    <td class="text-end"><strong>${{ number_format($quotation->total_amount, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Scope of Work -->
                    @if($quotation->scope_of_work)
                        <div class="mt-4">
                            <h6 class="text-primary">Scope of Work</h6>
                            <p class="text-muted">{{ $quotation->scope_of_work }}</p>
                        </div>
                    @endif

                    <!-- Terms and Conditions -->
                    @if($quotation->terms_and_conditions)
                        <div class="mt-4">
                            <h6 class="text-primary">Terms & Conditions</h6>
                            <p class="text-muted">{{ $quotation->terms_and_conditions }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions & Status -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-light">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Quotation Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Current Status:</strong>
                        <span class="badge bg-{{ match($quotation->status) {
                            'draft' => 'secondary',
                            'sent' => 'info',
                            'accepted' => 'success',
                            'rejected' => 'danger',
                            default => 'light'
                        } }} float-end">
                            {{ ucfirst($quotation->status) }}
                        </span>
                    </div>

                    @if($quotation->sent_at)
                        <div class="mb-3">
                            <strong>Sent Date:</strong>
                            <span class="float-end">{{ $quotation->sent_at->format('M d, Y') }}</span>
                        </div>
                    @endif

                    @if($quotation->accepted_at)
                        <div class="mb-3">
                            <strong>Accepted Date:</strong>
                            <span class="float-end">{{ $quotation->accepted_at->format('M d, Y') }}</span>
                        </div>
                    @endif

                    @if($quotation->rejected_at)
                        <div class="mb-3">
                            <strong>Rejected Date:</strong>
                            <span class="float-end">{{ $quotation->rejected_at->format('M d, Y') }}</span>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Valid Until:</strong>
                        <span class="float-end {{ $quotation->isExpired() ? 'text-danger' : 'text-success' }}">
                            {{ $quotation->valid_until->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('designer.quotations') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>

                        @if($quotation->status === 'draft')
                            <button class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>Send to Customer
                            </button>
                        @endif

                        <button class="btn btn-outline-primary">
                            <i class="fas fa-print me-2"></i>Print Quotation
                        </button>

                        <button class="btn btn-outline-info">
                            <i class="fas fa-download me-2"></i>Download PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
