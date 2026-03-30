<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\DesignRequest;
use App\Models\NetworkEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TechnicalAdminController extends Controller
{
    /**
     * Display technical admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_network_nodes' => NetworkEquipment::count(),
            'active_maintenance' => MaintenanceRequest::whereIn('status', ['open', 'in_progress'])->count(),
            'network_uptime' => $this->calculateNetworkUptime(),
            'pending_designs' => DesignRequest::where('status', 'pending')->count(),
            'active_leases' => Lease::where('status', 'active')->count(),
            'equipment_health' => $this->getEquipmentHealthScore(),
        ];

        // Network status
        $networkStatus = $this->getNetworkStatus();

        // Recent incidents
        $recentIncidents = MaintenanceRequest::with('assignedTechnician')
            ->where('priority', 'high')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Performance metrics
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('admin.technical.dashboard', compact('stats', 'networkStatus', 'recentIncidents', 'performanceMetrics'));
    }

    /**
     * Display network monitor
     */
    public function networkMonitor()
    {
        $networkEquipment = NetworkEquipment::with('location')
            ->orderBy('status')
            ->get();

        $networkAlerts = $this->getNetworkAlerts();

        $bandwidthUsage = $this->getBandwidthUsage();

        return view('admin.technical.network-monitor.index', compact('networkEquipment', 'networkAlerts', 'bandwidthUsage'));
    }

    /**
     * Display infrastructure management
     */
    public function infrastructure()
    {
        $infrastructure = [
            'data_centers' => $this->getDataCenters(),
            'network_nodes' => $this->getNetworkNodes(),
            'fiber_routes' => $this->getFiberRoutes(),
            'equipment_inventory' => $this->getEquipmentInventory(),
        ];

        return view('admin.technical.infrastructure.index', compact('infrastructure'));
    }

    /**
     * Display technical reports
     */
    public function technicalReports()
    {
        $reports = [
            'network_performance' => $this->generateNetworkPerformanceReport(),
            'maintenance_history' => $this->generateMaintenanceHistoryReport(),
            'capacity_planning' => $this->generateCapacityPlanningReport(),
            'incident_reports' => $this->generateIncidentReports(),
        ];

        return view('admin.technical.reports.index', compact('reports'));
    }

    /**
     * Display system health
     */
    public function systemHealth()
    {
        $systemHealth = [
            'server_status' => $this->getServerStatus(),
            'database_performance' => $this->getDatabasePerformance(),
            'storage_metrics' => $this->getStorageMetrics(),
            'backup_status' => $this->getBackupStatus(),
            'security_status' => $this->getSecurityStatus(),
        ];

        return view('admin.technical.system-health.index', compact('systemHealth'));
    }

    /**
     * Calculate network uptime
     */
    private function calculateNetworkUptime()
    {
        $totalTime = 30 * 24 * 60; // 30 days in minutes
        $downtime = MaintenanceRequest::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('downtime_minutes');

        return round((($totalTime - $downtime) / $totalTime) * 100, 2);
    }

    /**
     * Get equipment health score
     */
    private function getEquipmentHealthScore()
    {
        $totalEquipment = NetworkEquipment::count();
        $healthyEquipment = NetworkEquipment::where('status', 'operational')->count();

        return $totalEquipment > 0 ? round(($healthyEquipment / $totalEquipment) * 100, 2) : 100;
    }

    /**
     * Get network status
     */
    private function getNetworkStatus()
    {
        return [
            'core_network' => 'healthy',
            'access_network' => 'degraded',
            'backbone' => 'healthy',
            'last_updated' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics()
    {
        return [
            'latency' => [
                'current' => 45,
                'threshold' => 100,
                'unit' => 'ms'
            ],
            'packet_loss' => [
                'current' => 0.2,
                'threshold' => 1,
                'unit' => '%'
            ],
            'throughput' => [
                'current' => 950,
                'threshold' => 1000,
                'unit' => 'Gbps'
            ],
        ];
    }

    /**
     * Get network alerts
     */
    private function getNetworkAlerts()
    {
        return MaintenanceRequest::with(['equipment', 'assignedTechnician'])
            ->whereIn('status', ['open', 'in_progress'])
            ->where('priority', 'high')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get bandwidth usage
     */
    private function getBandwidthUsage()
    {
        return DB::table('bandwidth_usage')
            ->select('timestamp', 'usage_gbps')
            ->where('timestamp', '>=', now()->subDays(7))
            ->orderBy('timestamp')
            ->get()
            ->groupBy(function($item) {
                return date('Y-m-d', strtotime($item->timestamp));
            })
            ->map(function($group) {
                return round($group->avg('usage_gbps'), 2);
            });
    }

    /**
     * Get data centers
     */
    private function getDataCenters()
    {
        return DB::table('data_centers')
            ->select('name', 'location', 'status', 'capacity_utilization')
            ->get();
    }

    /**
     * Get network nodes
     */
    private function getNetworkNodes()
    {
        return NetworkEquipment::with('location')
            ->select('id', 'name', 'type', 'status', 'location_id')
            ->get()
            ->groupBy('type');
    }

    /**
     * Get fiber routes
     */
    private function getFiberRoutes()
    {
        return DB::table('fiber_routes')
            ->select('route_name', 'length_km', 'status', 'available_capacity')
            ->get();
    }

    /**
     * Get equipment inventory
     */
    private function getEquipmentInventory()
    {
        return NetworkEquipment::select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Generate network performance report
     */
    private function generateNetworkPerformanceReport()
    {
        return [
            'period' => 'Last 7 Days',
            'average_latency' => 42,
            'max_latency' => 89,
            'packet_loss' => 0.15,
            'throughput_utilization' => 78,
            'downtime_minutes' => 45,
        ];
    }

    /**
     * Generate maintenance history report
     */
    private function generateMaintenanceHistoryReport()
    {
        return MaintenanceRequest::with(['equipment', 'assignedTechnician'])
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    /**
     * Generate capacity planning report
     */
    private function generateCapacityPlanningReport()
    {
        return [
            'current_utilization' => 65,
            'projected_growth' => 25,
            'recommended_upgrades' => [
                'core_routers' => 2,
                'fiber_capacity' => '40km',
                'data_center_racks' => 5,
            ],
        ];
    }

    /**
     * Generate incident reports
     */
    private function generateIncidentReports()
    {
        return MaintenanceRequest::with(['equipment', 'assignedTechnician'])
            ->where('priority', 'high')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get server status
     */
    private function getServerStatus()
    {
        return [
            'web_servers' => 'healthy',
            'database_servers' => 'healthy',
            'application_servers' => 'degraded',
            'file_servers' => 'healthy',
        ];
    }

    /**
     * Get database performance
     */
    private function getDatabasePerformance()
    {
        return [
            'query_performance' => 'good',
            'connection_pool' => 'optimal',
            'replication_lag' => '0 seconds',
            'backup_size' => '2.3 GB',
        ];
    }

    /**
     * Get storage metrics
     */
    private function getStorageMetrics()
    {
        return [
            'total_capacity' => '10 TB',
            'used_capacity' => '6.5 TB',
            'available_capacity' => '3.5 TB',
            'utilization_rate' => '65%',
        ];
    }

    /**
     * Get backup status
     */
    private function getBackupStatus()
    {
        return [
            'last_successful_backup' => now()->subHours(6)->format('Y-m-d H:i:s'),
            'backup_size' => '45 GB',
            'retention_period' => '30 days',
            'backup_health' => 'good',
        ];
    }

    /**
     * Get security status
     */
    private function getSecurityStatus()
    {
        return [
            'firewall_status' => 'active',
            'intrusion_detection' => 'enabled',
            'ssl_certificates' => 'valid',
            'security_patches' => 'up_to_date',
        ];
    }
}
