<?php

namespace App\Http\Controllers;

use App\Models\CommercialRoute;
use Illuminate\Http\Request;

class CommercialRouteController extends Controller
{
    public function index(Request $request)
    {
        $query = CommercialRoute::query();

        // Filter by option
        if ($request->has('option')) {
            $query->where('option', $request->option);
        }

        // Filter by technology type
        if ($request->has('tech_type')) {
            $query->where('tech_type', $request->tech_type);
        }

        // Filter by availability
        if ($request->has('availability')) {
            $query->where('availability', $request->availability);
        }

        // Filter by currency
        if ($request->has('currency')) {
            $query->where('currency', $request->currency);
        }

        $routes = $query->get()->groupBy('option');

        return view('commercial-routes.index', compact('routes'));
    }

    public function summary()
    {
        $summary = CommercialRoute::selectRaw('
            option,
            COUNT(*) as total_routes,
            SUM(approx_distance_km) as total_distance,
            SUM(capital_expenditure) as total_capex,
            SUM(
                no_of_cores_required *
                unit_cost_per_core_per_km_per_month *
                approx_distance_km * 12
            ) as total_annual_cost
        ')->groupBy('option')->get();

        return view('commercial-routes.summary', compact('summary'));
    }

    public function capexRoutes()
    {
        $capexRoutes = CommercialRoute::requiresCapex()->get();
        return view('commercial-routes.capex', compact('capexRoutes'));
    }
}
