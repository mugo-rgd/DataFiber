<?php

namespace App\Console\Commands;

use App\Models\FiberNode;
use App\Models\FiberNetwork;
use App\Models\FiberSegment;
use App\Models\FiberPricing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportFiberData extends Command
{
    protected $signature = 'fiber:import {file : "G:\project\darkfibre-crm\storage\app\Copy of KPLC DARK FIBER INVENTORY DEC 2025 NEW"}';
    protected $description = 'Import fiber data from CSV file';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("Importing fiber data from {$file}...");

        DB::beginTransaction();
        try {
            // Initialize pricing data
            $this->initPricingData();

            // Read and parse CSV
            $handle = fopen($file, 'r');
            $header = fgetcsv($handle);

            $networks = [];
            $segments = [];
            $nodes = [];

            while (($row = fgetcsv($handle)) !== FALSE) {
                $data = array_combine($header, $row);

                // Extract network data
                $networkId = $this->extractNetworkId($data);

                if (!isset($networks[$networkId])) {
                    $networks[$networkId] = $this->createNetworkFromData($data);
                }

                // Create segment
                $segment = $this->createSegmentFromData($data, $networkId);
                $segments[] = $segment;

                // Create nodes if they don't exist
                $this->createNodeIfNotExists($data['SOURCE'], $data['Source Coordinates']);
                $this->createNodeIfNotExists($data['DESTINATION'], $data['Destination Coordinates']);
            }

            fclose($handle);

            // Save to database
            foreach ($networks as $network) {
                FiberNetwork::create($network);
            }

            foreach ($segments as $segment) {
                FiberSegment::create($segment);
            }

            DB::commit();
            $this->info("Import completed successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function initPricingData()
    {
        $pricingData = [
            [
                'link_type' => 'Metro',
                'base_rate_km_kes' => 30000,
                'base_rate_km_usd' => 200,
                'volume_discount_threshold' => 50,
                'volume_discount_percent' => 10,
                'description' => 'Metro area connections, high reliability'
            ],
            [
                'link_type' => 'Premium',
                'base_rate_km_kes' => 50000,
                'base_rate_km_usd' => 333.33,
                'volume_discount_threshold' => 100,
                'volume_discount_percent' => 15,
                'description' => 'Long-haul premium routes, SLA guaranteed'
            ],
            [
                'link_type' => 'Non Premium',
                'base_rate_km_kes' => 15000,
                'base_rate_km_usd' => 100,
                'volume_discount_threshold' => 0,
                'volume_discount_percent' => 0,
                'description' => 'Standard connectivity, best effort'
            ]
        ];

        foreach ($pricingData as $data) {
            FiberPricing::updateOrCreate(
                ['link_type' => $data['link_type']],
                $data
            );
        }
    }

    private function parseCoordinates($coordString)
    {
        // Parse DMS format: 1°18'37.84"S ,  36°49'42.06"E
        preg_match('/(\d+)°(\d+)\'([\d.]+)"([NS]),?\s*(\d+)°(\d+)\'([\d.]+)"([EW])/', $coordString, $matches);

        if (count($matches) >= 9) {
            $lat = $this->dmsToDecimal($matches[1], $matches[2], $matches[3], $matches[4]);
            $lng = $this->dmsToDecimal($matches[5], $matches[6], $matches[7], $matches[8]);
            return ['lat' => $lat, 'lng' => $lng];
        }

        return null;
    }

    private function dmsToDecimal($degrees, $minutes, $seconds, $direction)
    {
        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

        if ($direction == 'S' || $direction == 'W') {
            $decimal = -$decimal;
        }

        return round($decimal, 7);
    }

    private function extractNetworkId($data)
    {
        // Generate network ID based on region and source/destination
        $region = $data['Region'] ?? 'Unknown';
        $source = substr($data['SOURCE'], 0, 3);
        $dest = substr($data['DESTINATION'], 0, 3);

        return strtoupper(substr($region, 0, 2) . '-' . $source . '-' . $dest);
    }

    private function createNetworkFromData($data)
    {
        $sourceCoord = $this->parseCoordinates($data['Source Coordinates']);
        $destCoord = $this->parseCoordinates($data['Destination Coordinates']);

        return [
            'network_id' => $this->extractNetworkId($data),
            'network_name' => $data['SOURCE'] . ' to ' . $data['DESTINATION'],
            'region' => $data['Region'] ?? 'Unknown',
            'total_distance_km' => floatval($data['Link Distance'] ?? 0),
            'fiber_cores' => intval($data['Fiber cores'] ?? 48),
            'link_type' => $this->determineLinkType($data),
            'cost_per_month' => 0,
            'currency' => 'KES',
            'status' => $this->determineStatus($data),
            'connection_sequence' => $data['SOURCE'] . ' → ' . $data['DESTINATION']
        ];
    }

    private function createSegmentFromData($data, $networkId)
    {
        $sourceCoord = $this->parseCoordinates($data['Source Coordinates']);
        $destCoord = $this->parseCoordinates($data['Destination Coordinates']);
        $distance = floatval($data['Link Distance'] ?? 0);
        $linkType = $this->determineLinkType($data);
        $fiberCores = intval($data['Fiber cores'] ?? 48);

        return [
            'segment_id' => $networkId . '-S001',
            'network_id' => $networkId,
            'segment_order' => 1,
            'source_name' => $data['SOURCE'],
            'source_lat' => $sourceCoord['lat'],
            'source_lon' => $sourceCoord['lng'],
            'destination_name' => $data['DESTINATION'],
            'dest_lat' => $destCoord['lat'],
            'dest_lon' => $destCoord['lng'],
            'cable_type' => $data['Cable Construction'] ?? 'Unknown',
            'distance_km' => $distance,
            'fiber_cores' => $fiberCores,
            'link_type' => $linkType,
            'cost_per_month' => FiberPricing::calculateCost($distance, $linkType, $fiberCores),
            'currency' => 'KES',
            'status' => $this->determineStatus($data)
        ];
    }

    private function determineLinkType($data)
    {
        $source = strtoupper($data['SOURCE'] ?? '');
        $dest = strtoupper($data['DESTINATION'] ?? '');
        $distance = floatval($data['Link Distance'] ?? 0);

        // Determine link type based on location and distance
        if (strpos($source, 'NAIROBI') !== false || strpos($dest, 'NAIROBI') !== false) {
            if ($distance <= 20) {
                return 'Metro';
            }
        }

        if ($distance > 50) {
            return 'Premium';
        }

        if ($distance > 20) {
            return 'Non Premium';
        }

        return 'Metro';
    }

    private function determineStatus($data)
    {
        $remark = strtoupper($data['Remark'] ?? '');

        if (strpos($remark, 'DAMAGED') !== false) {
            return 'Damaged';
        }

        return 'Active';
    }

    private function createNodeIfNotExists($name, $coordString)
    {
        if (empty($name) || empty($coordString)) {
            return;
        }

        $coords = $this->parseCoordinates($coordString);
        if (!$coords) {
            return;
        }

        $nodeId = 'NODE-' . substr(md5($name), 0, 8);

        FiberNode::firstOrCreate(
            ['node_name' => $name],
            [
                'node_id' => $nodeId,
                'node_type' => $this->determineNodeType($name),
                'latitude' => $coords['lat'],
                'longitude' => $coords['lng'],
                'region' => $this->determineRegionFromName($name)
            ]
        );
    }

    private function determineNodeType($name)
    {
        $name = strtoupper($name);

        if (strpos($name, 'SS') !== false || strpos($name, 'SUBSTATION') !== false) {
            return 'SS';
        }

        if (strpos($name, 'OFFICE') !== false || strpos($name, 'DEPOT') !== false) {
            return 'OFFICE';
        }

        if (strpos($name, 'DATA CENTER') !== false) {
            return 'DATA_CENTER';
        }

        return 'OTHER';
    }

    private function determineRegionFromName($name)
    {
        $name = strtoupper($name);

        if (strpos($name, 'NAIROBI') !== false) {
            if (strpos($name, 'WEST') !== false) return 'Nairobi West';
            if (strpos($name, 'NORTH') !== false) return 'Nairobi North';
            if (strpos($name, 'SOUTH') !== false) return 'Nairobi South';
            return 'Nairobi';
        }

        if (strpos($name, 'MOMBASA') !== false || strpos($name, 'MALINDI') !== false || strpos($name, 'LAMU') !== false) {
            return 'Coast Region';
        }

        if (strpos($name, 'KISUMU') !== false || strpos($name, 'KAKAMEGA') !== false) {
            return 'West Kenya';
        }

        return 'Other';
    }
}
