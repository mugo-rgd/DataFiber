@extends('layouts.app')

@section('title', 'Create Payment Followup')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Create Payment Followup</h1>
                <a href="{{ route('account-manager.payments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Payments
                </a>
            </div>
        </div>
    </div>

    <!-- My Customers with Debt Section -->
    @if($customersWithDebt->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>My Customers with Outstanding Debt
                        <span class="badge bg-light text-dark ms-2">{{ $customersWithDebt->count() }} customers</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th class="text-end">Outstanding (USD)</th>
                                    <th class="text-end">Outstanding (KSH)</th>
                                    <th class="text-end">Overdue</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customersWithDebt as $customer)
                                <tr>
                                    <td>
                                        <strong>{{ $customer->name }}</strong>
                                        @if($customer->company_name)
                                            <br><small class="text-muted">{{ $customer->company_name }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone ?? 'N/A' }}</td>
                                    <td class="text-end text-danger fw-bold">
                                        ${{ number_format($customer->outstanding_usd, 2) }}
                                    </td>
                                    <td class="text-end text-warning fw-bold">
                                        KSH {{ number_format($customer->outstanding_ksh, 2) }}
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-danger">{{ $customer->overdue_count }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary select-customer-btn"
                                                data-id="{{ $customer->id }}"
                                                data-name="{{ $customer->name }}"
                                                data-outstanding-usd="{{ $customer->outstanding_usd }}"
                                                data-outstanding-ksh="{{ $customer->outstanding_ksh }}">
                                            <i class="fas fa-check"></i> Select
                                        </button>
                                        <a href="{{ route('account-manager.customers.show', $customer->id) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end">TOTAL:</td>
                                    <td class="text-end text-danger">
                                        ${{ number_format($customersWithDebt->sum('outstanding_usd'), 2) }}
                                    </td>
                                    <td class="text-end text-warning">
                                        KSH {{ number_format($customersWithDebt->sum('outstanding_ksh'), 2) }}
                                    </td>
                                    <td class="text-end">{{ $customersWithDebt->sum('overdue_count') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Create Payment Followup Form -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form action="{{ route('account-manager.payments.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_id">Customer *</label>
                                    <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            data-outstanding-usd="{{ $customer->outstanding_usd ?? 0 }}"
                                            data-outstanding-ksh="{{ $customer->outstanding_ksh ?? 0 }}"
                                            {{ old('customer_id', request('customer_id')) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->email }}
                                            @if(($customer->outstanding_usd ?? 0) > 0 || ($customer->outstanding_ksh ?? 0) > 0)
                                                (Outstanding: ${{ number_format($customer->outstanding_usd ?? 0, 2) }} / KSH {{ number_format($customer->outstanding_ksh ?? 0, 2) }})
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">Amount *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" name="amount" id="amount"
                                               class="form-control @error('amount') is-invalid @enderror"
                                               value="{{ old('amount') }}" step="0.01" min="0" placeholder="0.00" required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date">Due Date *</label>
                                    <input type="date" name="due_date" id="due_date"
                                           class="form-control @error('due_date') is-invalid @enderror"
                                           value="{{ old('due_date') }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="reminded" {{ old('status') == 'reminded' ? 'selected' : '' }}>Reminded</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Payment status will be set to pending by default</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="4" placeholder="Add any notes about this payment followup...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-kp-primary">
                                <i class="fas fa-save"></i> Create Payment Followup
                            </button>
                            <a href="{{ route('account-manager.payments.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Outstanding Balance Alert -->
<div class="alert alert-info mt-3" id="outstandingAlert" style="display: none;">
    <i class="fas fa-info-circle me-2"></i>
    <span id="outstandingMessage"></span>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('due_date').min = today;

        // Format amount field on blur
        $('#amount').on('blur', function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value)) {
                $(this).val(value.toFixed(2));
            }
        });

        // Show outstanding balance when customer is selected
        $('#customer_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const outstandingUsd = parseFloat(selectedOption.data('outstanding-usd')) || 0;
            const outstandingKsh = parseFloat(selectedOption.data('outstanding-ksh')) || 0;

            if (outstandingUsd > 0 || outstandingKsh > 0) {
                let message = '';
                if (outstandingUsd > 0) {
                    message += `Customer has outstanding balance of $${outstandingUsd.toFixed(2)} USD. `;
                }
                if (outstandingKsh > 0) {
                    message += `Customer has outstanding balance of KSH ${outstandingKsh.toFixed(2)}. `;
                }
                message += 'Please follow up on previous dues.';
                $('#outstandingMessage').text(message);
                $('#outstandingAlert').show();
            } else {
                $('#outstandingAlert').hide();
            }
        });

        // Select customer from debt table
        $('.select-customer-btn').on('click', function() {
            const customerId = $(this).data('id');
            const customerName = $(this).data('name');
            const outstandingUsd = $(this).data('outstanding-usd');
            const outstandingKsh = $(this).data('outstanding-ksh');

            // Select the customer in the dropdown
            $('#customer_id').val(customerId).trigger('change');

            // Scroll to form
            $('html, body').animate({
                scrollTop: $('form').offset().top - 100
            }, 500);

            // Optional: Pre-fill amount with outstanding amount
            if (outstandingUsd > 0) {
                $('#amount').val(outstandingUsd.toFixed(2));
            }

            // Show success message
            const toast = $(`
                <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;">
                    <i class="fas fa-check-circle me-2"></i>
                    Customer "${customerName}" selected.
                    ${outstandingUsd > 0 ? `Outstanding: $${outstandingUsd.toFixed(2)}` : ''}
                    ${outstandingKsh > 0 ? ` / KSH ${outstandingKsh.toFixed(2)}` : ''}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            $('body').append(toast);
            setTimeout(() => toast.alert('close'), 3000);
        });
    });
</script>
@endpush
