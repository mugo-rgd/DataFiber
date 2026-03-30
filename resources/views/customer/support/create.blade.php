@extends('layouts.app')

@section('title', 'Create Support Ticket')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 text-gray-800 mb-4">
                <i class="fas fa-ticket-alt me-2"></i> Create Support Ticket
            </h1>

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">New Support Request</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.support.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lease_id" class="form-label">Related Lease (Optional)</label>
                                    <select class="form-select" id="lease_id" name="lease_id">
                                        <option value="">Select a lease (optional)</option>
                                        @foreach($leases as $lease)
                                            <option value="{{ $lease->id }}" {{ old('lease_id') == $lease->id ? 'selected' : '' }}>
                                                Lease #{{ $lease->lease_number }} - {{ $lease->service_type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select a category</option>
                                        <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Technical Issue</option>
                                        <option value="billing" {{ old('category') == 'billing' ? 'selected' : '' }}>Billing Question</option>
                                        <option value="service" {{ old('category') == 'service' ? 'selected' : '' }}>Service Upgrade</option>
                                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject *</label>
                            <input type="text" class="form-control" id="subject" name="subject"
                                   value="{{ old('subject') }}" placeholder="Brief description of your issue" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="6"
                                      placeholder="Please provide detailed information about your issue..." required>{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label">Attachment (Optional)</label>
                            <input type="file" class="form-control" id="attachment" name="attachment"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt">
                            <div class="form-text">Maximum file size: 10MB. Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG, TXT</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Submit Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
