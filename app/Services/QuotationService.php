<?php

namespace App\Services;

use App\Models\CommercialRoute;
use App\Models\ColocationList;
use App\Models\ColocationService;
use App\Models\Quotation;

class QuotationService
{
    /**
     * Calculate route pricing
     */
    public static function calculateRoutePricing($routeId, $cores = null, $duration = 12)
    {
        $route = CommercialRoute::find($routeId);
        if (!$route) {
            return null;
        }

        $cores = $cores ?? $route->no_of_cores_required;
        $monthlyCost = $route->calculateMonthlyCost($cores);
        $totalCost = $monthlyCost * $duration;

        return [
            'route' => $route,
            'cores' => $cores,
            'duration' => $duration,
            'monthly_cost' => $monthlyCost,
            'total_cost' => $totalCost,
            'description' => $route->name_of_route . " ({$cores} cores, {$duration} months)"
        ];
    }

    /**
     * Calculate colocation service pricing
     */
    public static function calculateServicePricing($serviceId, $quantity = 1, $duration = null, $source = 'list')
    {
        if ($source === 'list') {
            $service = ColocationList::where('service_id', $serviceId)->first();
            if (!$service) {
                return null;
            }

            $duration = $duration ?? $service->min_contract_months ?? 12;

            // Calculate monthly rate
            if ($service->monthly_price_usd > 0) {
                $monthlyRate = $service->monthly_price_usd;
            } elseif ($service->recurrent_per_Annum > 0) {
                $monthlyRate = $service->recurrent_per_Annum / 12;
            } else {
                $monthlyRate = 0;
            }

            // Calculate setup fee
            if ($service->setup_fee_usd > 0) {
                $setupFee = $service->setup_fee_usd;
            } elseif ($service->oneoff_rate > 0) {
                $setupFee = $service->oneoff_rate;
            } else {
                $setupFee = 0;
            }

            $monthlyTotal = $monthlyRate * $quantity;
            $setupTotal = $setupFee * $quantity;
            $totalCost = ($monthlyTotal * $duration) + $setupTotal;

            return [
                'service' => $service,
                'template' => $service,
                'quantity' => $quantity,
                'duration' => $duration,
                'monthly_rate' => $monthlyRate,
                'setup_fee' => $setupFee,
                'monthly_total' => $monthlyTotal,
                'setup_total' => $setupTotal,
                'total_cost' => $totalCost,
                'description' => $service->service_type . " (Qty: {$quantity}, {$duration} months)"
            ];
        } else {
            // For colocation_services table instances
            $service = ColocationService::find($serviceId);
            if (!$service) {
                return null;
            }

            $duration = $duration ?? $service->contract_months ?? 12;
            $monthlyRate = $service->monthly_price;
            $setupFee = $service->setup_fee;

            $monthlyTotal = $monthlyRate * $quantity;
            $setupTotal = $setupFee * $quantity;
            $totalCost = ($monthlyTotal * $duration) + $setupTotal;

            return [
                'service' => $service,
                'template' => null,
                'quantity' => $quantity,
                'duration' => $duration,
                'monthly_rate' => $monthlyRate,
                'setup_fee' => $setupFee,
                'monthly_total' => $monthlyTotal,
                'setup_total' => $setupTotal,
                'total_cost' => $totalCost,
                'description' => $service->service_type . " (Qty: {$quantity}, {$duration} months)"
            ];
        }
    }

