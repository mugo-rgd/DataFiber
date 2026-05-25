@php
    $commercialRoutes = [
        'leases' => ['name' => 'Leases', 'icon' => 'file-signature', 'route' => 'admin.leases.index'],
        'contracts' => ['name' => 'Contracts', 'icon' => 'file-contract', 'route' => 'contracts.index'],
        'quotations' => ['name' => 'Quotations', 'icon' => 'file-invoice-dollar', 'route' => 'admin.quotations.index'],
        'cak' => ['name' => 'CAK Forms', 'icon' => 'chart-line', 'route' => 'cak.dashboard', 'divider' => true],
    ];
@endphp

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="commercialDocsDropdown" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-file-alt me-1"></i> Commercial Documents
    </a>
    <ul class="dropdown-menu" aria-labelledby="commercialDocsDropdown">
        @foreach($commercialRoutes as $key => $item)
            @if(isset($item['divider']) && $item['divider'])
                <li><hr class="dropdown-divider"></li>
            @endif
            @if(Route::has($item['route']))
            <li>
                <a class="dropdown-item" href="{{ route($item['route']) }}">
                    <i class="fas fa-{{ $item['icon'] }} me-2"></i> {{ $item['name'] }}
                </a>
            </li>
            @endif
        @endforeach
    </ul>
</li>
