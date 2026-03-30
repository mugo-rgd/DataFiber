@extends('layouts.app')

@section('title', 'Darkfire Items Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-fire text-dark"></i> Darkfire Items Management
            </h1>
            <p class="text-muted">Manage Commercial Routes and Colocation Items</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('designer.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Table Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="btn-group" role="group">
                            <a href="{{ route('designer.darkfire-items', ['table' => 'commercial_routes']) }}"
                               class="btn btn-{{ $table === 'commercial_routes' ? 'primary' : 'outline-primary' }}">
                                <i class="fas fa-route me-2"></i> Commercial Routes
                            </a>
                            <a href="{{ route('designer.darkfire-items', ['table' => 'colocation_list']) }}"
                               class="btn btn-{{ $table === 'colocation_list' ? 'primary' : 'outline-primary' }}">
                                <i class="fas fa-server me-2"></i> Colocation List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            {{ $table === 'commercial_routes' ? 'Commercial Routes' : 'Colocation List' }}
                        </h5>
                        <a href="{{ route('designer.darkfire-items.create', $table) }}"
                           class="btn btn-success">
                            <i class="fas fa-plus me-2"></i> Add New
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        @foreach($columns as $key => $label)
                                            <th>{{ $label }}</th>
                                        @endforeach
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            @foreach($columns as $key => $label)
                                                <td>
                                                    @if($key === 'availability' || $key === 'fibrestatus')
                                                        <span class="badge bg-{{ $item->$key === 'YES' || $item->$key === 'Active' ? 'success' : 'danger' }}">
                                                            {{ $item->$key }}
                                                        </span>
                                                    @elseif($key === 'created_at')
                                                        {{ $item->$key->format('Y-m-d H:i') }}
                                                    @elseif(in_array($key, ['unit_cost_per_core_per_km_per_month', 'approx_distance_km',
                                                             'capital_expenditure', 'power_kw', 'space_sqm', 'oneoff_rate',
                                                             'recurrent_per_Annum', 'monthly_price_usd', 'setup_fee_usd']))
                                                        {{ number_format($item->$key, 2) }}
                                                    @else
                                                        {{ $item->$key }}
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('designer.darkfire-items.edit', [$table, $item->id ?? $item->service_id]) }}"
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($table === 'commercial_routes')
                                                        <form action="{{ route('designer.darkfire-items.toggle', [$table, $item->id]) }}"
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-{{ $item->availability === 'YES' ? 'danger' : 'success' }}"
                                                                    title="{{ $item->availability === 'YES' ? 'Deactivate' : 'Activate' }}">
                                                                <i class="fas fa-{{ $item->availability === 'YES' ? 'times' : 'check' }}"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('designer.darkfire-items.toggle', [$table, $item->service_id]) }}"
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-{{ $item->fibrestatus === 'Active' ? 'danger' : 'success' }}"
                                                                    title="{{ $item->fibrestatus === 'Active' ? 'Deactivate' : 'Activate' }}">
                                                                <i class="fas fa-{{ $item->fibrestatus === 'Active' ? 'times' : 'check' }}"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('designer.darkfire-items.destroy', [$table, $item->id ?? $item->service_id]) }}"
                                                          method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $items->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No items found. Add your first item!</p>
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
    .table th {
        font-weight: 600;
        background-color: #f8f9fc;
    }
    .btn-group .btn {
        border-radius: 0.25rem;
        margin-right: 0.25rem;
    }
</style>
@endpush
