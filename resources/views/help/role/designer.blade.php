@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-blue text-white">
        <h4 class="mb-0">
            <i class="fas fa-pencil-ruler me-2"></i>
            Design Engineer Help Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-primary">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Your Role:</strong> Design Engineer - Create fibre route designs, manage quotations, and coordinate with surveyors,Regional ICT Engineers and Account Managers.
        </div>

        <h3>Dashboard Overview</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h2 class="text-kp-yellow">2</h2>
                        <p>Pending Design Requests</p>
                    </div>
                    <div class="col-md-3">
                        <h2 class="text-secondary">0</h2>
                        <p>In Progress</p>
                    </div>
                    <div class="col-md-3">
                        <h2 class="text-kp-green">0</h2>
                        <p>Completed Designs</p>
                    </div>
                    <div class="col-md-3">
                        <h2 class="text-info">0</h2>
                        <p>Quotations Sent</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Design Workflow</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                        <p>1. Request<br>Received</p>
                    </div>
                    <div class="col-md-1">→</div>
                    <div class="col-md-3">
                        <i class="fas fa-drafting-compass fa-2x"></i>
                        <p>2. Design<br>Created</p>
                    </div>
                    <div class="col-md-1">→</div>
                    <div class="col-md-3">
                        <i class="fas fa-check-double fa-2x"></i>
                        <p>3. Review &<br>Approve</p>
                    </div>
                    <div class="col-md-1">→</div>
                    <div class="col-md-3">
                        <i class="fas fa-file-signature fa-2x"></i>
                        <p>4. Quotation<br>Sent</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Using the Kenya Fibre Dashboard</h3>
        <div class="card mb-4">
            <div class="card-body">
                <p>The Kenya Fibre Dashboard helps you plan routes and check available infrastructure:</p>
                <ul>
                    <li><strong>Existing Fibre Routes</strong> - View active network infrastructure</li>
                    <li><strong>Capacity Availability</strong> - Check bandwidth availability</li>
                    <li><strong>POP Locations</strong> - Identify nearest Points of Presence</li>
                    <li><strong>Distance Calculator</strong> - Measure route distances</li>
                </ul>
                <a href="{{ route('kenya.fibre.dashboard') }}" class="btn btn-sm btn-kp-primary">
                    <i class="fas fa-network-wired"></i> Open Fibre Dashboard
                </a>
            </div>
        </div>

        <h3>Creating a Quotation</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to <strong>Design Requests</strong> → <strong>Pending (2)</strong></li>
                    <li>Select a design request</li>
                    <li>Click <strong>"Create Quotation"</strong></li>
                    <li>Fill in pricing details:
                        <ul>
                            <li>Installation fee</li>
                            <li>Monthly recurring charge</li>
                            <li>Equipment costs</li>
                            <li>Maintenance fees</li>
                        </ul>
                    </li>
                    <li>Review and click <strong>"Send to Customer"</strong></li>
                </ol>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Tip:</strong> Use commercial routes from the "Darkfire Items" menu for standard pricing templates.
        </div>

    </div>
</div>
@endsection
