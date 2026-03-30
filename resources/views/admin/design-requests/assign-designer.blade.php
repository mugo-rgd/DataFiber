@extends('layouts.app')

@section('title', 'Assign Designer - ' . $designRequest->request_number)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-user-plus text-primary"></i> Assign Designer
                </h1>
                {{-- ✅ CHANGE: Use request_number instead of id --}}
                <a href="{{ route('admin.design-requests.show', $designRequest->request_number) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Request
                </a>
            </div>
            <p class="text-muted">Request #{{ $designRequest->request_number }} - {{ $designRequest->title }}</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Select Designer</h5>
                </div>
                <div class="card-body">
                    {{-- ✅ CHANGE: Use request_number instead of id --}}
                    <form action="{{ route('account-manager.design-requests.assign-designer', $designRequest->request_number) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="designer_id" class="form-label">Designer *</label>
                            <select name="designer_id" id="designer_id" class="form-select @error('designer_id') is-invalid @enderror" required>
                                <option value="">-- Choose a Designer --</option>
                                @foreach($designers as $designer)
                                    <option value="{{ $designer->id }}" {{ old('designer_id') == $designer->id ? 'selected' : '' }}>
                                        {{ $designer->name }} ({{ $designer->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('designer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Assigning a designer will change the request status to "Assigned" and record the assignment time.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            {{-- ✅ CHANGE: Use request_number instead of id --}}
                            <a href="{{ route('admin.design-requests.show', $designRequest->request_number) }}" class="btn btn-secondary me-md-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-1"></i> Assign Designer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
