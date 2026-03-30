<?php

namespace App\Console\Commands;

use App\Models\FiberNetwork;
use App\Models\FiberNode;
use App\Models\FiberSegment;
use App\Models\FiberPricing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyFiberData extends Command
{
    protected $signature = 'fiber:verify';
    protected $description = 'Verify imported fiber data';

    public function handle()
    {
        $this->info('=== FIBER DATA VERIFICATION ===');

        // Check pricing data
        $pricingCount = FiberPricing::count();
        $this->info("Pricing records: {$pricingCount}");

        // Check networks
        $networkCount = FiberNetwork::count();
        $this->info("Networks: {$networkCount}");

        // Check segments
        $segmentCount = FiberSegment::count();
        $this->info("Segments: {$segmentCount}");

        // Check nodes
        $nodeCount = FiberNode::count();
        $this->info("Nodes: {$nodeCount}");

        // Show summary by region
        $this->info("\n=== NETWORKS BY REGION ===");
        $regions = FiberNetwork::select('region', DB::raw('count(*) as count'), DB::raw('sum(total_distance_km) as total_distance'))
            ->groupBy('region')
            ->get();

        foreach ($regions as $region) {
            $this->line("{$region->region}: {$region->count} networks, {$region->total_distance} km");
        }

        // Show summary by link type
        $this->info("\n=== NETWORKS BY LINK TYPE ===");
        $linkTypes = FiberNetwork::select('link_type', DB::raw('count(*) as count'), DB::raw('sum(total_distance_km) as total_distance'))
            ->groupBy('link_type')
            ->get();

        foreach ($linkTypes as $type) {
            $this->line("{$type->link_type}: {$type->count} networks, {$type->total_distance} km");
        }

        // Show summary by status
        $this->info("\n=== NETWORKS BY STATUS ===");
        $statuses = FiberNetwork::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        foreach ($statuses as $status) {
            $this->line("{$status->status}: {$status->count} networks");
        }

        // Show total distance and revenue
        $totalDistance = FiberNetwork::sum('total_distance_km');
        $totalRevenue = FiberNetwork::sum('cost_per_month');

        $this->info("\n=== TOTALS ===");
        $this->line("Total Distance: " . number_format($totalDistance, 2) . " km");
        $this->line("Total Monthly Revenue: KES " . number_format($totalRevenue, 2));

        // Show sample networks
        $this->info("\n=== SAMPLE NETWORKS (first 5) ===");
        $networks = FiberNetwork::with('segments')->limit(5)->get();

        foreach ($networks as $network) {
            $this->line("{$network->network_id}: {$network->network_name}");
            $this->line("  Region: {$network->region}");
            $this->line("  Distance: {$network->total_distance_km} km");
            $this->line("  Type: {$network->link_type}");
            $this->line("  Status: {$network->status}");
            $this->line("  Cost: KES {$network->cost_per_month}");
            $this->line("  Segments: {$network->segments->count()}");
            $this->line("---");
        }

        return 0;
    }
}
