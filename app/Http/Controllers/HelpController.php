<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Models\HelpFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpController extends Controller
{
    // ==================== MAIN HELP METHODS ====================

    public function index()
    {
        return view('help.index', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        ]);
    }

    public function storeFeedback(Request $request)
    {
        $request->validate([
            'page' => 'required|string',
            'helpful' => 'required|boolean',
            'comment' => 'nullable|string|max:500',
        ]);

        HelpFeedback::create([
            'user_id' => Auth::id(),
            'page' => $request->page,
            'helpful' => $request->helpful,
            'comment' => $request->comment,
            'role' => $request->role ?? RoleHelper::getCurrentRole(),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['success' => true]);
    }

    public function faq()
    {
        return view('help.faq', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        ]);
    }

    // public function contact()
    // {
    //     return view('help.contact', [
    //         'role' => RoleHelper::getCurrentRole(),
    //         'roleDisplayName' => RoleHelper::getRoleDisplayName(),
    //     ]);
    // }

    public function videoTutorials()
    {
        return view('help.video-tutorials', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        ]);
    }

    // ==================== MODULE GUIDES ====================

    public function aspGuide()
    {
        return view('help.asp-guide', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        ]);
    }

    // public function cspGuide()
    // {
    //     return view('help.csp-guide', [
    //         'role' => RoleHelper::getCurrentRole(),
    //         'roleDisplayName' => RoleHelper::getRoleDisplayName(),
    //     ]);
    // }

    // public function nfpGuide()
    // {
    //     return view('help.nfp-guide', [
    //         'role' => RoleHelper::getCurrentRole(),
    //         'roleDisplayName' => RoleHelper::getRoleDisplayName(),
    //     ]);
    // }

    // public function exportGuide()
    // {
    //     return view('help.export-guide', [
    //         'role' => RoleHelper::getCurrentRole(),
    //         'roleDisplayName' => RoleHelper::getRoleDisplayName(),
    //     ]);
    // }

    // ==================== PROFILE HELP METHODS ====================

    public function profileIndex()
    {
        return view('help.profile.index', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        ]);
    }

    public function profileInfo()
    {
        return view('help.profile.info', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        ]);
    }

    public function profileSecurity()
    {
        return view('help.profile.security', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        ]);
    }

    public function profileNotifications()
    {
        return view('help.profile.notifications', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        ]);
    }

    public function profileActivity()
    {
        return view('help.profile.activity', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        ]);
    }

    // ==================== ROLE-BASED HELP METHODS ====================

   public function roleDashboard()
{
    $view = 'help.role.dashboard';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
            'metrics' => RoleHelper::getDashboardMetrics(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Dashboard Guide',
    ]);
}

   public function roleGettingStarted()
{
    $view = 'help.role.getting-started';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Getting Started Guide',
    ]);
}

    // Finance Role Methods
    public function roleFinance()
    {
        return view('help.role.finance', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
            'metrics' => RoleHelper::getDashboardMetrics(),
        ]);
    }

        // Designer Role Methods
    public function roleDesigner()
    {
        return view('help.role.designer', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
            'metrics' => RoleHelper::getDashboardMetrics(),
        ]);
    }

        // Debt Manager Methods
    public function roleDebtManager()
    {
        return view('help.role.debt-manager', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
            'metrics' => RoleHelper::getDashboardMetrics(),
        ]);
    }

        // Customer Methods
    public function roleCustomer()
    {
        return view('help.role.customer', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
            'metrics' => RoleHelper::getDashboardMetrics(),
        ]);
    }

    public function roleCustomerProfile()
    {
        return view('help.role.customer-profile', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    public function roleCustomerInvoices()
    {
        return view('help.role.customer-invoices', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    public function roleCustomerTickets()
    {
        return view('help.role.customer-tickets', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    // ICT Engineer Methods
    public function roleIctEngineer()
    {
        return view('help.role.ict-engineer', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
            'metrics' => RoleHelper::getDashboardMetrics(),
        ]);
    }
    // Account Manager Methods
    public function roleAccountManager()
    {
        return view('help.role.account-manager', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
            'metrics' => RoleHelper::getDashboardMetrics(),
        ]);
    }

    public function roleCustomerCare()
    {
        return view('help.role.customer-care', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    // Compliance Officer Methods
    public function roleComplianceOfficer()
    {
        return view('help.role.compliance-officer', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    // Surveyor Methods
    public function roleSurveyor()
    {
        return view('help.role.surveyor', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    public function roleFieldData()
    {
        return view('help.role.field-data', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    // Technician Methods
    public function roleTechnician()
    {
        return view('help.role.technician', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    public function roleEquipment()
    {
        return view('help.role.equipment', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    // Technical Admin Methods
    public function roleTechnicalAdmin()
    {
        return view('help.role.technical-admin', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
            'metrics' => RoleHelper::getDashboardMetrics(),
        ]);
    }

    public function roleLeaseManagement()
    {
        return view('help.role.lease-management', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    // Admin Methods
    public function roleAdmin()
    {
        return view('help.role.admin', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    public function roleBackup()
    {
        return view('help.role.backup', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    // Additional Role Methods
    public function roleAccountmanagerAdmin()
    {
        return view('help.role.accountmanager-admin', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

     public function roleViewer()
    {
        return view('help.role.viewer', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

public function getContent($page)
{
    $view = "help.role.{$page}";

    if (view()->exists($view)) {
        $html = view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ])->render();

        return response()->json(['html' => $html]);
    }

    return response()->json(['error' => 'Page not found'], 404);
}
    // Main Help Methods
public function gettingStarted()
{
    return view('help.getting-started', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
    ]);
}

public function cakComplianceGuide()
{
    return view('help.cak-compliance-guide', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
    ]);
}

public function cspGuide()
{
    return view('help.csp-guide', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
    ]);
}

public function nfpGuide()
{
    return view('help.nfp-guide', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
    ]);
}

public function exportGuide()
{
    return view('help.export-guide', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
    ]);
}

public function accountManagerCustomers()
{
    return view('help.account-manager-customers', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
    ]);
}

// Technical Admin Additional Methods
public function roleLeases()
{
    $view = 'help.role.leases';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Lease Management Guide',
    ]);
}

public function roleMaintenance()
{
    $view = 'help.role.maintenance';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Maintenance Guide',
    ]);
}

public function roleRenewals()
{
    $view = 'help.role.renewals';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Contract Renewals Guide',
    ]);
}

// Designer Additional Methods
public function roleQuotations()
{
    $view = 'help.role.quotations';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Quotations Guide',
    ]);
}

public function roleFibreDashboard()
{
    $view = 'help.role.fibre-dashboard';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Fibre Dashboard Guide',
    ]);
}

// Finance Additional Methods
public function roleBilling()
{
    $view = 'help.role.billing';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Billing Guide',
    ]);
}

public function rolePayments()
{
    $view = 'help.role.payments';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Payments Guide',
    ]);
}

// Debt Manager Additional Methods
public function roleAgingReport()
{
    $view = 'help.role.aging-report';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Aging Report Guide',
    ]);
}

public function roleCollection()
{
    $view = 'help.role.collection';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Collection Guide',
    ]);
}

