<?php

namespace App\Http\Controllers;

use App\Models\FibreStation;
use Illuminate\Http\Request;

class FibreStationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = FibreStation::available();

            // Apply filters
            if ($request->has('owner') && $request->owner !== 'all') {
                $query->where('owner', $request->owner);
            }

            if ($request->has('area')) {
                $query->where('area', $request->area);
            }

            $stations = $query->get();

            return response()->json($stations);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch substation data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unique owners for filters.
     */
    public function getOwners()
    {
        $owners = FibreStation::select('owner')
            ->distinct()
            ->whereNotNull('owner')
            ->pluck('owner');

        return response()->json($owners);
    }

    /**
     * Get unique areas for filters.
     */
    public function getAreas()
    {
        $areas = FibreStation::select('area')
            ->distinct()
            ->whereNotNull('area')
            ->pluck('area');

        return response()->json($areas);
    }
}
