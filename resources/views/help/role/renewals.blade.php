@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-green text-white">
        <h4 class="mb-0">
            <i class="fas fa-sync-alt me-2"></i>
            Contract Renewals Guide
        </h4>
    </div>
    <div class="card-body">

        <h3>Lease Expiration Monitoring</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6">
                        <h2 class="text-kp-blue">286</h2>
                        <p>Active Leases</p>
                    </div>
                    <div class="col-md-6">
                        <h2 class="text-kp-yellow">2</h2>
                        <p>Expiring in 30 Days</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Renewal Process</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Review expiring leases 30 days in advance</li>
                    <li>Contact customer to discuss renewal terms</li>
                    <li>Generate renewal quotation</li>
                    <li>Get customer approval</li>
                    <li>Process renewal in system</li>
                    <li>Send confirmation to customer</li>
                </ol>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Tip:</strong> Offer incentives for early renewal (e.g., 5% discount for 12-month commitment).
        </div>

    </div>
</div>
@endsection
