{{-- resources/views/layouts/partials/navigation.blade.php --}}
@can('view_debt_dashboard')
<li class="nav-item">
    <a class="nav-link {{ Request::is('finance/debt*') ? 'active' : '' }}"
       href="{{ route('finance.debt.dashboard') }}">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Debt Management
        @if($debtCount = \App\Services\DebtService::getOverdueCount())
            <span class="badge bg-danger rounded-pill ms-2">{{ $debtCount }}</span>
        @endif
    </a>
</li>
@endcan
