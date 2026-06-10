@extends('layouts.app')

@section('title', 'Notifications - ICT Engineer')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-bell me-2 text-kp-yellow"></i>Notifications
                    </h1>
                    <p class="text-muted mb-0">Stay updated with your certificate assignments and requests</p>
                </div>
                <div>
                    <button class="btn btn-outline-secondary" id="markAllReadBtn">
                        <i class="fas fa-check-double me-2"></i>Mark All as Read
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-bell me-2 text-kp-yellow"></i>All Notifications
            </h5>
        </div>
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        @php
                            $notificationData = $notification->data;
                            $actionUrl = '#';
                            $certificateId = null;
                            $certificateType = null;

                            // Determine notification type and URL
                            if (isset($notificationData['certificate_id'])) {
                                $certificateId = $notificationData['certificate_id'];
                                $certificateType = $notificationData['certificate_type'] ?? 'conditional';

                                // Use the correct route based on certificate type
                                if ($certificateType === 'conditional') {
                                    $actionUrl = route('ictengineer.certificates.conditional.show', $certificateId);
                                } elseif ($certificateType === 'acceptance') {
                                    $actionUrl = route('ictengineer.certificates.acceptance.show', $certificateId);
                                }
                            } elseif (isset($notificationData['design_request_id'])) {
                                $actionUrl = route('ictengineer.requests.show', $notificationData['design_request_id']);
                            } elseif (isset($notificationData['action_url'])) {
                                $actionUrl = $notificationData['action_url'];
                            }

                            // Set icon and color based on notification type
                            $icon = $notificationData['icon'] ?? 'bell';
                            $color = $notificationData['color'] ?? 'info';

                            if (isset($notificationData['certificate_id'])) {
                                $icon = 'file-certificate';
                                $color = 'success';
                            } elseif (str_contains($notificationData['title'] ?? '', 'Certificate')) {
                                $icon = 'file-certificate';
                                $color = 'primary';
                            }
                        @endphp

                        <div class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'bg-light border-start border-4 border-primary' }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-{{ $color }}-light p-2" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-{{ $icon }} fa-lg text-{{ $color }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $notificationData['title'] ?? 'Notification' }}</h6>
                                            <p class="mb-1 text-muted small">{{ $notificationData['message'] ?? '' }}</p>
                                        </div>
                                        <small class="text-muted ms-2">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>

                                    @if(isset($notificationData['certificate_id']))
                                        <div class="mt-2 mb-2">
                                            <span class="badge bg-info">
                                                <i class="fas fa-hashtag me-1"></i> Certificate ID: {{ $notificationData['certificate_id'] }}
                                            </span>
                                            @if(isset($notificationData['ref_number']))
                                                <span class="badge bg-secondary ms-1">
                                                    <i class="fas fa-barcode me-1"></i> {{ $notificationData['ref_number'] }}
                                                </span>
                                            @endif
                                            @if(isset($notificationData['certificate_type']))
                                                <span class="badge bg-primary ms-1">
                                                    <i class="fas fa-tag me-1"></i> {{ ucfirst($notificationData['certificate_type']) }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="mt-2">
                                        @if($actionUrl && $actionUrl != '#')
                                            <a href="{{ $actionUrl }}" class="btn btn-sm btn-primary me-2">
                                                <i class="fas fa-eye me-1"></i>View Details
                                            </a>
                                        @endif

                                        @if(!$notification->read_at)
                                            <button class="btn btn-sm btn-outline-secondary mark-read-btn" data-id="{{ $notification->id }}">
                                                <i class="fas fa-check me-1"></i>Mark as Read
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-white">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Notifications</h5>
                    <p class="text-muted">You don't have any notifications yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.bg-success-light { background-color: rgba(40, 167, 69, 0.1); }
.bg-primary-light { background-color: rgba(0, 102, 179, 0.1); }
.bg-info-light { background-color: rgba(23, 162, 184, 0.1); }
.bg-warning-light { background-color: rgba(255, 215, 0, 0.1); }
.bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }

.list-group-item-action:hover {
    background-color: #f8f9fa;
}
</style>

<script>
document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
    fetch('{{ route("ictengineer.notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              location.reload();
          }
      });
});

document.querySelectorAll('.mark-read-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch(`{{ url('ict-engineer/notifications') }}/${id}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  location.reload();
              }
          });
    });
});
</script>
@endsection
