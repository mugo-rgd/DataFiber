<?php

namespace App\Http\Controllers;

use App\Models\FiberSegment;
use App\Models\FiberNetwork;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FiberSegmentController extends Controller
{
    public function index(): JsonResponse
    {
        $segments = FiberSegment::with('network')->get();
        return response()->json($segments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'segment_id' => 'required|unique:fiber_segments',
            'network_id' => 'required|exists:fiber_networks,network_id',
            'segment_order' => 'required|integer',
            'source_name' => 'required',
            'source_lat' => 'required|numeric|between:-90,90',
            'source_lon' => 'required|numeric|between:-180,180',
            'destination_name' => 'required',
            'dest_lat' => 'required|numeric|between:-90,90',
            'dest_lon' => 'required|numeric|between:-180,180',
            'cable_type' => 'required',
            'distance_km' => 'required|numeric|min:0',
            'fiber_cores' => 'required|integer|min:1',
            'link_type' => 'required|in:Metro,Premium,Non Premium',
            'currency' => 'required|in:USD,KES',
            'status' => 'required|in:Active,Damaged,Planned,Decommissioned'
        ]);

        $segment = FiberSegment::create($validated);

        return response()->json(['message' => 'Segment created successfully', 'segment' => $segment], 201);
    }

    public function show($id): JsonResponse
    {
        $segment = FiberSegment::with('network')->where('segment_id', $id)->firstOrFail();
        return response()->json($segment);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $segment = FiberSegment::where('segment_id', $id)->firstOrFail();

        $validated = $request->validate([
            'source_name' => 'sometimes|required',
            'source_lat' => 'sometimes|required|numeric|between:-90,90',
            'source_lon' => 'sometimes|required|numeric|between:-180,180',
            'destination_name' => 'sometimes|required',
            'dest_lat' => 'sometimes|required|numeric|between:-90,90',
            'dest_lon' => 'sometimes|required|numeric|between:-180,180',
            'cable_type' => 'sometimes|required',
            'distance_km' => 'sometimes|required|numeric|min:0',
            'fiber_cores' => 'sometimes|required|integer|min:1',
            'link_type' => 'sometimes|required|in:Metro,Premium,Non Premium',
            'currency' => 'sometimes|required|in:USD,KES',
            'status' => 'sometimes|required|in:Active,Damaged,Planned,Decommissioned'
        ]);

        $segment->update($validated);

        return response()->json(['message' => 'Segment updated successfully', 'segment' => $segment]);
    }

    public function destroy($id): JsonResponse
    {
        $segment = FiberSegment::where('segment_id', $id)->firstOrFail();
        $segment->delete();

        return response()->json(['message' => 'Segment deleted successfully']);
    }

    public function getByNetwork($networkId): JsonResponse
    {
        $segments = FiberSegment::where('network_id', $networkId)
            ->orderBy('segment_order')
            ->get();
        return response()->json($segments);
    }
}
