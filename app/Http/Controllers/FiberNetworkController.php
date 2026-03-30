<?php

namespace App\Http\Controllers;

use App\Models\FiberNetwork;
use App\Models\FiberSegment;
use App\Models\FiberNode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;

class FiberNetworkController extends Controller
{
    public function index(): JsonResponse
    {
        $networks = FiberNetwork::with('segments')->get();
        return response()->json($networks);
    }
public function dashboard()
    {
        $stats = [
            'total_networks' => FiberNetwork::count(),
            'total_distance' => FiberNetwork::sum('total_distance_km'),
            'total_nodes' => FiberNode::count(),
            'total_monthly_revenue' => FiberNetwork::sum('cost_per_month'),
        ];

        $regions = FiberNetwork::select('region')->distinct()->get();

        $regionStats = FiberNetwork::select('region',
                DB::raw('count(*) as count'),
                DB::raw('sum(total_distance_km) as total_distance'))
            ->groupBy('region')
            ->get();

        $recent_networks = FiberNetwork::latest()->take(50)->get();

        $nodes = FiberNode::select('node_id', 'node_name', 'node_type', 'latitude', 'longitude', 'region')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // Generate GeoJSON for all networks with their segments
        $geoJson = $this->generateNetworkGeoJson();

        return view('kenya-fibre.dashboard', compact(
            'stats',
            'regions',
            'regionStats',
            'recent_networks',
            'nodes',
            'geoJson'
        ));
    }

    private function generateNetworkGeoJson()
    {
        $networks = FiberNetwork::with('segments')->get();

        $features = [];

        foreach ($networks as $network) {
            // Collect all coordinates from segments
            $coordinates = [];

            foreach ($network->segments as $segment) {
                // Add source coordinates
                $coordinates[] = [$segment->source_lon, $segment->source_lat];
            }

            // Add destination of last segment
            if ($network->segments->isNotEmpty()) {
                $lastSegment = $network->segments->last();
                $coordinates[] = [$lastSegment->dest_lon, $lastSegment->dest_lat];
            }

            if (count($coordinates) > 1) {
                $features[] = [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $network->network_id,
                        'name' => $network->network_name,
                        'region' => $network->region,
                        'distance' => $network->total_distance_km,
                        'fiber_cores' => $network->fiber_cores,
                        'link_type' => $network->link_type,
                        'status' => $network->status,
                        'currency' => $network->currency,
                        'cost_per_month' => $network->cost_per_month,
                    ],
                    'geometry' => [
                        'type' => 'LineString',
                        'coordinates' => $coordinates
                    ]
                ];
            }
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'network_id' => 'required|unique:fiber_networks',
            'network_name' => 'required',
            'region' => 'required',
            'total_distance_km' => 'required|numeric|min:0',
            'fiber_cores' => 'required|integer|min:1',
            'link_type' => 'required|in:Metro,Premium,Non Premium',
            'currency' => 'required|in:USD,KES',
            'status' => 'required|in:Active,Damaged,Planned,Decommissioned',
            'connection_sequence' => 'nullable',
            'waypoints' => 'nullable|array',
            'segments' => 'required|array|min:1'
        ]);

        DB::beginTransaction();
        try {
            // Create network
            $network = FiberNetwork::create([
                'network_id' => $validated['network_id'],
                'network_name' => $validated['network_name'],
                'region' => $validated['region'],
                'total_distance_km' => $validated['total_distance_km'],
                'fiber_cores' => $validated['fiber_cores'],
                'link_type' => $validated['link_type'],
                'cost_per_month' => 0, // Will be calculated from segments
                'currency' => $validated['currency'],
                'status' => $validated['status'],
                'connection_sequence' => $validated['connection_sequence'] ?? '',
                'waypoints_json' => $validated['waypoints'] ?? []
            ]);

            // Create geometry from waypoints or segments
            $points = [];

            // Create segments
            foreach ($validated['segments'] as $index => $segmentData) {
                $segmentData['segment_id'] = $segmentData['segment_id'] ?? $network->network_id . '-S' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                $segmentData['network_id'] = $network->network_id;
                $segmentData['segment_order'] = $index + 1;

                $segment = FiberSegment::create($segmentData);

                // Collect points for network geometry
                if ($index === 0) {
                    $points[] = new Point($segment->source_lat, $segment->source_lon);
                }
                $points[] = new Point($segment->dest_lat, $segment->dest_lon);
            }

            // Set network geometry
            if (count($points) >= 2) {
                $network->geometry = new LineString($points);
                $network->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Network created successfully',
                'network' => $network->load('segments')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create network: ' . $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $network = FiberNetwork::with('segments')->where('network_id', $id)->firstOrFail();
        return response()->json($network);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $network = FiberNetwork::where('network_id', $id)->firstOrFail();

        $validated = $request->validate([
            'network_name' => 'sometimes|required',
            'region' => 'sometimes|required',
            'total_distance_km' => 'sometimes|required|numeric|min:0',
            'fiber_cores' => 'sometimes|required|integer|min:1',
            'link_type' => 'sometimes|required|in:Metro,Premium,Non Premium',
            'currency' => 'sometimes|required|in:USD,KES',
            'status' => 'sometimes|required|in:Active,Damaged,Planned,Decommissioned',
            'connection_sequence' => 'nullable'
        ]);

        $network->update($validated);

        return response()->json(['message' => 'Network updated successfully', 'network' => $network]);
    }

    public function destroy($id): JsonResponse
    {
        $network = FiberNetwork::where('network_id', $id)->firstOrFail();
        $network->delete();

        return response()->json(['message' => 'Network deleted successfully']);
    }

    public function getByRegion($region): JsonResponse
    {
        $networks = FiberNetwork::with('segments')->where('region', $region)->get();
        return response()->json($networks);
    }

    public function getStats(): JsonResponse
    {
        $stats = [
            'total_networks' => FiberNetwork::count(),
            'total_distance' => FiberNetwork::sum('total_distance_km'),
            'total_monthly_revenue' => FiberNetwork::sum('cost_per_month'),
            'by_region' => FiberNetwork::select('region',
                DB::raw('count(*) as count'),
                DB::raw('sum(total_distance_km) as total_distance'),
                DB::raw('sum(cost_per_month) as total_revenue'))
                ->groupBy('region')
                ->get(),
            'by_link_type' => FiberNetwork::select('link_type',
                DB::raw('count(*) as count'),
                DB::raw('sum(total_distance_km) as total_distance'))
                ->groupBy('link_type')
                ->get(),
            'by_status' => FiberNetwork::select('status',
                DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
        ];

        return response()->json($stats);
    }

    public function getGeoJSON(): JsonResponse
    {
        $networks = FiberNetwork::with('segments')->get();

        $features = [];
        foreach ($networks as $network) {
            $coordinates = [];

            // Build coordinates array from segments
            foreach ($network->segments()->orderBy('segment_order')->get() as $segment) {
                if (empty($coordinates)) {
                    $coordinates[] = [$segment->source_lon, $segment->source_lat];
                }
                $coordinates[] = [$segment->dest_lon, $segment->dest_lat];
            }

            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'network_id' => $network->network_id,
                    'name' => $network->network_name,
                    'region' => $network->region,
                    'distance' => $network->total_distance_km,
                    'fiber_cores' => $network->fiber_cores,
                    'link_type' => $network->link_type,
                    'cost' => $network->cost_per_month,
                    'currency' => $network->currency,
                    'status' => $network->status
                ],
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => $coordinates
                ]
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }
}
