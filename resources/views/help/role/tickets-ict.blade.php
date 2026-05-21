@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-blue text-white">
        <h4 class="mb-0">
            <i class="fas fa-ticket-alt me-2"></i>
            ICT Ticket Management Guide
        </h4>
    </div>
    <div class="card-body">

        <h3>Managing Support Tickets</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to <strong>Tickets</strong> from the main menu</li>
                    <li>View assigned tickets in your queue</li>
                    <li>Click on a ticket to view details</li>
                    <li>Update status as you work on the issue</li>
                    <li>Add internal notes and customer responses</li>
                    <li>Mark as resolved when complete</li>
                </ol>
            </div>
        </div>

        <h3>Ticket Prioritization</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Priority</th><th>Response SLA</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <tr><td class="bg-danger text-white">Critical</td><td>15 min</td><td>Drop everything, resolve immediately</td></tr>
                    <tr><td class="bg-kp-yellow">High</td><td>1 hour</td><td>Prioritize after critical</td></tr>
                    <tr><td class="bg-info text-white">Medium</td><td>4 hours</td><td>Work within SLA</td></tr>
                    <tr><td class="bg-secondary text-white">Low</td><td>24 hours</td><td>Schedule as time permits</td></tr>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Tip:</strong> Always update ticket status and add notes so customers and managers can track progress.
        </div>

    </div>
</div>
@endsection
