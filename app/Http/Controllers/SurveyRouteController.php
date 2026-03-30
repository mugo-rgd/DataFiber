<?php

namespace App\Http\Controllers;

use App\Models\SurveyRoute;
use Illuminate\Http\Request;

class SurveyRouteController extends Controller
{

    public function index(Request $request)
    {
        $query = SurveyRoute::with(['designRequest.customer', 'surveyor.user'])
            ->approved()
            ->active();

        // Filters
        if ($request->has('route_type') && $request->route_type != 'all') {
            $query->where('route_type', $request->route_type);
        }

        if ($request->has('complexity') && $request->complexity != 'all') {
            $query->where('complexity', $request->complexity);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('route_code', 'LIKE', "%{$search}%")
                  ->orWhere('route_name', 'LIKE', "%{$search}%")
                  ->orWhere('start_location', 'LIKE', "%{$search}%")
                  ->orWhere('end_location', 'LIKE', "%{$search}%");
            });
        }

        $routes = $query->orderBy('route_name')->paginate(20);
        $routeTypes = ['aerial', 'underground', 'hybrid', 'submarine'];
        $complexities = ['low', 'medium', 'high', 'very_high'];

        return view('survey-routes.index', compact('routes', 'routeTypes', 'complexities'));
    }

    public function show(SurveyRoute $surveyRoute)
    {
        $surveyRoute->load(['segments', 'designRequest.customer', 'surveyor.user', 'surveyResult']);
        return view('survey-routes.show', compact('surveyRoute'));
    }

    public function getRoutesApi(Request $request)
    {
        $routes = SurveyRoute::approved()
            ->active()
            ->when($request->has('project_type'), function($q) use ($request) {
                $q->where('route_type', $request->project_type);
            })
            ->select(['id', 'route_code', 'route_name', 'route_type', 'total_distance_km', 'complexity'])
            ->orderBy('route_name')
            ->get();

        return response()->json($routes);
    }
    /**
     * Display a listing of the resource.

     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

       /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
