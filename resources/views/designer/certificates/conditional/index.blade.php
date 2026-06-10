@extends('layouts.app')

@section('title', 'Conditional Certificates')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-contract text-info me-2"></i>Conditional Certificates
                    </h1>
                    <p class="text-muted mb-0">View all conditional certificates issued by ICT engineers</p>
                </div>
                <a href="{{ route('designer.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">All Conditional Certificates</h5>
        </div>
        <div class="card-body">
            @if($certificates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Certificate Ref</th>
                                <th>Request #</th>
                                <th>Customer</th>
                                <th>Presale Engineer</th>
                                <th>ICT Engineer</th>
                                <th>Link Name</th>
                                <th>Issue Date</th>
                                <th>Status / Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificates as $certificate)
                                @php
                                    $designRequest = $certificate->designRequest;
                                    $daysSince = Carbon\Carbon::parse($certificate->certificate_date)->diffInDays(now());
                                    $daysRemaining = max(0, ceil(30 - $daysSince));
                                    $acceptanceReady = $daysSince >= 30;
                                    $acceptanceExists = $designRequest->acceptanceCertificate;
                                @endphp
                                <tr class="{{ $designRequest->designer_id == Auth::id() ? 'table-primary' : '' }}">
                                    <td>
                                        <strong>{{ $certificate->ref_number }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $certificate->id }}</small>
                                    </td>
                                    <td>
                                        <strong>#{{ $designRequest->request_number ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">Request ID: {{ $designRequest->id ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $designRequest->customer->name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-kp-green text-white rounded-circle">
                                                    {{ substr($designRequest->designer->name ?? 'N/A', 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <span>{{ $designRequest->designer->name ?? 'N/A' }}</span>
                                                <br>
                                                <small class="text-muted">{{ $designRequest->designer->email ?? '' }}</small>
                                                @if($designRequest->designer_id == Auth::id())
                                                    <span class="badge bg-primary ms-1">You</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-kp-blue text-white rounded-circle">
                                                    {{ substr($certificate->ictEngineer->name ?? 'N/A', 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <span>{{ $certificate->ictEngineer->name ?? 'N/A' }}</span>
                                                <br>
                                                <small class="text-muted">{{ $certificate->ictEngineer->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $certificate->link_name }}</td>
                                    <td>
                                        {{ Carbon\Carbon::parse($certificate->certificate_date)->format('M d, Y') }}
                                        <br>
                                        @if($daysSince >= 30)
                                            <small class="text-success">{{ $daysSince }} day{{ $daysSince != 1 ? 's' : '' }} ago</small>
                                        @else
                                            <small class="text-warning">{{ $daysSince }} day{{ $daysSince != 1 ? 's' : '' }} ago</small>
                                            <br>
                                            <small class="text-muted">{{ $daysRemaining }} day{{ $daysRemaining != 1 ? 's' : '' }} until acceptance</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'pending_designer' => 'warning',
                                                'sent_to_designer' => 'info',
                                                'acknowledged' => 'primary',
                                                'completed' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            $color = $statusColors[$certificate->certificate_status] ?? 'secondary';
                                        @endphp
                                        <div>
                                            <span class="badge bg-{{ $color }} rounded-pill px-3 py-1">
                                                {{ ucfirst(str_replace('_', ' ', $certificate->certificate_status)) }}
                                            </span>

                                            @if($acceptanceReady && !$acceptanceExists)
                                                <span class="badge bg-success mt-1 d-block">Ready for Acceptance</span>
                                            @elseif(!$acceptanceReady && !$acceptanceExists && $daysSince > 0)
                                                <span class="badge bg-warning text-dark mt-1 d-block">Acceptance in {{ $daysRemaining }} day{{ $daysRemaining != 1 ? 's' : '' }}</span>
                                            @elseif($acceptanceExists)
                                                <span class="badge bg-success mt-1 d-block">Acceptance Issued</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group gap-1">
                                            <a href="{{ route('designer.certificates.conditional.show', $certificate->id) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                               title="View Certificate">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('ictengineer.certificates.conditional.download', $certificate->id) }}"
                                               class="btn btn-sm btn-outline-success rounded-pill px-3"
                                               title="Download conditional certificate">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @if($certificate->certificate_status === 'sent_to_designer' && $designRequest->designer_id == Auth::id())
                                                <form action="{{ route('designer.certificates.conditional.acknowledge', $certificate->id) }}"
                                                      method="POST"
                                                      class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-warning rounded-pill px-3"
                                                            title="Acknowledge Certificate"
                                                            onclick="return confirm('Acknowledge this conditional certificate?')">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($acceptanceReady && !$acceptanceExists && $designRequest->designer_id == Auth::id())
                                                <a href="{{ route('designer.certificates.acceptance.create', $designRequest) }}"
                                                   class="btn btn-sm btn-success rounded-pill px-3"
                                                   title="Generate Acceptance Certificate">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('designer.requests.show', $designRequest) }}"
                                               class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                               title="View Design Request">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $certificates->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-contract fa-4x text-muted opacity-25 mb-3"></i>
                    <h5 class="text-muted">No Conditional Certificates Found</h5>
                    <p class="text-muted mb-0">No conditional certificates have been issued yet.</p>
                    <p class="small text-muted mt-2">Conditional certificates are issued by ICT engineers after technical review.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: bold;
    font-size: 14px;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}

.btn-group .btn {
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
}

.table td {
    vertical-align: middle;
}

.table-primary {
    background-color: #e8f4fd !important;
}
</style>
@endsection
