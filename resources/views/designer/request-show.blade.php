@extends('layouts.app')

@section('title', 'Design Request #' . ($designRequest->request_number ?? 'N/A'))

@section('content')
<div class="container-fluid px-4">
    <!-- Enhanced Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <!-- Main Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-4 position-relative">
                        <i class="fas fa-drafting-compass fa-2x text-primary"></i>
                        <div class="position-absolute top-0 start-100 translate-middle">
                            <span class="badge bg-{{ match($designRequest->status ?? 'pending') {
                                'pending' => 'secondary',
                                'Assigned' => 'info',
                                'designed' => 'success',
                                'quoted' => 'info',
                                default => 'light'
                            }} fs-7">
                                {{ ucfirst(str_replace('_', ' ', $designRequest->status ?? 'pending')) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <h1 class="h2 mb-1 fw-bold text-gray-800">Design Request #{{ $designRequest->request_number ?? 'N/A' }}</h1>
                        <p class="text-muted mb-0">{{ $designRequest->title ?? 'No Title' }}</p>
                        <div class="d-flex align-items-center mt-1">
                            <small class="text-muted me-3">
                                <i class="fas fa-user me-1"></i>{{ $designRequest->customer->name ?? 'Unknown Customer' }}
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>{{ $designRequest->created_at?->format('M d, Y') ?? 'Unknown' }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <!-- Return to Dashboard Button -->
                    <a href="{{ route('designer.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('designer.requests') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Requests
                    </a>
                    @if(($designRequest->designItems?->count() ?? 0) > 0)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#designItemsModal">
                        <i class="fas fa-eye me-2"></i>View Items
                        <span class="badge bg-white text-primary ms-2">{{ $designRequest->designItems?->count() ?? 0 }}</span>
                    </button>
                    @endif
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="progress-container">
                                <div class="progress-steps d-flex justify-content-between position-relative">
                                    @php
                                        $steps = [
                                            'requested' => ['icon' => 'paper-plane', 'label' => 'Requested', 'date' => $designRequest->requested_at],
                                            'assigned' => ['icon' => 'user-check', 'label' => 'Assigned', 'date' => $designRequest->assigned_at],
                                            'designing' => ['icon' => 'pencil-ruler', 'label' => 'Designing', 'date' => $designRequest->designItems?->count() > 0 ? now() : null],
                                            'designed' => ['icon' => 'check-circle', 'label' => 'Designed', 'date' => $designRequest->design_completed_at],
                                            'quoted' => ['icon' => 'file-invoice-dollar', 'label' => 'Quoted', 'date' => $designRequest->quoted_at]
                                        ];
                                        $currentStep = array_search(true, array_map(fn($step) => !empty($step['date']), $steps));
                                        $progress = (($currentStep + 1) / count($steps)) * 100;
                                    @endphp

                                    @foreach($steps as $key => $step)
                                        <div class="step {{ !empty($step['date']) ? 'completed' : '' }} {{ $key === $currentStep ? 'current' : '' }}">
                                            <div class="step-icon">
                                                <i class="fas fa-{{ $step['icon'] }}"></i>
                                            </div>
                                            <div class="step-label">
                                                <small class="d-block fw-semibold">{{ $step['label'] }}</small>
                                                <small class="text-muted">
                                                    {{ $step['date']?->format('M d') ?? 'Pending' }}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="progress-bar-background"></div>
                                    <div class="progress-bar" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end gap-3">
                                <div class="text-center">
                                    <div class="h4 mb-0 fw-bold text-primary">{{ $designRequest->designItems?->count() ?? 0 }}</div>
                                    <small class="text-muted">Items</small>
                                </div>
                                <div class="text-center">
                                    <div class="h4 mb-0 fw-bold text-success">
                                        ${{ number_format($designRequest->designItems?->sum(function($item) {
                                            return ($item->cores_required ?? 0) * ($item->unit_cost ?? 0) * ($item->distance ?? 0);
                                        }) ?? 0, 2) }}
                                    </div>
                                    <small class="text-muted">Total Cost</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-5"></i>
                <div class="flex-grow-1">
                    <strong class="fw-semibold">Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-5"></i>
                <div class="flex-grow-1">
                    <strong class="fw-semibold">Success!</strong>
                    <span class="ms-2">{{ session('success') }}</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column - Main Content -->
        <div class="col-lg-8">
            <!-- Quick Actions Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-primary text-start p-3" data-bs-toggle="collapse" data-bs-target="#designItemsForm">
                                    <i class="fas fa-plus-circle me-2 fs-5"></i>
                                    <div>
                                        <strong>Add Design Items</strong>
                                        <small class="d-block text-muted">Create fibre route components</small>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-success text-start p-3" data-bs-toggle="collapse" data-bs-target="#designSpecifications">
                                    <i class="fas fa-pencil-ruler me-2 fs-5"></i>
                                    <div>
                                        <strong>Design Specifications</strong>
                                        <small class="d-block text-muted">Add technical details</small>
                                    </div>
                                </button>
                            </div>
                        </div>
                        @if(($designRequest->status ?? 'pending') === 'designed')
                        <div class="col-md-6">
                            <div class="d-grid">
                                <a href="{{ route('designer.quotations.create', $designRequest) }}" class="btn btn-outline-info text-start p-3">
                                    <i class="fas fa-file-invoice-dollar me-2 fs-5"></i>
                                    <div>
                                        <strong>Create Quotation</strong>
                                        <small class="d-block text-muted">Generate customer quote</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                        @endif
                        <!-- Additional Dashboard Button in Quick Actions -->
                        <div class="col-md-6">
                            <div class="d-grid">
                                <a href="{{ route('designer.dashboard') }}" class="btn btn-outline-secondary text-start p-3">
                                    <i class="fas fa-tachometer-alt me-2 fs-5"></i>
                                    <div>
                                        <strong>Return to Dashboard</strong>
                                        <small class="d-block text-muted">Back to main dashboard</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Design Items Form (Collapsible) -->
            <div class="collapse {{ $errors->any() ? 'show' : '' }}" id="designItemsForm">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-list-alt text-success me-2"></i>Design Items
                        </h5>
                        <span class="badge bg-success bg-opacity-10 text-success">
                            {{ $designRequest->designItems?->count() ?? 0 }} items
                        </span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('designer.design-items.store') }}" method="POST" id="designItemsForm">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $designRequest->customer_id }}">
                            <input type="hidden" name="designer_id" value="{{ Auth::id() }}">
                            <input type="hidden" name="request_number" value="{{ $designRequest->request_number }}">

                            <div id="designItemsContainer">
                                <!-- Design items will be added here dynamically -->
                            </div>

                            <div class="text-center mt-4">
                                <button type="button" id="addItemBtn" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-2"></i>Add Design Item
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <div class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Add all required fibre route components
                                </div>
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="fas fa-save me-2"></i>Save Items
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Design Specifications (Collapsible) -->
            <div class="collapse" id="designSpecifications">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-pencil-ruler text-warning me-2"></i>Design Specifications
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('designer.requests.update', $designRequest) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Design Specifications</label>
                                <textarea class="form-control border" name="design_specifications" rows="5"
                                          placeholder="Provide detailed fibre route design specifications...">{{ old('design_specifications', $designRequest->design_specifications ?? '') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Design Notes</label>
                                <textarea class="form-control border" name="design_notes" rows="3"
                                          placeholder="Any additional notes or considerations...">{{ old('design_notes', $designRequest->design_notes ?? '') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end pt-3 border-top">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Save Specifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Navigation Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-compass text-info me-2"></i>Quick Navigation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('designer.dashboard') }}" class="btn btn-outline-primary text-start">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            PreSale Engineer 
                        </a>
                        <a href="{{ route('designer.requests') }}" class="btn btn-outline-secondary text-start">
                            <i class="fas fa-list me-2"></i>
                            All Design Requests
                        </a>
                        <a href="{{ route('designer.quotations') }}" class="btn btn-outline-info text-start">
                            <i class="fas fa-file-invoice-dollar me-2"></i>
                            My Quotations
                        </a>
                    </div>
                </div>
            </div>

            <!-- Request Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle text-primary me-2"></i>Request Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Customer</span>
                            <span class="fw-semibold">{{ $designRequest->customer->name ?? 'Unknown' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Request Date</span>
                            <span class="fw-semibold">{{ $designRequest->created_at?->format('M d, Y') ?? 'Unknown' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Assigned</span>
                            <span class="fw-semibold">{{ $designRequest->assigned_at?->format('M d, Y') ?? 'Not assigned' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Priority</span>
                            <span class="badge bg-warning">Normal</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-semibold mb-3">Description</h6>
                        <p class="text-muted lh-lg">{{ $designRequest->description ?? 'No description provided' }}</p>
                    </div>

                    @if($designRequest->technical_requirements)
                    <div class="mt-3 pt-3 border-top">
                        <h6 class="fw-semibold mb-3">Technical Requirements</h6>
                        <p class="text-muted lh-lg">{{ $designRequest->technical_requirements }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Cost Summary -->
            @if(($designRequest->designItems?->count() ?? 0) > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-pie text-success me-2"></i>Cost Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Items</span>
                            <span class="fw-bold text-primary">{{ $designRequest->designItems?->count() ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Cores</span>
                            <span class="fw-bold text-info">{{ $designRequest->designItems?->sum('cores_required') ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Distance</span>
                            <span class="fw-bold text-warning">{{ number_format($designRequest->designItems?->sum('distance') ?? 0, 2) }} km</span>
                        </div>
                        <div class="pt-2 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Estimated Cost</span>
                                <span class="h5 fw-bold text-success">
                                    ${{ number_format($designRequest->designItems?->sum(function($item) {
                                        return ($item->cores_required ?? 0) * ($item->unit_cost ?? 0) * ($item->distance ?? 0);
                                    }) ?? 0, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Rest of your modal and script sections remain the same -->
<!-- Design Items Modal -->
<div class="modal fade" id="designItemsModal" tabindex="-1" aria-labelledby="designItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold" id="designItemsModalLabel">
                    <i class="fas fa-list-check me-2"></i>
                    Design Items - Request #{{ $designRequest->request_number }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Modal content remains the same as your original -->
            </div>
        </div>
    </div>
</div>

<style>
/* Your existing CSS styles remain the same */
.progress-container {
    position: relative;
}

.progress-steps {
    padding: 0 20px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    flex: 1;
}

.step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border: 3px solid #e9ecef;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.step.completed .step-icon {
    background: #198754;
    border-color: #198754;
    color: white;
}

.step.current .step-icon {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
    transform: scale(1.1);
}

.step-label {
    text-align: center;
}

.progress-bar-background {
    position: absolute;
    top: 25px;
    left: 50px;
    right: 50px;
    height: 4px;
    background: #e9ecef;
    z-index: 1;
}

.progress-bar {
    position: absolute;
    top: 25px;
    left: 50px;
    height: 4px;
    background: #0d6efd;
    z-index: 1;
    transition: width 0.5s ease;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.collapse {
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .progress-steps {
        flex-direction: column;
        align-items: flex-start;
    }

    .step {
        flex-direction: row;
        margin-bottom: 15px;
        width: 100%;
    }

    .step-icon {
        margin-right: 15px;
        margin-bottom: 0;
    }

    .progress-bar-background,
    .progress-bar {
        display: none;
    }
}
</style>

<script>
// Your existing JavaScript remains the same
document.addEventListener('DOMContentLoaded', function() {
    // Initialize design items container with first item
    const container = document.getElementById('designItemsContainer');
    let itemIndex = 0;

    // Add first design item
    addDesignItem();

    // Add item button functionality
    document.getElementById('addItemBtn').addEventListener('click', addDesignItem);

    function addDesignItem() {
        const itemHTML = `
            <div class="design-item card border mb-3" data-item-index="${itemIndex}">
                <div class="card-header bg-light bg-opacity-50 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-cube me-2 text-primary"></i>
                            Design Item ${itemIndex + 1}
                        </h6>
                        ${itemIndex > 0 ? `
                        <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                            <i class="fas fa-trash me-1"></i>Remove
                        </button>
                        ` : ''}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cores Required *</label>
                            <input type="number" class="form-control" name="design_items[${itemIndex}][cores_required]" required min="1" placeholder="Enter cores">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Unit Cost ($) *</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="design_items[${itemIndex}][unit_cost]" required placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Distance (km) *</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="design_items[${itemIndex}][distance]" required placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Technology Type *</label>
                            <select class="form-select" name="design_items[${itemIndex}][technology_type]" required>
                                <option value="">Select Technology</option>
                                <option value="Fibre Optic">Fibre Optic</option>
                                <option value="Copper">Copper</option>
                                <option value="Wireless">Wireless</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', itemHTML);

        // Add animation
        const newItem = container.lastElementChild;
        newItem.style.opacity = '0';
        newItem.style.transform = 'translateY(20px)';

        setTimeout(() => {
            newItem.style.transition = 'all 0.3s ease';
            newItem.style.opacity = '1';
            newItem.style.transform = 'translateY(0)';
        }, 10);

        itemIndex++;
    }

    // Remove item functionality
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const item = e.target.closest('.design-item');
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '0';
            item.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                item.remove();
                // Update item numbers
                updateItemNumbers();
            }, 300);
        }
    });

    function updateItemNumbers() {
        const items = container.querySelectorAll('.design-item');
        items.forEach((item, index) => {
            const header = item.querySelector('h6');
            header.innerHTML = header.innerHTML.replace(/Design Item \d+/, `Design Item ${index + 1}`);
        });
    }

    // Auto-expand sections with errors
    @if($errors->any())
    const designItemsForm = document.getElementById('designItemsForm');
    if (designItemsForm) {
        const bsCollapse = new bootstrap.Collapse(designItemsForm);
        bsCollapse.show();
    }
    @endif
});
</script>
@endsection
