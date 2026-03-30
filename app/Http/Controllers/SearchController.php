<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lease;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
public function searchLeases(Request $request)
{
    $searchTerm = trim($request->query('q'));

    if (!$searchTerm) {
        return response()->json([]);
    }

    $results = Lease::query()
        ->where(function ($query) use ($searchTerm) {

            // Text search
            $query->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('lease_number', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('service_type', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('currency', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('start_location', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('end_location', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('status', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('bandwidth', 'LIKE', "%{$searchTerm}%");

            // If numeric, search numeric fields
            if (is_numeric($searchTerm)) {
                $query->orWhere('id', $searchTerm)
                      ->orWhere('monthly_cost', $searchTerm)
                      ->orWhere('installation_fee', $searchTerm)
                      ->orWhere('total_contract_value', $searchTerm);
            }
        })
        ->select([
            'id',
            'lease_number',
            'title',
            'service_type',
            'status',
            'currency',
            'monthly_cost'
        ])
        ->latest()
        ->limit(50)
        ->get();

    return response()->json($results);
}
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
