@if(session('success') || session('error') || session('warning') || session('info'))
<div class="flash-messages-container">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle me-3 fa-lg"></i>
        <div class="flex-grow-1">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
        <div class="flex-grow-1">{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle me-3 fa-lg"></i>
        <div class="flex-grow-1">{{ session('warning') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-info-circle me-3 fa-lg"></i>
        <div class="flex-grow-1">{{ session('info') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
</div>
@endif

<style>
.flash-messages-container {
    position: fixed;
    top: 80px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
    min-width: 300px;
}

.flash-messages-container .alert {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 8px;
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