// ICT Engineer Additional Methods
public function roleTicketsIct()
{
    $view = 'help.role.tickets-ict';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Tickets Management Guide',
    ]);
}

public function roleMonitoring()
{
    $view = 'help.role.monitoring';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Network Monitoring Guide',
    ]);
}

// Admin Additional Methods
public function roleUserManagement()
{
    $view = 'help.role.user-management';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'User Management Guide',
    ]);
}

public function roleRegionalManager()
{
    $view = 'help.role.regional-manager';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'Regional Manager Guide',
    ]);
}

public function roleCountyIctEngineer()
{
    $view = 'help.role.county-ict-engineer';

    if (view()->exists($view)) {
        return view($view, [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
        ]);
    }

    return view('help.role.generic', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
        'quickTips' => RoleHelper::getQuickTips(),
        'pageTitle' => 'County ICT Engineer Guide',
    ]);
}

public function contact()
{
    return view('help.contact', [
        'role' => RoleHelper::getCurrentRole(),
        'roleDisplayName' => RoleHelper::getRoleDisplayName(),
    ]);
}

/**
 * Generic fallback for role methods
 */
public function __call($method, $arguments)
{
    // Check if it's a role method (starts with 'role')
    if (str_starts_with($method, 'role')) {
        // Extract the role page name from the method
        $page = strtolower(str_replace('role', '', $method));
        $page = preg_replace('/(?<!^)[A-Z]/', '-$0', $page); // Convert camelCase to kebab-case
        $page = ltrim($page, '-');

        $view = "help.role.{$page}";

        if (view()->exists($view)) {
            return view($view, [
                'role' => RoleHelper::getCurrentRole(),
                'roleDisplayName' => RoleHelper::getRoleDisplayName(),
                'quickTips' => RoleHelper::getQuickTips(),
            ]);
        }

        // Return generic view if specific one doesn't exist
        return view('help.role.generic', [
            'role' => RoleHelper::getCurrentRole(),
            'roleDisplayName' => RoleHelper::getRoleDisplayName(),
            'quickTips' => RoleHelper::getQuickTips(),
            'pageTitle' => ucfirst(str_replace('-', ' ', $page)) . ' Guide',
        ]);
    }

    throw new \BadMethodCallException("Method {$method} does not exist.");
}

}
