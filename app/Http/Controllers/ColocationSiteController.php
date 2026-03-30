<?php
// app/Http/Controllers/ColocationSiteController.php

namespace App\Http\Controllers;

use App\Models\ColocationSite;
use App\Http\Requests\StoreColocationSiteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ColocationSiteController extends Controller
{
    public function store(StoreColocationSiteRequest $request): JsonResponse
    {
        Log::info('Starting colocation sites storage process');

        $validated = $request->validated();
        $sites = $validated['colocation_sites'];
        $designRequestId = $validated['design_request_id'];

        Log::info('Received data:', [
            'design_request_id' => $designRequestId,
            'sites_count' => count($sites),
            'sites_data' => $sites
        ]);

        // Step 1: Filter out empty or invalid sites
        $validSites = $this->filterValidSites($sites);

        Log::info('Valid sites after filtering:', [
            'valid_count' => count($validSites),
            'valid_sites' => $validSites
        ]);

        if (empty($validSites)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid colocation sites to save'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Step 2: Delete existing sites for this design request (optional)
            // ColocationSite::where('design_request_id', $designRequestId)->delete();

            // Step 3: Prepare data for insertion
            $sitesToInsert = $this->prepareSitesData($validSites, $designRequestId);

            // Step 4: Bulk insert
            $result = ColocationSite::insert($sitesToInsert);

            DB::commit();

            Log::info('Colocation sites saved successfully', [
                'sites_inserted' => count($sitesToInsert),
                'design_request_id' => $designRequestId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Colocation sites saved successfully',
                'data' => [
                    'sites_count' => count($sitesToInsert),
                    'design_request_id' => $designRequestId
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saving colocation sites:', [
                'error' => $e->getMessage(),
                'design_request_id' => $designRequestId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save colocation sites: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filter out empty or invalid sites
     */
    private function filterValidSites(array $sites): array
    {
        return array_filter($sites, function($site) {
            return !empty(trim($site['site_name'] ?? '')) &&
                   !empty($site['service_type'] ?? '');
        });
    }

    /**
     * Prepare sites data for insertion
     */
    private function prepareSitesData(array $sites, int $designRequestId): array
    {
        $now = now();

        return array_map(function($site) use ($designRequestId, $now) {
            return [
                'design_request_id' => $designRequestId,
                'site_name' => trim($site['site_name']),
                'service_type' => $site['service_type'],
                'created_at' => $now,
                'updated_at' => $now
            ];
        }, $sites);
    }

    /**
     * Get sites by design request ID
     */
    public function getByDesignRequest($designRequestId): JsonResponse
    {
        $sites = ColocationSite::where('design_request_id', $designRequestId)
                              ->orderBy('created_at', 'asc')
                              ->get();

        return response()->json([
            'success' => true,
            'data' => $sites
        ]);
    }
}
