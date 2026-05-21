@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-blue text-white">
        <h4 class="mb-0">
            <i class="fas fa-file-signature me-2"></i>
            Quotations Guide
        </h4>
    </div>
    <div class="card-body">

        <h3>Creating a Quotation</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to <strong>Design Requests</strong> → <strong>Pending</strong></li>
                    <li>Select a design request</li>
                    <li>Click <strong>"Create Quotation"</strong></li>
                    <li>Add pricing details:
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

        <h3>Quotation Statuses</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Status</th><th>Meaning</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <tr><td class="text-kp-yellow">Draft</td><td>In progress</td><td>Complete and send</td></tr>
                    <tr><td class="text-info">Sent</td><td>Delivered to customer</td><td>Awaiting response</td></tr>
                    <tr><td class="text-kp-green">Accepted</td><td>Customer approved</td><td>Proceed to installation</td></tr>
                    <tr><td class="text-danger">Rejected</td><td>Customer declined</td><td>Revise or close</td></tr>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Tip:</strong> Use commercial route templates from "Darkfire Items" for standard pricing.
        </div>

    </div>
</div>
@endsection
