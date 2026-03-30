@extends('layouts.app')

@section('title', 'Fibre Link Inventory - Dark Fibre CRM')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="dashboard-header bg-gradient-primary text-white py-2 py-sm-3 py-md-4">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center">
                <div class="col-12 col-lg-8 mb-2 mb-lg-0">
                    <div class="d-flex align-items-center">
                        <div class="header-icon me-2 me-sm-3">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="mb-1">Fibre Link Inventory</h1>
                            <p class="mb-0 opacity-75">Manage and view all fibre link conversion data</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end">
                        <a href="{{ route('conversion-data.create') }}" class="btn btn-light">
                            <i class="fas fa-plus me-1"></i> Add New Link
                        </a>
                        <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-3">
        <div class="row g-2 g-sm-3 mb-3">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 bg-primary bg-opacity-10">
                    <div class="card-body p-2 p-sm-3">
                        <div class="text-muted small mb-1">Total Contracts</div>
                        <div class="h4 mb-0">{{ $totals['total_contracts'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 bg-success bg-opacity-10">
                    <div class="card-body p-2 p-sm-3">
                        <div class="text-muted small mb-1">Monthly Value (USD)</div>
                        <div class="h4 mb-0">${{ number_format($totals['total_monthly_value_usd'], 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 bg-warning bg-opacity-10">
                    <div class="card-body p-2 p-sm-3">
                        <div class="text-muted small mb-1">Monthly Value (KES)</div>
                        <div class="h4 mb-0">KSh{{ number_format($totals['total_monthly_value_kes'], 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 bg-info bg-opacity-10">
                    <div class="card-body p-2 p-sm-3">
                        <div class="text-muted small mb-1">Total Contract (USD)</div>
                        <div class="h4 mb-0">${{ number_format($totals['total_contract_value_usd'], 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 bg-danger bg-opacity-10">
                    <div class="card-body p-2 p-sm-3">
                        <div class="text-muted small mb-1">Total Contract (KES)</div>
                        <div class="h4 mb-0">KSh{{ number_format($totals['total_contract_value_kes'], 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 bg-secondary bg-opacity-10">
                    <div class="card-body p-2 p-sm-3">
                        <div class="text-muted small mb-1">Total Distance</div>
                        <div class="h4 mb-0">{{ number_format($totals['total_distance'], 1) }} km</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-3">
        <div class="card border-0 shadow-sm">
          <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('conversion-data.summary-view') }}'">
    <i class="fas fa-plus me-1"></i> Summarised Data
</button>
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">

                    <h5 class="mb-2 mb-md-0">All Fibre Links</h5>

                    <!-- Search and Filters -->
                    <div class="d-flex flex-wrap gap-2">
                        <!-- Search Form -->
                        <form method="GET" action="{{ route('conversion-data.index') }}" class="d-flex">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control"
                                       placeholder="Search..." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Filter Button -->
                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                            <i class="fas fa-filter me-1"></i> Filters
                        </button>

                        <!-- Clear Filters -->
                        @if(request()->hasAny(['search', 'customer', 'link_class', 'min_duration', 'min_cores', 'min_distance']))
                        <a href="{{ route('conversion-data.index') }}" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times me-1"></i> Clear
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Filter Collapse -->
                <div class="collapse @if(request()->hasAny(['customer', 'link_class', 'min_duration', 'min_cores', 'min_distance'])) show @endif"
                     id="filterCollapse">
                    <form method="GET" action="{{ route('conversion-data.index') }}" class="mt-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label small">Customer</label>
                                <select name="customer" class="form-select form-select-sm">
                                    <option value="">All Customers</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer }}"
                                                {{ request('customer') == $customer ? 'selected' : '' }}>
                                            {{ $customer }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Link Class</label>
                                <select name="link_class" class="form-select form-select-sm">
                                    <option value="">All Classes</option>
                                    @foreach($linkClasses as $class)
                                        <option value="{{ $class }}"
                                                {{ request('link_class') == $class ? 'selected' : '' }}>
                                            {{ $class }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Min Duration (Years)</label>
                                <input type="number" name="min_duration" class="form-control form-control-sm"
                                       value="{{ request('min_duration') }}" placeholder="0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Min Cores</label>
                                <input type="number" name="min_cores" class="form-control form-control-sm"
                                       value="{{ request('min_cores') }}" placeholder="0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Min Distance (km)</label>
                                <input type="number" name="min_distance" class="form-control form-control-sm"
                                       value="{{ request('min_distance') }}" placeholder="0">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-sm btn-primary w-100">Apply</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">
                                    <a href="{{ request()->fullUrlWithQuery(['order_by' => 'customer_name', 'order_dir' => request('order_dir') == 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-decoration-none text-dark">
                                        Customer
                                        @if(request('order_by') == 'customer_name')
                                            <i class="fas fa-sort-{{ request('order_dir') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="border-0">Ref</th>
                                <th class="border-0">Route</th>
                                <th class="border-0">Links</th>
                                <th class="border-0">Class</th>
                                <th class="border-0">Cores</th>
                                <th class="border-0">Distance</th>
                                <th class="border-0">Monthly USD</th>
                                <th class="border-0">Monthly KES</th>
                                <th class="border-0">Duration</th>
                                <th class="border-0">Total USD</th>
                                <th class="border-0">Total KES</th>
                                <th class="border-0 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $item)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $item->customer_name }}</div>
                                        @if($item->customer_id)
                                            <small class="text-muted">ID: {{ $item->customer_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->customer_ref)
                                            <span class="badge bg-light text-dark">{{ $item->customer_ref }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->route_name ?? '-' }}</td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;"
                                             title="{{ $item->links_name }}">
                                            {{ $item->links_name }}
                                        </div>
                                    </td>
                                    <td>
    @php
        $badgeClass = match($item->link_class) {
            'PREMIUM' => 'success',
            'METRO' => 'info',
            'STANDARD' => 'primary',
            'BASIC' => 'secondary',
            'NON PREMIUM' => 'dark',
            default => 'warning',
        };
    @endphp

    <span class="badge bg-{{ $badgeClass }} text-uppercase fw-semibold">
        {{ $item->link_class }}
    </span>
</td>
                                    <td>
                                        @if($item->cores_leased)
                                            <span class="badge bg-primary">{{ $item->cores_leased }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->distance_km)
                                            {{ number_format($item->distance_km, 1) }} km
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @if($item->monthly_link_value_usd)
                                            ${{ number_format($item->monthly_link_value_usd, 2) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @if($item->monthly_link_kes)
                                            KSh{{ number_format($item->monthly_link_kes, 2) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->contract_duration_yrs)
                                            <span class="badge bg-secondary">{{ $item->contract_duration_yrs }} yrs</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @if($item->total_contract_value_usd)
                                            ${{ number_format($item->total_contract_value_usd, 2) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @if($item->total_contract_value_kes)
                                            KSh{{ number_format($item->total_contract_value_kes, 2) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('conversion-data.show', $item->id) }}"
                                               class="btn btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('conversion-data.edit', $item->id) }}"
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('conversion-data.destroy', $item->id) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this link?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center py-4">
                                        <i class="fas fa-database fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">No fibre links found</p>
                                        <a href="{{ route('conversion-data.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i> Add First Link
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
@if($data->hasPages() || $data->total() > $data->perPage())
    <div class="card-footer bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $data->firstItem() ?? 0 }} to {{ $data->lastItem() ?? 0 }} of {{ $data->total() }} entries
            </div>
            <nav aria-label="Page navigation">
                {{ $data->links() }}
            </nav>
        </div>
    </div>
@endif
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
    <div class="list-group">
        <!-- Option 1: Using individual routes -->
        <a href="{{ route('conversion-data.export.excel') }}"
           class="list-group-item list-group-item-action"
           onclick="applyExportFilters(this)">
            <i class="fas fa-file-excel text-success me-2"></i> Export as Excel
        </a>

        <a href="{{ route('conversion-data.export.csv') }}"
           class="list-group-item list-group-item-action"
           onclick="applyExportFilters(this)">
            <i class="fas fa-file-csv text-info me-2"></i> Export as CSV
        </a>

        <a href="{{ route('conversion-data.export.pdf') }}"
           class="list-group-item list-group-item-action"
           onclick="applyExportFilters(this)">
            <i class="fas fa-file-pdf text-danger me-2"></i> Export as PDF
        </a>

        <!-- Option 2: Using single route with format parameter -->
        <a href="{{ route('conversion-data.export', ['format' => 'excel']) }}"
           class="list-group-item list-group-item-action d-none">
            <i class="fas fa-file-excel text-success me-2"></i> Export as Excel
        </a>
    </div>
</div>
        </div>
    </div>
</div>

<style>
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    border-radius: 0.5rem;
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    border-top: none;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
}

.badge {
    font-weight: 500;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.text-nowrap {
    white-space: nowrap;
}

.input-group-sm {
    width: 250px;
}

@media (max-width: 768px) {
    .input-group-sm {
        width: 100%;
        margin-bottom: 0.5rem;
    }

    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus search field
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }

    // Show filter collapse if any filter is active
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = ['customer', 'link_class', 'min_duration', 'min_cores', 'min_distance']
        .some(param => urlParams.has(param));

    if (hasFilters) {
        const filterCollapse = new bootstrap.Collapse(document.getElementById('filterCollapse'), {
            toggle: false
        });
        filterCollapse.show();
    }

    // Confirm deletion
    document.querySelectorAll('form[onsubmit]').forEach(form => {
        form.onsubmit = function(e) {
            return confirm('Are you sure you want to delete this fibre link? This action cannot be undone.');
        };
    });

    // Table row click for mobile
    if (window.innerWidth < 768) {
        document.querySelectorAll('tbody tr').forEach(row => {
            const viewLink = row.querySelector('a[href*="show"]');
            if (viewLink) {
                row.style.cursor = 'pointer';
                row.addEventListener('click', function(e) {
                    if (!e.target.closest('a') && !e.target.closest('button') && !e.target.closest('form')) {
                        window.location = viewLink.href;
                    }
                });
            }
        });
    }

    function applyExportFilters(linkElement) {
    // Get current filter parameters
    const urlParams = new URLSearchParams(window.location.search);

    // Get selected item IDs for bulk export
    const selectedIds = Array.from(document.querySelectorAll('.select-item:checked'))
        .map(item => item.value)
        .join(',');

    // Add current filters to export URL
    let url = linkElement.href;
    urlParams.forEach((value, key) => {
        if (value) {
            url += (url.includes('?') ? '&' : '?') + key + '=' + encodeURIComponent(value);
        }
    });

    // Add selected IDs if any
    if (selectedIds) {
        url += (url.includes('?') ? '&' : '?') + 'selected_ids=' + selectedIds;
    }

    // Update the link and trigger download
    linkElement.href = url;
    return true; // Allow normal navigation
}

// Alternative: Update all export links on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update export links with current filters
    document.querySelectorAll('a[href*="export"]').forEach(link => {
        const originalHref = link.href;
        const urlParams = new URLSearchParams(window.location.search);

        let newUrl = originalHref;
        urlParams.forEach((value, key) => {
            if (value && !['page', 'per_page', 'order_by', 'order_dir'].includes(key)) {
                newUrl += (newUrl.includes('?') ? '&' : '?') + key + '=' + encodeURIComponent(value);
            }
        });

        link.href = newUrl;
    });
});
});
</script>
@endsection
