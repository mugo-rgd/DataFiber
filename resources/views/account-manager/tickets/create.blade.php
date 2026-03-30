@extends('layouts.app')

@section('title', 'Create Support Ticket')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Create Support Ticket</h1>
                <a href="{{ route('account-manager.tickets.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Tickets
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form action="{{ route('account-manager.tickets.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_id">Customer *</label>
                                    <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ old('customer_id', request('customer_id')) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->email }}
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
                                    <label for="type">Ticket Type *</label>
                                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        <option value="technical" {{ old('type') == 'technical' ? 'selected' : '' }}>Technical</option>
                                        <option value="billing" {{ old('type') == 'billing' ? 'selected' : '' }}>Billing</option>
                                        <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General</option>
                                        <option value="payment" {{ old('type') == 'payment' ? 'selected' : '' }}>Payment</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority">Priority *</label>
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                        <option value="">Select Priority</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date">Due Date</label>
                                    <input type="datetime-local" name="due_date" id="due_date"
                                           class="form-control @error('due_date') is-invalid @enderror"
                                           value="{{ old('due_date') }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Optional - Set a due date for this ticket</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" name="title" id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" placeholder="Enter ticket title" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea name="description" id="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="6" placeholder="Enter detailed description of the issue..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Ticket
                            </button>
                            <a href="{{ route('account-manager.tickets.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Set minimum datetime for due date to current time
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('due_date').min = now.toISOString().slice(0, 16);
    });
</script>
@endpush