    /**
     * Build line items for quotation
     */
    public static function buildLineItems($request, $designRequest)
    {
        $lineItems = [];
        $subtotal = 0;

        // Process commercial routes
        if ($request->selected_routes) {
            foreach ($request->selected_routes as $routeId) {
                $cores = $request->route_cores[$routeId] ?? null;
                $duration = $request->route_duration[$routeId] ?? 12;

                $routeData = self::calculateRoutePricing($routeId, $cores, $duration);

                if ($routeData) {
                    $lineItems[] = [
                        'type' => 'commercial_route',
                        'item_id' => $routeId,
                        'description' => $routeData['description'],
                        'quantity' => 1,
                        'unit_price' => $routeData['monthly_cost'],
                        'total' => $routeData['total_cost'],
                        'metadata' => [
                            'route_id' => $routeId,
                            'cores' => $routeData['cores'],
                            'duration_months' => $duration,
                            'monthly_cost' => $routeData['monthly_cost'],
                            'route_name' => $routeData['route']->name_of_route,
                            'route_code' => $routeData['route']->route_code ?? null,
                            'distance_km' => $routeData['route']->distance_km ?? null,
                            'technology_type' => $routeData['route']->technology_type ?? 'OPGW',
                            'pickup_points' => $routeData['route']->pickup_points ?? 'N/A'
                        ]
                    ];

                    $subtotal += $routeData['total_cost'];
                }
            }
        }

        // Process colocation services
        if ($request->selected_services) {
            foreach ($request->selected_services as $serviceId) {
                $duration = $request->service_duration[$serviceId] ?? null;
                $quantity = $request->service_quantity[$serviceId] ?? 1;

                // Determine source - check if it's from colocation_list or colocation_services
                $service = ColocationList::where('service_id', $serviceId)->first();
                $source = $service ? 'list' : 'instance';

                if ($source === 'instance') {
                    // Try to find in colocation_services
                    $service = ColocationService::find($serviceId);
                    if (!$service) continue;
                }

                $serviceData = self::calculateServicePricing($serviceId, $quantity, $duration, $source);

                if ($serviceData) {
                    $lineItems[] = [
                        'type' => 'colocation_service',
                        'item_id' => $serviceId,
                        'description' => $serviceData['description'],
                        'quantity' => $quantity,
                        'unit_price' => $serviceData['monthly_total'] / max($quantity, 1), // Unit price per item
                        'total' => $serviceData['total_cost'],
                        'metadata' => [
                            'service_id' => $serviceId,
                            'service_type' => $serviceData['service']->service_type,
                            'duration_months' => $serviceData['duration'],
                            'quantity' => $quantity,
                            'setup_fee' => $serviceData['setup_total'],
                            'monthly_rate' => $serviceData['monthly_rate'],
                            'setup_rate' => $serviceData['setup_fee'],
                            'space_sqm' => $serviceData['service']->space_sqm ?? $serviceData['service']->service_area ?? null,
                            'power_kw' => $serviceData['service']->power_kw ?? null,
                            'power_amps' => $serviceData['service']->power_amps ?? null
                        ]
                    ];

                    $subtotal += $serviceData['total_cost'];
                }
            }
        }

        // Process custom items
        if ($request->custom_items) {
            foreach ($request->custom_items as $index => $customItem) {
                if (!empty($customItem['description']) && !empty($customItem['unit_price'])) {
                    $quantity = $customItem['quantity'] ?? 1;
                    $unitPrice = $customItem['unit_price'];
                    $itemTotal = $quantity * $unitPrice;

                    $lineItems[] = [
                        'type' => 'custom_item',
                        'item_id' => 'custom_' . $index,
                        'description' => $customItem['description'],
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total' => $itemTotal,
                        'metadata' => [
                            'custom_item' => true,
                            'index' => $index,
                            'category' => $customItem['category'] ?? 'Other'
                        ]
                    ];

                    $subtotal += $itemTotal;
                }
            }
        }

        return [
            'line_items' => $lineItems,
            'subtotal' => $subtotal
        ];
    }

    /**
     * Generate quotation number
     */
    public static function generateQuotationNumber($designRequest)
    {
        $year = date('Y');
        $month = date('m');
        $sequence = Quotation::whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->count() + 1;

        $customerCode = strtoupper(substr(preg_replace('/[^A-Z]/', '', $designRequest->customer->name), 0, 3));

        return "QTN-{$customerCode}-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate totals with tax
     */
    public static function calculateTotals($subtotal, $taxRate = 0.16)
    {
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount
        ];
    }
}
