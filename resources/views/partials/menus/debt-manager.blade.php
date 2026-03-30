@if(auth()->check() && auth()->user()->role === 'debt_manager')
    <!-- Debt Manager Menu -->
    {{-- <li class="nav-header text-uppercase text-muted mt-3">
        <small>Debt Management</small>
    </li> --}}

    {{-- <li class="nav-item">
        <a class="nav-link {{ Request::is('finance/debt/dashboard*') ? 'active' : '' }}" href="{{ route('finance.debt.dashboard') }}">
            <i class="fas fa-tachometer-alt me-2"></i>Debt Management Dashboard
        </a>
    </li> --}}

    <li class="nav-item">
                                <a class="nav-link" href="{{ route('finance.ai.dashboard') }}" >
                                    <i class="fas fa-brain"></i> AI Analytics
                                </a>
                            </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('finance/debt/aging-report*') ? 'active' : '' }}" href="{{ route('finance.debt.aging.report') }}">
            <i class="fas fa-chart-bar me-2"></i>Aging Report
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('finance/debt/collection-report*') ? 'active' : '' }}" href="{{ route('finance.debt.collection.report') }}">
            <i class="fas fa-file-invoice-dollar me-2"></i>Collection Report
        </a>
    </li>

  <li class="nav-item">
    <a class="nav-link {{ Request::is('finance/debt/customers*') ? 'active' : '' }}" href="{{ route('finance.debt.customers') }}">
        <i class="fas fa-users me-2"></i>Customer Debts
    </a>
</li>
@endif
