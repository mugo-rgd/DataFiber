<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class RoleHelper
{
    /**
     * Get current user's role from database enum
     */
    public static function getCurrentRole()
    {
        $user = Auth::user();

        if (!$user) {
            return 'guest';
        }

        // Map database role values to our system
        $roleMap = [
            'admin' => 'admin',
            'finance' => 'finance',
            'designer' => 'designer',
            'surveyor' => 'surveyor',
            'technician' => 'technician',
            'account_manager' => 'account_manager',
            'system_admin' => 'system_admin',
            'accountmanager_admin' => 'accountmanager_admin',
            'technical_admin' => 'technical_admin',
            'customer' => 'customer',
            'ict_engineer' => 'ict_engineer',
            'debt_manager' => 'debt_manager',
            'compliance_officer' => 'compliance_officer',
            'viewer' => 'viewer',
            'county_ict_engineer' => 'county_ict_engineer',
            'regional_manager' => 'regional_manager',
        ];

        return $roleMap[$user->role] ?? 'guest';
    }

    /**
     * Get role display name
     */
    public static function getRoleDisplayName()
    {
        $roles = [
            'admin' => 'System Administrator',
            'finance' => 'Finance Officer',
            'designer' => 'Design Engineer',
            'surveyor' => 'Field Surveyor',
            'technician' => 'Network Technician',
            'account_manager' => 'Account Manager',
            'system_admin' => 'System Administrator',
            'accountmanager_admin' => 'Account Manager Admin',
            'technical_admin' => 'Technical Administrator',
            'customer' => 'Customer',
            'ict_engineer' => 'ICT Engineer',
            'debt_manager' => 'Debt Manager',
            'compliance_officer' => 'Compliance Officer',
            'viewer' => 'Viewer',
            'county_ict_engineer' => 'County ICT Engineer',
            'regional_manager' => 'Regional Manager',
            'guest' => 'Guest User'
        ];

        return $roles[self::getCurrentRole()] ?? 'System User';
    }

    /**
     * Get role-specific quick tips
     */
    public static function getQuickTips()
    {
        $role = self::getCurrentRole();

        $tips = [
            'admin' => [
                'Monitor system performance and user activity daily',
                'Review security logs weekly for unauthorized access',
                'Backup database before major system updates',
                'Manage user roles and permissions carefully',
                'Keep CAK compliance templates up to date'
            ],
            'finance' => [
                'Review aging report weekly to track overdue payments',
                "Current overdue: \$3,202,608.66 USD | KSh 43,440,500.10",
                'Send payment reminders 7 days before due date',
                'Reconcile payments daily for accurate reporting',
                'Generate monthly statements by the 5th of each month'
            ],
            'designer' => [
                'Check pending design requests daily (currently 2 pending)',
                'Use the Kenya Fibre Dashboard for route planning',
                'Coordinate with surveyors for accurate field data',
                'Review quotations before sending to customers',
                'Document all design changes in the system'
            ],
            'surveyor' => [
                'Complete assigned surveys within SLA timeframe',
                'Upload accurate GPS coordinates for all sites',
                'Take clear photos of proposed installation points',
                'Update survey status promptly after completion',
                'Report any site access issues immediately'
            ],
            'technician' => [
                'Respond to critical work orders within 1 hour',
                'Update work order status in real-time',
                'Document all equipment installed with serial numbers',
                'Take before/after photos of installations',
                'Complete safety checklists before starting work'
            ],
            'account_manager' => [
                'Review customer portfolio weekly (7 active customers)',
                'Follow up on overdue payments immediately',
                'Schedule quarterly business reviews with top customers',
                'Monitor customer satisfaction score (currently 100%)',
                'Identify upsell opportunities in your portfolio'
            ],
            'system_admin' => [
                'Monitor total users (92) and active leases (286)',
                'Review pending designs (2) daily',
                'Check system logs for errors each morning',
                'Update system documentation after changes',
                'Coordinate maintenance windows with technical team'
            ],
            'accountmanager_admin' => [
                'Track team performance and customer satisfaction',
                'Review sales pipeline weekly',
                'Monitor customer churn risk indicators',
                'Conduct monthly team training sessions',
                'Analyze customer feedback for improvements'
            ],
            'technical_admin' => [
                'Monitor network uptime and performance metrics',
                'Review pending design requests (2) daily',
                'Track lease utilization (286 active)',
                'Coordinate maintenance with technicians',
                'Update fibre inventory after deployments'
            ],
            'customer' => [
                'Complete your company profile for full access',
                'Currently at 33% completion - 6 more fields needed',
                'Upload required documents (6 pending)',
                'Check invoices and payment due dates',
                'Submit support tickets for technical issues'
            ],
            'ict_engineer' => [
                'Monitor network performance in your region',
                'Current uptime: 99.5% over last 30 days',
                'Respond to ticket assignments promptly',
                'Coordinate with technicians for field work',
                'Document all network changes in the system'
            ],
            'debt_manager' => [
                "Focus on top debtors: MINISTRY OF ICT (KSh 23M), KENGEN (KSh 18.5M)",
                'Review aging report daily for 30/60/90 day buckets',
                'Escalate 90+ day overdue accounts to legal team',
                'Negotiate payment plans for struggling customers',
                'Document all collection activities in the system'
            ],
            'compliance_officer' => [
                'Submit quarterly CAK returns within 15 days after quarter end',
                'Current Q1 2025/2026 due by October 15, 2026',
                'Verify all data before final submission',
                'Keep digital signature and company stamp ready',
                'Export compliance data quarterly for record keeping'
            ],
            'viewer' => [
                'Use filters to narrow down report data',
                'Export reports to Excel for offline analysis',
                'Bookmark important dashboards for quick access',
                'Request data export permission for analysis',
                'Contact your manager for additional access needs'
            ],
            'county_ict_engineer' => [
                'Monitor county-level ICT infrastructure',
                'Coordinate with national team on projects',
                'Submit monthly county performance reports',
                'Track budget utilization for county projects',
                'Report any county-specific challenges promptly'
            ],
            'regional_manager' => [
                'Review regional performance metrics weekly',
                'Conduct quarterly regional business reviews',
                'Identify regional growth opportunities',
                'Manage regional budget and resources',
                'Report regional challenges to headquarters'
            ],
        ];

        return $tips[$role] ?? ['Welcome to DarkFibre CRM. Contact your administrator for role-specific guidance'];
    }

    /**
     * Get role dashboard metrics
     */
    public static function getDashboardMetrics()
    {
        $role = self::getCurrentRole();

        $metrics = [
            'finance' => [
                'total_overdue_usd' => '3,202,608.66',
                'total_overdue_kes' => '43,440,500.10',
                'pending_invoices' => '104',
                'collection_rate' => '0%',
                'avg_payment_days' => '0',
                'active_customers' => '52'
            ],
            'designer' => [
                'pending_requests' => '2',
                'in_progress' => '0',
                'completed' => '0',
                'quotations_sent' => '0',
                'conversion_rate' => '0%'
            ],
            'account_manager' => [
                'customers' => '7',
                'active_support' => '0',
                'satisfaction_rate' => '100%',
                'payment_health' => '0 pending'
            ],
            'technical_admin' => [
                'total_users' => '92',
                'active_leases' => '286',
                'pending_designs' => '2',
                'pending_tickets' => '0',
                'uptime' => '99.5%'
            ],
            'customer' => [
                'profile_completion' => '33%',
                'documents_uploaded' => '0',
                'documents_required' => '6',
                'fields_completed' => '6',
                'fields_total' => '11'
            ],
            'debt_manager' => [
                'total_overdue_usd' => '3,202,608.66',
                'total_overdue_kes' => '43,440,500.10',
                'overdue_invoices' => '104',
                'top_debtor_usd' => '373,155.09',
                'top_debtor_kes' => '23,006,204.10',
                'max_days_overdue' => '84'
            ],
            'ict_engineer' => [
                'network_uptime' => '99.5%',
                'pending_tickets' => '0',
                'active_networks' => '0',
                'servers_online' => '0',
                'security_alerts' => '3'
            ]
        ];

        return $metrics[$role] ?? [];
    }
}
