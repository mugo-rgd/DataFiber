@extends('layouts.app')

@section('title', 'Edit Design Request')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Edit Design Request</h1>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Design Request Details</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.design-requests.update', $designRequest->request_number) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_id">Customer</label>
                                    <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ $designRequest->customer_id == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="designer_id">Designer</label>
                                    <select name="designer_id" id="designer_id" class="form-control @error('designer_id') is-invalid @enderror">
                                        <option value="">Assign Designer</option>
                                        @foreach($designers as $designer)
                                            <option value="{{ $designer->id }}" {{ $designRequest->designer_id == $designer->id ? 'selected' : '' }}>
                                                {{ $designer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('designer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="surveyor_id">Surveyor</label>
                                    <select name="surveyor_id" id="surveyor_id" class="form-control @error('surveyor_id') is-invalid @enderror">
                                        <option value="">Assign Surveyor</option>
                                        @foreach($surveyors as $surveyor)
                                            <option value="{{ $surveyor->id }}" {{ $designRequest->surveyor_id == $surveyor->id ? 'selected' : '' }}>
                                                {{ $surveyor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('surveyor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ $designRequest->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="assigned" {{ $designRequest->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                        <option value="in_design" {{ $designRequest->status == 'in_design' ? 'selected' : '' }}>In Design</option>
                                        <option value="designed" {{ $designRequest->status == 'designed' ? 'selected' : '' }}>Designed</option>
                                        <option value="quoted" {{ $designRequest->status == 'quoted' ? 'selected' : '' }}>Quoted</option>
                                        <option value="approved" {{ $designRequest->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="in_progress" {{ $designRequest->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $designRequest->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="rejected" {{ $designRequest->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="cancelled" {{ $designRequest->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $designRequest->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="survey_status">Survey Status</label>
                                    <select name="survey_status" id="survey_status" class="form-control @error('survey_status') is-invalid @enderror">
                                        <option value="not_required" {{ $designRequest->survey_status == 'not_required' ? 'selected' : '' }}>Not Required</option>
                                        <option value="requested" {{ $designRequest->survey_status == 'requested' ? 'selected' : '' }}>Requested</option>
                                        <option value="assigned" {{ $designRequest->survey_status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                        <option value="in_progress" {{ $designRequest->survey_status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $designRequest->survey_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="failed" {{ $designRequest->survey_status == 'failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="cancelled" {{ $designRequest->survey_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('survey_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $designRequest->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="technical_requirements">Technical Requirements</label>
                            <textarea name="technical_requirements" id="technical_requirements" class="form-control @error('technical_requirements') is-invalid @enderror" rows="3">{{ old('technical_requirements', $designRequest->technical_requirements) }}</textarea>
                            @error('technical_requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cores_required">Cores Required</label>
                                    <input type="number" name="cores_required" id="cores_required" class="form-control @error('cores_required') is-invalid @enderror" value="{{ old('cores_required', $designRequest->cores_required) }}" min="1">
                                    @error('cores_required')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="distance">Distance (km)</label>
                                    <input type="number" name="distance" id="distance" class="form-control @error('distance') is-invalid @enderror" value="{{ old('distance', $designRequest->distance) }}" step="0.01" min="0">
                                    @error('distance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="priority">Priority</label>
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror">
                                        <option value="low" {{ $designRequest->priority == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ $designRequest->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ $designRequest->priority == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ $designRequest->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="technology_type">Technology Type</label>
                                    <input type="text" name="technology_type" id="technology_type" class="form-control @error('technology_type') is-invalid @enderror" value="{{ old('technology_type', $designRequest->technology_type) }}">
                                    @error('technology_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="link_class">Link Class</label>
                                    <input type="text" name="link_class" id="link_class" class="form-control @error('link_class') is-invalid @enderror" value="{{ old('link_class', $designRequest->link_class) }}">
                                    @error('link_class')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="unit_cost">Unit Cost ($)</label>
                                    <input type="number" name="unit_cost" id="unit_cost" class="form-control @error('unit_cost') is-invalid @enderror" value="{{ old('unit_cost', $designRequest->unit_cost) }}" step="0.01" min="0">
                                    @error('unit_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="route_name">Route Name</label>
                            <input type="text" name="route_name" id="route_name" class="form-control @error('route_name') is-invalid @enderror" value="{{ old('route_name', $designRequest->route_name) }}">
                            @error('route_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="terms">Terms (months)</label>
                            <input type="number" name="terms" id="terms" class="form-control @error('terms') is-invalid @enderror" value="{{ old('terms', $designRequest->terms) }}" min="1">
                            @error('terms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="survey_requirements">Survey Requirements</label>
                            <textarea name="survey_requirements" id="survey_requirements" class="form-control @error('survey_requirements') is-invalid @enderror" rows="3">{{ old('survey_requirements', $designRequest->survey_requirements) }}</textarea>
                            @error('survey_requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="survey_estimated_hours">Survey Estimated Hours</label>
                                    <input type="number" name="survey_estimated_hours" id="survey_estimated_hours" class="form-control @error('survey_estimated_hours') is-invalid @enderror" value="{{ old('survey_estimated_hours', $designRequest->survey_estimated_hours) }}" step="0.5" min="0">
                                    @error('survey_estimated_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="survey_scheduled_at">Survey Scheduled At</label>
                                    <input type="datetime-local" name="survey_scheduled_at" id="survey_scheduled_at" class="form-control @error('survey_scheduled_at') is-invalid @enderror" value="{{ old('survey_scheduled_at', $designRequest->survey_scheduled_at ? $designRequest->survey_scheduled_at->format('Y-m-d\TH:i') : '') }}">
                                    @error('survey_scheduled_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Design Request</button>
                            <a href="{{ route('admin.design-requests.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
