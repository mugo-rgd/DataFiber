@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3>Customer Details: {{ $user->company_name ?? $user->name }}</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <h5>Customer Information</h5>
                <p><strong>ID:</strong> {{ $user->id }}</p>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
                <p><strong>Status:</strong> {{ $user->status }}</p>
                <p><strong>Role:</strong> {{ $user->role }}</p>
                <p><strong>Company:</strong> {{ $user->company_name ?? 'N/A' }}</p>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">Financial Summary</div>
                        <div class="card-body">
                            <p><strong>Total Billed:</strong> KSh {{ number_format($financialSummary['total_billed'], 2) }}</p>
                            <p><strong>Total Paid:</strong> KSh {{ number_format($financialSummary['total_paid'], 2) }}</p>
                            <p><strong>Outstanding:</strong> KSh {{ number_format($financialSummary['total_outstanding'], 2) }}</p>
                            <p><strong>Overdue:</strong> KSh {{ number_format($financialSummary['overdue_amount'], 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">Lease Statistics</div>
                        <div class="card-body">
                            <p><strong>Total Leases:</strong> {{ $leaseStats['total_leases'] }}</p>
                            <p><strong>Active Leases:</strong> {{ $leaseStats['active_leases'] }}</p>
                            <p><strong>Pending Leases:</strong> {{ $leaseStats['pending_leases'] }}</p>
                            <p><strong>Monthly Revenue:</strong> KSh {{ number_format($leaseStats['total_monthly_revenue'], 2) }}</p>
                            <p><strong>Contract Value:</strong> KSh {{ number_format($leaseStats['total_contract_value'], 2) }}</p>
                            <p><strong>Leased Distance:</strong> {{ number_format($leaseStats['leased_distance_km'], 2) }} km</p>
                            <p><strong>Leased Cores:</strong> {{ $leaseStats['leased_cores'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">Support Tickets</div>
                        <div class="card-body">
                            <p><strong>Total Tickets:</strong> {{ $ticketStats['total_tickets'] }}</p>
                            <p><strong>Open Tickets:</strong> {{ $ticketStats['open_tickets'] }}</p>
                            <p><strong>Resolved Tickets:</strong> {{ $ticketStats['resolved_tickets'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">Quotations</div>
                        <div class="card-body">
                            <p><strong>Total Quotations:</strong> {{ $quotationStats['total_quotations'] }}</p>
                            <p><strong>Pending:</strong> {{ $quotationStats['pending_quotations'] }}</p>
                            <p><strong>Won:</strong> {{ $quotationStats['won_quotations'] }}</p>
                            <p><strong>Pipeline Value:</strong> KSh {{ number_format($quotationStats['total_value_pipeline'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Back to Customers</a>
                @if(isset($user->id))
                <a href="{{ route('admin.customers.export', $user->id) }}" class="btn btn-primary">Export Data</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
