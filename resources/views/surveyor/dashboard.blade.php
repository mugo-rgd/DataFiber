@extends('layouts.app')

@section('title', 'Surveyor Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Surveyor Dashboard</h1>
        <div class="d-flex">
            <span class="badge badge-success badge-pill p-2 mr-3">
                <i class="fas fa-user-check"></i> Surveyor
            </span>
            <a href="{{ route('surveyor.assignments.index') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-tasks fa-sm text-white-50"></i> My Assignments
            </a>
        </div>
    </div>

    <!-- Debug Info (Remove after testing) -->
    <div class="alert alert-info mb-4">
        <strong>Debug Info:</strong><br>
        User ID: {{ Auth::id() }},
        Total Assigned Requests: {{ $assignedDesignRequests->count() }},
        Pending: {{ $pendingAssignments }},
        In Progress: {{ $inProgressAssignments }}
    </div>

    <!-- Welcome Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Welcome, {{ Auth::user()->name }}!
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Here's your work overview for today
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Pending Assignments -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Assignments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingAssignments }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                In Progress
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inProgressAssignments }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed This Week -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed (This Week)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedThisWeek }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Assignments -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Assignments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $assignedDesignRequests->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Assignments -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Design Requests</h6>
                    <a href="{{ route('surveyor.assignments.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentAssignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Request #</th>
                                        <th>Customer</th>
                                        <th>Title</th>
                                        <th>Priority</th>
                                        <th>Scheduled</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAssignments as $designRequest)
                                    <tr>
                                        <td><strong>{{ $designRequest->request_number }}</strong></td>
                                        <td>{{ $designRequest->customer->name ?? 'N/A' }}</td>
                                        <td>{{ Str::limit($designRequest->title, 30) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $designRequest->priority == 'high' ? 'danger' : ($designRequest->priority == 'medium' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($designRequest->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($designRequest->survey_scheduled_at)
                                                {{ $designRequest->survey_scheduled_at->format('M d, Y H:i') }}
                                            @else
                                                Not scheduled
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $designRequest->survey_status == 'completed' ? 'success' : ($designRequest->survey_status == 'in_progress' ? 'info' : 'warning') }}">
                                                {{ ucfirst(str_replace('_', ' ', $designRequest->survey_status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('surveyor.assignments.show', $designRequest->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">No design requests assigned</h5>
                            <p class="text-gray-400">You don't have any design requests assigned to you yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('surveyor.assignments.index') }}" class="btn btn-info btn-block">
                            <i class="fas fa-tasks fa-fw"></i> My Assignments
                        </a>

                        @if($recentAssignments->count() > 0)
                        <a href="{{ route('surveyor.assignments.show', $recentAssignments->first()->id) }}"
                           class="btn btn-success btn-block">
                            <i class="fas fa-file-alt fa-fw"></i> Work on Latest
                        </a>
                        @else
                        <button class="btn btn-success btn-block" disabled>
                            <i class="fas fa-file-alt fa-fw"></i> Work on Latest
                        </button>
                        @endif

                        <a href="{{ route('surveyor.profile') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-user-cog fa-fw"></i> Profile Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Deadlines -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Survey Deadlines</h6>
                </div>
                <div class="card-body">
                    @if($upcomingDeadlines->count() > 0)
                        <div class="list-group">
                            @foreach($upcomingDeadlines as $designRequest)
                            <a href="{{ route('surveyor.assignments.show', $designRequest->id) }}"
                               class="list-group-item list-group-item-action flex-column align-items-start">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $designRequest->request_number }} - {{ Str::limit($designRequest->title, 40) }}</h6>
                                    <small class="text-{{ $designRequest->survey_scheduled_at->isToday() ? 'danger' : 'warning' }}">
                                        Scheduled: {{ $designRequest->survey_scheduled_at->diffForHumans() }}
                                    </small>
                                </div>
                                <p class="mb-1">Customer: {{ $designRequest->customer->name }}</p>
                                <small>Status:
                                    <span class="badge badge-{{ $designRequest->survey_status == 'in_progress' ? 'info' : 'warning' }}">
                                        {{ ucfirst(str_replace('_', ' ', $designRequest->survey_status)) }}
                                    </span>
                                </small>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-calendar-check fa-2x text-gray-300 mb-2"></i>
                            <p class="text-gray-500 mb-0">No upcoming survey deadlines</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
