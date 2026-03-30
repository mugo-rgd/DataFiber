@extends('layouts.app')

@section('title', 'Create Quotation - Account Manager')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice-dollar me-2"></i>Create New Quotation
        </h1>
        <a href="{{ route('account-manager.quotations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Quotations
        </a>
    </div>

    <!-- Design Request Info Card -->
    @if(isset($designRequest))
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-drafting-compass me-1"></i> Design Request Information
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Request #:</strong> {{ $designRequest->request_number }}</p>
                    <p><strong>Customer:</strong> {{ $designRequest->customer->name ?? 'N/A' }}</p>
                    <p><strong>Title:</strong> {{ $designRequest->title }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Priority:</strong>
                        <span class="badge bg-{{ $designRequest->priority === 'urgent' ? 'danger' : ($designRequest->priority === 'high' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($designRequest->priority) }}
                        </span>
                    </p>
                    <p><strong>Status:</strong>
                        <span class="badge bg-{{ $designRequest->status === 'completed' ? 'success' : ($designRequest->status === 'in_progress' ? 'primary' : 'warning') }}">
                            {{ ucfirst($designRequest->status) }}
                        </span>
                    </p>
                    <p><strong>Created:</strong> {{ $designRequest->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            @if($designRequest->description)
            <div class="mt-3">
                <strong>Description:</strong>
                <p class="mt-1">{{ $designRequest->description }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Quotation Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-edit me-1"></i> Quotation Details
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('account-manager.quotations.store') }}" method="POST" id="quotationForm">
                @csrf

                @if(isset($designRequest))
                <input type="hidden" name="design_request_id" value="{{ $designRequest->id }}">
                @endif

                <!-- Customer Selection -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="customer_id" class="form-label">Customer *</label>
                        <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ (old('customer_id', isset($designRequest) ? $designRequest->customer_id : '') == $customer->id) ? 'selected' : '' }}>
                                    {{ $customer->name }} - {{ $customer->company ?? 'No Company' }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="quotation_number" class="form-label">Quotation Number *</label>
                        <input type="text" class="form-control @error('quotation_number') is-invalid @enderror"
                               id="quotation_number" name="quotation_number"
                               value="{{ old('quotation_number', 'Q-' . date('Ymd-His')) }}" required readonly>
                        @error('quotation_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Quotation Details -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Quotation Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title"
                               value="{{ old('title', isset($designRequest) ? 'Quotation for ' . $designRequest->title : '') }}"
                               required placeholder="Enter quotation title">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="valid_until" class="form-label">Valid Until *</label>
                        <input type="date" class="form-control @error('valid_until') is-invalid @enderror"
                               id="valid_until" name="valid_until"
                               value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}" required>
                        @error('valid_until')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="currency" class="form-label">Currency *</label>
                        <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                            <option value="KES" {{ old('currency', 'KES') == 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                        </select>
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Quotation Items -->
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label">Quotation Items *</label>
                        <div id="quotationItems">
                            <!-- Dynamic items will be added here -->
                            <div class="quotation-item mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Description *</label>
                                        <input type="text" class="form-control item-description" name="items[0][description]"
                                               placeholder="Item description" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Quantity *</label>
                                        <input type="number" class="form-control item-quantity" name="items[0][quantity]"
                                               value="1" min="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Unit Price *</label>
                                        <input type="number" class="form-control item-unit-price" name="items[0][unit_price]"
                                               step="0.01" min="0" placeholder="0.00" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Total</label>
                                        <input type="text" class="form-control item-total" readonly value="0.00">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-sm remove-item w-100" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="addItem" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-plus me-1"></i> Add Item
                        </button>
                    </div>
                </div>

                <!-- Totals Section -->
                <div class="row mb-3">
                    <div class="col-md-6 offset-md-6">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td><strong>Subtotal:</strong></td>
                                        <td class="text-end"><span id="subtotal">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Tax (%):</strong>
                                            <input type="number" class="form-control form-control-sm d-inline-block w-50 ms-2"
                                                   id="taxRate" name="tax_rate" value="{{ old('tax_rate', 16) }}" min="0" max="100" step="0.01">
                                        </td>
                                        <td class="text-end"><span id="taxAmount">0.00</span></td>
                                    </tr>
                                    <tr class="table-active">
                                        <td><strong>Total:</strong></td>
                                        <td class="text-end"><strong><span id="grandTotal">0.00</span></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes & Terms</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                  rows="4" placeholder="Enter any additional notes or terms and conditions...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <div>
                                <button type="submit" name="action" value="save_draft" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i> Save as Draft
                                </button>
                                <button type="submit" name="action" value="send_to_customer" class="btn btn-success">
                                    <i class="fas fa-paper-plane me-1"></i> Send to Customer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCount = 1;
    const quotationItems = document.getElementById('quotationItems');
    const addItemBtn = document.getElementById('addItem');
    const taxRateInput = document.getElementById('taxRate');

    // Add new item
    addItemBtn.addEventListener('click', function() {
        const newItem = document.createElement('div');
        newItem.className = 'quotation-item mb-3 p-3 border rounded';
        newItem.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Description *</label>
                    <input type="text" class="form-control item-description" name="items[${itemCount}][description]"
                           placeholder="Item description" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity *</label>
                    <input type="number" class="form-control item-quantity" name="items[${itemCount}][quantity]"
                           value="1" min="1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unit Price *</label>
                    <input type="number" class="form-control item-unit-price" name="items[${itemCount}][unit_price]"
                           step="0.01" min="0" placeholder="0.00" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total</label>
                    <input type="text" class="form-control item-total" readonly value="0.00">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm remove-item w-100">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        quotationItems.appendChild(newItem);

        // Enable remove buttons for all items except the first one
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.disabled = false;
        });

        // Add event listeners to new inputs
        const newInputs = newItem.querySelectorAll('.item-quantity, .item-unit-price');
        newInputs.forEach(input => {
            input.addEventListener('input', calculateItemTotal);
        });

        itemCount++;
    });

    // Remove item
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            const btn = e.target.classList.contains('remove-item') ? e.target : e.target.closest('.remove-item');
            const item = btn.closest('.quotation-item');

            if (document.querySelectorAll('.quotation-item').length > 1) {
                item.remove();
                updateItemIndexes();
                calculateTotals();
            }
        }
    });

    // Calculate item total
    function calculateItemTotal() {
        const item = this.closest('.quotation-item');
        const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
        const unitPrice = parseFloat(item.querySelector('.item-unit-price').value) || 0;
        const total = quantity * unitPrice;

        item.querySelector('.item-total').value = total.toFixed(2);
        calculateTotals();
    }

    // Update item indexes after removal
    function updateItemIndexes() {
        const items = document.querySelectorAll('.quotation-item');
        items.forEach((item, index) => {
            const inputs = item.querySelectorAll('input');
            inputs.forEach(input => {
                const name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
                input.name = name;
            });
        });
        itemCount = items.length;
    }

    // Calculate all totals
    function calculateTotals() {
        let subtotal = 0;

        document.querySelectorAll('.quotation-item').forEach(item => {
            const total = parseFloat(item.querySelector('.item-total').value) || 0;
            subtotal += total;
        });

        const taxRate = parseFloat(taxRateInput.value) || 0;
        const taxAmount = subtotal * (taxRate / 100);
        const grandTotal = subtotal + taxAmount;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('taxAmount').textContent = taxAmount.toFixed(2);
        document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
    }

    // Initialize event listeners
    document.querySelectorAll('.item-quantity, .item-unit-price').forEach(input => {
        input.addEventListener('input', calculateItemTotal);
    });

    taxRateInput.addEventListener('input', calculateTotals);

    // Form validation
    document.getElementById('quotationForm').addEventListener('submit', function(e) {
        const items = document.querySelectorAll('.quotation-item');
        let valid = true;

        items.forEach((item, index) => {
            const description = item.querySelector('.item-description').value.trim();
            const quantity = item.querySelector('.item-quantity').value;
            const unitPrice = item.querySelector('.item-unit-price').value;

            if (!description || !quantity || !unitPrice) {
                valid = false;
                item.classList.add('border-danger');
            } else {
                item.classList.remove('border-danger');
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Please fill in all required fields for all items.');
        }
    });
});
</script>

<style>
.quotation-item {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.quotation-item:hover {
    background-color: #e9ecef;
}

.quotation-item.border-danger {
    border-color: #dc3545 !important;
    background-color: #f8d7da;
}

.form-control:readonly {
    background-color: #e9ecef;
}
</style>
@endpush
