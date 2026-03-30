<?php

namespace App\Services;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public static function generateQuotationPdf(Quotation $quotation)
    {
        $data = self::preparePdfData($quotation);

        $pdf = PDF::loadView('customer.quotations.pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    private static function preparePdfData(Quotation $quotation)
    {
        $quotation->load([
            'designRequest.customer',
            'commercialRoutes',
            'colocationServices'
        ]);

        $lineItems = $quotation->line_items ?? [];
        $groupedItems = [
            'commercial_routes' => [],
            'colocation_services' => [],
            'custom_items' => []
        ];

        foreach ($lineItems as $item) {
            $groupedItems[$item['type'] . 's'][] = $item;
        }

        // Calculate summary
        $routeTotal = collect($groupedItems['commercial_routes'])->sum('total');
        $serviceTotal = collect($groupedItems['colocation_services'])->sum('total');
        $customTotal = collect($groupedItems['custom_items'])->sum('total');

        return [
            'quotation' => $quotation,
            'groupedItems' => $groupedItems,
            'routeTotal' => $routeTotal,
            'serviceTotal' => $serviceTotal,
            'customTotal' => $customTotal,
            'subtotal' => $quotation->subtotal,
            'taxAmount' => $quotation->tax_amount,
            'totalAmount' => $quotation->total_amount,
            'taxRate' => $quotation->tax_rate * 100
        ];
    }
}
