{{-- resources/views/customer-portal/statements/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>My Statements
                    </h4>
                    <a href="{{ route('customer.statements.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus-circle me-1"></i>Generate New Statement
                    </a>
                </div>
                <div class="card-body">
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

                    @if($statements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Statement #</th>
                                        <th>Date</th>
                                        <th>Period</th>
                                        <th class="text-end">Opening Balance</th>
                                        <th class="text-end">Closing Balance</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statements as $statement)
                                    <tr>
                                        <td>
                                            <strong>{{ $statement->statement_number }}</strong>
                                        </td>
                                        <td>{{ $statement->statement_date->format('d M Y') }}</td>
                                        <td>
                                            {{ $statement->period_start->format('d M Y') }} -
                                            {{ $statement->period_end->format('d M Y') }}
                                        </td>
                                        <td class="text-end">${{ number_format($statement->opening_balance, 2) }}</td>
                                        <td class="text-end {{ $statement->closing_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            <strong>${{ number_format($statement->closing_balance, 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($statement->status == 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @elseif($statement->status == 'generated')
                                                <span class="badge bg-info">Generated</span>
                                            @elseif($statement->status == 'sent')
                                                <span class="badge bg-success">Sent</span>
                                            @elseif($statement->status == 'viewed')
                                                <span class="badge bg-primary">Viewed</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('statements.download', $statement->id) }}"
                                                   class="btn btn-info"
                                                   title="Download PDF"
                                                   target="_blank">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="{{ route('customer.statements.show', $statement->id) }}"
                                                   class="btn btn-primary"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $statements->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No Statements Found</h5>
                            <p class="text-muted mb-4">You haven't generated any statements yet.</p>
                            <a href="{{ route('customer.statements.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Generate Your First Statement
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table > :not(caption) > * > * {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.5em 0.85em;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
</style>
@endpush
