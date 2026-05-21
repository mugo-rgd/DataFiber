@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-blue text-white">
        <h4 class="mb-0">
            <i class="fas fa-file-signature me-2"></i>
            My Leases Guide
        </h4>
    </div>
    <div class="card-body">

        <h3>Viewing Your Leases</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to <strong>My Leases</strong> from the main menu</li>
                    <li>View all your active fibre leases</li>
                    <li>Click on any lease to see details:
                        <ul>
                            <li>Lease ID and type</li>
                            <li>Bandwidth capacity</li>
                            <li>Contract period</li>
                            <li>Monthly recurring charge</li>
                            <li>Installation date</li>
                        </ul>
                    </li>
                </ol>
            </div>
        </div>

        <h3>Lease Actions</h3>
        <div class="row mb-4">
            <div class="col-md-4 text-center">
                <i class="fas fa-chart-line fa-2x text-kp-green"></i>
                <p><strong>Upgrade Bandwidth</strong><br>Request higher capacity</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-sync-alt fa-2x text-kp-yellow"></i>
                <p><strong>Renew Lease</strong><br>Extend contract term</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-download fa-2x text-info"></i>
                <p><strong>Download Contract</strong><br>Get signed agreement</p>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Tip:</strong> Review your leases before expiration to avoid service interruption.
        </div>

    </div>
</div>
@endsection
