@extends('layouts.app')

@section('content')
<div class="container">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="fas fa-network-wired me-2 text-warning"></i> NFP Compliance Preview
        </h4>

        <span class="badge
            @if($record->status == 'draft') bg-secondary
            @elseif($record->status == 'submitted') bg-warning
            @elseif($record->status == 'approved') bg-success
            @elseif($record->status == 'submitted_to_cak') bg-info
            @else bg-dark @endif">
            {{ strtoupper(str_replace('_', ' ', $record->status)) }}
        </span>
    </div>

    <!-- BASIC INFO -->
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

    <!-- ACTIONS -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light">
            <strong>Actions</strong>
        </div>

        <div class="card-body d-flex flex-wrap gap-2">

            <!-- PRINT -->
            <a href="{{ route('nfp.print', $record->id) }}" class="btn btn-primary">
                <i class="fas fa-print me-1"></i> Print CAK PDF
            </a>

            <!-- EDIT -->
            <a href="{{ route('nfp.edit', $record->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>

            <!-- SUBMIT -->
            @if($record->status === 'draft')
                <form method="POST" action="{{ route('nfp.approve', $record->id) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-success"
                            onclick="return confirm('Submit this NFP return for approval?')">
                        <i class="fas fa-paper-plane me-1"></i> Submit
                    </button>
                </form>
            @endif

            <!-- EMAIL -->
            @if($record->status !== 'draft')
                <form action="{{ route('nfp.email-cak', $record->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-success"
                            onclick="return confirm('Email this NFP return to CAK?')">
                        <i class="fas fa-envelope me-1"></i> Email to CAK
                    </button>
                </form>
            @endif

            <!-- DELETE -->
            <form method="POST" action="{{ route('nfp.destroy', $record->id) }}"
                  onsubmit="return confirm('Delete this record?')" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </form>

            <!-- BACK -->
            <a href="{{ route('nfp.index') }}" class="btn btn-dark">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>

        </div>
    </div>

    <!-- DATA PREVIEW -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <strong>Captured Form Data</strong>
        </div>

        <div class="card-body">
            <pre style="font-size:12px; white-space:pre-wrap;">
{{ json_encode($record->form_data, JSON_PRETTY_PRINT) }}
            </pre>
        </div>
    </div>

</div>
@endsection
