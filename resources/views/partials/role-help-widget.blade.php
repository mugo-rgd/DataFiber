@php
    use App\Helpers\RoleHelper;
    $currentRole = RoleHelper::getCurrentRole();
    $roleDisplayName = RoleHelper::getRoleDisplayName();

    $roleHelpConfig = [
        'finance' => ['route' => 'help.role.finance', 'color' => 'info', 'icon' => 'chart-line', 'title' => 'Finance Help Guide'],
        'designer' => ['route' => 'help.role.designer', 'color' => 'primary', 'icon' => 'drafting-compass', 'title' => 'Designer Help Guide'],
        'debt_manager' => ['route' => 'help.role.debt-manager', 'color' => 'danger', 'icon' => 'chart-simple', 'title' => 'Debt Management Guide'],
        'customer' => ['route' => 'help.role.customer', 'color' => 'warning', 'icon' => 'user-circle', 'title' => 'Customer Help Guide'],
        'ict_engineer' => ['route' => 'help.role.ict-engineer', 'color' => 'primary', 'icon' => 'microchip', 'title' => 'ICT Engineer Guide'],
        'account_manager' => ['route' => 'help.role.account-manager', 'color' => 'success', 'icon' => 'users', 'title' => 'Account Manager Guide'],
        'compliance_officer' => ['route' => 'help.role.compliance-officer', 'color' => 'info', 'icon' => 'file-alt', 'title' => 'Compliance Guide'],
        'surveyor' => ['route' => 'help.role.surveyor', 'color' => 'secondary', 'icon' => 'ruler-combined', 'title' => 'Surveyor Guide'],
        'technician' => ['route' => 'help.role.technician', 'color' => 'dark', 'icon' => 'tools', 'title' => 'Technician Guide'],
        'technical_admin' => ['route' => 'help.role.technical-admin', 'color' => 'primary', 'icon' => 'network-wired', 'title' => 'Technical Admin Guide'],
        'system_admin' => ['route' => 'help.role.admin', 'color' => 'danger', 'icon' => 'shield-alt', 'title' => 'Admin Guide'],
        'accountmanager_admin' => ['route' => 'help.role.accountmanager-admin', 'color' => 'success', 'icon' => 'chart-line', 'title' => 'Account Manager Admin Guide'],
        'regional_manager' => ['route' => 'help.role.regional-manager', 'color' => 'primary', 'icon' => 'chart-line', 'title' => 'Regional Manager Guide'],
        'county_ict_engineer' => ['route' => 'help.role.county-ict-engineer', 'color' => 'info', 'icon' => 'map-marked-alt', 'title' => 'County ICT Guide'],
        'viewer' => ['route' => 'help.role.viewer', 'color' => 'secondary', 'icon' => 'eye', 'title' => 'Viewer Guide'],
        'admin' => ['route' => 'help.role.admin', 'color' => 'danger', 'icon' => 'user-shield', 'title' => 'Admin Help Guide'],
    ];

    // Default config for any role not explicitly defined
    $config = $roleHelpConfig[$currentRole] ?? [
        'route' => 'help.index',
        'color' => 'secondary',
        'icon' => 'question-circle',
        'title' => 'Help Guide'
    ];
@endphp

<!-- Simple Dropdown Help Menu -->
<div class="dropdown d-inline-block">
    <button class="btn btn-sm btn-{{ $config['color'] }} dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">
        <i class="fas fa-{{ $config['icon'] }} me-1"></i>
        <span class="d-none d-md-inline">Help</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
        <li>
            <a class="dropdown-item" href="{{ route($config['route']) }}">
                <i class="fas fa-book-open me-2"></i>
                {{ $config['title'] }}
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="{{ route('help.faq') }}">
                <i class="fas fa-question-circle me-2"></i>
                Frequently Asked Questions
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('help.contact') }}">
                <i class="fas fa-headset me-2"></i>
                Contact Support
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('help.video-tutorials') }}">
                <i class="fas fa-video me-2"></i>
                Video Tutorials
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('help.index') }}">
                <i class="fas fa-home me-2"></i>
                Help Center Home
            </a>
        </li>
    </ul>
</div>
