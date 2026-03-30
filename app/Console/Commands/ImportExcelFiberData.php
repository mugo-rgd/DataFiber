<?php

namespace App\Console\Commands;

use App\Models\FiberNetwork;
use App\Models\FiberSegment;
use App\Models\FiberNode;
use App\Models\FiberPricing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportExcelFiberData extends Command
{
    protected $signature = 'fiber:import-excel {file : Path to Excel file}';
    protected $description = 'Import fiber data from Excel file';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("Loading Excel file: {$file}");

        try {
            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            DB::beginTransaction();

            // Initialize pricing data
            $this->initPricingData();

            $currentRegion = null;
            $importedCount = 0;

            foreach ($rows as $index => $row) {
                // Skip header rows
                if ($index < 4) continue;

                // Check if this is a region header
                if (!empty($row[0]) && empty($row[1]) && empty($row[2])) {
                    $currentRegion = $row[0];
                    $this->info("Processing region: {$currentRegion}");
                    continue;
                }

                // Skip empty rows or total rows
                if (empty($row[1]) || $row[1] === 'TOTAL' || empty($row[2])) {
                    continue;
                }

                // Extract data
                $source = $row[2] ?? null;
                $sourceCoords = $row[3] ?? null;
                $destination = $row[4] ?? null;
                $destCoords = $row[5] ?? null;
                $cableType = $row[6] ?? null;
                $distance = $row[7] ?? null;
                $fiberCores = $row[8] ?? 48;
                $remark = $row[9] ?? null;

                if (!$source || !$destination || !$distance) {
                    continue;
                }

                // Parse coordinates
                $sourceCoord = $this->parseCoordinates($sourceCoords);
                $destCoord = $this->parseCoordinates($destCoords);

                if (!$sourceCoord || !$destCoord) {
                    $this->warn("Could not parse coordinates for: {$source} -> {$destination}");
                    continue;
                }

                // Create nodes
                $sourceNode = $this->createNode($source, $sourceCoord, $currentRegion);
                $destNode = $this->createNode($destination, $destCoord, $currentRegion);

                // Determine link type and status
                $linkType = $this->determineLinkType($source, $destination, floatval($distance));
                $status = $this->determineStatus($remark);

                // Create network
                $networkId = $this->generateNetworkId($source, $destination, $currentRegion);
                $network = FiberNetwork::updateOrCreate(
                    ['network_id' => $networkId],
                    [
                        'network_name' => $source . ' to ' . $destination,
                        'region' => $currentRegion ?? 'Unknown',
                        'total_distance_km' => floatval($distance),
                        'fiber_cores' => intval($fiberCores),
                        'link_type' => $linkType,
                        'cost_per_month' => FiberPricing::calculateCost(floatval($distance), $linkType, intval($fiberCores)),
                        'currency' => 'KES',
                        'status' => $status,
                        'connection_sequence' => $source . ' → ' . $destination
                    ]
                );

                // Create segment
                FiberSegment::updateOrCreate(
                    ['segment_id' => $networkId . '-S001'],
                    [
                        'network_id' => $networkId,
                        'segment_order' => 1,
                        'source_name' => $source,
                        'source_lat' => $sourceCoord['lat'],
                        'source_lon' => $sourceCoord['lng'],
                        'destination_name' => $destination,
                        'dest_lat' => $destCoord['lat'],
                        'dest_lon' => $destCoord['lng'],
                        'cable_type' => $cableType ?? 'Unknown',
                        'distance_km' => floatval($distance),
                        'fiber_cores' => intval($fiberCores),
                        'link_type' => $linkType,
                        'cost_per_month' => FiberPricing::calculateCost(floatval($distance), $linkType, intval($fiberCores)),
                        'currency' => 'KES',
                        'status' => $status
                    ]
                );

                $importedCount++;

                if ($importedCount % 10 == 0) {
                    $this->info("Imported {$importedCount} links...");
                }
            }

            DB::commit();
            $this->info("Successfully imported {$importedCount} fiber links!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Import failed: " . $e->getMessage());
            $this->error($e->getTraceAsString());
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
        if (empty($coordString)) return null;

        // Parse DMS format: 1°18'37.84"S ,  36°49'42.06"E
        preg_match('/(\d+)°(\d+)\'([\d.]+)"([NS]),?\s*(\d+)°(\d+)\'([\d.]+)"([EW])/', $coordString, $matches);

        if (count($matches) >= 9) {
            $lat = $this->dmsToDecimal($matches[1], $matches[2], $matches[3], $matches[4]);
            $lng = $this->dmsToDecimal($matches[5], $matches[6], $matches[7], $matches[8]);
            return ['lat' => $lat, 'lng' => $lng];
        }

        // Try decimal format
        if (preg_match('/(-?\d+\.\d+),\s*(-?\d+\.\d+)/', $coordString, $matches)) {
            return ['lat' => floatval($matches[1]), 'lng' => floatval($matches[2])];
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

    private function createNode($name, $coords, $region)
    {
        $nodeId = 'NODE-' . substr(md5($name), 0, 8);
        $nodeType = $this->determineNodeType($name);

        return FiberNode::firstOrCreate(
            ['node_name' => $name],
            [
                'node_id' => $nodeId,
                'node_type' => $nodeType,
                'latitude' => $coords['lat'],
                'longitude' => $coords['lng'],
                'region' => $region ?? $this->determineRegionFromName($name)
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

        if (strpos($name, 'NAIROBI') !== false) return 'Nairobi';
        if (strpos($name, 'MOMBASA') !== false) return 'Coast';
        if (strpos($name, 'KISUMU') !== false) return 'West Kenya';
        if (strpos($name, 'NAKURU') !== false) return 'Central Rift';
        if (strpos($name, 'ELDORET') !== false) return 'North Rift';

        return 'Other';
    }

    private function determineLinkType($source, $destination, $distance)
    {
        $source = strtoupper($source);
        $dest = strtoupper($destination);

        // Premium routes (long distance, critical infrastructure)
        if ($distance > 100 ||
            (strpos($source, 'NAIROBI') !== false && strpos($dest, 'MOMBASA') !== false) ||
            (strpos($source, 'NAIROBI') !== false && strpos($dest, 'KISUMU') !== false)) {
            return 'Premium';
        }

        // Metro routes (within Nairobi or major cities)
        if (strpos($source, 'NAIROBI') !== false || strpos($dest, 'NAIROBI') !== false ||
            strpos($source, 'MOMBASA') !== false || strpos($dest, 'MOMBASA') !== false) {
            if ($distance <= 30) {
                return 'Metro';
            }
        }

        // Default to Non Premium
        return 'Non Premium';
    }

    private function determineStatus($remark)
    {
        if (empty($remark)) return 'Active';

        $remark = strtoupper($remark);
        if (strpos($remark, 'DAMAGED') !== false) {
            return 'Damaged';
        }

        return 'Active';
    }

    private function generateNetworkId($source, $destination, $region)
    {
        $prefix = substr(preg_replace('/[^A-Z]/', '', strtoupper($region ?? 'XX')), 0, 2);
        $sourceCode = substr(preg_replace('/[^A-Z]/', '', strtoupper($source)), 0, 3);
        $destCode = substr(preg_replace('/[^A-Z]/', '', strtoupper($destination)), 0, 3);

        return $prefix . '-' . $sourceCode . '-' . $destCode . '-' . rand(100, 999);
    }
}
