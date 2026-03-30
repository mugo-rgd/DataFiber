@extends('layouts.app')

@section('title', 'My Support Tickets')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Support Tickets</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> New Ticket
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Support Tickets</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Tickets Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="ticketsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Created Date</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data - replace with dynamic data from controller -->
                                <tr>
                                    <td>TKT-001</td>
                                    <td>Network connectivity issue</td>
                                    <td><span class="badge badge-warning">Open</span></td>
                                    <td><span class="badge badge-danger">High</span></td>
                                    <td>2024-01-15</td>
                                    <td>2024-01-16</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TKT-002</td>
                                    <td>Billing inquiry</td>
                                    <td><span class="badge badge-success">Resolved</span></td>
                                    <td><span class="badge badge-info">Normal</span></td>
                                    <td>2024-01-10</td>
                                    <td>2024-01-12</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div class="text-center py-4 d-none" id="emptyState">
                        <i class="fas fa-ticket-alt fa-3x text-gray-300 mb-3"></i>
                        <h4 class="text-gray-500">No Support Tickets</h4>
                        <p class="text-gray-500">You haven't created any support tickets yet.</p>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Your First Ticket
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Simple script to show empty state if no tickets
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('ticketsTable');
        const emptyState = document.getElementById('emptyState');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        if (rows.length === 0) {
            table.classList.add('d-none');
            emptyState.classList.remove('d-none');
        }
    });
</script>
@endsection
