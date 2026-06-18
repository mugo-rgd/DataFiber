<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use Illuminate\Http\Request;

class CustomerLeaseController extends Controller
{
    /**
     * Get active leases for a specific customer
     */
    public function getLeases($customerId)
    {
        try {
            $leases = Lease::where('customer_id', $customerId)
                ->where('status', 'active')
                ->get([
                    'id',
                    'lease_number',
                    'title',
                    'monthly_cost',
                    'currency',
                    'start_location',
                    'end_location',
                    'service_type'
                ]);

            return response()->json([
                'success' => true,
                'leases' => $leases,
                'count' => $leases->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
