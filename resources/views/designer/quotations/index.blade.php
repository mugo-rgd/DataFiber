@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">My Quotations</h4>
                </div>
                <div class="card-body">
                    @if($quotations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Quotation #</th>
                                        <th>Design Request</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Valid Until</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotations as $quotation)
                                        <tr>
                                            <td>{{ $quotation->quotation_number }}</td>
                                            <td>{{ $quotation->designRequest->title }}</td>
                                            <td>{{ $quotation->designRequest->customer->name }}</td>
                                            <td>${{ number_format($quotation->total_amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $quotation->status === 'sent' ? 'success' : ($quotation->status === 'draft' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($quotation->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $quotation->valid_until->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('designer.quotations.show', $quotation) }}"
                                                   class="btn btn-sm btn-primary">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $quotations->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p class="mb-0">No quotations found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
