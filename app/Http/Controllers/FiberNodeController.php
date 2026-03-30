<?php

namespace App\Http\Controllers;

use App\Models\FiberNode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FiberNodeController extends Controller
{
    public function index(): JsonResponse
    {
        $nodes = FiberNode::all();
        return response()->json($nodes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'node_id' => 'required|unique:fiber_nodes',
            'node_name' => 'required',
            'node_type' => 'required',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'region' => 'required',
            'address' => 'nullable',
            'description' => 'nullable'
        ]);

        $node = FiberNode::create($validated);

        return response()->json(['message' => 'Node created successfully', 'node' => $node], 201);
    }

    public function show($id): JsonResponse
    {
        $node = FiberNode::findOrFail($id);
        return response()->json($node);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $node = FiberNode::findOrFail($id);

        $validated = $request->validate([
            'node_name' => 'sometimes|required',
            'node_type' => 'sometimes|required',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'sometimes|required|numeric|between:-180,180',
            'region' => 'sometimes|required',
            'address' => 'nullable',
            'description' => 'nullable'
        ]);

        $node->update($validated);

        return response()->json(['message' => 'Node updated successfully', 'node' => $node]);
    }

    public function destroy($id): JsonResponse
    {
        $node = FiberNode::findOrFail($id);
        $node->delete();

        return response()->json(['message' => 'Node deleted successfully']);
    }

    public function getByRegion($region): JsonResponse
    {
        $nodes = FiberNode::where('region', $region)->get();
        return response()->json($nodes);
    }

    public function getNearby(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:0.1|max:100'
        ]);

        // Using spatial query to find nodes within radius (in kilometers)
        $nodes = FiberNode::selectRaw(
            "*, ST_Distance_Sphere(location, POINT(?, ?)) / 1000 as distance",
            [$validated['lng'], $validated['lat']]
        )
        ->having('distance', '<=', $validated['radius'])
        ->orderBy('distance')
        ->get();

        return response()->json($nodes);
    }
}
