@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="fas fa-file-alt me-2 text-primary"></i> ASP Compliance Preview
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

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light">
            <strong>Actions</strong>
        </div>

        <div class="card-body d-flex flex-wrap gap-2">

            <a href="{{ route('asp.print', $record->id) }}" class="btn btn-primary">
                <i class="fas fa-print me-1"></i> Print CAK PDF
            </a>

            <a href="{{ route('asp.edit', $record->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>

            @if($record->status === 'draft')
                <form method="POST" action="{{ route('asp.approve', $record->id) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-success"
                            onclick="return confirm('Submit this ASP return for approval?')">
                        <i class="fas fa-paper-plane me-1"></i> Submit
                    </button>
                </form>
            @endif

            <form action="{{ route('asp.email-cak', $record->id) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-success"
                        onclick="return confirm('Email this ASP return to CAK?')">
                    <i class="fas fa-envelope me-1"></i> Email to CAK
                </button>
            </form>

            <a href="{{ route('asp.index') }}" class="btn btn-dark">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>

        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <strong>Captured Form Data</strong>
        </div>

        <div class="card-body">
            <pre class="mb-0" style="font-size:12px; white-space:pre-wrap;">{{ json_encode($record->form_data, JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>

</div>
@endsection
