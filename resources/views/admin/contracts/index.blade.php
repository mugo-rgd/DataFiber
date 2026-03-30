@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Contracts</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Contract #</th>
                                    <th>Customer</th>
                                    <th>Quotation #</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contracts as $contract)
                                <tr>
                                    <td>{{ $contract->contract_number }}</td>
                                    <td>{{ $contract->quotation->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $contract->quotation->quotation_number }}</td>
                                    <td>
                                        <span class="badge bg-{{ $contract->status === 'approved' ? 'success' : ($contract->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($contract->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $contract->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.contracts.show', $contract) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($contract->status === 'draft')
                                        <a href="{{ route('admin.contracts.edit', $contract) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $contracts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
