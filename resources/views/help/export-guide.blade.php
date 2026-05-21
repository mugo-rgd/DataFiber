@extends('layouts.help')

@section('help-content')
<h1><i class="fas fa-download text-info me-2"></i> Export Guide</h1>
<hr>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    Export compliance data to Excel, CSV, or PDF format.
</div>

<h3>Export Options</h3>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-file-excel fa-3x text-kp-green mb-2"></i>
                <h5>Excel Export</h5>
                <p>Export data to Excel (.xlsx) format for analysis</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-file-csv fa-3x text-kp-blue mb-2"></i>
                <h5>CSV Export</h5>
                <p>Export data to CSV format for database import</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                <h5>PDF Export</h5>
                <p>Export data to PDF format for official reporting</p>
            </div>
        </div>
    </div>
</div>

<h3>How to Export Data</h3>
<div class="card mb-4">
    <div class="card-body">
        <ol>
            <li>Select the data type (ASP, CSP, NFP, or Combined)</li>
            <li>Apply filters to narrow down the data:
                <ul>
                    <li>Status (Draft, Submitted, Approved, etc.)</li>
                    <li>Financial Year (e.g., 2025/2026)</li>
                    <li>Quarter (Q1, Q2, Q3, Q4)</li>
                    <li>Date Range (Custom start and end dates)</li>
                </ul>
            </li>
            <li>Choose export format (Excel, CSV, or PDF)</li>
            <li>Click the "Export" button</li>
            <li>The file will automatically download to your computer</li>
        </ol>
    </div>
</div>

<h3>Quick Links</h3>
<ul>
    @if(Route::has('export.index'))
        <li><a href="{{ route('export.index') }}">Go to Export Module</a></li>
    @else
        <li><a href="{{ url('/export') }}">Go to Export Module</a></li>
    @endif
    <li><a href="{{ route('help.faq') }}">View FAQ</a></li>
    <li><a href="{{ route('help.contact') }}">Contact Support</a></li>
</ul>

<div class="alert alert-info mt-4">
    <i class="fas fa-lightbulb me-2"></i>
    <strong>Tip:</strong> Use filters before exporting to reduce file size and get more relevant data.
</div>
@endsection
