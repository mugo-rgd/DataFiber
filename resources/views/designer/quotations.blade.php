@extends('layouts.app')

@section('title', 'Quotations')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-file-invoice-dollar text-success"></i> Quotations
            </h1>
            <p class="text-muted">Manage your sent quotations</p>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Quotation #</th>
                            <th>Design Request</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Valid Until</th>
                            <th>Sent Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotations as $quotation)
                            <tr>
                                <td><strong>#{{ $quotation->quotation_number }}</strong></td>
                                <td>#{{ $quotation->designRequest->request_number }}</td>
                                <td>{{ $quotation->designRequest->customer->name }}</td>
                                <td>${{ number_format($quotation->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ match($quotation->status) {
                                        'draft' => 'secondary',
                                        'sent' => 'info',
                                        'accepted' => 'success',
                                        'rejected' => 'danger',
                                        default => 'light'
                                    } }}">
                                        {{ ucfirst($quotation->status) }}
                                    </span>
                                    @if($quotation->isExpired())
                                        <span class="badge bg-warning">Expired</span>
                                    @endif
                                </td>
                                <td>{{ $quotation->valid_until->format('M d, Y') }}</td>
                                <td>{{ $quotation->sent_at?->format('M d, Y') ?? 'Not sent' }}</td>
                                <td>
                                    <a href="{{ route('designer.quotations.show', $quotation) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($quotation->status === 'draft')
                                        <button class="btn btn-sm btn-success mt-1">
                                            <i class="fas fa-paper-plane"></i> Send
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No quotations found.</p>
                                    <p class="text-muted small">Quotations will appear here after you create them for design requests.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $quotations->links() }}
        </div>
    </div>
</div>
@endsection
