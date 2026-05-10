@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">
                <i class="fas fa-file-alt me-2 text-primary"></i> CAK Compliance Dashboard
            </h3>
            <small class="text-muted">ASP, CSP and NFP compliance returns management</small>
        </div>

        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-plus me-1"></i> New Return
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('asp.create') }}">
                        <i class="fas fa-server me-2 text-primary"></i> ASP Return
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('csp.create') }}">
                        <i class="fas fa-envelope me-2 text-success"></i> CSP Return
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('nfp.create') }}">
                        <i class="fas fa-network-wired me-2 text-warning"></i> NFP Return
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <h6 class="text-muted">ASP Returns</h6>
                    <h2>{{ $aspCount ?? 0 }}</h2>
                    <a href="{{ route('asp.index') }}" class="btn btn-sm btn-primary">
                        View ASP
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h6 class="text-muted">CSP Returns</h6>
                    <h2>{{ $cspCount ?? 0 }}</h2>
                    <a href="{{ route('csp.index') }}" class="btn btn-sm btn-success">
                        View CSP
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <h6 class="text-muted">NFP Returns</h6>
                    <h2>{{ $nfpCount ?? 0 }}</h2>
                    <a href="{{ route('nfp.index') }}" class="btn btn-sm btn-warning">
                        View NFP
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- STATUS SUMMARY --}}
    <div class="row mb-4">

        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Drafts</h6>
                    <h3 class="text-secondary">{{ $draftCount ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Submitted</h6>
                    <h3 class="text-warning">{{ $submittedCount ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Sent to CAK</h6>
                    <h3 class="text-info">{{ $sentCount ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Approved</h6>
                    <h3 class="text-success">{{ $approvedCount ?? 0 }}</h3>
                </div>
            </div>
        </div>

    </div>

    {{-- RECENT RETURNS --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>Recent Compliance Returns</strong>
            <small class="text-muted">Latest ASP, CSP and NFP records</small>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Type</th>
                        <th>Licensee</th>
                        <th>License No</th>
                        <th>Financial Year</th>
                        <th>Quarter</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th width="220">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse(($recentReturns ?? []) as $item)
                        <tr>
                            <td>
                                <span class="badge
                                    @if($item['type'] === 'ASP') bg-primary
                                    @elseif($item['type'] === 'CSP') bg-success
                                    @else bg-warning text-dark
                                    @endif">
                                    {{ $item['type'] }}
                                </span>
                            </td>

                            <td>{{ $item['record']->licensee_name ?? '—' }}</td>
                            <td>{{ $item['record']->license_no ?? '—' }}</td>
                            <td>{{ $item['record']->financial_year ?? '—' }}</td>
                            <td>{{ $item['record']->quarter ?? '—' }}</td>

                            <td>
                                <span class="badge
                                    @if($item['record']->status === 'draft') bg-secondary
                                    @elseif($item['record']->status === 'submitted') bg-warning
                                    @elseif($item['record']->status === 'submitted_to_cak') bg-info
                                    @elseif($item['record']->status === 'approved') bg-success
                                    @else bg-dark
                                    @endif">
                                    {{ strtoupper(str_replace('_', ' ', $item['record']->status ?? 'draft')) }}
                                </span>
                            </td>

                            <td>{{ optional($item['record']->created_at)->format('d M Y H:i') }}</td>

                            <td>
                                <a href="{{ route(strtolower($item['type']) . '.show', $item['record']->id) }}"
                                   class="btn btn-sm btn-info">
                                    View
                                </a>

                                <a href="{{ route(strtolower($item['type']) . '.print', $item['record']->id) }}"
                                   class="btn btn-sm btn-dark">
                                    PDF
                                </a>

                                <a href="{{ route(strtolower($item['type']) . '.edit', $item['record']->id) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                No compliance returns found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
