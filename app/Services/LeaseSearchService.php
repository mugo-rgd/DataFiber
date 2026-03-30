<?php
// app/Services/LeaseSearchService.php

namespace App\Services;

use App\Models\Lease;
use Illuminate\Database\Eloquent\Collection;

class LeaseSearchService
{
    public function search(string $term, int $limit = 20): Collection
    {
        return Lease::with('customer')
            ->where(function ($query) use ($term) {
                $query->where('lease_number', 'LIKE', "%{$term}%")
                    ->orWhere('service_type', 'LIKE', "%{$term}%")
                    ->orWhere('start_location', 'LIKE', "%{$term}%")
                    ->orWhere('end_location', 'LIKE', "%{$term}%")
                    ->orWhere('monthly_cost', 'LIKE', "%{$term}%")
                    ->orWhere('status', 'LIKE', "%{$term}%")
                    ->orWhereHas('customer', function ($q) use ($term) {
                        $q->where('name', 'LIKE', "%{$term}%")
                          ->orWhere('email', 'LIKE', "%{$term}%");
                    });
            })
            ->limit($limit)
            ->get();
    }

    public function searchWithFilters(array $filters): Collection
    {
        $query = Lease::with('customer');

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('lease_number', 'LIKE', "%{$term}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($term) {
                      $customerQuery->where('name', 'LIKE', "%{$term}%");
                  });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['service_type'])) {
            $query->where('service_type', $filters['service_type']);
        }

        return $query->limit(50)->get();
    }
}
