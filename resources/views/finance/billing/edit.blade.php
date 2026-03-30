{{-- resources/views/finance/billing/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Billing')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('finance.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('finance.billing.index') }}">Billings</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('finance.billing.show', $billing->id) }}">{{ $billing->billing_number }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Billing #{{ $billing->billing_number }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('finance.billing.update', $billing->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer</label>
                                    <select class="form-select" id="customer_id" disabled>
                                        <option value="{{ $billing->user_id }}">
                                            {{ $billing->user->name }} ({{ $billing->user->email }})
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="billing_number" class="form-label">Billing Number</label>
                                    <input type="text" class="form-control" id="billing_number"
                                           value="{{ $billing->billing_number }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="billing_date" class="form-label">Billing Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('billing_date') is-invalid @enderror"
                                           id="billing_date" name="billing_date"
                                           value="{{ old('billing_date', $billing->billing_date->format('Y-m-d')) }}" required>
                                    @error('billing_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                           id="due_date" name="due_date"
                                           value="{{ old('due_date', $billing->due_date->format('Y-m-d')) }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status" name="status" required>
                                        <option value="draft" {{ old('status', $billing->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="pending" {{ old('status', $billing->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ old('status', $billing->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="overdue" {{ old('status', $billing->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                        <option value="cancelled" {{ old('status', $billing->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency</label>
                                    <input type="text" class="form-control" id="currency"
                                           value="{{ $billing->currency }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total_amount" class="form-label">Total Amount</label>
                                    <input type="text" class="form-control" id="total_amount"
                                           value="{{ number_format($billing->total_amount, 2) }} {{ $billing->currency }}" disabled>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3">{{ old('description', $billing->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Line Items</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Lease</th>
                                        <th>Description</th>
                                        <th>Period</th>
                                        <th>Amount</th>
                                        <th>Currency</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($billing->lineItems as $item)
                                        <tr>
                                            <td>{{ $item->lease->lease_number ?? 'N/A' }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>
                                                @if($item->period_start && $item->period_end)
                                                    {{ $item->period_start->format('d M Y') }} - {{ $item->period_end->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ number_format($item->amount, 2) }}</td>
                                            <td>{{ $item->currency }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th>{{ number_format($billing->total_amount, 2) }}</th>
                                        <th>{{ $billing->currency }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>KRA/TEVIN Information</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="200">Status:</th>
                                            <td>
                                                @if($billing->tevin_status == 'validated')
                                                    <span class="badge bg-success">Validated</span>
                                                @elseif($billing->tevin_status == 'queued')
                                                    <span class="badge bg-info">Queued</span>
                                                @elseif($billing->tevin_status == 'failed')
                                                    <span class="badge bg-danger">Failed</span>
                                                @else
                                                    <span class="badge bg-secondary">Not Submitted</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($billing->tevin_control_code)
                                        <tr>
                                            <th>Control Code:</th>
                                            <td>{{ $billing->tevin_control_code }}</td>
                                        </tr>
                                        @endif
                                        @if($billing->tevin_qr_code)
                                        <tr>
                                            <th>QR Code:</th>
                                            <td>
                                                <a href="{{ $billing->tevin_qr_code }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-qrcode me-2"></i>View QR Code
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($billing->tevin_error_message)
                                        <tr>
                                            <th>Error Message:</th>
                                            <td class="text-danger">{{ $billing->tevin_error_message }}</td>
                                        </tr>
                                        @endif
                                        @if($billing->tevin_submitted_at)
                                        <tr>
                                            <th>Submitted At:</th>
                                            <td>{{ $billing->tevin_submitted_at->format('d M Y H:i:s') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <a href="{{ route('finance.billing.show', $billing->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary float-end">
                                    <i class="fas fa-save me-2"></i>Update Billing
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
    }
    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validate due date is after billing date
        const billingDate = document.getElementById('billing_date');
        const dueDate = document.getElementById('due_date');

        function validateDates() {
            if (billingDate.value && dueDate.value) {
                if (dueDate.value < billingDate.value) {
                    dueDate.setCustomValidity('Due date must be after billing date');
                } else {
                    dueDate.setCustomValidity('');
                }
            }
        }

        billingDate.addEventListener('change', validateDates);
        dueDate.addEventListener('change', validateDates);
    });
</script>
@endpush
