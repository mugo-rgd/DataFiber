@extends('layouts.app')

@section('title', 'My Assignments')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Assignments</h1>
    </div>

    <div class="card shadow">
        <div class="card-body">
            @if($assignments->count() > 0)
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $designRequest)
                            <tr>
                                <td><strong>{{ $designRequest->request_number }}</strong></td>
                                <td>{{ $designRequest->customer->name }}</td>
                                <td>{{ $designRequest->title }}</td>
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

                {{ $assignments->links() }}
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-4x text-gray-300 mb-3"></i>
                    <h4 class="text-gray-500">No Assignments Found</h4>
                    <p class="text-gray-400">You don't have any design requests assigned to you yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

