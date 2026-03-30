<?php
// app/Http/Controllers/Admin/LeaseSearchController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\User;
use Illuminate\Http\Request;

class LeaseSearchController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->get('q');

        if (strlen($searchTerm) < 2) {
            return response()->json([]);
        }

        $results = Lease::with('customer')
            ->where('lease_number', 'LIKE', "%{$searchTerm}%")
            ->orWhere('service_type', 'LIKE', "%{$searchTerm}%")
            ->orWhere('start_location', 'LIKE', "%{$searchTerm}%")
            ->orWhere('end_location', 'LIKE', "%{$searchTerm}%")
            ->orWhere('monthly_cost', 'LIKE', "%{$searchTerm}%")
            ->orWhere('status', 'LIKE', "%{$searchTerm}%")
            ->orWhere('currency', 'LIKE', "%{$searchTerm}%")
            ->orWhereHas('customer', function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('company_name', 'LIKE', "%{$searchTerm}%");
            })
            ->limit(50)
            ->get()
            ->map(function ($lease) {
                // Get account manager info
                $accountManagerName = 'Unassigned';
                if ($lease->customer && $lease->customer->account_manager_id) {
                    $accountManager = User::find($lease->customer->account_manager_id);
                    $accountManagerName = $accountManager ? $accountManager->name : 'Unassigned';
                }

                return [
                    'id' => $lease->id,
                    'lease_number' => $lease->lease_number,
                    'customer_name' => $lease->customer->name ?? 'N/A',
                    'customer_email' => $lease->customer->email ?? 'N/A',
                    'account_manager_name' => $accountManagerName,
                    'service_type' => $lease->service_type,
                    'start_location' => $lease->start_location,
                    'end_location' => $lease->end_location,
                    'monthly_cost' => number_format($lease->monthly_cost, 2),
                    'currency' => $lease->currency,
                    'status' => $lease->status,
                    'url' => route('admin.leases.show', $lease),
                ];
            });

        return response()->json($results);
    }

    // Advanced search with filters
    public function advancedSearch(Request $request)
    {
        $query = Lease::with('customer');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('lease_number', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('service_type', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($searchTerm) {
                      $customerQuery->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $results = $query->limit(50)->get();
        return response()->json($results);
    }

    /**
     * Get account managers for autocomplete
     */
    public function getAccountManagers(Request $request)
    {
        $searchTerm = $request->get('q');

        $query = User::where('role', 'account_manager')
                     ->where('status', 'active');

        if ($searchTerm && strlen($searchTerm) >= 1) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        $managers = $query->orderBy('name')
                          ->limit(10)
                          ->get()
                          ->map(function($manager) {
                              return [
                                  'id' => $manager->id,
                                  'name' => $manager->name,
                                  'email' => $manager->email,
                                  'initial' => substr($manager->name, 0, 1),
                                  'display' => $manager->name . ' (' . $manager->email . ')'
                              ];
                          });

        return response()->json($managers);
    }

    public function searchByAccountManager(Request $request)
    {
        $managerId = $request->get('manager_id');
        $searchTerm = $request->get('q');

        $query = Lease::with('customer');

        if ($managerId) {
            // Find customers assigned to this account manager
            $customers = User::where('role', 'customer')
                ->where('account_manager_id', $managerId)
                ->pluck('id');

            $query->whereIn('customer_id', $customers);
        } elseif ($searchTerm && strlen($searchTerm) >= 2) {
            // First find account managers matching the search
            $accountManagers = User::where('role', 'account_manager')
                ->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                })
                ->pluck('id');

            // Find customers assigned to these account managers
            $customers = User::where('role', 'customer')
                ->whereIn('account_manager_id', $accountManagers)
                ->pluck('id');

            $query->whereIn('customer_id', $customers);
        } else {
            return response()->json([]);
        }

        $results = $query->limit(20)
                         ->get()
                         ->map(function ($lease) {
                             $accountManagerName = 'Unassigned';

                             if ($lease->customer && $lease->customer->account_manager_id) {
                                 $accountManager = User::find($lease->customer->account_manager_id);
                                 $accountManagerName = $accountManager ? $accountManager->name : 'Unassigned';
                             }

                             return [
                                 'id' => $lease->id,
                                 'lease_number' => $lease->lease_number,
                                 'customer_name' => $lease->customer->name ?? 'N/A',
                                 'customer_company' => $lease->customer->company_name ?? '',
                                 'account_manager_name' => $accountManagerName,
                                 'service_type' => $lease->service_type,
                                 'start_location' => $lease->start_location,
                                 'end_location' => $lease->end_location,
                                 'monthly_cost' => number_format($lease->monthly_cost, 2),
                                 'currency' => $lease->currency,
                                 'status' => $lease->status,
                                 'url' => route('admin.leases.show', $lease),
                             ];
                         });

        return response()->json($results);
    }
}
