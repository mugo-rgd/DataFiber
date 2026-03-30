@extends('layouts.app')

@section('title', 'Account Manager Analytics - Admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar me-2"></i>Account Manager Analytics
        </h1>
        <a href="{{ route('admin.account-managers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Managers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['total_managers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Managers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['active_managers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['total_customers_managed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avg Customers/Manager</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['avg_customers_per_manager'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Managers Performance Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Account Manager Performance</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Manager</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Total Customers</th>
                            <th>Active Customers</th>
                            <th>Inactive Customers</th>
                            <th>Joined</th>
                            <th>This Month</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($managers as $manager)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2"
                                         style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 14px;">
                                        {{ strtoupper(substr($manager['name'], 0, 1)) }}
                                    </div>
                                    <a href="{{ route('admin.account-managers.show', $manager['id']) }}">
                                        {{ $manager['name'] }}
                                    </a>
                                </div>
                            </td>
                            <td>{{ $manager['email'] }}</td>
                            <td>
                                @if($manager['status'] === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $manager['total_customers'] }}</td>
                            <td class="text-center">{{ $manager['active_customers'] }}</td>
                            <td class="text-center">{{ $manager['inactive_customers'] }}</td>
                            <td>{{ $manager['joined_at'] }}</td>
                            <td class="text-center">{{ $manager['customers_added_this_month'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
