<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lease;
use App\Models\Billing;
use App\Models\SupportTicket;
use App\Models\DesignRequest;
use App\Models\MaintenanceRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SystemAdminController extends Controller
{
    /**
     * Display system admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_staff' => User::whereIn('role', ['designer', 'surveyor', 'technician', 'finance', 'account_manager'])->count(),
            'total_admins' => User::whereIn('role', ['system_admin', 'marketing_admin', 'technical_admin'])->count(),
            'active_leases' => Lease::where('status', 'active')->count(),
            'pending_leases' => Lease::where('status', 'pending')->count(),
            'total_revenue' => Billing::where('status', 'paid')->sum('amount'),
            'pending_billings' => Billing::where('status', 'pending')->count(),
            'open_tickets' => Ticket::whereIn('status', ['open', 'in_progress'])->count(),
            'active_design_requests' => DesignRequest::whereIn('status', ['assigned', 'in_progress'])->count(),
        ];

        // Recent activities
        $recentActivities = DB::table('audit_logs')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // System health
        $systemHealth = [
            'database_size' => $this->getDatabaseSize(),
            'storage_usage' => $this->getStorageUsage(),
            'last_backup' => $this->getLastBackupDate(),
            'active_sessions' => DB::table('sessions')->count(),
        ];

        return view('admin.system.dashboard', compact('stats', 'recentActivities', 'systemHealth'));
    }

    /**
     * Display system settings
     */
    public function settings()
    {
        $settings = [
            'system_name' => config('app.name'),
            'maintenance_mode' => config('app.maintenance_mode', false),
            'registration_enabled' => config('auth.registration_enabled', true),
            'backup_frequency' => config('backup.frequency', 'daily'),
        ];

        return view('admin.system.settings', compact('settings'));
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'system_name' => 'required|string|max:255',
            'maintenance_mode' => 'boolean',
            'registration_enabled' => 'boolean',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
        ]);

        // Update configuration (you might want to store these in database)
        // This is a simplified implementation
        session()->flash('success', 'System settings updated successfully.');

        return redirect()->route('admin.settings');
    }

    /**
     * Display all users
     */
    public function users()
    {
        $users = User::with('accountManager')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.system.users.index', compact('users'));
    }

    /**
     * Show user creation form
     */
    public function createUser()
    {
        $accountManagers = User::where('role', 'account_manager')->get();
        $roles = [
            'system_admin' => 'System Administrator',
            'marketing_admin' => 'Marketing Administrator',
            'technical_admin' => 'Technical Administrator',
            'finance' => 'Finance Manager',
            'designer' => 'Network Designer',
            'surveyor' => 'Field Surveyor',
            'technician' => 'Field Technician',
            'account_manager' => 'Account Manager',
            'customer' => 'Customer',
        ];

        return view('admin.system.users.create', compact('accountManagers', 'roles'));
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:system_admin,marketing_admin,technical_admin,finance,designer,surveyor,technician,account_manager,customer',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'account_manager_id' => 'nullable|exists:users,id',
        ]);

        // Generate temporary password
        $tempPassword = Str::random(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'role' => $validated['role'],
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'account_manager_id' => $validated['account_manager_id'],
            'account_status' => 'active',
        ]);

        // Send welcome email with temporary password
        // Mail::to($user->email)->send(new NewUserWelcome($user, $tempPassword));

        session()->flash('success', 'User created successfully. Temporary password has been generated.');

        return redirect()->route('admin.users.index');
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        $user->load(['accountManager', 'managedCustomers', 'supportTickets', 'leases']);

        return view('admin.system.users.show', compact('user'));
    }

    /**
     * Show user edit form
     */
    public function editUser(User $user)
    {
        $accountManagers = User::where('role', 'account_manager')->get();
        $roles = [
            'system_admin' => 'System Administrator',
            'marketing_admin' => 'Marketing Administrator',
            'technical_admin' => 'Technical Administrator',
            'finance' => 'Finance Manager',
            'designer' => 'Network Designer',
            'surveyor' => 'Field Surveyor',
            'technician' => 'Field Technician',
            'account_manager' => 'Account Manager',
            'customer' => 'Customer',
        ];

        return view('admin.system.users.edit', compact('user', 'accountManagers', 'roles'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'account_manager_id' => 'nullable|exists:users,id',
            'account_status' => 'required|in:active,inactive,suspended',
        ]);

        $user->update($validated);

        session()->flash('success', 'User updated successfully.');

        return redirect()->route('admin.users.show', $user);
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:system_admin,marketing_admin,technical_admin,finance,designer,surveyor,technician,account_manager,customer',
        ]);

        $user->update(['role' => $validated['role']]);

        session()->flash('success', 'User role updated successfully.');

        return redirect()->back();
    }

    /**
     * Delete user
     */
    public function destroyUser(User $user)
    {
        // Prevent deletion of own account
        if ($user->id === Auth::id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return redirect()->back();
        }

        $user->delete();

        session()->flash('success', 'User deleted successfully.');

        return redirect()->route('admin.users.index');
    }

    /**
     * Activate user
     */
    public function activateUser(User $user)
    {
        $user->update(['account_status' => 'active']);

        session()->flash('success', 'User activated successfully.');

        return redirect()->back();
    }

    /**
     * Suspend user
     */
    public function suspendUser(User $user)
    {
        $user->update(['account_status' => 'suspended']);

        session()->flash('success', 'User suspended successfully.');

        return redirect()->back();
    }

    /**
     * Display role management
     */
    public function roles()
    {
        $roles = [
            'system_admin' => [
                'name' => 'System Administrator',
                'permissions' => ['all'],
            ],
            'marketing_admin' => [
                'name' => 'Marketing Administrator',
                'permissions' => ['view_analytics', 'manage_campaigns', 'view_customer_insights'],
            ],
            'technical_admin' => [
                'name' => 'Technical Administrator',
                'permissions' => ['view_network_monitor', 'manage_infrastructure', 'view_technical_reports'],
            ],
            // Add other roles...
        ];

        return view('admin.system.roles.index', compact('roles'));
    }

    /**
     * Update permissions
     */
    public function updatePermissions(Request $request)
    {
        // Implement permission update logic
        session()->flash('success', 'Permissions updated successfully.');

        return redirect()->back();
    }

    /**
     * Display system reports
     */
    public function systemReports()
    {
        $reports = [
            'user_activity' => $this->getUserActivityReport(),
            'system_usage' => $this->getSystemUsageReport(),
            'financial_summary' => $this->getFinancialSummaryReport(),
        ];

        return view('admin.system.reports.index', compact('reports'));
    }

    /**
     * Display audit logs
     */
    public function auditLogs()
    {
        $logs = DB::table('audit_logs')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.system.audit-logs.index', compact('logs'));
    }

    /**
     * Get database size
     */
    private function getDatabaseSize()
    {
        // Implementation for getting database size
        return '2.5 GB';
    }

    /**
     * Get storage usage
     */
    private function getStorageUsage()
    {
        // Implementation for getting storage usage
        return '65%';
    }

    /**
     * Get last backup date
     */
    private function getLastBackupDate()
    {
        // Implementation for getting last backup date
        return now()->subDays(1)->format('Y-m-d H:i:s');
    }

    /**
     * Get user activity report
     */
    private function getUserActivityReport()
    {
        // Implementation for user activity report
        return [];
    }

    /**
     * Get system usage report
     */
    private function getSystemUsageReport()
    {
        // Implementation for system usage report
        return [];
    }

    /**
     * Get financial summary report
     */
    private function getFinancialSummaryReport()
    {
        // Implementation for financial summary report
        return [];
    }
}
