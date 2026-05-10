@extends('layouts.app')

@section('content')
<div class="container">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="fas fa-file-alt me-2 text-primary"></i> CSP Compliance Preview
        </h4>

        <span class="badge
            @if($record->status == 'draft') bg-secondary
            @elseif($record->status == 'submitted') bg-warning
            @elseif($record->status == 'approved') bg-success
            @else bg-dark @endif">
            {{ strtoupper($record->status) }}
        </span>
    </div>

    <!-- BASIC INFO CARD -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light">
            <strong>Basic Information</strong>
        </div>
        <div class="card-body row">

            <div class="col-md-6 mb-2">
                <strong>Licensee:</strong><br>
                {{ $record->licensee_name ?? '—' }}
            </div>

            <div class="col-md-6 mb-2">
                <strong>License No:</strong><br>
                {{ $record->license_no ?? '—' }}
            </div>

            <div class="col-md-6 mb-2">
                <strong>Financial Year:</strong><br>
                {{ $record->financial_year ?? '—' }}
            </div>

            <div class="col-md-6 mb-2">
                <strong>Quarter:</strong><br>
                {{ $record->quarter ?? '—' }}
            </div>

        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="card shadow-sm mb-3">
        <div class="card-body d-flex flex-wrap gap-2">

            <!-- PRINT -->
            <a href="{{ route('csp.print', $record->id) }}"
               class="btn btn-primary">
                <i class="fas fa-print me-1"></i> Print CAK PDF
            </a>

            <!-- EDIT -->
            <a href="{{ route('csp.edit', $record->id) }}"
               class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>

            <!-- SAVE / SUBMIT -->
            @if($record->status === 'draft')
                <form method="POST" action="{{ route('csp.approve', $record->id) }}">
                    @csrf
                    <button class="btn btn-success">
                        <i class="fas fa-paper-plane me-1"></i> Submit to CAK
                    </button>
                </form>
            @endif

            <!-- EMAIL -->
            @if($record->status !== 'draft')
                <form action="{{ route('csp.email-cak', $record->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-success">
                        <i class="fas fa-envelope me-1"></i> Email to CAK
                    </button>
                </form>
            @endif

            <!-- DELETE -->
            <form method="POST" action="{{ route('csp.destroy', $record->id) }}"
                  onsubmit="return confirm('Delete this record?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </form>

        </div>
    </div>

    <!-- JSON DATA PREVIEW (OPTIONAL DEBUG) -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <strong>Captured Form Data</strong>
        </div>
        <div class="card-body">

            <pre style="font-size: 12px;">
{{ json_encode($record->form_data, JSON_PRETTY_PRINT) }}
            </pre>

        </div>
    </div>

</div>
@endsection
