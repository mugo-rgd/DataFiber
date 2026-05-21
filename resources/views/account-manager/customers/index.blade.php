@extends('layouts.app')

@section('title', 'My Customers')

@section('content')

@php
    if (!function_exists('formatPhoneNumber')) {

        function formatPhoneNumber($phone)
        {
            if (empty($phone)) {
                return null;
            }

            // convert scientific notation
            if (is_numeric($phone)) {
                $phone = number_format((float)$phone, 0, '', '');
            }

            $phone = trim((string)$phone);

            // remove everything except digits
            $digits = preg_replace('/[^0-9]/', '', $phone);

            // 254712345678
            if (strlen($digits) === 12 && substr($digits,0,3) === '254') {

                return '+254 '
                    . substr($digits,3,3)
                    . ' '
                    . substr($digits,6,3)
                    . ' '
                    . substr($digits,9,3);
            }

            // 0712345678
            if (strlen($digits) === 10 && substr($digits,0,1) === '0') {

                return '+254 '
                    . substr($digits,1,3)
                    . ' '
                    . substr($digits,4,3)
                    . ' '
                    . substr($digits,7,3);
            }

            return $phone;
        }
    }
@endphp

<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-4">

        <div class="col-12">

            <div class="d-sm-flex align-items-center justify-content-between">

                <div>
                    <h1 class="h3 mb-1 text-gray-800">
                        <i class="fas fa-users me-2" style="color: #0066B3;"></i>
                        My Customers
                    </h1>

                    <p class="text-muted mb-0">
                        Manage and support assigned customers
                    </p>
                </div>

                <span class="badge rounded-pill px-4 py-2" style="background: #0066B3;">
                    Total: {{ $customers->count() }} Customers
                </span>

            </div>

        </div>

    </div>


    <div class="row">

        @forelse($customers as $customer)

            @php
                $formattedPhone = formatPhoneNumber($customer->phone);

                $pendingCount = \App\Models\Document::where(
                    'user_id',
                    $customer->id
                )
                ->where('source','customer')
                ->where('status','pending_review')
                ->count();
            @endphp

            <div class="col-xl-4 col-md-6 mb-4">

                <div class="card customer-card border-0 shadow-sm h-100">

                    <div class="card-body">

                        {{-- Customer Header --}}
                        <div class="d-flex align-items-start mb-3">

                            <div class="customer-avatar">
                                {{ strtoupper(substr($customer->name,0,1)) }}
                            </div>

                            <div class="ms-3 flex-grow-1">

                                <h5 class="fw-bold mb-1" style="color: #0066B3;">
                                    {{ $customer->name }}
                                </h5>

                                <div class="small text-muted">
                                    Customer ID: #{{ $customer->id }}
                                </div>

                            </div>

                        </div>

                        {{-- Customer Details --}}
                        <div class="customer-details">

                            <div class="detail-item">
                                <i class="fas fa-envelope" style="color: #0066B3;"></i>
                                <span>{{ $customer->email }}</span>
                            </div>

                            @if($formattedPhone)
                            <div class="detail-item">
                                <i class="fas fa-phone" style="color: #009639;"></i>
                                <span>{{ $formattedPhone }}</span>
                            </div>
                            @endif

                            <div class="detail-item">
                                <i class="fas fa-calendar" style="color: #FFD700;"></i>
                                <span>
                                    Assigned: {{ $customer->assigned_at
                                        ? $customer->assigned_at->format('M d, Y')
                                        : 'N/A'
                                    }}
                                </span>
                            </div>

                        </div>

                        {{-- Statistics --}}
                        <div class="row text-center mt-4">

                            <div class="col-6">
                                <div class="stats-box" style="background: #FFF8E1;">
                                    <div class="fw-bold" style="color: #FFD700;">
                                        {{ $customer->open_tickets_count }}
                                    </div>
                                    <small>Open Tickets</small>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="stats-box" style="background: #EAF6FF;">
                                    <div class="fw-bold" style="color: #0066B3;">
                                        {{ $customer->pending_payments_count }}
                                    </div>
                                    <small>Pending Payments</small>
                                </div>
                            </div>

                        </div>

                        {{-- Buttons --}}
                        <div class="mt-4 d-grid gap-2">

                            <a href="{{ route('account-manager.customers.show',$customer) }}"
                               class="btn btn-sm text-white" style="background: #0066B3;">
                                <i class="fas fa-eye me-1"></i>
                                View Details
                            </a>

                            <a href="{{ route('account-manager.documents.approve',$customer) }}"
                               class="btn btn-sm text-dark" style="background: #FFD700;">
                                <i class="fas fa-check-circle me-1"></i>
                                Approve Documents
                                @if($pendingCount > 0)
                                    <span class="badge bg-danger ms-1" style="background: #dc3545;">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </a>

                            <a href="{{ route('account-manager.customers.documents.manage',$customer) }}"
                               class="btn btn-sm text-white" style="background: #009639;">
                                <i class="fas fa-file-upload me-1"></i>
                                Manage Documents
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="card shadow-sm border-0">

                    <div class="card-body text-center py-5">

                        <i class="fas fa-users fa-4x text-muted mb-3"></i>

                        <h4 class="text-muted">No Customers Assigned</h4>

                        <p class="text-muted">You currently have no assigned customers.</p>

                        <small class="text-muted">Contact your administrator.</small>

                    </div>

                </div>

            </div>

        @endforelse

    </div>

</div>

<style>
/* Kenya Power Corporate Colors */
:root {
    --kp-blue: #0066B3;
    --kp-green: #009639;
    --kp-yellow: #FFD700;
    --kp-blue-light: #EAF6FF;
    --kp-yellow-light: #FFF8E1;
}

.customer-card {
    border-radius: 16px;
    transition: all 0.3s ease;
    border-top: 4px solid #0066B3;
}

.customer-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}

.customer-avatar {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0066B3, #009639);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    box-shadow: 0 2px 8px rgba(0,102,179,0.3);
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    font-size: .85rem;
    padding: 6px 8px;
    border-radius: 8px;
    transition: background 0.2s ease;
}

.detail-item:hover {
    background: rgba(0,102,179,0.05);
}

.detail-item i {
    width: 18px;
}

.stats-box {
    border-radius: 12px;
    padding: 10px;
    transition: transform 0.2s ease;
}

.stats-box:hover {
    transform: scale(1.02);
}

.btn {
    transition: all 0.2s ease;
    font-weight: 500;
    border: none;
}

.btn:hover {
    transform: translateY(-1px);
    filter: brightness(95%);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.btn:active {
    transform: translateY(0);
}

/* Badge styling */
.badge {
    font-weight: 500;
    padding: 0.35rem 0.65rem;
}

/* Kenya Power themed scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #0066B3;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #009639;
}
</style>

@endsection
